<?php

use Mary\Traits\Toast;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    use Toast;

    public bool $myModal1 = false;
    public bool $myModal2 = false;

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

    public $selectedFournisseurId = null;

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

    public function activer()
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/fournisseur/fournisseur/'. $this->selectedFournisseurId .'/state/1');

        if ($response->ok() && !$response['error']) {
            $this->success('Activation fournisseur avec succès');
            $this->myModal1 = false;
            $this->selectedFournisseurId = null;
            $this->fetchFournisseurs();
        } else {
            $this->error("Erreur lors de l'activation fournisseur.");
        }
    }

    public function desactiver()
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/fournisseur/fournisseur/'. $this->selectedFournisseurId .'/state/0');

        if ($response->ok() && !$response['error']) {
            $this->success('Desactivation fournisseur avec succès');
            $this->myModal2 = false;
            $this->selectedFournisseurId = null;
            $this->fetchFournisseurs();
        } else {
            $this->error("Erreur lors de la desactivation.");
        }
    }

    
    public function openActivationModal($id)
    {
        $this->selectedFournisseurId = $id;
        $this->myModal1 = true;
    }


    public function openDesactivationModal($id)
    {
        $this->selectedFournisseurId = $id;
        $this->myModal2 = true;
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
            <x-button icon="o-funnel" class="btn-active" />

            <fieldset class="fieldset">
                <select class="select" wire:model.live="perPage">
                    <option disabled selected>Afficher par</option>
                    <option value="10">5</option>
                    <option value="10">10</option>
                    <option value="10">20</option>
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
                {{-- <button class="px-5 py-2 rounded border border-transparent text-white bg-[#12232f] hover:bg-[#1f3544] transition text-sm btn-sm">
                    Get a demo
                </button> --}}
                <x-button icon="o-plus-circle" class="btn-active btn-sm" label="Ajouter fournisseur" link="/gestion/fournisseurs/create" />
                
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
                        <td class="text-end">
                            <div class="flex justify-end gap-2">
                                <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                                <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                                {{-- <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div> --}}
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
                            {{-- <a class="btn btn-active btn-primary btn-sm" href="{{ route('fournisseurs.view', $fournisseur['id']) }}" wire:navigate>
                                Details
                            </a> --}}

                            <!-- alternate Button -->
                            <a href="{{ route('fournisseurs.view', $fournisseur['id']) }}" wire:navigate type="button" class="btn btn-sm">Details</a>

                            <a class="btn btn-soft btn-primary btn-sm" href="{{ route('fournisseurs.edit', $fournisseur['id']) }}" wire:navigate>
                                Modifier
                            </a>
                        @if ($fournisseur['state'] != 1)
                            <x-button class="btn-sm btn-soft btn-accent" wire:click="openActivationModal({{ $fournisseur['id'] }})" icon="o-check-badge" />
                        @endif

                        @if ($fournisseur['state'] == 1)
                            <button class="btn btn-soft btn-error btn-sm" wire:click="openDesactivationModal({{ $fournisseur['id'] }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </button>
                        @endif
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



<x-modal wire:model="myModal1" persistent class="fixed inset-0 max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">

            <!-- Contenu -->
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:size-10">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6 text-green-600">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-base font-semibold text-gray-900">
                            Activation du fournisseur
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Confirmez-vous l’activation de ce fournisseur ?
                            </p>
                        </div>

                        <!-- Loading avec texte -->
                        <div class="mt-4 flex items-center space-x-2 text-sm text-blue-500" wire:loading wire:target="activer">
                            <span>Activation en cours...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <x-button
                    wire:click="activer"
                    label="Confirmer"
                    wire:loading.attr="disabled"
                    class="inline-flex w-full justify-center btn btn-primary px-3 py-2 font-semibold shadow-xs btn-primary sm:ml-3 sm:w-auto"
                />
                <x-button
                    label="Annuler"
                    @click="$wire.myModal1 = false"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto"
                />
            </div>
</x-modal>



<x-modal wire:model="myModal2" persistent class="fixed inset-0 max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
            <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6 text-red-600">
                    <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-base font-semibold text-gray-900">
                    Désactivation du fournisseur
                </h3>
                <div class="mt-2 text-sm text-gray-500">
                    Confirmez-vous la désactivation du fournisseur ?
                </div>

                <!-- Loading avec texte -->
                <div class="mt-4 flex items-center space-x-2 text-sm text-blue-500" wire:loading wire:target="desactiver">
                    <span>Desactivation en cours...</span>
                </div>
            </div>
        </div>
    </div>

    <x-slot:actions>
        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 w-full">
            <x-button 
                label="Confirmer" 
                wire:click="desactiver" 
                class="btn btn-primary"
            />
            <x-button 
                label="Annuler" 
                @click="$wire.myModal2 = false" class="mr-4 btn btn-error"
            />
        </div>
    </x-slot:actions>
</x-modal>


    
</div>
</div>