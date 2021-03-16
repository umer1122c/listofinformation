<?php

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
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
});
Route::get('/','Front\HomeControllor@home');
Route::get('/checkNotification','Front\HomeControllor@checkNotification');
//Products Routs
Route::match(['post','get'], '/products/all','Front\ProductControllor@index');
Route::match(['post','get'], '/all/products/search','Front\ProductController@productSearch');
Route::match(['post','get'], '/products/search/keyword','Front\ProductController@productSearchKeyword');
Route::match(['post','get'], '/products/category/{slug}','Front\ProductControllor@categoryProducts');
Route::match(['post','get'],'/products/subcategory/{categorySlug}/{subCategorySlug}','Front\ProductControllor@subCategoryProducts');
Route::post('/get/product/attribute','Front\ProductControllor@getAttribute');
Route::get('/product/detail/{slug}','Front\ProductControllor@detail');
Route::get('/get-products/{keyword}','Front\ProductControllor@getProducts');
Route::get('/get-products-mobile/{keyword}','Front\ProductControllor@getProductsMobile');
Route::POST('/get/product/subcategory','Front\ProductControllor@getProductsSubcategory');
//Pally Routes
Route::get('/pally/product/{type}','Front\PallyProductControllor@index');
Route::get('/existing/open/pally/product/{id}','Front\PallyProductControllor@existingPally');
Route::post('/create/pally/product','Front\PallyProductControllor@createPally');
Route::get('/pally/product/detail/{slug}/{p_id}','Front\PallyProductControllor@detail');
Route::get('/get-pallys/{p_id}','Front\PallyProductControllor@getExpallys');
Route::get('/check-followers/{size}','Front\PallyProductControllor@checkFollowers');
//Cart Routes
Route::match(['post', 'get'],'/my/cart','Front\CartController@index');
Route::post('/item/addtocart','Front\CartController@addToCart');
Route::get('/item/cart/deleteItem/{id}','Front\CartController@deleteCartItem');
Route::get('/item/cart/updateItem/{id}/{qty}','Front\CartController@updateItem');
//checkout Routes
Route::get('/checkout','Front\CheckoutControllor@index');
//Product Reviews
Route::get('/products/reviews/{id}','Front\ReviewControllor@index');
Route::get('/wright/review/{id}','Front\ReviewControllor@writeReview');
//Profile Routes
Route::get('/user/profile/{id}','Front\ProfileControllor@index');
//Pages Routes
Route::get('/about','Front\PageControllor@about');
Route::get('/faq','Front\PageControllor@faq');
Route::get('/terms','Front\PageControllor@terms');
Route::get('/return/policy','Front\PageControllor@return_policy');
Route::get('/privacy/policy','Front\PageControllor@privacy_policy');
Route::get('/covid/policy','Front\PageControllor@covid_policy');
Route::match(['post', 'get'],'/contactus','Front\PageControllor@contactus');
//Payment Routes
//Route::get('/pay', 'Front\PaymentController@payment');
Route::get('/payment/callback', 'Front\PaymentController@handleGatewayCallback');
Route::get('/payment/success','Front\PaymentController@successPayment');
Route::get('/payment/failed','Front\PaymentController@failerPayment');
//Auth Routes
Route::get ( '/redirect/{service}/{type}/{id}', 'Front\SocialAuthController@redirect' );
Route::get ( '/callback/{service}', 'Front\SocialAuthController@callback');
Route::match(['post', 'get'],'/login','Front\LoginControllor@showLoginForm');
Route::match(['post', 'get'],'/formers/signup','Front\LoginControllor@showFormerForm');
Route::get('/login/{type}','Front\LoginControllor@LoginForm');
Route::get('/signup','Front\LoginControllor@signinForm');
Route::post('/checkEmail','Front\LoginControllor@checkEmail');
Route::get('/logout','Front\LoginControllor@logout');
Route::post('/signup/newsletter','Front\LoginControllor@newsletter');
Route::match(['post', 'get'],'/forgetPassword','Front\LoginControllor@forgetPassword');

