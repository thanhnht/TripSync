@extends('layouts.guest')
@section('title', 'Tạo tài khoản')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Tạo tài khoản mới</h2>
    <p class="mt-1 text-sm text-gray-500">Bắt đầu lên kế hoạch chuyến đi cùng nhóm bạn.</p>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-4" novalidate>
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Họ và tên <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}" autofocus
               class="input-field @error('name') !border-red-400 @enderror"
               placeholder="Nguyễn Văn A">
        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="{{ old('email') }}"
               class="input-field @error('email') !border-red-400 @enderror"
               placeholder="you@example.com">
        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu <span class="text-red-500">*</span></label>
            <input type="password" name="password"
                   class="input-field @error('password') !border-red-400 @enderror"
                   placeholder="Tối thiểu 8 ký tự">
            @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation"
                   class="input-field" placeholder="Nhập lại mật khẩu">
        </div>
    </div>

    <button type="submit" class="btn-primary mt-1">Tạo tài khoản</button>
</form>

<div class="mt-6 pt-5 border-t border-gray-100 text-center text-sm text-gray-500">
    Đã có tài khoản?
    <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-800">Đăng nhập</a>
</div>
@endsection
