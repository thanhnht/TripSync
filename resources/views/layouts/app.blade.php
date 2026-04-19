<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TripSync') – TripSync</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Sora', 'sans-serif']
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#4F7FFA',
                            dark: '#3563E8',
                            light: '#EEF3FF'
                        },
                        surface: '#F8FAFF',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Sora', sans-serif;
            background: #F8FAFF;
        }

        .sidebar-link {
            @apply flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-primary-light hover:text-primary transition-all;
        }

        .sidebar-link.active {
            @apply bg-primary text-white shadow-md shadow-primary/30;
        }
    </style>
</head>

<body class="min-h-screen">

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-100 flex flex-col z-30 shadow-sm">
            {{-- Logo --}}
            <div class="px-6 py-5 border-b border-gray-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                        </svg>
                    </div>
                    <span class="text-lg font-700 text-gray-900 font-semibold">Trip<span
                            class="text-primary">Sync</span></span>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

                <a href="{{ route('dashboard') }}"
                    class="sidebar-link flex items-center gap-3 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Trang chủ</span>
                </a>

                <a href="{{ route('trips.create') }}"
                    class="sidebar-link flex items-center gap-3 {{ request()->routeIs('trips.create') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tạo chuyến đi</span>
                </a>

                <a href="{{ route('trips.join') }}"
                    class="sidebar-link flex items-center gap-3 {{ request()->routeIs('trips.join') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                    </svg>
                    <span>Tham gia chuyến đi</span>
                </a>

            </nav>

            {{-- User --}}
            <div class="px-4 py-4 border-t border-gray-100">
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer group">
                    <img src="{{ Auth::user()->avatar_url }}"
                        class="w-9 h-9 rounded-full object-cover ring-2 ring-primary/20" alt="">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition flex flex-col gap-1">
                        <a href="{{ route('profile') }}" class="text-xs text-primary hover:underline">Hồ sơ</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-xs text-red-500 hover:underline">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <main class="ml-64 flex-1 min-h-screen">
            {{-- Top bar --}}
            <header
                class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-gray-100 px-8 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                        <p class="text-sm text-gray-500 mt-0.5">@yield('page-subtitle')</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @yield('header-actions')
                </div>
            </header>

            {{-- Alerts --}}
            <div class="px-8 pt-4">
                @foreach (['success' => 'green', 'error' => 'red', 'info' => 'blue', 'warning' => 'yellow'] as $type => $color)
                    @if (session($type))
                        <div
                            class="mb-4 px-4 py-3 rounded-xl bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-800 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session($type) }}
                        </div>
                    @endif
                @endforeach

                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Page content --}}
            <div class="px-8 py-6">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
