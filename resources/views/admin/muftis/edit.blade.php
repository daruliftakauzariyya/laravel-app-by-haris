@extends('layouts.admin')
@section('title', 'Edit Mufti Profile')
@section('topbar-title', 'Updating Scholar Profile')
@section('content')

<div class="row justify-content-center g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                <h6 class="mb-0 fw-bold">Update Account — {{ $user->name }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.muftis.update', $user) }}" enctype="multipart/form-data" id="muftiEditForm">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="avatar_data" id="avatar_data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4 mt-4">
                        <h6 class="fw-bold small mb-3"><i class="bi bi-shield-lock me-2"></i>Change Password (Optional)</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <label class="form-label small fw-semibold text-muted">New Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        <div class="form-text mt-1 small">Leave blank to keep the current password.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Scholar's Country</label>
                                <input type="text" name="country" class="form-control" value="{{ old('country', $user->country) }}" placeholder="e.g. South Africa">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Select Madhhab(s)</label>
                                <div class="d-flex flex-wrap gap-3 mt-1">
                                    @php $madhabs = ['Hanafi', 'Shafii', 'Maliki', 'Hanbali']; @endphp
                                    @foreach($madhabs as $m)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="madhab[]" value="{{ $m }}" id="madhab_{{ $m }}" {{ in_array($m, (array)old('madhab', $user->madhab ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="madhab_{{ $m }}">{{ $m }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-info bg-opacity-10 border border-info border-opacity-10 p-3 rounded-3 mb-4">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">Account Status Active</label>
                        </div>
                        <hr class="my-2 border-info border-opacity-25 opacity-25">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="can_check_approve" id="canApprove" value="1" {{ $user->can_check_approve ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="canApprove">Can Verify & Approve Fatwas</label>
                            <div class="form-text small opacity-75">Senior status for checking other muftis' work.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('admin.muftis.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Side Stats --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold small mb-3">Scholar Activity Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Assigned Questions</span>
                    <span class="badge bg-secondary rounded-pill">{{ $user->assignedQuestions()->count() }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Fatwas Answered</span>
                    <span class="badge bg-primary rounded-pill">{{ $user->muftiAnswers()->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Profile Photo Section --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold small mb-3"><i class="bi bi-camera me-2"></i>Profile Photo</h6>
                
                <div class="text-center mb-3">
                    <div class="position-relative d-inline-block">
                        <img src="{{ $user->avatar ? asset($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1a7a4a&color=fff' }}" 
                             id="current-avatar" class="rounded-3 shadow-sm" style="width: 95px; height: 120px; object-fit: cover;">
                        @if(\Storage::disk('public')->exists("avatars/pending_{$user->id}.jpg"))
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-white">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>

                @php $hasPending = \Storage::disk('public')->exists("avatars/pending_{$user->id}.jpg"); @endphp

                @if($hasPending)
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 mb-3 border border-warning border-opacity-25">
                        <p class="x-small fw-bold text-warning-emphasis mb-2"><i class="bi bi-info-circle me-1"></i>A new photo is awaiting approval:</p>
                        <img src="{{ asset('/storage/avatars/pending_'.$user->id.'.jpg') }}" class="rounded-2 mb-3 shadow-sm" style="width: 70px; height: 90px; object-fit: cover;">
                        <div class="d-flex gap-2">
                            <button type="button" onclick="event.preventDefault(); document.getElementById('approveAvatarForm').submit();" class="btn btn-success btn-sm flex-grow-1">Approve</button>
                            <button type="button" onclick="event.preventDefault(); document.getElementById('rejectAvatarForm').submit();" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                @endif

                <div class="mt-3">
                    <label class="btn btn-light btn-sm w-100 border" for="avatar-input">
                        <i class="bi bi-upload me-2"></i>Upload New Photo
                    </label>
                    <input type="file" id="avatar-input" class="d-none" accept="image/*">
                    <div class="form-text x-small text-center mt-2">Format: 95x120 pixels. Max 100KB.</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Moderation Forms --}}
<form id="approveAvatarForm" action="{{ route('admin.muftis.approve-avatar', $user) }}" method="POST" class="d-none">@csrf</form>
<form id="rejectAvatarForm" action="{{ route('admin.muftis.reject-avatar', $user) }}" method="POST" class="d-none">@csrf</form>

{{-- Cropper Modal --}}
<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Crop Profile Photo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="cropper-image" src="" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-crop">Crop & Use</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
    .x-small { font-size: 0.75rem; }
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
            document.getElementById('current-avatar').src = dataUrl;
            document.getElementById('avatar_data').value = dataUrl;
            cropperModal.hide();
        }
    });
</script>
@endpush

@endsection
