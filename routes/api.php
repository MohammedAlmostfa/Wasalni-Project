<?php

use App\Models\UserDevice;
use Illuminate\Http\Request;

use App\Models\FavoritePerson;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SavedTripController;
use App\Http\Controllers\UserDeviceController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\FavoritePersonController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\RequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)

// User login
Route::post('/login', [AuthController::class, 'login']); // Handles user login
Route::post('/loginwithGoogel', [AuthController::class, 'loginwithGoogel']); // Handles login via Google OAuth

// User registration
Route::post('/register', [AuthController::class, 'register']); // Handles user registration

// User logout
Route::post('logout', [AuthController::class, 'logout']); // Logs out the authenticated user

// Refresh JWT token
Route::post('refresh', [AuthController::class, 'refresh']); // Refreshes the JWT token

// Email verification routes
Route::post('/verify-email', [AuthController::class, 'verify']); // Verifies user's email
Route::post('/resendCode', [AuthController::class, 'resendCode']); // Resends the verification code

// Change password routes
Route::post('/changePassword', [ForgetPasswordController::class, 'changePassword']); // Handles password change
Route::post('/checkEmail', [ForgetPasswordController::class, 'checkEmail']); // Checks if the email exists for password reset
Route::post('/checkCode', [ForgetPasswordController::class, 'checkCode']); // Verifies a password reset code

// Protected routes (require authentication)
Route::middleware('auth:api')->group(function () {
    // Get authenticated user details
    Route::get('/me', [ProfileController::class, 'getme']); // Retrieves details of the logged-in user

    // API resource routes for users (CRUD operations)
    Route::apiResource('user', UserController::class); // Handles CRUD for users

    // API resource routes for favorite persons
    Route::apiResource("favorite", FavoritePersonController::class); // Handles CRUD operations for favorite persons
    Route::post("favorite/remove", [FavoritePersonController::class,'remove']);

    // API resource routes for countries (CRUD operations)
    Route::apiResource('countries', CountryController::class); // Handles CRUD for countries

    // API resource routes for cities (CRUD operations)
    Route::apiResource('cities', CityController::class); // Handles CRUD for cities
    // API resource routes for cities (CRUD operations)
    Route::apiResource('userdevice', UserDeviceController::class); // Handles CRUD for cities

    // API resource routes for trips (CRUD operations)
    Route::apiResource('trip', TripController::class)->names([
        'index' => 'trip.list', // List all trips
        'store' => 'trip.create', // Create a new trip
        'show' => 'trip.details', // Get details of a specific trip
        'update' => 'trip.update', // Update a trip
        'destroy' => 'trip.delete', // Delete a trip
    ]);
    Route::post('/trip/ending/{id}', [TripController::class, 'endingTrip'])->name('trip.ended'); // Marks a trip as ended

    // Additional trip-related routes
    Route::get('/show_his_Trip', [TripController::class, 'showhisTrips']); // Retrieve trips for the authenticated user
    Route::get('/show_user_Trip/{id}', [TripController::class, 'showuserTrips']); // Retrieve trips for a specific user

    // Saved trips routes
    Route::get('trips/saved', [SavedTripController::class, 'index']); // Retrieve all saved trips for the authenticated user
    Route::post('trips/saved', [SavedTripController::class, 'AddToSavedTrip']); // Add a trip to the saved trips list
    Route::post('trips/remove', [SavedTripController::class, 'Removefromsavedtrip']); // Remove a trip from the saved trips list

    // API resource routes for user profile
    Route::put('profile', [ProfileController::class, 'update']); // Updates user profile
    Route::apiResource('profile', ProfileController::class); // Handles additional profile-related actions
    Route::post('profile/privatuser/store', [ProfileController::class, 'creatprivatuserdata']); // Add a trip to the saved trips list
    Route::post('profile/privatuser/update', [ProfileController::class, 'updateprivatuserdata']); // Remove a trip from the saved trips list

    // API resource routes for bookings (CRUD operations)
    Route::apiResource('booking', BookingController::class); // Handles CRUD for bookings
    Route::get('trip/bokking/{id}', [BookingController::class, 'showbookingsbytrip'])->name('booking.show'); // Retrieve bookings for a specific trip
    Route::post('/booking/{booking}/accept', [BookingController::class, 'accept'])->name('booking.accept'); // Accept a booking
    Route::post('/booking/{booking}/reject', [BookingController::class, 'reject'])->name('booking.reject'); // Reject a booking
    Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel'); // Cancel a booking

    // API resource routes for ratings
    Route::apiResource("rating", RatingController::class); // Handles CRUD operations for ratings

    Route::apiResource("request", RequestController::class);

});
