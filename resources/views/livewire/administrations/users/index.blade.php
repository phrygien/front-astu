<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component {
    
    public $profils = [];
    public bool $myModal1 = false;
    public bool $loading = true;
    public bool $showSkeleton = true;

    public function mount(): void
    {
        $this->fetchProfil();
    }

    /**
     * fetch profil
     * */

    public function fetchProfil(): void
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get("http://dev.astucom.com:9038/erpservice/api/admin/profil");

        if ($response->ok() && !$response['error']) {
            $this->profils = $response['data'];
        }
    }

    public function with(): array
    {
        return [
            'profils' => $this->profils,
            'showSkeleton' => $this->showSkeleton,
        ];
    }

    /**
     *  activer profil 
     **/
    public function activeProfil(): void
    {

    }

    /**
     * Desactiver profil
     * */
    public function disableProfil(): void
    {
        
    }


}; ?>
<div>
    <x-header title="Utilisateurs" subtitle="Gerer les utilisateurs ASTUPARF" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-bolt" placeholder="Chercher ..." />
        </x-slot:middle>
        <x-slot:actions>
            <div class="inline-flex gap-x-2">
                <button class="join-item btn btn-sm">1</button>
                <button class="join-item btn btn-sm">2</button>
                <button class="join-item btn btn-sm btn-disabled">...</button>
                <button class="join-item btn btn-sm">99</button>
                <button class="join-item btn btn-sm">100</button>
            </div>
            <x-button icon="o-plus-circle" class="btn-primary btn-sm" link="users/create" />
        </x-slot:actions>
    </x-header>


    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table w-full">
            <!-- head -->
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Job</th>
                <th>Favorite Color</th>
                <th class="text-end hidden md:table-cell">Action</th>
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
                @forelse($profils as $profil)
                <tr x-show="!showSkeleton"
                    x-transition.opacity.duration.2000ms
                    class="transition-opacity">
                    <th>{{ $profil['name'] }}</th>
                    <td>
                        @if ($profil['state'] == 1)
                            <span class="py-1 px-2 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                Actif
                            </span>
                        @else
                            <span class="py-1 px-2 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                Inactif
                            </span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($profil['created_at'])->format('d/m/Y H:i') }}</td>
                    <td class="text-end px-6 py-3">
                        <a class="btn btn-active btn-primary btn-sm" href="{{ route('profils.edit', $profil['id']) }}" wire:navigate>Details</a>
                        <button class="btn btn-dash btn-warning btn-sm">Activer</button>
                        <button class="btn btn-dash btn-error btn-sm">Desactiver</button>
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
            <button class="join-item btn btn-sm">1</button>
            <button class="join-item btn btn-sm">2</button>
            <button class="join-item btn btn-sm btn-disabled">...</button>
            <button class="join-item btn btn-sm">99</button>
            <button class="join-item btn btn-sm">100</button>
        </div>
</div>
</div>
