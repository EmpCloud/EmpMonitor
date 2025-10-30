<style>
    /* Modern Card Styling */
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0 !important;
        padding: 1rem 1.5rem;
        border: none;
        font-weight: 600;
    }
    
    /* Modern Button Styling */
    .btn {
        border-radius: 8px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn-primary {
        box-shadow: 0 2px 8px rgba(76, 139, 245, 0.25);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 139, 245, 0.35);
    }
    
    .btn-success {
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.25);
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.35);
    }
    
    .btn-danger {
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.25);
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.35);
    }
    
    .btn-info {
        box-shadow: 0 2px 8px rgba(23, 162, 184, 0.25);
    }
    
    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.35);
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
    }
    
    .btn-sm {
        padding: 0.35rem 0.85rem;
        font-size: 13px;
    }
    
    .btn-link:hover {
        transform: translateX(3px);
    }
    
    /* Modern Table Styling */
    .table {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table-bordered {
        border: 1px solid #e9ecef;
    }
    
    .table thead tr,
    .table-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .table thead th,
    .table-primary th {
        border: none;
        color: white !important;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }
    
    .table thead th a,
    .table thead th label,
    .table-primary th a,
    .table-primary th label {
        color: white !important;
        text-decoration: none;
    }
    
    .table thead th a:hover,
    .table-primary th a:hover {
        color: rgba(255, 255, 255, 0.9) !important;
        text-decoration: none;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.001);
    }
    
    .table tbody td {
        padding: 0.85rem 0.75rem;
        vertical-align: middle;
        border-color: #f0f0f0;
        font-size: 13px;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    /* Action Icons */
    .table tbody td i {
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 5px;
        border-radius: 4px;
    }
    
    .table tbody td i:hover {
        transform: scale(1.2);
    }
    
    .fa-edit:hover {
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .fa-trash-alt:hover {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    /* Modern Form Elements */
    .form-control {
        border-radius: 8px;
        border: 1.5px solid #e0e6ed;
        padding: 0.5rem 1rem;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #4c8bf5;
        box-shadow: 0 0 0 0.2rem rgba(76, 139, 245, 0.15);
    }
    
    .form-control::placeholder {
        color: #a0aec0;
    }
    
    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        padding-right: 2.5rem;
    }
    
    textarea.form-control {
        resize: vertical;
    }
    
    .input-group .form-control {
        border-radius: 8px 0 0 8px;
    }
    
    .input-group-append .btn {
        border-radius: 0 8px 8px 0;
    }
    
    /* Modern Modal Styling */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px 12px 0 0;
    }
    
    .modal-title {
        font-weight: 600;
        font-size: 18px;
        color: white;
    }
    
    .modal-header .close {
        color: white;
        opacity: 0.8;
        text-shadow: none;
    }
    
    .modal-header .close:hover {
        opacity: 1;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 1rem 1.5rem;
    }
    
    .modal-footer button[type=button].btn-secondary {
        color: #fff;
        background-color: #8c86ff;
        border-color: #8a87ff;
        box-shadow: 0px 4px 5px #a7a4eb;
    }
    
    .modal-footer button[type=button].btn-secondary:hover {
        color: #fff;
        background-color: #7771eb !important;
        border-color: #7771eb !important;
        transform: translateY(-2px);
    }
    
    /* Page Header */
    .page-title {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 1rem;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        font-size: 18px;
    }
    
    /* Form Labels */
    label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    /* Checkbox Styling */
    .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #4c8bf5;
        border-color: #4c8bf5;
    }
    
    .custom-checkbox .custom-control-label::before {
        border-radius: 4px;
    }
    
    /* Pagination */
    .pagination {
        border-radius: 8px;
        overflow: hidden;
    }
    

    
    .pagination a:hover {
        background-color: #4c8bf5;
        color: white;
        /* transform: translateY(-2px); */
    }
    
    /* Alert Styling */
    .alert {
        border-radius: 8px;
        border: none;
        padding: 1rem 1.25rem;
    }
    
    .alert-danger {
        background-color: #fee;
        color: #c33;
    }
    
    /* Badge Styling */
    .badge {
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 12px;
    }
    
    /* Dropdown Menu */
    .dropdown {
        position: relative;
    }
    
    .dropdown-menu {
        border-radius: 8px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1050;
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        border-radius: 4px;
        margin: 2px 4px;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(4px);
    }
    
    /* Date Range Picker */
    #reportranges,
    #dateRange,
    .daterangepicker-input {
        border-radius: 8px;
        border: 1.5px solid #e0e6ed;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        background-color: white;
    }
    
    #reportranges:hover,
    #dateRange:hover {
        border-color: #4c8bf5;
        box-shadow: 0 2px 8px rgba(76, 139, 245, 0.15);
    }
    
    /* Modern Scrollbar */
    ::-webkit-scrollbar {
        height: 8px !important;
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #5568d3 0%, #63398d 100%);
    }
    
    /* DataTables Modern Styling */
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1.5px solid #e0e6ed;
        padding: 0.375rem 2rem 0.375rem 0.75rem;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1.5px solid #e0e6ed;
        padding: 0.375rem 0.75rem;
    }
    
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #4c8bf5;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(76, 139, 245, 0.15);
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
        margin: 0 2px;
        transition: all 0.2s ease;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        border-color: transparent;
        transform: translateY(-2px);
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        border-color: transparent;
    }
</style>

