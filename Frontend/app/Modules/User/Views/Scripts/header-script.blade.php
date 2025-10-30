
@yield('title')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="keywords" content="admin,dashboard">
@if((new App\Modules\User\helper)->checkHost() )
<meta name="author" content="EmpMonitor">
@else
    <meta name="author" content="">
@endif
    <link rel="icon" href="../assets/images/favicons/{{ md5($_SERVER['HTTP_HOST']) }}.png"/>

<!-- Styles -->
<link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet">
<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/plugins/font-awesome/css/all.min.css" rel="stylesheet">
<link href="assets/plugins/icomoon/style.css" rel="stylesheet">
<link href="assets/plugins/switchery/switchery.min.css" rel="stylesheet">

<link href="../assets/plugins/dropify-master/css/dropify.min.css" rel="stylesheet"/>
{{--<link href="../assets/plugins/dropify-master/css/demo.css" rel="stylesheet"/>--}}
{{--{{for sweetalert}}--}}

{{--<link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.all.js" rel="stylesheet">--}}
{{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">--}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<link
        href="/assets/plugins/select2/css/select2.min.css"
        rel="stylesheet"
        />
<!-- Theme Styles -->
{{--<link href="assets/css/concept.css" rel="stylesheet">--}}
<link href="assets/css/custom.css" rel="stylesheet">
{{--For searching the list--}}
{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>--}}



<script src="../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
<script src="../assets/plugins/bootstrap/popper.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../assets/js/concept.min.js"></script>



{{--<script src="../assets/plugins/bootstrap/popper.min.js"></script>--}}


<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<Scripts src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></Scripts>
<Scripts src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></Scripts>




