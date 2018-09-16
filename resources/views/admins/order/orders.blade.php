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
		<colgroup>
			<col />
			<col width="260px" />
			<col width="160px" />
			<col width="140px" />
			<col width="100px" />
			<col width="230px" />
			<col width="110px" />
			<col width="130px" />
			<col width="80px" />
		</colgroup>
		<caption>
			<form action="{{ url('/admin/order') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
				<input type="hidden" value="1" class="curPage" name="curPage">
				下单时间<input class="form-control datepicker" type="text" name="startPayDate" value="@if(isset($startPayDate) && $startPayDate != '') {{ $startPayDate }} @endif" placeholder="起始日期" style="width: 100px;" /> - <input class="form-control datepicker" type="text" name="endPayDate" value="@if(isset($endPayDate) && $endPayDate != '') {{ $endPayDate }} @endif" placeholder="结束日期" style="width: 100px;" />
				&nbsp;&nbsp;订单状态<select class="form-control" name="state">
					<option value="">选择订单状态</option>
					@foreach (config('statuses.order.state') as $stateList)
						<option value="{{ $stateList['code'] }}" @if(isset($state) && $state == $stateList['code']) selected @endif>{{ $stateList['text'] }}</option>
					@endforeach
				</select>
				&nbsp;&nbsp;发货状态<select class="form-control" name="deliverStatus">
					<option value="">选择发货状态</option>
					@foreach (config('statuses.order.deliverStatus') as $deliverStatusList)
						<option value="{{ $deliverStatusList['code'] }}" @if(isset($deliverStatus) && $deliverStatus != '' && $deliverStatus == $deliverStatusList['code']) selected @endif>{{ $deliverStatusList['text'] }}</option>
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
				<th>发货状态</th>
				<th>收货人</th>
				<th>支付方式</th>
				<th>订单金额</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			@if (isset($page))
				@foreach ($page->data as $data)
				<tr>
					<td><a target="_blank" href="{{ url('/admin/order/' . $data->id) }}">{{ $data->order_sn }}</a></td>
					<td>{{ '【' . $data->user->mobile . '】' . $data->user->nickname}}</td>
					<td>{{ $data->created_at }}</td>
					<td>{{ translateStatus('order.state', $data->state) }}</td>
					<td>{{ translateStatus('order.deliverStatus', $data->deliver_status) }}</td>
					<td>{{ '【' . $data->receiver_mobile . '】' . $data->receiver_name }}</td>
					<td>{{ translateStatus('order.payType', $data->pay_type) }}</td>
					<td>{{ round($data->payment/100,2) }}</td>
					<td>
						<a target="_blank" href="{{ url('/admin/order/' . $data->id) }}"><i class='operator fa fa-file-text-o fa-lg' title="查看订单"></i></a>
					</td>
				</tr>
				@endforeach
			@endif
		</tbody>
	</table>
</div>
@include('include.message')
@include('include.page')
<script src="{{ asset('lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ elixir('js/common/datetimePicker.js') }}"></script>
@include('admins.footer')
