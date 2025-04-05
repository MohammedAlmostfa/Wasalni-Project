<?php

namespace App\Services\Auth;

use Exception;
use App\Models\Country;
use App\Models\Profile;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProfileService
 *
 * Provides functionalities related to user profile management.
 */
class ProfileService
{
    /**
     * Create a new user profile.
     *
     * @param array $data The profile data.
     * @return array The result containing status, message, and data.
     */
    public function createProfile(array $data): array
    {
        try {
            $user = Auth::user();

            // Check if the user already has a profile to prevent duplicates
            if (!$user->profile) {
                $profile = Profile::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'gender' => $data['gender'] ?? null,
                    'birthday' => $data['birthday'] ?? null,
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'user_id' => $user->id,
                    'city_id' => $data['city_id'],
                ]);

                return [
                    'message' => 'Profile created successfully',
                    'data' => $profile,
                    'status' => 200,
                ];
            }

            return [
                'status' => 400,
                'message' => [
                    'errorDetails' => ['User already has a profile.'],
                ],
            ];
        } catch (Exception $e) {
            Log::error('Profile creation failed: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while creating the profile.'],
                ],
            ];
        }
    }

    /**
     * Update an existing user profile.
     *
     * @param array $data The updated profile data.
     * @return array The result containing status, message, and data.
     */
    public function updateProfile(array $data): array
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;

            if ($profile) {
                $profile->update([
                    'first_name' => $data['first_name'] ?? $profile->first_name,
                    'last_name' => $data['last_name'] ?? $profile->last_name,
                    'gender' => $data['gender'] ?? $profile->gender,
                    'birthday' => $data['birthday'] ?? $profile->birthday,
                    'phone' => $data['phone'] ?? $profile->phone,
                    'address' => $data['address'] ?? $profile->address,
                    'city_id' => $data['city_id'] ?? $profile->city_id,
                ]);

                // Clear cached user data
                Cache::forget('userdata_' . $user->id);

                return [
                    'message' => 'Profile updated successfully',
                    'data' => $profile,
                    'status' => 200,
                ];
            }

            return [
                'status' => 404,
                'message' => [
                    'errorDetails' => ['User does not have a profile.'],
                ],
            ];
        } catch (Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while updating the profile.'],
                ],
            ];
        }
    }

    /**
     * Retrieve the authenticated user's data.
     *
     * @return array The result containing status, message, and user data.
     */
    public function getMe(): array
    {
        try {
            /** @var \App\Models\User $user */

            $user = Auth::user();
            $cacheKey = 'userdata_' . $user->id;

            // Cache user data for 24 hours (86400 seconds)
            $userData = Cache::remember($cacheKey, 86400, function () use ($user) {
                $user->load(['profile.city']);
                return [
                    'id' => $user->id,
                    'name' => $user->profile->first_name . ' ' . $user->profile->last_name,
                    'email' => $user->email,
                    'gender' => $user->profile->gender,
                    'birthday' => $user->profile->birthday,
                    'phone' => $user->profile->phone,
                    'address' => $user->profile->address,
                    'city_name' => $user->profile->city->city_name,
                ];
            });

            return [
                'message' => 'User data retrieved successfully',
                'status' => 200,
                'data' => $userData,
            ];
        } catch (Exception $e) {
            Log::error('Failed to fetch user data: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => 'An error occurred while fetching user data.',
                'errorDetails' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }

    /**
     * Create private user data for the authenticated user.
     *
     * @param array $data The private user data.
     * @return array The result containing status, message, and data.
     */
    public function createPrivateUserData(array $data): array
    {
        try {
            $privateUserRole = Role::where('name', 'PrivateUser')->firstOrFail();
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Process and store the uploaded image
            $image = $data['image'];
            $imageName = Str::random(32) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('private_users/images', $imageName, 'public');

            // Prepare pivot data including image information
            $pivotData = [
                'about_User' => $data['about_User'] ?? 'This user is a private user',
                'car_Type' => $data['car_Type'] ?? 'SUV',
                'mime_type' => $image->getClientMimeType(),
                'image_path' => Storage::url($path),
                'image_name' => $imageName,
            ];

            // Attach role with pivot data
            $user->roles()->syncWithoutDetaching([$privateUserRole->id => $pivotData]);

            return [
                'status' => 200,
                'message' => 'Private user data created successfully',
                'data' => [
                    'about_User' => $pivotData['about_User'],
                    'car_Type' => $pivotData['car_Type'],
                    'image_url' => $pivotData['image_path'],
                ]
            ];
        } catch (Exception $e) {
            Log::error('Private user data creation failed: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => 'An error occurred while creating private user data.',
                'errorDetails' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }

    /**
     * Update private user data for the authenticated user.
     *
     * @param array $data The updated private user data.
     * @return array The result containing status, message, and updated data.
     */
    public function updatePrivateUserData(array $data): array
    {
        try {
            $privateUserRole = Role::where('name', 'PrivateUser')->firstOrFail();
            /** @var \App\Models\User $user */

            $user = Auth::user();

            // Get current pivot data
            $currentPivot = $user->roles()
                                ->where('role_id', $privateUserRole->id)
                                ->first()
                                ->pivot;

            $imageData = [];
            if (isset($data['image'])) {
                // Delete old image if exists
                if ($currentPivot->image_name) {
                    Storage::disk('public')->delete("private_users/images/{$currentPivot->image_name}");
                }

                // Store new image
                $image = $data['image'];
                $imageName = Str::random(32) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('private_users/images', $imageName, 'public');

                $imageData = [
                    'mime_type' => $image->getClientMimeType(),
                    'image_path' => Storage::url($path),
                    'image_name' => $imageName,
                ];
            }

            // Prepare update data
            $updateData = [
                'about_User' => $data['about_User'] ?? $currentPivot->about_User,
                'car_Type' => $data['car_Type'] ?? $currentPivot->car_Type,
            ] + $imageData;

            // Update pivot data
            $user->roles()->updateExistingPivot($privateUserRole->id, $updateData);

            return [
                'status' => 200,
                'message' => 'Private user data updated successfully',
                'data' => [
                    'about_User' => $updateData['about_User'],
                    'car_Type' => $updateData['car_Type'],
                    'image_url' => $updateData['image_path'] ?? $currentPivot->image_path,
                ]
            ];
        } catch (Exception $e) {
            Log::error('Private user data update failed: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => 'An error occurred while updating private user data.',
                'errorDetails' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}
