@extends('User::Layout._layout')

@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           @if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif @endif{{ __('messages.manageLocDept') }} </title>
@endsection

@section('page-style')
    @include('User::Layout._modernStyles')
    <style>
        /* Page Specific Styles Only */
        #addLocationModal{
            z-index: 9999999 !important;
        }
        
        #deleteDepartmentsModal{
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
        
        @media (max-width: 767px){
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
        
        .dept_new{
            border: 1px solid #479fff5c;
            padding: 8px;
            border-radius: 8px;
            display: inline-flex;
        }
    </style>
@endsection

@section('post-load-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    {{--    <script src="../assets/js/pages/dashboard.js"></script>--}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
                            {{ __('messages.Location') }}
                        </li>
                    </ol>
                </nav>
               
                <h1 class="page-title">{{ __('messages.Location') }}</h1>

                <button
                    id="side-step2"
                    type="button"
                    class="btn btn-primary float-right"
                    data-toggle="modal"
                    data-target="#addLocationModal"
                    >
                    {{ __('messages.add') }} {{ __('messages.Location') }}
                </button>
            </div>

            <div class="row">
                @if(Session::has(env('Admin')) && ((new App\Modules\User\helper)->getHostName() == env('Admin')) )

                    <div class="col-md-12" style="display: none" >
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2"><label class="font-weight-bold">{{ __('messages.select') }}
                                            {{ __('messages.timezone') }}:-</label></div>
                                    <div class="col-sm">
                                        <div class="form-group mb-0">
                                            <select class="form-control select2" id="SelectUpdateTimeZoneOrg">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="w-100">
                                <table
                                    id="locationTable"
                                    class="table table-striped table-bordered">
                                    <thead>
                                    <tr class="table-primary">
                                        <th>{{ __('messages.Location') }} </th>
                                        <th>{{ __('messages.action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody id="getLocDept">
                                       @if(isset($location_departmnet['code']) && $location_departmnet['code'] == 200)
                                          @if(!empty($location_departmnet['data']))
                                              @foreach($location_departmnet['data'] as $location)
                                                  <tr id="{{ $location['id'] }}">
                                                      <td id="location{{ $location['id'] }}">
                                                               {{ $location['location_name'] }}
                                                      </td>
                                                      <td>
                                                           <a class="" data-toggle="modal" data-target="#updateLocationModal" onclick="updateLocation({{ $location['id'] }});"><i class="far fa-edit text-success" title="Edit"></i></a>
                                                           <a id="" class="" data-toggle="modal" onclick="deleteLocation({{ $location['id'] }});"><i class="far fa-trash-alt text-danger" title="Delete"></i></a>
                                                     </td>
                                                  </tr>
                                              @endforeach
                                          @endif
                                        @else
                                                <tr align="center" id="data_not_found">
                                                  <td colspan="2"><p>{{ $location_departmnet['message'] ?? 'No data found.' }}</p></td>
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
    {{--============= add location ============== --}}
   
        <div class="modal fade" id="addLocationModal" tabindex="-1" role="dialog" aria-labelledby="addLocationLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="open-addLocModal modal-title " id="addLocationModalLabel">{{ __('messages.add') }} {{ __('messages.Location') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <div class="form-group">
                                <input type="text" class="form-control mb-2" name="locName" id="locatinName"
                                       placeholder="{{ __('messages.type') }} {{ __('messages.Location') }}"/>
                                <div class="error" style="color: red;" id="locError1"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"> {{ __('messages.cancel') }} </button>
                            <button type="submit" id="addLocId" class="btn btn-primary" onclick="addLocationFun()">{{ __('messages.add') }} {{ __('messages.Location') }} </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--============= update location ============== --}}
   
        <div class="modal fade" id="updateLocationModal" tabindex="-1" role="dialog" aria-labelledby="updateLocationLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="open-addLocModal modal-title " id="addLocationModalLabel">{{ __('messages.update') }} {{ __('messages.Location') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <div class="form-group">
                                  <input type="hidden" id="locID"/>
                                <input type="text" class="form-control mb-2" name="editlocationName" id="editlocationName"
                                       placeholder="{{ __('messages.update') }} {{ __('messages.Location') }}"/>
                                <div class="error" style="color: red;" id="locError1"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"> {{ __('messages.cancel') }} </button>
                            <button type="submit" id="updateLocId" class="btn btn-primary" onclick="updateLocationFun()">{{ __('messages.update') }} {{ __('messages.Location') }} </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

   
   
@endsection

@section ('page-scripts')
    <script src="../assets/js/final-timezone.js"></script>
    <script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js"></script>
    <script>
    function addLocationFun() {
    $.ajax({
        url: "/" + userType + '/add-location',
        type: 'Post',
        data: {
            location_name: $('#locatinName').val(),
        },
        beforeSend: function () {
             $('#locError1').html("");
                                     $('#addLocId').attr("disabled", true);
        },
       success: function (response) {
    if (response.statusCode === 200 && response.data.code === 200) {
        successSwal(response.data.message);
        location.reload();
    } else {
        errorSwal(response.data.message ?? 'Something went wrong');
    }
    }
})
}
      
function updateLocation(id) {
    $('#locID').val(id);
    // Get the location name from the table and prefill it
    let locationName = $('#location' + id).text().trim();
    $('#editlocationName').val(locationName);
}
   
    function deleteLocation(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to delete this location?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/" + userType + '/delete-location', 
                type: 'POST',  
                data: {
                    id,  
                },
                beforeSend: function () {
                    $('#locError1').html("");
                    $('#addLocId').attr("disabled", true);
                },
                success: function (response) {
                    if (response.code === 200) {
                        successSwal(response.message);
                        location.reload();
                    } else {
                        errorSwal(response.message ?? 'Something went wrong');
                    }
                },
                error: function () {
                    errorSwal('Failed to delete location. Please try again.');
                },
                complete: function () {
                    $('#addLocId').attr("disabled", false);
                }
            });
        }
    });
}
function updateLocationFun(){
     $.ajax({
                url: "/" + userType + '/update-location', 
                type: 'POST',  
                data: {
                    id : $('#locID').val(), 
                    locationName : $('#editlocationName').val() 
                },
                beforeSend: function () {
                   
                },
                success: function (response) {
                    if (response.code === 200) {
                        successSwal(response.message);
                        location.reload();
                    } else {
                        errorSwal(response.message ?? 'Something went wrong');
                    }
                },
                error: function () {
                    errorSwal('Failed to update location. Please try again.');
                },
                complete: function () {
                    $('#addLocId').attr("disabled", false);
                }
            });
}
 </script>
@endsection

