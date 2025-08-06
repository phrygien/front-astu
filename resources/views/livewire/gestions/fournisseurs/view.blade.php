<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public array $fournisseur = [];

    public function mount(int $id): void
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/fournisseur/fournisseur/'. $id);

        if ($response->ok() && !$response['error']) {
            $this->fournisseur = $response['data'];
        } else {
            $this->fournisseur = [];
        }
    }

    public function with(): array
    {
        return [
            'fournisseur' => $this->fournisseur,
        ];
    }
};

?>

<div class="max-w-5xl mx-auto">

    <x-header title="Détails " subtitle="Détails du fournisseur" separator>
        <x-slot:actions>
        <div class="breadcrumbs text-sm">
            <ul>
                <li><a href="{{ route('fournisseurs.index') }}" wire:navigate>Fournisseurs</a></li>
                <li>...</li>
                <li class="text-pink-800">@if($fournisseur){{ $fournisseur['name'] }}@else Les données du fournisseur sont introuvables @endif</li>
            </ul>
        </div>
        </x-slot:actions>
    </x-header>

@if($fournisseur)
<div class="flex flex-col gap-6">

    <details open class="rounded-md border border-base-100 bg-base-100 shadow-sm">
        <summary class="cursor-pointer select-none px-4 py-3 font-semibold text-base-content bg-base-200 rounded-t-md">
            Informations du fournisseur
        </summary>
        <div class="px-4 pb-4 pt-2">
            <x-card subtitle="Détails" separator class="space-y-4">
                <div class="space-y-2">
                    @php
                        $fields = [
                            ['label' => 'Nom', 'value' => $fournisseur['name'] ?? '-'],
                            ['label' => 'Code', 'value' => $fournisseur['code'] ?? '-'],
                            ['label' => 'Raison social', 'value' => $fournisseur['raison_social'] ?? '-'],
                            ['label' => 'Adresse siège', 'value' => $fournisseur['adresse_siege'] ?? '-'],
                            ['label' => 'Code postal', 'value' => $fournisseur['code_postal'] ?? '-'],
                            ['label' => 'Ville', 'value' => $fournisseur['ville'] ?? '-'],
                            ['label' => 'Téléphone', 'value' => $fournisseur['telephone'] ?? '-'],
                            ['label' => 'Fax', 'value' => $fournisseur['fax'] ?? '-'],
                            ['label' => 'Email', 'value' => $fournisseur['mail'] ?? '-'],
                            ['label' => 'Date de création', 'value' => \Carbon\Carbon::parse($fournisseur['created_at'])->locale('fr')->isoFormat('LL')],
                            ['label' => 'Dernière modification', 'value' => \Carbon\Carbon::parse($fournisseur['updated_at'])->locale('fr')->isoFormat('LL')],
                        ];
                    @endphp

                    @foreach ($fields as $field)
                        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
                            <dt class="min-w-40 text-sm text-gray-500 dark:text-neutral-500">{{ $field['label'] }} :</dt>
                            <dd class="w-full text-right text-sm text-gray-800 dark:text-neutral-200">{{ $field['value'] }}</dd>
                        </dl>
                    @endforeach
                </div>
            </x-card>
        </div>
    </details>

    <!-- Collapse 2 : Produits fournisseur -->
    <details open class="rounded-md border border-base-100 bg-base-100 shadow-sm">
        <summary class="cursor-pointer select-none px-4 py-3 font-semibold text-base-content bg-base-200 rounded-t-md">
            Produits fournisseur
        </summary>
        <div class="px-4 pb-4 pt-2">
            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Job</th>
                            <th>Favorite Color</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><th>1</th><td>Cy Ganderton</td><td>Quality Control Specialist</td><td>Blue</td></tr>
                        <tr><th>2</th><td>Hart Hagerty</td><td>Desktop Support Technician</td><td>Purple</td></tr>
                        <tr><th>3</th><td>Brice Swyre</td><td>Tax Accountant</td><td>Red</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </details>

</div>


@else
    <!-- Carte vide si $fournisseur est null -->
    <x-card class="text-center text-gray-500 py-16">
        <div class="flex flex-col items-center justify-center space-y-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-6h13M9 5h13M3 12h.01M3 6h.01M3 18h.01" />
            </svg>
            <p class="text-lg font-medium">Aucun fournisseur trouvé</p>
            <p class="text-sm">Les données du fournisseur sont introuvables ou n'ont pas été chargées.</p>
        </div>
    </x-card>
@endif

</div>

