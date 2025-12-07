<?php

namespace App\Services;

use App\Models\FcmToken;
use App\Models\Notification;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $client;
    protected $projectId;
    protected $credentialsPath;

    public function __construct()
    {
        $this->client = new Client();
        $this->projectId = 'eccomerce-asdes';
        $this->credentialsPath = storage_path('app/firebase-credentials.json');
    }

    /**
     * Get OAuth 2.0 access token from service account.
     */
    protected function getAccessToken()
    {
        if (!file_exists($this->credentialsPath)) {
            throw new \Exception('Firebase credentials file not found');
        }

        $credentials = json_decode(file_get_contents($this->credentialsPath), true);
        
        $now = time();
        $jwt = [
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode($jwt));
        $signatureInput = $header . '.' . $payload;

        openssl_sign($signatureInput, $signature, $credentials['private_key'], 'SHA256');
        $signature = base64_encode($signature);

        $jwtToken = $signatureInput . '.' . $signature;

        $response = $this->client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwtToken,
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        return $result['access_token'];
    }

    /**
     * Send notification to a specific user.
     */
    public function sendToUser($userId, $title, $body, $data = [], $type = 'general')
    {
        // Save notification to database
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
        ]);

        // Get user's FCM tokens
        $tokens = FcmToken::where('user_id', $userId)->pluck('token')->toArray();

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No FCM tokens found for this user',
                'notification' => $notification
            ];
        }

        // Send to Firebase
        return $this->sendNotification($tokens, $title, $body, $data);
    }

    /**
     * Send notification to all users.
     */
    public function sendToAllUsers($title, $body, $data = [], $type = 'general')
    {
        // Save notification to database (no specific user)
        Notification::create([
            'user_id' => null,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
        ]);

        // Get all FCM tokens
        $tokens = FcmToken::pluck('token')->toArray();

        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No FCM tokens found'
            ];
        }

        return $this->sendNotification($tokens, $title, $body, $data);
    }

    /**
     * Send notification via Firebase Cloud Messaging API v1.
     */
    protected function sendNotification($tokens, $title, $body, $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();
            $successCount = 0;
            $failureCount = 0;
            $errors = [];

            // Send to each token individually (FCM v1 doesn't support batch)
            foreach ($tokens as $token) {
                try {
                    $messageData = [
                        'message' => [
                            'token' => $token,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'android' => [
                                'priority' => 'high',
                            ],
                            'apns' => [
                                'headers' => [
                                    'apns-priority' => '10',
                                ],
                            ],
                        ]
                    ];

                    // Add data only if provided and convert to string values
                    if (!empty($data)) {
                        $messageData['message']['data'] = array_map('strval', $data);
                    }

                    $response = $this->client->post(
                        "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                        [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $accessToken,
                                'Content-Type' => 'application/json',
                            ],
                            'json' => $messageData
                        ]
                    );
                    $successCount++;
                } catch (\Exception $e) {
                    $failureCount++;
                    $errors[] = $e->getMessage();
                    Log::warning('Failed to send to token: ' . $token . ' - ' . $e->getMessage());
                }
            }

            return [
                'success' => $successCount > 0,
                'message' => "Sent to {$successCount} devices, {$failureCount} failed",
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'errors' => $failureCount > 0 ? $errors : null,
            ];

        } catch (\Exception $e) {
            Log::error('Firebase notification error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ];
        }
    }
}
