<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{$title}}</title>

  <link rel="stylesheet" defer href="{{url('/')}}/admin/plugins/fontawesome-free/css/all.min.css">
  <link rel="preload" as="image" href="{{ asset('admin/uploads/logo/' . $settings->logo) ?? '' }}" fetchpriority="high">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" defer href="{{url('/')}}/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" defer href="{{url('/')}}/admin/dist/css/adminlte.min.css">
  <link rel="icon" type="image/x-icon" href="{{ asset('admin/uploads/logo/' . $settings->logo) ?? '' }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700&display=swap">
  </noscript>
  <style>
    /*=========================================================
=            GOOGLE FONT
=========================================================*/

    body,
    input,
    button,
    textarea {
      font-family: 'Source Sans Pro', sans-serif;
    }

    /*=========================================================
=            BODY
=========================================================*/

    body.login-page {

      margin: 0;

      min-height: 100vh;

      background:
        radial-gradient(circle at top left, #2563eb 0%, transparent 35%),
        radial-gradient(circle at bottom right, #0f172a 0%, transparent 40%),
        linear-gradient(135deg, #071423, #0f3057, #1f4b99);

      overflow-x: hidden;

      position: relative;

      display: flex;

      justify-content: center;

      align-items: center;

      padding: 30px;

    }

    /*=========================================================
=            BACKGROUND ORBS
=========================================================*/

    .background-orbs {

      position: fixed;

      inset: 0;

      overflow: hidden;

      pointer-events: none;

      z-index: 0;

    }

    .background-orbs span {

      position: absolute;

      border-radius: 50%;

      background: rgba(255, 255, 255, .08);

      filter: blur(80px);

      animation: orbFloat 20s linear infinite;

    }

    .background-orbs span:nth-child(1) {

      width: 340px;

      height: 340px;

      top: -60px;

      left: -60px;

    }

    .background-orbs span:nth-child(2) {

      width: 420px;

      height: 420px;

      bottom: -100px;

      right: -120px;

      animation-duration: 28s;

    }

    .background-orbs span:nth-child(3) {

      width: 250px;

      height: 250px;

      top: 45%;

      left: 45%;

      animation-duration: 18s;

    }

    @keyframes orbFloat {

      0% {

        transform: translate(0, 0);

      }

      50% {

        transform: translate(50px, -60px);

      }

      100% {

        transform: translate(0, 0);

      }

    }

    /*=========================================================
=            MAIN WRAPPER
=========================================================*/

    .demo-wrapper {

      width: 100%;

      max-width: 1400px;

      position: relative;

      z-index: 5;

    }

    /*=========================================================
=            MAIN CONTAINER
=========================================================*/

    .demo-container {

      display: flex;

      align-items: stretch;

      justify-content: space-between;

      min-height: 760px;

      border-radius: 30px;

      overflow: hidden;

      background: #ffffff;

      box-shadow:

        0 35px 100px rgba(0, 0, 0, .35),

        0 10px 30px rgba(0, 0, 0, .15);

    }

    /*=========================================================
=            LEFT PANEL
=========================================================*/

    .left-panel {

      width: 50%;

      position: relative;

      background:

        linear-gradient(145deg,

          #071423,

          #0f3057,

          #2563eb);

      color: #fff;

      padding: 25px;

      overflow: hidden;

    }

    .left-overlay {

      position: absolute;

      inset: 0;

      background:

        linear-gradient(rgba(255, 255, 255, .05),

          transparent);

      pointer-events: none;

    }

    .left-panel>* {

      position: relative;

      z-index: 2;

    }

    /*=========================================================
=            RIGHT PANEL
=========================================================*/

    .right-panel {

      width: 50%;

      background: #ffffff;

      display: flex;

      align-items: center;

      justify-content: center;

      padding: 20px;

    }

    /*=========================================================
=            LOGIN PANEL
=========================================================*/

    .login-panel {

      width: 100%;

      max-width: 600px;

    }

    /*=========================================================
=            RESPONSIVE
=========================================================*/

    @media(max-width:1200px) {

      .demo-container {

        min-height: auto;

      }

      .left-panel {

        padding: 40px;

      }

      .right-panel {

        padding: 40px;

      }

    }

    @media(max-width:992px) {

      .demo-container {

        flex-direction: column;

      }

      .left-panel {

        width: 100%;

        padding: 50px 35px;

      }

      .right-panel {

        width: 100%;

        padding: 45px 30px;

      }

    }

    @media(max-width:576px) {

      body.login-page {

        padding: 15px;

      }

      .demo-container {

        border-radius: 18px;

      }

      .left-panel {

        padding: 30px 20px;

      }

      .right-panel {

        padding: 30px 20px;

      }

    }

    /*=========================================================
=            BRAND LOGO
=========================================================*/

    .brand-area {
      margin-bottom: 25px;
    }

    .brand-logo {
      max-width: 260px;
      margin: auto;
      width: 100%;
      height: auto;
      display: block;
      filter: drop-shadow(0 12px 25px rgba(0, 0, 0, .25));
      border-radius: 30px 0 30px 0;
    }

    /*=========================================================
=            HERO CONTENT
=========================================================*/


    .hero-content h1 {

      font-size: 35px;

      font-weight: 700;

      line-height: 1.15;

      color: #fff;

      letter-spacing: -1px;

    }

    .hero-content h1 span {

      color: #8ec5ff;

    }

    .hero-content p {

      color: rgba(255, 255, 255, .85);

      font-size: 15px;

      line-height: 1.8;

      max-width: 600px;

    }

    /*=========================================================
=            FEATURE LIST
=========================================================*/

    .feature-list {

      display: grid;

      grid-template-columns: repeat(2, 1fr);

      gap: 18px;

      margin-bottom: 30px;

    }

    .feature-item {

      display: flex;

      align-items: center;

      padding: 8px;

      background: rgba(255, 255, 255, .08);

      backdrop-filter: blur(18px);

      border: 1px solid rgba(255, 255, 255, .12);

      border-radius: 18px;

      transition: .35s;

    }

    .feature-item:hover {

      transform: translateY(-5px);

      background: rgba(255, 255, 255, .12);

      box-shadow: 0 15px 35px rgba(0, 0, 0, .25);

    }

    .feature-icon {

      width: 60px;

      height: 60px;

      border-radius: 16px;

      display: flex;

      justify-content: center;

      align-items: center;

      font-size: 24px;

      color: #fff;

      margin-right: 15px;

      flex-shrink: 0;

    }

    .feature-item h5 {

      margin: 0 0 5px;

      color: #fff;

      font-size: 18px;

      font-weight: 700;

    }

    .feature-item small {

      color: rgba(255, 255, 255, .75);

      font-size: 14px;

      line-height: 1.5;

    }

    /* Tablet */

    @media(max-width:992px) {

      .feature-list {

        grid-template-columns: 1fr;

      }

    }

    /*=========================================================
=            DASHBOARD PREVIEW
=========================================================*/

    .dashboard-preview {
      position: relative;
    }

    .dashboard-preview img {

      width: 100%;

      border-radius: 22px;

      display: block;

      border: 8px solid rgba(255, 255, 255, .10);

      box-shadow:

        0 25px 60px rgba(0, 0, 0, .35);

      transition: .45s;

    }

    .dashboard-preview img:hover {

      transform: translateY(-8px) scale(1.02);

    }

    /*=========================================================
=            FLOATING CARD EFFECT
=========================================================*/

    .dashboard-preview::before {

      content: "";

      position: absolute;

      inset: -12px;

      border-radius: 28px;

      background: linear-gradient(135deg,
          rgba(255, 255, 255, .12),
          transparent);

      z-index: -1;

    }

    /*=========================================================
=            OPTIONAL STATUS BADGE
=========================================================*/

    .demo-status {

      display: inline-flex;

      align-items: center;

      gap: 10px;

      margin-bottom: 5px;

      padding: 5px 10px;

      border-radius: 40px;

      background: rgba(255, 255, 255, .10);

      border: 1px solid rgba(255, 255, 255, .15);

      color: #fff;

      font-weight: 600;

      font-size: 12px;

    }

    .demo-status::before {

      content: "";

      width: 10px;

      height: 10px;

      border-radius: 50%;

      background: #34d399;

      box-shadow: 0 0 12px #34d399;

    }

    /*=========================================================
=            LEFT PANEL ANIMATION
=========================================================*/

    .brand-area,
    .hero-content,
    .feature-list,
    .dashboard-preview {

      animation: leftFade .9s ease both;

    }

    .hero-content {
      animation-delay: .15s;
    }

    .feature-list {
      animation-delay: .3s;
    }

    .dashboard-preview {
      animation-delay: .45s;
    }

    @keyframes leftFade {

      from {

        opacity: 0;

        transform: translateX(-30px);

      }

      to {

        opacity: 1;

        transform: translateX(0);

      }

    }

    /*=========================================================
=            RESPONSIVE
=========================================================*/

    @media(max-width:992px) {

      .hero-content {

        text-align: center;

      }

      .brand-logo {

        margin: auto;

      }

      .hero-content h1 {

        font-size: 40px;

      }

      .hero-content p {

        max-width: 100%;

      }

      .feature-item {

        padding: 16px;

      }

      .dashboard-preview {

        margin-top: 35px;

      }

    }

    @media(max-width:576px) {

      .hero-content h1 {

        font-size: 32px;

      }

      .hero-content p {

        font-size: 16px;

      }

      .feature-icon {

        width: 50px;

        height: 50px;

        font-size: 18px;

      }

      .feature-item h5 {

        font-size: 16px;

      }

      .feature-item small {

        font-size: 13px;

      }

    }

    /*=========================================================
=            LOGIN LOGO
=========================================================*/

    .login-logo-img {

      max-width: 350px;

      width: 100%;

      height: auto;

      margin: auto;

      display: block;

      transition: .35s;

    }

    .login-logo-img:hover {

      transform: scale(1.03);

    }

    /*=========================================================
=            LOGIN HEADING
=========================================================*/

    .login-heading {

      margin-bottom: 15px;

    }

    .login-heading h3 {

      font-size: 34px;

      font-weight: 700;

      color: #111827;

    }

    .login-heading p {

      color: #6b7280;

      font-size: 16px;

      margin-bottom: 0;

    }

    /*=========================================================
=            INPUTS
=========================================================*/

    .form-group {

      margin-bottom: 22px;

    }

    .input-modern {

      border-radius: 14px;

      overflow: hidden;

      border: 1px solid #dbe3ef;

      transition: .35s;

      background: #fff;

    }

    .input-modern:focus-within {

      border-color: #2563eb;

      box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);

    }

    .input-modern .input-group-text {

      background: #fff;

      border: none;

      color: #2563eb;

      width: 54px;

      justify-content: center;

      font-size: 18px;

    }

    .input-modern .form-control {

      border: none;

      height: 56px;

      font-size: 15px;

      box-shadow: none;

    }

    .input-modern .form-control::placeholder {

      color: #9ca3af;

    }

    .password-toggle {

      cursor: pointer;

      transition: .3s;

    }

    .password-toggle:hover {

      background: #f3f4f6;

    }

    /*=========================================================
=            CAPTCHA
=========================================================*/
    .captcha-row {

      display: flex;

      align-items: center;

      gap: 12px;

    }

    .captcha-image {

      background: #f8fafc;

      border: 1px solid #dbe3ef;

      border-radius: 12px;

      padding: 6px;

      flex-shrink: 0;

    }

    .captcha-image img {

      display: block;

      height: 46px;

    }

    .btn-refresh {

      width: 48px;

      height: 48px;

      border-radius: 12px;

      display: flex;

      align-items: center;

      justify-content: center;

      padding: 0;

      flex-shrink: 0;

    }

    .captcha-input {

      flex: 1;

    }

    .captcha-input .form-control {

      height: 48px;

    }

    @media(max-width:576px) {

      .captcha-row {

        flex-direction: column;

        align-items: stretch;

      }

      .captcha-image {

        text-align: center;

      }

      .btn-refresh {

        width: 100%;

      }

    }

    /*=========================================================
=            LOGIN BUTTON
=========================================================*/

    .btn-login {

      height: 56px;

      border: none;

      border-radius: 14px;

      background: linear-gradient(135deg, #2563eb, #1d4ed8);

      color: #fff;

      font-size: 17px;

      font-weight: 700;

      transition: .35s;

      box-shadow: 0 18px 35px rgba(37, 99, 235, .25);

    }

    .btn-login:hover {

      transform: translateY(-3px);

      box-shadow: 0 24px 45px rgba(37, 99, 235, .35);

    }

    /*=========================================================
=            DEMO CARD
=========================================================*/

    .demo-info-card {

      background: #f8fafc;

      border: 1px solid #e5e7eb;

      border-radius: 20px;

      padding: 10px;

    }

    .demo-title {

      font-size: 18px;

      font-weight: 700;

      color: #111827;

    }

    .demo-title i {

      color: #2563eb;

      margin-right: 8px;

    }

    .demo-subtitle {

      font-weight: 700;

      margin-bottom: 18px;

      color: #111827;

    }

    /*=========================================================
=            CREDENTIAL BOX
=========================================================*/

    .credential-box {

      margin-bottom: 16px;

    }

    .credential-box label {

      display: block;

      font-size: 13px;

      color: #6b7280;

      margin-bottom: 6px;

    }

    .credential-item {

      display: flex;

      justify-content: space-between;

      align-items: center;

      background: #fff;

      border: 1px solid #dbe3ef;

      border-radius: 12px;

      padding: 12px 15px;

    }

    .credential-item span {

      font-weight: 600;

      color: #111827;

    }

    .copy-btn {

      border: none;

      background: none;

      color: #2563eb;

      font-size: 18px;

      transition: .3s;

    }

    .copy-btn:hover {

      transform: scale(1.15);

    }

    /*=========================================================
=            NOTES
=========================================================*/

    .demo-notes {

      list-style: none;

      margin: 0;

      padding: 0;

    }

    .demo-notes li {

      margin-bottom: 15px;

      display: flex;

      align-items: flex-start;

      gap: 10px;

      color: #374151;

      line-height: 1.6;

    }

    .demo-footer-message {

      margin-top: 18px;

      padding: 15px;

      background: #e8f1ff;

      border-radius: 12px;

      color: #1d4ed8;

      font-weight: 600;

      text-align: center;

    }

    /*=========================================================
=            FOOTER
=========================================================*/

    .demo-footer {

      display: flex;

      justify-content: center;

      align-items: center;

      flex-wrap: wrap;

      gap: 18px;

      margin-top: 30px;

      color: #fff;

      font-size: 15px;

    }

    .footer-item {

      display: flex;

      align-items: center;

      gap: 8px;

    }

    .footer-divider {

      opacity: .5;

    }

    /*=========================================================
=            ANIMATIONS
=========================================================*/

    .right-panel {

      animation: rightFade .8s ease;

    }

    @keyframes rightFade {

      from {

        opacity: 0;

        transform: translateX(30px);

      }

      to {

        opacity: 1;

        transform: translateX(0);

      }

    }

    /*=========================================================
=            MOBILE
=========================================================*/

    @media(max-width:992px) {

      .login-panel {

        max-width: 100%;

      }

      .demo-info-card {

        margin-top: 35px;

      }

      .demo-footer {

        font-size: 14px;

        text-align: center;

      }

    }

    @media(max-width:768px) {

      .login-heading h3 {

        font-size: 28px;

      }

      .credential-item {

        flex-direction: column;

        align-items: flex-start;

        gap: 10px;

      }

      .copy-btn {

        align-self: flex-end;

      }

      .demo-footer {

        flex-direction: column;

        gap: 10px;

      }

    }

    @media(max-width:576px) {

      .login-heading h3 {

        font-size: 24px;

      }

      .input-modern .form-control {

        height: 52px;

      }

      .btn-login {

        height: 52px;

      }

      .demo-info-card {

        padding: 18px;

      }

    }
  </style>
</head>
<div class="background-orbs">
  <span></span>
  <span></span>
  <span></span>
</div>

<body class="hold-transition login-page">

  <div class="background-orbs">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <div class="demo-wrapper">

    <div class="demo-container">

      <!-- ========================================================= -->
      <!-- LEFT SIDE -->
      <!-- ========================================================= -->

      <div class="left-panel">

        <div class="left-overlay"></div>

        <!-- Logo -->

        <div class="brand-area">

          @if(!empty($settings->logo))

            <img src="{{ asset('admin/uploads/logo/' . $settings->logo) }}" class="brand-logo"
              alt="{{ $settings->company_name }}">

          @else

            <img src="{{ asset('admin/uploads/logo/Demo.png') }}" class="brand-logo" alt="DemoNest">

          @endif

        </div>

        <!-- Heading -->
        <div class="demo-status">
          Live Demo Environment
        </div>
        <div class="hero-content">

          <h1>

            Powerful Dashboard |

            <span>Real Experience.</span>

          </h1>

          <p>

            Explore every module of our application in a fully
            functional demonstration environment.

          </p>

        </div>

        <!-- Features -->

        <div class="feature-list">

          <div class="feature-item">

            <div class="feature-icon bg-primary">

              <i class="fas fa-desktop"></i>

            </div>

            <div>

              <h5>Full Access</h5>

              <small>
                Explore all modules and functionality.
              </small>

            </div>

          </div>

          <div class="feature-item">

            <div class="feature-icon bg-success">

              <i class="fas fa-sync-alt"></i>

            </div>

            <div>

              <h5>Auto Reset</h5>

              <small>

                Database resets automatically every 24 hours.

              </small>

            </div>

          </div>

          <div class="feature-item">

            <div class="feature-icon bg-warning">

              <i class="fas fa-shield-alt"></i>

            </div>

            <div>

              <h5>Safe & Secure</h5>

              <small>

                No real production data is stored.

              </small>

            </div>

          </div>

          <div class="feature-item">

            <div class="feature-icon bg-info">

              <i class="fas fa-rocket"></i>

            </div>

            <div>

              <h5>Live Experience</h5>

              <small>

                Experience the complete application flow.

              </small>

            </div>

          </div>

        </div>

        <!-- Dashboard Preview -->

        <div class="dashboard-preview">

          {{-- Replace with your CRM screenshot --}}
          <img src="{{ asset('admin/uploads/logo/demo-dashboard.png') }}" class="img-fluid" alt="Dashboard Preview"
            onerror="this.src='https://placehold.co/700x420/1e3a8a/ffffff?text=Dashboard+Preview';">

        </div>

      </div>

      <!-- ========================================================= -->
      <!-- RIGHT SIDE -->
      <!-- ========================================================= -->

      <div class="right-panel">

        <div class="login-panel">

          <div class="text-center mb-4">

            @if(!empty($settings->logo))

              <img src="{{ asset('admin/uploads/logo/' . $settings->logo) }}" class="login-logo-img"
                alt="{{ $settings->company_name }}">

            @else

              <img src="{{ asset('admin/uploads/logo/Demo.png') }}" class="login-logo-img" alt="DemoNest">

            @endif

          </div>

          <div class="login-heading text-center">

            <h3>

              <strong>Management</strong> Login

            </h3>

            <p>

              Sign in to continue to your dashboard

            </p>

          </div>

          <!-- Login Form Starts Here -->
          <form action="{{ route('Checklogin') }}" method="POST">

            @csrf

</html>
{{-- ========================================================= --}}
{{-- EMAIL --}}
{{-- ========================================================= --}}

<div class="form-group">

  <div class="input-group input-modern">

    <div class="input-group-prepend">

      <span class="input-group-text">

        <i class="far fa-envelope"></i>

      </span>

    </div>

    <input type="email" name="email" id="email" class="form-control" placeholder="Email address" autocomplete="username"
      required>

  </div>

</div>

{{-- ========================================================= --}}
{{-- PASSWORD --}}
{{-- ========================================================= --}}

<div class="form-group">

  <div class="input-group input-modern">

    <div class="input-group-prepend">

      <span class="input-group-text">

        <i class="fas fa-lock"></i>

      </span>

    </div>

    <input type="password" name="password" id="password" class="form-control" placeholder="Password"
      autocomplete="current-password" required>

    <div class="input-group-append">

      <span class="input-group-text password-toggle" style="cursor:pointer;">

        <i class="far fa-eye" id="togglePassword"></i>

      </span>

    </div>

  </div>

</div>

{{-- ========================================================= --}}
{{-- CAPTCHA --}}
{{-- ========================================================= --}}

<div class="form-group">

  <div class="captcha-row">

    <!-- Captcha Image -->
    <div class="captcha-image">
      <span>
        {!! captcha_img('math') !!}
      </span>
    </div>

    <!-- Refresh Button -->
    <button type="button" class="btn btn-refresh">

      <i class="fas fa-sync-alt"></i>

    </button>

    <!-- Captcha Input -->
    <div class="input-group input-modern captcha-input">

      <div class="input-group-prepend">
        <span class="input-group-text">
          <i class="fas fa-shield-alt"></i>
        </span>
      </div>

      <input type="text" class="form-control" name="captcha" id="captcha" placeholder="Enter CAPTCHA" required>

    </div>

  </div>

</div>

{{-- ========================================================= --}}
{{-- LOGIN BUTTON --}}
{{-- ========================================================= --}}
<div class="form-group">

  <div class="custom-control custom-checkbox">

    <input class="custom-control-input" type="checkbox" id="remember" name="remember">

    <label class="custom-control-label" for="remember">

      Remember Me

    </label>

  </div>

</div>
<div class="form-group mb-4">

  <button type="submit" class="btn btn-login btn-block">

    <i class="fas fa-sign-in-alt mr-2"></i>

    Secure Login

  </button>

</div>

</form>
{{-- ========================================================= --}}
{{-- DEMO INFORMATION --}}
{{-- ========================================================= --}}

<div class="demo-info-card">

  <div class="demo-title">

    <i class="fas fa-info-circle"></i>

    Demo Information

  </div>

  <div class="row mt-3">

    <div class="col-md-6">

      <h6 class="demo-subtitle">

        Demo Credentials

      </h6>

      <div class="credential-box">

        <label>Email</label>

        <div class="credential-item">
          <span>
            {{ $demoUser->email ?? 'test@crmsystem.com' }}
          </span>
          <button type="button" class="copy-btn" data-copy="{{ $demoUser->email ?? 'test@crmsystem.com'}}">
            <i class=" far fa-copy"></i>

          </button>

        </div>

      </div>

      <div class="credential-box">

        <label>Password</label>

        <div class="credential-item">

          <span>

            {{ $demoUser->core_password ?? '12345678' }}

          </span>

          <button type="button" class="copy-btn" data-copy="{{ $demoUser->core_password ?? '12345678' }}">

            <i class="far fa-copy"></i>

          </button>

        </div>

      </div>

    </div>

    <div class="col-md-6">

      <h6 class="demo-subtitle">

        Important Notes

      </h6>

      <ul class="demo-notes">

        <li>

          <i class="fas fa-exclamation-triangle text-warning"></i>

          This is a demonstration environment.

        </li>

        <li>

          <i class="fas fa-sync-alt text-primary"></i>

          Database resets every 24 hours.

        </li>

        <li>

          <i class="fas fa-trash text-danger"></i>

          Uploaded data is temporary.

        </li>

        <li>

          <i class="fas fa-lock text-success"></i>

          No registration required.

        </li>

      </ul>

    </div>

  </div>

  <div class="demo-footer-message">

    🎉 Feel free to explore all features. Enjoy your demo!

  </div>

</div>{{-- ========================================================= --}}
{{-- LOGIN PANEL END --}}
{{-- ========================================================= --}}

</div>
<!-- /.login-panel -->

</div>
<!-- /.right-panel -->

</div>
<!-- /.demo-container -->

{{-- ========================================================= --}}
{{-- FOOTER --}}
{{-- ========================================================= --}}

<div class="demo-footer">

  <div class="footer-item">

    <i class="fas fa-globe"></i>

    <span>

      {{ $settings->company_name ?? 'Demo Company' }}

    </span>

  </div>

  <span class="footer-divider">|</span>

  <div class="footer-item">

    <i class="fas fa-phone-alt"></i>

    <span>

      {{ $settings->mobile ?? '+91 98765 43210' }}

    </span>

  </div>

  <span class="footer-divider">|</span>

  <div class="footer-item">

    <i class="fas fa-map-marker-alt"></i>

    <span>

      {{ $settings->address ?? 'Demo Business Park, India' }}

    </span>

  </div>

</div>

</div>
<!-- /.demo-wrapper -->

@include('sweetalert::alert')

<!-- ========================================================= -->
<!-- SCRIPTS -->
<!-- ========================================================= -->

<script src="{{ url('/') }}/admin/plugins/jquery/jquery.min.js"></script>

<script src="{{ url('/') }}/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="{{ url('/') }}/admin/dist/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
  toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "2500",
    "extendedTimeOut": "1000",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut",
    "preventDuplicates": true
  };
  document.querySelectorAll('.copy-btn').forEach(button => {

    button.addEventListener('click', function () {

      navigator.clipboard.writeText(this.dataset.copy);

      toastr.success('Copied successfully');

    });

  });
</script>
</body>

</html>