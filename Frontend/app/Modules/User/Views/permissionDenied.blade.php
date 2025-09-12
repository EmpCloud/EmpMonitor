@extends('User::Layout._layout')
@section('title')
    <title>Permission Denied</title>
@endsection

@section('extra-style-links')
{{-- <link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" /> --}}
<link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet" />
@endsection

@section('content')
<!-- Page Inner -->

<div class="page-inner no-page-title">
  <div id="main-wrapper">
      <div class="content-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style-1">
              <li class="breadcrumb-item active">     Goto<a href="employee-details" style="color: #0686d8;font-weight: 500;">
           Home</a></li>
              
            </ol>
        </nav>
          <div>
              <h1 style="color: red" class="page-title">Permission Denied, you don't have access to this page </h1>
          </div>

          
      </div>
  </div>

</div>





@endsection

