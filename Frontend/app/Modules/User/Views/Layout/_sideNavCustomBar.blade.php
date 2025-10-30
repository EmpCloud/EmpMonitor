<div class="secondary-sidebar">
    <div class="secondary-sidebar-bar">
        <a href="{{route('dashboard',(new App\Modules\User\helper)->getHostName())}}" class="logo-box">
            <img src="../assets/images/logos/{{ md5($_SERVER['HTTP_HOST']) }}.png" class="img-fluid"/>
            <!-- <img src="../assets/images/logos/icon.png" class="img-fluid"/>
            <img src="../assets/images/logos/Logo.png" class="img-fluid"/> -->
        </a>
    </div>
    <div class="secondary-sidebar-menu">
        <ul class="accordion-menu" id="sidebar_menus">
            @if(isset(Session::get((new App\Modules\User\helper)->getHostName() )['dashboard']) && Session::get((new App\Modules\User\helper)->getHostName() )['dashboard']  == 1)
                <li id="main-step7" class="main-step">
                    <a href="{{route('dashboard',(new App\Modules\User\helper)->getHostName())}}">
                        <i class="menu-icon icon-home4"
                           title="{{ __('messages.dashboard') }}"></i><span>{{ __('messages.dashboard') }}</span>
                    </a>
                </li>
            @endif
            <li>
                <a href="#">
                    <i class="menu-icon icon-users"
                       title="{{ __('messages.employee') }}"></i><span>{{ __('messages.employee') }}</span>
                    <i class="accordion-icon fas fa-angle-left"></i>
                </a>
                <ul class="sub-menu">

                    @if(isset(Session::get((new App\Modules\User\helper)->getHostName() )['employee_details']) && Session::get((new App\Modules\User\helper)->getHostName() )['employee_details']  == 1)
                        @if((new App\Modules\User\helper)->getHostName() === env('Admin') || ((new App\Modules\User\helper)->getHostName() === env('Manager') && in_array('employee_view',array_column(Session::get(env('Manager'))['token']['permissionData'],'permission'))))
                            <li id="main-step4" class="main-step"><a title="{{ __('messages.employee-details') }}"
                                                                     href="{{route('employee-details',(new App\Modules\User\helper)->getHostName())}}">{{ __('messages.employee-details') }}</a>
                        @endif
                    @endif
                    <li><a title="Manage Employment"
                           href="{{route('managerEmployment',(new App\Modules\User\helper)->getHostName())}}">
                            <span>Manage Employment</span></a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="menu-icon icon-cog"
                       title="{{ __('messages.settings') }}"></i><span>{{ __('messages.settings') }}</span><i
                        class="accordion-icon fas fa-angle-left"></i>
                    {{--                        ></i>--}}
                </a>
                <ul class="sub-menu">
                    @if(isset(Session::get((new App\Modules\User\helper)->getHostName() )['setting_dept_location']) && Session::get((new App\Modules\User\helper)->getHostName() )['setting_dept_location']  == 1)
                        @if((new App\Modules\User\helper)->getHostName() === env('Admin') || ((new App\Modules\User\helper)->getHostName() === env('Manager') && in_array('settings_locations_browse',array_column(Session::get(env('Manager'))['token']['permissionData'],'permission'))))

                            <li id="main-step2" class="main-step">
                                <a href="{{route('manageLocations',(new App\Modules\User\helper)->getHostName())}}"
                                   title="{{ __('messages.manageLocDept') }}">{{ __('messages.manageLocDept') }}</a>
                            </li>
                        @endif
                    @endif

                    @if(isset(Session::get((new App\Modules\User\helper)->getHostName())['setting_role'] ) && Session::get((new App\Modules\User\helper)->getHostName() )['setting_role']  == 1)
                        @if((new App\Modules\User\helper)->getHostName() === env('Admin') || ((new App\Modules\User\helper)->getHostName() === env('Manager') && in_array('roles_browse',array_column(Session::get(env('Manager'))['token']['permissionData'],'permission'))))

                            <li id="main-step3" class="main-step"><a href="role-view"
                                                                     title="{{ __('messages.roles') }}">{{ __('messages.roles') }}</a>
                            </li>
                        @endif
                    @endif
                    @if(isset(Session::get((new App\Modules\User\helper)->getHostName() )['setting_shift']) && Session::get((new App\Modules\User\helper)->getHostName() )['setting_shift']  == 1)
                        @if((new App\Modules\User\helper)->getHostName() === env('Admin') || ((new App\Modules\User\helper)->getHostName() === env('Manager') && in_array('shift_view',array_column(Session::get(env('Manager'))['token']['permissionData'],'permission'))))

                            <li><a href="shift-management-view"
                                   title="{{ __('messages.shiftManagement') }}">{{ __('messages.shiftManagement') }}
                                </a>
                            </li>
                        @endif
                    @endif
                    @if( isset(Session::get((new App\Modules\User\helper)->getHostName())['setting_localization'] ) && Session::get((new App\Modules\User\helper)->getHostName() )['setting_localization']  == 1)
                        @if((new App\Modules\User\helper)->getHostName() === env('Admin') || ((new App\Modules\User\helper)->getHostName() === env('Manager') && in_array('localize_view',array_column(Session::get(env('Manager'))['token']['permissionData'],'permission'))))

                            <li id="main-step1" class="main-step"><a href="localization"
                                                                     title="{{ __('messages.localization') }}">{{ __('messages.localization') }}</a>
                            </li>
                        @endif
                    @endif
                </ul>
            </li>
            <li class="">
                <a href="#">
                    <i class="menu-icon fas fa-exchange-alt"
                       title="{{ __('messages.settings') }}"></i><span>HRMS</span><i
                        class="accordion-icon fas fa-angle-left"></i>
                    {{--                        ></i>--}}
                </a>
                <ul class="sub-menu">
                    @if((new App\Modules\User\helper)->getHostName() === env('Admin') || ((new App\Modules\User\helper)->getHostName() === env('Manager')))
                        <li><a title="{{__('messages.employee')}}"
                               href="/admin/hrms/basic-detail" target="_blank"> Employee Settings</a>
                        </li>
                        <li><a title="{{__('messages.attendance')}}"
                               href="/admin/hrms/attendance-new" target="_blank">{{__('messages.attendance')}}</a>
                        </li>
                        <li><a title="organization"
                               href="/admin/hrms/organization" target="_blank">Organisation</a>
                        </li>
                        <li><a title="Biometric"
                               href="/admin/hrms/hrms-biometric" target="_blank">Biometric</a>
                        </li>
                    @endif
                </ul>
            </li>


        </ul>
    </div>
    @if(Session::has(env('Admin')) && (new App\Modules\User\helper)->getHostName() === env('Admin'))
        <button type="button" class="btn btn-primary license"><i class="fas fa-award"></i>
            <span> {{ __('messages.licenseInfo') }}</span>
            <text id="licensesContext"></text>
        </button>
    @endif
    <div class="text-center settings-menu-btn pb-0">
        @if(Session::has(env('Admin')) && (new App\Modules\User\helper)->checkHost() && (new App\Modules\User\helper)->getHostName() === env('Admin'))
            <p class="mb-0 main-step" id="main-step5">
                <a target="_blank" class="btn btn-primary btn-block rounded-0"
                   href="{{route('download-user-setup',(new App\Modules\User\helper)->getHostName())}}">
                    <i class="menu-icon fas fa-download text-white" title="{{ __('messages.download') }}"></i> <span
                        class="ml-2">{{ __('messages.download') }}</span>
                </a>
            </p>
        @endif
        @if($_SERVER['HTTP_HOST'] === 'app.tictacteam.com' && Session::has(env('Admin'))  && (new App\Modules\User\helper)->getHostName() === env('Admin'))
            <p class="mb-0 main-step" id="main-step5">
                <a target="_blank" class="btn btn-primary btn-block rounded-0 downloadAgent"
                   href="{{route('download-user-setup',(new App\Modules\User\helper)->getHostName())}}">
                    <i class="menu-icon fas fa-download text-white" title="{{ __('messages.download') }}"></i> <span
                        class="ml-2">{{ __('messages.download') }}</span>
                </a>
            </p>
        @endif

        @if((new App\Modules\User\helper)->getHostName() === env('Admin') && (new App\Modules\User\helper)->checkHost())
            <script>
                function initFreshChat() {
                    window.fcWidget.init({
                        token: '{{env('FRESH_CHAT_TOKEN')}}',
                        host: 'https://wchat.freshchat.com',
                        config: {
                            showFAQOnOpen: false,
                            hideFAQ: true,
                            "cssNames": {
                                "widget": "custom_fc_frame"//old value is "fc_frame"
                            }
                        },
                    });
                }

                function initialize(i, t) {
                    var e;
                    i.getElementById(t) ? initFreshChat() : ((e = i.createElement("script")).id = t, e.async = !0,
                        e.src = "https://wchat.freshchat.com/js/widget.js", e.onload = initFreshChat, i.head.appendChild(e))
                }

                function initiateCall() {
                    initialize(document, "freshchat-js-sdk")
                }

                window.addEventListener ? window.addEventListener("load", initiateCall, !1) : window.attachEvent("load", initiateCall, !1);
            </script>
        @endif
    </div>
</div>
