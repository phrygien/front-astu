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

<div class="max-w-4xl mx-auto">

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
<x-card subtitle="Informations du fournisseur" separator class="space-y">
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- SVG à gauche -->
        <div class="bg-gray-100 rounded-md p-4 flex items-center justify-center w-full lg:w-1/3 min-h-[220px]">
            <svg width="160" height="160" viewBox="0 0 220 200" xmlns="http://www.w3.org/2000/svg">
                <rect x="40" y="110" width="140" height="60" fill="#e0c097" stroke="#b08968" stroke-width="2" rx="4" />
                <polygon points="40,110 60,90 180,90 200,110" fill="#f3d5a8" stroke="#b08968" stroke-width="2"/>
                <polygon points="60,90 60,110 180,110 180,90" fill="none" stroke="#b08968" stroke-width="1" stroke-dasharray="4,2"/>

                <rect x="60" y="65" width="20" height="40" rx="5" fill="#fcd5ce" stroke="#b5838d" stroke-width="1.5"/>
                <rect x="65" y="55" width="10" height="10" fill="#e5989b" stroke="#b5838d" stroke-width="1" rx="2"/>
                
                <rect x="100" y="65" width="20" height="40" rx="5" fill="#cdb4db" stroke="#9d4edd" stroke-width="1.5"/>
                <rect x="105" y="55" width="10" height="10" fill="#b185db" stroke="#9d4edd" stroke-width="1" rx="2"/>
                
                <rect x="140" y="65" width="20" height="40" rx="5" fill="#b5ead7" stroke="#28a745" stroke-width="1.5"/>
                <rect x="145" y="55" width="10" height="10" fill="#7dd6b2" stroke="#28a745" stroke-width="1" rx="2"/>

                <text x="110" y="190" text-anchor="middle" font-size="14" fill="#333" font-family="Arial, sans-serif" font-weight="bold">
                    {{ $fournisseur['name'] }}
                </text>
            </svg>
        </div>

        <!-- Description à droite -->
        <div class="w-full lg:w-2/3 space-y-7">
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
    </div>
</x-card>

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

