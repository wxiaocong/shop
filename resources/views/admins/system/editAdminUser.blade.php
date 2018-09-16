@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="{{ url('/admin/home/6') }}">系统</a>
		</li>
		<li>
			<a href="#">权限管理</a>
		</li>
		<li class="active">编辑管理员</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form action="{{ url('admin/adminUser') }}" class="edit-admin-user-form">
		<textarea id="pubkey" class="pubkey hidden">-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCX9KeF+LPmJL5S4krtnDHqWG3xudzkeWDvjLHkXGECKIA66u5Zg2n1RiPdccZnW/4SNp7gpnjW4noFuDcLrYfQkppuWkIW324jqUHH2tclMMr2eAOq0LLFKSFn1Hs97Bf/sWoklDKwt+JRgtFhMRiENspM/c9dYtjSe5F7kq9JKwIDAQAB-----END PUBLIC KEY-----</textarea>
		<input class="admin-user-id" name="id" type="hidden" value="{{ isset($user) ? $user->id : "" }}" />
		<table class="table form-table">
			<colgroup>
				<col width="130px" />
				<col />
			</colgroup>
			<tr>
				<th>用户名：</th>
				<td><input type="text" class="form-control" name="name" value="{{ isset($user) ? $user->admin_name : "" }}"/><p class="help-block">* 管理员登录后台的用户名，请填写英文字母，数字或下划线，在4-20个字符之间</p></td>
			</tr>
			<tr>
				<th>密码：</th>
				<td>
				@if (isset($user))
					<input type="password" id="password" class="form-control" name="password" placeholder="编辑时，如不变更密码，请勿填写密码" /><p class="help-block">* 编辑时，如不变更密码，请勿填写密码</p>
				@else
					<input type="password" id="password" class="form-control" name="password"/><p class="help-block">* 管理员登录后台的密码，请填写英文字母，数字或下划线，在6-32个字符之间</p>
				@endif
				</td>
			</tr>
			<tr>
				<th>手机号：</th>
				<td><input type="text" class="form-control" name="phone" value="{{ isset($user) ? $user->phone : "" }}"/></td>
			</tr>
			<tr>
				<th>QQ：</th>
				<td><input type="text" class="form-control" name="qq" value="{{ isset($user) ? $user->qq : "" }}"/></td>
			</tr>
			<tr>
				<th>微信：</th>
				<td><input type="text" class="form-control" name="wechat" value="{{ isset($user) ? $user->we_chat : "" }}"/></td>
			</tr>
			<tr>
				<th>Email：</th>
				<td><input type="text" class="form-control" name="email" value="{{ isset($user) ? $user->email : "" }}"/></td>
			</tr>
			<tr>
				<th>关联角色：</th>
				<td>
					@if (isset($roles) && count($roles) > 0)
					@foreach ($roles as $role)
						<div style="width:180px;float:left;">
							<label class="checkbox-inline">
								<input type='checkbox' value="{{ $role->id }}" name="roleId[]" @if (isset($existRoleIds) && count($existRoleIds) > 0 && in_array($role->id, $existRoleIds)) checked @endif /> {{ $role->name }}
							</label>
						</div>
					@endforeach
					@endif
				</td>
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
