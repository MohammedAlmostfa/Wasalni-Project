<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        $cities = [
            ['en' => 'Muthanna', 'ar' => 'المثنى'],
            ['en' => 'Al-Qadisiyyah', 'ar' => 'القادسية'],
            ['en' => 'Sulaymaniyah', 'ar' => 'السليمانية'],
            ['en' => 'Erbil', 'ar' => 'أربيل'],
            ['en' => 'Anbar', 'ar' => 'الأنبار'],
            ['en' => 'Babil', 'ar' => 'بابل'],
            ['en' => 'Baghdad', 'ar' => 'بغداد'],
            ['en' => 'Basra', 'ar' => 'البصرة'],
            ['en' => 'Duhok', 'ar' => 'دهوك'],
            ['en' => 'Dhi Qar', 'ar' => 'ذي قار'],
            ['en' => 'Diyala', 'ar' => 'ديالى'],
            ['en' => 'Salah ad-Din', 'ar' => 'صلاح الدين'],
            ['en' => 'Halabja', 'ar' => 'حلبجة'],
            ['en' => 'Karbala', 'ar' => 'كربلاء'],
            ['en' => 'Kirkuk', 'ar' => 'كركوك'],
            ['en' => 'Maysan', 'ar' => 'ميسان'],
            ['en' => 'Najaf', 'ar' => 'النجف'],
            ['en' => 'Nineveh', 'ar' => 'نينوى'],
            ['en' => 'Wasit', 'ar' => 'واسط']
        ];

        foreach ($cities as $city) {
            City::create([
    'city_name' => $city,
            ]);
        }
    }
}
