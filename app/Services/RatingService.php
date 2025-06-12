<?php 

namespace App\Services;

use App\Models\Rating;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class RatingService
 * 
 * Handles the business logic related to user ratings, including retrieval, creation, updating, and deletion.
 */
class RatingService
{
   

    /**
     * Store a new rating in the database.
     * 
     * @param array $data Validated request data.
     * @return array Response containing status and message.
     */
    public function storeRate($data)
    {
        try {
            // Create a new rating record
            $rate = Rating::create([
                'user_id'       => auth()->user()->id,  // Authenticated user
                'rate'          => $data["rate"],       // Rating value (1-5)
                'review'        => $data["review"],     // Optional review text
                'rated_user_id' => $data["rated_user_id"], // ID of rated user
            ]);

            return [
                'status'  => 200,
                'message' => __('rate.create_successful'),
            ];
        } catch (Exception $e) {
            // Log error and return failure response
            Log::error('Error in storeRate: ' . $e->getMessage());

            return [
                'status'  => 500,
                'message' => __('general.failed'),
            ];
        }
    }

    /**
     * Update an existing rating record.
     * 
     * @param Rating $rating The rating model instance to update.
     * @param array $data Data for updating the rating.
     * @return array Response containing status and message.
     */
    public function updateRate(Rating $rating, $data)
    {
        try {
            // Update provided fields only
            $rating->update([
                'rate'   => $data["rate"] ?? $rating->rate,
                'review' => $data["review"] ?? $rating->review,
            ]);

            return [
                'status'  => 200,
                'message' => __('rate.update_successful'),
            ];
        } catch (Exception $e) {
            // Log error and return failure response
            Log::error('Error in updateRate: ' . $e->getMessage());

            return [
                'status'  => 500,
                'message' => __('general.failed'),
            ];
        }
    }

    /**
     * Soft delete a rating record.
     * 
     * @param Rating $rating The rating model instance to delete.
     * @return array Response containing status and message.
     */
    public function deleteRate(Rating $rating)
    {
        try {
            // Perform soft delete
            $rating->delete();

            return [
                'status'  => 200,
                'message' => __('rate.delete_successful'),
            ];
        } catch (Exception $e) {
            // Log error and return failure response
            Log::error('Error in deleteRate: ' . $e->getMessage());

            return [
                'status'  => 500,
                'message' => __('general.failed'),
            ];
        }
    }
}