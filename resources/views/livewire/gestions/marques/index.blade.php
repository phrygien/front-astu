<?php

use Mary\Traits\Toast;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    use Toast;

    public bool $myModal1 = false;

    #[Validate('required', message: 'Code marque obligatoire')]
    #[Validate('min:3', message: 'Le champ CODE doit contenir 3 caractères maximum')]
    #[Validate('max:3', message: 'Le champ CODE doit contenir 3 caractères maximum')]
    public string $code = '';

    #[Validate('required', message: 'Libelle marque obligatoire')]
    public string $name = '';

    public $token;

    public function mount(): void {
        $this->token = session('token');
    }

    public function save()
    {
        $this->validate();

        $payload = [
            'code' => $this->code,
            'name' => $this->name
        ];

        $response = Http::withToken($this->token)
            ->post(config('services.jwt.profile_endpoint') . '/product/marque', $payload);

        if ($response->ok() && !$response['error']) {
            $this->success('Marque sauvegardé avec succès');
            $this->reset(['code', 'name']);
            $this->selectedActions = [];
        } else {
            $this->error('Erreur lors de la sauvegarde du marque.');
        }

    }

}; ?>

<div>

    <div class="max-w-7xl mx-auto">
    <x-header title="Marque" subtitle="Tous les marques" separator progress-indicator>
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

            {{-- <div class="inline-flex gap-x-2">
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
             --}}
            <x-button icon="o-cloud-arrow-down" class="btn-primary btn-sm" @click="$wire.myModal1 = true" label="Créer une marque" />
            
        </x-slot:actions>
    </x-header>

        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table">
            <!-- head -->
            <thead>
            <tr>
                <th>CODE</th>
                <th>NAME</th>
                <th>STATUT</th>
                <th>CREE LE</th>
                <th>ACTION</th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <th>1</th>
                <td>Cy Ganderton</td>
                <td>Quality Control Specialist</td>
                <td>Blue</td>
            </tr>

            </tbody>
        </table>
        </div>

    </div>




    <x-modal wire:model="myModal1" title="Création de marque" class="backdrop-blur">

        <x-form wire:submit="save">
            <x-input label="Code Marque" wire:model="code" hint="Exemple: 001" />
            <x-input label="Libelle" wire:model="name" placeholder="" />
        
            <x-slot:actions>
                <x-button label="Annuler" @click="$wire.myModal1 = false" />
                <x-button label="Sauvegarder" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

</div>
