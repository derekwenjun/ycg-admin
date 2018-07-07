<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>洋仓物流管理后台</title>

    <!-- Fonts -->
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">-->
    <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">-->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    <script src="{{ asset('js/jquery.validate.js') }}"></script>

    <style>
        body {
            font-family: 'Lato';
        }

        .navbar-btn {
            margin: 8px 15px;
        }

        .navbar .divider-vertical {
            height: 30px;
            margin: 10px 0;
            border-right: 1px solid #ffffff;
            border-left: 1px solid #f2f2f2;
        }

        .navbar-inverse .divider-vertical {
            border-right-color: #222222;
            border-left-color: #111111;
        }

        @media (max-width: 767px) {
            .navbar-collapse .nav > .divider-vertical {
                display: none;
            }
        }

        .fa-btn, .fa-title {
            margin-right: 6px;
        }

    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img alt="YCG" src="{{ asset('image/logo.png') }}" height="30">
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                @if (!isset($nav))
                    <!-- {{ $nav = 'home' }} -->
                @endif
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li class="{{ $nav == 'home' ? 'active' : '' }}"><a href="{{ url('/') }}">首页</a></li>

                    <li class="dropdown {{ $nav == 'order' ? 'active' : '' }}">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">包裹管理 <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="{{ route('order.index') }}">所有包裹</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="{{ route('order.pickup') }}"><i class="fa fa-arrow-circle-down fa-title" aria-hidden="true"></i>包裹入库</a></li>
                      </ul>
                    </li>
                    <li class="{{ $nav == 'batch' ? 'active' : '' }}"><a href="{{ route('batch.index') }}">批次管理</a></li>
                    <li class="divider-vertical"></li>

                    <li class="{{ $nav == 'client' ? 'active' : '' }}"><a href="{{ route('client.index') }}">客户管理</a></li>
                    <li class="{{ $nav == 'user' ? 'active' : '' }}"><a href="{{ route('user.index') }}">网点管理</a></li>
                    <li class="divider-vertical"></li>
                    
                    <!-- <li class="{{ $nav == 'address' ? 'active' : '' }}"><a href="{{ url('address') }}">Addresses</a></li> -->
                    <li class="{{ $nav == 'product' ? 'active' : '' }}"><a href="{{ route('product.index') }}">商品管理</a></li>
                    <li class="{{ $nav == 'finance' ? 'active' : '' }}"><a href="{{ route('transcation.index') }}">财务管理</a></li>
                    <li class="{{ $nav == 'setting' ? 'active' : '' }}"><a href="{{ route('setting.index') }}">系统设置</a></li>
                    <li class="divider-vertical"></li>
                    <a style="margin:8px 0px 0px 8px;" class="btn btn-default" href="{{ route('setting.index') }}">包裹查询</a>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">登陆</a></li>
                        <li><a href="{{ url('/register') }}">注册</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-cog"></i>账户设置</a></li>
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-power-off"></i>退出登录</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="footer">
      <div class="container">
        <p class="text-center text-muted">Copyright @2017 YCG GROUP</p>
      </div>
    </footer>

</body>
</html>
