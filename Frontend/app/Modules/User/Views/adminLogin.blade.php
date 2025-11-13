<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content=""/>
    <meta name="keywords" content="admin,dashboard"/>
    <meta name="author" content=""/>
    <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title -->
    <title>@if((new App\Modules\User\helper)->checkHost() )
            {{env('WEBSITE_TITLE')}} | @endif SignIn</title>

    <!-- Favicon -->
    <link href="../assets/images/favicons/{{ md5($_SERVER['HTTP_HOST']) }}.png" rel="icon"/>
    <!-- Styles -->

    <link
        href="../assets/plugins/bootstrap/css/bootstrap.min.css"
        rel="stylesheet"
    />
    <link
        href="../assets/plugins/font-awesome/css/all.min.css"
        rel="stylesheet"
    />
    <link href="../assets/plugins/icomoon/style.css" rel="stylesheet"/>
    <link
        href="../assets/plugins/switchery/switchery.min.css"
        rel="stylesheet"
    />

    <!-- Theme Styles -->
    @if($_SERVER['HTTP_HOST'] == 'tts.silah.com.sa')
    <link href="../assets/css/concept-silah.css" rel="stylesheet"/>
    <link href="../assets/css/custom.css" rel="stylesheet"/>
    @else
    <link href="../assets/css/concept.css" rel="stylesheet"/>
    <link href="../assets/css/custom.css" rel="stylesheet"/>
    @endif

    <!-- Modern UI Enhancements -->
    <style>
        .login-box {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .login-box:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        }
        
        .login-header h1 {
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #333;
            font-size: 14px;
        }
        
        .form-control {
            height: 46px;
            border-radius: 8px;
            border: 1.5px solid #e0e6ed;
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4c8bf5;
            box-shadow: 0 0 0 0.2rem rgba(76, 139, 245, 0.15);
        }
        
        .input-group-append .btn {
            border-radius: 0 8px 8px 0;
            height: 46px;
        }
        
        .pswd-div-wrap {
            position: relative;
            width: 100%;
        }
        
        .pswd-div-wrap .form-control {
            width: 100%;
            padding-right: 45px;
        }
        
        .pswd-div-wrap .input-group-append {
            position: absolute;
            right: 0;
            top: 0;
            z-index: 10;
        }
        
        .pswd-div-wrap .toggle-password {
            cursor: pointer;
            border: none;
            background: transparent;
            padding: 12px 15px;
            color: #6c757d;
        }
        
        .pswd-div-wrap .toggle-password:hover {
            color: #495057;
        }
        
        .btn-primary {
            height: 46px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 139, 245, 0.3);
        }
        
        .custom-control-label {
            font-size: 14px;
            color: #6c757d;
        }
        
        .forget-pswd-link {
            font-size: 14px;
            text-decoration: none;
            color: #4c8bf5;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forget-pswd-link:hover {
            color: #3a7ae0;
            text-decoration: none;
        }
        
        .button-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-content {
            border-radius: 12px;
            border: none;
        }
        
        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            padding: 1.5rem;
        }
        
        .modal-title {
            font-weight: 600;
            font-size: 18px;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 1rem 1.5rem;
        }
        
        .error {
            font-size: 13px;
            margin-top: 5px;
        }
        
        #login-error {
            font-size: 14px;
            padding: 10px 15px;
            border-radius: 6px;
            background-color: #fee;
            margin-top: 10px;
        }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->
</head>
<body>
<!-- Page Container -->
<div class="page-container">
    <div class="login">
        <div class="login-bg">
        @if(in_array($_SERVER['HTTP_HOST'], ['app.dev.empmonitor.com', 'app.empmonitor.com']))
        <div>
            <img src="assets/images/logos/white-logo.png" class="white-logo">
        </div>
        <div class="login-bg-content">
            <h1>Build future ready workforce</h1>
            <p>Reach new productivity peaks and empower your business with deep insights, behaviour analytics, monitoring solutions and a lot more with EmpMonitor.</p>
        </div>
        @endif
        </div>
        <div class="login-content text-center">

@if($_SERVER['HTTP_HOST'] == 'tts.silah.com.sa')
<div class="silah-admin-login-wrapper">
    <div class="m-3" style="width: 100px;">
        <select id="languageChange" class="form-control select2">
            <option disabled>Select Language</option>
            <option  value="ENGLISHLANGUAGE">English</option>
            <option selected  value="ARABICLANGUAGE">عربي</option>
        </select>
    </div>
