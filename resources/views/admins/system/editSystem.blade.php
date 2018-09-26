@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="{{ url('/admin/home/6') }}">系统</a>
		</li>
		<li>
			<a href="#">后台首页</a>
		</li>
		<li class="active">编辑系统参数</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form action="{{ url('admin/adminUser') }}" class="edit-admin-user-form">
		<textarea id="pubkey" class="pubkey hidden">-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCX9KeF+LPmJL5S4krtnDHqWG3xudzkeWDvjLHkXGECKIA66u5Zg2n1RiPdccZnW/4SNp7gpnjW4noFuDcLrYfQkppuWkIW324jqUHH2tclMMr2eAOq0LLFKSFn1Hs97Bf/sWoklDKwt+JRgtFhMRiENspM/c9dYtjSe5F7kq9JKwIDAQAB-----END PUBLIC KEY-----</textarea>
		<input class="admin-user-id" name="id" type="hidden" value="{{ $param->id ?? '' }}" />
		<table class="table form-table">
			<tr>
				<th>参数名：</th>
				<td><input type="text" class="form-control" name="phone" value="{{ $param->name ?? '' }}"/></td>
			</tr>
			<tr>
				<th>参数值：</th>
				<td><input type="text" class="form-control" name="qq" value="{{ $param->val ?? '' }}"/></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<button class="btn btn-primary admin-user-submit" type="button">保存</button>
				</td>
			</tr>
		</table>
	</form>
</div>
@include('include.message')
<script src="{{ asset('lib/jsencrypt/jsencrypt.min.js') }}"></script>
<script src="{{ elixir('js/admins/adminUser.js') }}"></script>
@include('admins.footer')
