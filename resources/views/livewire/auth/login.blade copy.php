<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\{Layout, Title};
use Illuminate\Auth\Events\Lockout;

new 
#[Layout('components.layouts.guest')]
#[Title('Login')]
class extends Component {
    
    #[Validate('required|string|email')]
    public string $email;

    #[Validate('required|string')]
    public string $password;

    public bool $remember = false;

    public $errorMessage = '';

    /**
     * Handle an incoming authentication request.
     */
    public function login()
    {
        $this->validate();

        $response = Http::post('http://dev.astucom.com:9038/erpservice/api/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $data = $response->json();
        if ($data['code'] != 200) {
            $this->errorMessage = $data['data']['error'] ?? 'Erreur de connexion.';
            return;
        }

        session(['token' => $data['data']['access_token']]);

       $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

        //$this->redirectRoute('dashboard');
    }


    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

}; ?>

<div class="flex h-screen w-screen">
    <!-- Colonne gauche : Image -->
    <div class="flex-1 hidden lg:flex">
        <div class="relative h-full w-full bg-zinc-900 text-white flex flex-col justify-end items-start p-16"
             style="background-image: url('/img/demo/auth_aurora_2x.png'); background-size: cover; background-position: center;">
             
            <div class="flex gap-2 mb-4 text-yellow-400">
                @for ($i = 0; $i < 5; $i++)
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.564-.955L10 0l2.948 5.955 6.564.955-4.756 4.635 1.122 6.545z" />
                    </svg>
                @endfor
            </div>

            <blockquote class="mb-6 italic font-light text-2xl xl:text-3xl">
                “Flux has enabled me to design, build, and deliver apps faster than ever before.”
            </blockquote>

            <div class="flex items-center gap-4">
                <!-- SVG flacon de parfum -->
                <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center">
                    <svg class="w-8 h-8 text-zinc-800" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 2a1 1 0 0 0-1 1v1H7a1 1 0 0 0 0 2h10a1 1 0 1 0 0-2h-1V3a1 1 0 0 0-1-1H9zM7 8a1 1 0 0 0-1 1v1.25A6.75 6.75 0 0 0 12 21a6.75 6.75 0 0 0 6-10.75V9a1 1 0 0 0-1-1H7zm1 2h8v.5a4.75 4.75 0 1 1-8 0V10z"/>
                    </svg>
                </div>

                <!-- Texte -->
                <div>
                    <div class="text-lg font-medium">Parfum Élégant</div>
                    <div class="text-sm text-zinc-300">Fragrance Design</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Colonne droite : Formulaire -->
    <div class="flex-1 flex justify-center items-center bg-white">
        <div class="w-96 max-w-full space-y-6 px-6">
            <!-- Logo -->
            <div class="flex justify-center opacity-50">
                <a href="/" class="group flex items-center gap-3">
                    <svg class="h-4 text-zinc-800" viewBox="0 0 18 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <line x1="1" y1="5" x2="1" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <line x1="5" y1="1" x2="5" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <line x1="9" y1="5" x2="9" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <line x1="13" y1="1" x2="13" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <line x1="17" y1="5" x2="17" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </g>
                    </svg>
                    <span class="text-xl font-semibold text-zinc-800">astuparf</span>
                </a>
            </div>

            <h2 class="text-center text-2xl font-bold text-gray-900">Bienvenue à nouveau</h2>

            @if ($errorMessage)
                <div class="bg-red-100 text-red-700 text-sm px-4 py-2 rounded">
                    {{ $errorMessage }}
                </div>
            @endif

            <x-form wire:submit="login">
                <x-input label="Email" wire:model.live="email" placeholder="" icon="o-user" hint="Votre adresse email" />

                <x-password label="Mot de passe" wire:model.lazy="password" placeholder="" clearable hint="Votre mot de passe" />

                <x-slot:actions>
                    <x-button label="Log in" class="w-full mt-4 btn-primary" type="submit" spinner="login" />
                </x-slot:actions>
            </x-form>
        </div>
    </div>
</div>