</div>
@endif
            <div class="login-box">
            <div class="logIn-content">
            @if($_SERVER['HTTP_HOST'] == 'tts.silah.com.sa')
                    <div class="text-center silah-logo">
                        <img  src="assets/images/logos/{{ md5($_SERVER['HTTP_HOST']) }}.png"
                        alt="Image"  class="img-fluid" />
                    </div>
                @endif
                <div class="login-header">

                    <img class="fav-icon" src="">
                    <h1 id="welcomeh1">Welcome!</h1>
                    <p id="WelcomeBackMsg">Sign Into Your Account</p>

                    <p class="lead text-center text-danger" id="login-error"></p>
                    @if(session('error'))
                        <script>
                            errorMessage = '<?php echo session('error'); ?>';
                            document.getElementById("login-error").innerHTML = errorMessage;
                            $("#languageChange").trigger("change");
                        </script>
                    @endif
                </div>

                <div class="login-body">
                    <form id="login-form" action="login" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email" id="EmailIdSpan">Email address</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                aria-describedby="emailHelp"
                                placeholder="Enter email"
                                name="email" required
                            />
                        </div>
                        <div class="error" style="color: red;">{{ $errors->first('email') }}</div>
                        <div class="input-group">
                            <label for="password" id="passwordSpan">Password</label>
                            <div class="pswd-div-wrap">
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                placeholder="Password"
                                name="password"
                            />
                            <div class="input-group-append">
                                            <span toggle="#password"
                                                  style="line-height: 1.6;border: 1px solid #ced4da;border-left-color: white;"
                                                  class="btn btn-default fas fa-eye-slash toggle-password"></span>
                            </div>
                            </div>
                        </div>
                        <div class="error" style="color: red;">{{ $errors->first('password') }}</div>
                        <div class="form-group">
                            <input
                                type="hidden"
                                class="form-control"
                                id="ip-address"
                                name="ip"
                            />
                        </div>
                        <div class="button-wrap">
                        <div class="custom-control custom-checkbox form-group">
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="exampleCheck1"
                            />
                            <label id="CustomCheckBox1" class="custom-control-label" for="exampleCheck1"
                            >Remember password</label
                            >
                        </div>
                        <a class="float-right forget-pswd-link" href="#" data-toggle="modal" data-target="#forgotPasswdModal"
                        >Forgot password?</a>
                        {{-- <a href="dashboard.html" type="submit" class="btn btn-primary">Login</a>--}}
    </div>
                        <button id = "LoginDash2" class="btn btn-primary btn-block login-btn" type="submit">Login</button>
                <p class="m-t-sm">
                        <a class="float-right" href="/" id="AdminLoginBtn"
                        >Admin Login?</a>
                    </p>
                    </form>

                </div>

    </div>
           </div>
        </div>
    </div>
</div>
<!-- /Page Container -->

<!-- Modal -->
<div
    class="modal fade"
    id="forgotPasswdModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="forgotPasswdModalTitle"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswdModalTitle">
                    Forgot Password
                </h5>
                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="error" style="color: red;">{{ $errors->first('fg-pwd-error') }}</div>
            <div class="modal-body">
                <div class="form-group">
                    <label id="EmailIdForgot" for="email_id_forgot">Email address</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email_id_forgot"
                        aria-describedby="emailHelp"
                        placeholder="Enter email"
                        required
                    />
                    <p id="fg-pwd-error" style="color: red;"></p>
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                        else.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="button" id="CloseModell"
                    class="btn btn-secondary"
                    data-dismiss="modal"
                >
                    Close
                </button>
                <button type="button" id="forgot-pwd-btn" onclick="forgotPassword()" class="btn btn-primary">Submit
                </button>
            </div>
        </div>
    </div>
</div>

