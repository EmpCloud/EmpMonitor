@extends('User::Layout._layout')

@section('title')
<title>@if((new App\Modules\User\helper)->checkHost() )
    @if((new App\Modules\User\helper)->checkHost() )
    {{env('WEBSITE_TITLE')}} | @endif @endif Manage Department
</title>
@endsection
@section('extra-style-links')
<link rel="stylesheet" type="text/css" href="../assets/plugins/daterangepicker/daterangepicker.css" />

@endsection

@section('page-style')
<style>
    #addLocationModal {
        z-index: 9999999 !important;
    }

    #deleteDepartmentsModal {
        z-index: 9999999 !important;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-search__field {
        width: 140% !important;
    }

    .chosen-container-multi .chosen-drop .result-selected {
        display: list-item;
        color: #ccc;
        cursor: default;
    }

    .select2-results__option[aria-selected=true] {
        display: none;
    }

    @media (max-width: 767px) {
        #dropdownMenuLink {
            padding: 2px 10px;
        }
    }

    /*.alert {*/
    /*    padding: 10px;*/
    /*    background-color: #2196F3;*/
    /*    color: white;*/
    /*}*/

    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .closebtn:hover {
        color: black;
    }

    .modal-open[style] {
        padding-right: 0px !important;
    }

    .dept_new {
        border: 1px solid #479fff5c;
        padding: 8px;
        border-radius: 3px;
        display: inline-flex;
    }
</style>
@endsection



