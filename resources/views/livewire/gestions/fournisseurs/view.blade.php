<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public array $fournisseur = [];


    public int $page = 1;
    public array $products = [];
    public int $totalPages = 1;
    public int $perPage = 10;

    public $fournisseurId;

    public function mount(int $id): void
    {
        $token = session('token');
        $this->fournisseurId = $id;

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/fournisseur/fournisseur/'. $id);

        if ($response->ok() && !$response['error']) {
            $this->fournisseur = $response['data'];
            $this->fetchProducts();
        } else {
            $this->fournisseur = [];
        }
    }

    // get products fournisseur
    public function fetchProducts(): void
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/fournisseur/produitfournisseur/liste/' . $this->fournisseurId, [
                'page' => $this->page,
                'per_page' => $this->perPage
            ]);

        if ($response->ok() && !$response['error']) {
            $this->products = $response['data']['data'];
            $this->totalPages = $response['data']['total_page'];
        }
    }    

    public function goToPage($page): void
    {
        if ($page > 0 && $page <= $this->totalPages) {
            $this->page = $page;
            $this->fetchProducts();
        }
    }
    
    public function updatedPerPage(): void
    {
        $this->page = 1;
        $this->fetchProducts();
    }

    public function with(): array
    {
        return [
            'fournisseur' => $this->fournisseur,
            'products' => $this->products,
            'currentPage' => $this->page,
            'totalPages' => $this->totalPages,
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
           <small> INFORMATIONS FOURNISSEUR</small>
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
<details open class="rounded-md border border-base-100 shadow-sm">
    <summary class="cursor-pointer select-none px-4 py-3 font-semibold text-pink-800 bg-base-200 rounded-t-md">
        <small>PRODUIT FOURNISSEUR</small>
    </summary>

<!-- Zone de recherche à gauche + contrôles à droite -->
<div class="flex flex-wrap justify-between items-center gap-4 px-4 pt-4">

    <!-- 🔍 Zone de recherche -->
    <div class="w-full sm:w-auto">
        <x-input icon="o-bolt" placeholder="Chercher ..." />
    </div>

    <div class="flex flex-wrap justify-end items-center gap-2">

        <fieldset class="fieldset">
            <select class="select select-sm" wire:model.live="perPage">
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

        <div class="inline-flex gap-x-1">
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

        <x-button icon="o-plus-circle" class="btn-primary btn-sm" @click="$wire.showDrawer3 = true" label="Attacher produit" />
    </div>
</div>


    <!-- Table -->
    <div class="px-4 pb-4 pt-2">
        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table w-full">
            <!-- head -->
            <thead>
            <tr>
                <th>DESIGNATION</th>
                <th>VARIANT</th>
                <th>ARTICLE</th>
                <th>REF_FABRI</th>
                <th>EAN</th>
                <th class="text-end hidden md:table-cell">ACTION</th>
            </tr>
            </thead>

            <tbody x-data="{ showSkeleton: true }" x-init="setTimeout(() => showSkeleton = false, 2000)">
                {{-- Skeleton visible pendant 5 secondes --}}
                @for ($i = 0; $i < 10; $i++)
                <tr x-show="showSkeleton" class="animate-pulse">
                    <th>
                        <div class="h-4 w-24 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </th>
                    <td>
                        <div class="h-4 w-20 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </td>
                    <td>
                        <div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </td>
                    <td>
                        <div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </td>
                    <td>
                        <div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </td>
                    <td class="text-end">
                        <div class="flex justify-end gap-2">
                            <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                        </div>
                    </td>
                </tr>
                @endfor
            
                {{-- Données affichées après 5 secondes avec fade-in --}}
                @forelse($products as $product)
                <tr x-show="!showSkeleton"
                    x-transition.opacity.duration.2000ms
                    class="transition-opacity">
                    <th>{{ $product['designation'] }}</th>
                    <th>{{ $product['designation_variant'] }}</th>
                    <th>{{ $product['article'] }}</th>
                    <th>{{ $product['ref_fabri_n_1'] }}</th>
                    <th>{{ $product['EAN'] }}</th>
                    <td class="text-end px-6 py-3">
                        <a class="btn btn-active btn-sm" href="{{ route('produits.show', $product['product_id']) }}" wire:navigate>
                            Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr x-show="!showSkeleton" x-transition.opacity.duration.1000ms>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-neutral-500">
                        Aucun produit trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
            
            
            

        </table>
        </div>
    </div>

    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
        <div>
            <flux:input icon="magnifying-glass" placeholder="Chercher profil" />
        </div>

        <div class="inline-flex gap-x-2">
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

