<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

Route::patch('/patch', function (Request $request) {
    $request->validateWithBag('patch_error_bag',
            ['accept_field' => 'required']);
});

Route::delete('/delete', function (Request $request) {
    $request->validate(['user_id_field' => 'numeric|max:100']);
})->name('delete_route');

Route::post('/post', function (Request $request) {
    $regexRule = 'regex:/^[a-zA-Z]([a-zA-Z0-9]|[a-zA-Z0-9]\.[a-zA-Z0-9])*$/';
    $request->validate([
        'username_field' => ['required', 'string', 'max:20', $regexRule],
        'password_field' => [Password::min(6)],
        'same_max_field' => 'max:20',
        'same_regex_field' => [$regexRule],
    ]);
});

/**
 * :extended_request_can_had_been_used
 * 
 */

