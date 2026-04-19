{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.guest')
@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="max-w-md mx-auto">
    {{-- Header Section --}}
    <div class="mb-8">
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Tạo tài khoản</h2>
        <p class="text-sm text-gray-500 mt-2">Bắt đầu hành trình và lên kế hoạch chuyến đi cùng nhóm bạn.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="flex items-center gap-5 p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50">
            <div class="relative group">
                <img id="avatar-preview"
                     src="https://ui-avatars.com/api/?name=?&background=4F7FFA&color=fff&size=128"
                     class="w-20 h-20 rounded-full object-cover ring-4 ring-white shadow-sm transition-transform group-hover:scale-105">

                <label for="avatar" class="absolute -bottom-1 -right-1 w-7 h-7 bg-blue-600 border-2 border-white rounded-full flex items-center justify-center cursor-pointer hover:bg-blue-700 transition-all shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </label>
                <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*">
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-gray-800">Ảnh đại diện</p>
                <p class="text-xs text-gray-500 mt-1">JPG, PNG tối đa 2MB (tùy chọn)</p>
            </div>
        </div>

        {{-- Full Name --}}
        <div class="group">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5 transition-colors group-focus-within:text-blue-600">Họ và tên <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none @error('name') border-red-400 @enderror"
                   placeholder="Nguyễn Văn A">
            @error('name') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div class="group">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5 transition-colors group-focus-within:text-blue-600">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none @error('email') border-red-400 @enderror"
                   placeholder="you@example.com">
            @error('email') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
        </div>

        {{-- Password Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5 transition-colors group-focus-within:text-blue-600">Mật khẩu <span class="text-red-500">*</span></label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none @error('password') border-red-400 @enderror"
                       placeholder="••••••••">
                @error('password') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>
            <div class="group">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5 transition-colors group-focus-within:text-blue-600">Xác nhận <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                       placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-[0.98] mt-4">
            Đăng ký tài khoản
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-8">
        Đã có tài khoản?
        <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Đăng nhập ngay</a>
    </p>
</div>

<script>
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            const preview = document.getElementById('avatar-preview');
            preview.src = ev.target.result;
            preview.classList.add('animate-pulse');
            setTimeout(() => preview.classList.remove('animate-pulse'), 500);
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
