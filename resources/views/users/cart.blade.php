@include('users.inc.header')
<link href="{{ elixir('css/users/aui.css') }}" rel="stylesheet">
<link href="{{ elixir('css/users/cart.css') }}" rel="stylesheet">
<header class="zyw-header">
    <div class="zyw-container white-color">
        <div class="head-l">
            <a href="javascript:{{$_COOKIE['lastRecord'] ?? "self.location='/'"}};" target="_self"><i class="iconfont icon-fanhui1"></i></a>
        </div>
        <h1>购物车</h1>
        <div class="head-r" id="rem_s">编辑</div>
    </div>
</header>
<section class="zyw-container">
<div class="commodity_list_box">
            <!--商品列表-->
            <div class="commodity_box">
                <div class="commodity_list">
                    <!--商品-->
                    <ul class="commodity_list_term">
                        @if(!empty($cart))
                        @foreach ($cart as $val)
                        <li class="select">
                            <em aem="0" cart_id="{{$val['spec_id']}}"></em>
                            <a href="/goods/{{$val['spec_id']}}"><img src="{{$val['img']}}"/></a>
                            @if($val['state'] > 0 || $val['deleted_at'])
                            <p class="image_tag" >已下架</p>
                            @elseif($val['number'] < 1)
                            <p class="image_tag" >库存不足</p>
                            @endif
                            <div class="div_center">
                                <h4><a href="/goods/{{$val['spec_id']}}">{{$val['name']}}</a></h4>
                            </div>
                            <div class="now_value">￥<b class="qu_su">{{sprintf("%.2f",$val['price']/100)}}</b></div>
                            <div class="div_right">
                                @if($val['state'] == 0 && empty($val['deleted_at']) && $val['number'] > 0)
                                <i onclick="reducew(this)">-</i>
                                <span class="zi" goods_id="{{$val['goods_id']}}" spec_id="{{$val['spec_id']}}" number="{{$val['number']}}">{{$val['num']}}</span>
                                <i onclick="plusw(this)">+</i>
                                @else
                                <i>-</i>
                                <span class="zi" goods_id="{{$val['goods_id']}}" spec_id="{{$val['spec_id']}}" number="{{$val['number']}}">{{$val['num']}}</span>
                                <i>+</i>
                                @endif
                            </div>
                            
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <!-- 商品列表 end -->
        </div>
</section>
<form method="post" action="/cart/purchase" id="purchase-form">
<input type="hidden" name="_token" value="{{ csrf_token() }}" />
</form>
<footer class="zyw-footer">
    <div class="zyw-container white-bgcolor">
        <div class="settle_box" style="bottom: 56px;height: 48px;">
            <dl class="all_check select">
                <dt>
                    <span id="all_pitch_on"></span>
                    <b>全选</b>
                </dt>
            </dl>
            <dl class="total_amount" style="margin-top:0;">
                <dd id="total_price" style="font-size: 16px;float: left;line-height: 28px;color: #f34347;">合计：¥<b>0</b></dd>
                <dd style="font-size: 12px;float: left;line-height: 18px;">购物车商品不参与限时特价</dd>
            </dl>
            <input type="hidden" name="gcs" id="gcs"/>
            <a class="settle_btn" href="javascript:void(0);" id="confirm_cart" style="padding: 13px 0;">去结算<span id="chooseNum"></span></a>
            <a class="settle_btn" href="javascript:void(0);" id="confirm_del" style="padding: 13px 0;">删除</a>
        </div>
    </div>
</footer>
@include('users.inc.footer')
<script src="{{asset('js/users/ionic.bundle.min.js')}}"></script>
<script type="text/javascript">
    //定义全局变量
    var i=0;
    //金额总和
    var money=0;
    //计算合计价格
    var cart_money=new Object();
    //全部商品ID
    var cart_id=new Object();
    //备份商品ID，用于全选后去掉全选又再次全选
    var cart_id_copy=new Object();
