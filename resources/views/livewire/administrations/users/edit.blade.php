<?php

use Livewire\Volt\Component;

use Mary\Traits\Toast;
use Illuminate\Support\Facades\Http;

new class extends Component {
    
    use Toast;

    public $profils = [];
    public $profil_id;
    public $name;
    public $email;
    public $token;

    // get user ID
    public $userId;

    protected function rules() 
    {
        return [
            'name' => 'required|min:5',
            'email' => 'required',
            'profil_id' => 'required'
        ];
    }

    public function mount($id = null)
    {
        $this->token = session('token');
        $this->userId = $id;
        $this->loadUser($id);
        $this->fetchProfil();

    }


    public function loadUser($id)
    {
        $response = Http::withToken($this->token)
            ->get("http://dev.astucom.com:9038/erpservice/api/admin/user/{$id}");

        if ($response->ok() && !$response['error']) {
            $data = $response['data']['user'];
            $this->name = $data['name'];
            $this->email = $data['email'];
            $this->profil_id = $data['profil']['id'];
        } else {
            $this->danger("Erreur lors du chargement de l'utilisateur .");
        }
    }


    public function fetchProfil(): void
    {
        $token = session('token');

        $response = Http::withToken($token)
                    ->get("http://dev.astucom.com:9038/erpservice/api/admin/profil");

        if ($response->ok()) {
            $this->profils = $response['data'];
        }
    }

    public function save(): void 
    {
        $this->validate(); 

        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'profil_id' => $this->profil_id
        ];

        $response = Http::withToken($this->token)
            ->put('http://dev.astucom.com:9038/erpservice/api/admin/user/' . $this->userId, $payload);

        if ($response->ok() && !$response['error']) {
            $this->success(
                'Modification avec success',
                '',
                redirectTo: '/administrations/users'
            );
        } else {
            $this->success("Erreur lors de la sauvegarde.");
        }
    
    }

    public function activer()
    {
        $response = Http::withToken($this->token)
            ->get('http://dev.astucom.com:9038/erpservice/api/admin/user/' . $this->userId . '/state/'. 1);

        if ($response->ok() && !$response['error']) {
            $this->success(
                'Activation utilisateur avec succes',
                '',
                redirectTo: '/administrations/users'
            );
        } else {
            $this->success("Erreur lors de la sauvegarde.");
        }
    }

    public function with(): array
    {
        return [
            'profils' => $this->profils
        ];
    }

}; ?>

<div class="max-w-4xl mx-auto">

<x-header title="Détails " subtitle="Basic information" separator>
    <x-slot:actions>
    <div class="breadcrumbs text-sm">
        <ul>
            <li><a href="{{ route('users.index') }}" wire:navigate>Utilisateurs</a></li>
            <li>...</li>
            <li class="text-pink-800">{{ $name }}</li>
        </ul>
    </div>
        {{-- |
        <x-button label="Activer" class="btn-accent btn-sm btn-soft" wire:click='activer' spiner />
        <x-button label="Desactiver" class="btn-error btn-sm btn-soft" /> --}}
    </x-slot:actions>
</x-header>

<div class="max-w-4xl mx-auto">
    <x-form wire:submit="save">

        <x-card subtitle="Les informations de l'utilisateur sont modifiables" separator progress-indicator class="space-y">
            <div>
                <x-input label="Nom et prenoms" wire:model="name" placeholder="" icon="o-user" hint="Nom complet de l'utilisateur" />
            </div>

            <div class="mt-3">
                <x-input label="Adresse email" wire:model="email" placeholder="" icon="o-envelope" />
            </div>
        
            <fieldset class="fieldset w-full mt-2">
                <legend class="fieldset-legend">Profil</legend>
                <select class="select w-full" wire:model="profil_id">
                    @foreach ($profils as $profil)
                        <option value="{{ $profil['id'] }}">{{ $profil['name'] }}</option>
                    @endforeach
                </select>
            </fieldset>

            <x-slot:actions>
                <x-button label="Annuler" link="/administrations/users" />
                <x-button label="Sauvergarder" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-card>

        </x-form>
    </div>
</div>

