<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia::render('Frontend/Index', [
        'response' => rtFormat([100, 200, 300]),
    ]);
});

Route::get('/todolist', function () {
    return Inertia::render('Frontend/Todolist/TodoList');
});
