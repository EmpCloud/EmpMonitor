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
            <li>
                <a title="{{ __('messages.employee-details') }}"
                   href="{{route('employee-details',(new App\Modules\User\helper)->getHostName())}}">  <i class="menu-icon icon-users"
                                                                                                          title="{{ __('messages.employee') }}"></i>{{ __('messages.employee-details') }}</a>
            </li>
            <li>
                <a href="{{route('Storage',(new App\Modules\User\helper)->getHostName())}}"
                   title="{{ __('messages.storageType') }}"><i class="menu-icon icon-cog"
                                                               title="{{ __('messages.settings') }}"></i>{{ __('messages.storageType') }}</a>
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
