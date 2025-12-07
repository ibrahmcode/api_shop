# 🔔 Firebase Notification Setup Guide

## ✅ Backend Setup Complete!

Your Laravel API is fully configured with Firebase Cloud Messaging API v1.

### 📋 What's Already Done:
- ✅ Firebase Service Account configured
- ✅ OAuth 2.0 authentication implemented
- ✅ API endpoints ready
- ✅ Database tables created (notifications, fcm_tokens)
- ✅ **Auto FCM token registration during login/register**

---

## 🚀 Quick Start - Auto Token Registration

### Register New User (with FCM token):
```bash
POST http://localhost:8000/api/register
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123",
    "fcm_token": "YOUR_FCM_TOKEN_FROM_DEVICE",  // Optional
    "device_type": "android"  // Optional: android, ios, web
}
```

### Login (with FCM token):
```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123",
    "fcm_token": "YOUR_FCM_TOKEN_FROM_DEVICE",  // Optional
    "device_type": "android"  // Optional: android, ios, web
}
```

### Logout (removes FCM token):
```bash
POST http://localhost:8000/api/logout
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "fcm_token": "YOUR_FCM_TOKEN_FROM_DEVICE"  // Optional
}
```

---

## 📱 Mobile App Integration

### Step 1: Install Firebase SDK

#### For Flutter:
```yaml
# pubspec.yaml
dependencies:
  firebase_core: ^2.24.0
  firebase_messaging: ^14.7.6
  http: ^1.1.0
```

#### For React Native:
```bash
npm install @react-native-firebase/app @react-native-firebase/messaging
```

---

### Step 2: Get FCM Token & Auto Register on Login

#### Flutter Example (Recommended):
```dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

// Initialize Firebase
await Firebase.initializeApp();

// Get FCM Token
final FirebaseMessaging messaging = FirebaseMessaging.instance;
final String? fcmToken = await messaging.getToken();

// Login with auto FCM token registration
Future<void> loginUser(String email, String password, String? fcmToken) async {
  final response = await http.post(
    Uri.parse('http://your-api.com/api/login'),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: json.encode({
      'email': email,
      'password': password,
      'fcm_token': fcmToken,  // Automatically registered
      'device_type': 'android',  // or 'ios'
    }),
  );
  
  if (response.statusCode == 200) {
    final data = json.decode(response.body);
    final userToken = data['data']['token'];
    // Save token and proceed
    print('Login successful, FCM token auto-registered');
  }
}

// Register with auto FCM token registration
Future<void> registerUser(String email, String password, String? fcmToken) async {
  final response = await http.post(
    Uri.parse('http://your-api.com/api/register'),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: json.encode({
      'email': email,
      'password': password,
      'fcm_token': fcmToken,  // Automatically registered
      'device_type': 'android',  // or 'ios'
    }),
  );
  
  if (response.statusCode == 201) {
    print('User registered, FCM token auto-registered');
  }
}

// Logout with token removal
Future<void> logoutUser(String userToken, String? fcmToken) async {
  await http.post(
    Uri.parse('http://your-api.com/api/logout'),
    headers: {
      'Authorization': 'Bearer $userToken',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: json.encode({
      'fcm_token': fcmToken,  // Automatically removed
    }),
  );
}
```

#### React Native Example:
```javascript
import messaging from '@react-native-firebase/messaging';
import { Platform } from 'react-native';

// Get FCM Token
const fcmToken = await messaging().getToken();

// Login with auto FCM token registration
const loginUser = async (email, password, fcmToken) => {
  try {
    const response = await fetch('http://your-api.com/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        email: email,
        password: password,
        fcm_token: fcmToken,  // Automatically registered
        device_type: Platform.OS,  // 'android' or 'ios'
      }),
    });
    
    const data = await response.json();
    console.log('Login successful, FCM token auto-registered');
    return data.data.token;
  } catch (error) {
    console.error('Error:', error);
  }
};

// Register with auto FCM token registration
const registerUser = async (email, password, fcmToken) => {
  try {
    const response = await fetch('http://your-api.com/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        email: email,
        password: password,
        fcm_token: fcmToken,  // Automatically registered
        device_type: Platform.OS,
      }),
    });
    
    const data = await response.json();
    console.log('User registered, FCM token auto-registered');
  } catch (error) {
    console.error('Error:', error);
  }
};
```

---

### Step 3: Handle Incoming Notifications

