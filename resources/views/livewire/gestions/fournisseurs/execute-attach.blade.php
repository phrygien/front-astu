<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public array $produitsSelectionnes = [];
    public int $fournisseurId = 0;
    public ?array $fournisseur = null;

    public array $prix = [];
    public array $tax = [];

    public function mount(): void
    {
        $this->produitsSelectionnes = collect(session('produits_selectionnes', []))
            ->filter(fn($item) => is_array($item) && isset($item['id']))
            ->values()
            ->all();

        $this->fournisseurId = session('fournisseur_id', 0);

        // Initialisation des valeurs par défaut
        foreach ($this->produitsSelectionnes as $produit) {
            $this->prix[$produit['id']] = "0.00";
            $this->tax[$produit['id']] = 0.2;
        }

        $this->loadFournisseur();
    }

    public function loadFournisseur(): void
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get(config('services.jwt.profile_endpoint') . '/fournisseur/fournisseur/' . $this->fournisseurId);

        if ($response->ok() && !$response['error']) {
            $this->fournisseur = $response['data'];
        }
    }

    public function envoyerProduits(): void
    {
        $token = session('token');

        $produitList = collect($this->produitsSelectionnes)->map(function ($produit) {
            $id = $produit['id'];

            return [
                'product_id' => $id,
                'prix_fournisseur_ht' => $this->prix[$id] ?? '0.00',
                'tax' => $this->tax[$id] ?? 0.2,
            ];
        })->values()->all();

        $body = [
            'fournisseur_id' => (string) $this->fournisseurId,
            'produit_list' => $produitList,
        ];

        $response = Http::withToken($token)
            ->post('http://dev.astucom.com:9038/erpservice/api/fournisseur/produitfournisseur', $body);

        if ($response->ok()) {
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Produits affectés au fournisseur avec succès !',
            ]);

            // Optionnel : vider la session
            session()->forget(['produits_selectionnes', 'fournisseur_id']);

            // Redirection après succès
            $this->redirect('/gestion/fournisseurs');
        } else {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Échec de l\'envoi',
                'description' => $response->json('message') ?? 'Erreur inconnue',
            ]);
        }
    }
};

?>

<div class="w-full mx-auto">
    <x-header title="Affectation produit fournisseur" subtitle="Valider les produits sélectionnés" separator>
        <x-slot:actions>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><a href="{{ route('fournisseurs.index') }}" wire:navigate>Fournisseurs</a></li>
                    <li>...</li>
                    <li class="text-pink-800">
                        {{ $fournisseur['name'] ?? 'Fournisseur inconnu' }}
                    </li>
                </ul>
            </div>
        </x-slot:actions>
    </x-header>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100 mt-4">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>EAN</th>
                    <th>Code Produit</th>
                    <th>Catégorie</th>
                    <th>Marque</th>
                    <th>Désignation</th>
                    <th>Prix HT</th>
                    <th>Taxe</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produitsSelectionnes as $produit)
                    <tr>
                        <td>{{ $produit['id'] }}</td>
                        <td>{{ $produit['EAN'] }}</td>
                        <td>{{ $produit['product_code'] }}</td>
                        <td>{{ $produit['categorie_code'] }}</td>
                        <td>{{ $produit['marque_code'] }}</td>
                        <td>{{ $produit['designation'] }}</td>
                        <td>
                            <x-input
                                type="text"
                                class="input-sm w-28"
                                wire:model.defer="prix.{{ $produit['id'] }}"
                                label="Prix"
                                placeholder="0.00"
                            />
                        </td>
                        <td>
                            <x-input
                                type="text"
                                class="input-sm w-20"
                                wire:model.defer="tax.{{ $produit['id'] }}"
                                label="TVA"
                                placeholder="0.2"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500">Aucun produit sélectionné.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-end">
        <x-button
            label="Valider"
            class="btn-primary"
            wire:click="envoyerProduits"
        />
    </div>
</div>


{{-- <div class="p-4">
    <h2 class="text-xl font-semibold mb-4">Produits sélectionnés</h2>

    <table class="table w-full">
        <thead>
            <tr>
                <th>Code</th>
                <th>Catégorie</th>
                <th>Marque</th>
                <th>Désignation</th>
                <th>Variante</th>
                <th>Réf. Fabriquant</th>
                <th>EAN</th>
                <th>État</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produitsSelectionnes as $produit)
                <tr>
                    <td>{{ $produit['product_code'] }}</td>
                    <td>{{ $produit['categorie_code'] }}</td>
                    <td>{{ $produit['marque_code'] }}</td>
                    <td>{{ $produit['designation'] }}</td>
                    <td>{{ $produit['designation_variant'] }}</td>
                    <td>{{ $produit['ref_fabri_n_1'] }}</td>
                    <td>{{ $produit['EAN'] }}</td>
                    <td>
                        @if ($produit['state'] == 1)
                            <span class="badge badge-success">Actif</span>
                        @else
                            <span class="badge badge-error">Inactif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-gray-500">Aucun produit sélectionné.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div> --}}

