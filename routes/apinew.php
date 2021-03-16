<?php

use Illuminate\Http\Request;

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
Auth::routes();
Route::post('GenerateAccesstoken','API\V2\ApiController@GenerateAccesstoken');
Route::post('LoginUser','API\V2\ApiController@LoginUser');
Route::post('RegisterUser','API\V2\ApiController@RegisterUser');
Route::post('ForgotPassword','API\V2\ApiController@ForgotPassword');
Route::post('SearchUsers','API\V2\ApiController@SearchUsers');
Route::post('GetUsers','API\V2\UserApiController@GetUsers');
//Products Routes
Route::post('GetRecommendedProducts','API\V2\ProductController@GetRecommendedProducts');
Route::post('SearchFoodItems','API\V2\ProductController@SearchFoodItems');
Route::post('GetProductDetail','API\V2\ProductController@GetProductDetail');
Route::post('GetFilterProducts','API\V2\ProductController@GetFilterProducts');
//Product Pally Routes
Route::post('GetOpenPallyProducts','API\V2\PallyProductController@GetOpenPallyProducts');

Route::post('GetPallyDetail','API\V2\PallyProductController@GetPallyDetail');
//Category Routes
Route::post('/GetCategories', 'API\V2\CategoryController@GetCategories');
Route::post('/GetSubCategories', 'API\V2\CategoryController@GetSubCategories');

Route::get('updateRefferal','API\V2\ApiController@updateRefferal');

Route::get('updateProductMataInfo','API\V2\ApiController@updateProductMataInfo');

Route::middleware('auth:api')->group(function() {
    Route::post('GetProfile','API\V2\ApiController@GetProfile');
    Route::post('EditProfile','API\V2\ApiController@EditProfile');
    Route::post('ChangePassword','API\V2\ApiController@ChangePassword');
    Route::post('GetDeviceToken','API\V2\ApiController@GetDeviceToken');
    Route::get('GetYouTubeKey','API\V2\ApiController@GetYouTubeKey');
    Route::post('GetHomeData','API\V2\ApiController@GetHomeData');
    Route::post('Logout','API\V2\ApiController@Logout');
    //Products Routes
    Route::post('GetProductAttributes','API\V2\ProductController@GetProductAttributes');
    Route::post('GetFavouriteProducts','API\V2\ProductController@GetFavouriteProducts');
    Route::post('AddFavouriteProducts','API\V2\ProductController@AddFavouriteProducts');
    //Product Review Routes
    Route::post('AddProductReview','API\V2\ProductReviewController@AddProductReview');
    Route::post('GetProductReviews','API\V2\ProductReviewController@GetProductReviews');
    //Product Pally Routes
    Route::post('GetClosePallyProducts','API\V2\PallyProductController@GetClosePallyProducts');
    Route::post('CreateOpenPally','API\V2\PallyProductController@CreateOpenPally');
    Route::post('CreateClosePally','API\V2\PallyProductController@CreateClosePally');
    Route::post('CheckExistingPally','API\V2\PallyProductController@CheckExistingPally');
    Route::post('GetPallyPeople','API\V2\PallyProductController@GetPallyPeople');
    //User Address Routes
    Route::get('GetAreas','API\V2\AddresController@GetAreas');
    Route::get('GetUserAddress','API\V2\AddresController@GetUserAddress');
    Route::post('AddUserAddress','API\V2\AddresController@AddUserAddress');
    Route::post('EditUserAddress','API\V2\AddresController@EditUserAddress');
    Route::post('GetShippingCost','API\V2\AddresController@GetShippingCost');
    //User Followers Routes
    Route::post('GetFollowers','API\V2\UserApiController@GetFollowers');
    Route::post('GetUserFollowers','API\V2\UserApiController@GetUserFollowers');
    Route::post('GetFollowing','API\V2\UserApiController@GetFollowing');
    Route::post('GetUserFollowing','API\V2\UserApiController@GetUserFollowing');
    Route::post('FollowUser','API\V2\UserApiController@FollowUser');
    Route::get('GetBankInfo','API\V2\UserApiController@GetBankInfo');
    //Cart Routes
    Route::post('AddProductToCart','API\V2\CartController@AddProductToCart');
    Route::post('UpdateCartProduct','API\V2\CartController@UpdateCartProduct');
    Route::get('GetCartItems','API\V2\CartController@GetCartItems');
    Route::get('ValidateCart','API\V2\CartController@ValidateCart');
    Route::post('DeleteCartProduct','API\V2\CartController@DeleteCartProduct');
    Route::post('ValidationCoupon','API\V2\CartController@ValidationCoupon');
    Route::post('ValidateWalletMoney','API\V2\CartController@ValidateWalletMoney');
    Route::get('GetWalletAmount','API\V2\CartController@GetWalletAmount');
    //Orders Routes
    Route::post('MakeOrder','API\V2\OrderController@MakeOrder');
    Route::post('GetOrders','API\V2\OrderController@GetOrders');
    Route::post('OrderDetail','API\V2\OrderController@OrderDetail');
    //Notification Routes
    Route::get('GetNotifications','API\V2\NotificationController@GetNotifications');
    Route::get('ReadNotification','API\V2\NotificationController@ReadNotification');
});
