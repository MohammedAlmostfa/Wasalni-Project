<?php

namespace Database\Seeders;

use App\Models\TripPropertie;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class tripPropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipProperties = [
    // اختيارات الراحة
    ['en' => 'Allow music during the trip', 'ar' => 'السماح بتشغيل الموسيقى أثناء الرحلة'],
    ['en' => 'Allow smoking in the vehicle', 'ar' => 'السماح بالتدخين داخل المركبة'],
    ['en' => 'Smoking prohibited in the vehicle', 'ar' => 'التدخين ممنوع داخل المركبة'],
    ['en' => 'Provide continuous air conditioning', 'ar' => 'توفير مكيف هواء يعمل بشكل مستمر'],
    ['en' => 'No air conditioning provided', 'ar' => 'عدم توفير مكيف هواء يعمل بشكل مستمر'],

    // التفاعل مع الركاب
    ['en' => 'No discussion or interaction with passengers', 'ar' => 'عدم النقاش أو التفاعل مع الركاب أثناء القيادة'],
    ['en' => 'Allow brief questions only', 'ar' => 'السماح بأسئلة قصيرة فقط'],
    ['en' => 'Limit long discussions', 'ar' => 'الحد من النقاشات الطويلة'],

    // إعدادات الطريق
    ['en' => 'Avoid main or congested roads', 'ar' => 'اختيار تجنب الطرق الرئيسية أو المزدحمة'],
    ['en' => 'Do not change route without prior request', 'ar' => 'منع تغيير المسار بدون طلب مسبق من الركاب'],

    // التفضيلات الشخصية
    ['en' => 'Allow food or drinks in the vehicle', 'ar' => 'السماح بتناول الطعام أو المشروبات داخل السيارة'],
    ['en' => 'No pets allowed in the vehicle', 'ar' => 'عدم السماح بوجود حيوانات أليفة داخل المركبة'],
    ['en' => 'No phone calls during the trip', 'ar' => 'منع إجراء المكالمات الهاتفية أثناء الرحلة'],

    // خيارات إضافية
    ['en' => 'Provide Wi-Fi during the trip', 'ar' => 'توفير اتصال واي فاي خلال الرحلة'],
    ['en' => 'Provide phone chargers during the trip', 'ar' => 'تقديم شاحن هواتف أثناء الرحلة'],
    ['en' => 'Private ride (no shared passengers)', 'ar' => 'اختيار عدم مشاركة الرحلة مع ركاب آخرين (رحلة خاصة)'],
];
        foreach($tipProperties as $tipPropertie) {
            TripPropertie::create(
                [
                'attributes'=>$tipPropertie,],
            );

        }


    }
}
