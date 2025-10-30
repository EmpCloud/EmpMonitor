@extends('User::Layout._layout')

@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
            {{env('WEBSITE_TITLE')}} | @endif {{ __('messages.employee') }} Real Time
    </title>
@endsection

@section('extra-style-links')
@endsection

@section('page-style')
    <style>
        #cover-spin {
            position: fixed;
            width: 100%;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: none;
        }

        @-webkit-keyframes spin {
            from {
                -webkit-transform: rotate(0deg);
            }

            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #cover-spin::after {
            content: '';
            display: block;
            position: absolute;
            left: 48%;
            top: 26%;
            width: 40px;
            height: 40px;
            border-style: dotted;
            border-color: #057dca;
            border-top-color: transparent;
            border-width: 6px;
            border-radius: 50%;
            -webkit-animation: spin .8s linear infinite;
            animation: spin .8s linear infinite;
        }

        #PdfText {
            position: absolute;
            left: 33%;
            top: 36%;
            width: 450px;
            height: 40px;
        }

        .generateCSV {
            cursor: pointer;
        }

        .generating {
            cursor: default;
        }

        .tooltip-inner {
            max-width: 100% !important;
            text-align: left;
        }

        /* Basic card styles */
        .card-container {
            display: flex;
            justify-content: center;
            margin: 0 9px;
            flex-wrap: wrap;
        }

        .card {
            background-color: #ffffff;
            border: 1.7px solid #C9C9C9; /* Light gray */
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            width: 350px;
            padding: 20px;
            font-family: 'Montserrat', sans-serif !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: -10px;
            padding-bottom: 10px;
        }

        .card-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #1f2937; /* Dark gray */
        }

        .card-body p {
            margin: 10px 0;
            font-size: 16px;
            color: #4b5563; /* Medium gray */
            align-items: center;
        }

        .card-body strong {
            color: #111827; /* Darker gray for labels */
        }

        .row {
            margin: 0;
            display: flex;
            gap: 20px;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .status-dot {
            height: 15px;
            width: 15px;
            border-radius: 50%;
            display: inline-block;
        }
        .online {
            background-color: #4CAF50;
        }
        .offline {
            background-color: #F44336;
            margin-inline: 10px;
        }
        .main_content_container{
            padding: 15px 0;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 0 6.2px 0 #00000026;

        }
        .main_content_container_heading{
            font-size: 16px;
            color: #000000;
            padding-left: 15px;
        }
        .card-header:first-child{
            border-radius: 6px 6px 0 0;
            background: linear-gradient(to right, #9181FF, #4CB5FE)
        }
        .card-header:first-child h2{
            font-size: 20px;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            font-family: 'Montserrat';

        }
        .no-page-title .card{
            border-radius: 17.5px;
            border:1.5px solid #C9C9C9;
            overflow: hidden;
        }
        .card-body p {
            word-break: break-word;
            display: grid;
            grid-template-columns: 0.6fr 1fr;
            font-size: 16px;
            font-weight: 500;
        }
        .card-body strong {
            color: #1A1A1A;
            font-size: 16px;
            font-weight: 600;
        }
        .offline{
            border:1.5px solid #ffffff;
        }
        .online {
            background-color: #39C90D;
            border: 1.5px solid #ffffff;
            margin-inline: 10px;
        }

        .search_main_container{
            padding: 15px 0;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 0 6.2px 0 #00000026;
            margin-bottom:20px;
            gap: 80px;

        }
        .search_container {
            display: flex;
            align-items: center;
            border: 2px solid #C9C9C9;
            border-radius: 5px;
            padding: 0px;
            background: #fff;
            width: 100%;
            max-width: 400px;
            margin: 0 auto; /* Centers the container */
            margin-left:20px;
        }

        .search_container input {
            border: none;
            outline: none;
            padding: 8px;
            font-size: 10px;
            width: 150%;
        }

        .search_container button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
        }

        .search_container button i {
            font-size: 15px;
            color: #575757;
        }
        .search_main_container{
            flex-wrap: nowrap;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .search_container {
                max-width: 90%;
                padding: 6px;
            }

            .search_container input {
                font-size: 12px;
                padding: 6px;
            }

            .search_container button {
                padding: 6px;
            }

            .search_container button i {
                font-size: 16px;
            }
        }
        .emp_Search_header{
            margin-left: 20px;
            font-size: 14px;
            font-weight: 500;

        }

        /*range css*/
        .dual-slider-container {
            position: relative;
            width: 320px;
            top: 30px;
        }

        .dual-slider-track {
            position: absolute;
            width: 100%;
            height: 8px;
            background: #fff;
            border-radius: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: 1px solid #aaa;
        }

        .dual-slider-track-active {
            position: absolute;
            height: 8px;
            background: linear-gradient( 45deg , #9181FF , #4CB5FE);
            border-radius: 8px;
            top: 50%;
            transform: translateY(-50%);
        }

        .dual-slider {
            position: absolute;
            width: 100%;
            pointer-events: none;
        }

        .dual-slider input {
            position: absolute;
            width: 100%;
            -webkit-appearance: none;
            background: none;
            pointer-events: auto;
        }

        .dual-slider input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: linear-gradient( 45deg , #9181FF , #4CB5FE);
            border: 3px solid white;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            cursor: pointer;
            position: relative;
            z-index: 2;
            top: -11px;
        }

        .dual-slider input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: linear-gradient( 45deg , #9181FF , #4CB5FE);
            border: 3px solid white;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            cursor: pointer;
            position: relative;
            top: -10px;
            z-index: 2;
        }

        .dual-slider-tooltip {
            position: absolute;
            background: linear-gradient( 45deg , #9181FF , #4CB5FE);
            color: #fff;
            padding: 0px 8px;
            font-size: 12px;
            border-radius: 4px;
            top: -32px;
            white-space: nowrap;
            transform: translateX(-50%);
        }
        .ER_time{
            font-size: 18px;
            font-weight: 500;
            color: #000000;
        }
        .emel{
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    <!-- Page Inner -->
    <div class="page-inner no-page-title">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item"><a href="employee-details" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page">
                            Employee's Real Time Insights
                        </li>
                    </ol>
                </nav>
            </div>
            <p id="access_token" class="d-none"><?php echo Session::get((new App\Modules\User\helper)->getHostName())['token']['data'] ?></p>
            <p class="ER_time">Employee’s Real Time Insights</p>
            <div class="row search_main_container">
                <div>
                    <h2 class="emp_Search_header">Search</h2>
                    <div class="search_container">
                        <input type="text" placeholder="Search Employee" id="search" autocomplete="off">
                        <button>
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <h2 class="emp_Search_header">Productivity tracker</h2>
                    <div class="dual-slider-container">
                        <div class="dual-slider-track"></div>
                        <div class="dual-slider-track-active"></div>

                        <div class="dual-slider">
                            <input type="range" id="dual-min-range" min="0" max="100" value="0">
                            <input type="range" id="dual-max-range" min="0" max="100" value="100">
                        </div>

                        <div class="dual-slider-tooltip" id="dual-min-tooltip">20</div>
                        <div class="dual-slider-tooltip" id="dual-max-tooltip">80</div>
                    </div>
                </div>



            </div>
            <div class="row main_content_container">
                <h2 class="main_content_container_heading">Employee’s Insights</h2>
                <div class="col-md-12" style="padding-inline:45px">
                    <div class="">
                        <div class="">
                            <div class="row">
                                @if($employeesList['code'] == 200)
                                    @if(!empty($employeesList['data']))
                                        <script>
                                            var employee_details = <?php echo json_encode($employeesList['data'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
                                        </script>
                                        @foreach($employeesList['data'] as $empl)
                                            @php
                                                $prod = null;
                                                if (!empty($employeesProductivity['data']['user_data'])) {
                                                    foreach($employeesProductivity['data']['user_data'] as $productivity) {
                                                        if($empl['id'] == $productivity['employee_id']) {
                                                            $prod = $productivity['productivity'] / 100;
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <div class="card-container" id="{{ $empl['u_id'] }}">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h2 title="{{ $empl['first_name'] . ' ' . $empl['last_name'] }}">{{ getSubstring($empl['first_name'] . ' ' . $empl['last_name']) }} <span id="{{ $empl['u_id'] }}_status" class="status-dot"></span></h2>
                                                        <a id="{{ $empl['u_id'] }}_location" target="_blank" href="">
                                                            <i class="fa fa-map-marker" style="font-size:15px;display: none"></i>
                                                        </a>
                                                    </div>
                                                    <div class="card-body">
                                                        <p id="{{ $empl['id'] }}_productivity" style="font-weight: 700; font-size: 20px; color: #565656"><strong>Productivity:</strong> {{ $prod !== null ? number_format($prod * 100, 2) . '%' : '0%' }}</p>
                                                        <p><strong>Email:</strong><span class="emel" title="{{ $empl['email'] }}">{{ getSubstring($empl['email']) ?? '-' }}</span> </p>
                                                        <div class="d-none" id="{{ $empl['u_id'] }}_details">
                                                            <p><strong>Title: </strong> - </p>
                                                            <p style="text-transform: capitalize;"><strong>Application: </strong> - </p>
                                                            <p><strong>URL: </strong> - </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p>No employee data available.</p>
                                    @endif
                                @else
                                    <p>Error fetching employees list.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Inner -->
@endsection
@php
    function getSubstring($sentence, $charCount = 20) {
        if (!is_string($sentence) || !is_int($charCount)) {
            return $sentence;
        }
        if ($charCount < 0) {
            return $sentence;
        }
        if (strlen($sentence) > $charCount) {
            return substr($sentence, 0, $charCount) . "...";
        }
        return $sentence;
    }
@endphp

@section('post-load-scripts')
    <script src="../assets/plugins/select2/js/select2.min.js"></script>
    <script src="../assets/plugins/daterangepicker/moment.min.js"></script>
    <script src="../assets/plugins/daterangepicker/moment-timezone-with-data.js"></script>
    <script>
        const minRange = document.getElementById("dual-min-range");
        const maxRange = document.getElementById("dual-max-range");
        const minTooltip = document.getElementById("dual-min-tooltip");
        const maxTooltip = document.getElementById("dual-max-tooltip");
        const sliderTrackActive = document.querySelector(".dual-slider-track-active");

        function updateSlider() {
            let minValue = parseInt(minRange.value);
            let maxValue = parseInt(maxRange.value);

            if (maxValue - minValue < 0) {
                minRange.value = maxValue - 0;
                minValue = maxValue - 0;
            }

            if (maxValue - minValue < 0) {
                maxRange.value = minValue + 0;
                maxValue = minValue + 0;
            }

            let minPercent = (minValue / minRange.max) * 100;
            let maxPercent = (maxValue / maxRange.max) * 100;

            sliderTrackActive.style.left = (minPercent<0?0:minPercent) + "%";
            sliderTrackActive.style.width = (maxPercent - minPercent) + "%";

            minTooltip.style.left = (minPercent<0?0:minPercent) + "%";
            minTooltip.innerHTML = (minValue < 0 ? 0 : minValue);
            maxTooltip.style.left = maxPercent + "%";
            maxTooltip.innerHTML = (maxValue < 0 ? 0 : maxValue );
        }

        minRange.addEventListener("input", updateSlider);
        maxRange.addEventListener("input", updateSlider);

        updateSlider();

    </script>
@endsection

@section('page-scripts')
    <script src="../assets/js/incJSFile/real_time_notify.js"></script>
@endsection
