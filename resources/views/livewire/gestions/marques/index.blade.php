<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>

    <div class="max-w-7xl mx-auto">
    <x-header title="Marque" subtitle="Tous les marques" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-bolt" placeholder="Chercher ..." />
        </x-slot:middle>
        <x-slot:actions>

        <fieldset class="fieldset">
            <select class="select" wire:model.live="perPage">
                <option disabled selected>Afficher par</option>
                <option value="10">10</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="150">150</option>
                <option value="200">200</option>
                <option value="250">250</option>
                <option value="300">300</option>
            </select>
            </fieldset>

            {{-- <div class="inline-flex gap-x-2">
                @for ($i = 1; $i <= $totalPages; $i++)
                    @if ($i === $currentPage)
                        <button class="join-item btn btn-sm btn-primary" wire:click="goToPage({{ $i }})">{{ $i }}</button>
                    @elseif ($i === 1 || $i === $totalPages || abs($i - $currentPage) <= 1)
                        <button class="join-item btn btn-sm" wire:click="goToPage({{ $i }})">{{ $i }}</button>
                    @elseif ($i === $currentPage - 2 || $i === $currentPage + 2)
                        <button class="join-item btn btn-sm btn-disabled">...</button>
                    @endif
                @endfor
            </div>
             --}}
            <x-button icon="o-cloud-arrow-down" class="btn-primary btn-sm" @click="$wire.showDrawer3 = true" label="Ajouter marque" />
            
        </x-slot:actions>
    </x-header>

        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table">
            <!-- head -->
            <thead>
            <tr>
                <th>CODE</th>
                <th>NAME</th>
                <th>STATUT</th>
                <th>CREE LE</th>
                <th>ACTION</th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <th>1</th>
                <td>Cy Ganderton</td>
                <td>Quality Control Specialist</td>
                <td>Blue</td>
            </tr>

            </tbody>
        </table>
        </div>

    </div>




</div>
