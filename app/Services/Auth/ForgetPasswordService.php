<?php

namespace App\Services\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Mail\SendForgetPasswordCodeMail;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ForgetPasswordService
{
    /**
     * Check if the email exists and send a verification code.
     *
     * @param string $email The email address to send the code to.
     * @return array An array containing the status and message.
     */
    public function checkEmail($email)
    {
        try {
            // Create a unique cache key for the user's email
            $key = $email;
            Cache::delete($key);

            // Generate a 6-digit random code and store it in the cache for 1 hour
            $code = Cache::remember($key, 3600, function () {
                return random_int(100000, 999999);
            });

            // Send the code to the user's email
            Mail::to($email)->send(new SendForgetPasswordCodeMail($code));

            return [
                'status' => 200,
                'message' => __('auth.verification_success'),
            ];
        } catch (Exception $e) {
            // Log the error and return a server error response
            Log::error("Error in checkEmail: " . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }

    /**
     * Check if the provided code matches the cached code.
     *
     * @param string $email The email address associated with the code.
     * @param string $code The code to verify.
     * @return array An array containing the status and message.
     */
    public function checkCode($email, $code)
    {
        try {
            // Create a unique cache key for the user's email
            $key = $email;

            // Check if the code exists in the cache
            if (Cache::has($key)) {
                $cached_code = Cache::get($key);

                // Verify if the provided code matches the cached code
                if ($code != $cached_code) {
                    return [
                        'status' => 400,
                        'message' => [
                            'errorDetails' => [__('auth.invalid_verification_code')],
                        ],
                    ];
                }

                return [
                    'status' => 200,
                    'message' => __('auth.code_correct'),
                ];
            } else {
                // If the code is not found in the cache, it has expired
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => [__('auth.code_expired')],
                    ],
                ];
            }
        } catch (Exception $e) {
            // Log the error and return a server error response
            Log::error("Error in checkCode: " . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }

    /**
     * Change the user's password.
     *
     * @param array $data An array containing the email, code, and new password.
     * @return array An array containing the status and message.
     */
    public function changePassword($email, $password, $code)
    {
        try {
            $key = $email;

            // Check if the code exists in the cache
            if (Cache::has($key)) {
                $cached_code = Cache::get($key);

                // Verify if the provided code matches the cached code
                if ($code != $cached_code) {
                    return [
                        'status' => 400,
                        'message' => [
                            'errorDetails' => [__('auth.invalid_code')],
                        ],
                    ];
                }

                // Find the user by email
                $user = User::where('email', $email)->first();

                if ($user) {
                    // Hash the new password and save it
                    $user->password = Hash::make($password);
                    $user->save();

                    // Delete the cached code after the password is changed
                    Cache::delete($key);

                    return [
                        'status' => 200,
                        'message' => __('auth.password_changed'),
                    ];
                } else {
                    // If the user is not found, return a 404 response
                    return [
                        'status' => 404,
                        'message' => [
                            'errorDetails' => [__('auth.user_not_found')],
                        ],
                    ];
                }
            } else {
                // If the key is not found in the cache, return a 400 response
                return [
                    'status' => 400,
                    'message' => [
                        'errorDetails' => [__('auth.invalid_key')],
                    ],
                ];
            }
        } catch (Exception $e) {
            // Log the error and return a server error response
            Log::error("Error in changePassword: " . $e->getMessage());

            return [
                'status' => 500,
                'message' => [
                    'errorDetails' => [__('auth.general_error')],
                ],
            ];
        }
    }
}
