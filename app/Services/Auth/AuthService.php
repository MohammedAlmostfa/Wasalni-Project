<?php

namespace App\Services\Auth;

use Exception;
use App\Models\City;
use App\Models\User;
use App\Models\Country;
use App\Events\Registered;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AuthService
{
    /**
     * Register a new user.
     *
     * This method handles user registration. It stores user data temporarily in the cache
     * and sends a verification code to the user's email.
     *
     * @param array $data User data: email, password, etc.
     * @return array Contains message, status, and additional data.
     */
    public function register($data)
    {
        try {
            // Generate a unique cache key for the user data
            $userDataKey = 'user_data_' . $data['email'];

            // Check if the user data is already cached
            if (Cache::has($userDataKey)) {
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => [__('auth.registration_error')],
                    ],
                ];
            }

            // Store user data in cache for 1 hour
            Cache::put($userDataKey, $data, 3600);

            // Generate a unique cache key for the verification code
            $verifkey = 'verification_code_' . $data['email'];

            // Check if the verification code already exists in the cache
            if (Cache::has($verifkey)) {
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => [__('auth.verification_code_error')],
                    ],
                ];
            }

            // Generate a random 6-digit code and store it in the cache
            $code = Cache::remember($verifkey, 3600, function () {
                return random_int(100000, 999999);
            });

            // Trigger the Registered event to send the verification email
            event(new Registered($data, $verifkey));

            // Return success response
            return [
                'message' => __('auth.verification_success'),
                'status' => 201, // HTTP status code for created
                'data' => [
                    'email' => $data['email'],
                ],
            ];
        } catch (Exception $e) {
            // Log the error if registration fails
            Log::error('Error in registration: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }

    /**
     * Verify user account using the verification code.
     *
     * This method verifies the user's account using the verification code sent to their email.
     * If the code is correct, it creates the user in the database and returns a JWT token.
     *
     * @param array $data Contains email and verification code.
     * @return array Contains message, status, and additional data.
     */
    public function verficationacount($data)
    {
        try {
            // Generate the cache key for the verification code
            $verifkey = 'verification_code_' . $data['email'];

            // Retrieve the cached verification code
            $cachedCode = Cache::get($verifkey);

            // Check if the provided code matches the cached code
            if ($cachedCode == $data['code']) {
                // Retrieve the user data from cache
                $userDataKey = 'user_data_' . $data['email'];
                $userData = Cache::get($userDataKey);

                if (!$userData) {
                    return [
                        'status' => 404,
                        'message' => [
                            'errorDetails' => [__('auth.not_registered_yet')],
                        ],
                    ];
                }

                // Create the user in the database
                $user = User::create([
                    'email' => $userData['email'],
                    'password' => bcrypt($userData['password']), // Hash the password
                    'email_verified_at' => now(), // Mark the email as verified
                ]);

                // Generate a JWT token for the user
                $token = JWTAuth::fromUser($user);

                // Clear the verification code and user data from the cache
                Cache::forget($verifkey);
                Cache::forget($userDataKey);

                // Fetch all cities
                $cities = Cache::rememberForever('cities_list', function () {
                    return City::select('id', 'cities_list')->get();
                });
                return [
                    'message' => __('auth.email_verified_and_registered'),
                    'status' => 200,
                    'data' => [
                        'token' => $token, // Return the generated token
                        'cities' => $cities, // Return cities for new users
                    ],
                ];
            } else {
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => [__('auth.invalid_verification_code')],
                    ],
                ];
            }
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in verficationacount: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }

    /**
     * Resend the verification code.
     *
     * This method resends the verification code to the user's email if the previous code has expired or the user requests a new one.
     *
     * @param array $data Contains the user's email.
     * @return array Contains message, status, and additional data.
     */
    public function resendCode($data)
    {
        try {
            // Generate the cache key for the verification code
            $verifkey = 'verification_code_' . $data['email'];

            // Check if a verification code already exists in the cache
            if (Cache::has($verifkey)) {
                // Delete the existing code from the cache
                Cache::forget($verifkey);

                // Generate a new 6-digit random code and store it in the cache for 1 hour
                $code = Cache::remember($verifkey, 3600, function () {
                    return random_int(100000, 999999);
                });

                // Trigger the Registered event to send the new verification email
                event(new Registered($data, $verifkey));

                // Return success response
                return [
                    'message' => __('auth.verification_success'),
                    'status' => 200,
                    'data' => [
                        'email' => $data['email'],
                    ],
                ];
            } else {
                // If no code exists in the cache, return an error
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => [__('auth.no_code_found')],
                    ],
                ];
            }
        } catch (Exception $e) {
            // Log the error if resending the code fails
            Log::error('Error in resendCode: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }


    /**
     * Login a user.
     *
     * This method authenticates a user using their email and password.
     * If successful, it returns a JWT token for further authenticated requests.
     *
     * @param array $credentials User credentials: email, password.
     * @return array Contains message, status, data, and authorization details.
     */
    public function login($credentials)
    {
        try {
            // Attempt to authenticate the user using JWT
            if (!$token = JWTAuth::attempt($credentials)) {
                // If authentication fails
                return [
                    'status' => 401,
                    'message' => [
                        'errorDetails' => [__('auth.login_failed')],
                    ],
                ];
            } else {
                // If authentication succeeds
                $user = Auth::user();
                return [
                    'message' => __('auth.login_success'),
                    'status' => 201, // HTTP status code for successful creation
                    'data' => [
                        'token' => $token, // Return the generated token
                        'type' => 'bearer', // Token type
                    ],
                ];
            }
        } catch (Exception $e) {
            // Log the error if login fails
            Log::error('Error in login: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }

    /**
     * Logout the authenticated user.
     *
     * This method logs out the currently authenticated user.
     *
     * @return array Contains message and status.
     */
    public function logout()
    {
        try {
            // Logout the user
            Auth::logout();
            return [
                'message' => __('auth.logout_success'),
                'status' => 200, // HTTP status code for success
            ];
        } catch (Exception $e) {
            // Log the error if logout fails
            Log::error('Error in logout: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }

    /**
     * Refresh the JWT token for the authenticated user.
     *
     * This method refreshes the JWT token for the authenticated user.
     *
     * @return array Contains message, status, user, and authorization details.
     */
    public function refresh()
    {
        try {
            // Refresh the token for the authenticated user
            return [
                'message' => __('auth.token_refresh_success'),
                'status' => 200, // HTTP status code for success
                'data' => [
                    'user' => Auth::user(), // Return the authenticated user
                    'token' => Auth::refresh(), // Return the new token
                ],
            ];
        } catch (Exception $e) {
            // Log the error if token refresh fails
            Log::error('Error in token refresh: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }

    /**
     * Login a user using Google OAuth.
     *
     * This method authenticates a user using Google OAuth.
     * If successful, it returns a JWT token for further authenticated requests.
     *
     * @param string $googleToken Google access token.
     * @return array Contains message, status, and authorization details.
     */
    public function loginwithgoogel($googleToken)
    {
        try {
            // Get user info from Google API
            $response = Http::get("https://www.googleapis.com/oauth2/v1/userinfo?access_token={$googleToken}");

            // If the request fails
            if ($response->failed()) {
                return [
                    'status' => $response->status(),
                    'message' => [
                        'errorDetails' => [__('auth.google_auth_failed')],
                    ],
                ];
            }

            // Decode the response JSON
            $userData = $response->json();

            // Find or create the user
            $user = User::firstOrCreate(
                [
                    'email' => $userData['email'], // Use email as the unique identifier
                ],
                [
                    'password' => bcrypt('123456dummy'), // Default password for Google users
                ]
            );

            // Generate a JWT token for the user
            $token = JWTAuth::fromUser($user);

            // Check if the user has profile
            if (!$user->profile) {
                // Fetch all countries for new users
                $cities = Cache::rememberForever('cities_list', function () {
                    return City::select('id', 'cities_list')->get();
                });


                return [
                    'message' => __('auth.google_login_success'),
                    'status' => 200,
                    'data' => [
                        'token' => $token,
                        'type' => 'bearer', // Token type
                        'cities' => $cities, // Return countries for new users
                    ],
                ];
            } else {
                // Return success response for existing users
                return [
                    'message' => __('auth.google_login_success'),
                    'status' => 200,
                    'data' => [
                        'token' => $token,
                        'type' => 'bearer', // Token type
                    ],
                ];
            }
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in login with Google: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }
}
