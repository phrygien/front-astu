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

        // $response = Http::withToken($token)
        //     ->get("http://dev.astucom.com:9038/erpservice/api/admin/profil");

        // if ($response->ok() && !$response['error']) {
        //     $this->profils = $response['data'];
        // }
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

            <tbody>
               @if ($showSkeleton)
                    @for ($i = 0; $i < 10; $i++)
                    <tr class="animate-pulse">
                        <th class="py-4">
                            <div class="h-4 w-4 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                        </th>
                        <td>
                            <div class="h-4 w-32 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                        </td>
                        <td>
                            <div class="h-4 w-40 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                        </td>
                        <td>
                            <div class="h-4 w-24 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                        </td>
                        <td class="text-end px-6 py-3 hidden md:table-cell">
                            <div class="flex gap-2 justify-end">
                                <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                                <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                                <div class="h-8 w-16 bg-gray-200 dark:bg-neutral-800 rounded"></div>
                            </div>
                        </td>
                    </tr>
                    @endfor
                @else
                    @foreach($profils as $profil)
                        <tr class="group hover:bg-gray-50 transition">
                            <th>gh</th>
                            <td>sdfs</td>
                            <td>sdfd</td>
                            <td>dgfd</td>
                            <td class="text-end px-6 py-3 hidden md:table-cell">
                                <!-- Tes boutons ici -->
                            </td>
                        </tr>
                    @endforeach
                @endif
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
