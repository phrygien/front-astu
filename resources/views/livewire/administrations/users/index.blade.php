<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component {
    
    public $profils = [];
    public bool $myModal1 = false;
    public bool $loading = true;
    public bool $showSkeleton = true;


    public int $page = 1;
    public array $users = [];
    public int $totalPages = 1;
    public int $perPage = 30;

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        $this->fetchUsers();
    }

    /**
     * fetch users
     * */

    public function fetchUsers(): void
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/admin/user', [
                'page' => $this->page,
                'per_page' => $this->perPage
            ]);

        if ($response->ok() && !$response['error']) {
            $this->users = $response['data']['data'];
            $this->totalPages = $response['data']['total_page'];
        }
    }

    public function goToPage($page): void
    {
        if ($page > 0 && $page <= $this->totalPages) {
            $this->page = $page;
            $this->fetchUsers();
        }
    }

    public function with(): array
    {
        return [
            'users' => $this->users,
            'currentPage' => $this->page,
            'totalPages' => $this->totalPages,
        ];
    }

}; ?>
<div>
    <x-header title="Utilisateurs" subtitle="Gerer les utilisateurs ASTUPARF" separator>
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
            <x-button icon="o-plus-circle" class="btn-primary btn-sm" link="users/create" />
        </x-slot:actions>
    </x-header>


    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table w-full">
            <!-- head -->
            <thead>
            <tr>
                <th>NOM ET PRENOM</th>
                <th>EMAIL</th>
                <th>PROFIL</th>
                <th>CRÉE-LE</th>
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
                @forelse($users as $user)
                <tr x-show="!showSkeleton"
                    x-transition.opacity.duration.2000ms
                    class="transition-opacity">
                    <th>{{ $user['name'] }}</th>
                    <td>
                        {{ $user['email'] }}
                    </td>
                    <th>{{ $user['profil'] }}</th>
                    <td>{{ \Carbon\Carbon::parse($user['created_at'])->format('d/m/Y H:i') }}</td>
                    <td class="text-end px-6 py-3">
                        <a class="btn btn-active btn-primary btn-sm" href="{{ route('users.edit', $user['id']) }}" wire:navigate>Modifier</a>
                        <button class="btn btn-dash btn-warning btn-sm">Activer</button>
                        <button class="btn btn-dash btn-error btn-sm">Desactiver</button>
                    </td>
                </tr>

                @empty
                <tr x-show="!showSkeleton" x-transition.opacity.duration.1000ms>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-neutral-500">
                        Aucun utilisateurs trouvé.
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
