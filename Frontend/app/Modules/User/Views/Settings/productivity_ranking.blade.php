@extends('User::Layout._layout')

@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif {{__('messages.productivityRules')}}</title>
@endsection

@section('extra-style-links')
    {{-- <link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" /> --}}
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet"/>
@endsection

@section('page-style')
    @include('User::Layout._modernStyles')
    <style>
        /* Page Specific Styles Only */
        /* Radio Buttons */
        .form-check-inline {
            margin-right: 1.5rem;
        }
        
        .form-check-label {
            font-weight: 500;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .form-check-label:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .form-check-input:checked + .form-check-label {
            font-weight: 600;
        }
        
        /* Spinner */
        .spinner-border {
            border-radius: 50%;
        }
        
        /* Misc */
        input#upload-bulk {
            cursor: pointer;
        }

        label.custom-file-label::after {
            content: "{{__('messages.browse')}}";
            cursor: pointer !important;
        }

        label.custom-file-label {
            z-index: 2;
        }
        
        .suspended_user {
            line-height: 1.6;
            margin-bottom: -24px;
        }
        
        .suspended_user i{
            color: #fff9b2;
        }
        
        .toolbar {
            text-align: center;
        }
        
        dl, ol, ul {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
        
        #ExportBtn, #UpdateBtn, #ExportDiv button[type=button]{
            margin-bottom: 10px;
        }

        @media screen and (max-width: 1199px) {
            .suspended_user {
                position: absolute !important;
                left: 45%;
                top: 5%;
            }
        }
        
        @media screen and (max-width: 991px) {
            .suspended_user {
                position: static !important;
                text-align: right;
                margin-bottom: 20px
            }
        }
        
        @media screen and (max-width: 767px) {
            .suspended_user {
                position: static !important;
                text-align: center;
                margin-bottom: 12px;
            }
            #All{
                height: 50px;
            }
            #showPageNumbers {
                margin-bottom: 12px !important;
            }
        }
        
        .pickerStyles {
            text-align: inherit !important;
            cursor: text;
        }

        .controlsDivStyle {
            left: 95% !important;
            height: 3px;
            margin-top: 6px;
        }

        .scroll-up {
            top: 1px !important;
            transform: translateY(0px);
            height: 9.5px !important;
        }

        .scroll-down {
            top: 9.5px !important;
            transform: translateY(0px);
            height: 9.5px !important;
        }
    </style>
@endsection

