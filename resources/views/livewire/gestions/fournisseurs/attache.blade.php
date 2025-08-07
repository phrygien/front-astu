<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    use Toast;

    public int $page = 1;
    public array $products = [];
    public int $totalPages = 1;
    public int $perPage = 15;

    public array $selectedProducts = [];
    public bool $checkedall = false;

    public int $fournisseurId;

    public function mount($id): void
    {
        // Récupération de la sélection globale en session
        $this->selectedProducts = session('selected_products', []);
        $this->fetchProducts();
        $this->fournisseurId = $id;
    }

    public function updatedPerPage(): void
    {
        $this->page = 1;
        $this->fetchProducts();
    }

    public function fetchProducts(): void
    {
        $token = session('token');
        $response = Http::withToken($token)->get(config('services.jwt.profile_endpoint') . '/product/produit', [
            'page' => $this->page,
            'per_page' => $this->perPage
        ]);

        if ($response->ok() && !$response['error']) {
            $this->products = $response['data']['data'];
            $this->totalPages = $response['data']['total_page'];

            $ids = collect($this->products)->pluck('id')->toArray();
            $this->checkedall = empty(array_diff($ids, $this->selectedProducts));
        }
    }

    public function goToPage(int $page): void
    {
        if ($page >= 1 && $page <= $this->totalPages) {
            $this->page = $page;
            $this->fetchProducts();
        }
    }

    public function updatedCheckedall(bool $value): void
    {
        $ids = collect($this->products)->pluck('id')->toArray();

        if ($value) {
            $this->selectedProducts = array_unique(array_merge($this->selectedProducts, $ids));
        } else {
            $this->selectedProducts = array_diff($this->selectedProducts, $ids);
        }

        $this->selectedProducts = array_values($this->selectedProducts);

        // Persist in session
        session(['selected_products' => $this->selectedProducts]);
    }

    public function updatedSelectedProducts(): void
    {
        $ids = collect($this->products)->pluck('id')->toArray();
        $this->checkedall = empty(array_diff($ids, $this->selectedProducts));

        // Persist in session
        session(['selected_products' => $this->selectedProducts]);
    }

    // public function sauvegarderSelection(): void
    // {
    //     // Nettoyage session et enregistrement final
    //     $selected = $this->selectedProducts;
    //     session()->forget('selected_products');
    //     session(['produits_selectionnes' => $selected]);

    //     $this->warning(
    //         'Produits sélectionnés enregistrés.',
    //         redirectTo: '/gestion/fournisseurs/execute-attach'
    //     );
    // }

    // public function sauvegarderSelection(): void
    // {
    //     $selected = collect($this->products)
    //         ->whereIn('id', $this->selectedProducts)
    //         ->values()
    //         ->all();

    //     $old = session('produits_selectionnes', []);
    //     $merged = collect($old)
    //         ->merge($selected)
    //         ->unique('id')
    //         ->values()
    //         ->all();

    //     session(['produits_selectionnes' => $merged]);

    //     $this->warning(
    //         'Produits sélectionnés enregistrés.',
    //         redirectTo: '/gestion/fournisseurs/execute-attach'
    //     );
    // }

    // public function sauvegarderSelection(): void
    // {
    //     $selected = collect($this->products)
    //         ->whereIn('id', $this->selectedProducts)
    //         ->values()
    //         ->all();

    //     $old = session('produits_selectionnes', []);

    //     // Nettoyer les anciennes données invalides (genre strings ou ids seuls)
    //     $old = collect($old)
    //         ->filter(fn($item) => is_array($item) && isset($item['id']))
    //         ->values();

    //     $merged = $old
    //         ->merge($selected)
    //         ->unique('id') // pour ne pas avoir deux fois le même produit
    //         ->values()
    //         ->all();

    //     session(['produits_selectionnes' => $merged]);

    //     $this->warning(
    //         'Produits sélectionnés enregistrés.',
    //         redirectTo: '/gestion/fournisseurs/execute-attach'
    //     );
    // }

        public function sauvegarderSelection(): void
    {
        // Produits de la page actuelle sélectionnés
        $selectionPage = collect($this->products)
            ->whereIn('id', $this->selectedProducts)
            ->values()
            ->all();

        // Ancienne sélection en session
        $ancienneSelection = collect(session('produits_selectionnes', []))
            ->filter(fn($item) => is_array($item) && isset($item['id']))
            ->values();

        // Fusion des données
        $fusion = $ancienneSelection
            ->merge($selectionPage)
            ->unique('id')
            ->values()
            ->all();

        // Sauvegarde session
        session([
            'produits_selectionnes' => $fusion,
            'fournisseur_id' => $this->fournisseurId,
        ]);
        

        $this->success('Produits sélectionnés enregistrés.');

        $this->redirect('/gestion/fournisseurs/execute-attach'); // ou route() si besoin
    }


    public function with(): array
    {
        return [
            'products' => $this->products,
            'currentPage' => $this->page,
            'totalPages' => $this->totalPages,
            'selectedCount' => count($this->selectedProducts),
        ];
    }
};


?>
<div>
    <x-header title="Produit fournisseur" subtitle="Gerer les produits fournisseur sur ASTUPARF" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Chercher ..." class="input-sm" />
        </x-slot:middle>
        <div class="mb-4">
    <span class="text-sm text-gray-600">Produits sélectionnés : <strong>{{ $selectedCount }}</strong></span>
