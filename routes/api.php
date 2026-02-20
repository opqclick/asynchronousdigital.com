<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    $user->load('role');
    $array = $user->toArray();
    $array['role_name'] = $user->role ? $user->role->name : null;
    $array['role_display_name'] = $user->role ? $user->role->display_name : null;
    return $array;
});
