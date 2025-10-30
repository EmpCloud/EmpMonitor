@extends('User::Layout._layout')

@section('title')
<title>@if((new App\Modules\User\helper)->checkHost() )
    @if((new App\Modules\User\helper)->checkHost() )
    {{env('WEBSITE_TITLE')}} | @endif @endif {{ __('messages.report') }}
</title>
@endsection

@section('extra-style-links')
<link rel="stylesheet" type="text/css" href="../assets/plugins/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" />
<style>
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate {
        margin-top: 15px;
    }
    .export-buttons {
        margin-bottom: 15px;
    }
    .export-buttons .btn {
        margin-right: 10px;
    }
    .logo-container {
        text-align: center;
        margin-bottom: 20px;
    }
    .logo-container img {
        max-height: 60px;
        max-width: 200px;
    }
</style>
@endsection

@section('page-style')
    @include('User::Layout._modernStyles')
    <style>
        /* Page Specific Styles Only */
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
    
    /* Info Icon */
    .toDateLimitInfo {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .toDateLimitInfo:hover {
        transform: scale(1.2);
    }
    
    /* Float Right Buttons */
    .float-right .btn {
        margin-left: 0.5rem;
    }
    
    /* Smooth Transitions */
    * {
        transition: background-color 0.2s ease, border-color 0.2s ease;
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

            <div class="float-right">
                <button
                    id="exportPDF"
                    type="button"
                    class="btn btn-danger">
                    <i class="fa fa-file-pdf-o"></i> Export PDF
                </button>
                <button
                    id="exportCSV"
                    type="button"
                    class="btn btn-success">
                    <i class="fa fa-file-excel-o"></i> {{ __('messages.exportExcel') }}
                </button>
            </div>
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
                                        <option value="">Select Location</option>
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
                                <label class="font-weight-bold" for="department_option">
                                    {{ __('messages.department') }}</label>
                                <div class="form-group">
                                    <select class="form-control mb-2" id="departmentID">
                                        <option value="">Select Department</option>
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
                                <label class="font-weight-bold" for="employee_option">
                                    {{ __('messages.employee') }}</label>
                                <div class="form-group">
                                    <select class="form-control mb-2" id="employeeID">
                                       <option selected class="active-result" value="">
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
                                <label style="font-weight: 700;" for="date_range">
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
                        <div class="table-responsive">
                            <table
                                id="reportTable"
                                class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.fullName') }}</th>
                                        <th>{{ __('messages.email') }}</th>
                                        <th>{{ __('messages.Location') }}</th>
                                        <th>{{ __('messages.department') }}</th>
                                        <th>{{ __('messages.designation') }}</th>
                                        <th>Title</th>
                                        <th>Application Name</th>
                                        <th>URL</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Total Seconds</th>
                                        <th>Idle Seconds</th>
                                        <th>Active Seconds</th>
                                        <th>Keystrokes Count</th>
                                        <th>Mouse Movements Count</th>
                                        <th>Button Clicks</th>
                                        <th>Keystrokes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($reportData['code']) && $reportData['code'] == 200)
                                    @foreach($reportData['data'] as $report)
                                    <tr>
                                        <td>{{ $report['first_name'] }} {{ $report['last_name'] }}</td>
                                        <td>{{ $report['email'] }}</td>
                                        <td>{{ $report['location_name'] }}</td>
                                        <td>{{ $report['department_name'] }}</td>
                                        <td>{{ $report['role'] ?? '--' }}</td>
                                        <td>{{ $report['title'] }}</td>
                                        <td>{{ $report['application_name'] }}</td>
                                        <td>{{ $report['url'] }}</td>
                                        <td>{{ $report['start_time'] }}</td>
                                        <td>{{ $report['end_time'] }}</td>
                                        <td>{{ $report['total_seconds'] }}</td>
                                        <td>{{ $report['idle_seconds'] }}</td>
                                        <td>{{ $report['active_seconds'] }}</td>
                                        <td>{{ $report['keystrokesCount'] }}</td>
                                        <td>{{ $report['mouseMovementsCount'] }}</td>
                                        <td>{{ $report['buttonClicks'] }}</td>
                                        <td>{{ $report['keystrokes'] }}</td>
                                    </tr>
                                    @endforeach
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

<!-- Hidden table for export -->
<table id="exportTable" style="display: none;">
    <thead>
        <tr>
            <th colspan="17" class="text-center">
                <div class="logo-container">
                    <img src="../assets/images/logos/Icon.png" alt="Company Logo" />
                </div>
                <h3>Employee Activity Report</h3>
                <p>Generated on: <span id="exportDate"></span></p>
            </th>
        </tr>
        <tr>
            <th>{{ __('messages.fullName') }}</th>
            <th>{{ __('messages.email') }}</th>
            <th>{{ __('messages.Location') }}</th>
            <th>{{ __('messages.department') }}</th>
            <th>{{ __('messages.designation') }}</th>
            <th>Title</th>
            <th>Application Name</th>
            <th>URL</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Total Seconds</th>
            <th>Idle Seconds</th>
            <th>Active Seconds</th>
            <th>Keystrokes Count</th>
            <th>Mouse Movements Count</th>
            <th>Button Clicks</th>
            <th>Keystrokes</th>
        </tr>
    </thead>
    <tbody id="exportTableBody">
    </tbody>
</table>

@endsection

@section('post-load-scripts')
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="../assets/plugins/daterangepicker/moment.min.js"></script>
<script src="../assets/plugins/daterangepicker/moment-timezone-with-data.js"></script>
<script src="../assets/plugins/daterangepicker/daterangepicker.js"></script>
<script src="//unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.2.11/jspdf.plugin.autotable.min.js"></script>
@endsection

@section('page-scripts')
<script src="../assets/js/final-timezone.js"></script>
<script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js"></script>

<script>
let reportTable;
let currentData = [];

$(function() {
    const start = moment().subtract(29, 'days');
    const end = moment();

    function cb(start, end) {
        $('#reportranges span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
        $('#from').val(start.format('YYYY-MM-DD'));
        $('#to').val(end.format('YYYY-MM-DD'));
        loadReportData();
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

    cb(start, end);
    
    // Initialize DataTable
    initializeDataTable();
    
    // Load initial data
    loadReportData();
});

function initializeDataTable() {
    if ($('#reportTable').length) {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#reportTable')) {
            $('#reportTable').DataTable().destroy();
        }
        
        try {
            reportTable = $('#reportTable').DataTable({
                processing: true,
                serverSide: false,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                responsive: true,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                columnDefs: [
                    {
                        targets: [6, 7], // URL and Application Name columns
                        render: function(data, type, row) {
                            if (type === 'display' && data && data.length > 30) {
                                return '<span title="' + data + '">' + data.substring(0, 30) + '...</span>';
                            }
                            return data || '';
                        }
                    }
                ]
            });
        } catch (error) {
            console.error('Error initializing DataTable:', error);
        }
    }
}

function loadReportData() {
    const locationId = $('#locationID').val();
    const departmentId = $('#departmentID').val();
    const employeeId = $('#employeeID').val();
    const fromDate = $('#from').val();
    const toDate = $('#to').val();

    // Fallback for userType if not defined
    const currentUserType = typeof userType !== 'undefined' ? userType : 'admin';

    $.ajax({
        url: "/" + currentUserType + '/get-report-data',
        type: 'POST',
        data: {
            location_id: locationId,
            department_id: departmentId,
            employee_id: employeeId,
            from_date: fromDate,
            to_date: toDate
        },
        beforeSend: function() {
            if (reportTable) {
                reportTable.clear().draw();
            }
        },
        success: function(response) {
            if (response.code === 200 && response.data) {
                currentData = response.data;
                updateTableData(response.data);
            } else {
                currentData = [];
                updateTableData([]);
                console.error('Error loading report data:', response.message);
            }
        },
        error: function(xhr, status, error) {
            currentData = [];
            updateTableData([]);
            console.error('Ajax error:', error);
        }
    });
}

function updateTableData(data) {
    if (reportTable) {
        reportTable.clear();
        
        if (data && data.length > 0) {
            data.forEach(function(item) {
                reportTable.row.add([
                    item.first_name + ' ' + item.last_name,
                    item.email || '',
                    item.location_name || '',
                    item.department_name || '',
                    item.role || '--',
                    item.title || '',
                    item.application_name || '',
                    item.url || '',
                    item.start_time || '',
                    item.end_time || '',
                    item.total_seconds || '',
                    item.idle_seconds || '',
                    item.active_seconds || '',
                    item.keystrokesCount || '',
                    item.mouseMovementsCount || '',
                    item.buttonClicks || '',
                    item.keystrokes || ''
                ]);
            });
        }
        
        reportTable.draw();
    }
}

// Export to PDF
$('#exportPDF').on('click', function() {
    if (!currentData || currentData.length === 0) {
        errorSwal('No data available to export.');
        return;
    }
    
    try {
        exportToPDF();
    } catch (error) {
        console.error('PDF export error:', error);
        errorSwal('Error generating PDF. Please try again.');
    }
});

// Export to Excel
$('#exportCSV').on('click', function() {
    if (!currentData || currentData.length === 0) {
        errorSwal('No data available to export.');
        return;
    }
    
    try {
        exportToExcel();
    } catch (error) {
        console.error('Excel export error:', error);
        errorSwal('Error generating Excel file. Please try again.');
    }
});

function exportToPDF() {
    if (!currentData || currentData.length === 0) {
        errorSwal('No data available to export.');
        return;
    }

    const doc = new jsPDF('l', 'pt', 'a4');
    
    // Add logo to first page
    const logoImg = new Image();
    logoImg.src = '../assets/images/logos/Icon.png';
    
    logoImg.onload = function() {
        try {
            // Add logo (smaller and positioned better)
            doc.addImage(logoImg, 'PNG', 20, 15, 40, 20);
            
            // Add title
            doc.setFontSize(16);
            doc.text('Employee Activity Report', 80, 30);
            
            // Add date
            doc.setFontSize(10);
            doc.text('Generated on: ' + moment().format('MMMM D, YYYY HH:mm:ss'), 80, 45);
            
            // Prepare table data
            const tableData = currentData.map(function(item) {
                return [
                    item.first_name + ' ' + item.last_name,
                    item.email || '',
                    item.location_name || '',
                    item.department_name || '',
                    item.role || '--',
                    item.title || '',
                    item.application_name || '',
                    item.url || '',
                    item.start_time || '',
                    item.end_time || '',
                    item.total_seconds || '',
                    item.idle_seconds || '',
                    item.active_seconds || '',
                    item.keystrokesCount || '',
                    item.mouseMovementsCount || '',
                    item.buttonClicks || '',
                    item.keystrokes || ''
                ];
            });
            
            const headers = [
                'Full Name',
                'Email',
                'Location',
                'Department',
                'Designation',
                'Title',
                'Application Name',
                'URL',
                'Start Time',
                'End Time',
                'Total Seconds',
                'Idle Seconds',
                'Active Seconds',
                'Keystrokes Count',
                'Mouse Movements Count',
                'Button Clicks',
                'Keystrokes'
            ];
            
            doc.autoTable({
                head: [headers],
                body: tableData,
                startY: 60,
                theme: 'grid',
                styles: {
                    fontSize: 6,
                    cellPadding: 1,
                    overflow: 'linebreak',
                    halign: 'left'
                },
                headStyles: {
                    fillColor: [41, 128, 185],
                    textColor: 255,
                    fontSize: 7,
                    fontStyle: 'bold',
                    halign: 'center'
                },
                didDrawPage: function(data) {
                    // Add logo to each page (smaller and positioned to avoid covering table)
                    doc.addImage(logoImg, 'PNG', 20, 15, 40, 20);
                    
                    // Add page number at the bottom
                    const pageCount = doc.internal.getNumberOfPages();
                    doc.setFontSize(8);
                    doc.text('Page ' + data.pageNumber + ' of ' + pageCount, doc.internal.pageSize.width - 80, doc.internal.pageSize.height - 15);
                },
                margin: {
                    top: 60,
                    right: 5,
                    bottom: 30,
                    left: 5
                },
                tableWidth: 'auto'
            });
            
            doc.save('Employee_Activity_Report_' + moment().format('YYYY-MM-DD') + '.pdf');
        } catch (error) {
            console.error('Error in PDF generation:', error);
            errorSwal('Error generating PDF. Please try again.');
        }
    };
    
    // Handle logo loading error
    logoImg.onerror = function() {
        // If logo fails to load, continue without it
        exportToPDFWithoutLogo();
    };
}

function exportToPDFWithoutLogo() {
    const doc = new jsPDF('l', 'pt', 'a4');
    
    // Add title
    doc.setFontSize(16);
    doc.text('Employee Activity Report', 80, 30);
    
    // Add date
    doc.setFontSize(10);
    doc.text('Generated on: ' + moment().format('MMMM D, YYYY HH:mm:ss'), 80, 45);
    
    // Prepare table data
    const tableData = currentData.map(function(item) {
        return [
            item.first_name + ' ' + item.last_name,
            item.email || '',
            item.location_name || '',
            item.department_name || '',
            item.role || '--',
            item.title || '',
            item.application_name || '',
            item.url || '',
            item.start_time || '',
            item.end_time || '',
            item.total_seconds || '',
            item.idle_seconds || '',
            item.active_seconds || '',
            item.keystrokesCount || '',
            item.mouseMovementsCount || '',
            item.buttonClicks || '',
            item.keystrokes || ''
        ];
    });
    
    const headers = [
        'Full Name',
        'Email',
        'Location',
        'Department',
        'Designation',
        'Title',
        'Application Name',
        'URL',
        'Start Time',
        'End Time',
        'Total Seconds',
        'Idle Seconds',
        'Active Seconds',
        'Keystrokes Count',
        'Mouse Movements Count',
        'Button Clicks',
        'Keystrokes'
    ];
    
    doc.autoTable({
        head: [headers],
        body: tableData,
        startY: 60,
        theme: 'grid',
        styles: {
            fontSize: 6,
            cellPadding: 1,
            overflow: 'linebreak',
            halign: 'left'
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontSize: 7,
            fontStyle: 'bold',
            halign: 'center'
        },
        didDrawPage: function(data) {
            // Add page number at the bottom
            const pageCount = doc.internal.getNumberOfPages();
            doc.setFontSize(8);
            doc.text('Page ' + data.pageNumber + ' of ' + pageCount, doc.internal.pageSize.width - 80, doc.internal.pageSize.height - 15);
        },
        margin: {
            top: 60,
            right: 5,
            bottom: 30,
            left: 5
        },
        tableWidth: 'auto'
    });
    
    doc.save('Employee_Activity_Report_' + moment().format('YYYY-MM-DD') + '.pdf');
}

function exportToExcel() {
    if (!currentData || currentData.length === 0) {
        errorSwal('No data available to export.');
        return;
    }
    
    try {
        // Prepare headers
        const headers = [
            'Full Name',
            'Email',
            'Location',
            'Department',
            'Designation',
            'Title',
            'Application Name',
            'URL',
            'Start Time',
            'End Time',
            'Total Seconds',
            'Idle Seconds',
            'Active Seconds',
            'Keystrokes Count',
            'Mouse Movements Count',
            'Button Clicks',
            'Keystrokes'
        ];
        
        // Prepare data for Excel
        const excelData = [
            headers, // First row is headers
            ...currentData.map(function(item) {
                return [
                    item.first_name + ' ' + item.last_name,
                    item.email || '',
                    item.location_name || '',
                    item.department_name || '',
                    item.role || '--',
                    item.title || '',
                    item.application_name || '',
                    item.url || '',
                    item.start_time || '',
                    item.end_time || '',
                    item.total_seconds || '',
                    item.idle_seconds || '',
                    item.active_seconds || '',
                    item.keystrokesCount || '',
                    item.mouseMovementsCount || '',
                    item.buttonClicks || '',
                    item.keystrokes || ''
                ];
            })
        ];
        
        // Create workbook and worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(excelData);
        
        // Set column widths for better readability
        const colWidths = [
            { wch: 20 }, // Full Name
            { wch: 25 }, // Email
            { wch: 15 }, // Location
            { wch: 15 }, // Department
            { wch: 12 }, // Designation
            { wch: 20 }, // Title
            { wch: 20 }, // Application Name
            { wch: 30 }, // URL
            { wch: 15 }, // Start Time
            { wch: 15 }, // End Time
            { wch: 12 }, // Total Seconds
            { wch: 12 }, // Idle Seconds
            { wch: 12 }, // Active Seconds
            { wch: 15 }, // Keystrokes Count
            { wch: 18 }, // Mouse Movements Count
            { wch: 12 }, // Button Clicks
            { wch: 15 }  // Keystrokes
        ];
        ws['!cols'] = colWidths;
        
        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Employee Activity Report');
        
        // Generate Excel file and download
        const fileName = 'Employee_Activity_Report_' + moment().format('YYYY-MM-DD') + '.xlsx';
        XLSX.writeFile(wb, fileName);
        
    } catch (error) {
        console.error('Error in Excel generation:', error);
        errorSwal('Error generating Excel file. Please try again.');
    }
}

// Filter change handlers
$('#locationID, #departmentID, #employeeID').on('change', function() {
    loadReportData();
});

// Initialize filters
$(document).ready(function() {
    // Set initial values for date range
    const start = moment().subtract(29, 'days');
    const end = moment();
    $('#from').val(start.format('YYYY-MM-DD'));
    $('#to').val(end.format('YYYY-MM-DD'));
});
</script>
@endsection