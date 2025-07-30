<?php

use Livewire\Volt\Volt;

Volt::route('/', 'users.index');

/**
 * @see https://github.com/robsontenorio/laravel-volt
 */
Volt::route('/login', 'auth.login');

/**
 * @see https://github.com/robsontenorio/laravel-volt
 */
Volt::route('/dashboard', 'plateforme.dashboard.dashboard')->name('dashboard');
