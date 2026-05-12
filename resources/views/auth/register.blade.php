@extends('layouts.guest')
@section('title', 'Tạo tài khoản')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Tạo tài khoản mới</h2>
    <p class="mt-1 text-sm text-gray-500">Bắt đầu lên kế hoạch chuyến đi cùng nhóm bạn.</p>
</div>

<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-4">
    @csrf

    {{-- Avatar --}}
    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-gray-100">
        <div class="relative shrink-0">
            <img id="avatar-preview"
                 src="https://ui-avatars.com/api/?name=?&background=2563EB&color=fff&size=128"
                 class="w-16 h-16 rounded-full object-cover ring-2 ring-white shadow">
            <label for="avatar"
                   class="absolute -bottom-1 -right-1 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-blue-700 transition shadow border-2 border-white">
                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
            </label>
            <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*">
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">Ảnh đại diện</p>
            <p class="text-xs text-gray-400 mt-0.5">JPG, PNG · tối đa 2MB · tùy chọn</p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Họ và tên <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="input-field @error('name') !border-red-400 @enderror"
               placeholder="Nguyễn Văn A">
        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="input-field @error('email') !border-red-400 @enderror"
               placeholder="you@example.com">
        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Mật khẩu <span class="text-red-500">*</span></label>
            <input type="password" name="password" required
                   class="input-field @error('password') !border-red-400 @enderror"
                   placeholder="••••••••">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Xác nhận <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" required
                   class="input-field" placeholder="••••••••">
        </div>
    </div>

    <button type="submit" class="btn-primary mt-1">Tạo tài khoản</button>
</form>

<div class="mt-6 pt-5 border-t border-gray-100 text-center text-sm text-gray-500">
    Đã có tài khoản?
    <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-800">Đăng nhập</a>
</div>

<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => { document.getElementById('avatar-preview').src = ev.target.result; };
    reader.readAsDataURL(file);
});
</script>
@endsection
