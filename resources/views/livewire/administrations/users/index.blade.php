<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <x-header title="Utilisateurs" subtitle="Gerer les utilisateurs ASTUPARF" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-bolt" placeholder="Chercher profil..." />
        </x-slot:middle>
        <x-slot:actions>
            <div class="inline-flex gap-x-2">
                <button class="join-item btn">1</button>
                <button class="join-item btn">2</button>
                <button class="join-item btn btn-disabled">...</button>
                <button class="join-item btn">99</button>
                <button class="join-item btn">100</button>
            </div>
            <x-button icon="o-plus-circle" class="btn-primary" link="profil/create" />
        </x-slot:actions>
    </x-header>


<div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
  <table class="table">
    <!-- head -->
    <thead>
      <tr>
        <th></th>
        <th>Name</th>
        <th>Job</th>
        <th>Favorite Color</th>
      </tr>
    </thead>
    <tbody>
      <!-- row 1 -->
      <tr>
        <th>1</th>
        <td>Cy Ganderton</td>
        <td>Quality Control Specialist</td>
        <td>Blue</td>
      </tr>
      <!-- row 2 -->
      <tr>
        <th>2</th>
        <td>Hart Hagerty</td>
        <td>Desktop Support Technician</td>
        <td>Purple</td>
      </tr>
      <!-- row 3 -->
      <tr>
        <th>3</th>
        <td>Brice Swyre</td>
        <td>Tax Accountant</td>
        <td>Red</td>
      </tr>
    </tbody>
  </table>
</div>

    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
    <div>
        <flux:input icon="magnifying-glass" placeholder="Chercher profil" />
    </div>

        <div class="inline-flex gap-x-2">
            <button class="join-item btn">1</button>
            <button class="join-item btn">2</button>
            <button class="join-item btn btn-disabled">...</button>
            <button class="join-item btn">99</button>
            <button class="join-item btn">100</button>
        </div>
</div>
</div>
