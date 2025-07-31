@php
    $user = null;
    try {
        $token = session('token');
        if ($token) {
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->post('http://dev.astucom.com:9038/erpservice/api/profile');

            if ($response->ok()) {
                $user = $response->json('data');
            }
        }
    } catch (\Exception $e) {
        $user = null;
    }

    function initials($name) {
        return strtoupper(collect(explode(' ', $name))->map(fn($w) => substr($w, 0, 1))->join(''));
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            {{-- BRAND --}}
            <x-app-brand class="px-5 pt-4" />

            {{-- MENU --}}
            <x-menu activate-by-route>

                {{-- User --}}
                @if($user)
                    <x-menu-separator />

                    <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                        <x-slot:actions>
                            <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="logoff" no-wire-navigate link="/logout" />
                        </x-slot:actions>
                    </x-list-item>

                    <x-menu-separator />
                @endif

                <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" />

                {{-- <x-menu-item title="Hello" icon="o-sparkles" link="/" /> --}}
                
                <x-menu-sub title="Administrations" icon="o-cog-6-tooth">
                    <x-menu-item title="Tous les profiles" icon="o-key" link="/administrations/profils" />
                    <x-menu-item title="Creation profile" icon="o-user" link="/administrations/profil/create" />
                </x-menu-sub>
            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
</body>
</html>
