<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{$title}}</title>
    <link rel="icon" type="image/x-icon" href="{{url('/')}}/admin/uploads/logo/{{($settings->logo)}}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{url('/')}}/admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{url('/')}}/admin/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <!-- <link rel="stylesheet" href="{{url('/')}}/admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css"> -->
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/summernote/summernote-bs4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="preload" href="{{url('/')}}/admin/plugins/fontawesome-free/webfonts/fa-solid-900.woff2" as="font"
        type="font/woff2" crossorigin>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!--================ Vendor JS ================-->
    {{-- Vite CSS --}}
    {{-- {{ module_vite('build-admin', 'resources/assets/sass/app.scss') }} --}}
    <!-- jQuery -->
    <script src="{{url('/')}}/admin/plugins/jquery/jquery.min.js"></script>
    <style>
        .main-header.navbar {
            min-height: 57px;
        }

        #customerTabs .nav-link {
            cursor: pointer;
        }

        .dashboard-card {
            min-height: 130px;
        }

        .skeleton {
            background: linear-gradient(90deg, #1e293b 25%, #334155 37%, #1e293b 63%);
            background-size: 400% 100%;
            animation: shimmer 1.4s infinite;
        }

        .small-btn {
            width: 36px;
            height: 36px;
            line-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .card-teal {
            /* background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%) !important; */

        }

        /* ===== LOADER BACKGROUND ===== */
        .loader-bg {
            background: linear-gradient(120deg,
                    #081a2d,
                    #0f3057,
                    #1b4f72,
                    #0a2540) !important;
            background-size: 400% 400%;
            animation: gradientShift 10s ease infinite;
        }

        /* ===== LOGO CONTAINER ===== */
        .logo-wrapper {
            position: relative;
            width: 260px;
            height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== LOGO ===== */
        .loader-logo {
            width: 200px;
            height: auto;
            animation: logoFloat 3s ease-in-out infinite,
                logoPulse 2.5s ease-in-out infinite;
            z-index: 2;
        }

        /* ===== GLOW RING ===== */
        .logo-wrapper::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle,
                    rgba(56, 189, 248, 0.35),
                    rgba(56, 189, 248, 0.05),
                    transparent 70%);
            animation: glowPulse 3s ease-in-out infinite;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes logoFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes logoPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.03);
            }
        }

        @keyframes glowPulse {

            0%,
            100% {
                opacity: 0.4;
                transform: scale(0.95);
            }

            50% {
                opacity: 0.7;
                transform: scale(1.05);
            }
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .loader-text {
            color: #cbd5f5;
            letter-spacing: 1px;
            font-size: 14px;
            animation: fadePulse 1.8s ease-in-out infinite;
        }

        @keyframes fadePulse {

            0%,
            100% {
                opacity: 0.4;
            }

            50% {
                opacity: 1;
            }
        }

        /* ===== MAIN WINDOW SCROLLBAR ONLY ===== */
        html::-webkit-scrollbar {
            width: 12px;
        }

        html::-webkit-scrollbar-track {
            background: #081a2d;
        }

        html::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom,
                    #081a2d 0%,
                    #0f3057 60%,
                    #1b4f72 100%);
            border-radius: 8px;
            border: 2px solid #081a2d;
        }

        html::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom,
                    #0f3057,
                    #1b4f72);
        }

        .dropdown-menu-lg {
            width: 320px !important;
            max-width: 320px;
        }

        .dropdown-item {
            white-space: normal !important;
            word-break: break-word;
            line-height: 1.4;
        }

        .dropdown-item strong {
            display: block;
            font-size: 14px;
        }

        .dropdown-item .text-sm {
            font-size: 12px;
            color: #6c757d;
        }
    </style>
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
    <div class="wrapper">
        @yield('navbar')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        @yield('footer')
    </div>

    <script>
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();
            const $el = $(this);
            const itemName = $el.data('name') || 'this item';

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${itemName}. This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                focusCancel: true,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const $form = $el.closest('form');
                    if ($form.length) {
                        $form.trigger('submit');
                        return;
                    }
                    const href = $el.attr('href');
                    if (href) window.location.href = href;
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your item is safe.',
                        icon: 'info',
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
            });
        });
    </script>
    @include('sweetalert::alert')
    <!-- jQuery UI 1.11.4 -->
    <script src="{{url('/')}}/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="{{url('/')}}/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- ChartJS -->
    <script src="{{url('/')}}/admin/plugins/chart.js/Chart.min.js"></script>
    <!-- daterangepicker -->
    <script src="{{url('/')}}/admin/plugins/moment/moment.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{url('/')}}/admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="{{url('/')}}/admin/plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <!-- <script src="{{url('/')}}/admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> -->
    <!-- AdminLTE App -->
    <script src="{{url('/')}}/admin/dist/js/adminlte.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <audio id="notificationSound" preload="auto">
        <source src="{{ asset('admin/uploads/notification.mp3') }}" type="audio/mpeg">
    </audio>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            let lastCount = sessionStorage.getItem('notification_count') ?? 0;
            let currentCount = {{ auth()->user()->unreadNotifications->count() }};

            if (currentCount > lastCount) {
                document.getElementById('notificationSound').play();
            }

            sessionStorage.setItem('notification_count', currentCount);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        });
    </script>
    <!-- @if(auth()->check())
        @php
            $notification = auth()->user()->unreadNotifications->first();
        @endphp

        @if($notification)
            <script>
                Swal.fire({
                    icon: "{{ $notification->data['type'] ?? 'info' }}",
                    title: "{{ $notification->data['title'] ?? 'Notification' }}",
                    text: "{{ $notification->data['message'] ?? '' }}",
                    confirmButtonText: "View"
                }).then(() => {
                });
            </script>

            @php
                $notification->markAsRead();
            @endphp
        @endif
    @endif -->

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}",
                timer: 4000,
                showConfirmButton: true
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `
                                                                                                                                                                                                    <ul style="text-align: left;">
                                                                                                                                                                                                        @foreach ($errors->all() as $error)
                                                                                                                                                                                                              <li>{{ $error }}</li>
                                                                                                                                                                                                        @endforeach
                                                                                                                                                                                                    </ul>
                                                                                                                                                                                                `,
                confirmButtonText: 'Okay'
            });
        </script>
    @endif
    @if(session('demo_notice'))

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                Swal.fire({

                    width: 720,

                    background: '#ffffff',

                    confirmButtonColor: '#2563eb',

                    confirmButtonText: '<i class="fas fa-rocket mr-1"></i> Start Exploring',

                    showCloseButton: true,

                    allowOutsideClick: false,

                    allowEscapeKey: false,

                    html: `

            <div style="padding:10px 15px;">

                <div style="
                    width:85px;
                    height:85px;
                    margin:auto;
                    border-radius:50%;
                    background:linear-gradient(135deg,#2563eb,#1d4ed8);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    color:white;
                    font-size:36px;
                    box-shadow:0 15px 35px rgba(37,99,235,.35);
                ">

                    🚀

                </div>

                <h2 style="
                    margin-top:25px;
                    margin-bottom:8px;
                    color:#111827;
                    font-weight:700;
                ">

                    Welcome to the CRM Demo

                </h2>

                <p style="
                    color:#6b7280;
                    font-size:16px;
                    margin-bottom:25px;
                ">

                    Experience every feature with complete freedom.

                </p>

                <div style="
                    background:#f8fafc;
                    border:1px solid #e5e7eb;
                    border-radius:16px;
                    padding:20px;
                    text-align:left;
                ">

                    <div style="display:flex;margin-bottom:18px;">

                        <div style="
                            width:42px;
                            height:42px;
                            background:#dbeafe;
                            border-radius:12px;
                            display:flex;
                            justify-content:center;
                            align-items:center;
                            margin-right:15px;
                            font-size:20px;
                        ">
                            🔄
                        </div>

                        <div>

                            <strong>Database Auto Reset</strong>

                            <div style="color:#6b7280;font-size:14px;">
                                All records are automatically deleted every 24 hours.
                            </div>

                        </div>

                    </div>

                    <div style="display:flex;margin-bottom:18px;">

                        <div style="
                            width:42px;
                            height:42px;
                            background:#dcfce7;
                            border-radius:12px;
                            display:flex;
                            justify-content:center;
                            align-items:center;
                            margin-right:15px;
                            font-size:20px;
                        ">
                            🔒
                        </div>

                        <div>

                            <strong>Safe Demo Environment</strong>

                            <div style="color:#6b7280;font-size:14px;">
                                Please avoid uploading confidential or sensitive information.
                            </div>

                        </div>

                    </div>

                    <div style="display:flex;">

                        <div style="
                            width:42px;
                            height:42px;
                            background:#fef3c7;
                            border-radius:12px;
                            display:flex;
                            justify-content:center;
                            align-items:center;
                            margin-right:15px;
                            font-size:20px;
                        ">
                            ⭐
                        </div>

                        <div>

                            <strong>Everything is Unlocked</strong>

                            <div style="color:#6b7280;font-size:14px;">
                                Feel free to explore all modules, dashboards, reports and workflows.
                            </div>

                        </div>

                    </div>

                </div>

                <div style="
                    margin-top:22px;
                    padding:16px;
                    border-radius:14px;
                    background:linear-gradient(135deg,#2563eb,#1d4ed8);
                    color:white;
                    font-size:15px;
                    font-weight:600;
                ">

                    🎉 Enjoy exploring the Demo CRM. Have fun!

                </div>

            </div>

            `

                });

            });
        </script>

    @endif
    @stack('scripts')
</body>

</html>