@include('User::Layout._modernStyles')
<style>
    /* Page Specific Styles Only */
    
    /* Fix intlTelInput styling and padding */
    .iti {
        width: 100%;
        display: block;
    }
    
    .iti--allow-dropdown .iti__flag-container, .iti--separate-dial-code .iti__flag-container {
        right: auto;
        left: 16px;
    }
    
    .iti__flag-container {
        padding: 0;
    }
    
    .iti__selected-flag {
        padding: 0 8px 0 8px;
        display: flex;
        align-items: center;
    }
    
    .iti input, .iti input[type=text], .iti input[type=tel] {
        width: 100%;
        padding: 10px 10px 10px 80px !important;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
        height: 38px;
        box-sizing: border-box;
    }
    
    .iti input:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    #error-msg, #error-msgs {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        padding-left: 2px;
    }
    
    #valid-msg, #valid-msgs {
        color: #28a745;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        padding-left: 2px;
    }
    
    /* Nav Tabs */
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        background-color: #f8f9fa;
    }
    
    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
    
    /* Original Styles with Modern Enhancements */
    .fancybox-button {
        display: block !important;
    }

    div.scrollmenu {
        overflow: auto;
        white-space: nowrap;
        border-radius: 8px;
    }

    div.scrollmenu a {
        display: inline-block;
        color: black;
        text-align: center;
        padding: 8px 12px;
        text-decoration: none;
        border-radius: 6px;
        margin: 2px;
        transition: all 0.3s ease;
    }

    div.scrollmenu a:hover {
        background-color: #b33cff;
        color: white;
        transform: translateY(-2px);
    }

    li {
        list-style-type: none;
    }

    div .modal-body li {
        list-style-type: square;
    }

    .imageSize {
        width: 250px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .upload-button {
        font-size: 1.2em;
        transition: all 0.3s ease;
    }

    .upload-button:hover {
        transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
        color: #667eea;
        transform: scale(1.1);
    }

    .fancybox-title.fancybox-title-inside-wrap {
        text-align: center;
        font-size: inherit;
        font-weight: bold;
    }

    .fa-2x {
        font-size: 23px !important;
    }

    .info.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 8px !important;
        padding: 0.75rem 1rem;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    
    /* Statistics Cards */
    .stat-card {
        border-radius: 12px;
        padding: 1.5rem;
        background: white;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }
    
    /* Progress Bar */
    .progress {
        border-radius: 8px;
        height: 10px;
    }
    
    .progress-bar {
        border-radius: 8px;
    }
</style>
