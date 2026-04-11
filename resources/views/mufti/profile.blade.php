@extends('layouts.mufti')
@section('title', 'Profile Settings')
@section('topbar-title', 'Profile Settings')
@section('content')
<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <div class="position-relative d-inline-block">
                        <img src="{{ $user->avatar ? asset($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1a7a4a&color=fff' }}" 
                             id="current-avatar-display" class="rounded-3 border shadow-sm" style="width:95px; height:120px; object-fit:cover;">
                        
                        @if(\Storage::disk('public')->exists("avatars/pending_{$user->id}.jpg"))
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-white" title="Pending Admin Approval">
                                <i class="bi bi-clock-history"></i>
                            </span>
                        @endif
                    </div>
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-0">{{ $user->country ?? 'No Country Provided' }}</p>
                <div class="mt-3">
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $user->is_active ? 'text-success' : 'text-danger' }} border {{ $user->is_active ? 'border-success' : 'border-danger' }} border-opacity-25 px-3">
                        {{ $user->is_active ? 'Active Account' : 'Inactive Account' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Administration Info <small class="text-muted fw-normal">(Read-only)</small></h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="mb-3">
                    <label class="small text-muted fw-semibold d-block mb-1">Has "Checked & Approved" Permission?</label>
                    <div class="form-check form-switch opacity-75">
                        <input class="form-check-input" type="checkbox" disabled {{ $user->can_check_approve ? 'checked' : '' }}>
                        <span class="small fw-medium">{{ $user->can_check_approve ? 'Yes' : 'No' }}</span>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="small text-muted fw-semibold d-block mb-1">Scholar Madhhab(s)</label>
                    @forelse($user->madhabs as $m)
                        <span class="badge bg-light text-dark border me-1">{{ $m->name }}</span>
                    @empty
                        <span class="text-muted small">Not specified</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>Personal Settings</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <form method="POST" action="{{ route('mufti.profile.update') }}" enctype="multipart/form-data" id="muftiProfileForm">
                    @csrf @method('PUT')
                    <input type="hidden" name="avatar_data" id="avatar_data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">WhatsApp / Mobile</label>
                        <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $user->mobile) }}">
                        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Profile Photo</label>
                        @if(\Storage::disk('public')->exists("avatars/pending_{$user->id}.jpg"))
                            <div class="alert alert-warning py-2 small mb-2">
                                <i class="bi bi-info-circle me-2"></i>You have a new photo awaiting admin approval.
                            </div>
                        @endif
                        <div class="input-group">
                            <label class="input-group-text btn btn-light border" for="avatar-input"><i class="bi bi-camera me-2"></i>Select & Crop</label>
                            <input type="file" id="avatar-input" class="d-none" accept="image/*">
                        </div>
                        <div class="form-text small">Photos are moderated and must be under 100KB. Target: 95x120 pixels.</div>
                        @error('avatar_data')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4 mt-4">
                        <h6 class="fw-bold small mb-2"><i class="bi bi-lock me-2"></i>Change Password</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">New Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        <div class="form-text small">Leave blank if you don't want to change your password.</div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Cropper Modal --}}
<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Crop Your Profile Photo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="img-container" style="max-height: 400px; overflow: hidden;">
                    <img id="cropper-image" src="" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" id="btn-crop">Crop & Submit</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
    .mufti-avatar-preview { width: 95px; height: 120px; object-fit: cover; }
</style>
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
