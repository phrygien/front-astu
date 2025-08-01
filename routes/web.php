<?php

use Livewire\Volt\Volt;


/**
 * @see https://github.com/robsontenorio/laravel-volt
 */
Volt::route('/login', 'auth.login')->name('auth.login');


/**
 * Route pour le plateforme
 */
Route::middleware(['jwt-session-auth'])->group( function(): void {

    /**
     * Dashboard
     */

    Volt::route('/', 'plateforme.dashboard.dashboard');
    /**
     * @see https://github.com/robsontenorio/laravel-volt
     */
    Volt::route('/dashboard', 'plateforme.dashboard.dashboard')->name('dashboard');


    /**
     * Gestion profil et profilage
     */
    Volt::route('/administrations/profils','administrations.profils.index')->name('profils.index');
    Volt::route('/administrations/profil/create','administrations.profils.create')->name('profils.create');
    Volt::route('administrations/profil/{id}/edit','administrations.profils.edit')->name('profils.edit');

    /**
     * Gestion utilisateurs
     */
    Volt::route('/administrations/users','administrations.users.index')->name('users.index');
    Volt::route('/administrations/users/create', 'administrations.users.create')->name('users.create');

    /**
     * Routes pour la gestion
     */
    Route::prefix('gestion')->group(function(): void {

        // gestion produit
        Volt::route('/produits','gestions.produits.index')->name('produits.index');
        Volt::route('/produit/parkod','gestions.produits.parkod')->name('produits.parkod');
        Volt::route('/produit/{id}/show','gestions.produits.show')->name('produits.show');
        Volt::route('/produit/{id}/edit','gestions.produits.edit')->name('produits.edit');

        /**
         * gestion marque
         */
        Volt::route('/marque','gestions.marques.index')->name('marques.index');
        Volt::route('/marque/create','gestions.marques.create')->name('marque.create');
        Volt::route('/marque/{id}/edit','gestions.marques.edit')->name('marque.edit');

    });

});