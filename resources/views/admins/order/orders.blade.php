@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="#">订单</a>
		</li>
		<li>
			<a href="#">订单管理</a>
		</li>
		<li class="active">订单列表</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<table class="table list-table">
		<caption>
			<form action="{{ url('/admin/order') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
				<input type="hidden" value="1" class="curPage" name="curPage">
				<select class="form-control" id="province" name="province">
					<option value="">请选择省</option>
					@foreach($province as $val)
					<option value="{{$val->id}}">{{$val->area_name}}</option>
					@endforeach
				</select>
				<select class="form-control" id="city" name="city">
					<option value="">请选择市</option>
				</select>
				<select class="form-control" id="area" name="area">
					<option value="">请选择区</option>
				</select>
				下单时间&nbsp;<input class="form-control datepicker" type="text" name="startPayDate" value="@if(isset($startPayDate) && $startPayDate != '') {{ $startPayDate }} @endif" placeholder="起始日期" style="width: 100px;" /> - <input class="form-control datepicker" type="text" name="endPayDate" value="@if(isset($endPayDate) && $endPayDate != '') {{ $endPayDate }} @endif" placeholder="结束日期" style="width: 100px;" />
				&nbsp;&nbsp;订单状态&nbsp;<select class="form-control" name="state">
					<option value="">选择订单状态</option>
					@foreach (config('statuses.order.state') as $stateList)
						<option value="{{ $stateList['code'] }}" @if(isset($state) && $state == $stateList['code']) selected @endif>{{ $stateList['text'] }}</option>
					@endforeach
				</select>
				&nbsp;&nbsp;<input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="订单编号/下单用户/收货人姓名手机" style="width: 250px;">
				<button class="btn btn-default" type="submit">
				    <i class="fa fa-search"></i>搜 索
				</button>
			</form>
		</caption>
		<thead>
			<tr>
				<th>订单编号</th>
				<th>下单用户</th>
				<th>下单时间</th>
				<th>订单状态</th>
				<th>收货人</th>
				<th>收货地区</th>
				<th>订单金额</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			@if (isset($page))
				@foreach ($page->data as $data)
				<tr>
					<td><a target="_blank" href="{{ url('/admin/order/' . $data->id) }}">{{ $data->order_sn }}</a></td>
					<td>{{ $data->user->nickname}}</td>
					<td>{{ $data->created_at }}</td>
					<td>{{ translateStatus('order.state', $data->state) }}</td>
					<td>{{ $data->receiver_name }}</td>
					<td>@if (isset($data->provinceObj))
                            {{ $data->provinceObj->area_name }}&nbsp;
                        @endif
                        @if (isset($data->cityObj))
                            {{ $data->cityObj->area_name }}&nbsp;
                        @endif
                        @if (isset($data->areaObj))
                            {{ $data->areaObj->area_name }}&nbsp;
                        @endif
                    </td>
					<td>{{ round($data->payment/100,2) }}</td>
					<td>
						<a target="_blank" href="{{ url('/admin/order/' . $data->id) }}"><i class='operator fa fa-file-text-o fa-lg' title="查看订单"></i></a>
					</td>
				</tr>
				@endforeach
				<tr>
					<td>总订单数：{{$totalStatic->order_num ?? 0}}</td>
					<td></td>
					<td>总商品数量：{{$totalStatic->goods_num ?? 0}}</td>
					<td></td>
					<td>总订单金额:{{$totalStatic->payment/100 ?? 0}}</td>
					<td></td>
					<td>总付款金额:{{$totalStatic->real_pay/100 ?? 0}}</td>
				</tr>
			@endif
		</tbody>
	</table>
</div>
@include('include.message')
@include('include.page')
<script src="{{ asset('lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ elixir('js/common/datetimePicker.js') }}"></script>
@include('admins.footer')
<script type="text/javascript">
$('#province').change(function(){
	$.ajax({
        url:'/admin/system/areas/ajaxGetArea/'+$(this).val(),
        type: 'get',
        dataType: 'json',
        success: function(res) {
        	$('#city').html("<option>请选择市</option>");
            var len = res.length;
            for (var i = 0; i < len; i++) {
            	$('#city').append("<option value='"+res[i].id+"'>"+res[i].area_name+"</option>");
            }
        }
    });
})
$('#city').change(function(){
	$.ajax({
        url:'/admin/system/areas/ajaxGetArea/'+$(this).val(),
        type: 'get',
        dataType: 'json',
        success: function(res) {
        	$('#area').html("<option>请选择地区</option>");
            var len = res.length;
            for (var i = 0; i < len; i++) {
            	$('#area').append("<option value='"+res[i].id+"'>"+res[i].area_name+"</option>");
            }
        }
    });
})
</script>