//Old Routes
//Route::match(['post', 'get'],'/shop/{id}','Front\ProductControllor@index');
//Route::match(['post', 'get'],'/shop/{id}/{sub_cat_id}','Front\ProductControllor@index');
//Route::get('/my/favorites','Front\ProductControllor@favorites');
//Route::get('/shop/detail/{slug}/{id}','Front\ProductControllor@detail');



Route::post('/pally/product','Front\ProductControllor@pally');
Route::get('/open/pally/product','Front\ProductControllor@pally_products');
//Route::get('/pay', 'PaymentController@handleGatewayCallback');

Route::get('/import-orders','Front\OrderControllor@importOrders');

Route::group(['middleware' => 'user'], function () {
    
    Route::match(['post', 'get'],'/write/review/{id}','Front\ReviewControllor@writeReview');
    
    //Faviorty Routes
    Route::get('/my/favorites','Front\ProductWishlistControllor@index');
    Route::get('/product/faviorty/{status}/{id}','Front\ProductWishlistControllor@productFaviorty');
    //Close Pally Followers
    Route::get('/select/followers','Front\PallyProductControllor@selectFollowers');
    
    Route::match(['post', 'get'],'/my/account','Front\DashboardController@index');
    Route::match(['post', 'get'],'/update/profile','Front\DashboardController@updateProfile');
    Route::match(['post', 'get'],'/followers','Front\DashboardController@followers');
    Route::match(['post', 'get'],'/following','Front\DashboardController@following');
    
    Route::match(['post', 'get'],'/invite/peaple','Front\DashboardController@invitePeaple');
    Route::get('/user/wallet','Front\DashboardController@wallet');
    //Address Routes
    Route::match(['post', 'get'],'/user/address','Front\DashboardController@address');
    Route::match(['post', 'get'],'/save/address/new','Front\DashboardController@saveAddress');
    Route::post('/delete-address','Front\DashboardController@deleteAddress');
    
    Route::match(['post', 'get'],'/find/friends','Front\FriendControllor@index');
    
    Route::match(['post', 'get'],'/find/followers','Front\PallyProductControllor@selectFollowers');
    
    Route::get('/my/profile','Front\ProfileControllor@myProfile');
    
    Route::post('/update/phone','Front\ProfileControllor@updatePhone');
    
    Route::match(['post', 'get'],'/save/address','Front\ProductControllor@saveAddress');
    
    
    
    Route::post('/pay', 'Front\PaymentController@payment')->name('pay'); 
    //Route::post('/pay', 'MonnifyPaymentController@redirectToGateway');
    
    //Route::post('/check/cart', 'MonnifyPaymentController@checkCart');
    
    Route::get('/my/followers','Front\ProfileControllor@followers');
    Route::get('/user/follow/{status}/{id}','Front\FriendControllor@userFallow');
    Route::get('/user/follow/{status}/{id}/{c_user_id}','Front\ProfileControllor@userFallow1');
    Route::get('/current/user/follow/{status}/{id}/{c_user_id}','Front\ProfileControllor@userCurrentFallow1');
    
    Route::post('/open/pally/product','Front\ProductControllor@pally_products');
    Route::get('/close/pally/products','Front\ProductControllor@close_products');
    Route::post('/close/pally/product','Front\ProductControllor@close_pally_products');
    Route::get('/close/pally/product/{slug}/{id}','Front\ProductControllor@get_close_pally_products');
    
    
    Route::post('/send/pally/request','Front\ProductControllor@send_pally_request');
    
    Route::get('/my/orders','Front\OrderControllor@index');
    Route::get('/order/detail/{id}','Front\OrderControllor@orderDetail');
    Route::get('/pallied/friends/{pally_id}/{order_id}','Front\OrderControllor@palliedFriends');
    Route::get('/check/copoun/{code}/{amount}','Front\OrderControllor@applyCouponCode');
});
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
    Route::match(['post', 'get'],'admin/settings','Admin\DashboardControllor@settings');
    Route::match(['post', 'get'],'admin/bank/info','Admin\DashboardControllor@bankInfo');
    
    Route::GET('/admin/users', 'Admin\UserControllor@view')->name('users');
    Route::GET('users-list', 'Admin\UserControllor@listView')->name('users-list');
    Route::get('/admin/users1','Admin\UserControllor@index');
    Route::get('/admin/users/{status}/{id}','Admin\UserControllor@updateStatus');
    Route::match(['post', 'get'],'/admin/user/edit/{id}','Admin\UserControllor@edit');
    Route::match(['post', 'get'],'/admin/send/notifications','Admin\UserControllor@sendPushNotification');
    
    Route::get('/admin/slider/images','Admin\SliderControllor@index');
    Route::match(['post', 'get'], '/admin/slider/image/add','Admin\SliderControllor@create');
    Route::get('/admin/slider/image/delete/{id}','Admin\SliderControllor@delete');
    Route::match(['post', 'get'],'/admin/slider/image/edit/{id}','Admin\SliderControllor@edit');
    
    Route::get('/admin/categories/{id}','Admin\CategoryControllor@index');
    Route::match(['post', 'get'], '/admin/category/add/{id}','Admin\CategoryControllor@create');
    Route::get('/admin/category/delete/{id}','Admin\CategoryControllor@delete');
    Route::match(['post', 'get'],'/admin/category/edit/{p_id}/{id}','Admin\CategoryControllor@edit');
    Route::get('/admin/sub/categories/{id}','Admin\CategoryControllor@getCategories');
    Route::get('/admin/category/{status}/{cat_id}/{id}','Admin\CategoryControllor@updateStatus');
    
    Route::GET('/admin/products', 'Admin\ProductControllor@view')->name('products');
    Route::GET('products-list', 'Admin\ProductControllor@listView')->name('products-list');
    Route::get('/admin/products1','Admin\ProductControllor@index');
    Route::get('/admin/products/recommended','Admin\ProductControllor@indexRecommended');
    Route::match(['post', 'get'], '/admin/product/add','Admin\ProductControllor@create');
    Route::POST('/admin/product/delete','Admin\ProductControllor@delete');
    Route::match(['post', 'get'],'/admin/product/edit/{id}','Admin\ProductControllor@edit');
    Route::get('/admin/product/deleteimage/{product_id}/{image_id}','Admin\ProductControllor@deleteImage');
    Route::get('/admin/product/stock/{status}/{id}','Admin\ProductControllor@updateStatus');
    Route::get('/admin/product/season/{status}/{id}','Admin\ProductControllor@seasonStatus');
    Route::get('/admin/product/recommended/remove/{id}','Admin\ProductControllor@remove');
    
    Route::GET('/products-attributes/{id}', 'Admin\AttributeControllor@view')->name('products-attributes');
    Route::GET('products-attributes-list/{id}', 'Admin\AttributeControllor@listView')->name('products-attributes-list');
    Route::match(['post', 'get'],'/save-product-attribute','Admin\AttributeControllor@save');
    Route::POST('/delete-product-attribute','Admin\AttributeControllor@delete');
    
    Route::get('admin/orders/{type}','Admin\OrdersControllor@view');
    //Route::GET('/admin/normal/orders', 'Admin\OrdersControllor@viewNormal')->name('normal-orders');
    Route::GET('orders-list/{type}', 'Admin\OrdersControllor@listView')->name('normal-orders-list');
    //Route::get('admin/orders/{type}','Admin\OrdersControllor@index');
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
    /* Areas */
    Route::GET('/admin/areas', 'Admin\AreaControllor@view')->name('address-areas');
    Route::GET('areas-list', 'Admin\AreaControllor@listView')->name('areas-list');
    Route::match(['post', 'get'],'/save-area','Admin\AreaControllor@save');
    Route::POST('/delete-area','Admin\AreaControllor@delete');
    Route::GET('/admin/area/{status}/{id}','Admin\AreaControllor@updateStatus');
    
    Route::match(['post', 'get'],'/admin/newslatter/list','Admin\DashboardControllor@newsLetter');
    
    Route::get('admin/abundant/cart','Admin\CartControllor@view');
    Route::GET('cart-list', 'Admin\CartControllor@listView')->name('cart-list');
    Route::GET('user/cart/items/{id}','Admin\CartControllor@cartDetail');
    
    Route::get('admin/fromers/list','Admin\DashboardControllor@view')->name('fromers');
    Route::GET('fromers-list', 'Admin\DashboardControllor@listView')->name('fromers-list');
});