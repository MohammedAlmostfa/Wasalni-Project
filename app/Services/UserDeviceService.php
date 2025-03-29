<?php

namespace App\Services;

use Exception;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserDeviceService
{
    /**
     * Create or update a user's device token.
     *
     * This method checks if the device (identified by 'uidd') already exists for the authenticated user.
     * If it exists, it updates the device's FCM token. If not, it creates a new device record with the given token.
     *
     * @param array $data - The data containing the FCM token, user ID, and device unique ID ('uidd').
     *
     * @return array - The result of the operation, with status and message.
     */
    public function createUserDevice($data)
    {
        try {
            // Get the currently authenticated user
            $user = Auth::user();

            // Check if the device with the given 'uidd' exists for the authenticated user
            $existingDevice = $user->devices()->where('uidd', $data["uidd"])->first();

            // If the device exists, update the existing record with the new FCM token
            if ($existingDevice) {
                $existingDevice->update([
                    'fcm_token' => $data["fcm_token"]??$existingDevice->fcm_token,  // Update the FCM token for the device
                    'uidd' => $data["uidd"] ?? $existingDevice->uidd,            // Ensure the device ID is updated
                    'user_id' => $data["user_id"] ?? $existingDevice->user_id,      // Store the user ID
                ]);

                // Return a success response indicating that the token was updated
                return [
                    'status' => 200,
                    'message' => 'Token updated successfully',
                ];
            } else {
                // If the device doesn't exist, create a new device record
                $user->devices()->create([
                    'fcm_token' => $data["fcm_token"],  // Store the FCM token
                    'uidd' => $data["uidd"],            // Store the unique device ID
                ]);

                // Return a success response indicating that the token was stored successfully
                return [
                    'status' => 200,
                    'message' => 'Token stored successfully',
                ];
            }
        } catch (Exception $e) {
            // Log the error if an exception occurs
            Log::error('Error in createUserDevice: ' . $e->getMessage());

            // Return a generic error response with the error details
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('trip.general_error')],
                ],
            ];
        }
    }
}
