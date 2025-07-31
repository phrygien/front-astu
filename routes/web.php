<?php

use Livewire\Volt\Volt;

Volt::route('/', 'users.index');

/**
 * @see https://github.com/robsontenorio/laravel-volt
 */
Volt::route('/login', 'auth.login')->name('auth.login');


/**
 * Route pour le plateforme
 */
Route::middleware(['jwt-session-auth'])->group( function(): void {


    /**
     * @see https://github.com/robsontenorio/laravel-volt
     */
    Volt::route('/dashboard', 'plateforme.dashboard.dashboard')->name('dashboard');


    /**
     * Gestion profil et profilage
     */
    Volt::route('/administrations/profils','administrations.profils.index')->name('profils.index');
    Volt::route('/administrations/profil/create','administrations.profils.create')->name('profils.create');

    /**
     * Gestion utilisateurs
     */

});