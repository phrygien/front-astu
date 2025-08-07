<?php

use Livewire\Volt\Component;

new class extends Component {
    public array $produitsSelectionnes = [];

    public function mount()
    {
        $this->produitsSelectionnes = session('produits_selectionnes', []);
    }

}; ?>

<div>
    <table class="table">
    <thead>
        <tr>
            <th>Code</th>
            <th>Désignation</th>
            <th>EAN</th>
            <th>État</th>
        </tr>
    </thead>
    <tbody>
        @forelse($produitsSelectionnes as $produit)
            <tr>
                <td>{{ $produit['product_code'] }}</td>
                <td>{{ $produit['designation'] }}</td>
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
                <td colspan="4">Aucun produit sélectionné.</td>
            </tr>
        @endforelse
    </tbody>
</table>

</div>
