<?php

use Illuminate\Support\Facades\Http;
use Livewire\Volt\Component;

new class extends Component {
    
    public $profils = [];
    public bool $myModal1 = false;

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

    public function with(): array
    {
        return [
            'profils' => $this->profils
        ];
    }

}; ?>

<div>
    <x-header title="Profie" subtitle="Manage profiles" separator>
    <x-slot:middle class="!justify-end">
        <x-input icon="o-bolt" placeholder="Chercher profil..." />
    </x-slot:middle>
    <x-slot:actions>
        <div class="inline-flex gap-x-2">
            <button class="join-item btn">1</button>
            <button class="join-item btn">2</button>
            <button class="join-item btn btn-disabled">...</button>
            <button class="join-item btn">99</button>
            <button class="join-item btn">100</button>
        </div>
        <x-button icon="o-plus-circle" class="btn-primary" link="profil/create" />
    </x-slot:actions>
</x-header>



<div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
  <table class="table">
    <!-- head -->
    <thead>
      <tr>
        <th>Libelle</th>
        <th>Status</th>
        <th>Date de creation</th>
        <th class="text-end">Action</th>
      </tr>
    </thead>
    <tbody>
        @forelse($profils as $profil)
        <tr>
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
            <td> {{ \Carbon\Carbon::parse($profil['created_at'])->format('d/m/Y H:i') }}</td>
            <td class="text-end px-6 py-3">
                <a class="btn btn-active btn-primary" href="{{ route('profils.edit', $profil['id']) }}" wire:navigate>Details</a>
                <button class="btn btn-dash btn-warning">Activer</button>
                <button class="btn btn-dash btn-error">Desactiver</button>
            </td>
        </tr>
        @empty
            <tr>
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
            <button class="join-item btn">1</button>
            <button class="join-item btn">2</button>
            <button class="join-item btn btn-disabled">...</button>
            <button class="join-item btn">99</button>
            <button class="join-item btn">100</button>
        </div>
</div>

</div>
