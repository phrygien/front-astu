<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public int $page = 1;
    public array $products = [];
    public int $totalPages = 1;
    public int $perPage = 50;


    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';


    public function mount(): void
    {
        $this->fetchProducts();
    }

    public function updatedPerPage(): void
    {
        $this->page = 1;
        $this->fetchProducts();
    }

    public function fetchProducts(): void
    {
        $token = session('token');

        // $response = Http::withToken($token)
        //     ->get("http://dev.astucom.com:9038/erpservice/api/product/produit?page={$this->page}");

        $response = Http::withToken($token)
            ->get("http://dev.astucom.com:9038/erpservice/api/product/produit", [
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

    public function with(): array
    {
        return [
            'products' => $this->products,
            'currentPage' => $this->page,
            'totalPages' => $this->totalPages,
        ];
    }
};
?>
<div>
    <x-header title="Produits" subtitle="Gerer les produits ASTUPARF" separator progress-indicator>
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
            
            <x-button icon="o-cloud-arrow-down" class="btn-primary btn-sm" link="/gestion/produit/parkod" label="Charger PARKOD" />
        </x-slot:actions>
    </x-header>

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
                <th>ETAT</th>
                <th>CREE LE</th>
                <th class="text-end hidden md:table-cell">ACTION</th>
            </tr>
            </thead>

            <tbody x-data="{ showSkeleton: true }" x-init="setTimeout(() => showSkeleton = false, 5000)">
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
                    <td>
                        <div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </td>
                    <td>
                        <div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                    </td>

                    <td class="text-end">
                        <div class="flex justify-end gap-2">
                            <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                            <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div>
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
                    <td>{{ \Carbon\Carbon::parse($product['created_at'])->format('d/m/Y H:i') }}</td>
                    <td class="text-end px-6 py-3">
                        <a class="btn btn-active btn-primary btn-sm" href="{{ route('produits.show', $product['id']) }}" wire:navigate>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </a>
                        <a class="btn btn-dash btn-warning btn-sm" href="##">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                <path d="m2.695 14.762-1.262 3.155a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.886L17.5 5.501a2.121 2.121 0 0 0-3-3L3.58 13.419a4 4 0 0 0-.885 1.343Z" />
                            </svg>
                        </a>
                        <a class="btn btn-dash btn-error btn-sm" href="##">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM6.75 9.25a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr x-show="!showSkeleton" x-transition.opacity.duration.1000ms>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-neutral-500">
                        Aucun profil trouvé.
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
        </div>

    </div>
</div>
