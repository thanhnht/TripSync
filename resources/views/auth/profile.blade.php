@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân')
@section('page-title', 'Hồ sơ cá nhân')
@section('page-subtitle', 'Quản lý thông tin tài khoản')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Profile info --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-6">Thông tin cá nhân</h3>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            {{-- Avatar --}}
            <div class="flex items-center gap-5">
                <div class="relative">
                    <img id="avatar-preview" src="{{ $user->avatar_url }}"
                         class="w-20 h-20 rounded-full object-cover ring-4 ring-gray-100">
                    <label for="avatar" class="absolute -bottom-1.5 -right-1.5 w-7 h-7 bg-primary rounded-full flex items-center justify-center cursor-pointer hover:bg-primary-dark transition shadow-md">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </label>
                    <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*">
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG tối đa 2MB</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Họ và tên</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Số điện thoại</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition"
                       placeholder="0900 000 000">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-sm text-gray-500 cursor-not-allowed">
                <p class="mt-1 text-xs text-gray-400">Email không thể thay đổi</p>
            </div>

            <button type="submit"
                    class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-md shadow-primary/30">
                Lưu thay đổi
            </button>
        </form>
    </div>

    {{-- Change password --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-6">Đổi mật khẩu</h3>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition @error('current_password') border-red-400 @enderror">
                @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu mới</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition @error('password') border-red-400 @enderror"
                           placeholder="Tối thiểu 8 ký tự">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition"
                           placeholder="Nhập lại mật khẩu">
                </div>
            </div>

            <button type="submit"
                    class="px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-700 transition">
                Đổi mật khẩu
            </button>
        </form>
    </div>
</div>

<script>
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => document.getElementById('avatar-preview').src = ev.target.result;
        reader.readAsDataURL(file);
    });
</script>
@endsection
