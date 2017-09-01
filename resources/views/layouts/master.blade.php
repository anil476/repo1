<!doctype html>
<html lang = "en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{'assets/img/logo.png'}}">
        <link rel="stylesheet" href="{{URL::to('assets/lib/bootstrap/css/bootstrap.min.css')}}"/>
        <link rel="stylesheet" href="{{URL::to('assets/lib/font-awesome/css/font-awesome.min.css')}}"/>
        <link rel="stylesheet" href="{{URL::to('assets/lib/simple-line-icons/css/simple-line-icons.css')}}"/>
         <link rel="stylesheet" href="{{URL::to('assets/lib/device-mockups/device-mockups.min.css')}}"/>
          <link rel="stylesheet" href="{{URL::to('assets/css/new-age.css')}}"/>
          <!-- Custom Fonts -->
          <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
          <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
          <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">

  @yield('styles')
</head>
<body id="page-top">

    @yield('content')

   <script type="text/javascript" src="{{ URL::to('assets/lib/jquery/jquery.min.js') }}"></script>
   <script type="text/javascript" src="{{ URL::to('assets/lib/bootstrap/js/bootstrap.min.js') }}"></script>
   <script type="text/javascript" src="{{ URL::to('assets/js/new-age.min.js') }}"></script>
   <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
   <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
 @yield('scripts')

</body>
</html>
