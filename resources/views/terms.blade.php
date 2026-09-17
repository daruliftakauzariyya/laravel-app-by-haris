@extends('layouts.public')

@section('title', 'Terms of Service — Darul Ifta Kauzariyya')

@section('content')

{{-- Hero Header --}}
<section class="legal-hero py-5">
    <div class="container position-relative text-center py-4">
        <span class="legal-eyebrow mb-2 d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-text"></i> Legal & Platform Guidelines
        </span>
        <h1 class="legal-main-title mt-2">Terms of Service</h1>
        <p class="legal-subtitle mx-auto">
            Rules, guidelines, and terms governing your use of the Darul Ifta Kauzariyya platform.
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
                        <h4 class="fw-bold text-dark mb-3">Welcome to Darul Ifta Kauzariyya</h4>
                        <p class="text-secondary leading-relaxed mb-0">
                            By accessing or using the <strong>Darul Ifta Kauzariyya</strong> website, portals, mobile services, or submitting Fatwa inquiries (collectively, the "Services"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to these terms, please do not use our services.
                        </p>
                    </div>

                    {{-- 1. Nature of Islamic Rulings (Fatwas) --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">1</span> Nature & Purpose of Islamic Rulings (Fatwas)</h3>
                        <p class="text-secondary">
                            Darul Ifta Kauzariyya is an Islamic educational and jurisprudence research institution. It is crucial to understand the nature of the verdicts issued:
                        </p>
                        <div class="alert alert-info border-0 rounded-3 p-3 mb-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Religious Guidance Only:</strong> Fatwas issued through this portal represent non-binding scholarly religious opinions based on authentic Islamic jurisprudence (Fiqh) according to recognized Sunni Madhabs (Hanafi, Shafi'i, Maliki, Hanbali).
                        </div>
                        <ul class="legal-list">
                            <li>Fatwas are provided for spiritual, moral, and conscience guidance of Muslim individuals and communities.</li>
                            <li><strong>Not a Court of Law:</strong> A Fatwa is not a judicial court decree, statutory legal counsel, medical verdict, or financial investment advice. It does not replace civil or criminal judicial procedures in your jurisdiction.</li>
                            <li>In legal disputes (e.g., civil litigation, property disputes, divorce arbitration in civil courts), users must consult accredited legal professionals under their national law.</li>
                        </ul>
                    </div>

                    {{-- 2. User Accounts & Registration --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">2</span> User Accounts & Authentication</h3>
                        <p class="text-secondary">When creating an account or authenticating via Google:</p>
                        <ul class="legal-list">
                            <li>You must provide true, accurate, current, and complete information regarding your contact details and inquiries.</li>
                            <li>You are responsible for maintaining the confidentiality of your credentials, OTPs, and connected Google account.</li>
                            <li>You must notify us immediately of any unauthorized use or security breach of your account.</li>
                            <li>We reserve the right to suspend or terminate accounts that engage in harassment, spam, fraudulent queries, or platform abuse.</li>
                        </ul>
                    </div>

                    {{-- 3. Inquiries & Question Submission Guidelines --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">3</span> Inquiry Submission Code of Conduct</h3>
                        <p class="text-secondary">To ensure scholarly integrity and meaningful service, users must adhere to the following rules when submitting questions:</p>
                        <ul class="legal-list">
                            <li><strong>Sincerity & Truthfulness:</strong> Questions must be genuine and represent actual scenarios. Fictitious, hypothetical tests, or mocking inquiries are strictly prohibited.</li>
                            <li><strong>Respect & Decency:</strong> Content containing hate speech, blasphemy, obscenity, defamation, or political agitation will be rejected immediately without notice.</li>
                            <li><strong>Clarity & Detail:</strong> Provide necessary context and specify your preferred Madhab (Hanafi, Shafi'i, Maliki, Hanbali) if applicable.</li>
                            <li><strong>Attachments:</strong> Any attached documents (PDFs/Images) must be relevant, lawful, and free from malicious code.</li>
                        </ul>
                    </div>

                    {{-- 4. Publication Rights & Intellectual Property --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">4</span> Intellectual Property & Publication Rights</h3>
                        <p class="text-secondary">
                            The scholarly answers, compilations, research papers, references, and digital layout on this portal belong to <strong>Darul Ifta Kauzariyya</strong>:
                        </p>
                        <ul class="legal-list">
                            <li><strong>Educational Sharing:</strong> You are encouraged to read, study, bookmark, and share published fatwas for personal non-commercial educational purposes with proper attribution to <em>Darul Ifta Kauzariyya</em>.</li>
                            <li><strong>Commercial Use:</strong> You may not commercially reproduce, sell, republish in bulk, or exploit the content of this portal without written permission from the institution.</li>
                            <li><strong>Anonymized Public Benefit:</strong> By submitting a query, you grant Darul Ifta Kauzariyya the right to edit, anonymize, and publish the scholarly question and verdict in our public archive for broader community education.</li>
                        </ul>
                    </div>

                    {{-- 5. Free Service & Availability --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">5</span> Free Service & Turnaround Time</h3>
                        <p class="text-secondary">
                            Darul Ifta Kauzariyya operates as a <strong>100% Free Public Service</strong> for the Ummah:
                        </p>
                        <ul class="legal-list">
                            <li>No fees or mandatory charges are levied for asking questions or receiving verified Fatwas.</li>
                            <li>While our scholars strive to address inquiries promptly, response times may vary depending on research complexity, scholarly review queues, and academic calendars.</li>
                        </ul>
                    </div>

                    {{-- 6. Limitation of Liability & Disclaimer --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">6</span> Disclaimer of Warranties & Limitation of Liability</h3>
                        <p class="text-secondary">
                            The services and all content are provided on an "as-is" and "as-available" basis. To the maximum extent permitted by applicable law:
                        </p>
                        <ul class="legal-list">
                            <li>Darul Ifta Kauzariyya, its scholars, muftis, and developers shall not be liable for any direct, indirect, incidental, or consequential damages resulting from the use or inability to use the platform.</li>
                            <li>The institution is not responsible for misinterpretations, unilateral actions taken based on general archive rulings without context, or technical interruptions.</li>
                        </ul>
                    </div>

                    {{-- 7. Modifications & Updates --}}
                    <div class="legal-section mb-5">
                        <h3 class="legal-heading"><span class="legal-num">7</span> Modifications to Terms</h3>
                        <p class="text-secondary">
                            We reserve the right to revise these Terms at any time. Continued use of our portal after such changes constitutes acceptance of the updated Terms.
                        </p>
                    </div>

                    {{-- 8. Governing Contact --}}
                    <div class="legal-section">
                        <h3 class="legal-heading"><span class="legal-num">8</span> Contact & Governance</h3>
                        <p class="text-secondary">
                            For any inquiries regarding these Terms of Service or institutional matters:
                        </p>
                        <div class="contact-highlight-box p-4 rounded-3 mt-3">
                            <h6 class="fw-bold text-dark mb-2">Darul Ifta Kauzariyya Administration</h6>
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
