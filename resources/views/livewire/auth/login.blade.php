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

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company" class="mx-auto h-10 w-auto" />
      <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Sign in to your account</h2>

      @if ($errorMessage)
      <div role="alert" class="alert alert-error mt-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ $errorMessage}}</span>
        </div>
    @endif

    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <x-form wire:submit="login">
            <x-input label="Email" wire:model.loading="email" />
            <x-password label="Mot de passe" hint="It toggles visibility" wire:model.loading="password" clearable />
         
            <x-slot:actions>
                <x-button label="Login" class="btn-primary" type="submit" spinner="login" />
            </x-slot:actions>
        </x-form>
  
    </div>
  </div>
