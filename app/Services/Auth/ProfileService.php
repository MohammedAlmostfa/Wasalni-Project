<?php

namespace App\Services\Auth;

use Exception;
use App\Models\City;
use App\Models\User;
use App\Models\Country;
use App\Models\Profile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProfileService
{
    /**
     * Create a new profile for the authenticated user.
     *
     * @param array $data The profile data to be stored.
     * @return array An array containing the result of the operation.
     *               - 'message': A message describing the result.
     *               - 'data': The created or existing profile data.
     *               - 'status': HTTP status code.
     */
    public function createProfile($data)
    {
        try {
            $user = Auth::user();

            // Check if the user already has a profile
            if (!$user->profile) {
                // Create a profile for the user
                $profile = Profile::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'gender' => $data['gender'] ?? null, // Optional field
                    'birthday' => $data['birthday'] ?? null, // Optional field
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'user_id' => $user->id, // Link profile to the user
                    //'country_id' => $data['country_id'],
                    'city_id' => $data['city_id'],
                ]);

                // Find the country by ID
                //  $country = Country::find($data['country_id']);

                // Check if cities for the selected country already exist
                //    if (!City::where('country_id', $data['country_id'])->exists()) {
                // Make API call to fetch cities for the selected country
                //       $response = Http::post('https://countriesnow.space/api/v0.1/countries/cities', [
                //           'country' => $country->country_name,
                //       ]);

                // If the API call is successful, save the cities to the database
                //       if ($response->successful()) {
                //          $cities = $response->json()['data']; // Extract cities from the response

                // Create cities in the database
                //       foreach ($cities as $cityName) {
                //          City::create([
                ///                'city_name' => $cityName,
                //              'country_id' => $country->id,
                //        ]);
                //    }
                //   }
                //}

                return [
                    'message' => 'Profile created successfully',
                    'data' => $profile,
                    'status' => 200,
                ];
            } else {
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => ['User already has a profile.'],
                    ],
                ];
            }
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in creating profile: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while creating the profile.'],
                ],
            ];
        }
    }

    /**
     * Update the profile of the authenticated user.
     *
     * @param array $data The updated profile data.
     * @return array An array containing the result of the operation.
     *               - 'message': A message describing the result.
     *               - 'data': The updated profile data.
     *               - 'status': HTTP status code.
     */
    public function updateProfile($data)
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;

            // Check if the user already has a profile
            if ($profile) {
                // Update the profile
                $profile->update([
                    'first_name' => $data['first_name'] ?? $profile->first_name,
                    'last_name' => $data['last_name'] ?? $profile->last_name,
                    'gender' => $data['gender'] ?? $profile->gender,
                    'birthday' => $data['birthday'] ?? $profile->birthday,
                    'phone' => $data['phone'] ?? $profile->phone,
                    'address' => $data['address'] ?? $profile->address,
                    //  'country_id' => $data['country_id'] ?? $profile->country_id,
                    'city_id' => $data['city_id'] ?? $profile->city_id,
                ]);
                $key = 'userdata_' . $user->id;

                // Forget the cache entry
                Cache::forget($key);

                // Find the country by ID
                $country = Country::find($profile->country_id);

                // Check if cities for the selected country already exist
                // if (!City::where('country_id', $profile->country_id)->exists()) {
                // Make API call to fetch cities for the selected country
                //     $response = Http::post('https://countriesnow.space/api/v0.1/countries/cities', [
                //          'country' => $country->country_name,
                //     ]);

                // If the API call is successful, save the cities to the database
                //    if ($response->successful()) {
                //      $cities = $response->json()['data']; // Extract cities from the response

                // Create cities in the database
                //      foreach ($cities as $cityName) {
                //       City::create([
                //             'city_name' => $cityName,
                //          'country_id' => $country->id,
                //       ]);
                //   }
                // }
                // }

                return [
                    'message' => 'Profile updated successfully',
                    'data' => $profile,
                    'status' => 200,
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => [
                        'errorDetails' => ['User does not have a profile.'],
                    ],
                ];
            }
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in updating profile: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => ['An error occurred while updating the profile.'],
                ],
            ];
        }
    }

    /**
     * Get the authenticated user's data.
     *
     * @return array Contains message, status, and user data.
     *               - 'message': A message describing the result.
     *               - 'data': The user's profile data.
     *               - 'status': HTTP status code.
     */
    public function getMe()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Generate a unique cache key
            $key = 'userdata_' . $user->id;

            // Retrieve or cache the user data
            $userData = Cache::remember($key, 86400, function () use ($user) {
                // Ensure profile and city relationships are loaded
                $user->load(['profile.city']);
                return [
                    'id' => $user->id,
                    'name' => $user->profile->first_name . ' ' . $user->profile->last_name,
                    'email' => $user->email,
                    'gender' => $user->profile->gender,
                    'birthday' => $user->profile->birthday,
                    'phone' => $user->profile->phone,
                    'address' => $user->profile->address,
                    "city_name" => $user->profile->city->city_name,
                ];
            });

            // Return user data
            return [
                'message' => 'User data retrieved successfully',
                'status' => 200,
                'data' => $userData,
            ];
        } catch (Exception $e) {
            // Log the error if fetching user data fails
            Log::error('Error in getMe: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => 'An error occurred while fetching user data.',
                'errorDetails' => $e->getMessage(),
            ];
        }
    }
}
