<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
        <x-header title="Dashboard">
            <x-slot:actions>
                <x-theme-toggle class="btn btn-circle btn-ghost" />
            </x-slot:actions>
        </x-header>
        <div class="space-y-4">
            <section id="stats" aria-labelledby="stats" class="grid grid-cols-6 gap-4">
                <x-stat title="Produits" value="13" icon="o-circle-stack" class="col-span-2"/>
                <x-stat title="Out of stock" value="6" icon="o-server" class="col-span-2"/>
                <x-stat title="Coomande" value="150" icon="o-briefcase" class="col-span-2" />
                <x-stat title="Customers" value="4" icon="o-user-group" class="col-span-2"/>
                <x-stat title="Applications" value="2" icon="o-cube" class="col-span-2"/>
                <x-stat title="Ventes" value="340" icon="o-currency-euro" class="col-span-2"/>
            </section>
            {{-- <livewire:tenants.stats />
            <livewire:tenants.department-list /> --}}
        </div>
</div>
