<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" type="text/css" rel="stylesheet"/>

<script src="../assets/js/JqueryPagination/jquery.jqpagination.js" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

<style>
    .file-upload {
        display: none;
    }
    .emp-details-upload i{
        position: absolute;
        bottom: 0;
        font-size: 25px;
    }
    .emp-details-upload input{
        opacity: 0;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }

    #loaderForm {
        position: absolute;
        background: #000000a3;
        width: 100%;
        height: 100%;
        z-index: 999;
    }
    #loaderForm1 {
        position: absolute;
        background: #000000a3;
        width: 100%;
        height: 100%;
        z-index: 999;
    }
    #autoacceptTimeclaim + div .toggle-off {
        padding-top: 0;
        margin-top: 0.4rem;
        background: transparent;
    }
    .btn-xs.toggle-on, .btn-xs.toggle-off{
        padding-top: 0.8em;
    }
    .toggle.btn-xs{
        display: block;
        margin-left: 13px;
    }
    .introjs-showElement {
        z-index: 999999 !important;
    }
</style>
<!-- Add Emp Details Modal -->
<div class="modal fade" id="addEmpModal" tabindex="-1" role="dialog" aria-labelledby="empLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">{{ __('messages.add') }}  {{ __('messages.employee') }} </h5>
                @if(session('error'))
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
                    <script>
                        errorMessage = '<?php echo session('error'); ?>';
                        Swal.fire({
                            position: 'inherit',
                            icon: 'error',
                            title: errorMessage,
                            showConfirmButton: false,
                            timer: 1500
                        });

                    </script>
                @endif
                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" id="emp-register" name="registrationFormData">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for=""><b>{{ __('messages.firstName') }} *</b></label>
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="{{ __('messages.enterFirstName') }}"
                                       onkeydown="return alphaOnly(event,1);"/>
                                <div class="error" id="usrname"
                                     style="color: red;">{{ $errors->first('username') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="f_name"><b>{{ __('messages.lastName') }} *</b></label>
                                <input type="text" class="form-control" id="f_name" name="Full_Name"
                                       placeholder="{{ __('messages.enterLastName') }}"
                                       onkeydown="return alphaOnly(event,2);"/>
                                <div class="error" id="FullName"
                                     style="color: red;">{{ $errors->first('f_name') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emp_email"><b>{{ __('messages.emailAddress') }}  *</b></label>
                                <input type="email" class="form-control" id="emp_email" name="email"
                                       aria-describedby="emailHelp"
                                       placeholder="{{ __('messages.enterEmailAddress') }}"
                                       onkeydown="clearErrorMsgs('email')"/>
                                <div class="error" id="EmailAddress"
                                     style="color: red;">{{ $errors->first('email') }}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="passwd"><b>{{ __('messages.password') }} *</b></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="passwd" name="password"
                                           placeholder="{{ __('messages.password') }}"
                                           onkeydown=" return onspace(event, 'pswd')"/>
                                    <div class="input-group-append">
                                        <span toggle="#passwd"
                                              style="line-height: 1.6;border: 1px solid #ced4da;border-left-color: white;"
                                              class="btn btn-default fas fa-eye toggle-password-show"></span>
                                    </div>
                                </div>
                                <div class="error" id="Paswd"
                                     style="color: red;">{{ $errors->first('password') }}</div>
                                <p>{{ __('messages.pwdValidation') }} </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="c_passwd"><b>{{ __('messages.confirm') }} {{ __('messages.password') }}
                                        *</b></label>

                                <div class="input-group">
                                    <input type="password" class="form-control" id="c_passwd" name="confirmPassword"
                                           placeholder="{{ __('messages.confirm') }} {{ __('messages.password') }}"
                                           onkeydown=" return onspace(event, 'cpswd')"/>
                                    <div class="input-group-append">
                                        <span toggle="#c_passwd"
                                              style="line-height: 1.6;border: 1px solid #ced4da;border-left-color: white;"
                                              class="btn btn-default fas fa-eye toggle-password-show-c"></span>
                                    </div>
                                </div>
                                <div class="error" id="CPaswd"
                                     style="color: red;">{{ $errors->first('confirmPassword') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone"><b>{{ __('messages.mobile') }}</b></label>
                                <input name="number" type="tel" class="form-control" id="telephone"
                                       onkeypress="return numbersOnly(event);"/>
                                <p style="color: green"><span id="valid-msg" class="hide"></span></p>
                                <p style="color: red"><span id="error-msg" class="hide"></span></p>
                                <input type="hidden" id="validContact" value="0">
                                <div class="error"
                                     style="color: red;">{{ $errors->first('number') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="c_passwd"><b>{{ __('messages.employeeCode') }}
                                        *</b></label>
                                <input type="text" class="form-control" name="empCode"
                                       placeholder="{{ __('messages.employeeCode') }}"
                                       onkeydown="clearErrorMsgs('Ecode')"/>
                                <div class="error" id="EmpCodeError"
                                     style="color: red;">{{ $errors->first('empCode') }}</div>
                            </div>
                        </div> 
                        
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_join"><b>{{ __('messages.timezone') }} *</b></label>
                                <select name="TimeZone" id="timeZoneAppend" class="form-control " onchange="clearErrorMsgs('TZ')">
                                       <option id="" data-offset="" disabled="" selected="">Select Timezone</option>
                                        <option id="tz-opt-0" data-zone="Atlantic/Canary" data-offset="3600">(UTC +00:00) Canary, Atlantic</option>
                                        <option id="tz-opt-0" data-zone="Atlantic/Canary" data-offset="3600">(UTC +00:00) Canary, Atlantic</option>
                                        <option id="tz-opt-38" data-zone="Asia/Kolkata" data-offset="19800">(UTC +05:30) Kolkata, Asia</option>
                                        <option id="tz-opt-46" data-zone="Asia/Yangon" data-offset="23400">(UTC +06:30) Yangon, Asia</option>
                                        <option id="tz-opt-62" data-zone="America/Anchorage" data-offset="-28800">(UTC -09:00) Anchorage, America</option>
                                        <option id="tz-opt-76" data-zone="Pacific/Midway" data-offset="-39600">(UTC -11:00) Midway, Pacific</option>
                                </select>
                                <div class="error" id="ErrorTimeZone"
                                     style="color: red;">{{ $errors->first('ErrorTimeZone') }}</div>
                            </div>
                        </div> 
                          
                        <div class="col-md-6" style="display: none;">
                            <div class="form-group">
                                <label class="font-weight-bold">Selected Superior Role Members  </label>

                                <select multiple="multiple" class="form-control js-example-tokenizer search-field chosen-results" id="addSelectedManagerList">
                                </select>

                            </div>
                        </div> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" id="empReg"
                            class="btn btn-primary"> {{ __('messages.register') }} {{ __('messages.employee') }}</button>
                </div>
            </form>
            <div class="col-md-12" id="loaderForm1" style="display: none;">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Emp Details Modal -->
<div class="modal fade" id="editEmpModal" tabindex="-1" role="dialog" aria-labelledby="empLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmpLabel">{{ __('messages.edit') }}  {{ __('messages.employee') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" id="Emp-edit">
                @csrf
                <div class="modal-body" id="editEmpModal">
                    <div class="row">
                        <div class="col-md-2 align-self-center">
                            <label for="f_upload"></label>
                            <img id="img" style="height: 130px !important; width:148px !important;"
                                 class="profile-pic img-fluid rounded-circle " class=""
                                 src="../assets/images/avatars/avatar1.png"/>
                            <span class="emp-details-upload">
                                <i class="fa fa-camera" aria-hidden="true"></i>
                                <input type="file" name="file" click-type="type2" accept=".png, .jpg, .jpeg"
                                       id="profilepicupload" class="profile_picupload"
                                       title="{{__('messages.imageUploadInfo')}}"
                                       multiple/>
                            </span>
                        </div>
                        <div class="col-sm">
                            <div class="form-group">
                                <label for=""><b>{{ __('messages.firstName') }} *</b></label>
                                <input name="name" type="text" class="form-control" id="Name"
                                       placeholder="{{ __('messages.firstName') }}"
                                       onkeydown="return alphaOnly(event,1);"/>
                                <div class="error" id="ErrorsName"
                                     style="color: red;">{{ $errors->first('ErrorsName') }}</div>
                            </div>

                            <div class="form-group">
                                <label for="emp_email"><b>{{ __('messages.emailAddress') }} *</b></label>
                                <input name="email" id="emp_emailAddress" type="email" class="form-control"
                                       aria-describedby="emailHelp"
                                       placeholder="{{ __('messages.emailAddress') }} "
                                       onkeydown="clearErrorMsgs('email')"/>

                                <div class="error" id="ErrorsEmailAddress"
                                     style="color: red;">{{ $errors->first('ErrorsEmailAddress') }}</div>
                            </div> 
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label for=""><b>{{ __('messages.lastName') }} *</b></label>
                                <input name="Full_name" type="text" class="form-control" id="fullName"
                                       placeholder="{{ __('messages.lastName') }}"
                                       onkeydown="return alphaOnly(event,2);"/>
                                <div class="error" id="ErrorsFullName"
                                     style="color: red;">{{ $errors->first('ErrorsFullName') }}</div>
                            </div>
                            <div class="form-group">
                                <label for="Emp_code"><b>{{ __('messages.employeeCode') }}
                                        *</b></label>
                                <input name="EmpCode" type="text" class="form-control" id="emp_code"
                                       placeholder="{{ __('messages.employeeCode') }}"
                                       onkeydown="clearErrorMsgs('Ecode')"/>

                                <div class="error" id="ErrorsEmpCode"
                                     style="color: red;">{{ $errors->first('ErrorsEmpCode') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emp_email"><b>{{ __('messages.password') }} *</b></label>

                                <div class="input-group">
                                    <input name="password" id="password-editEmp" type="password"
                                           class="form-control"
                                           aria-describedby="emailHelp" placeholder=" {{ __('messages.password') }}"
                                           onkeydown=" return onspace(event, 'pswd')"/>

                                    <div class="input-group-append">
                                            <span toggle="#password-editEmp"
                                                  style="line-height: 1.6;border: 1px solid #ced4da;border-left-color: white;"
                                                  class="btn btn-default fas fa-eye toggle-password-show-edit"></span>
                                    </div>
                                </div>
                                <div class="error" id="ErrorsPaswd"
                                     style="color: red;">{{ $errors->first('ErrorsPaswd') }}</div>
                                <p>{{ __('messages.pwdValidation') }} </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="c_passwd"><b>{{ __('messages.confirm') }}  {{ __('messages.password') }} *</b></label>

                                <div class="input-group">
                                    <input type="password" class="form-control" id="cpassword-editEmp"
                                           name="confirmPassword"
                                           placeholder="{{ __('messages.confirm') }} {{ __('messages.password') }}"
                                           onkeydown=" return onspace(event, 'cpswd')"/>

                                    <div class="input-group-append">
                                            <span toggle="#cpassword-editEmp"
                                                  style="line-height: 1.6;border: 1px solid #ced4da;border-left-color: white;"
                                                  class="btn btn-default fas fa-eye toggle-password-show-edit-c"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="error" id="ErrorsCPaswd"
                                 style="color: red;">{{ $errors->first('ErrorsCPaswd') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Edittelephones"><b> {{ __('messages.mobile') }}  </b></label>
                                <input name="number" type="tel" class="form-control" id="Edittelephones"
                                       onkeypress="return numbersOnly(event);"/>
                                <input type="hidden" id="validContactEdit" value="0">
                                <p style="color: green"><span id="valid-msgs" class="hide"></span></p>
                                <p style="color: red"><span id="error-msgs" class="hide"></span></p>
                                <div class="error" id="ErrorsContact"
                                     style="color: red;">{{ $errors->first('ErrorsContact') }}</div>
                            </div>
                        </div>
                     
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><b>{{ __('messages.timezone') }} *</b></label>
                                <select name="timeZone" id="timezoneAddendEdit"
                                        class="form-control " onchange="clearErrorMsgs('TZ')"></select>
                                <div class="error" id="ErrorTimeZoneField"
                                     style="color: red;">{{ $errors->first('ErrorTimeZoneField') }}</div>
                            </div>

                        </div>
                         
     

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('messages.close') }}</button>
                    <input type="hidden" class="edit-loc btn btn-primary hide-loc" id="hide" name="hideId">
                    <input type="hidden" id="FormCalled" value="">
                    <button type="submit" id="emp-edit" class="btn btn-primary">{{ __('messages.update') }}</button>
                </div>
            </form>
            <div class="col-md-12" id="loaderForm" style="display: none;">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</div>
