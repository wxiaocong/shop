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
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@include('admins.footer')
