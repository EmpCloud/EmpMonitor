@extends('User::Layout._layout')

@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() ){{env('WEBSITE_TITLE')}} | @endif Monitoring Control </title>
@endsection

@section('page-style')
    @include('User::Layout._modernStyles')
    <style>
        /* Monitoring Rules Specific Styles */
        .modal {
            z-index: 9999999 !important;
        }
        
        .select2-container {
            width: 100% !important;
        }
        
        .select2-search__field {
            width: 140% !important;
        }
        
        /* Fix dropdown z-index and positioning */
        .table tbody td {
            overflow: visible !important;
        }
        
        .dropdown {
            position: relative !important;
        }
        
        .dropdown-menu {
            position: absolute !important;
            z-index: 99999 !important;
            top: 100% !important;
            left: auto !important;
            right: 0 !important;
            margin-top: 0.125rem;
        }
        
        /* Modern Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e0e6ed;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        input:checked + .slider {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .tracking-options {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .tracking-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .tracking-option:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .tracking-option label {
            margin: 0;
            font-weight: 500;
            color: #495057;
        }
        
        .badge-count {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-default {
            background-color: #6c757d;
        }
        
        /* Remove all overflow restrictions */
        .table-responsive,
        .card,
        .card-body,
        .w-100,
        #rulesTable {
            overflow: visible !important;
        }
        
        /* Remove transform from table hover to prevent stacking context issues */
        #rulesTable tbody tr:hover {
            transform: none !important;
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('post-load-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('content')
    <div class="page-inner no-page-title" style="padding-right: 15px;">
        <div id="main-wrapper">

            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item"><a href="/{{$role}}/dashboard" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.home') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Monitoring Control
                        </li>
                    </ol>
                </nav>
               
                <h1 class="page-title">Monitoring Control</h1>

                <button
                    type="button"
                    class="btn btn-primary float-right"
                    data-toggle="modal"
                    data-target="#addRuleModal"
                    >
                    <i class="fas fa-plus"></i> Add Rule
                </button>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="w-100">
                                <table
                                    id="rulesTable"
                                    class="table table-striped table-bordered">
                                    <thead>
                                    <tr class="table-primary">
                                        <th>Rule Name</th>
                                        <th>Description</th>
                                        <th>Tracking Options</th>
                                        <th>Employees</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody id="rulesTableBody">
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <div class="spinner-border" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{--============= add rule ============== --}}
   
        <div class="modal fade" id="addRuleModal" tabindex="-1" role="dialog" aria-labelledby="addRuleLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Monitoring Rule</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="addRuleForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Rule Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ruleName" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <hr>
                        <h6 class="mb-3">Tracking Options</h6>
                        <div class="tracking-options">
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackApplications" checked>
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Applications</label>
                            </div>
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackWebsites" checked>
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Websites</label>
                            </div>
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackKeystrokes" checked>
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Keystrokes</label>
                            </div>
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackScreenshots" checked>
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Screenshots</label>
                            </div>
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackMouseClicks" checked>
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Mouse Clicks</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Rule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--============= edit rule ============== --}}
   
        <div class="modal fade" id="editRuleModal" tabindex="-1" role="dialog" aria-labelledby="editRuleLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Monitoring Rule</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="editRuleForm">
                    <input type="hidden" name="id" id="editRuleId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Rule Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ruleName" id="editRuleName" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="editDescription" rows="2"></textarea>
                        </div>
                        <hr>
                        <h6 class="mb-3">Tracking Options</h6>
                        <div class="tracking-options">
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackApplications" id="editTrackApplications">
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Applications</label>
                            </div>
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackWebsites" id="editTrackWebsites">
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Websites</label>
                            </div>
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackKeystrokes" id="editTrackKeystrokes">
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Keystrokes</label>
                            </div>
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackScreenshots" id="editTrackScreenshots">
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Screenshots</label>
                            </div>
                            <div class="tracking-option">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="trackMouseClicks" id="editTrackMouseClicks">
                                    <span class="slider"></span>
                                </label>
                                <label class="mb-0">Track Mouse Clicks</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Rule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--============= assign employees ============== --}}
   
        <div class="modal fade" id="assignEmployeesModal" tabindex="-1" role="dialog" aria-labelledby="assignEmployeesLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Employees to Rule</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="assignEmployeesForm">
                    <input type="hidden" name="ruleId" id="assignRuleId">
                    <div class="modal-body">
                        <p class="text-muted mb-3">Rule: <strong id="assignRuleName"></strong></p>
                        <div class="form-group">
                            <label>Select Employees <span class="text-danger">*</span></label>
                            <select class="form-control select2-multiple" name="employeeIds[]" id="employeeSelect" multiple="multiple" required>
                            </select>
                            <small class="form-text text-muted">
                                Note: Employees will be removed from their previous rule when assigned to this rule.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign Employees</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--============= view employees ============== --}}
   
        <div class="modal fade" id="viewEmployeesModal" tabindex="-1" role="dialog" aria-labelledby="viewEmployeesLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Employees in Rule</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Rule: <strong id="viewRuleName"></strong></p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr class="table-primary">
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Assigned At</th>
                            </tr>
                            </thead>
                            <tbody id="employeesTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
   
