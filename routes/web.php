<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','Front\HomeControllor@home');
//Auth Routes
Route::match(['post', 'get'],'/login','Front\LoginControllor@showLoginForm');
Route::get('/signup','Front\LoginControllor@signinForm');
Route::post('/checkEmail','Front\LoginControllor@checkEmail');
Route::get('/logout','Front\LoginControllor@logout');
Route::post('/signup/newsletter','Front\LoginControllor@newsletter');
//Products Routs
Route::match(['post','get'], '/cources/all','Front\CourceControllor@index');
Route::match(['post','get'], '/course/category/{slug}','Front\CourceControllor@categoryCourse');
Route::get('/course/detail/{slug}','Front\CourceControllor@detail');

//Cart routs
Route::post('/item/session/addtocart','Front\CartController@addToCartSession');
Route::get('/item/session/cart/deleteItem/{id}','Front\CartController@deleteCartItemSession');
Route::get('/item/cart/updateItem/{id}/{qty}','Front\CartController@updateItem');

//Pages Routes
Route::get('/membership','Front\PageControllor@membership');
Route::get('/programme','Front\PageControllor@programme');
Route::get('/history','Front\PageControllor@history');
Route::get('/mvstatements','Front\PageControllor@mvstatements');
Route::get('/organization','Front\PageControllor@organization');

Route::get('/checkout','Front\CheckoutControllor@index');


