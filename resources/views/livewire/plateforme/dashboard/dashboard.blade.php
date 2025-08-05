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
            <section id="stats" aria-labelledby="stats" class="grid grid-cols-1 lg:grid-cols-6 gap-4">
                <x-stat title="Produits" value="13" icon="o-circle-stack" class="col-span-2" color="text-primary" />
                <x-stat title="Out of stock" value="6" icon="o-server" class="col-span-2" color="text-primary" />
                <x-stat title="Coomande" value="150" icon="o-briefcase" class="col-span-2" color="text-primary" />
                <x-stat title="Customers" value="4" icon="o-user-group" class="col-span-2" color="text-primary" />
                <x-stat title="Applications" value="2" icon="o-cube" class="col-span-2" color="text-primary" />
                <x-stat title="Ventes" value="340" icon="o-currency-euro" class="col-span-2" color="text-primary" />
            </section>
        </div>

        <x-card title="Commande" subtitle="Always triggers" separator class="mt-10 py-10">

        </x-card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <x-card title="Your stats" subtitle="Always triggers" separator >

            </x-card>
            
            <x-card title="Your stats" subtitle="Only triggers with `save2`" separator>

            </x-card>
        </div>
</div>
