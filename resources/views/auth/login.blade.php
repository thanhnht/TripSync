@extends('layouts.guest')
@section('title', 'Đăng nhập')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Chào mừng trở lại</h2>
    <p class="mt-1 text-sm text-gray-500">Đăng nhập để tiếp tục lên kế hoạch chuyến đi.</p>
</div>

<form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" autofocus
               class="input-field @error('email') !border-red-400 @enderror"
               placeholder="you@example.com">
        @error('email')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="text-sm font-medium text-gray-700">Mật khẩu</label>
            <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                Quên mật khẩu?
            </a>
        </div>
        <input type="password" name="password"
               class="input-field @error('password') !border-red-400 @enderror"
               placeholder="••••••••">
        @error('password')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="remember" name="remember"
               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
        <label for="remember" class="text-sm text-gray-600 cursor-pointer select-none">Ghi nhớ đăng nhập</label>
    </div>

    <button type="submit" class="btn-primary mt-1">Đăng nhập</button>
</form>

<div class="mt-6 pt-5 border-t border-gray-100 text-center text-sm text-gray-500">
    Chưa có tài khoản?
    <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-800">Tạo tài khoản</a>
</div>
@endsection
