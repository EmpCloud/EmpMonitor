@extends('User::Layout._layout')
@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           @if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif @endif {{  trans_choice('messages.setting', 10) }}</title>
@endsection

@section('extra-style-links')
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <script src="../assets/js/html-duration-picker.min.js"></script>
@endsection

@section('content')
    <div class="page-inner no-page-title">
        <p id="user-id" style="display: none" name="{{$userId}}"></p>
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item"><a href="employee-details" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.home') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="employee-details" style="color: #0686d8;font-weight: 500;">{{ __('messages.employee') }}-{{  trans_choice('messages.detail', 1) }}</a>
                        </li>
                        <li class="breadcrumb-item " aria-current="page">
                            {{ __('messages.trackUserSetting') }}
                        </li>
                    </ol>
                </nav>
            </div>
            <div id="UnaccessModal" style="display: none">
                <div class="alert alert-danger ">
                    <button type="button" class="close" data-dismiss="alert" >&times;</button>
                    <strong>Note : </strong><p id="ErrorMsgForUnaccess"> Indicates a successful or positive action.</p>
                </div>
            </div>
            <form id="SaveAllData">
                @csrf
                <div class="row mb-2">
                    <div class="col-md-4">
                        <p id="UserData" name="{{json_encode($data)}}" style="display: none"></p>
                        <p id="advanseUserData" name="{{json_encode($data['data']['custom_tracking_rule'])}}" style="display: none"></p>
                        @if($data['code']==200)
                            <h4>{{ucfirst(trans($data['data']['first_name'])) ." ". ucfirst(trans($data['data']['last_name']))}}
                            </h4>
                        @endif
                        <a class="active" style="text-decoration: underline; color: #0d95e8"
                           href="get-employee-details?id={{$userId}}"> {{ __('messages.employeeFullDetails') }}</a>
                    </div>
                    <div class="col text-right pl-0">
                        <button type="submit" id="SaveButton" class="btn btn-primary">{{ __('messages.save') }}</button>
{{--                        <button type="button" onclick="CancelButton()" class="btn btn-danger">{{ __('messages.cancel') }}</button>--}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">{{ __('messages.employee') }} {{ __('messages.general') }} {{  trans_choice('messages.detail', 10) }}
                            </div>
                            <div class="card-body">
                                <div class="row">
{{--                                    @if(in_array((Session::get(env('Admin'))['token']['organization_id']), explode(',', env('SPECIAL_ADMIN'))))--}}

                                        <div class="col-md-4">
                                            <p><i class="fas fa-cog mr-2"></i>{{__('messages.settingAppliedMsg')}}</p>
                                            <div class="form-group">
                                                <select class="form-control" id="AppliedSetting">
                                                    <option id="1" value="3">{{__('messages.custom')}}</option>
                                                    <option id="1" value="1">{{__('messages.default')}}</option>
                                                    @if($OrgGroups['code']===200)
                                                        @foreach($OrgGroups['data'] as $groups)
                                                            <option id="{{$groups['group_id']}}"
                                                                    value="2">{{$groups['name']}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
{{--                                    @endif--}}
                                    <div class="col-md-4">
                                        <p><i class="fas fa-tv mr-2"></i>{{__('messages.offcSystemMsg')}}</p>
                                        <div class="form-group">
                                            <select class="form-control bg-light" id="roll_option" disabled>
                                                <option id="1" value="computer" selected>{{__('messages.offcSystem')}}</option>
                                                {{--  <option id="0" value="personal ">Personal System</option>--}}
                                            </select>
                                        </div>
                                    </div>
                                        <div class="col-md-4" >
                                            <p><i class="far fa-eye mr-2"></i>{{__('messages.visibleMsg')}}</p>
                                            <div class="form-check form-control alert-info">
                                                <label class="form-check-label text-primary">
                                                    <input onclick="disableTrackScenario(0)" id="visable" type="radio"
                                                           value="true" class="form-check-input"
                                                           name="EmpIcon"
                                                           checked>{{__('messages.visible')}}
                                                </label>
                                                @if((new App\Modules\User\helper)->checkHost() )
                                                    <small>{{__('messages.visibleMsghead')}}</small>
                                                @else
                                                    <small>{{__('messages.visibleMsgheadReseller')}}</small>
                                                @endif
                                            </div>
                                        </div>
                                    <div class="col-md-4">
                                        <p><i class="far fa-eye-slash mr-2 text-danger"></i>
                                            @if((new App\Modules\User\helper)->checkHost() )
                                                {{ __('messages.steathHeader') }}
                                            @else
                                                {{ __('messages.steathHeaderReseller') }}
                                            @endif</p>
                                        <div class="form-check form-control">
                                            <label class="form-check-label text-danger">
                                                <input onclick="disableTrackScenario(1)" id="stealth" type="radio"
                                                       value="false" class="form-check-input"
                                                       name="EmpIcon">{{__('messages.steath')}}
                                            </label>
                                            <small>{{__('messages.steathMsg')}}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Added code for the Tracking feature--}}
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">{{__('messages.trackingHead')}}
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#TrackAdvanceSetting" onclick="TrackUserAdvWeb();" class="btn btn-primary btn-sm float-right WebBlockModalBtn">{{__('messages.AdvancedSettings')}}</a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table">
                                        @if((new App\Modules\User\helper)->checkEnvPermission('WEBAPP_USED_FEATURE'))
                                            <tr>
                                                <td>
                                                    {{__('messages.ApplicationUsed')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="AppRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="AppRadio1" name="Appoption" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="AppRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="AppRadio0" name="Appoption" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{__('messages.web')}} {{__('messages.used')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="WU1">
                                                                <input type="radio" class="form-check-input" id="WU1"
                                                                       name="WebsiteOption" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="WU0">
                                                                <input type="radio" class="form-check-input" id="WU0"
                                                                       name="WebsiteOption" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endif
                                            @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_KEYSTROKE_FEATURE'))
                                                <tr>
                                                    <td>
                                                        {{__('messages.keystroke')}}
                                                    </td>
                                                    <td>
                                                        <form>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-primary"
                                                                       for="KeyStrokeRadio1">
                                                                    <input type="radio" class="form-check-input"
                                                                           id="KeyStrokeRadio1" name="KeyStrokeOption"
                                                                           value="1">{{__('messages.enable')}}
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-danger"
                                                                       for="KeyStrokeRadio0">
                                                                    <input type="radio" class="form-check-input"
                                                                           id="KeyStrokeRadio0" name="KeyStrokeOption"
                                                                           value="0">{{__('messages.disable')}}
                                                                </label>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_SCREENCAST_FEATURE'))
                                                <tr>
                                                    <td>
                                                        Screen Casting
                                                    </td>
                                                    <td>
                                                        <form>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-primary"
                                                                       for="ScreenCast1">
                                                                    <input type="radio" class="form-check-input"
                                                                           id="ScreenCast1" name="ScreenCast"
                                                                           value="1">{{__('messages.enable')}}
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-danger"
                                                                       for="ScreenCast0">
                                                                    <input type="radio" class="form-check-input"
                                                                           id="ScreenCast0" name="ScreenCast"
                                                                           value="0">{{__('messages.disable')}}
                                                                </label>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(!(new App\Modules\User\helper)->checkOrangeClient())
                                                <tr>
                                                    <td>
                                                        {{__('messages.ss')}}
                                                    </td>
                                                    <td>
                                                        <form>
                                                            @csrf
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-primary"
                                                                       for="SS1">
                                                                    <input type="radio" class="form-check-input"
                                                                           id="SS1"
                                                                           onclick="screenshotactive(1,'ss'),storageStatusCheck();"
                                                                           name="ScreenshotsOption"
                                                                           value="1">{{__('messages.enable')}}
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-danger"
                                                                       for="SS0">
                                                                    <input type="radio" class="form-check-input"
                                                                           onclick="screenshotactive(0,ss),storageStatusCheck();" id="SS0"
                                                                           name="ScreenshotsOption"
                                                                           value="0">{{__('messages.disable')}}
                                                                </label>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endif
                                                @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_VIDEOQUALITY_FEATURE'))
                                                    <tr>
                                                        <td>
                                                            {{ __('messages.VideoQuality') }}
                                                        </td>
                                                        <td>
                                                            <form>
                                                                @csrf
                                                                <div class="form-check-inline">
                                                                    <label class="form-check-label text-primary"
                                                                           for="vd1">
                                                                        <input type="radio" class="form-check-input"
                                                                               id="vd1"
                                                                               onclick="screenshotactive(1,'video');"
                                                                               name="videoOption"
                                                                               value="1">{{__('messages.enable')}}
                                                                    </label>
                                                                </div>
                                                                <div class="form-check-inline">
                                                                    <label class="form-check-label text-danger"
                                                                           for="vd0">
                                                                        <input type="radio" class="form-check-input"
                                                                               onclick="screenshotactive(0,'video');"
                                                                               id="vd0"
                                                                               name="videoOption" value="0"
                                                                               checked>{{__('messages.disable')}}
                                                                    </label>
                                                                </div>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td>
                                                        {{__('messages.attendanceoverride')}}
                                                    </td>
                                                    <td>
                                                        <form>
                                                            @csrf
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-primary" for="attendance_in">
                                                                    <input type="radio" class="form-check-input" id="attendance_in" onclick="isAttendance(1);"
                                                                           name="attendanceOption"
                                                                           value="1">{{__('messages.enable')}}
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-danger" for="attendance_out">
                                                                    <input type="radio" class="form-check-input" id="attendance_out" onclick="isAttendance(0);"
                                                                           name="attendanceOption"
                                                                           value="0">{{__('messages.disable')}}
                                                                </label>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_MANUAL_LOG_FEATURE'))
                                                <tr>
                                                    <td>
                                                        {{__('messages.manual_log')}}
                                                    </td>
                                                    <td>
                                                        <form>
                                                            @csrf
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-primary" for="manual_clock_in">
                                                                    <input type="radio" class="form-check-input" id="manual_clock_in" onclick="isManualClock(1);"
                                                                           name="WebsiteOption"
                                                                           value="1">{{__('messages.enable')}}
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-danger" for="manual_clock_out">
                                                                    <input type="radio" class="form-check-input" id="manual_clock_out" onclick="isManualClock(0);"
                                                                           name="WebsiteOption"
                                                                           value="0">{{__('messages.disable')}}
                                                                </label>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Geo Location Logs
                                                    </td>
                                                    <td>
                                                        <form>
                                                            @csrf
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-primary" for="mobile_data_enable">
                                                                    <input type="radio" class="form-check-input" id="mobile_data_enable" onclick="isMobileData(1);"
                                                                           name="systemLockOption"
                                                                           value="1">{{__('messages.enable')}}
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-danger" for="mobile_data_disable">
                                                                    <input type="radio" class="form-check-input" id="mobile_data_disable" onclick="isMobileData(0);"
                                                                           name="systemLockOption"
                                                                           value="0">{{__('messages.disable')}}
                                                                </label>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @if((new App\Modules\User\helper)->checkEnvPermission('REAL_TIME_TRACKING'))
                                                    <tr>
                                                        <td>
                                                            Real Time Track
                                                        </td>
                                                        <td>
                                                            <form>
                                                                @csrf
                                                                <div class="form-check-inline">
                                                                    <label class="form-check-label text-primary"
                                                                           for="real_time_track_enable">
                                                                        <input type="radio" class="form-check-input"
                                                                               id="real_time_track_enable"
                                                                               name="realTimeTrackOption"
                                                                               value="1">{{__('messages.enable')}}
                                                                    </label>
                                                                </div>
                                                                <div class="form-check-inline">
                                                                    <label class="form-check-label text-danger"
                                                                           for="real_time_track_disable">
                                                                        <input type="radio" class="form-check-input"
                                                                               id="real_time_track_disable"
                                                                               name="realTimeTrackOption"
                                                                               value="0">{{__('messages.disable')}}
                                                                    </label>
                                                                </div>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if(Session::get((new App\Modules\User\helper)->getHostName())['token']['organization_id'] == 249)
                                                <tr>
                                                    <td>
                                                        Location
                                                    </td>
                                                    <td>
                                                        <form>
                                                            @csrf
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-primary" for="location_enable">
                                                                    <input type="radio" class="form-check-input" id="location_enable"
                                                                           name="locationOption"
                                                                           value="1">{{__('messages.enable')}}
                                                                </label>
                                                            </div>
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label text-danger" for="location_disable">
                                                                    <input type="radio" class="form-check-input" id="location_disable"
                                                                           name="locationOption"
                                                                           value="0">{{__('messages.disable')}}
                                                                </label>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                               @endif
                                            @endif

                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Added code for the DLP feature--}}
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">{{__('messages.dlpHead')}}</div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table">
                                          @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_WEBAPP_FEATURE'))
                                            <tr>
                                                <td>
                                                    {{__('messages.webBlocking')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="webBlockRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="webBlockRadio1" name="webBlockRadio" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="webBlockRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="webBlockRadio0" name="webBlockRadio" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#WebBlockModal"  onclick="TrackUserAdvWeb();" class="btn btn-primary btn-sm float-right WebBlockModalBtn">Add Websites to Block</a>
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{__('messages.appBlocking')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="appBlockRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="appBlockRadio1" name="appBlockRadio" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="appBlockRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="appBlockRadio0" name="appBlockRadio" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#AppBlockModal" onclick="TrackUserAdvWeb();" class="btn btn-primary btn-sm float-right AppBlockModalBtn">Add Application to Block</a>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endif
                                          @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_USB_FEATURE'))
                                            <tr>
                                                <td>
                                                    {{__('messages.usbDetection')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="usbDetecRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="usbDetecRadio1" name="usbDetecRadio" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="usbDetecRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="usbDetecRadio0" name="usbDetecRadio" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                        <small class="float-right usbDetecInfo" style="display:none"><i class="far fa-question-circle"></i> Select enable USB detection to use USB blocking</small>
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr id = "usbBlock">
                                                <td>
                                                    {{__('messages.usb_blocking')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="usbBlockRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="usbBlockRadio1" name="usbBlockRadio" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="usbBlockRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="usbBlockRadio0" name="usbBlockRadio" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endif
                                          @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_BLUETOOTH_FEATURE'))
                                            <tr>
                                                <td>
                                                    {{__('messages.bluetoothDetection')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="blueDetecRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="blueDetecRadio1" name="blueDetecRadio" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="blueDetecRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="blueDetecRadio0" name="blueDetecRadio" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                        <small class="float-right bluetoothInfo" style="display:none"><i class="far fa-question-circle"></i> Select enable bluetooth detection to use bluetooth blocking</small>
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr id = "bluetoothBloc">
                                                <td>
                                                    {{__('messages.bluetoothBlocking')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="blueBlockRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="blueBlockRadio1" name="blueBlockRadio" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="blueBlockRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="blueBlockRadio0" name="blueBlockRadio" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#BluetoothBlockModal"  onclick="TrackUserAdvWeb();" class="btn btn-primary btn-sm float-right BlueModalBtn">Add Bluetooth Address</a>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endif
                                          @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_CLIPBOARD_FEATURE'))
                                            <tr>
                                                <td>
                                                    {{__('messages.clipboardhDetection')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="clipDetecRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="clipDetecRadio1" name="clipDetecRadio" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="clipDetecRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="clipDetecRadio0" name="clipDetecRadio" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                        <small class="float-right clipboardInfo" style="display:none"><i class="far fa-question-circle"></i> Select enable clipboard detection to use clipboard blocking</small>
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr id = "clipboardhBlock">
                                                <td>
                                                    {{__('messages.clipboardhBlocking')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="clipBlockRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="clipBlockRadio1" name="clipBlockRadio" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="clipBlockRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="clipBlockRadio0" name="clipBlockRadio" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endif
                                          @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_SYSTEM_LOCK_FEATURE'))
                                            <tr>
                                                <td>
                                                    {{__('messages.systemLock')}}
                                                </td>
                                                <td>
                                                    <form>
                                                        @csrf
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-primary" for="sysLocRadio1">
                                                                <input type="radio" class="form-check-input"
                                                                       id="sysLocRadio1" name="sysLocRadio" value="1">{{__('messages.enable')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label text-danger" for="sysLocRadio0">
                                                                <input type="radio" class="form-check-input"
                                                                       id="sysLocRadio0" name="sysLocRadio" value="0">{{__('messages.disable')}}
                                                            </label>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endif


                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>



                    @if(!(new App\Modules\User\helper)->checkOrangeClient())
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"> {{__('messages.ss')}}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><i class="fas fa-tv mr-2"></i> {{ __('messages.ssMsg') }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label> {{ __('messages.ss') }}  {{ __('messages.frequency') }}</label>
                                            <select class="form-control" id="SSFrequencySelected">
                                                @if($DropDownOption['code']==200)
                                                    <option disabled>{{ __('messages.ss') }}  {{ __('messages.frequency') }}</option>
                                                    @foreach($DropDownOption['data']['data']['screenshotFrequency'] as $screenshotFrequency)
                                                        <option
                                                            id="{{$screenshotFrequency['value']}}">{{$screenshotFrequency['name']}}</option>
                                                    @endforeach
                                                @else
                                                    <option disabled> {{ __('messages.Nodata') }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    @if((new App\Modules\User\helper)->checkEnvPermission('SHOW_VIDEOQUALITY_FEATURE'))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label> {{ __('messages.VideoQuality') }} </label>
                                            <select class="form-control" id="videoQuality" disabled>
                                                <option disabled>{{ __('messages.VideoQuality') }}</option>
                                               <option id="vid1280" value="1280">1280 px (High)</option>
                                               <option id="vid1080" value="1080">1080 px (Medium)</option>
                                               <option id="vid720" value="720">720 px (Low)</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                    {{--                                    <div class="col-md-4">--}}
                                    {{--                                        <div class="form-group form-check">--}}
                                    {{--                                            <label class="form-check-label text-primary">--}}
                                    {{--                                                <input class="form-check-input" id="AccessWatchingSS" type="checkbox"--}}
                                    {{--                                                       name="AccessOfWatchingSS">Allow--}}
                                    {{--                                                employees to--}}
                                    {{--                                                access Screenshots--}}
                                    {{--                                            </label>--}}
                                    {{--                                        </div>--}}
                                    {{--                                        <div class="form-group form-check">--}}
                                    {{--                                            <label class="form-check-label text-danger">--}}
                                    {{--                                                <input class="form-check-input" id="deleteAccessSS" type="checkbox"--}}
                                    {{--                                                       name="AccessOfDeleteSS"> Allow--}}
                                    {{--                                                employees to--}}
                                    {{--                                                remove Screenshots--}}
                                    {{--                                            </label>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"> {{ __('messages.AgentAutomaticUpdate') }}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>{{ __('messages.Agentmsg') }}</p>
                                    </div>
                                        <div class="col-md-4">
                                            <p>{{ __('messages.EnableAutomaticUpdate') }}</p>
                                        </div>
                                        <div class="col-md-2 ">
                                            <input type="checkbox" data-toggle="toggle" data-width="100"
                                                   id="autoUpdates_id" name="autoUpdates"  data-on="{{__('messages.on')}}" data-off="{{__('messages.off')}}"
                                                   data-height="25" data-size="mini">
                                        </div>
{{--                                        <div class="col-sm">--}}
{{--                                            <button type="button"--}}
{{--                                                    class="btn btn-primary btn-sm">{{ __('messages.UpdateAvilable') }}</button>--}}
{{--                                        </div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            {{--                            <div class="card-header">Employee's tracking Time--}}
                            {{--                            </div>--}}
                            <div class="card-body">
                                <div class="row">
                                    @if(env('APP_ENV')=='dev')
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label
                                                    class="text-danger">{{ __('messages.break') }} {{ __('messages.time') }}
                                                    <i class="far fa-question-circle"></i></label>
                                                <select class="form-control alert-danger border-danger" id="BreakTime">
                                                    @if($DropDownOption['code']==200)
                                                        @foreach($DropDownOption['data']['data']['beakTime'] as $breakTime)
                                                            <option
                                                                id="{{$breakTime['value']}}">{{$breakTime['name']}}</option>
                                                        @endforeach
                                                    @else
                                                        <option disabled>{{ __('messages.Nodata') }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label
                                                class="text-warning">{{ __('messages.idles') }} {{ __('messages.time') }}
                                                <i
                                                    class="far fa-question-circle"></i></label>
                                            <select class="form-control alert-warning border-warning" id="IdleTime">
                                                @if($DropDownOption['code']==200)
                                                    @foreach($DropDownOption['data']['data']['idleTime'] as $IdleTime)
                                                        <option
                                                            id="{{$IdleTime['value']}}">{{$IdleTime['name']}}</option>
                                                    @endforeach
                                                @else
                                                    <option disabled>{{ __('messages.Nodata') }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label
                                                class="text-secondary">{{ __('messages.idles') }} {{ __('messages.time') }}
                                                {{ __('messages.forTimesheets') }}
                                                <i
                                                    class="far fa-question-circle"
                                                    title="{{ __('messages.forTimesheetsToolTip') }}"
                                                    data-toggle="tooltip"> ( MM:SS ) </i></label>
                                            <input type="text" id="idleTimeForTimeSheet"
                                                   class="form-control html-duration-picker"
                                                   data-duration-max="59:59"
                                                   data-hide-seconds style="padding-right: 27px"
                                                   data-duration="{{$data['data']['custom_tracking_rule']['timesheetIdleTime'] ?? "00:00"}}">
                                        </div>
                                    </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="text-warning">Inactive Time <i class="far fa-question-circle"></i></label>
                                                <select class="form-control alert-warning border-warning" name="inactiveTime" id="inactiveTime">
                                                    <option value="" selected disabled>Select Inactive Time</option>
                                                    @for ($i = 3; $i < 1440; $i += 3)
                                                        <option value="{{ $i }}" id="{{ $i }}">{{ $i }} min</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="error" style="color: red;" id="IdleTime"></div>
                                        </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <p>{{__('messages.trackingHeadMsg')}}<i class="far fa-question-circle"></i></p>
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs bg-light text-center" role="tablist">

                                            <li class="col nav-item p-0">
                                                <a class="nav-link" data-toggle="tab" href="#Unlimited" onclick="radioChecked(1)">
                                                    <input type="radio" class="form-check-input" name="Scenario"
                                                       id="Scenario1" value="unlimited"   checked onclick="radioChecked(1)">{{__('messages.unlimited')}}</a>
                                            </li>

                                            <li class="col nav-item p-0 form-check">
                                                <a class="nav-link form-check-label" for="Fixed" data-toggle="tab" href="#Fixed" onclick="radioChecked(2)">
                                                    <input type="radio" class="form-check-input" name="Scenario" value="fixed"
                                                           id="Scenario2"  onclick="radioChecked(2)">{{__('messages.fixed')}}</a>
                                            </li>


                                            @if(!(new App\Modules\User\helper)->checkOrangeClient())
                                                <li class="col nav-item p-0 form-check" id="ManualNav">
                                                    <a class="nav-link form-check-label" id="ManualActiveTab"
                                                       data-toggle="tab"
                                                       href="#ManualClockedIn" onclick="radioChecked(4)">
                                                        <input type="radio" id="Scenario4" value="manual"
                                                               name="Scenario">{{__('messages.ManualClocked')}}</a>
                                                </li>
                                                <li class="col nav-item p-0 form-check" id="ProjectBasedNav">
                                                    <a class="nav-link" id="ProjectActiveTab" data-toggle="tab"
                                                       href="#ProjectBased"
                                                       onclick="radioChecked(5)">
                                                        <input type="radio" class="form-check-input"
                                                               value="projectBased" name="Scenario" id="Scenario5"
                                                               onclick="radioChecked(5)">{{ __('messages.projBased') }}
                                                    </a>
                                                </li>
                                                <li class="col nav-item p-0 form-check">
                                                    <a class="nav-link" data-toggle="tab" href="#NetworkBased"
                                                       onclick="radioChecked(6)">
                                                        <input type="radio" class="form-check-input" id="Scenario6"
                                                               value="networkBased" name="Scenario"
                                                               onclick="radioChecked(6)">{{ __('messages.netBased') }}
                                                    </a>
                                                </li>
                                                <li class="col nav-item p-0 form-check">
                                                    <a class="nav-link" data-toggle="tab" href="#GeoLocation"
                                                       onclick="radioChecked(7)">
                                                        <input type="radio" class="form-check-input" id="Scenario7"
                                                               value="geoLocation" name="Scenario"
                                                               onclick="radioChecked(7)">{{ __('messages.geo_location') }}
                                                    </a>
                                                </li>
                                            @endif

                                            {{--   this comment section is for futre enable the track-senario  settings --}}
                                            {{--                                            <li class="col nav-item p-0">--}}
                                            {{--                                                <a class="nav-link active" id="UnlimitedActiveTab" data-toggle="tab"--}}
                                            {{--                                                   href="#Unlimited" onclick="radioChecked(1)">--}}
                                            {{--                                                    <input checked type="radio" class="form-check-input"--}}
                                            {{--                                                           onclick="radioChecked(1)" id="Scenario1" name="Scenario"--}}
                                            {{--                                                           value="unlimited"--}}
                                            {{--                                                    >Unlimited</a>--}}
                                            {{--                                            </li>--}}

                                            {{--                                            <li class="col nav-item p-0 form-check disabled" id="FixedNav">--}}
                                            {{--                                                <a class="nav-link disabled" id="FixedActiveTab" data-toggle="tab"--}}
                                            {{--                                                   href="#Fixed"--}}
                                            {{--                                                   for="Fixed" onclick="radioChecked(2)">--}}
                                            {{--                                                    <input type="radio" disabled class="form-check-input"--}}
                                            {{--                                                           onclick="radioChecked(2)" value="fixed" id="Scenario2"--}}
                                            {{--                                                           name="Scenario">Fixed</a>--}}
                                            {{--                                            </li>--}}
                                            {{--                                            <li class="col nav-item p-0 form-check">--}}
                                            {{--                                                <a class="nav-link" id="NetworkActiveTab" data-toggle="tab" selected--}}
                                            {{--                                                   href="#NetworkBased" onclick="radioChecked(3)">--}}
                                            {{--                                                    <input type="radio" class="form-check-input" id="Scenario3"--}}
                                            {{--                                                           value="networkBased" onclick="radioChecked(3)"--}}
                                            {{--                                                           name="Scenario">Network--}}
                                            {{--                                                    Based</a>--}}
                                            {{--                                            </li>--}}
                                            {{--                                            @if($trackUserData===3)--}}
                                            {{--                                                @if($data['data']['custom_tracking_rule']['system']['visibility']==="true")--}}
                                            {{--                                                    <li class="col nav-item p-0 form-check" id="ManualNav">--}}
                                            {{--                                                        <a class="nav-link" id="ManualActiveTab" data-toggle="tab"--}}
                                            {{--                                                           href="#ManualClockedIn" onclick="radioChecked(4)">--}}
                                            {{--                                                            <input type="radio" id="Scenario4" value="manual"--}}
                                            {{--                                                                   name="Scenario">Manual--}}
                                            {{--                                                            Clocked In</a>--}}
                                            {{--                                                    </li>--}}
                                            {{--                                                    <li class="col nav-item p-0 form-check" id="ProjectBasedNav">--}}
                                            {{--                                                        <a class="nav-link" id="ProjectActiveTab" data-toggle="tab"--}}
                                            {{--                                                           href="#ProjectBased" onclick="radioChecked(5)">--}}
                                            {{--                                                            <input type="radio" class="form-check-input"--}}
                                            {{--                                                                   onclick="radioChecked(5)" id="Scenario5"--}}
                                            {{--                                                                   value="projectBased"--}}
                                            {{--                                                                   name="Scenario">Project--}}
                                            {{--                                                            Based</a>--}}
                                            {{--                                                    </li>--}}
                                            {{--                                                @else--}}
                                            {{--                                                    <li class="col nav-item p-0 form-check disabled" id="ManualNav">--}}
                                            {{--                                                        <a class="nav-link disabled" id="ManualActiveTab"--}}
                                            {{--                                                           data-toggle="tab"--}}
                                            {{--                                                           href="#ManualClockedIn" onclick="radioChecked(4)">--}}
                                            {{--                                                            <input type="radio" disabled id="Scenario4" value="manual"--}}
                                            {{--                                                                   name="Scenario">Manual--}}
                                            {{--                                                            Clocked In</a>--}}
                                            {{--                                                    </li>--}}
                                            {{--                                                    <li class="col nav-item p-0 form-check disabled"--}}
                                            {{--                                                        id="ProjectBasedNav">--}}
                                            {{--                                                        <a class="nav-link disabled" id="ProjectActiveTab"--}}
                                            {{--                                                           data-toggle="tab"--}}
                                            {{--                                                           href="#ProjectBased" onclick="radioChecked(5)">--}}
                                            {{--                                                            <input type="radio" disabled class="form-check-input"--}}
                                            {{--                                                                   onclick="radioChecked(5)" id="Scenario5"--}}
                                            {{--                                                                   value="projectBased"--}}
                                            {{--                                                                   name="Scenario">Project--}}
                                            {{--                                                            Based</a>--}}
                                            {{--                                                    </li>--}}
                                            {{--                                                @endif--}}

                                            {{--                                            @else--}}
                                            {{--                                                <li class="col nav-item p-0 form-check" id="ManualNav">--}}
                                            {{--                                                    <a class="nav-link" id="ManualActiveTab" data-toggle="tab"--}}
                                            {{--                                                       href="#ManualClockedIn" onclick="radioChecked(4)">--}}
                                            {{--                                                        <input type="radio" id="Scenario4" value="manual"--}}
                                            {{--                                                               name="Scenario">Manual--}}
                                            {{--                                                        Clocked In</a>--}}
                                            {{--                                                </li>--}}
                                            {{--                                                <li class="col nav-item p-0 form-check" id="ProjectBasedNav">--}}
                                            {{--                                                    <a class="nav-link" id="ProjectActiveTab" data-toggle="tab"--}}
                                            {{--                                                       href="#ProjectBased" onclick="radioChecked(5)">--}}
                                            {{--                                                        <input type="radio" class="form-check-input"--}}
                                            {{--                                                               onclick="radioChecked(5)" id="Scenario5"--}}
                                            {{--                                                               value="projectBased"--}}
                                            {{--                                                               name="Scenario">Project--}}
                                            {{--                                                        Based</a>--}}
                                            {{--                                                </li>--}}
                                            {{--                                            @endif--}}
                                        </ul>
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div id="Unlimited" class="container tab-pane active">
                                                <h3>{{ __('messages.workingDaysHead') }}</h3>
                                                <p>{{ __('messages.workingDaysMsg') }}</p>
                                                <hr>
                                                <div>
                                                    <p style="color: red" id="errorMessageUnlimited"></p>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       value="1" id="Unlimited1" checked disabled
                                                                       name="Unlimited">{{__('messages.mon')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       value="2" id="Unlimited2" checked disabled
                                                                       name="Unlimited">{{__('messages.tue')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       value="3" id="Unlimited3" checked disabled
                                                                       name="Unlimited">{{__('messages.wed')}}

                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       value="4" id="Unlimited4" checked disabled
                                                                       name="Unlimited">{{__('messages.thur')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       value="5" id="Unlimited5" checked disabled
                                                                       name="Unlimited">{{__('messages.fri')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check alert-danger">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       checked disabled id="Unlimited6" name="Unlimited"
                                                                       value="6">{{__('messages.sat')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check bg-danger text-white">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       checked disabled id="Unlimited7" name="Unlimited"
                                                                       value="7">{{__('messages.sun')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="Fixed" class="container tab-pane fade">
                                                <div class="row">
                                                    <div class="col-md-6 p-0">
                                                        <h3>{{__('messages.select_days_timings')}}</h3>
                                                        <p>{{__('messages.fixed_working_hours')}}</p>
                                                        <div>
                                                            <p style="color: red" id="errorMessage"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="FixedMonday" name="Fixed" value="MONDAY">{{__('messages.mon')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <label class="">{{ __('messages.shift') }} {{ __('messages.Startsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control" id="MONDAYstartTime"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center"><label>{{ __('messages.shift') }} {{ __('messages.Endsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control" id="MONDAYendTime"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="button" onclick="makeALLsameValues()"
                                                                class="btn btn-primary btn-block float-right">{{ __('messages.applyToAll') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="FixedTuesday" name="Fixed" value="TUESDAY">{{__('messages.tue')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <label class="">{{ __('messages.shift') }} {{ __('messages.Startsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control"
                                                                   id="TUESDAYstartTime" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center"><label>{{ __('messages.shift') }} {{ __('messages.Endsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control" id="TUESDAYendTime"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2"></div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="FixedWEDNESDAY" name="Fixed"
                                                                       value="WEDNESDAY">{{__('messages.wed')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <label class="">{{ __('messages.shift') }} {{ __('messages.Startsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control"
                                                                   id="WEDNESDAYstartTime" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center"><label>{{ __('messages.shift') }} {{ __('messages.Endsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control"
                                                                   id="WEDNESDAYendTime" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2"></div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="FixedTHURSDAY" name="Fixed" value="THURSDAY">{{__('messages.thur')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <label class="">{{ __('messages.shift') }} {{ __('messages.Startsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control"
                                                                   id="THURSDAYstartTime" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center"><label>{{ __('messages.shift') }} {{ __('messages.Endsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control" id="THURSDAYendTime"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check alert-info">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="FixedFRIDAY" name="Fixed" value="FRIDAY">{{__('messages.fri')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <label class="">{{ __('messages.shift') }} {{ __('messages.Startsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control" id="FRIDAYstartTime"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center"><label>{{ __('messages.shift') }} {{ __('messages.Endsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control" id="FRIDAYendTime"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check alert-danger">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="FixedSATURDAY" name="Fixed" value="SATURDAY">{{__('messages.sat')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <label class="">{{ __('messages.shift') }} {{ __('messages.Startsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control"
                                                                   id="SATURDAYstartTime" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center"><label>{{ __('messages.shift') }} {{ __('messages.Endsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control" id="SATURDAYendTime"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-control form-check bg-danger text-white">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="FixedSUNDAY" name="Fixed" value="SUNDAY">{{__('messages.sun')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center">
                                                        <label class="">{{ __('messages.shift') }} {{ __('messages.Startsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control" id="SUNDAYstartTime"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col align-self-center"><label>{{ __('messages.shift') }} {{ __('messages.Endsat') }}</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="time" class="form-control" id="SUNDAYendTime"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2"></div>
                                                </div>
                                            </div>
                                            {{--network based modal view--}}
                                            <div id="NetworkBased" class="container tab-pane fade">
                                                <h3>{{ __('messages.NetworkTrack') }}</h3>
                                                <hr>
                                                <div class="row">
                                                    @if((new App\Modules\User\helper)->checkEnvPermission('ADD_MULTIPLE_NETWORK_TRACK'))
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4">
                                                            <button onclick=" addGeoRowloc(1);" type="button"
                                                                    class="btn btn-primary btn-sm">{{ __('messages.add_location') }}</button>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label for="email">{{ __('messages.NetworkName') }} :</label>
                                                            <input type="text" class="form-control"
                                                                   placeholder="{{ __('messages.enter_network_name') }}"
                                                                   id="NetworkName0">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label for="email">{{ __('messages.ip_address') }} :</label>
                                                            <input type="text" class="form-control"
                                                                   placeholder="{{ __('messages.enter_network_ip') }}"
                                                                   id="MACaddress0">
                                                            <p style="color: red" id="ErrorMACAddress"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <div
                                                            class="form-control form-check bg-primary text-white office_network_div">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" name="officeNetwork"
                                                                       class="form-check-input"
                                                                       id="officeNetwork0"> {{ __('messages.OfficeNetwork') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="font-weight-bold">{{ __('messages.note') }} :
                                                            <small>{{ __('messages.office_network_note') }}</small></p>
                                                    </div>
                                                </div>
                                                    <div id="newRowNetwork"></div>

                                            </div>

                                            {{--Geo location--}}
                                            <div id="GeoLocation" class="container tab-pane fade">
                                                <h3>{{ __('messages.geo_location') }}</h3>
                                                {{--                                                <p style="color: red" id="errorMessageGeoLocation"></p>--}}

                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span id="errorLatLng" style="color: #ff1123"></span>
                                                        <div class="row" id="inputFormRowloc">
                                                            <div class="col-sm">
                                                                <div class="form-group">
                                                                    <label for="usr">Location:</label>
                                                                    <input type="text" class="form-control"
                                                                           onkeyup="longitude(this.value,this.id);"
                                                                           placeholder="{{ __('messages.enter_location') }}"
                                                                           id="location0">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm">
                                                                <label id="" class="" data-toggle="tooltip"
                                                                       data-html="true"
                                                                       title="<ol>
                                                    <li>{{__('messages.geo_location_info1')}}</li>
                                                    <li>{{__('messages.geo_location_info2')}}</li>
                                                    <li>{{__('messages.geo_location_info3')}}</li>
                                                    <li>{{__('messages.geo_location_info4')}}</li>
                                                </ol>"
                                                                ><b>{{  __('messages.latitude') }}
                                                                        , {{ __('messages.longitude') }}</b>
                                                                    : <i
                                                                        class="fas fa-comment-dots ml-2"></i>
                                                                </label>
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control"
                                                                           onkeyup="longitude(this.value,this.id);"
                                                                           disabled
                                                                           placeholder=" {{ __('messages.enter_latitude') }}, {{ __('messages.enter_longitude') }}"
                                                                           id="longitude0"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm">
                                                                <label
                                                                    for="project_end_date"><b> {{ __('messages.Range_meters') }}
                                                                        ( mts )</b></label>
                                                                <div class="form-group">
                                                                    <input type="number" class="form-control" disabled
                                                                           placeholder="{{ __('messages.geo_range') }}"
                                                                           id="range0"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-append align-self-center">
                                                                <a class="text-primary"><i
                                                                        class="far fa-check-circle"></i></a>
                                                            </div>
                                                        </div>
                                                        <div id="newRowloc"></div>
                                                        <button onclick="addRowloc(1);" type="button"
                                                                class="btn btn-primary btn-sm">{{ __('messages.add_location') }}</button>
                                                    </div>

                                                </div>
                                            </div>

                                            {{--   project based modal view--}}
                                            <div id="ProjectBased" class="container tab-pane fade">
                                                <h3>{{ __('messages.projects_lists') }}</h3>
                                                <p style="color: red" id="errorMessageProjectBased"></p>
                                                <hr>

                                                @if($UserProjects['code'] === 200 && count( $UserProjects['data'] )!== 0)
                                                    <div class="input-group mb-3">
                                                        <input class="form-control" id="myInput" type="text"
                                                               placeholder="{{ __('messages.search') }}..">
                                                    </div>
                                                    <p class="text-primary pl-1"><input type="checkbox" class="mr-2"
                                                                                        id="checkAll">{{ __('messages.select_all') }}
                                                    </p>
                                                    <form class="list-data" id="mychkemp">
                                                        @foreach($UserProjects['data'] as $projects )
                                                            <div class="form-check">
                                                                <label class="form-check-label" for="check1">
                                                                    <input type="checkbox"
                                                                           class="form-check-input selected-checkbox"
                                                                           name="projectBased"
                                                                           id="{{$projects['project_id']}}">{{$projects['project_name']}}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </form>
                                                @else
                                                    <div class="form-check">
                                                        <p>{{__('messages.Nodata')}}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{--                                            <div id="NetworkBased" class="container tab-pane fade">--}}
                                            {{--                                                <h3>Specific Network Track</h3>--}}
                                            {{--                                                <p>Track will work when computer is connected to specific network</p>--}}
                                            {{--                                                <hr>--}}
                                            {{--                                                <div class="row">--}}
                                            {{--                                                    <div class="col-md-6">--}}
                                            {{--                                                        <div class="form-group">--}}
                                            {{--                                                            <label for="email">Network Name:</label>--}}
                                            {{--                                                            <input type="text" class="form-control"--}}
                                            {{--                                                                  autocomplete="off" placeholder="Enter network name" id="NetworkName"--}}
                                            {{--                                                                   value="">--}}
                                            {{--                                                        </div>--}}
                                            {{--                                                        <div>--}}
                                            {{--                                                            <p style="color: red" id="ErrorNetworkName"></p>--}}
                                            {{--                                                        </div>--}}
                                            {{--                                                    </div>--}}
                                            {{--                                                    <div class="col-md-6">--}}
                                            {{--                                                        <div class="form-group">--}}
                                            {{--                                                            <label for="email">MAC address:</label>--}}
                                            {{--                                                            <input type="text" class="form-control"--}}
                                            {{--                                                                  autocomplete="off" placeholder="Enter MAC Address" id="MACaddress"--}}
                                            {{--                                                                   value="">--}}
                                            {{--                                                        </div>--}}
                                            {{--                                                        <div>--}}
                                            {{--                                                            <p style="color: red" id="ErrorMACAddress"></p>--}}
                                            {{--                                                        </div>--}}
                                            {{--                                                    </div>--}}

                                            {{--                                                    <div class="col-md-6">--}}
                                            {{--                                                        <div class="form-control form-check bg-primary text-white">--}}
                                            {{--                                                            <label class="form-check-label">--}}
                                            {{--                                                                <input type="checkbox" name="officeNetwork"--}}
                                            {{--                                                                       class="form-check-input"--}}
                                            {{--                                                                       value="">Office--}}
                                            {{--                                                                Network--}}
                                            {{--                                                            </label>--}}
                                            {{--                                                        </div>--}}
                                            {{--                                                    </div>--}}
                                            {{--                                                </div>--}}
                                            {{--                                            </div>--}}
                                            <div id="ManualClockedIn" class="container tab-pane fade">
                                                <h3>{{ __('messages.ManualInOut') }}</h3>
                                                <p class="display-3 text-center text-secondary"><i
                                                        class="fas fa-business-time"></i></p>
                                                <p class="w-50 text-center mx-auto text-secondary">{{ __('messages.ManualMsg') }} </p>
                                            </div>
{{--                                            <div id="ProjectBased" class="container tab-pane fade">--}}
{{--                                                <h3>{{ __('messages.timeTaskTracked') }}</h3>--}}
{{--                                                <hr>--}}
{{--                                                <p class="display-3 text-center text-secondary"><i--}}
{{--                                                        class="fas fa-user-clock"></i></p>--}}
{{--                                                <p class="w-50 text-center mx-auto text-secondary">{{ __('messages.empTracked') }}</p>--}}
{{--                                            </div>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {{--                    <div class="col-md-12">--}}
                {{--                        <div class="card">--}}
                {{--                            <div class="card-header">Employee's Permissions</div>--}}
                {{--                            <div class="card-body">--}}
                {{--                                <div class="row">--}}
                {{--                                    <div class="col-md-6">--}}
                {{--                                        <p class="text-primary">Permission Settings <i--}}
                {{--                                                class="far fa-question-circle"></i>--}}
                {{--                                        </p>--}}
                {{--                                        <div class="form-group form-check">--}}
                {{--                                            <label class="form-check-label">--}}
                {{--                                                <input class="form-check-input" type="checkbox"> Employee can analyze--}}
                {{--                                                their own productivity--}}
                {{--                                            </label>--}}
                {{--                                        </div>--}}

                {{--                                        <div class="form-group form-check">--}}
                {{--                                            <label class="form-check-label">--}}
                {{--                                                <input class="form-check-input" type="checkbox"> Employee can see used--}}
                {{--                                                app and website--}}
                {{--                                            </label>--}}
                {{--                                        </div>--}}
                {{--                                        <div class="form-group form-check">--}}
                {{--                                            <label class="form-check-label">--}}
                {{--                                                <input class="form-check-input" type="checkbox"> Employee can add--}}
                {{--                                                offline time--}}
                {{--                                            </label>--}}
                {{--                                        </div>--}}
                {{--                                    </div>--}}

                {{--                                    <div class="col-md-6">--}}
                {{--                                        <p class="text-info">Tasks <i class="far fa-question-circle"></i></p>--}}
                {{--                                        <div class="form-group form-check">--}}
                {{--                                            <label class="form-check-label">--}}
                {{--                                                <input class="form-check-input" type="checkbox"> Track time on task--}}
                {{--                                            </label>--}}
                {{--                                        </div>--}}
                {{--                                        <div class="form-group form-check">--}}
                {{--                                            <label class="form-check-label">--}}
                {{--                                                <input class="form-check-input" name="AllowEmployeeTask"--}}
                {{--                                                       id="AllowTaskEmployee" type="checkbox"> Allow Employee to add--}}
                {{--                                                new task--}}
                {{--                                            </label>--}}
                {{--                                        </div>--}}
                {{--                                    </div>--}}
                {{--                                </div>--}}
                {{--                            </div>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
                  </div>

            </form>
        </div>
    </div>
    @include('User::EmployeeDetail.trackUserAdvancedSettings')
@endsection

@section('post-load-scripts')
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.14/js/gijgo.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js" integrity="sha256-VeNaFBVDhoX3H+gJ37DpT/nTuZTdjYro9yBruHjVmoQ=" crossorigin="anonymous"></script>
@endsection

@section('page-scripts')
    <?php
    $ENV_SPECIAL_IDLE_ADMIN = ((new \App\Modules\User\helper)->checkOrgIdle()) ? 1 : 0;
    $add_multiple_network_track = (new \App\Modules\User\helper)->checkEnvPermission('ADD_MULTIPLE_NETWORK_TRACK') ? true : false;
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="../assets/js/incJSFile/TimeAttendance/trackUserSetting.js"></script>
    <script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js"></script>
    <script>
        $(".js-example-tokenizer").select2({
            tags: true,
            tokenSeparators: [',', ' ']
        })
        $("#userTrackBlockingApplciation").select2({
            tags: true,
            tokenSeparators: [","],
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term
                };
            },
            matcher: caseSensitiveMatcher
        });
        function caseSensitiveMatcher(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined') {
                return null;
            }
            if (data.text.indexOf(params.term) > -1) {
                var modifiedData = $.extend({}, data, true);
                return modifiedData;
            }
            return null;
        }
    </script>
    <script>
        let envAdminId = [];
        let organizationId = Number('<?php echo Session::get(env('Admin'))['token']['organization_id']; ?>');
        let MONITORING_CONTROL_LOCALIZATION =  JSON.parse('{{__('messages.monitoring_control_data')}}'.replace(/&quot;/g, '"'));
        let Track_user_settings =  JSON.parse('{{__('messages.Track_user_settings')}}'.replace(/&quot;/g, '"'));
        var saveText='{{__('messages.save')}}';
        var cancelText='{{__('messages.cancel')}}';
        let enter_longitude = '{{__('messages.enter_longitude')}}';
        let enter_latitude = '{{__('messages.enter_latitude')}}';
        let geo_range = '{{__('messages.geo_range')}}';
        let enter_location = '{{__('messages.enter_location')}}';
        let enter_network_name = '{{__('messages.enter_network_name')}}';
        let enter_network_ip = '{{__('messages.enter_network_ip')}}';
        let office_network_note = '{{__('messages.office_network_note')}}';
        let office_network = '{{__('messages.OfficeNetwork')}}';
        let note = '{{__('messages.note')}}';
        let storageStatus = @json((isset($storageStatus)?($storageStatus):(false)));

{{--{{ __('messages.enter_latitude') }}, {{ __('messages.enter_longitude') }}--}}
        <?php
        $envAdminIds = explode(',', env('SPECIAL_ADMIN'));
        foreach($envAdminIds as $key => $val){ ?>
        envAdminId.push(Number('<?php echo $val; ?>'));
        <?php } ?>
        let LOCATION_LOCALIZATION =  JSON.parse('{{__('messages.monitor_control_JS')}}'.replace(/&quot;/g, '"'));
        let ENV_SPECIAL_IDLE_ADMIN = '<?php echo $ENV_SPECIAL_IDLE_ADMIN; ?>'
        let add_multiple_network_track = '<?php echo $add_multiple_network_track; ?>'
        $(document).ready(function () {
            $('#autoUpdates_id').bootstrapToggle({
                on: '{{__('messages.on')}}',
                off: '{{__('messages.off')}}'
            });
        });

        //    for project based
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#mychkemp .form-check").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    </script>
@endsection
