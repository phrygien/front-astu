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

    protected function rules() 
    {
        return [
            'name' => 'required|min:5',
            'email' => 'required',
            'profil_id' => 'required'
        ];
    }

    public function mount(): void
    {
        $this->fetchProfil();
        $this->token = session('token');
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
            ->post('http://dev.astucom.com:9038/erpservice/api/admin/user', $payload);

        if ($response->ok() && !$response['error']) {
            $this->success('Utilisateur sauvegardé avec succès');
            $this->reset();
        } else {
            $this->success("Erreur lors de la sauvegarde de l'utilisateur.");
        }
    
    }

    public function with(): array
    {
        return [
            'profils' => $this->profils
        ];
    }

}; ?>

<div class="p-8">

    <x-header title="Creation utilisateur" subtitle="Ajouter un tulisateur WINPARF" separator>

        {{-- <x-slot:actions>
            <x-button label="Annuler" />
            <x-button label="Sauvegarder" class="btn-primary" type="submit" spinner="saveProfile" />
        </x-slot:actions> --}}
    </x-header>

<div class="max-w-4xl mx-auto">
    <x-form wire:submit="save">

        <x-card subtitle="Basic information sur le profil" separator progress-indicator class="space-y">
            <div class="mt-3">
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
                <x-button label="Annuler" />
                <x-button label="Sauvergarder" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-card>

        </x-form>
    </div>
</div>
