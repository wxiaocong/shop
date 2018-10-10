@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="#">工具</a>
        </li>
        <li>
            <a href="#">微信工具</a>
        </li>
        <li class="active">订单退款</li>
    </ul>
</div>
@include("include.serviceMessage")
<div class="content">
  <div class="row">
    <div class="col-lg-4">
        <input type="text" class="form-control" id="transaction_id" placeholder="请输入微信订单号">
    </div>
    <div class="col-lg-4">
        <input type="text" class="form-control" id="money" placeholder="请输入退款金额">
    </div>
    <div class="col-lg-3">
      <button id="refund" class="btn btn-default">退款</button>
    </div>
  </div>
  <div id="content"></div>
</div>
@include('include.message')
@include('admins.footer')
<script>
$('#refund').click(function(){
    var money = parseInt($('#money').val());
    $(this).cnConfirm('确定要退款'+money+'元吗?', function() {
        $.ajax({
            url: '/admin/wechat/getOrderInfo',
            data:{'transaction_id':$('#transaction_id').val(),'money':money},
            type: 'post',
            dataType: 'json',
            success: function(jsonObject) {
                if (jsonObject.code == 200) {
                    $('#content').html(jsonObject.messages);
                } else {
                    tips(jsonObject.messages);
                }
            },
            error: function(xhr, type) {
                ajaxResponseError(xhr, type);
            }
        });
    });
});
</script>
