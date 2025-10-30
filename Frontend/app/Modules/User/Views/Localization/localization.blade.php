@extends('User::Layout._layout')
@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           @if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif @endif  {{__('messages.localization')}}</title>
@endsection
@section('page-style')
    @include('User::Layout._modernStyles')
    <style>
        /* Page Specific Styles Only */
        .card-body {
            padding: 2rem;
        }
        
        .text-uppercase {
            letter-spacing: 0.5px;
        }
        
        /* Form Groups with separators */
        .form-group.row {
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .form-group.row:last-child {
            border-bottom: none;
        }
        
        /* Submit Button */
        input[type="button"].btn-primary {
            height: 46px;
            font-size: 15px;
        }
    </style>
@endsection
@section('content')

    <div class="page-inner no-page-title">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item"><a href="dashboard" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.home') }}</a></li>
                        <li class="breadcrumb-item " aria-current="page">
                            {{__('messages.localization')}}
                        </li>
                    </ol>
                </nav>

                <h1 class="page-title">{{__('messages.localization')}} </h1>
                <div id="UnaccessModal" style="display: none">
                    <div class="alert alert-danger ">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Note : </strong>
                        <p id="ErrorMsgForUnaccess"> Indicates a successful or positive action.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="localeForm">
                                @csrf
                                <div class="form-group row">
                                    <label for="rname"
                                           class="text-uppercase col-sm-3">{{__('messages.select')}} {{__('messages.timezone')}}</label>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select class="form-control select2" name="timezone" id="localeTimezones">
                                                <option selected
                                                        disabled>{{__('messages.select')}} {{__('messages.timezone')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="name"
                                           class="text-uppercase col-sm-3">{{__('messages.select')}} {{__('messages.language')}}</label>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select name="language" class="form-control select2">
                                               
                                                <option
                                                        disabled>{{__('messages.select')}} {{__('messages.language')}}</option>
                                                   

                                                <option @if($result['locale'] == "en") selected @endif value="en">English</option>
                                                <option @if($result['locale'] == "es") selected @endif value="es">Español</option>
                                                <option @if($result['locale'] == "idn") selected @endif value="idn">Indonesia</option>
                                                <option @if($result['locale'] == "fr") selected @endif value="fr">français</option>
                                                <option @if($result['locale'] == "ar") selected @endif value="ar">عربي</option>
                                                <option @if($result['locale'] == "pt") selected @endif value="pt">Português</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row" style="display: none">
                                    <label for="name" class="text-uppercase col-sm-3">Week Start Day</label>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <select class="form-control select2">
                                                <option selected disabled>Select Start Day</option>
                                                <option>Monday</option>
                                                <option>Tuesday</option>
                                                <option>Wednesday</option>
                                                <option>Thursday</option>
                                                <option>Friday</option>
                                                <option>Saturday</option>
                                                <option>Sunday</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">

                                    <input type="button" id="save" onclick="saveLocale();" value="{{__('messages.save')}}"
                                            class="col-sm-3 btn btn-primary btn-block m-auto">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Wrapper -->
    </div>
@endsection

@section('page-scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <!-- <script src="../assets/plugins/jquery-ui/jquery-ui.min.js"></script> -->
    <script src="../assets/plugins/daterangepicker/moment.min.js"></script>
    <script src="../assets/js/final-timezone.js"></script>
    <script>
        let LOCALIZE_ERROR = JSON.parse('{{__('messages.localizeJs')}}'.replace(/&quot;/g, '"'));
        let RESULT_TIMEZONE_ID = '<?php echo $result['timezone']; ?>'

        let append = "";
        append += '<option id="" value="0" disabled selected>'+SELECT_MSG+' '+ TIMEZONE_LOCALE_MSG +'</option>';

        timezones.forEach(function (time) {
            replace_timezone = time.zone.replace('/', "");
            append += '<option  id="' + time.zone + '" value="' + time.zone + '">' + time.name + '</option>';
        });
        $('#localeTimezones').empty();
        $('#localeTimezones').append(append);
        $('#localeTimezones option[value="'+RESULT_TIMEZONE_ID+'"]').attr('selected','selected');

        function saveLocale() {
            var form = document.getElementById('localeForm');
            var formData = new FormData(form);
            let userType = '<?php echo (new App\Modules\User\helper)->getHostName();?>';
            $.ajax({
                url: "/" + userType + "/save-locale",
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                beforeSend: function () {
                    $('#save').prop('disabled', true);

                },
                success: function (resp) {
                    $('#save').prop('disabled', false);
                    if (resp.code === 200) {
                        if(Number(localStorage.main_tour) == 1){
                            successTourMessage(DASHBOARD_JS.success,2)
                        }else{
                        successMessage(DASHBOARD_JS.success)
                        setTimeout(function () {
                          location.reload();
                        }, 1000);
                        }
                    } else if(resp.code == 404) {
                        Swal.fire({
                            icon: 'error',
                            title: LOCALIZE_ERROR.locale,
                            showConfirmButton: true,
                            confirmButtonText: DASHBOARD_JS.ok
                        });
                    }else if(resp.code === 400){
                        Swal.fire({
                            icon: 'error',
                            title: LOCALIZE_ERROR.timezone,
                            showConfirmButton: true,
                            confirmButtonText: DASHBOARD_JS.ok
                        });
                    }else{
                        errorMessage(DASHBOARD_JS_ERROR.reload);
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#save').prop('disabled', false);

                    if (jqXHR.status == 410) {

                        $("#UnaccessModal").empty();
                        $("#UnaccessModal").css('display', 'block');
                        $("#UnaccessModal").append('<div class="alert alert-danger text-center"><button type="button" class="close" data-dismiss="alert" >&times;</button><b  id="ErrorMsgForUnaccess"> ' + jqXHR.responseJSON.error + '</b></div>')
                    } else errorMessage(DASHBOARD_JS_ERROR.reload);

                }
            });
        }

        function errorMessage(msg) {
            Swal.fire({
                icon: 'error',
                title: msg,
                showConfirmButton: true,
                confirmButtonText: DASHBOARD_JS.ok
            });
        }

        function successMessage(msg) {
            Swal.fire({
                icon: 'success',
                title: msg,
                showConfirmButton: true,
                confirmButtonText: DASHBOARD_JS.ok
            });
        }

        $(document).ready(function () {
            if (is_admin && !tour_completed) {
                $('#videoPlayer').attr('src','https://www.youtube.com/embed/9DvivDiPgnk');
                $('#showvideo').show();
                $('#skipTour').show();
                $('#skipTourButoon').show();
            }
        });
    </script>
@endsection
