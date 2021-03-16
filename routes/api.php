<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
//Category Routes
Route::post('/ProductCategory', 'API\V2\CategoryController@ProductCategory');
Route::post('/GetSubCategories', 'API\V2\CategoryController@GetSubCategories');
//Serices Routes
Route::post('/ServiceCategory', 'API\V2\ServiceController@ServiceCategory');
Route::get('/GetTopServices', 'API\V2\ServiceController@GetTopServices');
Route::post('/ServiceList', 'API\V2\ServiceController@ServiceList');
//Products Routes
Route::post('NewArrivalProducts','API\V2\ProductController@NewArrivalProducts');
Route::post('SearchProducts','API\V2\ProductController@SearchProducts');
Route::post('ProductsDetail','API\V2\ProductController@ProductsDetail');
Route::post('FilterProducts','API\V2\ProductController@FilterProducts');

Route::middleware('auth:api')->group(function() {
    Route::post('GetProfile','API\V2\ApiController@GetProfile');
    Route::post('EditProfile','API\V2\ApiController@EditProfile');
    Route::post('ChangePassword','API\V2\ApiController@ChangePassword');
    Route::post('GetDeviceToken','API\V2\ApiController@GetDeviceToken');
    Route::get('GetYouTubeKey','API\V2\ApiController@GetYouTubeKey');
    Route::post('GetHomeData','API\V2\ApiController@GetHomeData');
    Route::post('Logout','API\V2\ApiController@Logout');
    //Products Routes
    Route::post('GetFavouriteProducts','API\V2\ProductController@GetFavouriteProducts');
    Route::post('AddFavouriteProducts','API\V2\ProductController@AddFavouriteProducts');
    //Product Review Routes
    Route::post('AddProductReview','API\V2\ProductReviewController@AddProductReview');
    Route::post('GetProductReviews','API\V2\ProductReviewController@GetProductReviews');
    //User Address Routes
    Route::get('GetAreas','API\V2\AddresController@GetAreas');
    Route::get('InsideLagosAreas','API\V2\AddresController@InsideLagosAreas');
    Route::get('OutsideLagosAreas','API\V2\AddresController@OutsideLagosAreas');
    Route::get('OutsideNigeriaAreas','API\V2\AddresController@OutsideNigeriaAreas');
    Route::get('GetUserAddress','API\V2\AddresController@GetUserAddress');
    Route::post('AddUserAddress','API\V2\AddresController@AddUserAddress');
    Route::post('EditUserAddress','API\V2\AddresController@EditUserAddress');
    Route::post('GetShippingCost','API\V2\AddresController@GetShippingCost');
    //Cart Routes
    Route::post('AddProductToCart','API\V2\CartController@AddProductToCart');
    Route::post('AddServiceToCart','API\V2\CartController@AddServiceToCart');
    Route::post('UpdateCartItem','API\V2\CartController@UpdateCartItem');
    Route::get('GetCartItems','API\V2\CartController@GetCartItems');
    Route::get('ValidateCart','API\V2\CartController@ValidateCart');
    Route::post('DeleteCartItem','API\V2\CartController@DeleteCartItem');
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
    
    //Media Routes
    Route::get('GetMediaServices','API\V2\MediaController@GetMediaServices');
    Route::post('GetMediaImages','API\V2\MediaController@GetMediaImages');
});
