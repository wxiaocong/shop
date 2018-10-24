@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="/admin/home/2">用户</a>
		</li>
		<li>
			<a href="/admin/user">用户管理</a>
		</li>
		<li class="active">用户明细</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab1" data-toggle="tab">基本信息</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab1">
				<div class="row">
					<div class="col-md-6">
						<div class="box box-success box-solid">
							<div class="box-header">
								个人信息
							</div>
							<div class="box-body">
								<table class="table form-table">
									<colgroup>
										<col width="120px" />
										<col />
									</colgroup>
									<tbody>
										<tr>
											<th>状态</th><td>{{ translateStatus('user.state', $user->state) }}</td>
										</tr>
										<tr>
											<th>头像:</th><td><img src="{{ $user->headimgurl }}" style="height:90px; width: 90px;"/></td>
										</tr>
										<tr>
											<th>昵称:</th><td>{{ $user->nickname }}</td>
										</tr>
										<tr>
											<th>手机号:</th><td>{{ $user->mobile }}</td>
										</tr>
										<tr>
											<th>级别:</th>
											<td>
												<select id="userLevel">
													@foreach($userLevel as $key=>$val)
													<option value="{{$key}}" @if($key == $user->level) selected @endif>{{$val}}</option>
													@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<th>推荐人:</th><td>{{ $user->referee_nickname ?? '无' }}</td>
										</tr>
										<tr>
											<th>推荐级别:</th><td>{{ $userLevel[$user->referee_level] ?? '无' }}</td>
										</tr>
										<tr>
											<th>Email:</th><td>{{ $user->email }}</td>
										</tr>
										<tr>
											<th>性别:</th><td>@if ($user->sex == 1) 男 @elseif ($user->sex == 2) 女 @else 未知 @endif</td>
										</tr>
										<tr>
											<th>生日:</th><td>{{ $user->birthday }}</td>
										</tr>
										<tr>
											<th>余额</th><td>{{ round($user->balance/100,2) }}</td>
										</tr>
										<tr>
											<th>创建时间</th><td>{{ $user->created_at }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@include('include.message')
<script type="text/javascript">
$('#userLevel').change(function(){
	var newLevel = $(this).val();
	$(this).cnConfirm('确认修改用户级别吗?', function(){
		window.loadding();
		$.ajax({
	        url:  '/admin/user/' + {{$user->id}} + '/changeLevel',
	        type: 'post',
	        data: {'level': newLevel},
	        dataType: 'json',
	        success: function(jsonObject) {
	        	window.unloadding();
	            if (jsonObject.code == 200) {
	            	window.tips(jsonObject.messages);
	                window.location.href = jsonObject.url;
	            } else {
	                showErrorNotice(jsonObject.messages);
	            }
	        },
	        error: function(xhr, type) {
	            ajaxResponseError(xhr, type);
	        }
	    });
	})
});
</script>
@include('admins.footer')