#### Flutter:
```dart
// Foreground messages
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  print('Got a message in foreground!');
  print('Message title: ${message.notification?.title}');
  print('Message body: ${message.notification?.body}');
  
  // Show local notification or update UI
});

// Background messages
FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);

Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print('Handling background message: ${message.messageId}');
}

// Notification tap (app opened from notification)
FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
  print('Notification tapped!');
  // Navigate to specific screen
});
```

#### React Native:
```javascript
import messaging from '@react-native-firebase/messaging';

// Foreground messages
messaging().onMessage(async remoteMessage => {
  console.log('Notification in foreground:', remoteMessage);
});

// Background messages
messaging().setBackgroundMessageHandler(async remoteMessage => {
  console.log('Message handled in background:', remoteMessage);
});

// Notification tap
messaging().onNotificationOpenedApp(remoteMessage => {
  console.log('Notification opened app:', remoteMessage);
});
```

---

## 🧪 Testing Guide

### Method 1: Auto Registration (Recommended)

#### 1. Register with FCM Token
```bash
POST http://localhost:8000/api/register
Content-Type: application/json

{
    "email": "test@example.com",
    "password": "password123",
    "fcm_token": "REAL_FCM_TOKEN_FROM_DEVICE",
    "device_type": "android"
}

Response: { "token": "YOUR_USER_TOKEN", "user": {...} }
```

#### 2. Send Notification (Admin)
```bash
POST http://localhost:8000/api/admin/notifications/send-to-user
Authorization: Bearer ADMIN_TOKEN
Content-Type: application/json

{
    "user_id": 1,
    "title": "سڵاو",
    "body": "ئەمە نۆتیفیکەیشنێکی تاقیکردنەوەیە"
}
```

### Method 2: Manual Token Registration

#### 1. Register User First
```
POST http://localhost:8000/api/register
Content-Type: application/json

{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}

Response: { "token": "YOUR_USER_TOKEN" }
```

### 2. Register FCM Token
```
POST http://localhost:8000/api/user/notifications/token
Authorization: Bearer YOUR_USER_TOKEN
Content-Type: application/json

{
    "token": "REAL_FCM_TOKEN_FROM_MOBILE_APP",
    "device_type": "android"
}
```

### 3. Send Notification (Admin)
```
POST http://localhost:8000/api/admin/notifications/send-to-all
Authorization: Bearer ADMIN_TOKEN
Content-Type: application/json

{
    "title": "سڵاو",
    "body": "ئەمە نۆتیفیکەیشنێکی تاقیکردنەوەیە",
    "type": "announcement"
}
```

---

## 📡 API Endpoints

### User Endpoints:
- `POST /api/user/notifications/token` - Register FCM token
- `DELETE /api/user/notifications/token` - Delete FCM token
- `GET /api/user/notifications` - Get user notifications
- `POST /api/user/notifications/{id}/read` - Mark as read
- `GET /api/user/notifications/unread-count` - Count unread

### Admin Endpoints:
- `POST /api/admin/notifications/send-to-user` - Send to specific user
- `POST /api/admin/notifications/send-to-all` - Broadcast to all users
- `GET /api/admin/notifications` - List all notifications
- `GET /api/admin/notifications/users` - Get users list

---

## 🔧 Troubleshooting

### Issue: "No FCM tokens found"
**Solution:** User needs to register FCM token first via mobile app

### Issue: "The registration token is not a valid FCM registration token"
**Solution:** Make sure you're using REAL FCM token from mobile device, not test data

### Issue: "Firebase credentials file not found"
**Solution:** Check `storage/app/firebase-credentials.json` exists

---

## 📝 Important Notes

1. **Real Device Required**: FCM tokens only work on real devices or emulators with Google Play Services
2. **Token Refresh**: FCM tokens can expire, implement token refresh in your app
3. **Permissions**: Request notification permissions in mobile app
4. **Testing**: Use real mobile device with Firebase SDK installed

---

## 🎉 Success Checklist

- [ ] Firebase SDK installed in mobile app
- [ ] FCM token obtained from device
- [ ] Token registered via API
- [ ] Notification received on device
- [ ] Background notifications working
- [ ] Notification tap handling works

---

## 📞 Next Steps

1. Install Firebase SDK in your mobile app (Flutter/React Native)
2. Get real FCM token from device
3. Register token using API endpoint
4. Test sending notifications from admin panel
5. Implement notification UI in mobile app

**Your backend is ready! Now implement the mobile app side.** 🚀
