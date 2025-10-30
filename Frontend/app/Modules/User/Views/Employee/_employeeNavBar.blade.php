<!-- Minimal Employee Header Text Styling Only -->
<style>
    /* Text styling only - no layout changes */
    .page-header .navbar-nav li a {
        font-weight: 500;
    }
</style>

<div class="page-header">
    <div class="search-form">
        <form action="#" method="GET">
            @csrf
            <div class="input-group">
                <input type="text" name="search" class="form-control search-input" placeholder="Type something...">
                <span class="input-group-btn">
                                    <button class="btn btn-default" id="close-search" type="button"><i
                                            class="icon-close"></i></button>
                                </span>
            </div>
        </form>
    </div>
    <nav class="navbar navbar-default navbar-expand-md">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <div class="logo-sm">
                    <a href="javascript:void(0)" id="sidebar-toggle-button"><i class="fas fa-bars"></i></a>
                    {{--                    <a class="logo-box" href="index.html"><span>EmpMonitor</span></a>--}}
                </div>
                <button type="button" class="navbar-toggler collapsed" data-toggle="collapse"
                       id="toggleNavEmployee" aria-expanded="false">
                    <i class="fas fa-angle-down"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->

            <div class="collapse navbar-collapse justify-content-between" >
                <ul class="nav navbar-nav mr-auto">
                    <li class="collapsed-sidebar-toggle-inv"><a href="javascript:void(0)"
                                                                id="collapsed-sidebar-toggle-button"><i
                                class="fas fa-bars"></i></a></li>
                    <li><a href="javascript:void(0)" id="toggle-fullscreen"><i class="fas fa-expand"></i></a></li>
                    {{--                    <li><a href="javascript:void(0)" id="search-button"><i class="fas fa-search"></i></a></li>--}}
                </ul>
            </div>

            @if($errors->any())
                <p style="color:red">{{$errors->first()}}</p>
            @endif
            <div class="employee-mobile-nav">
        <!-- button help  -->
            @if((new App\Modules\User\helper)->checkHost())
                <a href="http://help.empmonitor.com/" target="_blank" class="btn btn-help bg-transparent" style="background:transparent !important;">
                    <i class="fas fa-info-circle text-white"></i> {{ __('messages.help') }}</a>
            @endif
            @if((new App\Modules\User\helper)->getHostName() !== env('Admin'))
                <li class="dropdown nav-item align-self-center">
                    <i class="fas fa-chalkboard-teacher nav-link text-primary" style="color: white !important;" title="{{ __('messages.changeRole') }} "
                       data-toggle="dropdown"></i>
                    <ul class="dropdown-menu">
                        @foreach(Session::get((new App\Modules\User\helper)->getHostName())['token']['roles'] as $key => $value)
                            <li>
                                <a class="dropdown-item @if($value['role_id'] == Session::get((new App\Modules\User\helper)->getHostName())['token']['role_id']) active"
                                   href="#" @else " href="change-role/{{ $value['role_id'] }}" @endif
                                >{{ $value['name'] }}</a></li>

                        @endforeach

                    </ul>
                </li>
            @endif

           

            <ul class="nav navbar-nav">
                @if(env('APP_ENV') =='dev' || env('APP_ENV') == 'local')
                    <li class="nav-item">
                        <a href="#" onclick="openToast('All')" id="toastNotifications"><i
                                class="fas fa-bell text-primary"></i><span
                                class="counter counter-lg text-light notification"><div><p class="bg-danger"
                                                                                           id="unReadPushNotificationCount">&nbsp;0&nbsp;</p></div></span></a>
                    </li>
                @endif
                <li class="nav-item d-md-block"><a href="javascript:void(0)"
                                                   class="right-sidebar-toggle">{{ __('messages.welcome') }} </a>
                </li>
                <li class="nav-item d-md-block"><h5 style="padding-top: 19px;"><span
                            style="font-size: 14px;color: white;font-weight: 400;">{{Session::get((new App\Modules\User\helper)->getHostName())['token']['login']}}</span>
                    </h5>
                </li>
                <li class="dropdown nav-item d-md-block">
                    <a href="#" class="nav-link dropdown-toggle" id="navbarDropdown" data-toggle="dropdown"
                       role="button"
                       aria-haspopup="true" aria-expanded="false" style="padding-left: 11px !important;"><img src="../assets/images/avatars/avatar-new.png" alt=""
                                                                       class="rounded-circle"/>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <li><a href="{{route('employee-logout',(new App\Modules\User\helper)->getHostName())}}"
                                   onclick="clearStorage()">{{ __('messages.logout') }}</a></li>
                    </ul>
                </li>
            </ul>
         </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</div>