@section('content')
<div class="page-inner no-page-title" style="padding-right: 15px;">
    <div id="main-wrapper">

        <div class="content-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style-1">
                    <li class="breadcrumb-item"><a href="dashboard" style="color: #0686d8;font-weight: 500;">
                            {{ __('messages.home') }}</a></li>
                    <li class="breadcrumb-item " aria-current="page">
                        {{ __('messages.report') }}
                    </li>
                </ol>
            </nav>

            <h1 class="page-title">{{ __('messages.report') }}</h1>

            <button
                id="side-step2"
                type="button"
                class="btn btn-primary float-right"
                data-toggle="modal"
                data-target="#addDepartmentModal">
                {{ __('messages.reportDownload') }}
            </button>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm">
                                <label class="font-weight-bold" for="location_option">
                                    {{ __('messages.location') }}</label>
                                <div class="form-group">
                                    <select class="form-control mb-2" id="locationID">
                                        <option>Select Location</option>
                                        @if(isset($location_departmnet['code']) && $location_departmnet['code'] == 200)
                                        @if(!empty($location_departmnet['data']))
                                        @foreach($location_departmnet['data'] as $location)
                                        <option value="{{ $location['id'] }}"> {{ $location['location_name'] }} </option>
                                        @endforeach
                                        @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm">
                                <label class="font-weight-bold" for="location_option">
                                    {{ __('messages.department') }}</label>
                                <div class="form-group">
                                    <select class="form-control mb-2" id="locationID">
                                        <option>Select Department</option>
                                        @if(isset($departments['code']) && $departments['code'] == 200)
                                        @if(!empty($departments['data']))
                                        @foreach($departments['data'] as $department)
                                        <option value="{{ $department['id'] }}"> {{ $department['name'] }} </option>
                                        @endforeach
                                        @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm">
                                <label class="font-weight-bold" for="location_option">
                                    {{ __('messages.employee') }}</label>
                                <div class="form-group">
                                    <select class="form-control mb-2" id="locationID">
                                       <option selected class="active-result" value="0">
                                                {{ __('messages.all') }}
                                            </option>
                                            @if (isset($employeesList['employees']) && count($employeesList['employees']) > 0)
                                            @foreach ($employeesList['employees'] as $empl)
                                            <option class="active-result" value="{{ $empl['id'] }}">
                                                {{ $empl['first_name'] }} {{ $empl['last_name'] }}
                                            </option>
                                            @endforeach
                                            @else
                                            <option disabled>No employees found.</option>
                                            @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label style="font-weight: 700;" for="location_option">
                                    {{ __('messages.date_ranges') }} :<i
                                        class="fa fa-info-circle text-primary ml-2 toDateLimitInfo"
                                        data-toggle="tooltip" title="{{ __('messages.toDateLimitInfo') }}"></i>
                                </label>
                                <div class="form-control" id="reportranges" style="cursor: pointer;">
                                    <i class="fa fa-calendar"></i>&nbsp; <span class="small"></span>
                                    <i class="fa fa-caret-down"></i>
                                    <input type="hidden" name="to" id="to" value="">
                                    <input type="hidden" name="from" id="from" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="w-100">
                            <table
                                id="locationTable"
                                class="table table-striped table-bordered">
                                <thead>
                                    <th>
                                        <a onclick="sort('Full Name','NameSort')"> {{ __('messages.fullName') }} </a>
                                        <span class="float-right"><i id="NameSort"
                                                class="fas fa-long-arrow-alt-up text-light"></i>
                                        </span>
                                    </th>
                                    <th><label class="d-flex mb-0"><a class="w-100"
                                                onclick="sort('Email','EmailSort')">{{ __('messages.email') }} </a><span
                                                class="float-right"><i id="EmailSort"
                                                    class="fas fa-long-arrow-alt-up text-light"></i>
                                            </span></label></th>
                                    <th><label class="d-flex mb-0"><a class="w-100"
                                                onclick="sort('Location','LocationSort')">{{ __('messages.Location') }}</a><span
                                                class="float-right"><i id="LocationSort"
                                                    class="fas fa-long-arrow-alt-up text-light"></i>
                                            </span></label></th>
                                    <th>
                                        <a onclick="sort('Department','DepartmentSort')">{{ __('messages.department') }}</a><span
                                            class="float-right"><i id="DepartmentSort"
                                                class="fas fa-long-arrow-alt-up text-light"></i>
                                        </span>
                                    </th>
                                    <th>
                                        <a onclick="sort('Role','RoleSort')">{{ __('messages.designation') }}</a><span
                                            class="float-right"><i id="RoleSort"
                                                class="fas fa-long-arrow-alt-up text-light"></i>
                                        </span>
                                    </th>
                                </thead>
                                <tbody id="getLocDept">
                                    @if(isset($reportData['code']) && $reportData['code'] == 200)
                                    @foreach($reportData['data'] as $report)
                                    <tr id="{{ $report['id'] }}">
                                        <td> {{ $report['first_name'] }} {{ $report['last_name'] }}</td>
                                        <td> {{ $report['email'] }}</td>
                                        <td>{{ $report['location_name'] }}</td>
                                        <td id="report{{ $report['id'] }}">
                                            {{ $report['department_name'] }}
                                        </td>
                                        <td>{{ $report['role'] ?? '--' }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr align="center" id="data_not_found">
                                        <td colspan="2">
                                            <p>{{ $location_departmnet['message'] ?? 'No data found.' }}</p>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section ('page-scripts')
<script src="../assets/js/final-timezone.js"></script>
<script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js"></script>
<script src="../assets/plugins/daterangepicker/moment.min.js"></script>
<script src="../assets/plugins/daterangepicker/moment-timezone-with-data.js"></script>
<script src="../assets/plugins/daterangepicker/daterangepicker.js"></script>
<script>
    $(function() {
        const start = moment().subtract(29, 'days');
        const end = moment();

        function cb(start, end) {
            $('#reportranges span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            $('#from').val(start.format('YYYY-MM-DD'));
            $('#to').val(end.format('YYYY-MM-DD'));
        }

        $('#reportranges').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end); // Set initial values
    });

    function addDepartmentFun() {
        console.log($('#locationID').val());
        $.ajax({
            url: "/" + userType + '/add-department',
            type: 'Post',
            data: {
                departmentName: $('#departmentName').val(),
                locationId: $('#locationID').val(),
            },
            beforeSend: function() {
                $('#locError1').html("");
                $('#addDeptId').attr("disabled", true);
            },
            success: function(response) {
                if (response.statusCode === 200 && response.data.code === 200) {
                    successSwal(response.data.message);
                    location.reload();
                } else {
                    errorSwal(response.data.message ?? 'Something went wrong');
                }
            }
        })
    }

    function updateDepartment(id) {}

    function deleteDepartment(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete this department?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/" + userType + '/delete-department',
                    type: 'POST',
                    data: {
                        id,
                    },
                    beforeSend: function() {
                        $('#locError1').html("");
                        $('#addLocId').attr("disabled", true);
                    },
                    success: function(response) {
                        if (response.code === 200) {
                            successSwal(response.message);
                            location.reload();
                        } else {
                            errorSwal(response.message ?? 'Something went wrong');
                        }
                    },
                    error: function() {
                        errorSwal('Failed to delete location. Please try again.');
                    },
                    complete: function() {
                        $('#addLocId').attr("disabled", false);
                    }
                });
            }
        });
    }
</script>
@endsection