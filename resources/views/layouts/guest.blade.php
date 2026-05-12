<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') – TripSync</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { DEFAULT: '#2563EB', dark: '#1D4ED8', light: '#EFF6FF' }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .input-field {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1.5px solid #E2E8F0;
            background: #FAFBFC;
            font-size: 14px;
            color: #1E293B;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .input-field:focus {
            border-color: #2563EB;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        }
        .input-field::placeholder { color: #94A3B8; }
        .btn-primary {
            width: 100%;
            padding: 11px 16px;
            background: #2563EB;
            color: white;
            font-weight: 600;
            font-size: 14px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: background .15s, transform .1s;
        }
        .btn-primary:hover  { background: #1D4ED8; }
        .btn-primary:active { transform: scale(.98); }
    </style>
</head>
<body class="min-h-screen bg-[#F1F5F9] flex items-center justify-center p-4">

    {{-- Subtle background pattern --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-100 rounded-full opacity-30 -translate-y-1/2 translate-x-1/3 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-sky-100 rounded-full opacity-40 translate-y-1/3 -translate-x-1/4 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-[420px]">

        {{-- Logo --}}
        <div class="text-center mb-7">
            <a href="/" class="inline-flex flex-col items-center gap-2">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-gray-900 tracking-tight">Trip<span class="text-blue-600">Sync</span></span>
            </a>
            <p class="mt-1.5 text-[13px] text-gray-500">Cùng nhau lên kế hoạch, cùng nhau trải nghiệm</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/80 border border-gray-100 p-8">

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>

        <p class="text-center text-[12px] text-gray-400 mt-5">
            © {{ date('Y') }} TripSync. Lên kế hoạch cho những chuyến đi tuyệt vời.
        </p>
    </div>

</body>
</html>