</script>
<script>
    var noX = 0;
    /* 没选中时点击加减计算数量  */
    var allThis = $(".commodity_box .select em");
    /*底部全选*/
    var totalH;
    //结算
    $('#confirm_cart').click(function(){
        if($('.commodity_list_term .select .pitch_on').length < 1) {
            $.toast('请选择结算商品', "forbidden");
            return false;
        }
        var cartData = {};
        $('.commodity_list_term .select .pitch_on').each(function(){
            $('#purchase-form').append($("<input type='hidden' name='spec["+$(this).attr('cart_id')+"]' value='"+ $(this).siblings('.div_right').children('.zi').text()+"' />"));
        });
        $('#purchase-form').submit();
        return false;
    });
    //删除
    $('#confirm_del').click(function(){
        $.confirm({
            title: '删除购物车商品提示',
            text: '是否确认将已选中的商品删除?',
            onOK: function () {
                var spec = {};
                $('.commodity_list_term .select .pitch_on').each(function(i){
                    spec[i] = $(this).attr('cart_id');
                });
                if($.isEmptyObject(spec)) {
                    $.toast('请选择要删除的商品', "forbidden");
                    return false;
                }
                $.ajax({
                    url:  '/cart/delCart',
                    data: {'spec':spec},
                    type: 'post',
                    dataType: 'json',
                    success: function(jsonObject) {
                        if (jsonObject.code == 200) {
                            big_cart_remove();
                            changeChooseNum();
                            $.toast(jsonObject.messages);
                            $('.weui-badge').text(jsonObject.data);
                         } else {
                             $.toast(jsonObject.messages, "forbidden");
                         }
                    }
                })
            }
        });
    });
    /* 减  */
    function reduceMod(e, totalH, mod, noX) {
        var tn = e.siblings().find(".qu_su").text();
        /* 当前选中商品  */
        var tn1 = e.siblings().find(".zi").text();
        /* 商品数量  */
        if (mod != 2) {
            var Total = parseFloat(totalH) - (tn * tn1);
            /* 总价格减该商品总数价格  */
            $("#total_price b").text(Total.toFixed(2));
        } else {
            /* 合计加单价-1 */
            var Total = parseFloat(totalH) - parseFloat(tn);
            /* 总价格减该商品总数价格  */
            $("#total_price b").text(Total.toFixed(2));
        }
        changeChooseNum();
    }
    ;/* 加  */
    function plusMod(e, totalH, mod) {
        var tn = e.siblings().find(".qu_su").text();
        /* 当前选中商品  */
        var tn1 = e.siblings().find(".zi").text();
        /* 商品数量  */
        if (mod != 2) {
            var Total = parseFloat(totalH) + (tn * tn1);
            /* 总价格加上该商品总数价格  */
            $("#total_price b").text(Total.toFixed(2));
        } else {
            /* 合计加单价+1 */
            var Total = parseFloat(totalH) + (parseFloat(tn) + (noX - 1));
            /* 总价格加上该商品总数价格  */
            $("#total_price b").text(Total.toFixed(2));
        }
        changeChooseNum();
    }
    ;/*全选该店商品价格 加*/
    function commodityPlusMod(e, totalH) {
        var qu = e.parents(".commodity_list").find(".pitch_on").parent().find(".qu_su");
        var quj = e.parents(".commodity_list").find(".pitch_on").parent().find(".zi");
        var Total = 0;
        var erTotal = true;
        /* 该商品全部金额  */
        for (var i = 0; i < qu.length; i++) {
            var n = qu.eq(i).text();
            var n1 = quj.eq(i).text();
            /*合计价格*/
            if (erTotal) {
                Total = parseFloat(totalH) + (parseFloat(n) * parseFloat(n1));
                if (Total < 0)
                    Total = 0;
                erTotal = false;
            } else {
                Total = parseFloat(Total) + (parseFloat(n) * parseFloat(n1));

            }
        }
        $("#total_price b").text(Total.toFixed(2));
        /* 合计金额  */
    }
    ;var plus;
    /*全选该店商品价格 减*/
    function commodityReduceMod(e, totalH) {
        var qu = e.parents(".commodity_list").find(".pitch_on").parent().find(".qu_su");
        var quj = e.parents(".commodity_list").find(".pitch_on").parent().find(".zi");
        var Total = 0;
        plus = totalH;

        var erTotal = true;
        /* 该商品全部金额  */
        for (var i = 0; i < qu.length; i++) {
            var n = qu.eq(i).text();
            var n1 = quj.eq(i).text();
            /*合计价格*/
            if (erTotal) {
                Total = parseFloat(totalH) - (parseFloat(n) * parseFloat(n1));
                plus = Total;
                if (Total < 0)
                    Total = 0;
                erTotal = false;
            } else {
                Total = parseFloat(Total) - (parseFloat(n) * parseFloat(n1));
                plus = Total;

            }

            $("#total_price b").text(Total.toFixed(2));
            /* 合计金额  */
            plus;
        }
    }
    ;/*全部商品价格*/
    function commodityWhole() {
        /* 合计金额  */
        var je = $(".commodity_box .select .qu_su");
        /* 全部商品单价  */
        var je1 = $(".commodity_box .select .zi");
        /* 全部商品数量  */
        var TotalJe = 0;
        for (var i = 0; i < je.length; i++) {
            var n = je.eq(i).text();
            var n1 = je1.eq(i).text();
            TotalJe = TotalJe + (parseFloat(n) * parseFloat(n1));

        }
        $("#total_price b").text(TotalJe.toFixed(2));
        /* 合计金额  */
    }
    ;
    //选择结算商品

    $(".select em").click(function() {
        var su = $(this).attr("aem");
        var carts_id = $(this).attr("cart_id");
        totalH = $("#total_price b").text();
        /* 合计金额  */
        if (su == 0) {
            /* 单选商品  */
            if ($(this).hasClass("pitch_on")) {
                /*去该店全选*/
                $(this).parents("ul").siblings(".select").find("em").removeClass("pitch_on");
                /*去底部全选*/
                $("#all_pitch_on").removeClass("pitch_on");
                $(this).removeClass("pitch_on");
                reduceMod($(this), totalH);
                cart_id[carts_id] = "";
                delete cart_id[carts_id];
            } else {
                $(this).addClass("pitch_on");
                var n = $(this).parents("ul").children().find(".pitch_on");
                var n1 = $(this).parents("ul").children();
                plusMod($(this), totalH, 0, noX);
                cart_id[carts_id] = "";
                /*该店商品全选中时*/
                if (n.length == n1.length) {
                    $(this).parents("ul").siblings(".select").find("em").addClass("pitch_on");
                }
                /*商品全部选中时*/
                var fot = $(".commodity_list_term .select .pitch_on");
                var fot1 = $(".commodity_list_term .select em");
                if (fot.length == fot1.length)
                    $("#all_pitch_on").addClass("pitch_on");
            }
        } else {
            /* 全选该店铺  */
            if ($(this).hasClass("pitch_on")) {
                /*去底部全选*/
                $("#all_pitch_on").removeClass("pitch_on");
                $(this).removeClass("pitch_on");

                commodityReduceMod($(this), totalH);
                $(this).parent().siblings("ul").find("em").removeClass("pitch_on");
                delete cart_id[carts_id];
            } else {
                commodityReduceMod($(this), totalH);

                $(this).addClass("pitch_on");

                $(this).parent().siblings("ul").find("em").addClass("pitch_on");

                /*if(plus != NaN && plus != undefined && plus > 0){
                 totalH = parseFloat(totalH)-parseFloat(plus);
                 if(totalH < 0)
                 totalH = 0;
                 }*/
                if (plus == undefined || plus == NaN) {
                    plus = 0
                }

                commodityPlusMod($(this), plus);
                cart_id[carts_id] = "";
                /*商品全部选中时*/
                var fot = $(".commodity_list_box .tite_tim .pitch_on");
                var fot1 = $(".commodity_list_box .tite_tim em");
                if (fot.length == fot1.length) {
                    $("#all_pitch_on").addClass("pitch_on");
                }

            }
        }
    });
    /* 底部全选  */

    var bot = 0;
    $("#all_pitch_on").click(function() {
        if (bot == 0) {
            $(this).addClass("pitch_on");
            allThis.removeClass("pitch_on");
            allThis.addClass("pitch_on");
            /*总价格*/
            commodityWhole();
            bot = 1;
            //重新加入属性对象
            for (var key in cart_id_copy) {
                cart_id[key] = "";
            }
        } else {
            $(this).removeClass("pitch_on");
            allThis.removeClass("pitch_on");
            $("#total_price b").text("0");
            bot = 0;
            //移除全部对象
            for (var key in cart_id) {
                delete cart_id[key];
            }
        }
        changeChooseNum();
    });

    function changeChooseNum() {
        var num = 0;
        $('.commodity_list_term .select .pitch_on').each(function(){
            num = num + parseInt($(this).siblings('.div_right').children('.zi').text());
        });
        if(num > 0) {
            $('#chooseNum').text('('+num+')');
        } else {
            $('#chooseNum').text('');
        }
    }

    /* 编辑商品  */
    var topb = 0;
    $("#rem_s").click(function() {
        if (topb == 0) {
            $(this).text("完成");
            $(".total_amount").hide();
            /* 合计  */
            $("#confirm_cart").hide();
            /* 结算  */
            $("#confirm_cart1").show();
            /* 删除 */
            topb = 1;
        } else {
            topb = 0;
            $(this).text("编辑");
            $(".total_amount").show();
            /* 合计  */
            $("#confirm_cart").show();
            /* 结算  */
            $("#confirm_cart1").hide();
            /* 删除 */
            allThis.removeClass("pitch_on");
            /* 取消所有选择  */
            $("#all_pitch_on").removeClass("pitch_on");
            /* 取消所有选择  */
            $("#total_price b").text("0");
            /*合计价格清零*/

        }

    });
    /* 加减  */

    function reducew(obj) {
        //减
        var $this = $(obj);
        var totalH = $("#total_price b").text();
        /* 合计金额  */
        var ise = $this.siblings("span").text();
        if (noX <= 0) {
            noX = 0;
        } else {
            noX--;
        }
        ;
        if (parseInt(ise) <= 1) {
            $this.siblings("span").text("1");
        } else {
            var n = parseInt(ise) - 1;
            $this.siblings("span").text(n);
            if ($this.parent().parent().children("em").hasClass("pitch_on")) {
                var mo = $this.parent().parent().children("em");
                reduceMod(mo, totalH, 2, noX);
                noX = 0;
            }
            //更新购物车
            updateNum($this,-1);
        }
    };
    function plusw(obj) {
        //加
        var $this = $(obj);
        var totalH = $("#total_price b").text();
        /* 合计金额  */
        var ise = $this.siblings("span").text();
        var maxNumber = $this.siblings("span").attr('number');
        var n = parseInt(ise) + 1;
        if(n > maxNumber) {
            $.toast('最多只能买'+maxNumber+'件哦！', "text");
            return false;
        }
        noX++;

        $this.siblings("span").text(n);
        if ($this.parent().parent().children("em").hasClass("pitch_on")) {
            var mo = $this.parent().parent().children("em");
            plusMod(mo, totalH, 2, noX);
            noX = 0;
        }
        updateNum($this,1);
    }

    //更新购物车数量
    function updateNum($this, num) {
        var goods_id = $this.siblings('.zi').attr('goods_id');
        var spec_id = $this.siblings('.zi').attr('spec_id');
        $.ajax({
            url:  '/cart',
            data: {'goods_id':goods_id, 'spec_id':spec_id,'num':num},
            type: 'post',
            dataType: 'json',
            success: function(res) {
                $('.weui-badge').text(res.data);
            }
        })
    }

    //删除
    function big_cart_remove() {
        $(".commodity_list_term .pitch_on").parent().remove();
        $(".commodity_list .tite_tim > em.pitch_on").parents(".commodity_box").remove();
    }
</script>