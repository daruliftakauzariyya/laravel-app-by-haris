{{-- High Security Fatwa Deletion Modal (Email OTP -> Live Selfie -> Delete -> Done) --}}
<div class="modal fade" id="securityFatwaDeleteModal" tabindex="-1" aria-labelledby="securityFatwaDeleteModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            
            {{-- Modal Header --}}
            <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #0f172a, #1e293b);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171;">
                        <i class="bi bi-shield-lock-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="securityFatwaDeleteModalLabel" style="font-size: 1.15rem;">High-Security Fatwa Deletion</h5>
                        <small class="text-light opacity-75 d-block mt-0.5" id="modalFatwaTarget">Fatwa #—</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="closeSecurityDeleteModal()"></button>
            </div>

            {{-- Stepper Progress Bar --}}
            <div class="bg-light px-4 py-3 border-bottom">
                <div class="d-flex justify-content-between position-relative">
                    {{-- Progress Line --}}
                    <div class="position-absolute top-50 start-0 translate-middle-y w-100 bg-secondary bg-opacity-25" style="height: 3px; z-index: 1;"></div>
                    <div class="position-absolute top-50 start-0 translate-middle-y bg-danger transition-all" id="securityStepProgress" style="height: 3px; width: 0%; z-index: 2; transition: width 0.35s ease;"></div>

                    {{-- Step 1 Indicator --}}
                    <div class="position-relative text-center step-item" id="stepIndicator1" style="z-index: 3;">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-danger text-white" style="width: 32px; height: 32px; font-size: 0.85rem;">1</span>
                        <small class="d-block fw-bold mt-1 text-danger" style="font-size: 0.72rem;">Email OTP</small>
                    </div>

                    {{-- Step 2 Indicator --}}
                    <div class="position-relative text-center step-item" id="stepIndicator2" style="z-index: 3;">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-white border text-muted" style="width: 32px; height: 32px; font-size: 0.85rem;">2</span>
                        <small class="d-block fw-semibold mt-1 text-muted" style="font-size: 0.72rem;">Face Selfie</small>
                    </div>

                    {{-- Step 3 Indicator --}}
                    <div class="position-relative text-center step-item" id="stepIndicator3" style="z-index: 3;">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-white border text-muted" style="width: 32px; height: 32px; font-size: 0.85rem;">3</span>
                        <small class="d-block fw-semibold mt-1 text-muted" style="font-size: 0.72rem;">Done</small>
                    </div>
                </div>
            </div>

            <div class="modal-body p-4">

                {{-- ==================== STEP 1: EMAIL OTP ==================== --}}
                <div id="securityStep1" class="step-view">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 58px; height: 58px; background: rgba(220, 38, 38, 0.08); color: #dc2626;">
                            <i class="bi bi-envelope-check-fill fs-2"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Enter 6-Digit Email OTP</h6>
                        <p class="text-muted small mb-0">
                            A verification code has been dispatched to <strong class="text-dark" id="otpRecipientEmail">your registered admin email</strong>.
                        </p>
                    </div>

                    {{-- Alert Box --}}
                    <div id="otpAlertBox" class="alert alert-danger py-2 px-3 small rounded-3 d-none mb-3"></div>

                    {{-- 6-Digit OTP Inputs --}}
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <input type="text" maxlength="1" class="form-control text-center fw-bold fs-4 otp-digit" style="width: 48px; height: 56px; border-radius: 10px;" data-index="0" autofocus inputmode="numeric">
                        <input type="text" maxlength="1" class="form-control text-center fw-bold fs-4 otp-digit" style="width: 48px; height: 56px; border-radius: 10px;" data-index="1" inputmode="numeric">
                        <input type="text" maxlength="1" class="form-control text-center fw-bold fs-4 otp-digit" style="width: 48px; height: 56px; border-radius: 10px;" data-index="2" inputmode="numeric">
                        <input type="text" maxlength="1" class="form-control text-center fw-bold fs-4 otp-digit" style="width: 48px; height: 56px; border-radius: 10px;" data-index="3" inputmode="numeric">
                        <input type="text" maxlength="1" class="form-control text-center fw-bold fs-4 otp-digit" style="width: 48px; height: 56px; border-radius: 10px;" data-index="4" inputmode="numeric">
                        <input type="text" maxlength="1" class="form-control text-center fw-bold fs-4 otp-digit" style="width: 48px; height: 56px; border-radius: 10px;" data-index="5" inputmode="numeric">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 px-1">
                        <small class="text-muted" id="otpCountdownText">Resend code in <span id="otpCountdownTimer" class="fw-bold text-dark">60</span>s</small>
                        <button type="button" class="btn btn-link p-0 text-decoration-none small text-danger fw-bold d-none" id="resendOtpBtn" onclick="resendDeleteOtp()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Resend OTP
                        </button>
                    </div>

                    <button type="button" class="btn btn-danger w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2" id="verifyOtpBtn" onclick="verifyDeleteOtp()" style="border-radius: 12px; height: 46px;">
                        <span>Verify OTP & Next</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                {{-- ==================== STEP 2: LIVE SELFIE VERIFICATION ==================== --}}
                <div id="securityStep2" class="step-view d-none">
                    <div class="text-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 50px; height: 50px; background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                            <i class="bi bi-camera-fill fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Live Face / Selfie Verification</h6>
                        <p class="text-muted small mb-0">Capture a quick live selfie to authenticate this permanent deletion for audit records.</p>
                    </div>

                    {{-- Camera Container --}}
                    <div class="position-relative mx-auto bg-dark rounded-4 overflow-hidden mb-3 shadow-sm" style="width: 100%; max-width: 320px; height: 260px; border: 3px solid #e2e8f0;" id="cameraViewport">
                        <video id="deleteSelfieVideo" autoplay playsinline class="w-100 h-100" style="object-fit: cover; transform: scaleX(-1);"></video>
                        <canvas id="deleteSelfieCanvas" class="d-none"></canvas>
                        <img id="deleteSelfiePreview" class="w-100 h-100 d-none" style="object-fit: cover;" alt="Captured Selfie">

                        {{-- Face Guide Oval --}}
                        <div class="position-absolute top-50 start-50 translate-middle rounded-circle pointer-events-none" id="faceGuideOval" style="width: 170px; height: 210px; border: 2px dashed rgba(255, 255, 255, 0.65); box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.35);"></div>

                        {{-- Live Badge --}}
                        <div class="position-absolute top-0 start-0 m-2 px-2 py-0.5 rounded-pill bg-danger text-white small fw-bold d-flex align-items-center gap-1" id="liveBadge" style="font-size: 0.7rem;">
                            <span class="spinner-grow spinner-grow-sm" style="width: 6px; height: 6px;" role="status"></span>
                            <span>LIVE</span>
                        </div>
                    </div>

                    {{-- Camera Error Fallback --}}
                    <div id="cameraErrorBox" class="alert alert-warning py-2 px-3 small rounded-3 d-none mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <span>Camera access unavailable. You can take/upload a photo manually:</span>
                        <input type="file" id="fallbackSelfieInput" accept="image/*" capture="user" class="form-control form-control-sm mt-2" onchange="handleFallbackSelfie(this)">
                    </div>

                    {{-- Capture & Action Buttons --}}
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <button type="button" class="btn btn-outline-dark px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 rounded-pill" id="captureSelfieBtn" onclick="captureSelfie()">
                            <i class="bi bi-camera"></i>
                            <span>Capture Selfie</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-semibold d-none rounded-pill" id="retakeSelfieBtn" onclick="retakeSelfie()">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Retake</span>
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50 py-2 fw-semibold rounded-3" onclick="goToStep(1)">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </button>
                        <button type="button" class="btn btn-danger w-50 py-2 fw-bold d-inline-flex align-items-center justify-content-center gap-1 rounded-3" id="executeDeleteBtn" onclick="executePermanentDeletion()" disabled>
                            <i class="bi bi-trash-fill"></i>
                            <span>Delete Fatwa</span>
                        </button>
                    </div>
                </div>

                {{-- ==================== STEP 3: PROCESSING & DELETING ==================== --}}
                <div id="securityStep3" class="step-view d-none text-center py-4">
                    <div class="spinner-border text-danger mb-3" style="width: 3.5rem; height: 3.5rem; border-width: 4px;" role="status">
                        <span class="visually-hidden">Deleting...</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Purging Fatwa Records...</h5>
                    <p class="text-muted small mb-0">Cleaning up scholarly answers, citations, references, and files.</p>
                </div>

                {{-- ==================== STEP 4: DONE ("DEN") ==================== --}}
                <div id="securityStep4" class="step-view d-none text-center py-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 76px; height: 76px; background: #ecfdf5; color: #10b981; border: 3px solid #a7f3d0;">
                        <i class="bi bi-check2-circle fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Fatwa Permanently Deleted</h5>
                    <p class="text-muted small mb-4 px-2" id="deleteSuccessMsg">
                        The fatwa and all associated records have been removed from the portal and logged in the security audit journal.
                    </p>
                    <button type="button" class="btn btn-success w-100 py-2 fw-bold" id="doneCloseBtn" onclick="finishSecurityDelete()" style="border-radius: 12px; height: 46px;">
                        <span>Done</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- High Security Deletion Controller Script --}}
