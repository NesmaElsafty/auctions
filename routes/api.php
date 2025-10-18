<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SubCategoryInputController;
use App\Http\Controllers\SelectableDataController;

Route::match(['GET', 'POST'], '/ping', function () {
    return ['ok' => true];
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/contactUs', [ContactUsController::class, 'index']);
Route::get('/terms', [TermController::class, 'index']);
Route::get('/terms/{id}', [TermController::class, 'show']);

// categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// sub categories
Route::get('/subCategories', [SubCategoryController::class, 'index']);
Route::get('/subCategories/{id}', [SubCategoryController::class, 'show']);
Route::get('/subCategoriesByCategoryId', [SubCategoryController::class, 'getSubCategoriesByCategoryId']);

// sub category inputs
Route::get('/subCategoryInputs', [SubCategoryInputController::class, 'index']);
Route::get('/subCategoryInputs/{id}', [SubCategoryInputController::class, 'show']);
Route::get('/subCategoryInputsBySubCategoryId', [SubCategoryInputController::class, 'getSubCategoryInputsBySubCategoryId']);


// selectable data
Route::get('/selectableData', [SelectableDataController::class, 'index']);
Route::get('/selectableData/{id}', [SelectableDataController::class, 'show']);
Route::get('/inputOptions', [SelectableDataController::class, 'getInputOptions']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/uploadAvatar', [AuthController::class, 'uploadAvatar']);
    Route::delete('/deleteAvatar', [AuthController::class, 'deleteAvatar']);
    Route::post('/changePassword', [AuthController::class, 'changePassword']);

    Route::apiResource('agencies', AgencyController::class);

    Route::get('/bankAccount', [BankAccountController::class, 'index']);
    Route::post('/bankAccount', [BankAccountController::class, 'store']);
    Route::delete('/bankAccount', [BankAccountController::class, 'destroy']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/usersBulkActions', [UserController::class, 'bulkActions']);
    Route::get('/blockList', [UserController::class, 'blockList']);

    Route::put('/contactUs', [ContactUsController::class, 'update']);

    // terms
    Route::post('/terms', [TermController::class, 'store']);
    Route::put('/terms/{id}', [TermController::class, 'update']);
    Route::delete('/terms/{id}', [TermController::class, 'destroy']);
    Route::post('/termBulkActions', [TermController::class, 'bulkActions']);

    // notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::put('/notifications/{id}', [NotificationController::class, 'update']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    // Route::post('/notificationsBulkActions', [NotificationController::class, 'bulkActions']);

    // alerts
    Route::get('/alerts', [AlertController::class, 'index']);
    Route::get('/alerts/{id}', [AlertController::class, 'show']);
    Route::post('/readToggle/{id}', [AlertController::class, 'readToggle']);
    Route::delete('/alerts/{id}', [AlertController::class, 'destroy']);

    // categories

    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    // Route::post('/categoriesBulkActions', [CategoryController::class, 'bulkActions']);

    // sub categories
    Route::post('/subCategories', [SubCategoryController::class, 'store']);
    Route::put('/subCategories/{id}', [SubCategoryController::class, 'update']);
    Route::delete('/subCategories/{id}', [SubCategoryController::class, 'destroy']);

    // sub category inputs
    Route::post('/subCategoryInputs', [SubCategoryInputController::class, 'store']);
    Route::put('/subCategoryInputs/{id}', [SubCategoryInputController::class, 'update']);
    Route::delete('/subCategoryInputs/{id}', [SubCategoryInputController::class, 'destroy']);

    // selectable data
    Route::post('/selectableData', [SelectableDataController::class, 'store']);
    Route::put('/selectableData/{id}', [SelectableDataController::class, 'update']);
    Route::delete('/selectableData/{id}', [SelectableDataController::class, 'destroy']);
});