//Admin Auth Routes
Route::match(['post', 'get'],'/admin','Admin\LoginControllor@showLoginForm');
Route::get('/admin/logout','Admin\LoginControllor@logout');
Route::match(['post', 'get'],'/admin/forget/password','Admin\LoginControllor@forgetPassword');
Route::match(['post', 'get'],'/user/reset_password/{id}', 'Admin\LoginControllor@resetUserPassword');
Route::match(['post', 'get'],'/admin/reset_password/{id}', 'Admin\LoginControllor@resetPassword');
//Upload Product Image Routes
Route::post('product/files/upload','Admin\ProductControllor@uploadFile');
Route::post('product/delete_img/{name}','Admin\ProductControllor@deleteFile');
Route::get('/admin/upload','Front\HomeControllor@uploadCSV');

Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin/dashboard','Admin\DashboardControllor@dashboard');
    Route::match(['post', 'get'],'profile','Admin\DashboardControllor@profile');
    Route::match(['post', 'get'],'admin/settings','Admin\DashboardControllor@settings');
    
    Route::GET('/admin/users', 'Admin\UserControllor@view')->name('users');
    Route::GET('users-list', 'Admin\UserControllor@listView')->name('users-list');
    Route::get('/admin/users1','Admin\UserControllor@index');
    Route::get('/admin/users/{status}/{id}','Admin\UserControllor@updateStatus');
    Route::match(['post', 'get'],'/admin/user/edit/{id}','Admin\UserControllor@edit');
    Route::match(['post', 'get'],'/admin/send/notifications','Admin\UserControllor@sendPushNotification');
    
    Route::get('/admin/countries','Admin\CountryControllor@index');
    Route::match(['post', 'get'], '/admin/country/add','Admin\CountryControllor@create');
    Route::get('/admin/country/delete/{id}','Admin\CountryControllor@delete');
    Route::match(['post', 'get'],'/admin/country/edit/{id}','Admin\CountryControllor@edit');
    
    Route::get('/admin/cities','Admin\CityControllor@index');
    Route::match(['post', 'get'], '/admin/city/add','Admin\CityControllor@create');
    Route::get('/admin/city/delete/{id}','Admin\CityControllor@delete');
    Route::match(['post', 'get'],'/admin/city/edit/{id}','Admin\CityControllor@edit');
    
    Route::get('/admin/categories/{id}','Admin\CategoryControllor@index');
    Route::match(['post', 'get'], '/admin/category/add/{id}','Admin\CategoryControllor@create');
    Route::get('/admin/category/delete/{id}','Admin\CategoryControllor@delete');
    Route::match(['post', 'get'],'/admin/category/edit/{p_id}/{id}','Admin\CategoryControllor@edit');
    Route::get('/admin/sub/categories/{id}','Admin\CategoryControllor@getCategories');
    Route::get('/admin/category/{status}/{cat_id}/{id}','Admin\CategoryControllor@updateStatus');
    
    Route::get('/admin/advertisement','Admin\AddsController@index');
    Route::match(['post', 'get'], '/admin/advertisement/add','Admin\AddsController@create');
    Route::get('/admin/advertisement/delete/{id}','Admin\AddsController@delete');
    Route::match(['post', 'get'],'/admin/advertisement/edit/{id}','Admin\AddsController@edit');
    
    Route::GET('/admin/courses', 'Admin\CourseControllor@view')->name('courses');
    Route::GET('courses-list', 'Admin\CourseControllor@listView')->name('courses-list');
    Route::match(['post', 'get'], '/admin/course/add','Admin\CourseControllor@create');
    Route::POST('/admin/product/delete','Admin\CourseControllor@delete');
    Route::match(['post', 'get'],'/admin/course/edit/{id}','Admin\CourseControllor@edit');
    Route::get('/admin/product/deleteimage/{product_id}/{image_id}','Admin\CourseControllor@deleteImage');
    Route::get('/admin/product/stock/{status}/{id}','Admin\CourseControllor@updateStatus');
    Route::get('/admin/product/season/{status}/{id}','Admin\CourseControllor@seasonStatus');
    Route::get('/admin/product/recommended/remove/{id}','Admin\CourseControllor@remove');
    
    Route::GET('/products-attributes/{id}', 'Admin\AttributeControllor@view')->name('products-attributes');
    Route::GET('products-attributes-list/{id}', 'Admin\AttributeControllor@listView')->name('products-attributes-list');
    Route::match(['post', 'get'],'/save-product-attribute','Admin\AttributeControllor@save');
    Route::POST('/delete-product-attribute','Admin\AttributeControllor@delete');
    
    Route::get('admin/orders','Admin\OrdersControllor@view');
    Route::GET('orders-list', 'Admin\OrdersControllor@listView')->name('normal-orders-list');
    Route::get('admin/pally/links/{id}','Admin\OrdersControllor@indexLinks');
    Route::match(['post', 'get'],'admin/order/detail/{id}','Admin\OrdersControllor@orderDetail');
    Route::match(['post', 'get'],'admin/order/update/status/{id}','Admin\OrdersControllor@edit');
    Route::get('/admin/pallied/friends/{pally_id}/{order_id}','Admin\OrdersControllor@palliedFriends');
    Route::post('save-transaction','Admin\OrdersControllor@refundAmount');
    Route::post('admin/order/delete','Admin\OrdersControllor@delete');
    
    Route::POST('update-status','Admin\OrdersControllor@updateStatus');
    
    /*Couopon Code*/
    Route::get('/admin/coupon/codes','Admin\CopounControllor@index');
    Route::match(['post', 'get'], '/admin/coupon/code/add','Admin\CopounControllor@create');
    Route::get('/admin/coupon/code/delete/{id}','Admin\CopounControllor@delete');
    Route::match(['post', 'get'],'/admin/coupon/code/edit/{id}','Admin\CopounControllor@edit');
    /* Awards */
    Route::get('/admin/awards','Admin\AwardsController@index');
    Route::match(['post', 'get'], '/admin/awards/add','Admin\AwardsController@create');
    Route::get('/admin/awards/delete/{id}','Admin\AwardsController@delete');
    Route::match(['post', 'get'],'/admin/awards/edit/{id}','Admin\AwardsController@edit');
    /*Inside Areas */
    Route::GET('/admin/areas/Inside', 'Admin\AreaInsideControllor@view')->name('address-areas-Inside');
    Route::GET('areas-list/Inside', 'Admin\AreaInsideControllor@listView')->name('areas-list-Inside');
    Route::match(['post', 'get'],'/save-area-Inside','Admin\AreaInsideControllor@save');
    Route::GET('/admin/area/{status}/{id}','Admin\AreaInsideControllor@updateStatus');
    
    /*Zones */
    Route::GET('/admin/zones/Outside', 'Admin\AreaOutsideControllor@view')->name('address-zones-Outside');
    Route::GET('zones-list/Outside', 'Admin\AreaOutsideControllor@listView')->name('zones-list-Outside');
    Route::match(['post', 'get'],'/save-zones-Inside','Admin\AreaOutsideControllor@save');
    Route::GET('/admin/zone/{status}/{id}','Admin\AreaOutsideControllor@updateStatus');
    /*Zone Areas */
    Route::GET('/admin/zones/areas/{id}', 'Admin\AreaZoneControllor@view')->name('address-zones-areas');
    Route::GET('zones-areas-list/{id}', 'Admin\AreaZoneControllor@listView')->name('zones-list-Outside');
    Route::match(['post', 'get'],'/save-zones-area','Admin\AreaZoneControllor@save');
    Route::GET('/admin/areas/zone/{status}/{id}','Admin\AreaZoneControllor@updateStatus');
    /*Zone Prices */
    Route::GET('/admin/zones/prices/{id}', 'Admin\PriceZoneControllor@view')->name('address-zones-price');
    Route::GET('zones-prices-list/{id}', 'Admin\PriceZoneControllor@listView')->name('zones-price-Outside');
    Route::match(['post', 'get'],'/save-zones-price','Admin\PriceZoneControllor@save');
    Route::GET('/admin/price/zones/{status}/{id}','Admin\PriceZoneControllor@updateStatus');
    /*Zones Nigeria */
    Route::GET('/admin/zones/nigeria', 'Admin\AreaNigeriaZoneControllor@view')->name('address-zones-ngeria');
    Route::GET('zones-list/nigeria', 'Admin\AreaNigeriaZoneControllor@listView')->name('zones-list-nigeria');
    Route::match(['post', 'get'],'/save-zones-nigeria','Admin\AreaNigeriaZoneControllor@save');
    Route::GET('/admin/ng/zones/{status}/{id}','Admin\AreaNigeriaZoneControllor@updateStatus');
    /*Zone NG Prices */
    Route::GET('/admin/zone/ng/prices/{id}', 'Admin\PriceNgZoneControllor@view')->name('address-zones-ng-price');
    Route::GET('zones-ng-prices-list/{id}', 'Admin\PriceNgZoneControllor@listView')->name('zones-ng-price-Outside');
    Route::match(['post', 'get'],'/save-zones-ng-price','Admin\PriceNgZoneControllor@save');
    Route::GET('/admin/price/ng/zones/{status}/{id}','Admin\PriceNgZoneControllor@updateStatus');
    /*Zones Countries */
    Route::GET('/admin/areas/countries', 'Admin\AreaCountryControllor@view')->name('address-zones-country');
    Route::GET('zones-countries-list', 'Admin\AreaCountryControllor@listView')->name('zones-countries-list');
    Route::match(['post', 'get'],'/save-zones-country','Admin\AreaCountryControllor@save');
    Route::GET('/admin/country/zone/{status}/{id}','Admin\AreaCountryControllor@updateStatus');
    
    Route::match(['post', 'get'],'/admin/newslatter/list','Admin\DashboardControllor@newsLetter');
    
    Route::get('admin/abundant/cart','Admin\CartControllor@view');
    Route::GET('cart-list', 'Admin\CartControllor@listView')->name('cart-list');
    Route::GET('user/cart/items/{id}','Admin\CartControllor@cartDetail');
    
    Route::get('admin/contacts/list','Admin\DashboardControllor@view')->name('contacts');
    Route::GET('contacts-list', 'Admin\DashboardControllor@listView')->name('contacts-list');
    /* Teams */
    Route::get('/admin/teams','Admin\TeamControllor@index');
    Route::match(['post', 'get'], '/admin/team/add','Admin\TeamControllor@create');
    Route::get('/admin/team/delete/{id}','Admin\TeamControllor@delete');
    Route::match(['post', 'get'],'/admin/team/edit/{id}','Admin\TeamControllor@edit');
    /* Testimonial */
    Route::get('/admin/testimonials','Admin\TestimonialControllor@index');
    Route::match(['post', 'get'], '/admin/testimonial/add','Admin\TestimonialControllor@create');
    Route::get('/admin/testimonial/delete/{id}','Admin\TestimonialControllor@delete');
    Route::match(['post', 'get'],'/admin/testimonial/edit/{id}','Admin\TestimonialControllor@edit');
});
