<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        Setting::firstOrCreate([
            'company_name' => 'Demo Company',
            'email' => 'demo@example.com',
            'mobile' => '+91 98765 43210',
            'landline' => '+91 0512 4000000',
            'address' => 'Demo Business Park, Tech City, India',
            'logo' => null,
            'footer_logo' => null,
            'website' => 'https://demo.yourdomain.com',
            'gst_number' => '22AAAAA0000A1Z5',
        ]);
    }
}

