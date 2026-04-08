@extends('layouts.public')
@section('title', 'Contact Us')
@section('content')

<style>
    /* Contact Page Custom Styles */
    .contact-hero {
        background: linear-gradient(135deg, var(--brand-blue-dark) 0%, var(--brand-blue) 100%);
        padding: 80px 0 160px;
        position: relative;
        overflow: hidden;
        color: white;
        text-align: center;
    }
    .contact-hero::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
        background-size: 30px 30px;
    }
    .contact-hero .motif {
        position: absolute;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
    .motif-1 { top: 15%; left: 10%; animation: float 6s ease-in-out infinite; }
    .motif-2 { top: 40%; right: 8%; animation: float 8s ease-in-out infinite; }
    .motif-3 { bottom: 20%; left: 15%; animation: float 7s ease-in-out infinite; }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    .contact-body-wrapper {
        margin-top: -100px;
        position: relative;
        z-index: 10;
        padding-bottom: 80px;
    }

    .contact-glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 2rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .contact-info-panel {
        background: #f8fafc;
        padding: 3rem;
        height: 100%;
    }

    .contact-form-panel {
        padding: 3rem;
    }

    .info-item {
        margin-bottom: 2.5rem;
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
    }

    .info-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: white;
        color: var(--brand-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: var(--shadow-sm);
        flex-shrink: 0;
    }

    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: var(--brand-blue);
    }
    
    .btn-contact-send {
        background: var(--brand-blue);
        color: white;
        padding: 0.8rem 2.5rem;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: var(--transition-base);
        border: none;
    }
    .btn-contact-send:hover {
        background: var(--brand-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(41, 90, 117, 0.2);
        color: white;
    }

    .map-container {
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        height: 450px;
        margin-top: 50px;
    }

    .wave-divider {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        line-height: 0;
    }

    @media (max-width: 991px) {
        .contact-body-wrapper { margin-top: -60px; }
        .contact-hero { padding: 60px 0 120px; }
        .contact-info-panel, .contact-form-panel { padding: 2rem; }
    }
</style>

{{-- Hero Section --}}
<section class="contact-hero">
    <div class="motif motif-1"><i class="bi bi-book text-white-50"></i></div>
    <div class="motif motif-2"><i class="bi bi-chat-heart text-white-50"></i></div>
    <div class="motif motif-3 text-white-50" style="font-size: 4rem;">&#1593;</div>

    <div class="container position-relative">
        <h1 class="display-4 fw-bold mb-3">Get in Touch</h1>
        <p class="lead opacity-75 mx-auto" style="max-width: 600px;">
            Let’s start a scholarly dialogue. Contact our administrative team for any inquiries or institutional guidance.
        </p>
    </div>

    <div class="wave-divider">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#F8F4F2" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,213.3C672,224,768,224,864,197.3C960,171,1056,117,1152,106.7C1248,96,1344,128,1392,144L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</section>

{{-- Contact Body --}}
<div class="container contact-body-wrapper">
    <div class="contact-glass-card">
        <div class="row g-0">
            {{-- Info Sidebar --}}
            <div class="col-lg-5">
                <div class="contact-info-panel">
                    <h3 class="fw-bold mb-4" style="color: var(--brand-blue);">Contact Details</h3>
                    
                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-clock"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Office Hours</h6>
                            <p class="text-muted mb-0">Monday — Saturday<br>8:30 AM — 6:30 PM</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-envelope-at"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Email Inquiry</h6>
                            <p class="text-muted mb-0">darulifta.kauzariyya@gmail.com</p>
                            <small class="text-brand-gold-dark fw-medium">Direct Admin Support</small>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Our Location</h6>
                            <p class="text-muted mb-0">Al Jamiathul Kauzariyya,<br>Edathala, Aluva, Kerala</p>
                        </div>
                    </div>

                    <hr class="my-5 opacity-10">

                    <div class="p-4 bg-white rounded-4 border border-light">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-info-circle-fill me-2 text-brand"></i>
                            For specific Islamic questions, we recommend using the <a href="{{ route('question.create') }}" class="text-brand fw-bold">Online Fatwa Portal</a> for prioritized scholarly response.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form Panel --}}
            <div class="col-lg-7">
                <div class="contact-form-panel">
                    <h3 class="fw-bold mb-4">Send a Message</h3>
                    
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" name="first_name" class="form-control border-0 bg-light rounded-4" id="fName" placeholder="First Name" required value="{{ old('first_name') }}">
                                    <label for="fName">First Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" name="last_name" class="form-control border-0 bg-light rounded-4" id="lName" placeholder="Last Name" required value="{{ old('last_name') }}">
                                    <label for="lName">Last Name</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control border-0 bg-light rounded-4" id="email" placeholder="Email Address" required value="{{ old('email') }}">
                            <label for="email">Email Address</label>
                        </div>

                        <div class="form-floating mb-4">
                            <textarea name="message" class="form-control border-0 bg-light rounded-4" id="message" placeholder="Your Message" style="height: 180px" required>{{ old('message') }}</textarea>
                            <label for="message">How can we help you?</label>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn-contact-send">
                                <i class="bi bi-send-fill me-2"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Map Section --}}
    <div class="map-container">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1964.1501344485552!2d76.3691653!3d10.0731824!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3b0809519b0b4bc9%3A0x4fa0e984ac83357e!2sAl%20Jamiathul%20Kauzariyya!5e0!3m2!1sen!2sin!4v1712555000000!5m2!1sen!2sin" 
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>

@endsection
