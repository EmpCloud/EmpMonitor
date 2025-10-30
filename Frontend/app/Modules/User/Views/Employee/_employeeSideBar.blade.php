<style>
    /* Minimal text styling only - no layout changes */
    .secondary-sidebar .accordion-menu > li > a {
        font-weight: 500;
    }
    
    .secondary-sidebar .accordion-menu .menu-icon {
        margin-right: 10px;
    }
    
    .secondary-sidebar .sub-menu li a {
        font-weight: 500;
    }
</style>

<div class="secondary-sidebar">
    <div class="secondary-sidebar-bar">
        <a href="<?php if(Session::has(env('Employee'))){?>
            get-details?id={{Session::get((new App\Modules\User\helper)->getHostName())['token']['user_id']}}
        <?php }else{?>dashboard<?php }?>"
           class="logo-box">
            <img src="../assets/images/logos/{{ md5($_SERVER['HTTP_HOST']) }}.png" class="img-fluid"/>
        </a>
    </div>
    <div class="secondary-sidebar-menu">
        <ul class="accordion-menu">
            <li class="dashboard" class="active-page">
                <a href="<?php if(Session::has('employee')){?>get-details?id={{Session::get((new App\Modules\User\helper)->getHostName())['token']['user_id']}}<?php }else{?>
                    dashboard<?php }?>">
                    <i class="menu-icon icon-home4" title="{{ __('messages.dashboard') }}"></i><span class="size">{{ __('messages.dashboard') }}</span>
                </a>
            </li>
            
            @if(Session::has(env('Manager')))
                <li class="dashboard">

                    <a href="{{"employee-details"}}">

                        <i class="menu-icon icon-users" title="Employee Details"></i><span
                            class="size">Employee</span>
                    </a>
                </li>
                <li>
                    <a class="dashboard" href="#">
                        <i class="menu-icon icon-security" title="Security"></i><span class="size">Security</span><i
                            class="accordion-icon fas fa-angle-left"></i>
                    </a>
                    <ul class="sub-menu">
                        <li><a class="dashboard size" title="Firewall" href="{{env('APP_URL')}}firewall">Firewall</a>
                        </li>
                        <li><a class="dashboard size" title="Add Domain/Category" href="{{env('APP_URL')}}add-domain">Add
                                Domain/Category</a>
                        </li>
                        <li><a class="dashboard size" title="View Domain" href="{{env('APP_URL')}}view-domain">View
                                Domain(s)</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="dashboard">
                        <i class="menu-icon icon-file-text" title="Reports"></i><span class="size">Reports</span><i
                            class="accordion-icon fas fa-angle-left"></i>
                    </a>
                    <ul class="sub-menu">
                        <li><a class="dashboard size" title=" Reports Download" href="{{env('APP_URL')}}Report">Reports
                                Download</a>
                        </li>
                        <li><a class="dashboard size" title="Consolidated Reports"
                               href="/consolidated-reports">Consolidated Reports</a>
                        </li>
                    </ul>
                </li>
                <li class="dashboard">
                    <a href="{{"/Setting"}}">
                        <i class="menu-icon icon-cog" title="Settings"></i><span class="size">Settings</span>
                    </a>
                </li>
            @endif
          
        </ul>
    </div>
</div>
