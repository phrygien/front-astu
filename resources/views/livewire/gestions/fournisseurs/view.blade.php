<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    use Toast;

    public array $fournisseur = [];

    public int $page = 1;
    public array $products = [];
    public array $choiceproducts = [];
    public int $totalPages = 1;
    public int $perPage = 10;

    public $fournisseurId;

    public bool $showDrawer3 = false;

    // Initialisation des inputs produits
    public array $productsInputs = [
        ['product_id' => null, 'tax' => null, 'prix_fournisseur' => null],
    ];

    // Propriété pour stocker les options filtrées par ligne
    public array $filteredOptions = [];

    public function mount(int $id): void
    {
        $token = session('token');
        $this->fournisseurId = $id;

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/fournisseur/fournisseur/'. $id);

        if ($response->ok() && !$response['error']) {
            $this->fournisseur = $response['data'];
            $this->fetchProducts();
            $this->choiceProducts();
            $this->updateFilteredOptions(); // Important pour initialiser $filteredOptions
        } else {
            $this->fournisseur = [];
        }
    }

    public function choiceProducts(): void
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/product/produit', [
                'page' => $this->page,
                'per_page' => 99999999
            ]);

        if ($response->ok() && !$response['error']) {
            $this->totalPages = $response['data']['total_page'];

            $this->choiceproducts = collect($response['data']['data'])->map(function ($item) {
                return [
                    'id'   => $item['id'],
                    'name' => $item['designation'],  // On garde 'name' ici mais avec la valeur 'designation'
                ];
            })->toArray();
        }
    }

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

    public function addProductInput(): void
    {
        $this->productsInputs[] = ['product_id' => null, 'tax' => null, 'prix_fournisseur' => null];
        $this->updateFilteredOptions();
    }

    public function removeProductInput(int $index): void
    {
        unset($this->productsInputs[$index]);
        $this->productsInputs = array_values($this->productsInputs); // Reindex
        $this->updateFilteredOptions();
    }

    public function attacherProduct()
    {
        $token = session('token');

        foreach ($this->productsInputs as $item) {
            if (
                empty($item['product_id']) ||
                empty($item['prix_fournisseur']) ||
                empty($item['tax'])
            ) {
                $this->warning("Veuillez remplir tous les champs.");
                return;
            }
        }

        // Construire la liste des produits à envoyer
        $produitList = [];

        foreach ($this->productsInputs as $item) {
            $produitList[] = [
                'product_id' => $item['product_id'],
                'prix_fournisseur_ht' => $item['prix_fournisseur'],
                'tax' => $item['tax'],
            ];
        }

        $payload = [
            'fournisseur_id' => $this->fournisseurId,
            'produit_list' => $produitList,
        ];

        $response = Http::withToken($token)
            ->post(config('services.jwt.profile_endpoint') . '/fournisseur/produitfournisseur', $payload);

        if (!$response->ok() || $response['error']) {
            $this->warning("Erreur lors de l'attachement des produits.");
            return;
        }

        $this->success('Tous les produits ont été attachés avec succès.');
        $this->productsInputs = [['product_id' => null, 'tax' => null, 'prix_fournisseur' => null]];
        $this->updateFilteredOptions();
        $this->showDrawer3 = false;
    }


    public function getAvailableProducts($currentIndex): array
    {
        $selectedIds = collect($this->productsInputs)
            ->pluck('product_id')
            ->filter()
            ->values();

        return collect($this->choiceproducts)
            ->reject(function ($product) use ($selectedIds, $currentIndex) {
                $currentSelected = $this->productsInputs[$currentIndex]['product_id'] ?? null;

                return $selectedIds->contains($product['id']) && $product['id'] != $currentSelected;
            })
            ->map(fn($product) => [
                'id' => $product['id'],
                'name' => $product['name'],
            ])
            ->values()
            ->toArray();
    }

    public function updatedProductsInputs()
    {
        $this->updateFilteredOptions();
    }

    public function updateFilteredOptions()
    {
        foreach ($this->productsInputs as $index => $input) {
            $this->filteredOptions[$index] = $this->getAvailableProducts($index);
        }
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


<div class="w-full mx-auto">

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

<div class="flow-root" x-data="{ showData: false }" x-init="setTimeout(() => showData = true, 2000)">
    {{-- Skeleton pendant le chargement --}}
    <div x-show="!showData" class="space-y-4 animate-pulse">
        @for ($i = 0; $i < 10; $i++)
            <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                <dt class="h-4 w-32 bg-gray-300 dark:bg-neutral-700 rounded col-span-1"></dt>
                <dd class="h-4 w-full bg-gray-200 dark:bg-neutral-600 rounded col-span-2"></dd>
            </div>
        @endfor
    </div>

    {{-- Données affichées après le skeleton --}}
    <div x-show="showData" x-transition.opacity.duration.500ms>
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

        <dl class="-my-3 divide-y divide-gray-200 rounded border border-gray-200 text-sm">
            @foreach ($fields as $field)
                <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-gray-900">{{ $field['label'] }}</dt>
                    <dd class="text-gray-700 sm:col-span-2">{{ $field['value'] }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>


    {{-- <x-card class="rounded-md border border-base-100 bg-base-100 shadow-sm" separator>
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


    <div class="flow-root">
    <dl class="-my-3 divide-y divide-gray-200 rounded border border-gray-200 text-sm">
        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
        <dt class="font-medium text-gray-900">Title</dt>

        <dd class="text-gray-700 sm:col-span-2">Mr</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
        <dt class="font-medium text-gray-900">Name</dt>

        <dd class="text-gray-700 sm:col-span-2">John Frusciante</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
        <dt class="font-medium text-gray-900">Occupation</dt>

        <dd class="text-gray-700 sm:col-span-2">Guitarist</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
        <dt class="font-medium text-gray-900">Salary</dt>

        <dd class="text-gray-700 sm:col-span-2">$1,000,000+</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
        <dt class="font-medium text-gray-900">Bio</dt>

        <dd class="text-gray-700 sm:col-span-2">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Et facilis debitis explicabo
            doloremque impedit nesciunt dolorem facere, dolor quasi veritatis quia fugit aperiam
            aspernatur neque molestiae labore aliquam soluta architecto?
        </dd>
        </div>
    </dl>
    </div>


    </x-card> --}}


        <div class="flex flex-wrap justify-between items-center gap-4 mt-4">
            <div class="w-full sm:w-auto">
                <x-input icon="o-bolt" placeholder="Chercher ..." class="input-sm" />
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

                <a href="{{ route('fournisseurs.attache', $fournisseur['id']) }}" wire:navigate class="btn btn-primary btn-sm">Attacher des produits au fournisseur</a>
            </div>
        </div>

        <div class="overflow-x-auto mt-4 rounded-box border border-base-content/5 bg-base-100">
            <table class="table w-full">
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
                    @for ($i = 0; $i < 10; $i++)
                    <tr x-show="showSkeleton" class="animate-pulse">
                        <th><div class="h-4 w-24 bg-gray-200 dark:bg-neutral-800 rounded"></div></th>
                        <td><div class="h-4 w-20 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                        <td><div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                        <td><div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                        <td><div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                        <td class="text-end"><div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                    </tr>
                    @endfor

                    @forelse($products as $product)
                    <tr x-show="!showSkeleton" x-transition.opacity.duration.2000ms class="transition-opacity">
                        <td>{{ $product['designation'] }}</td>
                        <td>{{ $product['designation_variant'] }}</td>
                        <td>{{ $product['article'] }}</td>
                        <td>{{ $product['ref_fabri_n_1'] }}</td>
                        <td>{{ $product['EAN'] }}</td>
                        <td class="text-end px-6 py-3">
                            <a class="btn btn-active btn-sm" href="{{ route('produits.show', $product['product_id']) }}" wire:navigate>
                                Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr x-show="!showSkeleton" x-transition.opacity.duration.1000ms>
                        <td colspan="6" class="py-10 px-6 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4 text-gray-500 dark:text-neutral-400">
                                <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.75 9.75v28.5h28.5V9.75H9.75zm3 3h22.5v22.5H12.75V12.75zm3 4.5h16.5M15.75 22.5h16.5M15.75 28.5h11.25"/>
                                </svg>
                                <p class="text-sm">Aucun produit trouvé pour ce fournisseur</p>
                            </div>
                        </td>
                    </tr>

                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700 mt-4">
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

    {{-- <x-card class="rounded-md border border-base-100 shadow-sm" title="Produits fournisseur" subtitle="Liste des produits associés" separator>

        <div class="flex flex-wrap justify-between items-center gap-4">
            <div class="w-full sm:w-auto">
                <x-input icon="o-bolt" placeholder="Chercher ..." class="input-sm" />
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

                <a href="{{ route('fournisseurs.attache', $fournisseur['id']) }}" wire:navigate class="btn btn-primary btn-sm">Attacher des produits au fournisseur</a>
            </div>
        </div>

        <div class="overflow-x-auto mt-4 rounded-box border border-base-content/5 bg-base-100">
            <table class="table w-full">
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
                    @for ($i = 0; $i < 10; $i++)
                    <tr x-show="showSkeleton" class="animate-pulse">
                        <th><div class="h-4 w-24 bg-gray-200 dark:bg-neutral-800 rounded"></div></th>
                        <td><div class="h-4 w-20 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                        <td><div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                        <td><div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                        <td><div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                        <td class="text-end"><div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div></td>
                    </tr>
                    @endfor

                    @forelse($products as $product)
                    <tr x-show="!showSkeleton" x-transition.opacity.duration.2000ms class="transition-opacity">
                        <td>{{ $product['designation'] }}</td>
                        <td>{{ $product['designation_variant'] }}</td>
                        <td>{{ $product['article'] }}</td>
                        <td>{{ $product['ref_fabri_n_1'] }}</td>
                        <td>{{ $product['EAN'] }}</td>
                        <td class="text-end px-6 py-3">
                            <a class="btn btn-active btn-sm" href="{{ route('produits.show', $product['product_id']) }}" wire:navigate>
                                Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr x-show="!showSkeleton" x-transition.opacity.duration.1000ms>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-neutral-500">
                            Aucun produit trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700 mt-4">
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
    </x-card> --}}

</div>



@else
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

<x-drawer
    wire:model="showDrawer3"
    title="Ajouter produit"
    subtitle="Attacher produit avec ce fournisseur"
    separator
    with-close-button
    close-on-escape
    class="w-11/12 lg:w-1/2"
    right
>
    <div class="space-y-4">

        @foreach ($productsInputs as $index => $input)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-end border p-4 rounded-md bg-base-100 shadow-sm">

                <div class="flex-1">
                <x-choices-offline
                    label="Produit"
                    wire:model="productsInputs.{{ $index }}.product_id"
                    :options="$filteredOptions[$index] ?? []"
                    placeholder="Rechercher un produit..."
                    searchable
                    clearable
                    single
                />


                </div>

                <div>
                    <x-input
                        label="TAX"
                        wire:model="productsInputs.{{ $index }}.tax"
                        type="number"
                        step="0.01"
                    />
                </div>

                <div class="flex gap-2 items-end">
                    <x-input
                        label="Prix fournisseur"
                        wire:model="productsInputs.{{ $index }}.prix_fournisseur"
                        type="number"
                        step="0.01"
                    />

                    @if (count($productsInputs) > 1)
                        <x-button icon="o-trash" class="btn-error btn-sm" wire:click="removeProductInput({{ $index }})" />
                    @endif
                </div>
            </div>
        @endforeach

        <div class="text-right">
            <x-button icon="o-plus-circle" class="btn-sm btn-primary" label="Ajouter une ligne" wire:click="addProductInput" />
        </div>

    </div>

    <x-slot:actions>
        <x-button label="Annuler" @click="$wire.showDrawer3 = false" />
        <x-button label="Sauvegarder" class="btn-primary" wire:click="attacherProduct" />
    </x-slot:actions>
</x-drawer>



</div>

