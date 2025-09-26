<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content=""/>
    <meta name="keywords" content="admin,dashboard"/>
    <meta name="author" content="EmpMonitor"/>
    <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title -->
    <title> @if((new App\Modules\User\helper)->checkHost() )
            {{env('WEBSITE_TITLE')}} | @endif  | SignIn</title>

    <!-- Styles -->
    <link
        href="https://fonts.googleapis.com/css?family=Rubik"
        rel="stylesheet"
    />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        rel="stylesheet"
    />
    <link href="../assets/plugins/css/icomoon.css" rel="stylesheet"/>
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css"
        rel="stylesheet"
    />

    <!-- Theme Styles -->
    <link href="../assets/css/concept.css" rel="stylesheet"/>
    <link href="../assets/css/custom.css" rel="stylesheet"/>

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
        <div class="login-bg"></div>
        <div class="login-content">
            <div class="login-box">
                <div class="login-header">
                    <img
                        src="assets/images/logos/{{ md5($_SERVER['HTTP_HOST']) }}.png"
                        alt="EmpMonitor"
                        class="login-logo"
                    />

                    <h3>Employee Log In</h3>
                    {{-- <p>Welcome back! Please login to continue.</p> --}}
                    <p class="lead Center " id="login-error" style="color: red; float: center"></p>
                    @if(session('error'))
                        <script>
                            errorMessage = '<?php echo session('error'); ?>';
                            document.getElementById("login-error").innerHTML = errorMessage;
                        </script>
                    @endif
                </div>

                <div class="login-body">
                    <form  action="employee-login" method="POST">
                        @csrf
                        <div class="form-group">
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
                                                  class="btn btn-default fas fa-eye toggle-password"></span>
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
                        <div class="custom-control custom-checkbox form-group">
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="exampleCheck1"
                            />
                            <label class="custom-control-label" for="exampleCheck1"
                            >Remember password</label
                            >
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <button class="btn btn-primary float-right col-12" type="submit">Login</button>
                        </div>
                        {{--                        <a href="dashboard.html" type="submit" class="btn btn-primary">Login</a>--}}
                    </form>
                    <p class="m-t-sm " style="padding-top: 20px;">
                        <br/>
                        {{-- <a href="#" data-toggle="modal" data-target="#forgotPasswdModal"
                        >Forgot password?</a> --}}
                        <span> &nbsp&nbsp&nbsp&nbsp&nbsp  </span>
                        <a href="{{env('APP_URL')}}"
                        >Admin Login?</a>
                    </p>
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
                    <label for="email_id_forgot">Email address</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email_id_forgot"
                        aria-describedby="emailHelp"
                        placeholder="Enter email"
                        required
                    />
                    <p id="fg-pwd-error" style="color: red;"></p>
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal"
                >
                    Close
                </button>
                <button type="button" id="forgot-pwd-btn" onclick="forgotPassword()" class="btn btn-primary">Submit</button>
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
            <p style="display: none;" id="email_id_token" ></p>
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
                        <span toggle="#password_reset" style="line-height: 1.6;border: 1px solid #ced4da;border-left-color: white;" class="btn btn-default fas fa-eye toggle-password"></span>
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
                        <span toggle="#password_reset_confirm" style="line-height: 1.6;border: 1px solid #ced4da;border-left-color: white;" class="btn btn-default fas fa-eye toggle-password"></span>
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
                <button type="button" id="resetPassBtn" onclick="resetPassword()" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
<!-- Javascripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
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
            success: function(data) {
                console.log(data);
                $('#ip-address').val(data.ip);
            }
        })
    });

    var reset = '<?php echo json_encode($reset); ?>';
    reset = JSON.parse(reset);
    if(reset.length != 0){
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
    function resetPassword(){
        var emailId = $('#email_id_reset').val();
        var password = $('#password_reset').val();
        var cPassword = $('#password_reset_confirm').val();
        var token = $('#email_id_token').text();
        $.ajax({
            url: "/reset-password",
            data: {
                email: emailId,
                new_password: password,
                confirm_password: cPassword,
                token: token
            },
            type: 'POST',
            beforeSend: function () {
                $('#resetPassBtn').prop('disabled',true);
            },
            success: function (response) {
                $('#resetPassBtn').prop('disabled',false);
                if (response['new_password'] !== undefined) $('#error-new-pwd').html(response['new_password']);
                if (response['confirm_password'] !== undefined) $('#error-confirm-pwd').html(response['confirm_password']);
                if(response.code != undefined){
                    if(response.code == 200){
                        var emailId = $('#email_id_reset').val('');
                        var password = $('#password_reset').val('');
                        var cPassword = $('#password_reset_confirm').val('');
                        $('#resetPasswdModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: response['msg'],
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Login'
                        }).then((result) => {
                            if (result.value) {
                                window.location.replace("{{env('APP_URL')}}login");
                            }
                        })
                    }
                    else{
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
    function forgotPassword(){
        var emailId = $('#email_id_forgot').val();
        if(emailId == ""){
            $('#fg-pwd-error').text('Email Id cannot be empty');
            return;
        }
        else $('#fg-pwd-error').text('');
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Zء-ي])+\.)+([a-zA-Zء-ي]{2,4})+$/;
        if(!regex.test(emailId)){
            $('#fg-pwd-error').text('Email Id entered is not valid');
        }
        else{
            $('#fg-pwd-error').text('');
            $.ajax({
                url: "/forgot-password",
                data: {email: emailId},
                type: 'POST',
                beforeSend: function () {
                    $('#forgot-pwd-btn').prop('disabled',true);
                },
                success: function (response) {
                    $('#forgot-pwd-btn').prop('disabled',false);
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
</body>
</html>
