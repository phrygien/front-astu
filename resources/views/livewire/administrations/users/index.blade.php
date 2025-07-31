<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component {
    
}; ?>

<div>
    <x-header title="Utilisateurs" subtitle="Gerer les utilisateurs ASTUPARF" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-bolt" placeholder="Chercher ..." />
        </x-slot:middle>
        <x-slot:actions>
            <div class="inline-flex gap-x-2">
                <button class="join-item btn btn-sm">1</button>
                <button class="join-item btn btn-sm">2</button>
                <button class="join-item btn btn-sm btn-disabled">...</button>
                <button class="join-item btn btn-sm">99</button>
                <button class="join-item btn btn-sm">100</button>
            </div>
            <x-button icon="o-plus-circle" class="btn-primary btn-sm" link="users/create" />
        </x-slot:actions>
    </x-header>


    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table w-full">
            <!-- head -->
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Job</th>
                <th>Favorite Color</th>
                <th class="text-end hidden md:table-cell">Action</th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <th>3</th>
                <td>Brice Swyre</td>
                <td>Tax Accountant</td>
                <td>Red</td>
                <td class="text-end px-6 py-3 hidden md:table-cell">
                    <a class="btn btn-active btn-primary btn-sm" href="###" wire:navigate>Details</a>
                    <button class="btn btn-dash btn-sm btn-warning">Activer</button>
                    <button class="btn btn-dash btn-sm btn-error">Desactiver</button>
                    <button class="btn btn-secondary btn-sm">Supprimer</button>
                </td>
            </tr>
            
            
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
    <div>
        <flux:input icon="magnifying-glass" placeholder="Chercher profil" />
    </div>

        <div class="inline-flex gap-x-2">
            <button class="join-item btn btn-sm">1</button>
            <button class="join-item btn btn-sm">2</button>
            <button class="join-item btn btn-sm btn-disabled">...</button>
            <button class="join-item btn btn-sm">99</button>
            <button class="join-item btn btn-sm">100</button>
        </div>
</div>
</div>
