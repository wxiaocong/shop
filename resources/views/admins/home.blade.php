@include('admins.header')
<div class="content">
    <div class="content">
        <div class="row">
            <section class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="box">
                    <div class="box-header">
                        <i class="fa fa-line-chart"></i>
                        <h3 class="box-title">用户统计</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <colgroup>
                                <col width="120px" />
                                <col />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>注册用户数</th>
                                    <td>
                                        {{ $registerUserCount }}<small>个</small>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>微信访问用户数</th>
                                    <td>
                                        {{ $weChatAccessUserCount }}<small>个</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>绑定微信用户数</th>
                                    <td>
                                        {{ $bindWeChatUserCount }}<small>个</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>昨日新增用户数</th>
                                    <td>
                                        {{ $yesterdayNewUserCount }}<small>个</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>今日新增用户数</th>
                                    <td>
                                        {{ $todayNewUserCount }}<small>个</small>
                                    </td>
                                </tr>

                                <tr>
                                    <th>上月新增用户数</th>
                                    <td>
                                        {{ $beforeMonthNewUserCount }}<small>个</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>当月新增用户数</th>
                                    <td>
                                        {{ $currentMonthNewUserCount }}<small>个</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <section class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="box">
                    <div class="box-header">
                        <i class="fa fa-line-chart"></i>
                        <h3 class="box-title">订单统计</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <colgroup>
                                <col width="160px" />
                                <col />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>待支付订单</th>
                                    <td>
                                        {{ $waitPayOrderCount }}<small>单</small>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>待发货订单</th>
                                    <td>
                                        {{ $waitDeliveryOrderCount }}<small>单</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>部分发货订单</th>
                                    <td>
                                        {{ $partDeliveryOrderCount }}<small>单</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>昨日订单数量(已支付)</th>
                                    <td>
                                        {{ $yesterdayOrderCount }}<small>单</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>今日订单数量(已支付)</th>
                                    <td>
                                        {{ $todayOrderCount }}<small>单</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>上月订单数量(已支付)</th>
                                    <td>
                                        {{ $beforeMonthOrderCount }}<small>单</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>当月订单数量(已支付)</th>
                                    <td>
                                        {{ $currentMonthOrderCount }}<small>单</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <section class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="box">
                    <div class="box-header">
                        <i class="fa fa-line-chart"></i>
                        <h3 class="box-title">销售统计</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <colgroup>
                                <col width="100px" />
                                <col />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>昨日销售额</th>
                                    <td>
                                        {{ round($yesterdayOrderSaleCount/100,2) }}<small>元</small>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>今日销售额</th>
                                    <td>
                                        {{ round($todayOrderSaleCount/100,2) }}<small>元</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>上月销售额</th>
                                    <td>
                                        {{ round($beforeMonthOrderSaleCount/100,2) }}<small>元</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>当月销售额</th>
                                    <td>
                                        {{ round($currentMonthOrderSaleCount/100,2) }}<small>元</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <section class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                <div class="box">
                    <div class="box-header">
                        <i class="fa fa-line-chart"></i>
                        <h3 class="box-title">商品统计</h3>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <colgroup>
                                <col width="140px" />
                                <col />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>品牌数量</th>
                                    <td>
                                        {{ $brandCount }}<small>个</small>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>商品数(sku)</th>
                                    <td>
                                        {{ $skuCount }}<small>个</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>有库存商品数(sku)</th>
                                    <td>
                                        {{ $validSkuCount }}<small>个</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>限时特价商品数</th>
                                    <td>
                                        {{ $promotionSkuCount }}<small>个</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        <div class="box box-info">
            <div class="box-header">
                <i class="fa fa-file-o"></i>
                <h3 class="box-title">最新10条等待发货订单</h3>
            </div>
            <div class="box-body">
                <table class="table">
                    <colgroup>
                        <col />
                        <col width="245px" />
                        <col width="150px" />
                        <col width="150px" />
                        <col width="130px" />
                        <col width="90px" />
                        <col width="215px" />
                        <col width="80px" />
                        <col width="90px" />
                        <col width="40px" />
                    </colgroup>
                    <thead>
                        <th>订单编号</th>
                        <th>下单用户</th>
                        <th>下单时间</th>
                        <th>付款时间</th>
                        <th>订单状态</th>
                        <th>发货状态</th>
                        <th>收货人</th>
                        <th>支付方式</th>
                        <th>订单金额</th>
                        <th>操作</th>
                    </thead>
                    <tbody>
                        @if (isset($orderPage))
                            @foreach ($orderPage->data as $data)
                                <tr>
                                    <td><a target="_blank" href="{{ url('/admin/order/' . $data->id) }}">{{ $data->order_sn }}</a></td>
                                    <td>{{ '【' . $data->user->mobile . '】' . $data->user->nickname}}</td>
                                    <td>{{ $data->created_at }}</td>
                                    <td>{{ $data->pay_time }}</td>
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
        </div>
    </div>
</div>
@include('admins.footer')
