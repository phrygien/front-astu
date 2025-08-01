<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Http;

new class extends Component {
    
    use WithFileUploads;
    use Toast;

    public $file;
    public $token;

    public function mount(): void
    {
        $this->token = session('token');
    }

    public function save()
    {
        if (!$this->file) {
            $this->warning('Aucun fichier sélectionné.');
            return;
        }

        try {
            $fileContents = file_get_contents($this->file->getRealPath());

            $base64File = base64_encode($fileContents);

            $mimeType = $this->file->getMimeType();
            $encodedFile = "data:{$mimeType};base64,{$base64File}";
            //$encodedFile = "data:{data:text\/csv;base64,{$base64File}";


            $response = Http::withToken($this->token)->post('http://dev.astucom.com:9038/erpservice/api/product/parkod_upload', [
                'file' => $encodedFile,
            ]);

        
            if ($response->successful()) {
                $this->success('Fichier PARKOD chargé avec succès.');
            } else {
                $this->success('Échec de l\'envoi : ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->warning('Erreur lors du chargement : ' . $e->getMessage());
        }
    }


}; ?>

<div class="max-w-4xl mx-auto">

    <x-header title="PARKOD" subtitle="Charger le fichier PARKOD pour ASTUPARF" separator>
    </x-header>

    <x-form wire:submit="save">

        <x-card subtitle="Fichier parkod" separator progress-indicator class="space-y">

        
            <div class="col-span-full">
                {{-- <label for="cover-photo" class="block text-sm/6 font-medium text-gray-900">PARKOD File</label> --}}
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                    <div class="text-center">
                    <div class="mt-4 flex text-sm/6 text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-pink-600 focus-within:ring-2 focus-within:ring-pink-600 focus-within:ring-offset-2 focus-within:outline-hidden hover:text-pink-500">
                        <span>Uploader le fichier</span>
                        <input id="file-upload" type="file" name="file" class="sr-only" wire:model="file" />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs/5 text-gray-600">TXT up to 10MB</p>
                    </div>
                </div>
            </div>


            <x-slot:actions>
                <x-button label="Annuler" />
                <x-button label="Charger PARKOD" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-card>
    </x-form>
</div>
