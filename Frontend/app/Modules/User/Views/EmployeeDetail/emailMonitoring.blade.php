@extends('User::Layout._layout')

@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           @if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif @endif Email-Monitoring </title>
@endsection

@section('extra-style-links')
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/bootstrap/css/loader.css" rel="stylesheet"/>

    {{--    this is for pagination--}}
    <link href="../assets/css/jqpagination.css" rel="stylesheet">

@endsection

@section('content')

    <div class="page-inner no-page-title">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item active"><a href="employee-details" style="color: #0686d8;font-weight: 500;">
                                Home</a></li>
                        <li class="breadcrumb-item active"><a href="employee-details"
                                                              style="color: #0686d8;font-weight: 500;">
                                Employee</a></li>
                        <li class="breadcrumb-item" aria-current="page">
                            Email Monitering
                        </li>
                    </ol>
                </nav>
                <h1 class="page-title">Email Monitering</h1>
                <div class="col-md-2 btn-group float-right btn-block">
                    <button class="btn btn-success" onclick="saveCSV()" title="{{ __('messages.downloads') }} CSV"><i
                            class="fas fa-file-download mr-2"></i>CSV
                    </button>
                    <button class="btn btn-danger" onclick="savePDF()" title="Download"><i
                            class="fas fa-file-download mr-2"></i>PDF
                    </button>
                </div>
                <div class="form-control col-md-4 float-right" id="reportrange" style="cursor: pointer">
                    <i class="fa fa-calendar"></i>&nbsp; <span></span>
                    <i class="fa fa-caret-down"></i>
                </div>
            </div>


            <div class="row">
                <div class="col-12 text-right"><p class="mb-0" style="color: red" id="SearchPdfMsg"></p></div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="form-group mb-0">
                                        <label> <b>Location</b></label>
                                        @if($Locations['code']==200)
                                            <select class="form-control select2" id="LocationAppend">
                                                <option selected>ALL</option>
                                                @foreach($Locations['data'] as $Location)
                                                    <option id="{{$Location['id']}}">{{$Location['name']}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control select2">
                                                <option disabled selected>No Data</option>
                                            </select>
                                            <p style="color: red">{{$Locations['msg']}}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm">
                                    <div class="form-group mb-0">
                                        <label> <b>Department</b></label>
                                        @if($Departments['code'] ==200)
                                            <select class="form-control select2" id="DepartmentAppend">
                                                <option selected value="">ALL</option>
                                                @foreach($Departments['data']['data'] as $department)
                                                    <option
                                                        value="{{$department['id']}}">{{$department['name']}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control select2">
                                                <option selected disabled>No Data</option>
                                            </select>

                                            <div>
                                                <p style="color: red">{{$Departments['msg']}}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm">
                                    <div class="form-group mb-0">
                                        <label> <b>Employee</b></label>

                                        <select class="form-control select2" id="EmployeeData">

                                        </select>
                                        <div style="display: none" id="EmployeeDiv">
                                            <p style="color: red" id="ErrorEmployee"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm">
                                    <div class="form-group mb-0">
                                        <label> <b>Clients</b></label>
                                        @if($ClientType['code']==200)
                                            <select class="form-control select2" id="ClientData">
                                                <option value="">ALL</option>
                                                @foreach($ClientType['data']  as $data)
                                                    <option value="{{$data}}">{{$data}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control select2">
                                                <option selected disabled>No Data</option>
                                            </select>
                                            <div><p style="color: red">{{$ClientType['msg']}}</p></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body pb-0">
                            <div style="height: 300px; width: 100%;"
                                 id="chartemail">
                                <h3 id="chartErrorMsg"
                                    style="display: none; text-align: center !important; color: red"></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-2 align-self-center">
                                    <p class="mb-0">Show <select class="" id="ShowEntriesList">
                                            <option id="10" selected>10</option>
                                            <option id="25">25</option>
                                            <option id="50">50</option>
                                            <option id="100">100</option>
                                            <option id="200">200</option>
                                        </select> Entries
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="btn-group btn-block">
                                        <button type="button" class="btn btn-outline-primary" onclick="typeSelected(2)">
                                            <i
                                                class="fas fa-envelope-open"></i>
                                            Incoming Email
                                        </button>
                                        <button type="button" class="btn btn-outline-info" onclick="typeSelected(1)"><i
                                                class="fas fa-external-link-alt"></i>
                                            Outgoing Email
                                        </button>
                                        <button type="button" class="btn btn-outline-success" onclick="typeSelected(0)">
                                            <i
                                                class="fas fa-mail-bulk"></i>
                                            All
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 input-group">
                                    <input type="text" class="form-control" placeholder="Search..." value=""
                                           id="SearchTextField">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="SearchButton"
                                                onclick="SearchText()">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 text-right"><p class="mb-0" style="color: red"
                                                                  id="SearchErrorMsg"></p></div>
                            </div>
                            <div class="table-wrap table-responsive">
                                <table class="table nowrap table-bordered table-monitoring" id="EmailDatatFullTable">
                                    <thead>
                                    <tr class="table-primary">
                                        <th>Date/Time</th>
                                        <th>Employee</th>
                                        <th>Computer</th>
                                        <th>Client</th>
                                        <th>Location</th>
                                        <th>Department</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Attachments</th>
                                        <th>Subject Body</th>
                                    </tr>
                                    </thead>
                                    <tbody id="emailListAppend">
                                    </tbody>

                                </table>
                            </div>
                            <div class="col-md-12" id="loader" style="display: none">
                                <div class="loader"></div>
                            </div>

                                <div id="wrapper" class="row">
                                <div class="col-md-6 align-self-center">
                                    <p class="mb-0" id="showPageNumbers"></p>
                                </div>
                                    <div class="col-md-6">
                                <div class="gigantic pagination"  id="PaginationShow">
                                    <a href="#" class="first" data-action="first">&laquo;</a>
                                    <a href="#" class="previous" data-action="previous">&lsaquo;</a>
                                    <input type="text" readonly="readonly"/>
                                    <a href="#" class="next" data-action="next">&rsaquo;</a>
                                    <a href="#" class="last" data-action="last">&raquo;</a>
                                </div>
                                    </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ----------- The mail content Modal ----------- -->
    <div class="modal fade" id="email_content">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h3>Employee Report : Timesheet 16 July 2020</h3>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form id="bodyContent">
                        @csrf
                        {{--                        <div class="row">--}}
                        {{--                            <label for="name" class="text-uppercase col-sm-3">Event Date/Time :</label>--}}
                        {{--                            <div class="col-sm-9">--}}
                        {{--                                <p class="mb-0" id="BodyEventDate"></p>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        <div class="row">
                            <label for="name" class="text-uppercase col-sm-3">Mail Date/Time :</label>
                            <div class="col-sm-9">
                                <p class="mb-0" id="BodyMailDate"></p>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="text-uppercase col-sm-3">From :</label>
                            <div class="col-sm-9">
                                <p class="mb-0" id="BodyFromMail"></p>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="text-uppercase col-sm-3">To :</label>
                            <div class="col-sm-9">
                                <p class="mb-0" id="BodyToMail"></p>
                            </div>
                        </div>

                        <div class="row">
                            <label for="name" class="text-uppercase col-sm-3">Subject :</label>
                            <div class="col-sm-9">
                                <p class="mb-0" id="BodySubject"></p>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="text-uppercase col-sm-3">Attachments :</label>
                            <div class="col-sm-9">
                                <p class="mb-0" id="BodyAttachment"></p>
                            </div>
                        </div>

                    </form>
                    <!-- full Email Design -->
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td style="padding: 10px 0 30px 0;">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="650"
                                       style="border: 1px solid #cccccc; border-collapse: collapse;">
                                    <tr>
                                        <td align="center"
                                            style="padding: 40px 0 30px 0; color: #153643; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
                                            <img src="https://empmonitor.com/wp-content/uploads/2019/03/0K2D_AqW-1.png"
                                                 alt="Logo"
                                                 width="50%"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0 30px 0 30px; float:right; font-family: Arial, sans-serif;">
                                            <b>Date :</b>&nbsp;&nbsp;17 / 6 / 2020
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#ffffff" style="padding: 40px 30px 30px 30px;">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tr>
                                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
                                                        <b>Test For</b> ABC
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table width="100%" border="1"
                                                               style="text-align: center; margin: 20px 00 20px 00;">
                                                            <tr bgcolor="#dbedf9">
                                                                <td width="33.33%"
                                                                    style="padding: 20px 00 20px 00; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Office Time </b><br>
                                                                    00:00:00 hr
                                                                </td>
                                                                <td width="33.33%"
                                                                    style="padding: 20px 00 20px 00; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Time on Projects </b><br>
                                                                    00:00:00 hr
                                                                </td>
                                                                <td width="33.33%"
                                                                    style="padding: 20px 00 20px 00; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Productivity</b><br>
                                                                    80.56 %
                                                                </td>
                                                            </tr>
                                                            <tr bgcolor="#adddfb">
                                                                <td width="33.33%"
                                                                    style="padding: 10px 00 10px 00; font-family: Arial, sans-serif;">
                                                                    <b>Total Time </b><br>
                                                                    00:00:00 hr
                                                                </td>
                                                                <td width="33.33%"
                                                                    style="padding: 10px 00 10px 00; font-family: Arial, sans-serif;">
                                                                    <b>Total Time </b><br>
                                                                    00:00:00 hr
                                                                </td>
                                                                <td width="33.33%"
                                                                    style="padding: 10px 00 10px 00; font-family: Arial, sans-serif;">
                                                                    <b>Compared to previous period</b><br>
                                                                    80.56 %
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <table border="1" width="100%"
                                                               style="text-align: center; margin: 00px 00 20px 00;">
                                                            <tr bgcolor="#0b569a">
                                                                <td
                                                                    style="color: #ffffff; font-family: Arial, sans-serif; font-size: 20px; text-align: center; padding: 10px 00 10px 00;"
                                                                    colspan="3">
                                                                    <b>Employees</b>
                                                                </td>
                                                            </tr>
                                                            <tr bgcolor="#dbedf9">
                                                                <td width="33.33%"
                                                                    style="padding: 20px 20px 20px 20px; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Most Productive</b><br>
                                                                    <img
                                                                        src="https://www.seekpng.com/png/detail/243-2438489_3d-chart-3-icon-png-pie-chart-3d.png"
                                                                        alt="Productive" width="153px"
                                                                        height="77px"/><br><br>
                                                                    <small><b>18th June 2020</b><br> 10:54:25 hrs
                                                                        (80%)</small>
                                                                </td>
                                                                <td width="33.33%"
                                                                    style="padding: 20px 20px 20px 20px; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Most Unproductive</b><br>
                                                                    <img
                                                                        src="https://www.pngkit.com/png/detail/243-2438344_3d-chart-4-icon-png-3-pie-chart.png"
                                                                        alt="Unproductive" width="153px" height="77px"/><br><br>
                                                                    <small><b>18th June 2020</b><br> 10:54:25 hrs
                                                                        (80%)</small>
                                                                </td>
                                                                <td width="33.33%"
                                                                    style="padding: 20px 20px 20px 20px; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Time and Attendance</b><br>
                                                                    <img
                                                                        src="https://currentmillis.com/images/date-time.png"
                                                                        alt="TimeAttendance" width="153px"
                                                                        height="77px"/><br><br>
                                                                    <small><b>18th June 2020</b><br> 90%</small>
                                                                </td>
                                                            </tr>

                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <table border="1" width="100%" style="margin: 00px 00 20px 00;">
                                                            <tr bgcolor="#0b569a">
                                                                <td
                                                                    style="color: #ffffff; font-family: Arial, sans-serif; font-size: 20px; text-align: center; padding: 10px 00 10px 00;"
                                                                    colspan="3">
                                                                    <b>Projects and Tasks</b>
                                                                </td>
                                                            </tr>
                                                            <tr bgcolor="#dbedf9">
                                                                <td width="50%"
                                                                    style="padding: 20px 20px 20px 20px; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Projects and Tasks</b><br><br>
                                                                    <small>EmpMoniter
                                                                        <ul>
                                                                            <li>Timesheet</li>
                                                                            <li>Report</li>
                                                                        </ul>
                                                                    </small>
                                                                    <small>PowerAdSpy
                                                                        <ul>
                                                                            <li>Ad Modify</li>
                                                                            <li>Client List</li>
                                                                        </ul>
                                                                    </small>
                                                                </td>
                                                                <td width="50%"
                                                                    style="padding: 20px 20px 20px 20px; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Top Tasks </b><br><br>
                                                                    <small>EmpMoniter
                                                                        <ul>
                                                                            <li>Timesheet</li>
                                                                            <li>Report</li>
                                                                            <li>Domain</li>
                                                                            <li>Chart</li>
                                                                            <li>Email</li>
                                                                        </ul>
                                                                    </small>
                                                                </td>
                                                            </tr>

                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <table border="1" width="100%">
                                                            <tr bgcolor="#0b569a">
                                                                <td
                                                                    style="color: #ffffff; font-family: Arial, sans-serif; font-size: 20px; text-align: center; padding: 10px 00 10px 00;"
                                                                    colspan="3">
                                                                    <b>Application and Websites</b>
                                                                </td>
                                                            </tr>
                                                            <tr bgcolor="#dbedf9">
                                                                <td width="50%"
                                                                    style="padding: 20px 20px 20px 20px; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Application Usage</b><br><br>
                                                                    <small>
                                                                        <ul>
                                                                            <li>Whatsapp</li>
                                                                            <li>Telegram</li>
                                                                            <li>Instagram</li>
                                                                            <li>Chingari</li>
                                                                            <li>Facebook</li>
                                                                        </ul>
                                                                    </small>
                                                                </td>
                                                                <td width="50%"
                                                                    style="padding: 20px 20px 20px 20px; font-family: Arial, sans-serif; color: #153643;">
                                                                    <b>Website Usage</b><br><br>
                                                                    <small>
                                                                        <ul>
                                                                            <li>Whatsapp</li>
                                                                            <li>Telegram</li>
                                                                            <li>Instagram</li>
                                                                            <li>Chingari</li>
                                                                            <li>Facebook</li>
                                                                        </ul>
                                                                    </small>
                                                                </td>

                                                            </tr>

                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#02213e" style="padding: 30px 30px 30px 30px;">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tr>
                                                    <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;"
                                                        width="75%">

                                                        &copy;
                                                        @if((new App\Modules\User\helper)->checkHost() )
                                                            EmpMonitor
                                                        @endif
                                                            2020<br/>
                                                        <a style="color: #adddfb;" href="#">Find out more</a><br>
                                                        Have any questions? Please check out our <a href="#"
                                                                                                    style="color: #adddfb;">Help
                                                            center.</a> <br>
                                                        You don’t want to recive this email report anymore?
                                                        <a href="#" style="color: #adddfb;">
                                                            Unsubscribe
                                                        </a>
                                                    </td>
                                                    <td align="right" width="25%">
                                                        <table border="0" cellpadding="0" cellspacing="0">
                                                            <tr>
                                                                <td
                                                                    style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">
                                                                    <a href="#" style="color: #ffffff;">
                                                                        <img
                                                                            src="https://www.freepngimg.com/download/logo/69787-icons-brand-encapsulated-postscript-computer-logo-isntagram.png"
                                                                            alt="isntagram" width="38" height="38"
                                                                            style="display: block; margin-right: 10px;"
                                                                            border="0"/>
                                                                    </a>
                                                                </td>
                                                                <td
                                                                    style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">
                                                                    <a href="#" style="color: #ffffff;">
                                                                        <img
                                                                            src="https://cdn1.iconfinder.com/data/icons/logotypes/32/square-twitter-512.png"
                                                                            alt="Twitter" width="38" height="38"
                                                                            style="display: block; margin-right: 10px;"
                                                                            border="0"/>
                                                                    </a>
                                                                </td>
                                                                <td
                                                                    style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">
                                                                    <a href="#" style="color: #ffffff;">
                                                                        <img
                                                                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Facebook_logo_%28square%29.png/480px-Facebook_logo_%28square%29.png"
                                                                            alt="Facebook" width="38" height="38"
                                                                            style="display: block;" border="0"/>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!-- // End full Email Design -->
                </div>

                <!-- Modal footer -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm"><i class="far fa-file-pdf mr-2"></i> Save as PDF
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="PrintContent"><i
                            class="fas fa-print mr-2"></i>Print Email
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i
                            class="fas fa-times mr-2"></i>Close Email
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('post-load-scripts')

    <!-- Javascripts -->
    <!-- Javascripts -->
    {{--    <script src="../assets/plugins/jquery/jquery-3.1.0.min.js"></script>--}}
    {{--    <script src="../assets/plugins/bootstrap/popper.min.js"></script>--}}
    {{--    <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>--}}
    {{--    <script src="../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>--}}
    {{--    <script src="../assets/js/concept.min.js"></script>--}}

    <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

    {{--    for pdf download--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"
            integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.2.11/jspdf.plugin.autotable.min.js"></script>
@endsection

@section ('page-scripts')
    <script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js"></script>
    <script src="../assets/js/incJSFile/EmployeeDetailJs/emailMonitoring.js"></script>
    <script src="../assets/js/incJSFile/_dataFiltration.js"></script>
    <script src="../assets/js/incJSFile/JqueryDatatablesCommon.js"></script>
    <script src="../assets/js/JqueryPagination/jquery.jqpagination.js"></script>

@endsection
