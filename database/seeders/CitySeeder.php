<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {

        $cities = [
            // Baghdad Governorate
            ['en' => 'Baghdad (Baghdad)', 'ar' => 'بغداد (بغداد)'],
                ['en' => 'Baghdad (Al-Karkh)', 'ar' => 'بغداد (الكرخ)'],
                ['en' => 'Baghdad (Al-Rusafa)', 'ar' => 'بغداد (الرصافة)'],
                ['en' => 'Baghdad (Al-Fadhil)', 'ar' => 'بغداد (الفضل)'],
                ['en' => 'Baghdad (Al-Sadriya)', 'ar' => 'بغداد (الصدرية)'],
                ['en' => 'Baghdad (Bab Al-Sheikh)', 'ar' => 'بغداد (باب الشيخ)'],
                ['en' => 'Baghdad (Al-Athefiya)', 'ar' => 'بغداد (العطيفية)'],
                ['en' => 'Baghdad (Al-Kadhimiya)', 'ar' => 'بغداد (الكاظمية)'],
                ['en' => 'Baghdad (Al-Adhamiya)', 'ar' => 'بغداد (الأعظمية)'],
                ['en' => 'Baghdad (Al-Shaab)', 'ar' => 'بغداد (الشعب)'],
                ['en' => 'Baghdad (Al-Karada)', 'ar' => 'بغداد (الكرادة)'],
                ['en' => 'Baghdad (Al-Mansour)', 'ar' => 'بغداد (المنصور)'],
                ['en' => 'Baghdad (Al-Qadisiyah)', 'ar' => 'بغداد (القادسية)'],
                ['en' => 'Baghdad (Al-Maamoun)', 'ar' => 'بغداد (المأمون)'],
                ['en' => 'Baghdad (Al-Dora)', 'ar' => 'بغداد (الدورة)'],
                ['en' => 'Baghdad (Al-Ghazaliya)', 'ar' => 'بغداد (الغزالية)'],
                ['en' => 'Baghdad (Al-Jadriya)', 'ar' => 'بغداد (الجادرية)'],
                ['en' => 'Baghdad (Al-Zaafaraniya)', 'ar' => 'بغداد (الزعفرانية)'],
                ['en' => 'Baghdad (Al-Madaen)', 'ar' => 'بغداد (المدائن)'],
                ['en' => 'Baghdad (Abu Ghraib)', 'ar' => 'بغداد (أبو غريب)'],
                ['en' => 'Baghdad (Al-Shula)', 'ar' => 'بغداد (الشعلة)'],
                ['en' => 'Baghdad (Al-Hurriya)', 'ar' => 'بغداد (الحرية)'],
                ['en' => 'Baghdad (Al-Taji)', 'ar' => 'بغداد (التاجي)'],
                ['en' => 'Baghdad (Al-Tarmiyah)', 'ar' => 'بغداد (الطارمية)'],
                ['en' => 'Baghdad (Al-Mahmoudiya)', 'ar' => 'بغداد (المحمودية)'],
                // Additional Baghdad cities
                ['en' => 'Baghdad (Al-Karradah)', 'ar' => 'بغداد (الكرادة)'],
                ['en' => 'Baghdad (Al-Saydiya)', 'ar' => 'بغداد (السيدية)'],
                ['en' => 'Baghdad (Al-Yarmouk)', 'ar' => 'بغداد (اليرموك)'],
                ['en' => 'Baghdad (Al-Bayaa)', 'ar' => 'بغداد (البياع)'],
                ['en' => 'Baghdad (Al-Hurriya City)', 'ar' => 'بغداد (مدينة الحرية)'],
                ['en' => 'Baghdad (Al-Shuala)', 'ar' => 'بغداد (الشعلة)'],
                ['en' => 'Baghdad (Al-Amel)', 'ar' => 'بغداد (العامل)'],
                ['en' => 'Baghdad (Al-Jihad)', 'ar' => 'بغداد (الجهاد)'],
                ['en' => 'Baghdad (Al-Sayyidiyah)', 'ar' => 'بغداد (السيدية)'],
                ['en' => 'Baghdad (Al-Khadraa)', 'ar' => 'بغداد (الخضراء)'],
                ['en' => 'Baghdad (Al-Rasheed)', 'ar' => 'بغداد (الرشيد)'],
                ['en' => 'Baghdad (Al-Dawra)', 'ar' => 'بغداد (الدورة)'],
                ['en' => 'Baghdad (Al-Salam)', 'ar' => 'بغداد (السلام)'],
                ['en' => 'Baghdad (Al-Qahira)', 'ar' => 'بغداد (القاهرة)'],
                ['en' => 'Baghdad (Al-Waziriya)', 'ar' => 'بغداد (الوزيرية)'],
                ['en' => 'Baghdad (Al-Utafiyya)', 'ar' => 'بغداد (العطيفية)'],
                ['en' => 'Baghdad (Al-Sa adoon)', 'ar' => 'بغداد (السعدون)'],
                ['en' => 'Baghdad (Al-Salhiya)', 'ar' => 'بغداد (الصالحية)'],
                ['en' => 'Baghdad (Al-Bataween)', 'ar' => 'بغداد (البتاوين)'],
                ['en' => 'Baghdad (Al-Mansour)', 'ar' => 'بغداد (المنصور)'],
                ['en' => 'Baghdad (Al-Adel)', 'ar' => 'بغداد (العادل)'],
                ['en' => 'Baghdad (Al-Harthiya)', 'ar' => 'بغداد (الحارثية)'],
                ['en' => 'Baghdad (Al-Zayouna)', 'ar' => 'بغداد (الزيونة)'],
                ['en' => 'Baghdad (Al-Muthanna)', 'ar' => 'بغداد (المثنى)'],
                ['en' => 'Baghdad (Al-Qadisiyah)', 'ar' => 'بغداد (القادسية)'],
                ['en' => 'Baghdad (Al-Maamoun)', 'ar' => 'بغداد (المأمون)'],
                ['en' => 'Baghdad (Al-Jamia)', 'ar' => 'بغداد (الجامعة)'],
                ['en' => 'Baghdad (Al-Saadoon)', 'ar' => 'بغداد (السعدون)'],
                ['en' => 'Baghdad (Al-Salhiya)', 'ar' => 'بغداد (الصالحية)'],
                ['en' => 'Baghdad (Al-Bataween)', 'ar' => 'بغداد (البتاوين)'],
                ['en' => 'Baghdad (Al-Mansour)', 'ar' => 'بغداد (المنصور)'],
                ['en' => 'Baghdad (Al-Adel)', 'ar' => 'بغداد (العادل)'],
                ['en' => 'Baghdad (Al-Harthiya)', 'ar' => 'بغداد (الحارثية)'],
                ['en' => 'Baghdad (Al-Zayouna)', 'ar' => 'بغداد (الزيونة)'],
                ['en' => 'Baghdad (Al-Muthanna)', 'ar' => 'بغداد (المثنى)'],
                ['en' => 'Baghdad (Al-Qadisiyah)', 'ar' => 'بغداد (القادسية)'],
                ['en' => 'Baghdad (Al-Maamoun)', 'ar' => 'بغداد (المأمون)'],
                ['en' => 'Baghdad (Al-Jamia)', 'ar' => 'بغداد (الجامعة)'],

            // Basra Governorate
            ['en' => 'Basra (Basra)', 'ar' => 'البصرة (البصرة)'],
                ['en' => 'Basra (Shatt Al-Arab)', 'ar' => 'البصرة (شط العرب)'],
                ['en' => 'Basra (Abu Al-Khasib)', 'ar' => 'البصرة (أبو الخصيب)'],
                ['en' => 'Basra (Al-Zubair)', 'ar' => 'البصرة (الزبير)'],
                ['en' => 'Basra (Al-Qurna)', 'ar' => 'البصرة (القرنة)'],
                ['en' => 'Basra (Umm Qasr)', 'ar' => 'البصرة (أم قصر)'],
                ['en' => 'Basra (Al-Faw)', 'ar' => 'البصرة (الفاو)'],
                ['en' => 'Basra (Al-Midaina)', 'ar' => 'البصرة (المدينة)'],
                ['en' => 'Basra (Al-Hartha)', 'ar' => 'البصرة (الهارثة)'],
                ['en' => 'Basra (Al-Tanuma)', 'ar' => 'البصرة (التنومة)'],
                ['en' => 'Basra (Al-Siba)', 'ar' => 'البصرة (السيبة)'],
                // Additional Basra cities
                ['en' => 'Basra (Al-Ashar)', 'ar' => 'البصرة (العشار)'],
                ['en' => 'Basra (Al-Maqal)', 'ar' => 'البصرة (المقلاع)'],
                ['en' => 'Basra (Al-Baradiyah)', 'ar' => 'البصرة (البراضعية)'],
                ['en' => 'Basra (Al-Jubaila)', 'ar' => 'البصرة (الجبيليات)'],
                ['en' => 'Basra (Al-Dayr)', 'ar' => 'البصرة (الدير)'],
                ['en' => 'Basra (Al-Qibla)', 'ar' => 'البصرة (القبلة)'],
                ['en' => 'Basra (Al-Madinah)', 'ar' => 'البصرة (المدينة)'],
                ['en' => 'Basra (Al-Seeba)', 'ar' => 'البصرة (السيبة)'],
                ['en' => 'Basra (Al-Tanuma)', 'ar' => 'البصرة (التنومة)'],
                ['en' => 'Basra (Al-Hartha)', 'ar' => 'البصرة (الهارثة)'],

                ['en' => 'Nineveh (Mosul)', 'ar' => 'نينوى (الموصل)'],
                ['en' => 'Nineveh (Tal Afar)', 'ar' => 'نينوى (تلعفر)'],
                ['en' => 'Nineveh (Sinjar)', 'ar' => 'نينوى (سنجار)'],
                ['en' => 'Nineveh (Al-Hamdaniya)', 'ar' => 'نينوى (الحمدانية)'],
                ['en' => 'Nineveh (Al-Baaj)', 'ar' => 'نينوى (البعاج)'],
                ['en' => 'Nineveh (Al-Qayyarah)', 'ar' => 'نينوى (القيارة)'],
                ['en' => 'Nineveh (Al-Shura)', 'ar' => 'نينوى (الشورة)'],

                  ['en' => 'Erbil (Erbil)', 'ar' => 'أربيل (أربيل)'],
                ['en' => 'Erbil (Ankawa)', 'ar' => 'أربيل (عنكاوا)'],
                ['en' => 'Erbil (Koya)', 'ar' => 'أربيل (كويه)'],
                ['en' => 'Erbil (Shaqlawa)', 'ar' => 'أربيل (شقلاوة)'],
                ['en' => 'Erbil (Soran)', 'ar' => 'أربيل (سوران)'],
                ['en' => 'Erbil (Rawanduz)', 'ar' => 'أربيل (رواندوز)'],


                  ['en' => 'Erbil (Erbil)', 'ar' => 'أربيل (أربيل)'],
                ['en' => 'Erbil (Ankawa)', 'ar' => 'أربيل (عنكاوا)'],
                ['en' => 'Erbil (Koya)', 'ar' => 'أربيل (كويه)'],
                ['en' => 'Erbil (Shaqlawa)', 'ar' => 'أربيل (شقلاوة)'],
                ['en' => 'Erbil (Soran)', 'ar' => 'أربيل (سوران)'],
                ['en' => 'Erbil (Rawanduz)', 'ar' => 'أربيل (رواندوز)'],


                ['en' => 'Najaf (Najaf)', 'ar' => 'النجف (النجف)'],
                ['en' => 'Najaf (Kufa)', 'ar' => 'النجف (الكوفة)'],
                ['en' => 'Najaf (Al-Manathera)', 'ar' => 'النجف (المناذرة)'],
                ['en' => 'Najaf (Al-Hira)', 'ar' => 'النجف (الحيرة)'],


                 ['en' => 'Karbala (Karbala)', 'ar' => 'كربلاء (كربلاء)'],
                ['en' => 'Karbala (Al-Hindiya)', 'ar' => 'كربلاء (الهندية)'],
                ['en' => 'Karbala (Ain Al-Tamr)', 'ar' => 'كربلاء (عين تمر)'],
        ];

        foreach ($cities as $city) {
            City::create([
    'city_name' => json_encode($city)
]);

        }
    }
}
