<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keywords" content="admin,dashboard" />
    <meta name="author" content="EmpMonitor" />
    <title>
        @if ((new App\Modules\User\helper())->checkHost())
            {{ env('WEBSITE_TITLE') }} |
        @endif SignIn
    </title>
    <link rel="icon" href="../assets/images/favicons/{{ md5($_SERVER['HTTP_HOST']) }}.png" />
    <link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="../assets/plugins/css/icomoon.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet">

    <link href="../assets/css/concept.css" rel="stylesheet" />
    <link href="../assets/css/custom.css" rel="stylesheet" />
    <link href="../assets/css/new-style.css" rel="stylesheet" />
    
    <style>
        /* Enhanced CSS with animations */
        body.login-whole-wrapper {
            font-family: 'Montserrat', sans-serif;
            overflow: hidden;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        
        .admin-login-wrapper {
            position: relative;
            z-index: 2;
        }
        
        .jumbotron {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            width: 90%;
            max-width: 450px;
            animation: fadeIn 1s ease, slideUp 0.8s ease;
            transform-origin: center;
            transition: all 0.3s ease;
        }
        
        .jumbotron:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .login-logo {
            max-width: 180px;
            margin: 0 auto 1.5rem;
            display: block;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .login-text {
            color: #333;
            font-weight: 700;
            margin-bottom: 1.8rem;
            position: relative;
            padding-bottom: 15px;
        }
        
        .login-text::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #23a6d5, #e73c7e);
            border-radius: 3px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
            animation: fadeIn 0.8s ease forwards;
            opacity: 0;
            animation-delay: 0.3s;
        }
        
        .input-group {
            margin-bottom: 1.5rem;
            animation: fadeIn 0.8s ease forwards;
            opacity: 0;
            animation-delay: 0.4s;
        }
        
        .form-control {
            border: none;
            border-bottom: 2px solid #ddd;
            border-radius: 0;
            padding: 12px 15px;
            background: transparent;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-bottom-color: #23a6d5;
            background: rgba(35, 166, 213, 0.05);
        }
        
        .input-group-append .btn {
            border: none;
            border-bottom: 2px solid #ddd;
            border-radius: 0;
            background: transparent;
            transition: all 0.3s;
        }
        
        .input-group:focus-within .input-group-append .btn {
            border-bottom-color: #23a6d5;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #23a6d5, #e73c7e);
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            animation: fadeIn 0.8s ease forwards;
            opacity: 0;
            animation-delay: 0.5s;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(35, 166, 213, 0.4);
            background: linear-gradient(45deg, #e73c7e, #23a6d5);
        }
        
        .btn-primary:active {
            transform: translateY(1px);
        }
        
        #MngrEmpLogin, #LogInBtn {
            animation: fadeIn 0.8s ease forwards;
            opacity: 0;
            animation-delay: 0.6s;
        }
        
        #LogInBtn {
            color: #23a6d5;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        #LogInBtn:hover {
            color: #e73c7e;
            text-decoration: underline;
        }
        
        .invalid_authentication, .error {
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
        
        .modal-content {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            animation: zoomIn 0.3s ease;
        }
        
        @keyframes zoomIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .modal-header {
            background: linear-gradient(45deg, #23a6d5, #e73c7e);
            color: white;
        }
        
        .modal-footer .btn {
            border-radius: 50px;
            padding: 8px 20px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .jumbotron {
                padding: 1.5rem;
                width: 95%;
            }
        }
    </style>
</head>

<body class="login-whole-wrapper">
    <div id="particles-js">
        <canvas class="particles-js-canvas-el" style="width: 100%; height: 100%;" width="1366" height="312"></canvas>
    </div>

    <div class="page-container h-100 d-flex justify-content-center admin-login-wrapper">
        <div class="jumbotron my-auto pt-3">
            <img src="https://empmonitor.com/wp-content/uploads/2023/12/emp.webp" class="login-logo w-100 mb-4" />
            <h1 class="display-6 text-center login-text" id="loginTitle">Log in</h1>
            @if (Session::has('error'))
                <p class="invalid_authentication">{{ Session::get('error') }}</p>
            @endif
            <form method="post" action="admin-login">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" name="username" aria-describedby="emailHelp"
                        placeholder="Enter Username" />
                    <p class="error" style="color: red">{{ $errors->first('username') }}</p>
                </div>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                    <div class="input-group-append">
                        <span toggle="#password"
                            style="line-height: 1.5;border-bottom: 1.3px solid gainsboro;border-left-color: white;cursor:pointer;"
                            class="btn btn-default fas fa-eye toggle-password"></span>
                    </div>

                </div>
                <p class="error" style="color: red">{{ $errors->first('password') }}</p>
                <button type="submit" class="btn btn-primary btn-block" id="loginButtonDiv">Login</button>
            </form>
            <a style="float: right" id="LogInBtn" href="/login">&nbsp; Login</a><span id="MngrEmpLogin"
                style="float: right">Employee?</span>
        </div>
    </div>
    <div class="modal fade" id="adminForgotPasswdModal" tabindex="-1" role="dialog"
        aria-labelledby="forgotPasswdModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminForgotPasswdModalTitle">
                        Forgot Password
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="error" style="color: red;">{{ $errors->first('fg-pwd-error') }}</div>
                <div class="modal-body">
                    <div class="form-group">
                        <label id="level_admin_email_id_forgot" for="admin_email_id_forgot">Email address</label>
                        <input type="email" class="form-control" id="admin_email_id_forgot"
                            aria-describedby="emailHelp" placeholder="Enter email" required />
                        <div class="error text-danger" id="forgot_admin_email_error"></div>
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                            else.</small>
                    </div>
                    <div class="custom-control text-center custom-checkbox form-group">
                        <input type="checkbox" class="custom-control-input" id="isNewClient" checked>
                        <label id="areNewClient" class="custom-control-label" for="isNewClient">Are you a New
                            Client</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="AdminForgetpassCancel" type="button" class="btn btn-secondary"
                        data-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="admin-forgot-pwd-btn" onclick="adminForgotPassword()"
                        class="btn btn-primary">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Javascripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="../assets/js/concept.min.js"></script>
    <script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3744450/particles.js"></script>
    <script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3744450/particles.min.js"></script>
    
    <script type="text/javascript">
        // Enhanced particles configuration
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS("particles-js", {
                particles: {
                    number: { value: 80, density: { enable: true, value_area: 800 } },
                    color: { value: "#ffffff" },
                    shape: {
                        type: "circle",
                        stroke: { width: 0, color: "#000000" },
                        polygon: { nb_sides: 5 }
                    },
                    opacity: {
                        value: 0.5,
                        random: false,
                        anim: { enable: false, speed: 1, opacity_min: 0.1, sync: false }
                    },
                    size: {
                        value: 3,
                        random: true,
                        anim: { enable: false, speed: 40, size_min: 0.1, sync: false }
                    },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: "#ffffff",
                        opacity: 0.4,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 3,
                        direction: "none",
                        random: false,
                        straight: false,
                        out_mode: "out",
                        bounce: false,
                        attract: { enable: false, rotateX: 600, rotateY: 1200 }
                    }
                },
                interactivity: {
                    detect_on: "canvas",
                    events: {
                        onhover: { enable: true, mode: "repulse" },
                        onclick: { enable: true, mode: "push" },
                        resize: true
                    },
                    modes: {
                        grab: { distance: 400, line_linked: { opacity: 1 } },
                        bubble: { distance: 400, size: 40, duration: 2, opacity: 8, speed: 3 },
                        repulse: { distance: 100, duration: 0.4 },
                        push: { particles_nb: 4 },
                        remove: { particles_nb: 2 }
                    }
                },
                retina_detect: true
            });
            
            // Add subtle animation to form elements on page load
            $('.form-control').each(function(i) {
                $(this).delay(i * 100).queue(function() {
                    $(this).addClass('animate__animated animate__fadeIn');
                    $(this).dequeue();
                });
            });
        });
        
        $(document).ready(function() {
            $('.toggle-password').addClass('fa-eye-slash');
            $('#password').attr('type', 'password');
        });
        
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            let input = $($(this).attr("toggle"));
            if (input.attr("type") === "password") {
                $('.toggle-password').addClass('fa-eye')
                $('.toggle-password').removeClass('fa-eye-slash')
                input.attr("type", "text");
            } else {
                $('.toggle-password').addClass('fa-eye-slash')
                $('.toggle-password').removeClass('fa-eye')
                input.attr("type", "password");
            }
        });
    </script>

</body> 
</html>