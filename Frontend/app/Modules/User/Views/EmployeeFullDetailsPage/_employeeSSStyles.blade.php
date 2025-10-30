@include('User::Layout._modernStyles')
<style>
    /* Page Specific Styles Only */
    
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
