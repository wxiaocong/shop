@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="/admin/home/3">订单</a>
		</li>
		<li>
			<a href="/admin/order">订单管理</a>
		</li>
		<li class="active">订单查看</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<div class="alert" style="background-color:#F5F5F5">
		<input type="hidden" class="order-id" value="{{ $order->id }}" />
		@if(in_array($order->state, array(config('statuses.order.state.waitDelivery.code'),config('statuses.order.state.waitGood.code')))
			&& in_array($order->deliver_status, array(config('statuses.order.deliverStatus.waitDelivery.code'),config('statuses.order.deliverStatus.partDelivery.code'))))
			<button type="button" class="btn btn-primary order-deliver-button">发货</button>
			<!-- <button type="button" class="btn btn-warning order-refundment-button">退单退款</button> -->
		@endif
		@if($order->state == config('statuses.order.state.waitPay.code'))
			<button type="button" class="btn btn-danger order-cancel-button">取消</button>
		@endif
	</div>
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab1" data-toggle="tab">基本信息</a></li>
			<li><a href="#tab_product" data-toggle="tab">商品信息</a></li>
			<li><a href="#tab2" data-toggle="tab">收退款记录</a></li>
			<li><a href="#tab3" data-toggle="tab">发货记录</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="tab1">
				<div class="row">
					<div class="col-md-6">
						<div class="box box-success box-solid">
							<div class="box-header">
								订单金额
							</div>
							<div class="box-body">
								<table class="table form-table">
									<colgroup>
										<col width="120px" />
										<col />
									</colgroup>
									<tbody>
										<tr>
											<th>商品金额：</th><td>￥{{ round($order->payment/100,2) }}</td>
										</tr>
										<tr>
											<th>配送费用：</th><td>￥{{ round($order->express_fee/100,2) }}</td>
										</tr>
										<tr>
											<th>订单总额：</th><td>￥{{ round($order->payment/100,2) }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="box box-success box-solid">
							<div class="box-header">
								买家信息
							</div>
							<div class="box-body">
								<table class="table form-table">
									<colgroup>
										<col width="120px" />
										<col />
									</colgroup>
									<tbody>
										<tr>
											<th>用户：</th><td>{{ $order->user->nickname }}</td>
										</tr>
										<tr>
											<th>电话：</th><td>{{ $order->user->mobile }}/{{ $order->user->phone }}</td>
										</tr>
										<tr>
											<th>Email：</th><td>{{ $order->user->email }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="box box-success box-solid">
							<div class="box-header">
								订单信息
							</div>
							<div class="box-body">
								<table class="table form-table">
									<colgroup>
										<col width="120px" />
										<col />
									</colgroup>

									<tbody>
										<tr>
											<th>订单编号：</th><td>{{ $order->order_sn }}</td>
										</tr>
										<tr>
											<th>支付方式：</th><td>{{ translateStatus('order.payType', $order->pay_type) }}</td>
										</tr>
										<tr>
											<th>下单时间：</th><td>{{ $order->created_at }}</td>
										</tr>
										<tr>
											<th>支付时间：</th><td>{{ $order->pay_time }}</td>
										</tr>
										<tr>
											<th>订单状态：</th><td class="red">{{ translateStatus('order.state', $order->state) }}</td>
										</tr>
										<tr>
											<th>发货状态：</th><td>{{ translateStatus('order.deliverStatus', $order->deliver_status) }}</td>
										</tr>
										<!--
										<tr>
											<th>用户留言：</th><td>{{ $order->message }}</td>
										</tr>
										<tr>
											<th>备注：</th><td>{{ $order->remark }}</td>
										</tr>
										-->
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="box box-success box-solid">
							<div class="box-header">
								收货人信息
							</div>
							<div class="box-body">
								<table class="table form-table">
									<colgroup>
										<col width="120px" />
										<col />
									</colgroup>

									<tbody>
										<tr>
											<th>发货时间：</th><td>{{ $order->deliver_time }}</td>
										</tr>
										<tr>
											<th>姓名：</th><td>{{ $order->receiver_name }}</td>
										</tr>
										<tr>
											<th>手机：</th><td>{{ $order->receiver_mobile }}</td>
										</tr>
										<tr>
											<th>地区：</th><td>{{ $order->receiver_area }}</td>
										</tr>
										<tr>
											<th>地址：</th><td>{{ $order->receiver_area }} {{ $order->receiver_address }}</td>
										</tr>
										<tr>
											<th>邮编：</th><td>{{ $order->receiver_zip }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tab_product">
				<table class="table list-table">
					<colgroup>
						<col />
						<col width="120px" />
						<col width="120px" />
						<col width="120px" />
						<col width="120px" />
						<col width="200px" />
					</colgroup>

					<thead>
						<tr>
							<th>商品名称</th>
							<th>商品原价</th>
							<th>实际价格</th>
							<th>商品数量</th>
							<th>小计</th>
							<th>发货状态</th>
						</tr>
					</thead>
					<tbody>
						@foreach($order->orderGoods as $orderGood)
						<tr>
							<td>
								<a href="{{ url('/admin/good/' . $orderGood->goods_id . '/edit') }}" target="_blank">{{ $orderGood->goods_name }}</a>
							</td>
							<td>￥{{ round($orderGood->prime_cost/100,2) }}</td>
							<td>￥{{ round($orderGood->price/100,2) }}</td>
							<td>{{ $orderGood->num }}</td>
							<td>￥{{ round($orderGood->total_price/100,2) }}</td>
							<td>
								{{ translateStatus('orderGood.state', $orderGood->state) }}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<div class="tab-pane" id="tab2">
				<div class="row">

					@if (count($orderPayLogs) > 0)
					<div class="col-md-6">
						<div class="box box-success box-solid">
							<div class="box-header">
								收款单据
							</div>
							<div class="box-body">
								<table class="table form-table">
									<colgroup>
										<col width="120px" />
										<col />
									</colgroup>

									<tbody>
									@if (isset($orderPayLogs) && count($orderPayLogs) > 0)
									@foreach ($orderPayLogs as $log)
										<tr>
											<th>支付金额：</th><td>{{ round($log->expense/100,2) }}</td>
										</tr>
										<tr>
											<th>支付方式：</th><td>微信支付</td>
										</tr>
										<tr>
											<th>商户单号：</th><td>{{ $order->out_trade_no }}</td>
										</tr>
										<tr>
											<th>交易单号：</th><td>{{ $order->transaction_id }}</td>
										</tr>
										<tr>
											<th>付款时间：</th><td>{{ $log->created_at }}</td>
										</tr>
										<tr>
											<th>付款备注：</th><td>{{ $log->remark }}</td>
										</tr>
									@endforeach
									@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>
					@endif

					@if (count($orderRefundLogs) > 0)
					<div class="col-md-6">
						<div class="box box-success box-solid">
							<div class="box-header">
								退款单据
							</div>
							<div class="box-body">
								<table class="table form-table">
									<colgroup>
										<col width="120px" />
										<col />
									</colgroup>

									<tbody>
									@if (isset($orderRefundLogs) && count($orderRefundLogs) > 0)
									@foreach ($orderRefundLogs as $log)
										<tr>
											<th>退款商品：</th>
											<td>
												@foreach ($log->orderGoodsRefunds as $goodsRefund)
													【名称:{{ $goodsRefund->goodsSpec->name }},退货数量:{{ $goodsRefund->refund_num }},退款金额:{{ round($goodsRefund->refund_fee/100,2) }}】;
												@endforeach
											</td>
										</tr>
										<tr>
											<th>申请退款金额：</th><td>{{ round($log->refund_fee/100,2) }}</td>
										</tr>
										<tr>
											<th>申请退款时间：</th><td>{{ $log->created_at }}</td>
										</tr>
										<tr>
											<th>商户退款单号：</th><td>{{ $log->out_refund_no }}</td>
										</tr>
										<tr>
											<th>退款交易单号：</th><td>{{ $log->refund_id }}</td>
										</tr>
										<tr>
											<th>确认退款金额：</th><td>{{ round($log->real_refund_fee/100,2) }}</td>
										</tr>
										<tr>
											<th>退款成功时间：</th><td>{{ $log->success_time }}</td>
										</tr>
										<tr>
											<th>退款状态：</th><td>
											@if ($log->state == 0)
												申请
											@elseif($log->state == 1)
												已退
											@elseif($log->state == 2)
												拒绝
											@else
												取消
											@endif
											</td>
										</tr>
										<tr>
											<th>退款方式：</th><td>微信原路返回</td>
										</tr>
										<tr>
											<th>退款原因：</th><td>{{ $log->refund_desc }}</td>
										</tr>
										<tr><th colspan="2"></th></tr>
									@endforeach
									@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>
					@endif

				</div>
			</div>


			<div class="tab-pane" id="tab3">
				@if (count($orderShipingList) > 0)
				<table class="table list-table">
					<colgroup>
						<col width="160px" />
						<col width="250px" />
						<col width="250px" />
						<col />
					</colgroup>
					<thead>
						<tr>
							<th>发货时间</th>
							<th>物流公司</th>
							<th>物流单号</th>
							<th>备注</th>
						</tr>
					</thead>

					<tbody>
						@foreach($orderShipingList as $orderShiping)
						<tr>
							<td>{{ $orderShiping['express_time'] }}</td>
							<td>{{ $orderShiping['express_name'] }}</td>
							<td>{{ $orderShiping['express_no'] }}</td>
							<td>{{ $orderShiping['text'] }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				@endif
			</div>
		</div>
	</div>
</div>
<div class="hidden order-deliver-template">
	<div class="container" style="width:1000px;height:auto;max-height:600px;overflow-x:hidden;">
		<form action="{{ url('/admin/order/' . $order->id . '/deliver') }}" method="post" class="order-deliver-form">
			<table class="table">
				<colgroup>
					<col />
					<col width="90px" />
					<col width="80px" />
					<col width="90px" />
					<col width="90px" />
					<col width="90px" />
					<col width="80px" />
				</colgroup>
				<thead>
					<tr>
						<th>商品名称</th>
						<th>商品价格</th>
						<th>购买数量</th>
						<th>已发货数量</th>
						<th>已退货数量</th>
						<th>未发货数量</th>
						<th>发货数量</th>
					</tr>
				</thead>
				<tbody>
	                @foreach($order->orderGoods as $orderGood)
					<tr>
						<td>{{ $orderGood->goods_name }}</td>
						<td>{{ round($orderGood->price/100,2) }}</td>
						<td>{{ $orderGood->num }}</td>
						<td>{{ $orderGood->send_num }}</td>
						<td>{{ $orderGood->return_num }}</td>
						<td>{{ $orderGood->num-$orderGood->send_num-$orderGood->return_num }}</td>
						@if (($orderGood->num-$orderGood->send_num-$orderGood->return_num) == 0)
							<td></td>
						@else
							<td>{{ $orderGood->num-$orderGood->send_num-$orderGood->return_num }}<input type="hidden" name="orderGoodNums[]" value="{{ $orderGood->num-$orderGood->send_num-$orderGood->return_num }}"/><input type="hidden" name="orderGoodIds[]" value="{{ $orderGood->id }}" /></td>
						@endif
					</tr>
					@endforeach
				</tbody>
			</table>

			<table class="table form-table">
				<colgroup>
					<col width="120px" />
					<col />
					<col width="120px" />
					<col />
				</colgroup>
				<tbody>
					<tr>
						<th>订单号:</th><td>{{ $order->order_sn }}<input type="hidden" class="order-sn" value="{{ $order->order_sn }}" /></td>
						<th>配送费用:</th><td>￥{{ round($order->express_fee/100,2) }}</td>
					</tr>
					<tr>
						<th>收货人姓名:</th><td>{{ $order->receiver_name }}</td>
						<th>电话:</th><td>{{ $order->receiver_mobile }}</td>
					</tr>
					<tr>
						<th>地址:</th><td>{{ $order->receiver_area }} {{ $order->receiver_address }}</td>
					</tr>
					<!--
					<tr>
						<th>邮政编码:</th><td>{{ $order->receiver_zip }}</td>
					</tr>
					-->
					<tr>
						<th>快递物流公司:</th><td><input type="text" class="form-control" name="expressName"/></td>
						<th>快递单号:</th><td><input type="text" class="form-control" name="expressNo"/></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
@include('include.message')
<script src="{{ asset('lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ elixir('js/common/datetimePicker.js') }}"></script>
<script src="{{ elixir('js/admins/order.js') }}"></script>
@include('admins.footer')
