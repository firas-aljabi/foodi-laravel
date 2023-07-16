<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\admin;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// branch_Apis
Route::get('/branches', [BranchController::class, 'index']);
Route::get('/show_branch/{id}', [BranchController::class, 'show']);
Route::post('/store_branch', [BranchController::class, 'store']);
Route::put('/update_branch/{id}', [BranchController::class, 'update']);
Route::post('/delete_branch/{id}', [BranchController::class, 'destroy']);

//Category_Apis
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/show_category/{id}', [CategoryController::class, 'show']);
Route::post('/store_category', [CategoryController::class, 'store']);
Route::put('/update_category/{id}', [CategoryController::class, 'update']);
Route::post('/delete_category/{id}', [CategoryController::class, 'destroy']);

// product_Apis
Route::get('/products', [ProductController::class, 'index']);
Route::get('/show_product/{id}', [ProductController::class, 'show']);
Route::post('/store_product', [ProductController::class, 'store']);
Route::put('/update_product/{id}', [ProductController::class, 'update']);
Route::post('/delete_product/{id}', [ProductController::class, 'destroy']);
Route::get('/mostRequestedProduct',[ProductController::class,'mostRequestedProduct']);//منتجات اكثر طلبا
Route::get('/leastRequestedProduct',[ProductController::class,'leastRequestedProduct']);//منتجات اقل طلبا

//Rating_Apis
Route::get('/avgRating/{id}', [RatingController::class, 'avgRating']);//معدل تقييم
Route::get('/ratings', [RatingController::class, 'index']);
Route::get('/show_rating/{id}', [RatingController::class, 'show']);
Route::post('/store_rating', [RatingController::class, 'store']);
Route::post('/delete_rating/{id}', [RatingController::class, 'destroy']);

//Ingredient_Apis
Route::get('/ingredients', [IngredientController::class, 'index']);
Route::get('/show_ingredient/{id}', [IngredientController::class, 'show']);
Route::post('/store_ingredient', [IngredientController::class, 'store']);
Route::put('/update_ingredient/{id}', [IngredientController::class, 'update']);
Route::post('/delete_ingredient/{id}', [IngredientController::class, 'destroy']);


//Order_Apis
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/show_order/{id}', [OrderController::class, 'show']);
Route::post('/store_order', [OrderController::class, 'store']);
Route::put('/update_order/{id}', [OrderController::class, 'update']);
Route::post('/delete_order/{id}', [OrderController::class, 'destroy']);
Route::post('/report_order',[OrderController::class, 'report_order']);//تقرير بالطلبات بين تاريخين
Route::get('ready_order/{id}',[OrderController::class,'readyOrder']);//قدي اخد الاوردر وقت
Route::get('peakTimes',[OrderController::class,'peakTimes']);//اوقات الذروة
Route::post('orders/export/', [OrderController::class, 'export']); //تصدير لملف اكسل

//Offer_Apis
Route::get('/offers', [OfferController::class, 'index']);
Route::get('/show_offer/{id}', [OfferController::class, 'show']);
Route::post('/store_offer', [OfferController::class, 'store']);
Route::put('/update_offer/{id}', [OfferController::class, 'update']);
Route::post('/delete_offer/{id}', [OfferController::class, 'destroy']);

// feedback_Apis
Route::get('/feedbacks', [FeedbackController::class, 'index']);
Route::get('/show_feedback/{id}', [FeedbackController::class, 'show']);
Route::post('/store_feedback', [FeedbackController::class, 'store']);
Route::put('/update_feedback/{id}', [FeedbackController::class, 'update']);
Route::post('/delete_feedback/{id}', [FeedbackController::class, 'destroy']);

// user_Apis


Route::group(['middleware' => 'api'], function () {
    Route::post('logout', [UserController::class, 'logout']);  
    Route::post('login', [UserController::class, 'login']);
    // Route::post('register', [UserController::class, 'register']); 
});
Route::group(['middleware' =>  ['auth:api', 'admin']], function () {
    Route::get('users', [UserController::class, 'index']);
    Route::post('add_user', [UserController::class, 'store']);
    Route::get('show_user/{id}', [UserController::class, 'show']);
    Route::put('update_user/{id}', [UserController::class, 'update']);
    Route::post('delete_user/{id}', [UserController::class, 'destroy']);   
});

//forget & reset password
// Route::post('forgotPassword',[NewPasswordController::class,'forgotPassword']);
// Route::post('resetpassword',[NewPasswordController::class,'passwordReset']);
// Route::get('/reset-password/{token}', function (string $token) {
//     return $token;
// });
