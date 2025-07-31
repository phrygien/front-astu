<?php

use Livewire\Volt\Component;

new class extends Component {
    
    public function mount(): void
    {

    }

    public function with(): array
    {
        return [
            ''
        ];
    }
}; ?>

<div class="p-8">
    <div class="max-w-4xl mx-auto">
    <x-form wire:submit="saveProfile">
        <x-header title="Modification profil" subtitle="Modifier les information sur le profils" separator>

            <x-slot:actions>
                <x-button label="Annuler" />
                <x-button label="Sauvegarder" class="btn-primary" type="submit" spinner="saveProfile" />
            </x-slot:actions>
        </x-header>

        <x-card subtitle="Basic information sur le profil" separator progress-indicator>
               
        
        </x-card>

        <x-card subtitle="Permissions" separator progress-indicator>
               

            <x-slot:actions>
                <x-button label="Annuler" />
                <x-button label="Sauvegarder" class="btn-primary" type="submit" spinner="saveProfile" />
            </x-slot:actions>
        
        </x-card>


        </x-form>
    </div>
</div>