@endsection

@section ('page-scripts')
<script>
    // Disable DataTable auto-initialization for this page
    if (typeof $.fn.dataTable !== 'undefined') {
        $.fn.dataTable.ext.errMode = 'none';
    }

    // Setup AJAX CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        console.log('Monitoring Control page loaded');
        loadRules();

        // Add Rule Form
        $('#addRuleForm').on('submit', function(e) {
            e.preventDefault();
            createRule();
        });

        // Edit Rule Form
        $('#editRuleForm').on('submit', function(e) {
            e.preventDefault();
            updateRule();
        });

        // Assign Employees Form
        $('#assignEmployeesForm').on('submit', function(e) {
            e.preventDefault();
            assignEmployees();
        });

        // Initialize Select2
        $('#assignEmployeesModal').on('shown.bs.modal', function () {
            $('#employeeSelect').select2({
                dropdownParent: $('#assignEmployeesModal'),
                placeholder: 'Select employees',
                allowClear: true
            });
            loadAllEmployees();
        });
    });

    function loadRules() {
        console.log('Loading rules...');

        $.ajax({
            url: '/' + userType + '/monitoring-rules/get',
            method: 'GET',
            data: {
                skip: 0,
                limit: 1000
            },
            success: function(response) {
                console.log('Rules loaded:', response);
                if (response.code === 200) {
                    displayRules(response.data.rules);
                }
            },
            error: function(xhr) {
                console.error('Error loading rules:', xhr);
                $('#rulesTableBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading rules. Please try again.</td></tr>');
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Failed to load rules', 'error');
                }
            }
        });
    }

    function displayRules(rules) {
        const tbody = $('#rulesTableBody');
        tbody.empty();

        if (rules.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center">No rules found</td></tr>');
            return;
        }

        rules.forEach(rule => {
            const trackingBadges = [];
            if (rule.track_applications == 1) trackingBadges.push('<span class="badge badge-success">Apps</span>');
            if (rule.track_websites == 1) trackingBadges.push('<span class="badge badge-success">Websites</span>');
            if (rule.track_keystrokes == 1) trackingBadges.push('<span class="badge badge-success">Keystrokes</span>');
            if (rule.track_screenshots == 1) trackingBadges.push('<span class="badge badge-success">Screenshots</span>');
            if (rule.track_mouse_clicks == 1) trackingBadges.push('<span class="badge badge-success">Mouse Clicks</span>');

            const isDefault = rule.is_default == 1;
            const row = `
                <tr>
                    <td>
                        ${rule.rule_name} 
                        ${isDefault ? '<span class="badge badge-primary">Default</span>' : ''}
                    </td>
                    <td>${rule.description || '-'}</td>
                    <td>${trackingBadges.join(' ')}</td>
                    <td>
                        <span class="badge-count">${rule.employee_count}</span>
                        <button class="btn btn-sm btn-link" onclick="viewEmployees(${rule.id}, '${rule.rule_name}')">
                            View
                        </button>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                Actions
                            </button>
                            <div class="dropdown-menu">
                                ${!isDefault ? `<a class="dropdown-item" href="#" onclick="editRule(${rule.id})">Edit</a>` : ''}
                                <a class="dropdown-item" href="#" onclick="assignEmployeesToRule(${rule.id}, '${rule.rule_name}')">Assign Employees</a>
                                ${!isDefault ? `<a class="dropdown-item text-danger" href="#" onclick="deleteRule(${rule.id}, '${rule.rule_name}')">Delete</a>` : ''}
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function createRule() {
        const formData = {
            ruleName: $('#addRuleForm input[name="ruleName"]').val(),
            description: $('#addRuleForm textarea[name="description"]').val(),
            trackApplications: $('#addRuleForm input[name="trackApplications"]').is(':checked') ? 1 : 0,
            trackWebsites: $('#addRuleForm input[name="trackWebsites"]').is(':checked') ? 1 : 0,
            trackKeystrokes: $('#addRuleForm input[name="trackKeystrokes"]').is(':checked') ? 1 : 0,
            trackScreenshots: $('#addRuleForm input[name="trackScreenshots"]').is(':checked') ? 1 : 0,
            trackMouseClicks: $('#addRuleForm input[name="trackMouseClicks"]').is(':checked') ? 1 : 0,
        };

        $.ajax({
            url: '/' + userType + '/monitoring-rules/create',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.code === 201) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Success', 'Rule created successfully', 'success');
                    }
                    $('#addRuleModal').modal('hide');
                    $('#addRuleForm')[0].reset();
                    loadRules();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to create rule';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', message, 'error');
                } else {
                    alert('Error: ' + message);
                }
            }
        });
    }

    function editRule(ruleId) {
        $.ajax({
            url: '/' + userType + '/monitoring-rules/get',
            method: 'GET',
            data: { skip: 0, limit: 1000 },
            success: function(response) {
                const rule = response.data.rules.find(r => r.id === ruleId);
                if (rule) {
                    $('#editRuleId').val(rule.id);
                    $('#editRuleName').val(rule.rule_name);
                    $('#editDescription').val(rule.description);
                    $('#editTrackApplications').prop('checked', rule.track_applications == 1);
                    $('#editTrackWebsites').prop('checked', rule.track_websites == 1);
                    $('#editTrackKeystrokes').prop('checked', rule.track_keystrokes == 1);
                    $('#editTrackScreenshots').prop('checked', rule.track_screenshots == 1);
                    $('#editTrackMouseClicks').prop('checked', rule.track_mouse_clicks == 1);
                    $('#editRuleModal').modal('show');
                }
            }
        });
    }

    function updateRule() {
        const formData = {
            id: $('#editRuleId').val(),
            ruleName: $('#editRuleName').val(),
            description: $('#editDescription').val(),
            trackApplications: $('#editTrackApplications').is(':checked') ? 1 : 0,
            trackWebsites: $('#editTrackWebsites').is(':checked') ? 1 : 0,
            trackKeystrokes: $('#editTrackKeystrokes').is(':checked') ? 1 : 0,
            trackScreenshots: $('#editTrackScreenshots').is(':checked') ? 1 : 0,
            trackMouseClicks: $('#editTrackMouseClicks').is(':checked') ? 1 : 0,
        };

        $.ajax({
            url: '/' + userType + '/monitoring-rules/update',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.code === 200) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Success', 'Rule updated successfully', 'success');
                    }
                    $('#editRuleModal').modal('hide');
                    loadRules();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to update rule';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', message, 'error');
                } else {
                    alert('Error: ' + message);
                }
            }
        });
    }

    function deleteRule(ruleId, ruleName) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: `Delete rule "${ruleName}"? Employees in this rule must be reassigned first.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/' + userType + '/monitoring-rules/delete',
                        method: 'POST',
                        data: { id: ruleId },
                        success: function(response) {
                            if (response.code === 200) {
                                Swal.fire('Deleted!', 'Rule has been deleted.', 'success');
                                loadRules();
                            }
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON?.message || 'Failed to delete rule';
                            Swal.fire('Error', message, 'error');
                        }
                    });
                }
            });
        } else if (confirm(`Delete rule "${ruleName}"?`)) {
            $.ajax({
                url: '/' + userType + '/monitoring-rules/delete',
                method: 'POST',
                data: { id: ruleId },
                success: function(response) {
                    if (response.code === 200) {
                        alert('Rule deleted successfully');
                        loadRules();
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Failed to delete rule';
                    alert('Error: ' + message);
                }
            });
        }
    }

    function assignEmployeesToRule(ruleId, ruleName) {
        $('#assignRuleId').val(ruleId);
        $('#assignRuleName').text(ruleName);
        $('#assignEmployeesModal').modal('show');
    }

    function loadAllEmployees() {
        $.ajax({
            url: '/' + userType + '/monitoring-rules/all-employees',
            method: 'GET',
            success: function(response) {
                const select = $('#employeeSelect');
                select.empty();
                if (response.employees && response.employees.length > 0) {
                    response.employees.forEach(emp => {
                        select.append(`<option value="${emp.id}">${emp.first_name} ${emp.last_name} (${emp.email})</option>`);
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading employees:', xhr);
            }
        });
    }

    function assignEmployees() {
        const ruleId = $('#assignRuleId').val();
        const employeeIds = $('#employeeSelect').val();

        if (!employeeIds || employeeIds.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'Please select at least one employee', 'error');
            } else {
                alert('Please select at least one employee');
            }
            return;
        }

        $.ajax({
            url: '/' + userType + '/monitoring-rules/assign',
            method: 'POST',
            data: {
                ruleId: ruleId,
                employeeIds: employeeIds
            },
            success: function(response) {
                if (response.code === 200) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Success', response.message, 'success');
                    }
                    $('#assignEmployeesModal').modal('hide');
                    loadRules();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to assign employees';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', message, 'error');
                } else {
                    alert('Error: ' + message);
                }
            }
        });
    }

    function viewEmployees(ruleId, ruleName) {
        $('#viewRuleName').text(ruleName);

        $.ajax({
            url: '/' + userType + '/monitoring-rules/employees',
            method: 'GET',
            data: { ruleId: ruleId, skip: 0, limit: 100 },
            success: function(response) {
                const tbody = $('#employeesTableBody');
                tbody.empty();

                if (response.code === 200 && response.data.employees.length > 0) {
                    response.data.employees.forEach(emp => {
                        const assignedAt = new Date(emp.assigned_at).toLocaleString();
                        tbody.append(`
                            <tr>
                                <td>${emp.first_name} ${emp.last_name}</td>
                                <td>${emp.email}</td>
                                <td>${emp.department_name || '-'}</td>
                                <td>${assignedAt}</td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center">No employees assigned</td></tr>');
                }

                $('#viewEmployeesModal').modal('show');
            },
            error: function(xhr) {
                console.error('Error loading employees:', xhr);
            }
        });
    }
</script>
@endsection

