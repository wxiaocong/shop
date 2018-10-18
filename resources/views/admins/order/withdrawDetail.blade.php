@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="/admin/home/2">订单</a>
        </li>
        <li>
            <a href="/admin/user">提现单管理</a>
        </li>
        <li class="active">提现单详情</li>
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
                                提现单
                            </div>
                            <div class="box-body">
                                <table class="table form-table">
                                    <colgroup>
                                        <col width="120px" />
                                        <col />
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>申请时间:</th><td>{{ $withdraw->created_at }}</td>
                                        </tr>
                                        <tr>
                                            <th>付款时间:</th><td>{{ $withdraw->pay_time ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>提现单号:</th><td>{{ $withdraw->order_sn }}</td>
                                        </tr>
                                        <tr>
                                            <th>申请人:</th><td>{{ $withdraw->nickname }}({{ $withdraw->realname }})</td>
                                        </tr>
                                        <tr>
                                            <th>提现金额:</th><td>{{ sprintf("%.2f",$withdraw->amount/100) }}</td>
                                        </tr>
                                        <tr>
                                            <th>手续费:</th><td>{{ sprintf("%.2f",$withdraw->cmms_amt/100) }}</td>
                                        </tr>
                                        <tr>
                                            <th>开户行:</th><td>{{ $bank_no[$withdraw->bank_code] }}</td>
                                        </tr>
                                        <tr>
                                            <th>银行账户:</th><td>{{ $withdraw->enc_bank_no }}</td>
                                        </tr>
                                        <tr>
                                            <th>状态</th><td>@if($withdraw->state==1) 等待付款 @elseif($withdraw->state==2) 提现成功 @else 取消 @endif</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">
                                                @if($withdraw->state == 1)
                                                <button type="button" class="btn btn-primary audit-button">审批通过已付款</button>
                                                <button type="button" class="btn btn-warning cancle-button">取消</button>
                                                @endif
                                            </th>
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
$('.audit-button').click(function(){
    var id = {{$withdraw->id}};
    var buttons = $(this);
    $.ajax({
        url:  '/admin/withdraw/audit',
        type: 'post',
        data: {'id': id},
        dataType: 'json',
        success: function(jsonObject) {
            if (jsonObject.code == 200) {
                window.location.reload();
            } else {
                showErrorNotice(jsonObject.messages);
            }
            buttons.removeAttr('disabled');
        },
        error: function(xhr, type) {
            buttons.removeAttr('disabled');
            ajaxResponseError(xhr, type);
        }
    });
});
$('.cancle-button').click(function(){
    var id = {{$withdraw->id}};
    var buttons = $(this);
    $.ajax({
        url:  '/admin/withdraw/cancle',
        type: 'post',
        data: {'id': id},
        dataType: 'json',
        success: function(jsonObject) {
            buttons.removeAttr('disabled');
            if (jsonObject.code == 200) {
                window.location.reload();
            } else {
                showErrorNotice(jsonObject.messages);
            }
        },
        error: function(xhr, type) {
            buttons.removeAttr('disabled');
            ajaxResponseError(xhr, type);
        }
    });
});
</script>
@include('admins.footer')
