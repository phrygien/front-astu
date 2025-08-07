<?php

use Mary\Traits\Toast;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    
    use Toast;

    #[Validate('required', message: 'Code du fournisseur obligatoire')]
    #[Validate('min:3', message: 'Le champ CODE doit contenir 3 caractères maximum')]
    #[Validate('max:3', message: 'Le champ CODE doit contenir 3 caractères maximum')]
    public string $code = '';

    #[Validate('required', message: 'Nom du fournisseur obligatoire')]
    public string $name = '';

    #[Validate('required', message: 'La raison social du fournisseur obligatoire')]
    public string $raison_social = '';

    #[Validate('required', message: 'Adresse siege du fournisseur obligatoire')]
    public string $adresse_siege = '';

    #[Validate('required', message: 'Code postal obligatoire')]
    public string $code_postal = '';

    #[Validate('required', message: 'Ville du fournisseur obligatoire')]
    public string $ville = '';

    #[Validate('required', message: 'Le telephone du fournisseur obligatoire')]
    public string $telephone = '';

    #[Validate('nullable', message: 'Fax du fournisseur obligatoire')]
    public string $fax = '';

    #[Validate('required', message: 'Adresse mail du fournisseur obligatoire')]
    public string $mail = '';

    #[Validate('required', message: 'Adresse retour du fournisseur obligatoire')]
    public string $adresse_retour = '';

    #[Validate('required', message: 'Code postal du fournisseur obligatoire')]
    public string $code_postal_retour = '';

    #[Validate('required', message: 'Ville retour du fournisseur obligatoire')]
    public string $ville_retour = '';


    public $token;


    public function mount(): void
    {
        $this->token = session('token');
    }

    public function save()
    {
        $this->validate();

        $payload = [
            'code' => $this->code,
            'name' => $this->name,
            'raison_social' => $this->raison_social,
            'adresse_siege' => $this->adresse_siege,
            'code_postal' => $this->code_postal,
            'ville' => $this->ville,
            'telephone' => $this->telephone,
            'fax' => $this->fax,
            'mail' => $this->mail,
            'adresse_retour' => $this->adresse_retour,
            'code_postal_retour' => $this->code_postal_retour,
            'ville_retour' => $this->ville_retour,
            'date_creation' =>  now()->format('Y-m-d')

        ];

        $response = Http::withToken($this->token)
            ->post(config('services.jwt.profile_endpoint') . '/fournisseur/fournisseur', $payload);

        if ($response->ok() && !$response['error']) {
            $this->success(
                'Fournisseur sauvegarder avec succees',
                redirectTo: '/gestion/fournisseurs'
            );
            $this->reset(['code', 'name']);
        } else {
            $this->warning('Erreur lors de la sauvegarde du fournisseur.');
        }

    }
}; ?>

<div class="max-w-4xl mx-auto">
        <x-form wire:submit="save">
    <x-header title="Fournisseur" subtitle="Formulaire de création de fournisseur" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Annuler" link="/gestion/fournisseurs" class="btn-sm btn-active" />
            <x-button label="Sauvegarder" class="btn-primary btn-sm" type="submit" spinner="save" />
        </x-slot:actions>
    </x-header>
    

            <div class="lg:grid grid-cols-5">
                <div class="col-span-2">
                    <x-header title="Basic" subtitle="Basic information concernant le fournisseur" size="text-lg" />
                </div>
                <div class="col-span-3 grid gap-3">
                    <x-card shadow separator>
                        <x-input label="Nom" wire:model="name" icon="o-user" />
                        <x-input label="Code" wire:model="code" icon="o-finger-print" />
                        <x-input label="Raison social" wire:model="raison_social" icon="o-information-circle" />
                    </x-card>
                </div>
            </div>
    
            <hr class="my-5 border-base-300" />
    
            <div class="lg:grid grid-cols-5">
                <div class="col-span-2">
                    <x-header title="Contact" subtitle="Renseigner les contact du fournisseur" size="text-lg" />
                </div>
                <div class="col-span-3 grid gap-3">
                    <x-card shadow separator>
                        <x-input label="Telephone" wire:model="telephone" icon="o-phone" />
                        <x-input label="Mail" wire:model="mail" icon="o-at-symbol" />
                        <x-input label="Fax" wire:model="fax" icon="o-bolt" />
                    </x-card>
                </div>
            </div>


            <hr class="my-5 border-base-300" />
    
            <div class="lg:grid grid-cols-5">
                <div class="col-span-2">
                    <x-header title="Adresse" subtitle="Renseigner l'adresse du fournisseur" size="text-lg" />
                </div>
                <div class="col-span-3 grid gap-3">
                    <x-card shadow separator>
                        <x-input label="Ville" wire:model="ville" icon="o-map" />
                        <x-input label="Code Postal" wire:model="code_postal" icon="o-information-circle" />
                        <x-input label="Adresse siege" wire:model="adresse_siege" icon="o-map-pin" />
                        <x-input label="Ville retour" wire:model="ville_retour" icon="o-map" />
                        <x-input label="Code Postal retour" wire:model="code_postal_retour" icon="o-information-circle" />
                        <x-input label="Adresse retour" wire:model="adresse_retour" icon="o-map-pin" />
                    </x-card>
                </div>
            </div>
    
            <x-slot:actions>
                <x-button label="Annuler" link="/gestion/fournisseurs" class="btn-sm btn-active" />
                <x-button label="Sauvegarder" class="btn-primary btn-sm" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>

</div>
