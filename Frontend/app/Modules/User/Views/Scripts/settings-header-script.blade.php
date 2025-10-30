@yield('title')

<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="description" content=""/>
<meta name="keywords" content="admin,dashboard"/>
@if((new App\Modules\User\helper)->checkHost() )

<meta name="author" content="EmpMonitor"/>
@else
    <meta name="author" content=""/>
    @endif
<!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->


<link rel="icon" href="../assets/images/favicons/{{ md5($_SERVER['HTTP_HOST']) }}.png"/>

<!-- Styles -->
<link
        href="https://fonts.googleapis.com/css?family=Rubik"
        rel="stylesheet"
        />
<link
        href="../assets/plugins/bootstrap/css/bootstrap.min.css"
        rel="stylesheet"
        />
<link
        href="../assets/plugins/font-awesome/css/all.min.css"
        rel="stylesheet"
        />
<link href="../assets/plugins/icomoon/style.css" rel="stylesheet"/>
<link
        href="../assets/plugins/switchery/switchery.min.css"
        rel="stylesheet"
        />
{{--<link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet"/>--}}

<link href="../assets/plugins/select2/css/select2.min.css"
        rel="stylesheet"
        />
{{--<link--}}
{{--href="../assets/plugins/select2/css/select2.min.css"--}}
{{--rel="stylesheet"--}}
{{--/>--}}


<link href="../assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet" />

<link href="../assets/plugins/jquery-ui/jquery-ui.css" rel="stylesheet"/>

<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<!-- Theme Styles -->
<link href="../assets/css/concept.css" rel="stylesheet"/>
<link href="../assets/css/custom.css" rel="stylesheet"/>