@section('content')
    <!-- Page Inner -->

    <div class="page-inner no-page-title">
        <input type="hidden" id="FilteredID" value="1">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item active"><a href="dashboard"
                                                              style="color: #0686d8;font-weight: 500;">
                                {{__('messages.home')}} </a></li>
                        <li class="breadcrumb-item" aria-current="page">
                            {{__('messages.productivityRules')}}
                        </li>
                    </ol>
                </nav>
              
            </div>
            <div id="UnaccessModal" style="display: none">
                <div class="alert alert-danger ">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Note : </strong>
                    <p id="ErrorMsgForUnaccess"></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h1 class="page-title"> {{__('messages.productivityRules')}} </h1>
                </div>
                <div class="col-md-4">&nbsp;</div>
               

            </div>
            <div id="UnaccessModal" style="display: none">
                <div class="alert alert-danger ">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Note : </strong>
                    <p id="ErrorMsgForUnaccess"> Indicates a successful or positive action.</p>
                </div>
            </div>
            <div class="row">

                <div class="col-md-12 mt-3">
                    <div class="card">
                        
                        <div class="card-body">
                            <div class="row">

                    
                               
                                <div class="col-md-9 real-show">
                                    <p class="mb-0">{{__('messages.show')}} <select class="" id="ShowEntriesList">
                                            <option id="10" selected>10</option>
                                            <option id="25">25</option>
                                            <option id="50">50</option>
                                            {{--                                            <option id="100">100</option>--}}
                                            {{--                                            <option id="200">200</option>--}}
                                        </select> {{__('messages.entries')}}
                                    </p>
                                </div>
                                <div class="col-md-3 mb-0">
                                    <div class="input-group">
                                        <input type="text" id="searchByname" class="form-control"
                                               placeholder="{{__('messages.searchbyActivity')}}..">
                                        <div class="input-group-append">
                                            <button class="btn btn-info setting-productivityRule-search" onclick="nameSearch()" type="button">
                                                <i class="fa fa-search"></i>
                                            </button>
                                            <i class="fas fa-info-circle fa-sm" data-toggle="tooltip" title="{{__('messages.categoryInfoSearch')}}"></i>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <table id="accordion" class="table table-striped table-bordered table-responsive-xl mt-4">
                                <thead>
                                <tr class="table-primary">

                                    <th width="300" idth="300"><a id="TableName"
                                                                  onclick="sort('Name ','NameSort')">{{__('messages.activity')}}</a>
                                        <span class="float-right"><i id="NameSort"
                                                                     class="fas fa-long-arrow-alt-up text-light"></i>
                                            </span></th>
                                    <th>{{__('messages.prodRanking')}}</th>
                                </tr>
                                </thead>
                                <tbody id="appendDataProRanking">
                                @if(count($response['data']) !=0)
                                    @foreach($response['data'] as  $ranking)

                                        <tr>
                                            @if($ranking['type'] === 2)
                                                @if(strlen($ranking['name']) <= 28)
                                                    <td><i class="fas fa-globe"></i> {{ $ranking['name']}}
                                                @else
                                                    <td title="{{ $ranking['name'] }}"><i class="fas fa-globe"></i> {{ substr($ranking['name'], 0, 28)}} ...
                                                @endif

                                            @else
                                                @if(strlen($ranking['name']) <= 28)
                                                    <td><i class="fas fa-mobile-alt"></i> {{ $ranking['name']}}
                                                @else
                                                    <td title="{{ $ranking['name'] }}"><i class="fas fa-mobile-alt"></i> {{ substr($ranking['name'], 0, 28)}} ...
                                                @endif
                                            @endif

                                                    </td>
                                            <td>
                                                <form action="/action_page.php">
                                                    @csrf
                                                    <div class="form-check-inline text-success">
                                                        <label class="form-check-label" for="productive">

                                                            <input type="radio" class="form-check-input globalPro"
                                                                   id="pro{{ $ranking['_id'] }}"
                                                                   onchange="changeProd('{{ $ranking['_id'] }}', 1);"
                                                                   name="radioPro{{ $ranking['_id'] }}"
                                                                   value="1-global-{{ $ranking['_id'] }}"
                                                                   <?php if(!empty($ranking)){
                                                                   if(isset($ranking['category']) && $ranking['category'] == 1){ ?>checked <?php }
                                                                } ?> >{{__('messages.productive')}}
                                                        </label>
                                                    </div>
                                                    <div class="form-check-inline text-warning">
                                                        <label class="form-check-label" for="neutral">
                                                            <input type="radio" class="form-check-input globalPro"
                                                                   id="neutral{{ $ranking['_id'] }}"
                                                                   onchange="changeProd('{{ $ranking['_id'] }}', 0);"
                                                                   name="radioPro{{ $ranking['_id'] }}"
                                                                   value="0-global-{{ $ranking['_id'] }}"
                                                                   <?php if(isset($ranking['category']) && $ranking['category'] == 0){ ?>checked <?php } ?>>{{__('messages.neutral')}}
                                                        </label>
                                                    </div>
                                                    <div class="form-check-inline text-danger">
                                                        <label class="form-check-label" for="unproductive">
                                                            <input type="radio" class="form-check-input globalPro"
                                                                   id="unpro{{ $ranking['_id'] }}"
                                                                   onchange="changeProd('{{ $ranking['_id'] }}', 2);"
                                                                   name="radioPro{{ $ranking['_id'] }}"
                                                                   value="2-global-{{ $ranking['_id'] }}"
                                                                   <?php if(isset($ranking['category']) && $ranking['category'] == 2){ ?>checked <?php } ?>>{{__('messages.unproductive')}}
                                                        </label>
                                                    </div>
                                    
                                                </form>
                                            </td>

                                        </tr>
                                  
                                    @endforeach
                                @else
                                    <tr>
                                        <td></td>
                                        <td>{{__('messages.Nodata')}}</td>

                                    </tr>

                                @endif


                                </tbody>
                            </table>


                            <div class="col-md-12 text-right pr-0">
                                <div style="display: none" id="loader" class="spinner-border text-primary"
                                     role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div id="wrapper" class="row">
                                <div class="col-md-6 align-self-center real-show">
                                    <p class="mb-0" id="showPageNumbers"></p>
                                </div>
                                <div class="col-md-6 real-pagination">
                                    <div class="gigantic pagination" id="PaginationShow">
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
        <!-- Main Wrapper -->
    </div>
    <!-- /Page Inner -->




    
@endsection

@section('page-scripts')
    <!-- ===============================  // Add ranking  ============================== -->
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="../assets/js/pages/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js"
            integrity="sha256-VeNaFBVDhoX3H+gJ37DpT/nTuZTdjYro9yBruHjVmoQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-duration-picker/dist/html-duration-picker.min.js"></script>

    <script>
        let TOTAL_COUNT_EMAILS = <?php echo isset($response['count']) ? $response['count'] : 0 ?>;
        var lblActivity = '{{__('messages.activity')}}';
        var lblCategory = '{{__('messages.category')}}';
        let departmentList;
      
    </script>
    <script src="../assets/js/incJSFile/JqueryDatatablesCommon.js"></script>
    <script src="../assets/js/JqueryPagination/jquery.jqpagination.js"></script>
    <script src="../assets/js/JqueryPagination/jquery.jqpaginationInside.js"></script>
    <link href="../assets/css/jqpagination.css" rel="stylesheet">
    <link href="../assets/css/jqpaginationInside.css" rel="stylesheet">
    <script src="../assets/js/incJSFile/ProductivityRanking.js"></script>
    <script type="text/javascript" src="//unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet"/>
    <script src="../assets/js/incJSFile/_timeConvertions.js"></script>
    <script src="../assets/js/incJSFile/_dataFiltration.js"></script>
@endsection
