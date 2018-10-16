@include('users.inc.header')
<link href="{{ asset('lib/blueimp-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet">
<style type="text/css">
body{
    background-color:#fff;
}
.weui-cells{
    font-size:14px !important;
    margin-top:0;
}
.weui-cells .weui-cell{
    border-bottom:none;
}
.weui-cell__ft,.weui-input{
    text-align:right;
    font-size: inherit;
}
p{
    margin:3px 0;
}
button.weui-btn_warn{
    bottom: 0;
    position: fixed;
    border-radius: 0;
    z-index:99;
}
.headimg{
    width: 60px;
    height: 60px;
    border-radius: 60px;
}
.toolbar, .toolbar .title {
    font-size: 1.1rem;
}
</style>
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l"><a href="javascript:self.location='/home'" target="_self"><i class="iconfont icon-fanhui1"></i></a></div>
        <h1>代理商详情</h1>
    </div>
</header>
<section class="zyw-container">
    <div class="weui-cells weui-cells_radio">
        <label class="weui-cell weui-check__label">
          <div class="weui-cell__bd">
            <p>{{$agentType[$agent->level]['type_name']}}</p>
            <div>保证金：{{sprintf("%.2f", $agent->payment/100)}}，库存：{{$agent->goodsNum}}</div>
          </div>
        </label>
    </div>
    <div class="weui-cells weui-cells_form">
      <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">姓名</label></div>
        <div class="weui-cell__bd">{{$agent->agent_name}}</div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">身份证</label></div>
        <div class="weui-cell__bd">{{$agent->idCard}}</div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd">
          <label class="weui-label">手机号码</label>
        </div>
        <div class="weui-cell__bd">{{$agent->mobile}}</div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">状态</label></div>
        <div class="weui-cell__bd">{{$agentState[$agent->state]}}</div>
      </div>
      <div class="weui-cell">
          <div class="weui-cell__hd">
            <label class="weui-label">所属区域</label>
          </div>
          <div class="weui-cell__bd">{{$agent->region}}</div>
      </div>
      <div class="weui-cell">
          <div class="weui-cell__hd"><label class="weui-label">详细地址</label></div>
          <div class="weui-cell__bd">{{$agent->address}}</div>
      </div>
      <div class="weui-cells__title">转账凭证</div>
      <div class="weui-cell">
          <div class="weui-cell__bd">
            <p>支付宝</p>
          </div>
          <div class="weui-uploader__bd">
              <img src="{{ elixir('images/users/alipay.jpg') }}" style="width:150px;height:150px;" >、
          </div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd">
          <label class="weui-label">转账银行</label>
        </div>
        <div class="weui-cell__bd">{{$bank['name']}}</div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd">
          <label class="weui-label">银行卡号</label>
        </div>
        <div class="weui-cell__bd">{{$bank['holder']}}</div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd">
          <label class="weui-label">收款人</label>
        </div>
        <div class="weui-cell__bd">{{$bank['card']}}</div>
      </div>
      <div class="weui-cells__title">备注</div>
      <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
          <div class="weui-cell__bd">{{$agent->remark}}</div>
        </div>
      </div>
    </div>
</section>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
</body>
</html>
