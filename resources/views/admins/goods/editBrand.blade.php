@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="/admin/home/1">商品</a>
        </li>
        <li>
            <a href="/admin/goods/brand">品牌管理</a>
        </li>
        <li class="active">编辑品牌</li>
    </ul>
</div>
<div class="content">
    <form id="edit_brand_form" action="/admin/goods/brand/{{$brandInfo->id ?? ''}}" method="post">
        <input class="brand_id" name="id" value="{{$brandInfo->id ?? ''}}" type="hidden" />
        <table class="table form-table">
            <colgroup>
                <col width="130px">
                <col>
            </colgroup>
            <tr>
                <th>品牌中文名：</th>
                <td>
                    <input class="form-control" name="logo_cname" type="text" value="{{$brandInfo->logo_cname ?? ''}}" />
                </td>
            </tr>
            <tr>
                <th>品牌英文名：</th>
                <td>
                    <input class="form-control" name="logo_ename" type="text" value="{{$brandInfo->logo_ename ?? ''}}" />
                </td>
            </tr>
            <tr>
                <th>简称：</th>
                <td>
                    <input class="form-control" name="short_name" type="text" value="{{$brandInfo->short_name ?? ''}}" pattern="required" />
                </td>
            </tr>
            <tr>
                <th>状态：</th>
                <td>
                    <div class="col-lg-3">
                        <label class="radio-inline">
                            <input type="radio" name="state" value="1" @if ( ! isset($brandInfo->state) || $brandInfo->state == 1) checked=checked @endif>启用
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="state" value="0" @if (isset($brandInfo->state) && $brandInfo->state == 0) checked=checked @endif>禁用
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>排序：</th>
                <td>
                    <input class="form-control" name="sort" type="number" value="{{$brandInfo->sort ?? ''}}"/>
                </td>
            </tr>
            <!-- <tr>
                <th>品牌logo：</th>
                <td>
                    @if (!empty($brandInfo->img))
                    <img alt="" src="{{$brandInfo->img}}">
                    <br>
                    @endif
                    <input  name="img" id="imgFile" type="file" />
                </td>
            </tr> -->
            <tr>
                <th>一句话介绍：</th>
                <td>
                    <input class="form-control" name="mini_desc" type="text" value="{{$brandInfo->mini_desc ?? ''}}" />
                </td>
            </tr>
            <tr>
                <th>文字简介：</th>
                <td><textarea class="form-control" name="short_desc" style="width:100%;height:200px;">{{$brandInfo->short_desc ?? ''}}</textarea></td>
            </tr>
            <tr>
                <th>详细介绍：</th>
                <td>
                    <textarea id="container" name="detail_desc" type="text/plain">{{$brandInfo->detail_desc ?? ''}}</textarea>
                </td>
            </tr>
            <tr>
                <td></td><td><button class='btn btn-primary brand-submit' type="submit">保存</button></td>
            </tr>
        </table>
    </form>
</div>
@include('include.message')
@include('admins.footer')
<script type="text/javascript" src="{{ asset('js/common/ueditor/ueditor.config.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/common/ueditor/ueditor.all.js') }}"></script>
<script type="text/javascript">
var ue = UE.getEditor('container',{
    initialFrameWidth : '100%',//宽度
    initialFrameHeight: 300//高度
});
$('#edit_brand_form').validate({
    submitHandler: function(form) {
        var buttons = $('.brand-submit');
        var oldBtnText = buttons.text();
        var action = $('.brand_id').val() > 0 ? 'patch' : 'post';
        var url = action == 'patch' ? '/admin/goods/brand/' + $('.brand_id').val() : '/admin/goods/brand';

        $.ajax({
            url:  url,
            data: $('#edit_brand_form').serialize(),
            type: action,
            dataType: 'json',
            beforeSend: function() {
                buttons.attr('disabled', 'true').text('提交中...');
            },
            success: function(jsonObject) {
                if (jsonObject.code == 200 && jsonObject.url) {
                    setTimeout(function(){
                        window.location.href = jsonObject.url;
                    },1000);
                }
                buttons.removeAttr('disabled').text(oldBtnText);
            },
            error: function(xhr, type) {
                ajaxResponseError(xhr, type);
                buttons.removeAttr('disabled').text(oldBtnText);
            }
        });
    },
    // errorPlacement
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    },
    // rules
    rules: {
        logo_cname: {
            maxlength:250
        },
        logo_ename: {
            maxlength:250
        },
        short_name: {
            required: true,
            maxlength:250,
        },
        sort: {
            digits:true
        }
    },
    messages: {
        logo_cname: {
            max:"品牌中文名称最大长度为250"
        },
        logo_ename: {
            max:"品牌英文名称最大长度为250"
        },
        short_name: {
            required: "请输入简称",
            max:"简称最大长度为250"
        },
        sort: {
            digits: "请输入整数"
        }
    }
});
</script>
