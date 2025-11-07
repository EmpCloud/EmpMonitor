@extends('User::Layout._layout')

@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
            {{env('WEBSITE_TITLE')}} | @endif Dashboard</title>
@endsection

@section('extra-style-links')
@endsection

@section('page-style')
    @include('User::Layout._modernStyles')
    <style>
        .stats-card {
            padding: 20px; 
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .mgm{
                background: linear-gradient(45deg, #9f78ff, #32cafe);
        }
        .mgm:hover{
            background: linear-gradient(45deg, #32cafe, #9f78ff);
        }
        .kps{
              background: linear-gradient(45deg, #a376fc, #f96f9b);
        }
        .kps:hover{
            background: linear-gradient(45deg, #f96f9b, #a376fc);
        }
        .dav {
                  background: linear-gradient(45deg, #f95058, #fc9197);
        }
        .dav:hover{
                background: linear-gradient(45deg, #fc9197, #f95058);
        }
        .stats-card h3 {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
            color: #fff;
        }
        
        .stats-card p {
            font-size: 15px;
            color: #fff;
            margin: 0;
            font-weight:600;
        }
        
        .stats-card .icon {
            font-size: 40px;
            color: #ffffffff;
        }
        
        .table-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table-card h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        
        .filter-buttons{
            display: flex;
        }

        .filter-btn {
            padding: 5px 15px;
            margin: 0 5px;
            border: 1px solid #0686d8;
            background: #fff;
            color: #0686d8;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            width: 50%;
        }
        
        .filter-btn.active {
            background: #0686d8;
            color: #fff;
        }
        
        .filter-btn:hover {
            background: #0686d8;
            color: #fff;
        }
        
        .usage-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .usage-item:last-child {
            border-bottom: none;
        }
        
        .usage-name {
            font-size: 14px;
            color: #333;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .usage-time {
            font-size: 14px;
            font-weight: 600;
            color: #0686d8;
            margin-left: 10px;
        }
        
        .employee-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .employee-item:last-child {
            border-bottom: none;
        }
        
        .employee-info {
            flex: 1;
        }
        
        .employee-name {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }
        
        .employee-email {
            font-size: 12px;
            color: #666;
        }
        
        .employee-time {
            font-size: 14px;
            font-weight: 600;
            color: #0686d8;
        }
        
        .loader {
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0686d8;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
        }
        
        .section-divider {
            margin: 20px 0;
        }
    </style>
@endsection

@section('content')
    <div class="page-inner no-page-title">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item active"><a href="dashboard" style="color: #0686d8;font-weight: 500;">Dashboard</a></li>
                    </ol>
                </nav>
                <h1 class="page-title" style="font-size: 21px;color: #111112 !important;">Dashboard</h1>
            </div>
            
            <!-- Employee Statistics -->
            <div class="row">
                <div class="col-md-4 ">
                    <div class="stats-card mgm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p>Total Employees</p>
                                <h3 id="totalEmployees">0</h3>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <div class="stats-card kps">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p>Present Today</p>
                                <h3 id="presentEmployees">0</h3>
                            </div>
                            <div class="icon" >
                               <i class="fa fa-user-tie"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <div class="stats-card dav">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p>Absent Today</p>
                                <h3 id="absentEmployees">0</h3>
                            </div>
                            <div class="icon" >
                               <i class="fa fa-user-slash"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Applications & Websites -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="table-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Top Application Usage</h4>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-period="today" data-target="applications">Today</button>
                            <button class="filter-btn" data-period="yesterday" data-target="applications">Yesterday</button>
                            <button class="filter-btn" data-period="week" data-target="applications">This Week</button>
                        </div>
                        <div id="topApplications">
                            <div class="loader"><div class="spinner"></div></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="table-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Top Website Usage</h4>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-period="today" data-target="websites">Today</button>
                            <button class="filter-btn" data-period="yesterday" data-target="websites">Yesterday</button>
                            <button class="filter-btn" data-period="week" data-target="websites">This Week</button>
                        </div>
                        <div id="topWebsites">
                            <div class="loader"><div class="spinner"></div></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Employee Active Hours -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="table-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Most Active Employees</h4>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-period="today" data-target="active">Today</button>
                            <button class="filter-btn" data-period="yesterday" data-target="active">Yesterday</button>
                            <button class="filter-btn" data-period="week" data-target="active">This Week</button>
                        </div>
                        <div id="mostActiveEmployees">
                            <div class="loader"><div class="spinner"></div></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="table-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Least Active Employees</h4>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-period="today" data-target="active-least">Today</button>
                            <button class="filter-btn" data-period="yesterday" data-target="active-least">Yesterday</button>
                            <button class="filter-btn" data-period="week" data-target="active-least">This Week</button>
                        </div>
                        <div id="leastActiveEmployees">
                            <div class="loader"><div class="spinner"></div></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Employee Productive Hours -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="table-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Most Productive Employees</h4>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-period="today" data-target="productive">Today</button>
                            <button class="filter-btn" data-period="yesterday" data-target="productive">Yesterday</button>
                            <button class="filter-btn" data-period="week" data-target="productive">This Week</button>
                        </div>
                        <div id="mostProductiveEmployees">
                            <div class="loader"><div class="spinner"></div></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="table-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Least Productive Employees</h4>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-period="today" data-target="productive-least">Today</button>
                            <button class="filter-btn" data-period="yesterday" data-target="productive-least">Yesterday</button>
                            <button class="filter-btn" data-period="week" data-target="productive-least">This Week</button>
                        </div>
                        <div id="leastProductiveEmployees">
                            <div class="loader"><div class="spinner"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('post-load-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
@endsection

@section('page-scripts')
    <script>
        let currentPeriod = {
            applications: 'today',
            websites: 'today',
            active: 'today',
            'active-least': 'today',
            productive: 'today',
            'productive-least': 'today'
        };
        
        $(document).ready(function() {
            // Load initial data
            loadEmployeeStats();
            loadTopApplications('today');
            loadTopWebsites('today');
            loadActiveEmployees('today');
            loadProductiveEmployees('today');
            
            // Filter button click handlers
            $('.filter-btn').on('click', function() {
                const period = $(this).data('period');
                const target = $(this).data('target');
                
                // Update active state
                $(this).siblings('.filter-btn').removeClass('active');
                $(this).addClass('active');
                
                // Update current period
                currentPeriod[target] = period;
                
                // Load data based on target
                if (target === 'applications') {
                    loadTopApplications(period);
                } else if (target === 'websites') {
                    loadTopWebsites(period);
                } else if (target === 'active' || target === 'active-least') {
                    loadActiveEmployees(period);
                } else if (target === 'productive' || target === 'productive-least') {
                    loadProductiveEmployees(period);
                }
            });
        });
        
        function loadEmployeeStats() {
            $.ajax({
                url: '/admin/dashboard/employee-stats',
                type: 'GET',
                success: function(response) {
                    if (response.code === 200) {
                        $('#totalEmployees').text(response.data.totalEmployees);
                        $('#presentEmployees').text(response.data.presentEmployees);
                        $('#absentEmployees').text(response.data.absentEmployees);
                    }
                },
                error: function(error) {
                    console.error('Error loading employee stats:', error);
                }
            });
        }
        
        function loadTopApplications(period) {
            $('#topApplications').html('<div class="loader"><div class="spinner"></div></div>');
            
            $.ajax({
                url: '/admin/dashboard/top-applications',
                type: 'GET',
                data: { period: period, limit: 10 },
                success: function(response) {
                    if (response.code === 200 && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(function(app) {
                            const time = formatTime(app.hours, app.minutes, app.seconds);
                            html += `
                                <div class="usage-item">
                                    <div class="usage-name" title="${app.name}">${app.name || 'Unknown'}</div>
                                    <div class="usage-time">${time}</div>
                                </div>
                            `;
                        });
                        $('#topApplications').html(html);
                    } else {
                        $('#topApplications').html('<div class="no-data">No data available</div>');
                    }
                },
                error: function(error) {
                    console.error('Error loading top applications:', error);
                    $('#topApplications').html('<div class="no-data">Error loading data</div>');
                }
            });
        }
        
        function loadTopWebsites(period) {
            $('#topWebsites').html('<div class="loader"><div class="spinner"></div></div>');
            
            $.ajax({
                url: '/admin/dashboard/top-websites',
                type: 'GET',
                data: { period: period, limit: 10 },
                success: function(response) {
                    if (response.code === 200 && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(function(website) {
                            const time = formatTime(website.hours, website.minutes, website.seconds);
                            html += `
                                <div class="usage-item">
                                    <div class="usage-name" title="${website.name}">${website.name || 'Unknown'}</div>
                                    <div class="usage-time">${time}</div>
                                </div>
                            `;
                        });
                        $('#topWebsites').html(html);
                    } else {
                        $('#topWebsites').html('<div class="no-data">No data available</div>');
                    }
                },
                error: function(error) {
                    console.error('Error loading top websites:', error);
                    $('#topWebsites').html('<div class="no-data">Error loading data</div>');
                }
            });
        }
        
        function loadActiveEmployees(period) {
            $('#mostActiveEmployees').html('<div class="loader"><div class="spinner"></div></div>');
            $('#leastActiveEmployees').html('<div class="loader"><div class="spinner"></div></div>');
            
            $.ajax({
                url: '/admin/dashboard/employees-active-hours',
                type: 'GET',
                data: { period: period, limit: 5 },
                success: function(response) {
                    if (response.code === 200) {
                        // Most Active
                        if (response.data.mostActive && response.data.mostActive.length > 0) {
                            let html = '';
                            response.data.mostActive.forEach(function(employee) {
                                const time = formatTime(employee.hours, employee.minutes, employee.seconds);
                                html += `
                                    <div class="employee-item">
                                        <div class="employee-info">
                                            <div class="employee-name">${employee.firstName} ${employee.lastName}</div>
                                            <div class="employee-email">${employee.email}</div>
                                        </div>
                                        <div class="employee-time">${time}</div>
                                    </div>
                                `;
                            });
                            $('#mostActiveEmployees').html(html);
                        } else {
                            $('#mostActiveEmployees').html('<div class="no-data">No data available</div>');
                        }
                        
                        // Least Active
                        if (response.data.leastActive && response.data.leastActive.length > 0) {
                            let html = '';
                            response.data.leastActive.forEach(function(employee) {
                                const time = formatTime(employee.hours, employee.minutes, employee.seconds);
                                html += `
                                    <div class="employee-item">
                                        <div class="employee-info">
                                            <div class="employee-name">${employee.firstName} ${employee.lastName}</div>
                                            <div class="employee-email">${employee.email}</div>
                                        </div>
                                        <div class="employee-time">${time}</div>
                                    </div>
                                `;
                            });
                            $('#leastActiveEmployees').html(html);
                        } else {
                            $('#leastActiveEmployees').html('<div class="no-data">No data available</div>');
                        }
                    }
                },
                error: function(error) {
                    console.error('Error loading active employees:', error);
                    $('#mostActiveEmployees').html('<div class="no-data">Error loading data</div>');
                    $('#leastActiveEmployees').html('<div class="no-data">Error loading data</div>');
                }
            });
        }
        
        function loadProductiveEmployees(period) {
            $('#mostProductiveEmployees').html('<div class="loader"><div class="spinner"></div></div>');
            $('#leastProductiveEmployees').html('<div class="loader"><div class="spinner"></div></div>');
            
            $.ajax({
                url: '/admin/dashboard/employees-productive-hours',
                type: 'GET',
                data: { period: period, limit: 5 },
                success: function(response) {
                    if (response.code === 200) {
                        // Most Productive
                        if (response.data.mostProductive && response.data.mostProductive.length > 0) {
                            let html = '';
                            response.data.mostProductive.forEach(function(employee) {
                                const time = formatTime(employee.hours, employee.minutes, employee.seconds);
                                html += `
                                    <div class="employee-item">
                                        <div class="employee-info">
                                            <div class="employee-name">${employee.firstName} ${employee.lastName}</div>
                                            <div class="employee-email">${employee.email}</div>
                                        </div>
                                        <div class="employee-time">${time}</div>
                                    </div>
                                `;
                            });
                            $('#mostProductiveEmployees').html(html);
                        } else {
                            $('#mostProductiveEmployees').html('<div class="no-data">No data available</div>');
                        }
                        
                        // Least Productive
                        if (response.data.leastProductive && response.data.leastProductive.length > 0) {
                            let html = '';
                            response.data.leastProductive.forEach(function(employee) {
                                const time = formatTime(employee.hours, employee.minutes, employee.seconds);
                                html += `
                                    <div class="employee-item">
                                        <div class="employee-info">
                                            <div class="employee-name">${employee.firstName} ${employee.lastName}</div>
                                            <div class="employee-email">${employee.email}</div>
                                        </div>
                                        <div class="employee-time">${time}</div>
                                    </div>
                                `;
                            });
                            $('#leastProductiveEmployees').html(html);
                        } else {
                            $('#leastProductiveEmployees').html('<div class="no-data">No data available</div>');
                        }
                    }
                },
                error: function(error) {
                    console.error('Error loading productive employees:', error);
                    $('#mostProductiveEmployees').html('<div class="no-data">Error loading data</div>');
                    $('#leastProductiveEmployees').html('<div class="no-data">Error loading data</div>');
                }
            });
        }
        
        function formatTime(hours, minutes, seconds) {
            hours = hours || 0;
            minutes = minutes || 0;
            seconds = seconds || 0;
            
            const h = String(hours).padStart(2, '0');
            const m = String(minutes).padStart(2, '0');
            const s = String(seconds).padStart(2, '0');
            
            return `${h}:${m}:${s}`;
        }
    </script>
@endsection

