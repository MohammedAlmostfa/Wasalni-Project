تمام، يعني بدك العناوين تكون **أقصر وبدون أفعال**، بصيغة **وصفية** أكتر، متل:

- بدل "تم تأكيد الحجز" ⟵ تصير: **"تأكيد الحجز"**
- بدل "تم رفض طلب السائق" ⟵ تصير: **"رفض طلب السائق"**

هيني عدّلتلك كل العناوين بصيغة مختصرة ووصفية، بدون أفعال:

```php
<?php

return [

    // =======================
    // Booking notifications
    // =======================
    'booking_accepted' => [
        'title' => [
            'en' => 'Booking Confirmation',
            'ar' => 'تأكيد الحجز',
        ],
    ],

    'booking_canceled' => [
        'title' => [
            'en' => 'Booking Cancellation',
            'ar' => 'إلغاء الحجز',
        ],
    ],

    'booking_rejected' => [
        'title' => [
            'en' => 'Booking Rejection',
            'ar' => 'رفض الحجز',
        ],
    ],


    // =======================
    // Trip-related notifications
    // =======================
    'trip_Ending' => [
        'title' => [
            'en' => 'Trip Completion',
            'ar' => 'انتهاء الرحلة',
        ],
    ],

    'trip_booking_completed' => [
        'title' => [
            'en' => 'Trip Booking',
            'ar' => 'حجز الرحلة',
        ],
    ],

    'trip_created' => [
        'title' => [
            'en' => 'New Trip',
            'ar' => 'رحلة جديدة',
        ],
    ],


    // =======================
    // Driver status notifications
    // =======================
    'driver_accepted' => [
        'title' => [
            'en' => 'Driver Request Approval',
            'ar' => 'موافقة طلب السائق',
        ],
    ],

    'driver_rejected' => [
        'title' => [
            'en' => 'Driver Request Rejection',
            'ar' => 'رفض طلب السائق',
        ],
    ],

];
