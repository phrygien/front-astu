<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public array $product = [];

    public function mount(int $id): void
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get("http://dev.astucom.com:9038/erpservice/api/product/produit/{$id}");

        if ($response->ok() && !$response['error']) {
            $this->product = $response['data'];
        } else {
            $this->product = [];
        }
    }

    public function with(): array
    {
        return [
            'product' => $this->product,
        ];
    }
};

?>

<div class="max-w-4xl mx-auto">

    <x-header title="Details" subtitle="Details du produit" separator>
        <x-slot:actions>
        <div class="breadcrumbs text-sm">
            <ul>
                <li><a href="{{ route('produits.index') }}" wire:navigate>Produit</a></li>
                <li>...</li>
                <li class="text-pink-800">{{ $product['designation'] }}</li>
            </ul>
        </div>
        </x-slot:actions>
    </x-header>

    <x-card subtitle="Basic information" separator progress-indicator class="space-y">

        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100 mt-3 mb-3">
            <table class="table">
                <!-- head -->
                <thead>
                <tr>
                    <th class="text-end">Code produit</th>
                    <th class="text-end">Code Marque</th>
                    <th class="text-end">Code Categorie</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th class="text-end">{{ $product['product_code'] ?? '-'}}</th>
                    <td class="text-end">{{ $product['marque_code'] ?? '-'}}</td>
                    <td class="text-end">{{ $product['categorie_code'] ?? '-'}}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="space-y-3">

        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-80">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Designation :</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ $product['designation'] ?? '-' }}
                    </li>
                </ul>
            </dd>
        </dl>


        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-80">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Designation Variant:</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ $product['designation_variant'] ?? '-' }}
                    </li>
                </ul>
            </dd>
        </dl>



        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-80">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Article :</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ $product['article'] ?? '-' }}
                    </li>
                </ul>
            </dd>
        </dl>


        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-80">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Reference de frabrication (PARKOD) :</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ $product['ref_fabri_n_1'] ?? '-' }}
                    </li>
                </ul>
            </dd>
        </dl>

        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-80">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">EAN :</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ $product['EAN'] ?? '-' }}
                    </li>
                </ul>
            </dd>
        </dl>


        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-80">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Prix de gros HT ( PARKOD ) :</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ $product['pght_parkod'] ?? '-' }}
                    </li>
                </ul>
            </dd>
        </dl>

        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">TVA ( 1 = 20 %):</span> 
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ $product['tva'] ?? '-' }}
                    </li>
                </ul>
            </dd>
        </dl>


        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Devise :</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ $product['devise'] ?? '-' }}
                    </li>
                </ul>
            </dd>
        </dl>

        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Statut PARKOD :</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ $product['statut_parkod'] ?? '-' }}
                    </li>
                </ul>
            </dd>
        </dl>

        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Date de creation :</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                        {{ \Carbon\Carbon::parse($product['created_at'])->locale('fr')->isoFormat('LL') }}
                    </li>
                </ul>
            </dd>
        </dl>


        <dl class="flex flex-col sm:flex-row gap-1 items-start sm:items-center justify-between">
            <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Dernier modification :</span>
            </dt>
            <dd class="w-full text-right">
                <ul>
                    <li class="inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                       {{ \Carbon\Carbon::parse($product['updated_at'])->locale('fr')->isoFormat('LL') }}

                    </li>
                </ul>
            </dd>
        </dl>


        </div>
    </x-card>

</div>

