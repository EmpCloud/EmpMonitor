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
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/plugins/font-awesome/css/all.min.css" rel="stylesheet" />
    <link href="../assets/plugins/icomoon/style.css" rel="stylesheet" />
    <link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet">

    <link href="../assets/css/concept.css" rel="stylesheet" />
    <link href="../assets/css/custom.css" rel="stylesheet" />
    <link href="../assets/css/new-style.css" rel="stylesheet" />
</head>

<body class="login-whole-wrapper">
    <div id="particles-js">
        <canvas class="particles-js-canvas-el" style="width: 100%; height: 100%;" width="1366"
            height="312"></canvas>
    </div>

    <div class="page-container h-100 d-flex justify-content-center admin-login-wrapper">
        <div class="jumbotron my-auto pt-3">
            <img src="../assets/images/logos/{{ md5($_SERVER['HTTP_HOST']) }}.png" class="login-logo w-100 mb-4" />
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
    <script src="../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
    <script src="../assets/plugins/bootstrap/popper.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="../assets/js/concept.min.js"></script>
    <script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3744450/particles.js"></script>
    <script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3744450/particles.min.js"></script>
    <script type="text/javascript">
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
