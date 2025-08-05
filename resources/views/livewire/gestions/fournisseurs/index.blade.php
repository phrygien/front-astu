<?php

use Mary\Traits\Toast;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    use Toast;

    public bool $myModal1 = false;

    public int $page = 1;
    public array $fournisseurs = [];
    public int $totalPages = 1;
    public int $perPage = 20;

    #[Validate('required', message: 'Code marque obligatoire')]
    #[Validate('min:3', message: 'Le champ CODE doit contenir 3 caractères maximum')]
    #[Validate('max:3', message: 'Le champ CODE doit contenir 3 caractères maximum')]
    public string $code = '';

    #[Validate('required', message: 'Libelle marque obligatoire')]
    public string $name = '';

    public $token;

    public function mount(): void {
        $this->token = session('token');
        $this->fetchFournisseurs();
    }

    public function updatedPerPage(): void
    {
        $this->page = 1;
        $this->fetchFournisseurs();
    }

    public function fetchFournisseurs(): void
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/fournisseur/fournisseur', [
                'page' => $this->page,
                'per_page' => $this->perPage
            ]);
        if ($response->ok() && !$response['error']) {
            $this->fournisseurs = $response['data']['data'];
            $this->totalPages = $response['data']['total_page'];
        }
    }

    public function goToPage($page): void
    {
        if ($page > 0 && $page <= $this->totalPages) {
            $this->page = $page;
            $this->fetchFournisseurs();
        }
    }


    public function with(): array
    {
        return [
            'fournisseurs' => $this->fournisseurs,
            'currentPage' => $this->page,
            'totalPages' => $this->totalPages,
        ];
    }

}; ?>
<div>
   <div>

        <x-header title="Fournisseur" subtitle="Gerer le fournisseur" separator progress-indicator>
            <x-slot:middle class="!justify-end">
                <x-input icon="o-bolt" placeholder="Chercher ..." />
            </x-slot:middle>
            <x-slot:actions>
            <x-button icon="o-funnel" />

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
                
                <x-button icon="o-plus-circle" class="btn-primary btn-sm" label="Ajouter fournisseur" link="/gestion/fournisseurs/create" />
                
            </x-slot:actions>
        </x-header>

        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
            <table class="table w-full">
                <!-- head -->
                <thead>
                <tr>
                    <th>NAME</th>
                    <th>CODE</th>
                    <th>RAISON SOCIAL</th>
                    <th>ADRESSE SIEGE</th>
                    <th>CODE POSTAL</th>
                    <th>ADRESSE RETOUR</th>
                    <th>ETAT</th>
                    <th>CRÉE-LE</th>
                    <th class="text-end hidden md:table-cell">ACTION</th>
                </tr>
                </thead>

                <tbody x-data="{ showSkeleton: true }" x-init="setTimeout(() => showSkeleton = false, 2000)">
                    {{-- Skeleton visible pendant 5 secondes --}}
                    @for ($i = 0; $i < 20; $i++)
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
                    @forelse($fournisseurs as $fournisseur)
                    <tr x-show="!showSkeleton"
                        x-transition.opacity.duration.2000ms
                        class="transition-opacity">
                        <th>{{ $fournisseur['name'] }}</th>
                        <th>{{ $fournisseur['code'] }}</th>
                        <th>{{ $fournisseur['raison_social'] }}</th>
                        <th>{{ $fournisseur['adresse_siege'] }}</th>
                        <th>{{ $fournisseur['code_postal'] }}</th>
                        <th>{{ $fournisseur['adresse_retour'] }}</th>
                        <td>
                            @if ($fournisseur['state'] == 1)
                                <span class="py-1 px-2 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    Actif
                                </span>
                            @else
                                <span class="py-1 px-2 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                    Inactif
                                </span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($fournisseur['created_at'])->format('d/m/Y H:i') }}</td>
                        <td class="text-end px-6 py-3">
                            <a class="btn btn-active btn-primary btn-sm" href="{{ route('fournisseurs.view', $fournisseur['id']) }}" wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </a>
                            {{-- <a class="btn btn-dash btn-warning btn-sm" href="{{ route('produits.edit', $fournisseur['code']) }}" wire:navigate>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path d="m2.695 14.762-1.262 3.155a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.886L17.5 5.501a2.121 2.121 0 0 0-3-3L3.58 13.419a4 4 0 0 0-.885 1.343Z" />
                                </svg>
                            </a> --}}
                            <a class="btn btn-dash btn-error btn-sm" href="##">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr x-show="!showSkeleton" x-transition.opacity.duration.1000ms>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-neutral-500">
                            Aucun marque trouvé, verifier votre connexion .
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



    <x-modal wire:model="myModal1" title="Création de marque" class="backdrop-blur">

        <x-form wire:submit="save">
            <x-input label="Code Marque" wire:model="code" hint="Exemple: 001" />
            <x-input label="Libelle" wire:model="name" placeholder="" />
        
            <x-slot:actions>
                <x-button label="Annuler" @click="$wire.myModal1 = false" class="btn-sm" />
                <x-button label="Sauvegarder" class="btn-primary btn-sm" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

</div>
</div>