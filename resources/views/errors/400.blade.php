<!DOCTYPE html>
<html lang="zh" class="cnm">
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', '植得艾')</title>
        <link href="{{ asset('lib/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ elixir('css/admins/admin.css') }}" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="{{ asset('js/lib/html5shiv-printshiv.min.js') }}"></script>
        <script src="{{ asset('js/lib/respond.min.js') }}"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <div class="row text-center error-margin-top">
                <div class="col-xs-12 col-sm-offset-2 col-sm-3">
                    <h1 class="text-muted">400 错误</h1>
                    <img class="img-thumbnail" src="/images/error-1.png" alt="400 错误" style="border:0;">
                </div>
                <div class="col-sm-7 error-info">
                    <h2>{{ $exception->getMessage() }}</h2>
                    <h4>
                        <a href="javascript:void();" onClick="javascript :history.back(-1);">返回上一页</a> |
                        <a href="{{ url('/admin/home') }}">首页</a>
                    </h4>
                </div>
            </div>
        </div>
    </body>
</html>
