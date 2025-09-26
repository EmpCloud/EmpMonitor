<!DOCTYPE html>
<html lang="en">

<head>
    @include('User::Layout._header')
</head>
@section('title')
    <title>EmpMonitor</title>
@endsection
<body>
<!-- Page Container -->
<main>
    <div class="downloadBG text-center text-white py-5 mb-5">
        <h1>
            @if((new App\Modules\User\helper)->checkHost() )
                {{ __('messages.downldEmpSoft') }}
            @else
                {{ __('messages.downldEmpSoftReseller') }}
            @endif
        </h1>
        <p>{{ __('messages.headerDesc') }}</p>
    </div>
    <section>
        <div class="container">
            <div class="row my-5">
                <div class="col-md-6">
                    <img class="img-fluid" src="../assets/images/WFH.png" alt="WFH">
                </div>
                <div class="col-md-6 pr-5">
                    <h3>{{ __('messages.WFH') }}</h3>
                    <p>{{ __('messages.desc1') }}</p>
                    <h3>{{ __('messages.BS') }}</h3>
                    <p>{{ __('messages.desc2') }}</p>
                    <p>{{ __('messages.desc3') }}</p>
                    <a href="javascript:void(0)" class="btn btn-primary float-left" style="padding: 15px" id="downloadPageBack" title="{{ __('messages.back_dashboard') }}"><i
                            class="fas fa-chevron-left mr-2"></i>{{ __('messages.back_dashboard') }}</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mt-4">
                    <h2>{{ __('messages.agentDownloads') }}</h2>
                    <div class="table-wrap table-responsive">
                        <table id="download_table" class="table table-bordered">
                            <thead class="table-primary">
                            <tr>
                                <th>{{ __('messages.downloadType') }}</th>
                                <th>{{ __('messages.officeAgent') }}
                                    <i class="fas fa-info-circle ml-2"
                                       title="{{ __('messages.titleOA') }}"></i>
                                </th>
                                <th>{{ __('messages.personalAgent') }}
                                    <i class="fas fa-info-circle ml-2"
                                       title="{{ __('messages.titlePA') }}"></i>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($DownloadLinksDetails['code']===200 )
                                <tr>
                                    <td>Windows 64 bit</td>
                                    <td>
                                        @foreach($DownloadLinksDetails['data'] as  $w64)
                                            @if($w64['type']==="win64" && $w64['mode']==="office")
                                                <ul>
                                                    @if($w64['file_type']===".exe")
                                                        <li><a href="{{$w64['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$w64['build_version']}}) <span
                                                                    class="badge badge-pill badge-success ml-1">{{$w64['file_type']}}</span></a>
                                                        </li>
                                                    @else
                                                        <li><a href="{{$w64['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$w64['build_version']}}) <span
                                                                    class="badge badge-pill badge-info ml-1">{{$w64['file_type']}}</span></a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($DownloadLinksDetails['data'] as  $w64)
                                            @if($w64['type']==="win64" && $w64['mode']==="personal")
                                                <ul>
                                                    @if($w64['file_type']===".exe")
                                                        <li><a href="{{$w64['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$w64['build_version']}})<span
                                                                    class="badge badge-pill badge-success ml-1">{{$w64['file_type']}} </span></a>
                                                        </li>
                                                    @else
                                                        <li><a href="{{$w64['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$w64['build_version']}})<span
                                                                    class="badge badge-pill badge-info ml-1">{{$w64['file_type']}} </span></a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>

                                <tr>
                                    <td>Windows 32 bit</td>
                                    <td>
                                        @foreach($DownloadLinksDetails['data'] as  $data)
                                            @if($data['type']==="win86" && $data['mode']==="office")
                                                <ul>
                                                    @if($data['file_type']===".exe")
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-success ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @else
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-info ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($DownloadLinksDetails['data'] as  $data)
                                            @if($data['type']==="win86" && $data['mode']==="personal")
                                                <ul>
                                                    @if($data['file_type']===".exe")
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-success ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @else
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-info ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif

                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>LINUX</td>
                                    <td>
                                        <?php $linuxDataOLength = 0; ?>
                                        @foreach($DownloadLinksDetails['data'] as  $data)
                                            @if($data['type']==="linux" && $data['mode']==="office")
                                            <?php $linuxDataOLength = 1; ?>
                                                <ul>
                                                    @if($data['file_type']===".run")
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-success ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @else
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-info ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                        @endforeach
                                        @if($linuxDataOLength == 0)
                                            <ul>
                                                <li><a href="http://help.empmonitor.com/" target="_blank">{{ __('messages.contactSupport') }}...</a></li>
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        <?php $linuxDataPLength = 0; ?>
                                        @foreach($DownloadLinksDetails['data'] as  $data)
                                            @if($data['type']==="linux" && $data['mode']==="personal")
                                            <?php $linuxDataPLength = 1; ?>
                                                <ul>
                                                    @if($data['file_type']===".run")
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-success ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @else
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-info ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                        @endforeach
                                        @if($linuxDataPLength == 0)
                                            <ul>
                                                <li><a href="http://help.empmonitor.com/" target="_blank">{{ __('messages.contactSupport') }}...</a></li>
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>MAC</td>
                                    <td>
                                    <?php $macOfficeLength = 0; ?>
                                        @foreach($DownloadLinksDetails['data'] as  $data)
                                            @if($data['type']==="mac" && $data['mode']==="office")
                                            <?php $macOfficeLength = 1; ?>
                                                <ul>
                                                    @if($data['file_type']===".pkg")
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-success ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @else
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-info ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                        @endforeach
                                        @if($macOfficeLength == 0)
                                            <ul>
                                                <li><a href="http://help.empmonitor.com/" target="_blank">{{ __('messages.contactSupport') }}...</a></li>
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        <?php $macPersonalLength = 0; ?>
                                        @foreach($DownloadLinksDetails['data'] as  $data)
                                            @if($data['type']==="mac" && $data['mode']==="personal")
                                            <?php $macPersonalLength = 1; ?>
                                                <ul>
                                                    @if($data['file_type']===".pkg")
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-success ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @else
                                                        <li><a href="{{$data['url']}}">{{ __('messages.downloads') }}
                                                                (v{{$data['build_version']}})
                                                                <span
                                                                    class="badge badge-pill badge-info ml-1">{{$data['file_type']}}</span></a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif

                                        @endforeach
                                        @if($macPersonalLength == 0)
                                            <ul>
                                                <li><a href="http://help.empmonitor.com/" target="_blank">{{ __('messages.contactSupport') }}...</a></li>
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="3"
                                        style="text-align: center"> {{ __('messages.agentBuildingInfo') }} </td>
                                </tr>
                            @endif

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>
@include('User::Layout._scripts')
<script src="../assets/plugins/dropify-master/js/dropify.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script>
    $("#downloadPageBack").on('click',function(){
        if(intro_status && !tour_completed){
            goToStep(6);
        }else{
            location.href = document.referrer;
        }
    })

    $(document).ready(function () {
        if (is_admin  && !tour_completed) {
            $('#videoPlayer').attr('src','https://www.youtube.com/embed/qM4Lh9crNzw');
            $('#showvideo').show();
        }
        $("#download_table").DataTable({
            "info": false,
            "paging": false,
            bFilter: false,
            order: [[0, 'dec']],
            language: {
                "emptyTable": "<b>{{ __('messages.contactTeam') }}...</b>"
            }
        });
        $("#select_date").datepicker();
        // $("#downloadPageBack").attr("href", document.referrer); 
    });
</script>

</body>

</html>
