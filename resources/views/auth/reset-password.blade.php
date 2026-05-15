@extends('layouts.guest')
@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="text-center mb-7">
    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-gray-900">Đặt lại mật khẩu</h2>
    <p class="text-sm text-gray-500 mt-1.5">Thiết lập mật khẩu mới cho tài khoản của bạn.</p>
</div>

<form method="POST" action="{{ route('password.update') }}" class="space-y-4" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
        <input type="email" name="email" value="{{ $email ?? old('email') }}"
               class="input-field bg-gray-50 text-gray-500 cursor-not-allowed @error('email') !border-red-400 @enderror"
               readonly>
        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu mới</label>
        <input type="password" name="password" autofocus
               class="input-field @error('password') !border-red-400 @enderror"
               placeholder="Tối thiểu 8 ký tự">
        @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận mật khẩu</label>
        <input type="password" name="password_confirmation"
               class="input-field"
               placeholder="Nhập lại mật khẩu mới">
    </div>

    <button type="submit" class="btn-primary" style="background:#059669" onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
        Cập nhật mật khẩu
    </button>
</form>

<p class="text-center mt-5 text-sm text-gray-400">
    Nhớ ra rồi?
    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">Đăng nhập</a>
</p>
@endsection
