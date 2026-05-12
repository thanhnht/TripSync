<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TripSync') – TripSync</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: {
                            DEFAULT: '#2563EB',
                            dark:    '#1D4ED8',
                            light:   '#EFF6FF',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F1F5F9; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #64748B;
            transition: all .15s;
            text-decoration: none;
        }
        .nav-item:hover { background: #F8FAFC; color: #1E293B; }
        .nav-item.active { background: #EFF6FF; color: #2563EB; font-weight: 600; }
        .nav-item.active svg { color: #2563EB; }

        /* Auto-dismiss toast */
        .toast { animation: slideIn .25s ease, fadeOut .4s ease 3.8s forwards; }
        @keyframes slideIn  { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeOut  { to   { opacity:0; height:0; padding:0; margin:0; overflow:hidden; } }
    </style>
</head>
<body class="min-h-screen">

<div class="flex min-h-screen">

    {{-- ── Sidebar ──────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 w-60 bg-white border-r border-gray-200/80 flex flex-col z-30">

        {{-- Logo --}}
        <div class="flex items-center gap-2.5 px-5 h-16 border-b border-gray-100 shrink-0">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-md shadow-blue-200">
                <svg class="w-4.5 h-4.5 w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                </svg>
            </div>
            <span class="text-[15px] font-bold text-gray-900 tracking-tight">Trip<span class="text-blue-600">Sync</span></span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            <p class="px-3 mb-1.5 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Menu</p>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Tổng quan
            </a>

            <a href="{{ route('trips.create') }}"
               class="nav-item {{ request()->routeIs('trips.create') ? 'active' : '' }}">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tạo chuyến đi
            </a>

            <a href="{{ route('trips.join') }}"
               class="nav-item {{ request()->routeIs('trips.join') ? 'active' : '' }}">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Tham gia chuyến đi
            </a>

            @if(request()->route('trip'))
            @php $sideTrip = request()->route('trip'); @endphp
            <div class="mt-4 mb-1">
                <p class="px-3 mb-1.5 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Chuyến đi này</p>
                <div class="px-3 py-2 mb-2 bg-blue-50 rounded-lg">
                    <p class="text-[13px] font-semibold text-blue-800 truncate">{{ $sideTrip->name }}</p>
                    <p class="text-[11px] text-blue-500 truncate">📍 {{ $sideTrip->destination }}</p>
                </div>
            </div>

            <a href="{{ route('trips.show', $sideTrip) }}"
               class="nav-item {{ request()->routeIs('trips.show') ? 'active' : '' }}">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tổng quan chuyến đi
            </a>
            <a href="{{ route('schedule.index', $sideTrip) }}"
               class="nav-item {{ request()->routeIs('schedule.*') ? 'active' : '' }}">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Lịch trình
            </a>
            <a href="{{ route('expense.index', $sideTrip) }}"
               class="nav-item {{ request()->routeIs('expense.*') ? 'active' : '' }}">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Chi tiêu
            </a>
            <a href="{{ route('photo.index', $sideTrip) }}"
               class="nav-item {{ request()->routeIs('photo.*') ? 'active' : '' }}">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Thư viện ảnh
            </a>
            <a href="{{ route('checklist.index', $sideTrip) }}"
               class="nav-item {{ request()->routeIs('checklist.*') ? 'active' : '' }}">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Checklist
            </a>
            @endif
        </nav>

        {{-- User section --}}
        <div class="px-3 py-3 border-t border-gray-100 shrink-0">
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-lg">
                <img src="{{ Auth::user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-100 shrink-0" alt="">
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-semibold text-gray-800 truncate leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <a href="{{ route('profile') }}" title="Hồ sơ"
                       class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition">
                        <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button title="Đăng xuất"
                                class="p-1.5 rounded-md text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                            <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── Main ──────────────────────────────────────────────────── --}}
    <div class="ml-60 flex-1 flex flex-col min-h-screen">

        {{-- Top bar --}}
        <header class="sticky top-0 z-20 h-16 bg-white border-b border-gray-200/80 px-8 flex items-center justify-between shrink-0">
            <div>
                <h1 class="text-[17px] font-semibold text-gray-900 leading-tight">@yield('page-title', 'Tổng quan')</h1>
                @hasSection('page-subtitle')
                    <p class="text-[13px] text-gray-500 leading-tight">@yield('page-subtitle')</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @yield('header-actions')
            </div>
        </header>

        {{-- Toasts --}}
        <div class="px-8 pt-4 space-y-2">
            @foreach(['success' => ['bg-green-50','border-green-200','text-green-800'], 'error' => ['bg-red-50','border-red-200','text-red-700'], 'info' => ['bg-blue-50','border-blue-200','text-blue-700'], 'warning' => ['bg-amber-50','border-amber-200','text-amber-700']] as $type => $cls)
                @if(session($type))
                    <div class="toast flex items-center gap-3 px-4 py-3 rounded-xl border text-sm {{ $cls[0] }} {{ $cls[1] }} {{ $cls[2] }}">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        {{ session($type) }}
                    </div>
                @endif
            @endforeach

            @if($errors->any())
                <div class="toast px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li class="flex items-start gap-1.5">
                                <span class="mt-0.5 shrink-0">•</span>{{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <main class="flex-1 px-8 py-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
