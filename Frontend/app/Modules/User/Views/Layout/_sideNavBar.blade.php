<div class="secondary-sidebar">
    <div class="secondary-sidebar-bar">
        <a href="{{route('dashboard',(new App\Modules\User\helper())->getHostName()) }}" class="logo-box">
            <img src="../assets/images/logos/{{ md5($_SERVER['HTTP_HOST']) }}.png" class="img-fluid" />
            <!-- <img src="../assets/images/logos/icon.png" class="img-fluid"/>
            <img src="../assets/images/logos/Logo.png" class="img-fluid"/> -->
        </a>
    </div>
    <div class="secondary-sidebar-menu">
        <ul class="accordion-menu" id="sidebar_menus">
         
        @if(Session::has('admin_session'))
            <li>
                <a href="#">
                    <i class="menu-icon icon-users"
                       title="{{ __('messages.employee') }}"></i><span>{{ __('messages.employee') }}</span>
                    <i class="accordion-icon fas fa-angle-left"></i>
                </a>
                <ul class="sub-menu">
                    <li id="main-step4" class="main-step"><a title="{{ __('messages.employee-details') }}"
                            href="{{ route('employee-details', (new App\Modules\User\helper())->getHostName()) }}">{{ __('messages.employee-details') }}</a>
                    </li>  
                </ul>
                
            </li>
            <li>
                <a href="#">
                    <i class="menu-icon icon-cog"
                       title="{{ __('messages.employee') }}"></i><span>{{ __('messages.settings') }}</span>
                    <i class="accordion-icon fas fa-angle-left"></i>
                </a>
                <ul class="sub-menu">
                      <li>
                 <a href="{{ route('manageLocations', (new App\Modules\User\helper())->getHostName()) }}">
                   <span>Add {{ __('messages.Location') }}</span>
                </a>
                  <a href="{{ route('manageDepartment', (new App\Modules\User\helper())->getHostName()) }}">
                   <span>Add {{ __('messages.department') }}</span>
                </a>
                <a href="localization">
                    <span title="{{ __('messages.localization') }}">{{ __('messages.localization') }}</span>
                </a> 
            </li> 
                </ul>
                
            </li>
          
            @endif
            <li>
               @php  $url = Session::has('employee_session') ? 'attendance-history-employee' : 'attendance-history'; @endphp
               @if(Session::has('employee_session'))
               <a href="">
                    <i class="menu-icon fas fa-tachometer-alt"
                        title="{{ __('messages.dashboard') }}"></i><span>{{ __('messages.dashboard') }}</span>
                </a>
               @else
                <a href="{{ route($url, (new App\Modules\User\helper())->getHostName()) }}">
                    <i class="menu-icon far fa-calendar-alt"
                        title="{{ __('messages.timesheets') }}"></i><span>{{ __('messages.timesheets') }}</span>
                </a>
                <a href="{{ route('reports', (new App\Modules\User\helper())->getHostName()) }}">
                    <i class="menu-icon icon-file-text"
                       title="{{ __('messages.reports') }}"></i><span>{{ __('messages.reports') }}</span>
                </a> 
                 <a href="{{route('productivity',(new App\Modules\User\helper)->getHostName())}}"
                                   title="{{ __('messages.productivityRules') }}"> <i class="menu-icon fas fa-tasks"
                                       title="{{ __('messages.productivityRules') }}"></i>{{ __('messages.productivityRules') }}</a>
                @endif
            </li>
        </ul>
    </div>
</div>

