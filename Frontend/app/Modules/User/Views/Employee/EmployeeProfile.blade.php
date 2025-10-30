@extends('User::Employee._employeeLayout')
@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           @if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif @endif Employee Profile Setting</title>
@endsection

@section('content')
    <div class="page-inner no-page-title">
        <div id="main-wrapper">
            <div class="col-7">
                <div class="card">
                    <div class="card-body">

                        <h4 style="font-weight:500;color: #111112 !important;">User Info</h4>

                        <div class="row mt-5">
                            <div class="col-md-4 text-center">
                                @if(substr($show_details['data']['photo_path'],0,5)==="https")
                                    <img id="img" style="height:140px; width: 140px; border-radius: 50% !important;"
                                         src="{{$show_details['data']['photo_path']}}"
                                         class="img-fluid rounded-circle"/>
                                @else
                                    <img id="img" style="height:140px; width: 140px; border-radius: 50% !important;"
                                         src="{{env('API_HOST').$show_details['data']['photo_path']}}"
                                         class="img-fluid rounded-circle"/>
                                @endif
                            </div>
                            <div class="col-md-8">

                                <table class="table table-borderless">
                                    <tbody>
                                    <tr>
                                        <th>Full Name</th>
                                        <td id="Fn">{{$show_details['data']['full_name']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Email-Id</th>
                                        <td id="Em">{{$show_details['data']['email']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Password</th>
                                        <td id="psw">*********
                                            {{--                                            {{md5($show_details['data']['password'])}}--}}
                                            <a href="#" style="color: blue" data-toggle="modal"
                                               data-target="#changePassword"
                                            >Change?</a>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Role</th>
                                        <td id="ro">{{$show_details['data']['role_name']}}</td>
                                    </tr> --}}
                                    <tr>
                                        <th>Emp-Code</th>
                                        <td id="Ec">{{$show_details['data']['emp_code']}}</td>
                                    </tr>

                                    <tr>
                                        <th>D.O.J</th>
                                        <td id="Dj">{{explode("T",$show_details['data']['date_join'])[0]}}</td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Location</th>
                                        <td id="lo">{{$show_details['data']['location_name']}}</td>
                                    </tr> --}}
                                    {{-- <tr>
                                        <th>Department</th>
                                        <td id="dpt">{{$show_details['data']['department_name']}}</td>
                                    </tr> --}}
                                    <tr>
                                        <th>Contact No.</th>
                                        <td id="Mn">{{$show_details['data']['phone']}}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td id="Ad">{{$show_details['data']['address']}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="forgotPasswdModalTitle"
         aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswdModalTitle">
                        Update Password
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

                <div class="modal-body">
                    <div class="form-group">
                        <label for="email_id_forgot">Change Password</label>
                        <input
                            type="password"
                            class="form-control"
                            id="new_pwd"
                            aria-describedby="emailHelp"
                            placeholder="Enter new password"
                            required
                        />
                        <p id="fg-pwd-error" style="color: red;"></p>

                        <input
                            type="password"
                            class="form-control"
                            id="conf_pwd"
                            aria-describedby="emailHelp"
                            placeholder="Confirm new password"
                            required
                        />
                        <p id="confirm-pwd-err" style="color: red;"></p>

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
                    <button type="button" id="change-pwd-btn" onclick="changePassword()" class="btn btn-primary">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('post-load-scripts')
    <script src="../assets/plugins/switchery/switchery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
@endsection

@section('page-scripts')
    <script src="../assets/js/incJSFile/_employeeChangePassword.js"></script>
@endsection
