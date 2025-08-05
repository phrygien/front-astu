<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="max-w-4xl mx-auto">

    <x-header title="Fournisseur" subtitle="Création d’un nouveau fournisseur" separator>
        <x-slot:actions>
            <x-button label="Annuler" link="/gestion/fournisseurs" class="btn-sm" />
            <x-button label="Sauvegarder" class="btn-primary btn-sm" type="submit" spinner="save" />
        </x-slot:actions>
    </x-header>
    
        <x-form wire:submit="save">

            <div class="lg:grid grid-cols-5">
                <div class="col-span-2">
                    <x-header title="Basic" subtitle="Basic information concernant le fournisseur" size="text-lg" />
                </div>
                <div class="col-span-3 grid gap-3">
                    <x-card shadow separator>
                        <x-input label="Nom" wire:model="name" icon="o-user" />
                        <x-input label="Code" wire:model="code" icon="o-finger-print" />
                        <x-input label="Raison social" wire:model="raison_social" icon="o-information-circle" />
                    </x-card>
                </div>
            </div>
    
            <hr class="my-5 border-base-300" />
    
            <div class="lg:grid grid-cols-5">
                <div class="col-span-2">
                    <x-header title="Contact" subtitle="Renseigner les contact du fournisseur" size="text-lg" />
                </div>
                <div class="col-span-3 grid gap-3">
                    <x-card shadow separator>
                        <x-input label="Telephone" wire:model="telephone" icon="o-phone" />
                        <x-input label="Mail" wire:model="mail" icon="o-at-symbol" />
                        <x-input label="Fax" wire:model="fax" icon="o-bolt" />
                    </x-card>
                </div>
            </div>


            <hr class="my-5 border-base-300" />
    
            <div class="lg:grid grid-cols-5">
                <div class="col-span-2">
                    <x-header title="Adresse" subtitle="Renseigner l'adresse du fournisseur" size="text-lg" />
                </div>
                <div class="col-span-3 grid gap-3">
                    <x-card shadow separator>
                        <x-input label="Ville" wire:model="ville" icon="o-map" />
                        <x-input label="Code Postal" wire:model="code_postal" icon="o-information-circle" />
                        <x-input label="Adresse siege" wire:model="adresse_siege" icon="o-map-pin" />
                        <x-input label="Ville retour" wire:model="ville_retour" icon="o-map" />
                        <x-input label="Code Postal retour" wire:model="code_postal_retour" icon="o-information-circle" />
                        <x-input label="Adresse retour" wire:model="adresse_retour" icon="o-map-pin" />
                    </x-card>
                </div>
            </div>
    
            <x-slot:actions>
                <x-button label="Annuler" link="/gestion/fournisseurs" class="btn-sm" />
                <x-button label="Sauvegarder" class="btn-primary btn-sm" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>

</div>
