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
        return strtoupper(
            collect(explode(' ', $name))
                ->filter()
                ->map(fn($word) => substr($word, 0, 1))
                ->take(2)
                ->join('')
        );
    }

@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">

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
            
            <div class="flex gap-2">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button">
                        <div class="w-10 h-10 rounded-md bg-gray-200 text-gray-500 flex items-center justify-center font-bold overflow-hidden">
                            @if (!empty($user['photo']))
                                <img
                                    alt="User Avatar"
                                    src="{{ $user['photo'] }}" />
                            @elseif (!empty($user['name']))
                                {{ initials($user['name']) }}
                            @else
                                ??
                            @endif
                        </div>
                    </div>
                    <ul
                        tabindex="0"
                        class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                        <x-menu-item title="PROFILE" icon="o-user-circle" link="/settings/profile" />
                        <x-menu-item title="DECONNECTER" icon="o-arrow-right-start-on-rectangle" />
                    </ul>
                </div>
            </div>

        </x-slot:actions>
    </x-nav>

    <x-main full-width>
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            <x-menu activate-by-route>

                <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" />
                
                <x-menu-sub title="Administrations" icon="o-cog-6-tooth">
                    <x-menu-item title="Profil" link="/administrations/profils" />
                    <x-menu-item title="Creation d'un Profil" link="/administrations/profil/create" />
                    <x-menu-item title="Utilisateur" link="/administrations/users" />
                </x-menu-sub>

                <x-menu-sub title="Gestion produit" icon="o-shopping-bag">
                    <x-menu-item title="Parkod" link="/gestion/produit/parkod" />
                    <x-menu-item title="Marque" link="/gestion/marque" />
                    <x-menu-item title="Produit" link="/gestion/produits" />
                </x-menu-sub>

                <x-menu-sub title="Gestion Fournisseur" icon="o-truck">
                    <x-menu-item title="Fournisseur" link="/gestion/fournisseurs" />
                    <x-menu-item title="Produit Fournisseur" link="##" />
                </x-menu-sub>

                <x-menu-sub title="Gestion Magasin" icon="o-building-storefront">
                    <x-menu-item title="Magasin" link="##" />
                </x-menu-sub>

                <x-menu-sub title="Gestion Approvisionnement" icon="o-folder-open">
                    <x-menu-item title="Commande" link="##" />
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
