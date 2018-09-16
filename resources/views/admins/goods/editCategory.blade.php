@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="/admin/home/1">商品</a>
        </li>
        <li>
            <a href="/admin/goods/category">商品分类管理</a>
        </li>
        <li class="active">编辑分类</li>
    </ul>
</div>
<div class="content">
    <form id="edit_category_form" action="/admin/goods/category/{{$categoryInfo->id ?? ''}}" method="post">
        <input class="category_id" name="id" value="{{$categoryInfo->id ?? ''}}" type="hidden" />
        <table class="table form-table">
            <colgroup>
                <col width="130px" />
                <col />
            </colgroup>

            <tr>
                <th>分类名称：</th>
                <td>
                    <input class="form-control" name="name" type="text" value="{{$categoryInfo->name ?? ''}}" required />
                </td>
            </tr>
            <tr>
                <th>一级分类：</th>
                <td>
                    <select name="first_id" class="form-control first_category" @if(!empty($categoryInfo)) disabled="disabled" @endif>
                        <option>选择一级分类</option>
                        @forelse ($firstCategory as $fval)
                        <option value="{{$fval->id}}" @if($first_id == $fval->id) selected @endif>{{$fval->name}}</option>
                        @empty
                        @endforelse
                    </select>
                    <p class="help-block">* 如果不选择一级分类，默认为顶级分类</p>
                </td>
            </tr>
            @if ( ! empty($secondCategory) )
            <tr>
                <th>二级分类：</th>
                <td>
                    <select name="parent_id" class="form-control second_category" @if(!empty($categoryInfo)) disabled="disabled" @endif>
                        <option>选择二级分类</option>
                        @forelse ($secondCategory as $sval)
                        <option value="{{$sval->id}}" @if($parent_id == $sval->id) selected @endif>{{$sval->name}}</option>
                        @empty
                        @endforelse
                    </select>
                    <p class="help-block">* 如果选择一级分类，不选择二级分类，默认为二级分类</p>
                </td>
            </tr>
            @endif
            <tr class="good-img">
                <th>分类图片：</th>
                <td>
                    @if( ! empty($categoryInfo->pic) )
                    <div style="float:left;margin-right:6px;"><img style="max-width: 200px;" src="{{$categoryInfo->pic}}"></div>
                    @endif
                    <input class="fileUpload" type="file" name="file">
                </td>
            </tr>
            <tr>
                <th>首页是否显示：</th>
                <td>
                    <label class="radio-inline">
                        <input type="radio" name="state" value="1" @if ( ! isset($categoryInfo->state) || $categoryInfo->state == 1) checked=checked @endif>是
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="state" value="2" @if ( isset($categoryInfo->state) && $categoryInfo->state == 2) checked=checked @endif>否
                    </label>
                </td>
            </tr>
<!--             <tr>
                <th>分类logo：</th>
                <td>
                    <div class="col-lg-3">
                        @if (!empty($categoryInfo->img))
                        <img alt="" src="{{$categoryInfo->img}}">
                        @endif
                        <input class="form-control" name="img" id="imgFile" type="file" />
                    </div>
                </td>
            </tr> -->
            <tr>
                <th>排序：</th>
                <td>
                    <input class="form-control" name="sort" type="number" value="{{$categoryInfo->sort ?? ''}}"/>
                </td>
            </tr>
            <tr>
                <td></td><td><button class='btn btn-primary category-submit' type="submit">保存</button></td>
            </tr>
        </table>
    </form>
</div>
@include('include.message')
@include('admins.footer')
<script src="{{ asset('js/common/fileinput/js/fileinput.js') }}"></script>
<script src="{{ asset('js/common/fileinput/js/locales/zh.js') }}"></script>
<script src="{{ elixir('js/common/fileUpload.js') }}"></script>
<script type="text/javascript">
createFileinput($('.fileUpload'));
$('.fileUpload').on('fileuploaded', function (event, data, previewId, index){
    if (data.response.code == 200) {
        $('.good-img').find('.file-upload-indicator').each(function() {
            var obj = $(this).find('i');
            if ('glyphicon glyphicon-ok-sign text-success' == obj.attr('class') && $(this).find('input[name="good[pic][]"]').length == 0) {
                $(this).append('<input type="hidden" name="good[pic][]" value="' + data.response.fileName + '"/>');
                return;
            }
        });
    }
});
//初始化上传控件
function createFileinput(className)
{
    className.fileinput({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        language: 'zh', //设置语言
        uploadUrl:'/fileUpload/uploadLocalFile', //上传的地址
        allowedFileExtensions: ['jpg', 'gif', 'png'],//接收的文件后缀
        uploadExtraData:{'dataType': 'file'},
        uploadAsync: true, //默认异步上传
        showUpload: false, //是否显示上传按钮
        showRemove: false, //显示移除按钮
        showPreview: true, //是否显示预览
        showCaption: false,//是否显示标题
        browseClass:"btn btn-primary", //按钮样式
        dropZoneEnabled: false,//是否显示拖拽区域
        maxFileCount:1, //表示允许同时上传的最大文件个数
        enctype:'multipart/form-data',
        validateInitialCount:true,
        msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
    });
}

@if ( ! empty($secondCategory) )
$('.first_category').change(function(){
    var category_id = $(this).val();
    if (category_id > 0) {
        $.get('/admin/goods/category/getCategory',{"category_id":category_id, 'isEdit':1},function(categorys){
            optionHtml = "<option></option>";
            for (var i=0; i< categorys.length; i++) {
                optionHtml += "<option value='" + categorys[i]['id'] + "'>" + categorys[i]['name'] + "</option>";
            }
            $('.second_category').html(optionHtml);
        });
    } else {
        $('.second_category').empty();
    }
});
@endif
$('#edit_category_form').validate({
    submitHandler: function(form) {
        var buttons = $('.category-submit');
        var oldBtnText = buttons.text();
        var action = $('.category_id').val() > 0 ? 'patch' : 'post';
        var url = action == 'patch' ? '/admin/goods/category/' + $('.category_id').val() : '/admin/goods/category';

        $.ajax({
            url:  url,
            data: $('#edit_category_form').serialize(),
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
        name: {
            required: true,
            maxlength:50,
        },
        sort: {
            digits:true
        }
    },
    messages: {
        name: {
            required: "请输入分类名称",
            max:"最大长度为50"
        },
        sort: {
            digits: "请输入整数"
        }
    }
});
</script>
