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
     * Create a new request for the authenticated user.
     *
     * @param array $data The input data for creating the request.
     * @return array Response with message, status code, and request data if successful.
     */
    public function createRequest($data)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Create the request associated with the user
            $request = Request::create([
                'about_user' => $data['about_user'],
                'car_type' => $data['car_type'],
                'user_id' => $user->id,
            ]);

            // Handle uploading the user profile image
            if (isset($data['User_image'])) {
                $image = $data['User_image'];
                $imageName = Str::random(32) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('private_users/profile/images', $imageName, 'public');

                $imageData = [
                    'mime_type' => Str::after($image->getClientMimeType(), '/'),
                    'image_path' => Storage::url($path),
                    'image_name' => $imageName,
                    'tage' => 'profile', // typo: should probably be 'tag'
                ];

                $user->image()->create($imageData);
            }

            // Handle uploading car images (if multiple)
            if (isset($data['car_images']) && is_array($data['car_images'])) {
                foreach ($data['car_images'] as $carImage) {
                    $imageName = Str::random(32) . '.' . $carImage->getClientOriginalExtension();
                    $path = $carImage->storeAs('private_users/car/images', $imageName, 'public');

                    $imageData = [
                        'mime_type' => Str::after($carImage->getClientMimeType(), '/'),
                        'image_path' => Storage::url($path),
                        'image_name' => $imageName,
                        'tage' => 'car', // typo: should probably be 'tag'
                    ];

                    $user->image()->create($imageData);
                }
            }

            // Return success response
            return [
                'message' => __('request.request_created_successfully'),
                'data' => $request,
                'status' => 200,
            ];

        } catch (Exception $e) {
            // Log the exception
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
     * Update the status of a given request (e.g., accepted or rejected).
     *
     * @param array $data The new status value (e.g., "accepted", "rejected")
     * @param Request $request The request model instance to be updated
     * @return array Response with message, status, and data
     */
    public function updataStatus($data, Request $request)
    {
        try {
            // Update the status field on the request model
            $request->update([
                'status' => $data['status'], // Will be converted via setStatusAttribute
            ]);

            return [
                'message' => __('request.status_updated_successfully'),
                'data' => $request,
                'status' => 200,
            ];

        } catch (Exception $e) {
            // Log any exception that occurs
            Log::error('Error in changeStatus: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => __('request.general_error'),
                ],
            ];
        }
    }
}
