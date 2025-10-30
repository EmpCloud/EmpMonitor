@include('User::Layout._managerProfile')
 <!-- Upper Nav Bar -->
<div class=" page-header">
    <div class="search-form">
        <form action="#" method="GET">
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
                    <a class="logo-box" href="/dashboard">
                        <span>
                            <img src="../assets/images/logos/{{ md5($_SERVER['HTTP_HOST']) }}.png" class="h-75">
                        </span>
                    </a>
                </div>
                <button type="button" class="navbar-toggler collapsed" data-toggle="collapse" aria-expanded="false"
                        id="toggleNav">
                    <i class="fas fa-angle-down"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->

            <div class="collapse navbar-collapse justify-content-between position-relative"
                 id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav mr-auto  skip-banner-align" id="sidebarhide">
                    <li class="collapsed-sidebar-toggle-inv"><a href="javascript:void(0)"
                                                                id="collapsed-sidebar-toggle-button"
                                                                title="{{ __('messages.collapse') }}"><i
                                class="fas fa-bars"></i></a></li>
                    <li><a href="javascript:void(0)" id="toggle-fullscreen" title="{{ __('messages.fullScreen') }}"><i
                                class="fas fa-expand"></i></a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
            <ul class="nav navbar-nav align-items-center mobile-nav"> 

        
                    <li class="nav-item"><a href="http://help.empmonitor.com/" target="_blank"
                                            class="btn btn-help mr-3">
                            <i class="fas fa-info-circle text-white"
                               title="{{ __('messages.help') }}"></i> {{ __('messages.help') }}</a></li>

             
                 <li class="nav-item"><a href="javascript:void(0)" class="right-sidebar-toggle"><label
                            class="mr-1 mb-0">{{ __('messages.welcome') }} </label>
                        <label
                            class="mb-0">{{Session::has('admin_session') ? Session::get('admin_session')['name'] : Session::get('employee_session')['first_name'];}}</label></a>
                </li>
                <li class="dropdown nav-item">
                   <a href="#" class="nav-link dropdown-toggle" id="navbarDropdown" data-toggle="dropdown"
                           role="button" aria-haspopup="true" aria-expanded="false"><img
                                src="../assets/images/avatars/avatar-new.png"
                                alt=""
                                class="rounded-circle"></a>
                  
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <li><a href="{{Session::has('admin_session') ? 'logout' : 'employee-logout'}}" onclick="clearStorage()">{{ __('messages.logout') }}</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>
