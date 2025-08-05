<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

    {{-- Header principal --}}
    <x-header title="Paramètres" subtitle="Configurer votre compte" separator class="mb-8" />

    {{-- Informations de profil --}}
    <x-card title="Profil" subtitle="Informations sur votre compte" separator progress-indicator class="space-y-6">
        <x-form wire:submit="saveProfile">
            <x-input label="Nom du compte" wire:model="name" placeholder="Votre nom" icon="o-user" />
            <x-input label="Adresse e-mail" wire:model="email" placeholder="votre@email.com" icon="o-at-symbol" />

            <x-slot:actions>
                <x-button label="Sauvegarder les informations" class="btn-primary" type="submit" spinner="saveProfile" />
            </x-slot:actions>
        </x-form>
    </x-card>

    {{-- Mise à jour mot de passe --}}
    <x-card title="Mot de passe" subtitle="Mettre à jour votre mot de passe" separator progress-indicator class="space-y-6">
        <x-form wire:submit="updatePassword">
            <x-input type="password" label="Mot de passe actuel" wire:model="current_password" icon="o-lock-closed" />
            <x-input type="password" label="Nouveau mot de passe" wire:model="new_password" icon="o-lock-closed" />
            <x-input type="password" label="Confirmer le nouveau mot de passe" wire:model="new_password_confirmation" icon="o-lock-closed" />

            <x-slot:actions>
                <x-button label="Mettre à jour le mot de passe" class="btn-primary" type="submit" spinner="updatePassword" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>

