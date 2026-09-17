@extends('layouts.public')

@section('title', 'Privacy Policy — Darul Ifta Kauzariyya')

@section('content')

{{-- Hero Header --}}
<section class="legal-hero py-5">
    <div class="container position-relative text-center py-4">
        <span class="legal-eyebrow mb-2 d-inline-flex align-items-center gap-2">
            <i class="bi bi-shield-check"></i> Privacy & Transparency
        </span>
        <h1 class="legal-main-title mt-2">Privacy Policy</h1>
        <p class="legal-subtitle mx-auto">
            How Darul Ifta Kauzariyya collects, protects, and handles your personal information.
        </p>
        <p class="text-white-50 small mb-0">
            <i class="bi bi-clock-history me-1"></i> Last updated: September 17, 2026
        </p>
    </div>
</section>

{{-- Main Content Container --}}
<section class="legal-body-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="legal-card shadow-sm p-4 p-md-5">

                    <div class="legal-intro-box mb-5">
                        <h4 class="fw-bold text-dark mb-3">Our Commitment to Your Privacy</h4>
                        <p class="text-secondary leading-relaxed mb-0">
                            At <strong>Darul Ifta Kauzariyya</strong> ("we," "our," or "institution"), we are committed to safeguarding the confidentiality and trust of our questioners, users, and scholars. This Privacy Policy explains our practices regarding data collection, usage, disclosure, and security when you use our web portal, ask questions, or authenticate using external providers like Google OAuth.
                        </p>
                    </div>

                    {{-- 1. Information We Collect --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">1</span> Information We Collect</h3>
                        <p class="text-secondary">We collect information that you directly provide to us, as well as data gathered automatically when interacting with our services:</p>
                        
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <div class="legal-subcard h-100 p-3">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Personal Information</h6>
                                    <ul class="small text-secondary ps-3 mb-0">
                                        <li>Full Name and Display Name</li>
                                        <li>Email address & Phone/Mobile Number</li>
                                        <li>Gender and Preferred Language</li>
                                        <li>Geographical Location (Country, State, District)</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legal-subcard h-100 p-3">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-google text-danger me-2"></i>Google OAuth Data</h6>
                                    <ul class="small text-secondary ps-3 mb-0">
                                        <li>Google Account ID (for seamless sign-in)</li>
                                        <li>Primary Google Email Address</li>
                                        <li>Google Profile Name and Avatar URL</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legal-subcard h-100 p-3">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-chat-left-text-fill text-success me-2"></i>Scholarly Inquiries</h6>
                                    <ul class="small text-secondary ps-3 mb-0">
                                        <li>Question body, madhab preference, and subject</li>
                                        <li>Supporting document attachments or PDFs</li>
                                        <li>Private inquiry history and bookmarks</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legal-subcard h-100 p-3">
                                    <h6 class="fw-bold text-dark"><i class="bi bi-hdd-network text-warning me-2"></i>Technical Logs</h6>
                                    <ul class="small text-secondary ps-3 mb-0">
                                        <li>IP address and approximate country</li>
                                        <li>Browser type and device analytics</li>
                                        <li>Portal page views and visit frequency</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. How We Use Your Information --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">2</span> How We Use Your Information</h3>
                        <p class="text-secondary">Your data is utilized solely for authentic, religious guidance and secure platform operations:</p>
                        <ul class="legal-list">
                            <li><strong>Scholarly Consultation:</strong> Enabling our qualified Muftis to understand your question's context and draft precise rulings.</li>
                            <li><strong>Account Management & Authentication:</strong> Verifying your identity, delivering one-time passcodes (OTP), and facilitating Google Sign-In.</li>
                            <li><strong>Notifications & Updates:</strong> Alerting you when your submitted Fatwa has been answered, approved, or published.</li>
                            <li><strong>Platform Quality & Security:</strong> Preventing spam, abuse, bot attacks, and ensuring uninterrupted service.</li>
                        </ul>
                    </div>

                    {{-- 3. Google API Services User Data Policy Compliance --}}
                    <div class="legal-section mb-5">
                        <div class="google-compliance-card p-4">
                            <h4 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-google text-danger"></i> Google User Data Policy Compliance
                            </h4>
                            <p class="text-secondary mb-3">
                                Darul Ifta Kauzariyya's use and transfer to any other app of information received from Google APIs will adhere to the <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener noreferrer" class="text-primary fw-semibold">Google API Services User Data Policy</a>, including the Limited Use requirements:
                            </p>
                            <ul class="legal-list small text-secondary mb-0">
                                <li>We strictly access only your basic profile information (Email, Name, Avatar) to authenticate your account.</li>
                                <li>We <strong>do not sell</strong>, rent, or trade Google user data to any third parties or advertisers.</li>
                                <li>Google user data is never used to develop, improve, or train generalized AI or machine learning models.</li>
                                <li>You may disconnect or unlink your Google account at any time via your <a href="{{ route('user.settings') }}" class="text-primary">Account Settings</a>.</li>
                            </ul>
                        </div>
                    </div>

                    {{-- 4. Fatwa Privacy & Anonymity --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">4</span> Fatwa Privacy & Public Archive Anonymity</h3>
                        <p class="text-secondary">
                            When an inquiry is approved for publication in our public Islamic Archive:
                        </p>
                        <ul class="legal-list">
                            <li>All personally identifiable information (such as your full name, phone number, email address, and exact location) is <strong>completely removed or anonymized</strong>.</li>
                            <li>Only the abstract question text, scholarly reasoning, madhab opinion, and Mufti signature are published for public educational benefit.</li>
                            <li>Private consultations designated as confidential remain strictly restricted between the Questioner and the assigned Scholar.</li>
                        </ul>
                    </div>

                    {{-- 5. Data Security & Storage --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">5</span> Data Security & Encryption</h3>
                        <p class="text-secondary">
                            We employ industry-standard administrative, physical, and electronic security measures to safeguard your information:
                        </p>
                        <ul class="legal-list">
                            <li><strong>Transport Security:</strong> All data transmissions are encrypted using HTTPS / TLS 1.3.</li>
                            <li><strong>Password Security:</strong> Passwords are cryptographically hashed using modern bcrypt/argon2 algorithms.</li>
                            <li><strong>Access Restrictions:</strong> Access to private inquiries is restricted strictly to authorized scholars and administrators.</li>
                        </ul>
                    </div>

                    {{-- 6. User Rights & Data Retention --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">6</span> Your Rights & Data Choices</h3>
                        <p class="text-secondary">You have complete control over your account and personal information:</p>
                        <ul class="legal-list">
                            <li><strong>Access & Update:</strong> View and update your profile information anytime from the <a href="{{ route('user.settings') }}" class="text-primary">Dashboard</a>.</li>
                            <li><strong>Account Deletion:</strong> Request full deletion of your account and associated personal data by contacting our administration.</li>
                            <li><strong>Unlink External Logins:</strong> Disconnect third-party OAuth providers (Google) with a single click in settings.</li>
                        </ul>
                    </div>

                    {{-- 7. Contact Us & Grievance Officer --}}
                    <div class="legal-section">
                        <h3 class="legal-heading"><span class="legal-num">7</span> Contact Us</h3>
                        <p class="text-secondary">
                            If you have questions, feedback, or data privacy requests regarding this policy, please reach out to our team:
                        </p>
                        <div class="contact-highlight-box p-4 rounded-3 mt-3">
                            <h6 class="fw-bold text-dark mb-2">Darul Ifta Kauzariyya</h6>
                            <p class="small text-secondary mb-1"><i class="bi bi-geo-alt-fill text-gold me-2"></i>Kauzariyya Compound, Edathala North, Aluva, Kerala, India</p>
                            <p class="small text-secondary mb-1"><i class="bi bi-envelope-fill text-gold me-2"></i>Email: <a href="mailto:darulifta.kauzariyya@gmail.com" class="text-primary fw-semibold">darulifta.kauzariyya@gmail.com</a></p>
                            <p class="small text-secondary mb-0"><i class="bi bi-globe me-2 text-gold"></i>Website: <a href="{{ route('home') }}" class="text-primary">https://darulifta.kauzariyya.com</a></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .legal-hero {
        background: linear-gradient(135deg, #0a2540 0%, #153e66 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .legal-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        opacity: 0.05;
        background-image: radial-gradient(#ffffff 1px, transparent 1px);
        background-size: 24px 24px;
        pointer-events: none;
    }
    .legal-eyebrow {
        background: rgba(201, 168, 76, 0.18);
        border: 1px solid rgba(201, 168, 76, 0.4);
        color: #f7e0a3;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 5px 16px;
        border-radius: 999px;
    }
    .legal-main-title {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 2.75rem;
        font-weight: 700;
        letter-spacing: -0.5px;
    }
    .legal-subtitle {
        font-size: 1.1rem;
        color: #cbd5e1;
        max-width: 650px;
    }
    .legal-body-section {
        background-color: #f8fafc;
    }
    .legal-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
    }
    .legal-heading {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1rem;
    }
    .legal-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        font-size: 0.95rem;
        font-weight: 800;
        flex-shrink: 0;
    }
    .legal-subcard {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
    }
    .google-compliance-card {
        background: #fffbeb;
        border: 1.5px solid #fde68a;
        border-radius: 18px;
    }
    .contact-highlight-box {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }
    .legal-list {
        padding-left: 1.25rem;
        color: #475569;
        line-height: 1.75;
    }
    .legal-list li {
        margin-bottom: 0.5rem;
    }
    .leading-relaxed {
        line-height: 1.8;
    }
</style>
@endpush

@endsection
