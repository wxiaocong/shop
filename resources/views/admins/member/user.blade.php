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
											<th>电话:</th><td>{{ $user->phone }}</td>
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
											<th>联系地址</th><td>
											@if (isset($user->provinceObj))
												{{ $user->provinceObj->area_name }}&nbsp;
											@endif
											@if (isset($user->cityObj))
												{{ $user->cityObj->area_name }}&nbsp;
											@endif
											@if (isset($user->areaObj))
												{{ $user->areaObj->area_name }}&nbsp;
											@endif
											{{ $user->address }}
											</td>
										</tr>
										<tr>
											<th>邮政编码:</th><td>{{ $user->zip }}</td>
										</tr>
										<tr>
											<th>余额</th><td>{{ round($user->balance/100,2) }}</td>
										</tr>
										<tr>
											<th>是否绑定微信</th><td>@if (isset($user->wechatUser)) 是 @else 否 @endif</td>
										</tr>
										<tr>
											<th>是否商家</th><td>
												@if ($user->business_audit_state == config('statuses.user.businessAuditState.pass.code'))
													是
												@else
													否
												@endif
											</td>
										</tr>
										<tr>
											<th>登录总次数</th><td>{{ $user->total_login }}</td>
										</tr>
										<tr>
											<th>创建时间</th><td>{{ $user->created_at }}</td>
										</tr>
										<tr>
											<th>最后登录时间</th><td>{{ $user->last_time }}</td>
										</tr>
										<tr>
											<th>最后登录IP</th><td>{{ $user->last_ip }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					@if ($user->business_audit_state != config('statuses.user.businessAuditState.noApply.code'))
					<div class="col-md-6">
						<div class="box box-success box-solid">
							<div class="box-header">
								商家信息
							</div>
							<div class="box-body">
								<table class="table form-table">
									<colgroup>
										<col width="120px" />
										<col />
									</colgroup>
									<tbody>
										<tr>
											<th>门头照片:</th><td><img src="{{ $user->doorhead_photo }}" style="height:200px;width:300px;"/></td>
										</tr>
										<tr>
											<th>营业执照:</th><td><img src="{{ $user->business_license }}" style="height:200px;width:300px;"/></td>
										</tr>
										<tr>
											<th>公司名称:</th><td>{{ $user->company_name }}</td>
										</tr>
										<tr>
											<th>店铺工位:</th><td>{{ $user->shop_site }}个</td>
										</tr>
										<tr>
											<th>公司地址:</th><td>
											@if (isset($user->companyProvinceObj))
												{{ $user->companyProvinceObj->area_name }}&nbsp;
											@endif
											@if (isset($user->companyCityObj))
												{{ $user->companyCityObj->area_name }}&nbsp;
											@endif
											@if (isset($user->companyAreaObj))
												{{ $user->companyAreaObj->area_name }}&nbsp;
											@endif
											{{ $user->company_address }}
											</td>
										</tr>
										<tr>
											<th>状态:</th><td>
												{{ translateStatus('user.businessAuditState', $user->business_audit_state) }}
											</td>
										</tr>
										<tr>
											<th>申请时间:</th><td>{{ $user->business_apply_time }}</td>
										</tr>
										@if ($user->business_audit_state == config('statuses.user.businessAuditState.apply.code'))
											<tr>
												<th>审批备注:</th><td>
													<input type="hidden" class="user-id" name="id" value="{{ $user->id }}"/>
													<textarea class="form-control" rows="2" name="businessAuditRemark" placeholder="审批备注,必填"></textarea>
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<button type="button" class="btn btn-primary pass-button pull-right" style="margin-top:5px;margin-left:5px;">通过</button>
													<button type="button" class="btn btn-warning refuse-button pull-right" style="margin-top:5px;">拒绝</button>
												</td>
											</tr>
										@else
											<tr>
												<th>审核时间:</th><td>{{ $user->business_audit_time }}</td>
											</tr>
											<tr>
												<th>审批备注:</th><td>{{ $user->business_audit_remark }}</td>
											</tr>
										@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@include('include.message')
<script type="text/javascript">
//审批-通过
$('.pass-button').click(function() {
	merchantAudit('pass');
});
//审批-拒绝
$('.refuse-button').click(function() {
	merchantAudit('refuse');
});
function merchantAudit(type) {
	var remark = $('textarea[name="businessAuditRemark"]').val();
	if (remark == null || remark == '' || remark == 'undefined') {
		$(this).cnAlert(new Array('审批备注不能为空'), 5);
	} else {
		$.ajax({
	        url:  '/admin/user/' + $('.user-id').val() + '/merchantAudit',
	        type: 'post',
	        data: {'type': type, 'remark': remark},
	        dataType: 'json',
	        success: function(jsonObject) {
	            if (jsonObject.code == 200) {
	                window.location.href = jsonObject.url;
	            } else {
	                showErrorNotice(jsonObject.messages);
	            }
	        },
	        error: function(xhr, type) {
	            ajaxResponseError(xhr, type);
	        }
	    });
	}
}
</script>
@include('admins.footer')
