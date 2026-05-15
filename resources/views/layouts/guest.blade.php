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
            padding: 9px 13px;
            border-radius: 8px;
            border: 1.5px solid #E2E8F0;
            background: #FAFBFC;
            font-size: 13.5px;
            color: #1E293B;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .input-field:focus {
            border-color: #2563EB;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,.08);
        }
        .input-field::placeholder { color: #B0BACA; font-size: 13px; }

        .btn-primary {
            width: 100%;
            padding: 10px 16px;
            background: #2563EB;
            color: #fff;
            font-weight: 600;
            font-size: 13.5px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background .15s, box-shadow .15s, transform .1s;
            box-shadow: 0 1px 3px rgba(37,99,235,.25);
        }
        .btn-primary:hover  { background: #1D4ED8; box-shadow: 0 2px 8px rgba(37,99,235,.3); }
        .btn-primary:active { transform: scale(.98); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background:#F1F5F9">

    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-100 rounded-full opacity-25 -translate-y-1/2 translate-x-1/3 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-sky-100 rounded-full opacity-30 translate-y-1/3 -translate-x-1/4 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-[400px]">

        {{-- Logo --}}
        <div class="text-center mb-6">
            <a href="/" class="inline-flex flex-col items-center gap-2">
                <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-md shadow-blue-200/60">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                    </svg>
                </div>
                <span class="text-[22px] font-bold text-slate-800 tracking-tight">Trip<span class="text-blue-600">Sync</span></span>
            </a>
            <p class="mt-1 text-[12.5px] text-slate-400">Cùng nhau lên kế hoạch, cùng nhau trải nghiệm</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl border border-slate-200/80 p-7" style="box-shadow:0 4px 24px rgba(0,0,0,.06)">

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm">
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>

        <p class="text-center text-[11.5px] text-slate-400 mt-5">
            © {{ date('Y') }} TripSync. Lên kế hoạch cho những chuyến đi tuyệt vời.
        </p>
    </div>

</body>
</html>
