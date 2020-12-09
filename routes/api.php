<?php

// public routes
Route::get('me', 'User\MeController@getMe');



Route::group(['middleware' => ['auth:api']], function() {

    Route::post('logout', 'Auth\LoginController@logout');
    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');

});

Route::group(['middleware' => ['guest:api']], function() {

    // Registrar Usuario
    Route::post('register', 'Auth\RegisterController@register');

    // Verificar Correo
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');

    // Reenviar Correo de Verificación
    Route::post('verification/resend', 'Auth\VerificationController@resend');

    // Login
    Route::post('login', 'Auth\LoginController@login');

    // cambiar contraseña
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');

    // restablecer contraseña
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

});