{{--reset-password-modal--}}
<div
    class="modal fade"
    id="resetPasswdModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="resetPasswdModalTitle"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswdModalTitle">
                    Reset Password
                </h5>
                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <p style="display: none;" id="email_id_token"></p>
            <div class="modal-body">
                <div class="form-group">
                    <input
                        type="email"
                        class="form-control"
                        id="email_id_reset"
                        aria-describedby="emailHelp"
                        placeholder="Enter email"
                        disabled
                    />
                </div>
                <div class="input-group">
                    <input
                        type="password"
                        class="form-control"
                        id="password_reset"
                        aria-describedby="emailHelp"
                        placeholder="Enter New Password"
                        name="new_password"
                    />
                    <div class="input-group-append">
                        <span toggle="#password_reset"
                              style="line-height: 1.6;border: 1px solid #ced4da;border-left-color: white;"
                              class="btn btn-default fas fa-eye toggle-password"></span>
                    </div>
                </div>
                <div class="error" style="color: red;" id="error-new-pwd"></div>
                <div class="input-group">
                    <input
                        type="password"
                        class="form-control"
                        id="password_reset_confirm"
                        aria-describedby="emailHelp"
                        placeholder="Confirm Password"
                        name="confirm_password"
                    />
                    <div class="input-group-append">
                        <span toggle="#password_reset_confirm"
                              style="line-height: 1.6;border: 1px solid #ced4da;border-left-color: white;"
                              class="btn btn-default fas fa-eye toggle-password"></span>
                    </div>
                </div>
                <div class="error" style="color: red;" id="error-confirm-pwd"></div>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal"
                >
                    Close
                </button>
                <button type="button" id="resetPassBtn" onclick="resetPassword()" class="btn btn-primary">Submit
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Javascripts -->
<script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
<script src="../assets/js/concept.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var url = 'https://ipapi.co/json/';
        $.ajax({
            url: url,
            async: false,
            dataType: 'json',
            success: function (data) {

                $('#ip-address').val(data.ip);
            }
        })
    });

    var reset = '<?php echo json_encode($reset); ?>';
    reset = JSON.parse(reset);
    if (reset.length != 0) {
        $('#email_id_reset').val(reset.email);
        $('#email_id_token').text(reset.token);
        $('#resetPasswdModal').modal('show');
    }

    $(".toggle-password").click(function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    //reset password
    function resetPassword() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        let isClient = urlParams.get('isClient') ?? false;
        let emailId = $('#email_id_reset').val();
        let password = $('#password_reset').val();
        let cPassword = $('#password_reset_confirm').val();
        let token = $('#email_id_token').text();
        $.ajax({
            url: "/reset-password",
            data: {
                email: emailId,
                new_password: password,
                confirm_password: cPassword,
                token: token,
                isClient
            },
            type: 'POST',
            beforeSend: function () {
                $('#resetPassBtn').prop('disabled', true);
            },
            success: function (response) {
                $('#resetPassBtn').prop('disabled', false);
                if (response['new_password'] !== undefined) $('#error-new-pwd').html(response['new_password']);
                if (response['confirm_password'] !== undefined) $('#error-confirm-pwd').html(response['confirm_password']);
                if (response.code != undefined) {
                    if (response.code === 200) {
                        $('#email_id_reset, #password_reset, #password_reset_confirm').val('');
                        $('#resetPasswdModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: response['msg'],
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Login'
                        }).then(function relod(result) {
                            if (result.value) {
                                isClient ? window.location = 'admin-login' : window.location = 'login';
                            }
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response['msg'],
                            showConfirmButton: true,
                            timer: 1500
                        });
                    }
                }
            },
            error: function (error) {
                Swal.fire({
                    icon: 'error',
                    title: "Something went wrong...",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        })
    }

    //forgot-password
    function forgotPassword() {
        var emailId = $('#email_id_forgot').val();
        if (emailId == "") {
            $('#fg-pwd-error').text('Email Id cannot be empty');
            return;
        } else $('#fg-pwd-error').text('');
        let regex = /^([a-zA-Z0-9_.+-]+)@(([a-zA-Zء-ي0-9-])+.)+([a-zA-Zء-ي]{2,63})+$/;
        if (!regex.test(emailId)) {
            $('#fg-pwd-error').text('Email Id entered is not valid');
        } else {
            $('#fg-pwd-error').text('');
            $.ajax({
                url: "/forgot-password",
                data: {email: emailId},
                type: 'POST',
                beforeSend: function () {
                    $('#forgot-pwd-btn').prop('disabled', true);
                },
                success: function (response) {
                    $('#forgot-pwd-btn').prop('disabled', false);
                    if (response['code'] == 200) {
                        $("#email_id_forgot").val('');
                        $('#forgotPasswdModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: response['msg'],
                            showConfirmButton: true
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response['msg'],
                            showConfirmButton: true
                        });
                    }
                },
                error: function (error) {
                    Swal.fire({
                        icon: 'error',
                        title: "Something went wrong...",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            })
        }
    }

    if (window.location.href.includes('staff.gettytech.com')) {
        $('.text-uppercase').hide();
        $('.float-right').hide();
        $('.float-left').toggleClass('float-left', 'float-right');
    }
</script>

@if($_SERVER['HTTP_HOST'] == 'tts.silah.com.sa')
<script>
    let ENGLISHLANGUAGE = { title : 'Log in',
                            welcome : 'Welcome!',
                            sign_acc : 'Sign Into Your Account',
                            welcome_back_msg: 'Welcome back! Please login to continue.',
                            enter_email : 'Enter email',
                            password : 'Password',
                            remember_password: 'Remember password',
                            login : 'Login',
                            forget_password : 'Forgot password?',
                            admin_login : 'Admin Login?',
                            forget_pass : 'Forgot Password',
                            email_add : 'Email address',
                            never_share_msg : "We'll never share your email with anyone else.",
                            close : "Close",
                            submit : "Submit",
                            user_not_exist : "User does not exists",
                            email_required : "The email field is required.",
                            password_required : "The password field is required.",
                            valide_required : "The email must be a valid email address."
                         };
    let ARABICLANGUAGE = {  title : 'تسجيل الدخول',
                            welcome : 'مرحباً!',
                            sign_acc : 'تسجيل الدخول إلى حسابك',
                            welcome_back_msg: 'مرحبًا بعودتك! الرجاء تسجيل الدخول للمتابعة.',
                            enter_email : 'أدخل البريد الإلكتروني',
                            password : 'كلمة المرور',
                            remember_password: 'تذكر كلمة المرور',
                            login : 'تسجيل الدخول',
                            forget_password : 'هل نسيت كلمة السر؟',
                            admin_login : 'دخول المشرف؟',
                            forget_pass : 'هل نسيت كلمة السر',
                            email_add : 'عنوان البريد الإلكتروني',
                            never_share_msg : "لن نشارك بريدك الإلكتروني مع أي شخص آخر.",
                            close : "يغلق",
                            submit : "يُقدِّم",
                            user_not_exist : "المستخدم غير موجود",
                            email_required : "حقل البريد الإلكتروني مطلوب.",
                            password_required : "حقل كلمة المرور مطلوب.",
                            valide_required : "يجب أن يكون البريد الإلكتروني عنوان بريد إلكتروني صالحًا."
                        };


function changeLang (LANGUAGE) {
    $('#loginTitle').text(LANGUAGE.title);
    $('#welcomeh1').text(LANGUAGE.welcome);
    ($("#WelcomeBackMsg").text() == 'Sign Into Your Account' || $("#WelcomeBackMsg").text() == 'تسجيل الدخول إلى حسابك') ? $('#WelcomeBackMsg').text(LANGUAGE.sign_acc) : $('#WelcomeBackMsg').text(LANGUAGE.welcome_back_msg);
    $('#email').attr('placeholder',LANGUAGE.enter_email);
    $('#password').attr('placeholder',LANGUAGE.password);
    $('#passwordSpan').text(LANGUAGE.password);
    $('#CustomCheckBox1').text(LANGUAGE.remember_password);
    $('#LoginDash2').text(LANGUAGE.login);
    $('a[data-target="#forgotPasswdModal"]').text(LANGUAGE.forget_password);
    $('#AdminLoginBtn').text(LANGUAGE.admin_login);
     // forgget password
    $('#forgotPasswdModalTitle').text(LANGUAGE.forget_pass);
    $('#EmailIdForgot').text(LANGUAGE.email_add);
    $('#EmailIdSpan').text(LANGUAGE.email_add);
    $('#email_id_forgot').attr('placeholder',LANGUAGE.enter_email);
    $("#emailHelp").text(LANGUAGE.never_share_msg);
    $("#CloseModell").text(LANGUAGE.close);
    $("#resetPassBtn").text(LANGUAGE.submit);
    $("#forgot-pwd-btn").text(LANGUAGE.submit);
    ($("#login-error").text() == 'User does not exists') ? $("#login-error").text(LANGUAGE.user_not_exist) : $("#login-error").val();
    $('.error').each(function(i,e){
            let text = $(e).text();
            console.log(text);
            if(text == 'The email field is required.'){
                $(e).text(LANGUAGE.email_required);
            }else if(text == 'The password field is required.'){
                $(e).text(LANGUAGE.password_required);
            }else if(text == 'The email must be a valid email address.'){
                $(e).text(LANGUAGE.valide_required);
            }
     });
}
$("#languageChange").change(function () {
    let langValue = $(this).val();
    langValue == 'ARABICLANGUAGE'?changeLang(ARABICLANGUAGE):changeLang(ENGLISHLANGUAGE);
    window.localStorage.user_language = langValue;
});

    if(window.localStorage.user_language){
        let langValue = window.localStorage.user_language;
        langValue == 'ARABICLANGUAGE'?changeLang(ARABICLANGUAGE):changeLang(ENGLISHLANGUAGE);
        $('#languageChange').val(langValue);
    }else{
        changeLang(ARABICLANGUAGE);
        $('#languageChange').val('ARABICLANGUAGE');
    }

</script>
@endif
</body>
</html>
