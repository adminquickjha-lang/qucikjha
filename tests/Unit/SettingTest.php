<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ================================================
// Setting Model Tests
// ================================================

test('settings can be created with key-value pairs', function () {
    $setting = Setting::create(['key' => 'jha_price', 'value' => '29.90']);

    expect($setting)->toBeInstanceOf(Setting::class);
    expect($setting->key)->toBe('jha_price');
    expect($setting->value)->toBe('29.90');
});

test('settings can be updated via updateOrCreate', function () {
    Setting::create(['key' => 'jha_price', 'value' => '19.90']);
    Setting::updateOrCreate(['key' => 'jha_price'], ['value' => '29.90']);

    $setting = Setting::where('key', 'jha_price')->first();
    expect($setting->value)->toBe('29.90');
});

test('multiple settings can be stored and retrieved', function () {
    Setting::create(['key' => 'jha_price', 'value' => '19.90']);
    Setting::create(['key' => 'aha_price', 'value' => '24.90']);
    Setting::create(['key' => 'jsa_price', 'value' => '14.00']);

    $prices = Setting::whereIn('key', ['jha_price', 'aha_price', 'jsa_price'])
        ->pluck('value', 'key')
        ->toArray();

    expect($prices)->toHaveCount(3);
    expect($prices['jha_price'])->toBe('19.90');
    expect($prices['aha_price'])->toBe('24.90');
    expect($prices['jsa_price'])->toBe('14.00');
});

// ================================================
// Template Settings Tests
// ================================================

test('template color settings can be stored and retrieved', function () {
    $templateSettings = [
        'header_color' => '#1a3a6b',
        'table_header_color' => '#2c5f9e',
        'rac_e_color' => '#c0392b',
        'rac_h_color' => '#e67e22',
        'rac_m_color' => '#f1c40f',
        'rac_l_color' => '#27ae60',
    ];

    foreach ($templateSettings as $key => $value) {
        Setting::create(['key' => $key, 'value' => $value]);
    }

    $retrieved = Setting::whereIn('key', array_keys($templateSettings))
        ->pluck('value', 'key')
        ->toArray();

    expect($retrieved)->toMatchArray($templateSettings);
});

test('disclaimer and PPE text settings can be stored', function () {
    Setting::create(['key' => 'required_ppe', 'value' => 'Hard hat, Safety glasses']);
    Setting::create(['key' => 'disclaimer_text', 'value' => 'Custom disclaimer text']);

    expect(Setting::where('key', 'required_ppe')->value('value'))->toBe('Hard hat, Safety glasses');
    expect(Setting::where('key', 'disclaimer_text')->value('value'))->toBe('Custom disclaimer text');
});

test('settings return null for non-existent keys', function () {
    $value = Setting::where('key', 'nonexistent_key')->value('value');
    expect($value)->toBeNull();
});
