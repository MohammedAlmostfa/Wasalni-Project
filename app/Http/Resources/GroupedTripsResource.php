<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupedTripsResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->groupBy(function ($item) {
            return $item->trip_start->format('Y-m-d'); // ترتيب حسب التاريخ
        })->map(function ($group, $date) {
            return [
                'date' => $date,
                'trips' => TripResource::collection($group), // تحويل كل مجموعة ل Resource
            ];
        })->values(); // تحويل النتيجة إلى مصفوفة مرقمة
    }
}