<script>
    let currentSecurityFatwaId = null;
    let currentSecurityQuestionId = null;
    let currentDeleteRedirectUrl = null;
    let deleteCameraStream = null;
    let capturedSelfieBase64 = null;
    let deleteOtpTimerInterval = null;
    let deleteOtpCountdown = 60;

    /** Open the multi-step security modal from anywhere */
    window.openSecurityDeleteModal = function(fatwaId, questionId, fatwaTitle) {
        currentSecurityFatwaId = fatwaId;
        currentSecurityQuestionId = questionId;
        capturedSelfieBase64 = null;

        document.getElementById('modalFatwaTarget').textContent = 'Target: ' + (questionId || ('#' + fatwaId)) + (fatwaTitle ? ' — ' + fatwaTitle : '');
        
        // Reset Inputs & Views
        document.querySelectorAll('.otp-digit').forEach(input => input.value = '');
        document.getElementById('otpAlertBox').classList.add('d-none');
        document.getElementById('cameraErrorBox').classList.add('d-none');
        document.getElementById('executeDeleteBtn').disabled = true;

        goToStep(1);

        // Show Modal
        const modalEl = document.getElementById('securityFatwaDeleteModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        // Trigger Send OTP immediately
        sendDeleteOtpRequest();
    };

    /** Switch Modal Stepper Views */
    function goToStep(stepNumber) {
        document.querySelectorAll('.step-view').forEach(el => el.classList.add('d-none'));
        
        // Reset step circles
        const s1 = document.getElementById('stepIndicator1').querySelector('.step-circle');
        const s2 = document.getElementById('stepIndicator2').querySelector('.step-circle');
        const s3 = document.getElementById('stepIndicator3').querySelector('.step-circle');
        const progress = document.getElementById('securityStepProgress');

        if (stepNumber === 1) {
            document.getElementById('securityStep1').classList.remove('d-none');
            progress.style.width = '0%';
            s1.className = 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-danger text-white';
            s2.className = 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-white border text-muted';
            s3.className = 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-white border text-muted';
            stopCamera();
            setTimeout(() => {
                const firstOtpInput = document.querySelector('.otp-digit[data-index="0"]');
                if (firstOtpInput) firstOtpInput.focus();
            }, 300);
        } else if (stepNumber === 2) {
            document.getElementById('securityStep2').classList.remove('d-none');
            progress.style.width = '50%';
            s1.className = 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-success text-white';
            s2.className = 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-danger text-white';
            s3.className = 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-white border text-muted';
            startCamera();
        } else if (stepNumber === 3) {
            document.getElementById('securityStep3').classList.remove('d-none');
            progress.style.width = '80%';
            stopCamera();
        } else if (stepNumber === 4) {
            document.getElementById('securityStep4').classList.remove('d-none');
            progress.style.width = '100%';
            s1.className = 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-success text-white';
            s2.className = 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-success text-white';
            s3.className = 'rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm step-circle bg-success text-white';
            stopCamera();
        }
    }

    /** Ajax Send OTP to Admin Email */
    function sendDeleteOtpRequest() {
        startOtpCountdown();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch(`/admin/questions/${currentSecurityFatwaId}/delete-otp/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.masked_email) {
                    document.getElementById('otpRecipientEmail').textContent = data.masked_email;
                }
                // If local dev OTP present, prefill for convenience
                if (data.dev_otp) {
                    fillOtpInputs(data.dev_otp);
                }
            } else {
                showOtpError(data.message || 'Failed to send OTP.');
            }
        })
        .catch(err => {
            console.error(err);
            showOtpError('Error communicating with authentication server.');
        });
    }

    function resendDeleteOtp() {
        document.getElementById('otpAlertBox').classList.add('d-none');
        sendDeleteOtpRequest();
    }

    function startOtpCountdown() {
        clearInterval(deleteOtpTimerInterval);
        deleteOtpCountdown = 60;
        document.getElementById('otpCountdownText').classList.remove('d-none');
        document.getElementById('resendOtpBtn').classList.add('d-none');
        document.getElementById('otpCountdownTimer').textContent = deleteOtpCountdown;

        deleteOtpTimerInterval = setInterval(() => {
            deleteOtpCountdown--;
            document.getElementById('otpCountdownTimer').textContent = deleteOtpCountdown;
            if (deleteOtpCountdown <= 0) {
                clearInterval(deleteOtpTimerInterval);
                document.getElementById('otpCountdownText').classList.add('d-none');
                document.getElementById('resendOtpBtn').classList.remove('d-none');
            }
        }, 1000);
    }

    function showOtpError(msg) {
        const box = document.getElementById('otpAlertBox');
        box.textContent = msg;
        box.classList.remove('d-none');
    }

    function fillOtpInputs(code) {
        const digits = code.toString().split('');
        document.querySelectorAll('.otp-digit').forEach((input, idx) => {
            input.value = digits[idx] || '';
        });
    }

    function getEnteredOtp() {
        let code = '';
        document.querySelectorAll('.otp-digit').forEach(input => {
            code += (input.value || '');
        });
        return code.trim();
    }

    /** Verify OTP */
    function verifyDeleteOtp() {
        const otp = getEnteredOtp();
        if (otp.length !== 6) {
            showOtpError('Please enter all 6 digits of the OTP code.');
            return;
        }

        const btn = document.getElementById('verifyOtpBtn');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> Verifying...`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch(`/admin/questions/${currentSecurityFatwaId}/delete-otp/verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ otp: otp })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = `<span>Verify OTP & Next</span> <i class="bi bi-arrow-right"></i>`;

            if (data.success) {
                goToStep(2); // Proceed to Selfie
            } else {
                showOtpError(data.message || 'Invalid OTP code.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = `<span>Verify OTP & Next</span> <i class="bi bi-arrow-right"></i>`;
            showOtpError('Network error verifying code.');
        });
    }

    /** Setup OTP Digits Auto Tab & Paste */
    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll('.otp-digit');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
                if (e.key === 'Enter') {
                    verifyDeleteOtp();
                }
            });
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                if (/^\d{6}$/.test(pasteData)) {
                    fillOtpInputs(pasteData);
                    verifyDeleteOtp();
                }
            });
        });
    });

    /** Start Camera for Selfie Verification */
    async function startCamera() {
        const video = document.getElementById('deleteSelfieVideo');
        const preview = document.getElementById('deleteSelfiePreview');
        const faceGuide = document.getElementById('faceGuideOval');
        const liveBadge = document.getElementById('liveBadge');
        const captureBtn = document.getElementById('captureSelfieBtn');
        const retakeBtn = document.getElementById('retakeSelfieBtn');

        video.classList.remove('d-none');
        preview.classList.add('d-none');
        faceGuide.classList.remove('d-none');
        liveBadge.classList.remove('d-none');
        captureBtn.classList.remove('d-none');
        retakeBtn.classList.add('d-none');
        document.getElementById('cameraErrorBox').classList.add('d-none');

        try {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                deleteCameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                    audio: false
                });
                video.srcObject = deleteCameraStream;
            } else {
                throw new Error('MediaDevices not supported');
            }
        } catch (err) {
            console.warn('Camera stream error:', err);
            document.getElementById('cameraErrorBox').classList.remove('d-none');
        }
    }

    /** Stop Camera Stream */
    function stopCamera() {
        if (deleteCameraStream) {
            deleteCameraStream.getTracks().forEach(track => track.stop());
            deleteCameraStream = null;
        }
    }

    /** Capture Selfie */
    function captureSelfie() {
        const video = document.getElementById('deleteSelfieVideo');
        const canvas = document.getElementById('deleteSelfieCanvas');
        const preview = document.getElementById('deleteSelfiePreview');
        const faceGuide = document.getElementById('faceGuideOval');
        const liveBadge = document.getElementById('liveBadge');
        const captureBtn = document.getElementById('captureSelfieBtn');
        const retakeBtn = document.getElementById('retakeSelfieBtn');
        const execBtn = document.getElementById('executeDeleteBtn');

        if (video.videoWidth) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            // Mirror image to match live view
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            capturedSelfieBase64 = canvas.toDataURL('image/jpeg', 0.85);

            preview.src = capturedSelfieBase64;
            preview.classList.remove('d-none');
            video.classList.add('d-none');
            faceGuide.classList.add('d-none');
            liveBadge.classList.add('d-none');
            captureBtn.classList.add('d-none');
            retakeBtn.classList.remove('d-none');

            stopCamera();
            execBtn.disabled = false;
        }
    }

    /** Retake Selfie */
    function retakeSelfie() {
        capturedSelfieBase64 = null;
        document.getElementById('executeDeleteBtn').disabled = true;
        startCamera();
    }

    /** Fallback File Upload */
    function handleFallbackSelfie(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                capturedSelfieBase64 = e.target.result;
                const preview = document.getElementById('deleteSelfiePreview');
                preview.src = capturedSelfieBase64;
                preview.classList.remove('d-none');
                document.getElementById('deleteSelfieVideo').classList.add('d-none');
                document.getElementById('faceGuideOval').classList.add('d-none');
                document.getElementById('liveBadge').classList.add('d-none');
                document.getElementById('executeDeleteBtn').disabled = false;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    /** Step 3 & 4: Execute Permanent Deletion */
    function executePermanentDeletion() {
        const otp = getEnteredOtp();
        goToStep(3); // Show spinner

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch(`/admin/questions/${currentSecurityFatwaId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                otp: otp,
                selfie_image: capturedSelfieBase64
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (res.ok && data.success) {
                currentDeleteRedirectUrl = data.redirect_url || '/admin/questions';
                document.getElementById('deleteSuccessMsg').textContent = `Fatwa #${data.question_id || currentSecurityQuestionId} and all associated answers, references, and files have been permanently deleted and logged in the security audit journal.`;
                goToStep(4); // Done
            } else {
                alert(data.message || 'Deletion failed. Verification was rejected.');
                goToStep(2);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Server communication error during deletion.');
            goToStep(2);
        });
    }


    /** Finish and Redirect ("den") */
    function finishSecurityDelete() {
        closeSecurityDeleteModal();
        if (currentDeleteRedirectUrl) {
            window.location.href = currentDeleteRedirectUrl;
        } else {
            window.location.href = '{{ route('admin.questions.index') }}';
        }
    }

    function closeSecurityDeleteModal() {
        stopCamera();
        clearInterval(deleteOtpTimerInterval);
        const modalEl = document.getElementById('securityFatwaDeleteModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
</script>
