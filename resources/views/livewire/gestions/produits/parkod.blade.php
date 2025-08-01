<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="max-w-4xl mx-auto">

    <x-header title="PARKOD" subtitle="Charger le fichier PARKOD pour ASTUPARF" separator>
    </x-header>

    <x-form wire:submit="save">

        <x-card subtitle="Fichier parkod" separator progress-indicator class="space-y">

        
            <div class="col-span-full">
                {{-- <label for="cover-photo" class="block text-sm/6 font-medium text-gray-900">PARKOD File</label> --}}
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                    <div class="text-center">
                    <div class="mt-4 flex text-sm/6 text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 focus-within:outline-hidden hover:text-indigo-500">
                        <span>Uploader le fichier</span>
                        <input id="file-upload" type="file" name="file-upload" class="sr-only" />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs/5 text-gray-600">TXT up to 10MB</p>
                    </div>
                </div>
            </div>


            {{-- <x-slot:actions>
                <x-button label="Annuler" />
                <x-button label="Sauvergarder" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions> --}}
        </x-card>
    </x-form>
</div>
