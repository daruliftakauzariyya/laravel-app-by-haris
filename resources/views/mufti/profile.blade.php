@extends('layouts.mufti')
@section('title', 'Profile Settings')

@section('content')
<!-- Page Header Sub-bar -->
<section class="border-b border-outline-variant/60 pb-3 flex flex-wrap items-baseline justify-between gap-2">
    <div>
        <h1 class="font-headline-lg text-[28px] text-primary tracking-tight font-medium">Profile Settings</h1>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-0.5">Manage your scholar credentials, moderation photo, and authentication security.</p>
    </div>
    <div class="flex items-center gap-1.5 text-label-sm text-[11px] text-secondary uppercase tracking-wider font-semibold">
        <span class="material-symbols-outlined text-[16px] text-emerald-700">badge</span>
        <span>Scholar Profile Verified</span>
    </div>
</section>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left Column: Identity & Administration Card -->
    <div class="lg:col-span-4 flex flex-col gap-6">
        <!-- Identity Summary Card -->
        <div class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 text-center shadow-sm relative overflow-hidden">
            <div class="mb-4">
                <div class="relative inline-block">
                    <img src="{{ $user->avatar ? asset($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=092217&color=fff' }}" 
                         id="current-avatar-display" class="rounded-lg border-2 border-outline-variant/60 shadow-md" style="width:105px; height:130px; object-fit:cover;">
                    
                    @if(\Storage::disk('public')->exists("avatars/pending_{$user->id}.jpg"))
                        <span class="absolute -top-2 -right-2 bg-amber-500 text-white rounded-full p-1 shadow border-2 border-white flex items-center justify-center" title="Pending Admin Approval">
                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                        </span>
                    @endif
                </div>
            </div>
            <h3 class="font-headline-sm text-[20px] font-semibold text-primary mb-0.5">{{ $user->name }}</h3>
            <p class="font-body-sm text-[13px] text-on-surface-variant mb-3">{{ $user->country ?? 'Global Scholar' }}</p>
            
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold {{ $user->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' }}">
                <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                <span>{{ $user->is_active ? 'Active Jurisconsult' : 'Inactive Account' }}</span>
            </div>
        </div>

        <!-- Administrative Meta -->
        <div class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 shadow-sm">
            <div class="flex items-center gap-2 pb-3 border-b border-surface-variant/60 mb-4">
                <span class="material-symbols-outlined text-[18px] text-secondary">admin_panel_settings</span>
                <h4 class="font-title-md text-[15px] font-semibold text-primary m-0">Administrative Info <span class="text-[11px] font-normal text-outline">(Read-only)</span></h4>
            </div>
            <div class="flex flex-col gap-4 text-[13px]">
                <div>
                    <div class="text-outline font-semibold text-[11px] uppercase tracking-wider mb-1">Approval Authority</div>
                    <div class="font-medium text-on-surface">
                        @if($user->can_check_approve)
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                Authorized (Can Check & Approve)
                            </span>
                        @else
                            <span class="text-outline">Standard Drafting Authority</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-outline font-semibold text-[11px] uppercase tracking-wider mb-1">Affiliated School (Madhhab)</div>
                    <span class="inline-block px-2.5 py-0.5 rounded text-[12px] font-semibold bg-surface-container border border-outline-variant text-secondary">
                        {{ $user->madhab?->name ?? 'General / Not Specified' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Edit Profile & Password Form -->
    <div class="lg:col-span-8">
        <div class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 lg:p-8 shadow-sm">
            <div class="flex items-center gap-2 pb-4 border-b border-surface-variant/60 mb-6">
                <span class="material-symbols-outlined text-[20px] text-primary">manage_accounts</span>
                <h4 class="font-headline-sm text-[18px] font-semibold text-primary m-0">Personal Credentials & Contact</h4>
            </div>

            <form method="POST" action="{{ route('mufti.profile.update') }}" enctype="multipart/form-data" id="muftiProfileForm">
                @csrf @method('PUT')
                <input type="hidden" name="avatar_data" id="avatar_data">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block font-label-md text-[12px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">Email Address <span class="text-error">*</span></label>
                        <input type="email" name="email" class="w-full px-3.5 py-2 rounded border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary bg-white text-[14px] text-on-surface" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="text-error text-[12px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block font-label-md text-[12px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">WhatsApp / Mobile Contact</label>
                        <input type="text" name="mobile" class="w-full px-3.5 py-2 rounded border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary bg-white text-[14px] text-on-surface" value="{{ old('mobile', $user->mobile) }}">
                        @error('mobile')<div class="text-error text-[12px] mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block font-label-md text-[12px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">Profile Photo</label>
                    @if(\Storage::disk('public')->exists("avatars/pending_{$user->id}.jpg"))
                        <div class="bg-amber-50 border border-amber-300 text-amber-900 p-3 rounded text-[13px] mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-700 text-[18px]">info</span>
                            <span>You currently have a cropped photo awaiting administrative moderation.</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-3">
                        <label class="px-4 py-2 rounded bg-surface-container hover:bg-surface-container-high border border-outline-variant text-[13px] font-medium cursor-pointer inline-flex items-center gap-2 text-on-surface transition-colors" for="avatar-input">
                            <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                            <span>Select & Crop New Photo</span>
                        </label>
                        <input type="file" id="avatar-input" class="hidden" accept="image/*">
                        <span class="text-[12px] text-outline">Target: 95×120 px, max 100KB</span>
                    </div>
                    @error('avatar_data')<div class="text-error text-[12px] mt-1">{{ $message }}</div>@enderror
                </div>

                <!-- Password Box -->
                <div class="bg-surface-container-low border border-surface-variant rounded-lg p-5 mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-[18px] text-secondary">lock</span>
                        <h5 class="font-title-md text-[14px] font-semibold text-primary m-0">Change Authentication Password</h5>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">New Password</label>
                            <input type="password" name="password" class="w-full px-3 py-2 rounded border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary bg-white text-[13px]">
                            @error('password')<div class="text-error text-[11px] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="w-full px-3 py-2 rounded border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary bg-white text-[13px]">
                        </div>
                    </div>
                    <p class="text-[11px] text-outline mt-2 mb-0">Leave blank if you wish to retain your current password.</p>
                </div>

                <div class="pt-4 border-t border-surface-variant/60 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2 rounded bg-primary text-on-primary text-[14px] font-medium hover:bg-primary/90 transition-all shadow-sm inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        <span>Update Scholar Profile</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cropper Modal --}}
<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 shadow-lg rounded-xl overflow-hidden bg-surface">
            <div class="modal-header border-b border-surface-variant px-5 py-3 bg-surface-container-low">
                <h6 class="modal-title font-headline-sm text-[16px] text-primary font-semibold m-0">Crop Profile Photo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-black/5">
                <div class="img-container" style="max-height: 400px; overflow: hidden;">
                    <img id="cropper-image" src="" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer border-t border-surface-variant px-5 py-3 bg-surface-container-low flex justify-between">
                <button type="button" class="px-4 py-1.5 rounded text-[13px] font-medium text-on-surface-variant hover:bg-surface-container" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="px-5 py-1.5 rounded bg-primary text-on-primary text-[13px] font-medium hover:bg-primary/90 shadow-sm" id="btn-crop">Crop & Attach</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
    let cropper;
    const avatarInput = document.getElementById('avatar-input');
    const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
    const cropperImage = document.getElementById('cropper-image');
    
    avatarInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function(event) {
                cropperImage.src = event.target.result;
                cropperModal.show();
            };
            reader.readAsDataURL(files[0]);
        }
    });

    document.getElementById('cropperModal').addEventListener('shown.bs.modal', function() {
        cropper = new Cropper(cropperImage, {
            aspectRatio: 95 / 120,
            viewMode: 2,
            autoCropArea: 1,
        });
    });

    document.getElementById('cropperModal').addEventListener('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });

    document.getElementById('btn-crop').addEventListener('click', function() {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 505,
            });
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('current-avatar-display').src = dataUrl;
            document.getElementById('avatar_data').value = dataUrl;
            cropperModal.hide();
        }
    });
</script>
@endpush
@endsection
