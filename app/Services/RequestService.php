<?php

namespace App\Services;

use Exception;
use App\Models\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RequestService
{
    /**
     * Create a new request.
     *
     * @param array $data
     * @return array
     */


    public function createRequest($data)
    {
        try {
            /** @var \App\Models\User $user */

            $user = Auth::user();

            // Create the request
            $request = Request::create([
                'about_user' => $data['about_user'],
                'car_type' => $data['car_type'],
                'user_id' => $user->id,
            ]);

            // Handle user profile image
            if (isset($data['User_image'])) {
                $image = $data['User_image'];
                $imageName = Str::random(32) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('private_users/profile/images', $imageName, 'public');

                $imageData = [
                    'mime_type' =>Str::after($image->getClientMimeType(), '/'),
                    'image_path' => Storage::url($path),
                    'image_name' => $imageName,
                    'tage' => 'profile',
                ];

                $user->image()->create($imageData);
            }

            // Handle car images
            if (isset($data['car_images']) && is_array($data['car_images'])) {
                foreach ($data['car_images'] as $carImage) {
                    $imageName = Str::random(32) . '.' . $carImage->getClientOriginalExtension();
                    $path = $carImage->storeAs('private_users/car/images', $imageName, 'public');

                    $imageData = [
                        'mime_type' =>Str::after($carImage->getClientMimeType(), '/'),
                        'image_path' => Storage::url($path),
                        'image_name' => $imageName,
                        'tage' => 'car',
                    ];

                    $user->image()->create($imageData);
                }
            }

            return [
                'message' => __('request.request_created_successfully'),
                'data' => $request,
                'status' => 200,
            ];

        } catch (Exception $e) {
            Log::error('Error in createRequest: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => __('request.general_error'),
                ],
            ];
        }
    }

    /**
     * Change the status of the request.
     *
     * @param array $data
     * @param Request $request
     * @return array
     */
    public function changeStatus($data, Request $request)
    {
        try {
            // Update the status of the request
            $request->update([
                'status' => $data['status'],  // New status to be updated
            ]);

            return [
                'message' => __('request.status_updated_successfully'),  // Success message for status update
                'data' => $request,  // The updated request data
                'status' => 200,  // HTTP status code 200 indicating success
            ];
        } catch (Exception $e) {
            // Log the error in the system logs
            Log::error('Error in changeStatus: ' . $e->getMessage());

            return [
                'status' => 500,  // HTTP status code 500 indicating internal server error
                'message' => [
                    'errorDetails' => __('request.general_error'),  // General error message
                ],
            ];
        }
    }
}
