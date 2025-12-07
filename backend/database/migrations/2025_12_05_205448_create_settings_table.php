<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, json, number, boolean
            $table->string('group')->default('general'); // general, appearance, contact, shipping, content, social
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            // General Settings
            ['key' => 'app_name', 'value' => 'Shopping App', 'type' => 'text', 'group' => 'general', 'description' => 'Application name', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_logo', 'value' => null, 'type' => 'image', 'group' => 'general', 'description' => 'Application logo', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency', 'value' => 'IQD', 'type' => 'text', 'group' => 'general', 'description' => 'Currency code', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'language', 'value' => 'ku', 'type' => 'text', 'group' => 'general', 'description' => 'Default language', 'created_at' => now(), 'updated_at' => now()],
            
            // Appearance Settings
            ['key' => 'primary_color', 'value' => '#FF6B6B', 'type' => 'text', 'group' => 'appearance', 'description' => 'Primary app color', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'secondary_color', 'value' => '#4ECDC4', 'type' => 'text', 'group' => 'appearance', 'description' => 'Secondary app color', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'background_color', 'value' => '#F7F7F7', 'type' => 'text', 'group' => 'appearance', 'description' => 'Background color', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'text_color', 'value' => '#333333', 'type' => 'text', 'group' => 'appearance', 'description' => 'Text color', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'success_color', 'value' => '#4CAF50', 'type' => 'text', 'group' => 'appearance', 'description' => 'Success color', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'error_color', 'value' => '#F44336', 'type' => 'text', 'group' => 'appearance', 'description' => 'Error color', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'warning_color', 'value' => '#FF9800', 'type' => 'text', 'group' => 'appearance', 'description' => 'Warning color', 'created_at' => now(), 'updated_at' => now()],
            
            // Language Settings
            ['key' => 'default_language', 'value' => 'ku', 'type' => 'text', 'group' => 'general', 'description' => 'Default language (ku, ar, en)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'supported_languages', 'value' => json_encode(['ku', 'ar', 'en']), 'type' => 'json', 'group' => 'general', 'description' => 'Supported languages', 'created_at' => now(), 'updated_at' => now()],
            
            // Contact Settings
            ['key' => 'contact_phone', 'value' => '+964 770 123 4567', 'type' => 'text', 'group' => 'contact', 'description' => 'Contact phone number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email', 'value' => 'info@shopping.com', 'type' => 'text', 'group' => 'contact', 'description' => 'Contact email', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_address', 'value' => 'Erbil, Kurdistan', 'type' => 'text', 'group' => 'contact', 'description' => 'Physical address', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_whatsapp', 'value' => '+964 770 123 4567', 'type' => 'text', 'group' => 'contact', 'description' => 'WhatsApp number', 'created_at' => now(), 'updated_at' => now()],
            
            // Social Media
            ['key' => 'facebook_url', 'value' => null, 'type' => 'text', 'group' => 'social', 'description' => 'Facebook page URL', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'instagram_url', 'value' => null, 'type' => 'text', 'group' => 'social', 'description' => 'Instagram profile URL', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'twitter_url', 'value' => null, 'type' => 'text', 'group' => 'social', 'description' => 'Twitter profile URL', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'youtube_url', 'value' => null, 'type' => 'text', 'group' => 'social', 'description' => 'YouTube channel URL', 'created_at' => now(), 'updated_at' => now()],
            
            // Shipping & Tax
            ['key' => 'shipping_fee', 'value' => '5000', 'type' => 'number', 'group' => 'shipping', 'description' => 'Default shipping fee', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'free_shipping_above', 'value' => '50000', 'type' => 'number', 'group' => 'shipping', 'description' => 'Free shipping minimum amount', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_rate', 'value' => '0', 'type' => 'number', 'group' => 'shipping', 'description' => 'Tax rate percentage', 'created_at' => now(), 'updated_at' => now()],
            
            // Content Pages
            ['key' => 'about_us', 'value' => 'About our shopping app...', 'type' => 'text', 'group' => 'content', 'description' => 'About us page content', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'terms_conditions', 'value' => 'Terms and conditions...', 'type' => 'text', 'group' => 'content', 'description' => 'Terms and conditions', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'privacy_policy', 'value' => 'Privacy policy...', 'type' => 'text', 'group' => 'content', 'description' => 'Privacy policy', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'return_policy', 'value' => 'Return policy...', 'type' => 'text', 'group' => 'content', 'description' => 'Return policy', 'created_at' => now(), 'updated_at' => now()],
            
            // Features
            ['key' => 'featured_items_count', 'value' => '10', 'type' => 'number', 'group' => 'general', 'description' => 'Number of featured items to show', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_reviews', 'value' => '1', 'type' => 'boolean', 'group' => 'general', 'description' => 'Enable product reviews', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_coupons', 'value' => '1', 'type' => 'boolean', 'group' => 'general', 'description' => 'Enable coupon system', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
