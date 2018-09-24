<!DOCTYPE html>
<html>
<head>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex,nofollow">
<title>植得艾管理后台登录</title>
<link href="{{ asset('lib/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('lib/admin-lte/css/AdminLTE.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/font-awesome.css') }}" rel="stylesheet">
<link href="{{ asset('lib/admin-lte/css/skins/_all-skins.min.css') }}" rel="stylesheet">
<link href="{{ elixir('css/admins/admin.css') }}" rel="stylesheet">
</head>
<body class="skin-blue">
	@include('include.serviceMessage')
	@include('include.message')
	<div class="wrapper" style="background: url('/images/admins/bg.jpg') no-repeat;background-size: 100% 100%;">
		<div class="loginbox">
			<form method='post' class="login-form">
				<div class="form-group text-center">
					<h2 style="color:#fff"><strong>植得艾后台管理</strong></h2>
				</div>
				<textarea id="pubkey" class="pubkey hidden">-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCX9KeF+LPmJL5S4krtnDHqWG3xudzkeWDvjLHkXGECKIA66u5Zg2n1RiPdccZnW/4SNp7gpnjW4noFuDcLrYfQkppuWkIW324jqUHH2tclMMr2eAOq0LLFKSFn1Hs97Bf/sWoklDKwt+JRgtFhMRiENspM/c9dYtjSe5F7kq9JKwIDAQAB-----END PUBLIC KEY-----</textarea>
				<div class="input-div">
					<div class="form-group input-group input-group-lg">
						<span class="input-group-addon" style="border-radius: 6px;border-top-right-radius: 0;border-bottom-right-radius: 0;"><i class="fa fa-user fa-lg" style="width:30px"></i></span>
						<input type="text" class="form-control" name="name" autocomplete="off" placeholder="用户名"/>
					</div>
				</div>
				<div class="input-div">
					<div class="form-group input-group input-group-lg">
						<span class="input-group-addon" style="border-radius: 6px;border-top-right-radius: 0;border-bottom-right-radius: 0;"><i class="fa fa-lock fa-lg" style="width:30px"></i></span>
						<input type="password" class="form-control" name="password" autocomplete="off" placeholder="密码"/>
					</div>
				</div>
				<div class="input-div">
					<div class="form-group input-group input-group-lg">
						<span class="input-group-addon" style="border-radius: 6px;border-top-right-radius: 0;border-bottom-right-radius: 0;"><i class="fa fa-font fa-lg" style="width:30px"></i></span>
						<input type="text" class="form-control" name="captcha" autocomplete="off" placeholder="验证码"/>
						<span class="input-group-addon no-padding" style="border-radius: 6px;border-top-left-radius: 0;border-bottom-left-radius: 0;"><img onclick="changeCaptcha()" src="{{ $captcha }}" style="width: 110px;height: 44px;border-radius: 6px;" id="captchaImg" /></span>
					</div>
				</div>
				<div class="form-group"><button type="submit" class="btn btn-lg btn-primary btn-block login-submit"><strong>登 录</strong></button></div>
			</form>
		</div>
	</div>
	@include('include.footer')
</body>
<script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('lib/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('lib/jsencrypt/jsencrypt.min.js') }}"></script>
<script src="{{ elixir('js/zda.js') }}"></script>
<script src="{{ elixir('js/admins/login.js') }}"></script>
</html>
