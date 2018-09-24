<!DOCTYPE html>
<html>

<head>
    <title>植得艾后台管理</title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="{{ asset('lib/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/admin-lte/css/AdminLTE.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/admin-lte/css/skins/_all-skins.min.css') }}" rel="stylesheet">
    <link href="{{ elixir('css/admins/admin.css') }}" rel="stylesheet">
    <link href="{{ elixir('js/common/artdialog/skins/aero.css') }}" rel="stylesheet">
    <link href="{{ asset('js/common/fileinput/css/fileinput.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/blueimp-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet">
    <link href="{{ asset('js/common/fileinput/themes/explorer/theme.css') }}" media="all" rel="stylesheet"/>
    <link href="{{ asset('lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('lib/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('lib/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('lib/admin-lte/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('lib/moment/moment.min.js') }}"></script>
    <script src="{{ asset('lib/moment/locale/moment-zh-cn.js') }}"></script>
    <script src="{{ asset('lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/common/artdialog/artDialog.js') }}"></script>
    <script src="{{ asset('js/common/artdialog/plugins/iframeTools.js') }}"></script>
</head>

<body class="skin-blue fixed sidebar-mini" style="height: auto; min-height: 100%;">
    <div class="wrapper" style="height: auto; min-height: 100%;">
        <header class="main-header">
            <a href="/admin/home" class="logo hidden-xs">
                <span class="logo-mini"><i class="home-icon fa fa-home fa-lg"></i>&nbsp;<b>植得艾</b></span>
                <span class="logo-lg"><i class="home-icon fa fa-home fa-lg"></i>&nbsp;<b>植得艾</b>后台管理</span>
            </a>
            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only"></span>
                </a>

                <!--top菜单 开始-->
                <div id="menu" class="navbar-custom-menu">
                    <ul class="nav navbar-nav" name="topMenu">
                        @foreach(Session::get('adminTopMenus') as $key => $menu)
                        @if ($menu['isShow'] == 1)
                            <li class="navbar-nav-li-{{ $menu['id'] }} @if(Session::get('currentTopMenuId') == $menu['id']) active @endif"><a hidefocus="true" href="{{url('/admin/home/' . $menu['id'])}}">{{ $menu['name'] }}</a></li>
                        @endif
                        @endforeach
                        <li>
                            <a href="#" data-toggle="control-sidebar" style="width:auto;"><i class="fa fa-user fa-lg"></i> <span style="margin-right:30px;">{{ Session::get('adminUser')->admin_name }}</span></a>
                        </li>
                    </ul>
                </div>
                <!--top菜单 结束-->
            </nav>
        </header>

        <!--左侧菜单 开始-->
        <aside id="admin_left" class="main-sidebar">
            <section class="sidebar" style="height: auto;">
                <ul class="sidebar-menu tree" data-widget="tree">
                    <li class="header">{{ Session::get('currentTopMenuName') }}模块菜单</li>
                    @foreach(Session::get('adminLeftMenus')[Session::get('currentTopMenuId')] as $menu)
                    @if ($menu['isShow'] == 1)
                        <li class="treeview sidebar-menu-li-{{ $menu['id'] }}">
                            <a href="#">
                                <i class="fa {{ $menu['icon'] == null ? 'fa-circle' : $menu['icon'] }}" name="ico" menu="{{ $menu['name'] }}"></i>
                                <span>{{ $menu['name'] }}</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu" name="leftMenu">
                                @foreach($menu['subMenus'] as $subMenu)
                                <li class="treeview-menu-li-{{ $subMenu['id'] }}"><a href="{{url($subMenu['url'])}}"><i class="fa fa-circle-o"></i>{{ $subMenu['name'] }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                    @endforeach
                </ul>
            </section>
        </aside>
        <!--左侧菜单 结束-->

        <!--右侧内容 开始-->
        <div id="admin_right" class="content-wrapper">
