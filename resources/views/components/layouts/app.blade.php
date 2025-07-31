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
    <x-nav sticky full-width>
 
        <x-slot:brand>
            <label for="main-drawer" class="lg:hidden mr-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
            <x-app-brand class="px-5 pt-4" />
        </x-slot:brand>

        <x-slot:actions>
            <x-button label="Messages" icon="o-envelope" link="###" class="btn-ghost btn-sm" responsive />
            <x-button label="Notifications" icon="o-bell" link="###" class="btn-ghost btn-sm" responsive />
        </x-slot:actions>
    </x-nav>

    <x-main full-width>
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            {{-- BRAND --}}
            {{-- <x-app-brand class="px-5 pt-4" /> --}}

            {{-- MENU --}}
            <x-menu activate-by-route>

                {{-- User --}}
                @if($user)

                    <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                        <x-slot:actions>
                            <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="logoff" no-wire-navigate link="/logout" />
                        </x-slot:actions>
                    </x-list-item>

                    <x-menu-separator />
                @endif

                <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" />

                {{-- <x-menu-item title="Hello" icon="o-sparkles" link="/" /> --}}
                
                {{-- <x-menu-sub title="Administrations" icon="o-cog-6-tooth">
                    <x-menu-item title="Tous les profiles" icon="o-key" link="/administrations/profils" />
                    <x-menu-item title="Creation profile" icon="o-user" link="/administrations/profil/create" />
                </x-menu-sub> --}}

                <x-menu-separator />
                <x-menu-item title="Profil" icon="o-cog-6-tooth" link="/administrations/profils" />
                {{-- <x-menu-item title="Profil" icon="o-user" link="/administrations/profil/create" /> --}}
                <x-menu-item title="Utilisateurs" icon="o-users" link="##" />

                <x-menu-separator />
                <x-menu-item title="Parkod" icon="o-calculator" link="##" />
                <x-menu-item title="Marque" icon="o-tag" link="##" />
                <x-menu-item title="Produit" icon="o-shopping-bag" link="##" />

                <x-menu-separator />
                <x-menu-item title="Fournisseur" icon="o-users" link="##" />
                <x-menu-item title="Produit Fournisseur" icon="o-shopping-bag" link="##" />

                <x-menu-separator />
                <x-menu-item title="Magasin" icon="o-users" link="##" />

                <x-menu-separator text="Gestion Approvisionnement" />
                <x-menu-item title="Commande" icon="o-users" link="##" />


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
