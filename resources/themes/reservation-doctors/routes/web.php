<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'reserve'], function () {
    Route::get('schedule/{service}', 'DoctorReservationsController@schedule');
    Route::get('schedule/get-service-schedule/{service}',
        'DoctorReservationsController@getServiceFormattedSchedule');

    Route::post('remove-reservation/{reservation}', 'DoctorReservationsController@removeReservation');

    Route::post('create-reservation', 'DoctorReservationsController@createReservation');

    Route::get('{service}/get-optional-line-items', 'DoctorReservationsController@getServiceOptionalLineItems');

    Route::get('list', 'DoctorPublicController@list');


    Route::post('cancel/{reservation}', 'DoctorReservationsController@cancel');
    Route::post('confirm-reservation/{reservation}', 'DoctorReservationsController@confirmReservation');


    Route::group(['prefix' => 'checkout'], function () {
        Route::get('success/{reservation}', 'DoctorReservationsController@successCheckout');

        Route::get('{reservation}', 'DoctorReservationsController@checkoutPage');
        Route::post('{reservation}', 'DoctorReservationsController@checkout');
    });


});

Route::get('my-dashboard', 'DoctorDashboardController@dashboard')->middleware('auth');
Route::get('my-patients', 'DoctorDashboardController@myPatients')->middleware('auth');

Route::get('favourites', 'DoctorWishlistsController@index')
    ->middleware('auth');

Route::post('wishlist/{user}', 'DoctorWishlistsController@setWishlist');


Route::group(['prefix' => 'doctor'], function () {

    Route::group(['prefix' => 'line-items'], function () {
        Route::get('/', 'DoctorLineItemsController@index');
        Route::get('{line_item}/edit', 'DoctorLineItemsController@edit');
        Route::get('/create', 'DoctorLineItemsController@create');

        Route::put('{line_item}/update', 'DoctorLineItemsController@update');
        Route::post('store', 'DoctorLineItemsController@store');

    });

    Route::get('my-service', 'DoctorServiceController@editMyService');
    Route::put('my-service/{service}', 'DoctorServiceController@update');

    Route::get('{user}', 'DoctorDashboardController@doctorProfile');
});


