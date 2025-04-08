<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', fn() => Inertia::render("Home"));

Route::get('/my-cart', fn() => Inertia::render('MyCart'));

Route::get('/checkout', fn() => Inertia::render('Checkout'))->name('checkout');
