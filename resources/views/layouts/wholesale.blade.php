<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Farm Direct') — Farm Direct</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Top nav --}}
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center">

            <!-- Left Section (Logo) -->
            <div class="flex-1 flex justify-start">
                <a href="{{ route('wholesale.index') }}" class="font-bold text-green-700 text-lg tracking-tight whitespace-nowrap">
                    🌿 Farm Direct <span class="text-gray-400 font-normal text-sm">Wholesale</span>
                </a>
            </div>

            <!-- Center Section (Links) -->
            <div class="flex items-center gap-8 justify-center">
                <a href="{{ route('wholesale.index') }}"
                class="relative py-1 text-sm transition-colors duration-300 {{ request()->routeIs('wholesale.index') ? 'text-green-700 font-medium' : 'text-gray-500 hover:text-gray-900' }} group">
                    Products
                    <span class="absolute inset-x-0 -bottom-1 h-0.5 bg-green-600 transform scale-x-0 transition-transform duration-300 group-hover:scale-x-100 {{ request()->routeIs('wholesale.index') ? 'scale-x-100' : '' }}"></span>
                </a>

                <a href="{{ route('wholesale.orders') }}"
                class="relative py-1 text-sm transition-colors duration-300 {{ request()->routeIs('wholesale.orders*') ? 'text-green-700 font-medium' : 'text-gray-500 hover:text-gray-900' }} group">
                    My Orders
                    <span class="absolute inset-x-0 -bottom-1 h-0.5 bg-green-600 transform scale-x-0 transition-transform duration-300 group-hover:scale-x-100 {{ request()->routeIs('wholesale.orders*') ? 'scale-x-100' : '' }}"></span>
                </a>
            </div>

            <!-- Right Section (Cart/Logout) -->
            <div class="flex-1 flex items-center justify-end gap-6 text-sm">
                <a href="{{ route('wholesale.cart') }}" class="relative group p-1 {{ request()->routeIs('wholesale.cart') ? 'text-green-700' : 'text-gray-600 hover:text-green-700' }}">
                    <span class="transition-transform inline-block group-hover:scale-110">🛒 Cart</span>
                    @php $cartCount = array_sum(session('wholesale_cart', [])); @endphp
                    <span id="cart-count-badge"
                        class="absolute -top-1 -right-2 bg-green-600 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center ring-2 ring-white {{ $cartCount === 0 ? 'hidden' : '' }}">
                        {{ $cartCount ?: '' }}
                    </span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="text-gray-400 hover:text-red-500 transition-colors duration-300 font-medium whitespace-nowrap">Sign out</button>
                </form>
            </div>

        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @include('partials.flash')
        @yield('content')
    </main>

    @stack('scripts')

</body>
</html>