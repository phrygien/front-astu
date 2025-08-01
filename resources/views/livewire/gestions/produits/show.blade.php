<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="max-w-4xl mx-auto">

    <x-header title="Details" subtitle="Details du produit" separator>
    </x-header>

    <x-form wire:submit="save">

        <x-card subtitle="Basic information" separator progress-indicator class="space-y">

            <div class="space-y-3">

            <dl class="flex flex-col sm:flex-row gap-1">
                <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Designation :</span>
                </dt>
                <dd>
                <ul>
                    <li class="me-1 after:content-[''] inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                     Figma
                    </li>
                </ul>
                </dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-1">
                <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Designation Variant :</span>
                </dt>
                <dd>
                <ul>
                    <li class="me-1 after:content-[''] inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                     la valeur de designation variant vas afficher ici
                    </li>
                </ul>
                </dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-1">
                <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Article :</span>
                </dt>
                <dd>
                <ul>
                    <li class="me-1 after:content-[''] inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                     afficher la valeur de l'article ici
                    </li>
                </ul>
                </dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-1">
                <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">ref_fabri_n_1 :</span>
                </dt>
                <dd>
                <ul>
                    <li class="me-1 after:content-[''] inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                     valeur de reference fabri doivent afficher ici
                    </li>
                </ul>
                </dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-1">
                <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">EAN :</span>
                </dt>
                <dd>
                <ul>
                    <li class="me-1 after:content-[''] inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                     EAN valeur affiche ici apres
                    </li>
                </ul>
                </dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-1">
                <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">pght_parkod :</span>
                </dt>
                <dd>
                <ul>
                    <li class="me-1 after:content-[''] inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                     pght_parkod value ici
                    </li>
                </ul>
                </dd>
            </dl>


            <dl class="flex flex-col sm:flex-row gap-1">
                <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">TVA :</span>
                </dt>
                <dd>
                <ul>
                    <li class="me-1 after:content-[''] inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                     tva value was display here
                    </li>
                </ul>
                </dd>
            </dl>

            <dl class="flex flex-col sm:flex-row gap-1">
                <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">Devise:</span>
                </dt>
                <dd>
                <ul>
                    <li class="me-1 after:content-[''] inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                     EUR
                    </li>
                </ul>
                </dd>
            </dl>


            <dl class="flex flex-col sm:flex-row gap-1">
                <dt class="min-w-40">
                <span class="block text-sm text-gray-500 dark:text-neutral-500">hs_code:</span>
                </dt>
                <dd>
                <ul>
                    <li class="me-1 after:content-[''] inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                     code_hs 54545456
                    </li>
                </ul>
                </dd>
            </dl>


            </div>

        </x-card>

        <x-card subtitle="Information sur le code" separator progress-indicator class="space-y mt-3">

        
            <div class="overflow-x-auto">
                <table class="table">
                    <!-- head -->
                    <thead>
                    <tr>
                        <th>Code produit</th>
                        <th>Code Marque</th>
                        <th>Code Categorie</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>1</th>
                        <td>Cy Ganderton</td>
                        <td>Quality Control Specialist</td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </x-card>


    </x-form>
</div>

