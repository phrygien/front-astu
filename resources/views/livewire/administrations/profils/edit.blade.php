<?php

use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    use Toast;

    public $tree = [];
    public $openModule = null;
    public $token;

    public $profileName = '';
    public $profileStatus = 1; // 1 = Actif, 0 = Inactif
    public $selectedActions = [];
    public $profileId;

    public function mount($id = null)
    {
        $this->token = session('token');

        if ($id) {
             $this->profileId = $id;
            $this->loadProfile($id);
        } else {
            $this->fetchTree();
        }
    }

    protected function rules()
    {
        return [
            'profileName' => 'required|min:5',
        ];
    }

    public function fetchTree()
    {
        $response = Http::withToken($this->token)
            ->get('http://dev.astucom.com:9038/erpservice/api/admin/listactionwithtree');

        if ($response->ok()) {
            $this->tree = $response['data'];
        } else {
            $this->tree = [];
        }
    }

    public function loadProfile($id)
    {
        $response = Http::withToken($this->token)
            ->get("http://dev.astucom.com:9038/erpservice/api/admin/profil/{$id}");

        if ($response->ok() && !$response['error']) {
            $data = $response['data']['profil'];
            $this->profileName = $data['name'];
            $this->profileStatus = $data['state'];
            $this->tree = $data['action_result'];

            // Pré-cocher les actions où "can" est true
            $this->selectedActions = [];

            foreach ($this->tree as $module) {
                foreach ($module['sub_module'] as $sub) {
                    foreach ($sub['actions'] as $action) {
                        if ($action['can']) {
                            $this->selectedActions[] = $action['code'];
                        }
                    }
                }
            }

            $this->selectedActions = array_unique($this->selectedActions);
        } else {
            $this->tree = [];
            $this->selectedActions = [];
            $this->danger('Erreur lors du chargement du profil.');
        }
    }

    public function toggleModule($index)
    {
        $this->openModule = $this->openModule === $index ? null : $index;
    }

    public function toggleAction($code)
    {
        if (in_array($code, $this->selectedActions)) {
            $this->selectedActions = array_diff($this->selectedActions, [$code]);
        } else {
            $this->selectedActions[] = $code;
        }
    }

    public function saveProfile()
    {
        $this->validate();

        $payload = [
            'name' => $this->profileName,
            'code_actions' => array_values($this->selectedActions),
        ];

        $response = Http::withToken($this->token)
            ->put("http://dev.astucom.com:9038/erpservice/api/admin/profil/{$this->profileId}", $payload);

        if ($response->ok() && !$response['error']) {
            $this->success(
                'Modification sauvegardée avec succès',
                '',
                redirectTo: '/administrations/profils'
            );
        } else {
            $this->warning('Sellectioner au moin un permission.');
        }
    }

    public function with()
    {
        return [
            'tree' => $this->tree,
            'openModule' => $this->openModule,
            'selectedActions' => $this->selectedActions,
        ];
    }
};

?>

<div class="p-8">
    <div class="max-w-4xl mx-auto">
    <x-form wire:submit="saveProfile">
        <x-header title="Modification d'un profil" subtitle="Modifier les information pour ce profile" separator>

            <x-slot:actions>
                <x-button label="Annuler" class="border-2 border-gray-300 rounded-md shadow-sm" link="/administrations/profils" />
                <x-button label="Sauvegarder" class="btn-primary" type="submit" spinner="saveProfile" />
            </x-slot:actions>
        </x-header>

        <x-card subtitle="Basic information sur le profil" separator progress-indicator>
                <x-input label="Nom du profile" wire:model.live="profileName" />
        
        </x-card>

        <x-card subtitle="Selectionner les actions pour ce profil" separator class="mt-3">
                            <div class="flex flex-col gap-4 text-on-surface dark:text-on-surface-dark">
                    @foreach($tree as $i => $module)
                        <div x-data="{ isExpanded: {{ $openModule === $i ? 'true' : 'false' }} }"
                            class="overflow-hidden bg-neutral-50/40 dark:border-neutral-700 dark:bg-neutral-900/50 border-2 border-gray-300 rounded-md shadow-sm">
                            <button type="button"
                                    class="flex w-full items-center justify-between gap-2 bg-neutral-50 p-4 text-left underline-offset-2 hover:bg-neutral-50/75 focus-visible:bg-neutral-50/75 focus-visible:underline focus-visible:outline-hidden dark:bg-neutral-900 dark:hover:bg-neutral-900/75 dark:focus-visible:bg-neutral-900/75" aria-controls="accordionItemOne" x-on:click="isExpanded = ! isExpanded" x-bind:class="isExpanded ? 'text-onSurfaceStrong dark:text-onSurfaceDarkStrong font-bold'  : 'text-onSurface dark:text-onSurfaceDark font-medium'"
                                    x-on:click="$wire.toggleModule({{ $i }})"
                                    x-bind:aria-expanded="isExpanded ? 'true' : 'false'"
                                    x-bind:class="isExpanded ? 'font-bold' : 'font-medium'">
                                {{ $module['module_name'] }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 transition"
                                    x-bind:class="isExpanded ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </button>

                            <div x-cloak x-show="isExpanded" x-collapse>
                                <div class="p-4 space-y-4">
                                    @foreach($module['sub_module'] as $sub)
                                        <div>
                                            <div class="text-base font-semibold mb-2">
                                                {{ $sub['sub_module_name'] }}
                                            </div>
                                            <div class="pl-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @foreach($sub['actions'] as $action)
                                                    <label class="flex items-center gap-2">
                                                        {{-- <input type="checkbox"  class="checkbox" wire:click="toggleAction('{{ $action['code'] }}')"
                                                            @checked(in_array($action['code'], $selectedActions)) /> --}}
                                                            <input type="checkbox" class="checkbox" wire:click="toggleAction('{{ $action['code'] }}')"
                                                            @checked(in_array($action['code'], $selectedActions)) />

                                                        {{ $action['name'] }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
            <x-slot:actions>
                <x-button label="Annuler" class="border-2 border-gray-300 rounded-md shadow-sm" link="/administrations/profils" />
                <x-button label="Sauvegarder" class="btn-primary" type="submit" spinner="saveProfile" />
            </x-slot:actions>
        </x-card>

        </x-form>
    </div>
</div>