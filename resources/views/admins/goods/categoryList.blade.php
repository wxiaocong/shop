@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="/admin/home/1">商品</a>
        </li>
        <li>
            <a href="#">商品分类管理</a>
        </li>
        <li class="active">分类列表</li>
    </ul>
</div>
<div class="content">
    <table class="table list-table">
        <colgroup>
            <col  />
            <col width="80px" />
            <col width="80px" />
            <col width="150px" />
        </colgroup>
        <caption>
            <a class="btn btn-default" href="/admin/goods/category/create">
                <i class="fa fa-plus"></i> 添加分类
            </a>
        </caption>
        <thead>
            <tr>
                <th>分类名称</th>
                <th>排序</th>
                <th>首页显示</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($categoryList as $val)
            <tr id="category_{{ $val->id }}" name="parent_{{ $val->parent_id }}">
                <td>
                    <a href="javascript:toggleCategory({{ $val->id }},1);"><i id="ctrl_{{ $val->id }}" name="box_{{ $val->parent_id }}" class="operator fa fa-plus-square" is_open="no" is_cache="no"></i></a>
                    <a href="/admin/goods/category/{{ $val->id }}/edit">{{ $val->name }}</a>
                </td>
                <td><input type="number" value="{{ $val->sort }}" id="s{{ $val->id }}" name="sort" class="form-control" style="width:80px" onblur="toSort({{ $val->id }},{{ $val->sort }});"></td>
                <td>@if ($val->state==1)<span class="text-success">是</span>@else 否 @endif</td>
                <td>
                    <a href="/admin/goods/category/create?parent_id={{ $val->id }}"><i class="operator fa fa-plus"></i></a>
                    <a href="/admin/goods/category/{{ $val->id }}/edit"><i class="operator fa fa-edit"></i></a>
                    <a href="javascript:deleteCategory({{ $val->id }});"><i class="operator fa fa-close"></i></a>
                </td>
            </tr>
            @empty
            @endforelse
        </tbody>
    </table>
</div>
@include("include.message")
@include('admins.footer')
<script type="text/javascript">
function deleteCategory(category_id)
{
    $(this).cnConfirm('确定删除该分类及其子分类吗?', function() {
        $.ajax({
            url: '/admin/goods/category/' + category_id,
            type: 'delete',
            dataType: 'json',
            success: function(jsonObject) {
                tips(jsonObject.messages);
                if (jsonObject.code == 200) {
                    setTimeout(function(){
                        window.location.href = jsonObject.url;
                    },1000);
                 }
            },
            error: function(xhr, type) {
                ajaxResponseError(xhr, type);
            }
        });
    });
}

//排序
function toSort(id,oldSort)
{
    if(id !='') {
        var va = $('#s'+id).val();
        if(va == oldSort) {
            return false;
        }
        var part = /^\d+$/i;
        if(va!='' && va!=undefined && part.test(va)) {
            $.getJSON("/admin/goods/category/categorySort",{'id':id,'sort':va}, function(data) {
                if (data.code == 200) {
                    $(this).cnAlert(data.messages, 3);
                } else {
                    showErrorNotice(data.messages);
                }
            });
        }
    }
}
//切换分类
function toggleCategory(category_id,level)
{
    var is_cache = $('#ctrl_'+category_id).attr('is_cache');
    var is_open  = $('#ctrl_'+category_id).attr('is_open');

    //缓存存在
    if(is_cache == 'yes') {
        $('[name="parent_'+category_id+'"]').toggle();
    } else {
        $.get('/admin/goods/category/getCategory',{"category_id":category_id,level:level},function(childHtml){
            $('#category_'+category_id).after(childHtml)
        });
        $('#ctrl_'+category_id).attr('is_cache','yes');
    }

    //是否已经展开
    if(is_open == 'yes') {
        $('#ctrl_'+category_id).removeClass("fa-minus-square").addClass("fa-plus-square");
        $('#ctrl_'+category_id).attr('is_open','no');

        //递归子分类
        $("img[name='box_"+category_id+"'][is_open='yes']").each(function() {
            var idValue = $(this).attr('id').replace("ctrl_","");
            toggleCategory(idValue);
        });
    } else {
        $('#ctrl_'+category_id).removeClass("fa-plus-square").addClass("fa-minus-square");
        $('#ctrl_'+category_id).attr('is_open','yes');
    }
}
</script>
