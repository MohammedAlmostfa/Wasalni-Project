<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    // Authentication
    'registration_error' => "You cannot register now. Please check your account or resend the code request.",
    'verification_code_error' => "You cannot resend the code again. Please try again in an hour.",
    'verification_success' => "Verification code sent successfully.",
    'registration_general_error' => "An error occurred during registration.",
    'login_failed' => "Account not found or credentials are incorrect.",
    'login_success' => "Successfully logged in.",
    'logout_success' => "Successfully logged out.",
    'logout_error' => "An error occurred during logout.",
    'token_refresh_success' => "Token refreshed successfully.",
    'token_refresh_error' => "An error occurred while refreshing the token.",
    'google_auth_failed' => "Failed to fetch user information from Google.",
    'google_login_success' => "Successfully logged in via Google.",
    'general_error' => 'A general error occurred. Please try again later.',
    'invalid_verification_code' => "The verification code is incorrect.",
    'email_verified_and_registered' => "Email verified and user successfully registered.",
    'not_registered_yet' => "You haven't registered yet.",

    // Forget password
    'code_correct' => "The code you entered is correct.",
    'code_expired' => "The code sent to this account has expired.",
    'password_changed' => "Password changed successfully.",
    'user_not_found' => "No user found with this email address.",
    'no_code_found' => "Please try again.",
    'not_found' => "The user does not exist.",
    'invalid_code' => "The code you entered is incorrect or does not match. Please try again.",
    'invalid_key' => "The key you entered is invalid or has expired. Please request a new code.",
];
