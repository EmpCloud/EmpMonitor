<div class="row">
    <div class="col-md-12">
  <!-- Nav tabs -->
        <div class="card">
            <div class="card-body">
                <div class="btn-group nav nav-tabs tab-action-wrapper">
                    <a type="button" class="btn btn-info nav-link" data-toggle="tab" href="#Timesheets" id="TimesheetsTab" onclick="loadTimeSheetData()" title="{{ __('messages.timesheets') }}">
                        <i class="far fa-calendar-alt mr-2"></i>{{ __('messages.timesheets') }}
                    </a>
                    <a type="button" class="btn btn-info nav-link" data-toggle="tab" onclick="loadBrowserHistory()" href="#BrowserHistory" id="BrowserHistoryTab" title="{{ __('messages.webHistory') }}">
                        <i class="fas fa-globe  mr-2"></i>{{ __('messages.webHistory') }}
                    </a>
                    <a type="button" class="btn btn-info nav-link" data-toggle="tab" onclick="loadWebAppHistory(1)" href="#AppHistory" id="AppHistoryTab" title="{{ __('messages.applicationHistory') }}">
                        <i class="fas fa-mobile-alt mr-2"></i>{{ __('messages.applicationHistory') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12"> 
        <div class="tab-content mt-0">
            <div id="Timesheets" class="tab-pane fade">
                <div class="card" id="Timesheet">
                    <div class="card-body">
                        <h3 class="text-center">{{ __('messages.timesheets') }}</h3>
                        <div class="table-wrap table-responsive">
                            <table id="timeSheetDataTable" class="table table-striped table-bordered ad_tab w-100">
                                <thead>
                                    <tr class="table-primary">
                                        <th class="text-center">{{ __('messages.clockin') }}</th>
                                        <th class="text-center">{{ __('messages.clockout') }}</th>
                                        <th class="text-center">
                                            {{ __('messages.totalHours') }}
                                            <i class="fas fa-info-circle ml-1" title="{{ __('messages.clockout') }} - {{ __('messages.clockin') }}"></i>
                                        </th>
                                         <th class="text-center">{{ __('messages.activeHours') }}<i class="fas fa-info-circle ml-1"
                                                                               title="{{__('messages.activeInfo')}}"></i>
                                        </th>
                                        <th class="text-center text-warning">{{ __('messages.idleHour') }}
                                            <i class="fas fa-info-circle ml-1"
                                               title="{{__('messages.idleInfo')}}"></i>
                                        </th>
                                        <th class="text-center text-warning">Productive Hours
                                            <i class="fas fa-info-circle ml-1"
                                               title="Productive Hours"></i>
                                        </th>
                                        <th class="text-center text-danger">Unproductive Hours
                                            <i class="fas fa-info-circle ml-1"
                                               title="Unproductive Hours"></i>
                                        </th>
                                        <th class="text-center">Neutral Hours
                                            <i class="fas fa-info-circle ml-1"
                                               title="Neutral Hours"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="timeSheetsData">
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Browser History Tab -->
            <div id="BrowserHistory" class="tab-pane fade">
                <div class="col-md-12 p-0" id="browserHistoryTable">
                    <div class="card">
                        <div class="card-body">
                            <table id="browserHistoryTableId" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.browser') }}</th>
                                        <th>{{ __('messages.title') }}</th>
                                        <th>{{ __('messages.browserURL') }}</th>
                                        <th>{{ __('messages.startTime') }}</th>
                                        <th>{{ __('messages.endTime') }}</th>
                                        <th>Active Seconds</th>
                                        <th>Idle Seconds</th>
                                        <th>Productive Seconds</th>
                                        <th>Unproductive Seconds</th>
                                        <th>Neutral Seconds</th>
                                        <th>{{ __('messages.keystrokeData') }}</th>
                                        <th>{{ __('messages.keystrokesCount') }}</th>
                                        <th>{{ __('messages.mouseClickCount') }}</th>
                                        <th>{{ __('messages.mouseMovementCount') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="browserHistoryDataTableData">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- App History Tab -->
            <div id="AppHistory" class="tab-pane fade">
                <div class="col-md-12 p-0">
                    <div class="card">
                        <div class="card-body">
                            <table id="applicationHistoryTableId" class="table table-striped table-bordered">
                                <thead>
                                    <tr style="text-align: center">
                                        <th>{{ __('messages.app') }}</th>
                                        <th>{{ __('messages.title') }}</th>
                                        <th>{{ __('messages.startTime') }}</th>
                                        <th>{{ __('messages.endTime') }}</th>
                                        <th>Active Seconds</th>
                                        <th>Idle Seconds</th>
                                        <th>Productive Seconds</th>
                                        <th>Unproductive Seconds</th>
                                        <th>Neutral Seconds</th>
                                        <th>{{ __('messages.keystrokeData') }}</th>
                                        <th>{{ __('messages.keystrokesCount') }}</th>
                                        <th>{{ __('messages.mouseClickCount') }}</th>
                                        <th>{{ __('messages.mouseMovementCount') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="applicationHistoryDataTableData">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div> 
    </div> 
</div> 
