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
								代理商信息
							</div>
							<div class="box-body">
								<table class="table form-table">
									<colgroup>
										<col width="120px" />
										<col />
									</colgroup>
									<tbody>
										<tr>
											<th>订单号:</th><td>{{ $agent->order_sn }}</td>
										</tr>
										<tr>
											<th>用户昵称:</th><td>{{ $agent->user->nickname }}</td>
										</tr>
										<tr>
											<th>合作类型:</th><td>{{ $agentLevel[$agent->level]['type_name'] }}</td>
										</tr>
										<tr>
											<th>订单金额:</th><td>{{sprintf("%.2f", $agent->payment/100)}}</td>
										</tr>
										<tr>
											<th>实付金额:</th><td>{{sprintf("%.2f", $agent->real_pay/100)}}</td>
										</tr>
										<tr>
											<th>付款时间:</th><td>{{$agent->pay_time}}</td>
										</tr>
										<tr>
											<th>库存:{{$agent->goodsNum}}</th><td>@if ($agent->state == 3) <input class="col-lg-8" type="number" id="stock" placeholder="请输入增加库存数" value="" /><button type="button" id="updateStock" class="btn btn-primary pull-right">更新</button> @endif</td>
										</tr>
										<tr>
											<th>代理商姓名:</th><td>{{ $agent->agent_name }}</td>
										</tr>
										<tr>
											<th>身份证:</th><td>{{ $agent->idCard }}</td>
										</tr>
										<tr>
											<th>代理商手机:</th><td>{{ $agent->mobile }}</td>
										</tr>
										<tr>
											<th>地址:</th><td>
											@if (isset($agent->provinceObj))
												{{ $agent->provinceObj->area_name }}&nbsp;
											@endif
											@if (isset($agent->cityObj))
												{{ $agent->cityObj->area_name }}&nbsp;
											@endif
											@if (isset($agent->areaObj))
												{{ $agent->areaObj->area_name }}&nbsp;
											@endif
											{{ $agent->address }}
											</td>
										</tr>
										<tr>
											<th>状态:</th><td>
												{{ $agentState[$agent->state] }}
											</td>
										</tr>
										<tr>
											<th>备注:</th><td>{{$agent->remark}}</td>
										</tr>
										@if ($agent->state == 2)
										<tr>
											<td colspan="2">
												<button type="button" class="btn btn-primary pass-button pull-right" style="margin-top:5px;margin-left:5px;">通过</button>
												<button type="button" class="btn btn-warning refuse-button pull-right" style="margin-top:5px;">拒绝</button>
											</td>
										</tr>
										@endif
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
//审批-通过
$('.pass-button').click(function() {
    $(this).cnConfirm('确认审核通过吗?', function(){
        merchantAudit('pass');
    })
});
//审批-拒绝
$('.refuse-button').click(function() {
    $(this).cnConfirm('确认拒绝申请吗?', function(){
        merchantAudit('refuse');
    })
});
function merchantAudit(type) {
    window.loadding();
	$.ajax({
        url:  '/admin/agent/' + {{$agent->id}} + '/audit',
        type: 'post',
        data: {'type': type},
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
}
//更新库存
$('#updateStock').click(function(){
	var stock = $('#stock').val();
    window.loadding();
	$.ajax({
        url:  '/admin/agent/increStock',
        type: 'post',
        data: {'id':"{{$agent->id}}",'stock': stock},
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
});
</script>
@include('admins.footer')