</div>

        <x-slot:actions>
        <x-button icon="o-funnel" class="btn-sm" />
        <fieldset class="fieldset">
            <select class="select input-sm" wire:model.live="perPage">
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
            
            <x-button 
                icon="o-chevron-double-left" 
                class="btn-sm" 
                label="RETOUR"
                link="/gestion/fournisseurs/{{ $fournisseurId }}/view"
            />

            <x-button 
                    icon-right="o-chevron-double-right" 
                    class="btn-primary btn-sm" 
                    label="ETAPE SUIVANT"
                    wire:click="sauvegarderSelection"
                />

            
        </x-slot:actions>
    </x-header>

<div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">

        <table class="table w-full mt-4">
            <!-- head -->
            <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox" wire:model.live="checkedall" /> TOUS
                        </label>
                    </th>
                    <th>CODE PRODUIT</th>
                    <th>CODE CATEGORIE</th>
                    <th>CODE MARQUE</th>
                    <th>DESIGNATION</th>
                    <th>VARIANT</th>
                    <th>ARTICLE</th>
                    <th>REF_FABRI</th>
                    <th>EAN</th>
                    <th>ETAT</th>
                </tr>
            </thead>

            <tbody x-data="{ showSkeleton: true }" x-init="setTimeout(() => showSkeleton = false, 2000)">
                {{-- Skeleton visible pendant 5 secondes --}}
                @for ($i = 0; $i < 20; $i++)
                <tr x-show="showSkeleton" class="animate-pulse">
                    <th>
                        <div class="h-4 w-24 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </th>
                    <th>
                        <div class="h-4 w-24 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </th>
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
                    <td>
                        <div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </td>
                    <td>
                        <div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </td>
                    <td>
                        <div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </td>

                    {{-- <td class="text-end">
                        <div class="flex justify-end gap-2">
                            <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                        </div>
                    </td> --}}
                </tr>
                @endfor
            
                {{-- Données affichées après 5 secondes avec fade-in --}}
                @forelse($products as $product)
                <tr x-show="!showSkeleton"
                    x-transition.opacity.duration.2000ms
                    class="transition-opacity">
                    <th>
                        <label>
                        <input 
                            type="checkbox"
                            class="checkbox"
                            wire:model.live="selectedProducts"
                            value="{{ $product['id'] }}"
                        />

                        </label>
                    </th>
                    <th>{{ $product['product_code']}}</th>
                    <th>{{ $product['categorie_code']}}</th>
                    <th>{{ $product['marque_code']}}</th>
                    <th>{{ $product['designation'] }}</th>
                    <th>{{ $product['designation_variant'] }}</th>
                    <th>{{ $product['article'] }}</th>
                    <th>{{ $product['ref_fabri_n_1'] }}</th>
                    <th>{{ $product['EAN'] }}</th>
                    <td>
                        @if ($product['state'] == 1)
                            <span class="py-1 px-2 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                Actif
                            </span>
                        @else
                            <span class="py-1 px-2 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                Inactif
                            </span>
                        @endif
                    </td>
                    {{-- <td>{{ \Carbon\Carbon::parse($product['created_at'])->format('d/m/Y H:i') }}</td> --}}
                    {{-- <td class="text-end px-6 py-3">
                        <a class="btn btn-active btn-primary btn-sm" href="{{ route('produits.show', $product['id']) }}" wire:navigate>
                            Details
                        </a>
                    </td> --}}
                </tr>
                @empty
                <tr x-show="!showSkeleton" x-transition.opacity.duration.1000ms>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-neutral-500">
                        Aucun produit trouvé, verifier votre connexion .
                    </td>
                </tr>
                @endforelse
            </tbody>
            
            
            

        </table>
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

                <x-button 
                    icon="o-chevron-double-left" 
                    class="btn-sm" 
                    label="RETOUR"
                    link="/gestion/fournisseurs/{{ $fournisseurId }}/view"
                />

                <x-button 
                    icon-right="o-chevron-double-right" 
                    class="btn-primary btn-sm" 
                    label="ETAPE SUIVANT"
                    wire:click="sauvegarderSelection"
                />

                    
        </div>

    </div>
    <x-form wire:submit="loadParkod">

    <x-drawer
        wire:model="showDrawer3"
        with-close-button
        close-on-escape
        class="w-11/12 lg:w-1/3"
        right
    >

    <x-header title="PARKOD" subtitle="Charger le fichier PARKOD sur ASTUPARF" separator progress-indicator="loadParkod">
    </x-header>

            <div class="col-span-full">
                {{-- <label for="cover-photo" class="block text-sm/6 font-medium text-gray-900">PARKOD File</label> --}}
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                    <div class="text-center">
                    <div class="mt-4 flex text-sm/6 text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-pink-600 focus-within:ring-2 focus-within:ring-pink-600 focus-within:ring-offset-2 focus-within:outline-hidden hover:text-pink-500">
                        <span>Uploader le fichier</span>
                        <input id="file-upload" type="file" name="file" class="sr-only" wire:model="file" />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs/5 text-gray-600">TXT up to 10MB</p>
                    </div>
                </div>
            </div>
    
        <x-slot:actions>
            <x-button label="Annuler" @click="$wire.showDrawer3 = false" />
            <x-button label="Executer" class="btn-primary" type="submit" spinner="loadParkod" />
        </x-slot:actions>
    </x-drawer>

    </x-form>
</div>
