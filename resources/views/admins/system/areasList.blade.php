@include('admins.header')
<!--右侧内容 开始-->
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="home-icon fa fa-home"></i>
            <a href="/admin/home">系统</a>
        </li>
        <li>
            <a href="#">地域管理</a>
        </li>
        <li class="active">地区列表</li>
    </ul>
</div>
<div class="content">
    <table class="table list-table">
        <colgroup>
            <col  />
            <col width="120px"/>
            <col width="100px">
        </colgroup>
        <caption>
            <a class="btn btn-default" onclick="addArea(0,0);">
                <i class="fa fa-plus"></i>添加地区
            </a>
        </caption>
        <thead>
            <tr>
                <th>名称</th>
                <th>排序</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="area_box">
            @foreach ($areasTree as $val)
            <tr id="area_{{ $val->id }}" name="parent_{{ $val->parent_id }}">
                <td>
                    <a href="javascript:toggleArea({{ $val->id }},1);"><i id="ctrl_{{ $val->id }}" name="box_{{ $val->parent_id }}" class="operator fa fa-plus-square" is_open="no" is_cache="no"></i></a>
                    <input type="text" value="{{ $val->area_name }}" data="{{ $val->area_name }}" name="area_name" class="form-control w-auto" style="width:150px" onblur="updateArea({{ $val->id }},this);">
                </td>
                <td><input type="number" value="{{ $val->sort }}" name="area_sort" data="{{ $val->sort }}" class="form-control" style="width:80px" onblur="updateArea({{ $val->id }},this);"></td>
                <td>
                    <a href="javascript:addArea({{ $val->id }},1);"><i class="operator fa fa-plus"></i></a>
                    <a href="javascript:delArea({{ $val->id }});"><i class="operator fa fa-close"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@include("include.message")
@include('admins.footer')
<script type="text/javascript">
//切换地区
function toggleArea(area_id,level)
{
    var is_cache = $('#ctrl_'+area_id).attr('is_cache');
    var is_open  = $('#ctrl_'+area_id).attr('is_open');

    //缓存存在
    if(is_cache == 'yes') {
        $('[name="parent_'+area_id+'"]').toggle();
    } else {
        $.get('/admin/system/areas/getArea',{"area_id":area_id,level:level},function(childHtml){
            $('#area_'+area_id).after(childHtml)
        });
        $('#ctrl_'+area_id).attr('is_cache','yes');
    }

    //是否已经展开
    if(is_open == 'yes') {
        $('#ctrl_'+area_id).removeClass("fa-minus-square").addClass("fa-plus-square");
        $('#ctrl_'+area_id).attr('is_open','no');

        //递归子分类
        $("img[name='box_"+area_id+"'][is_open='yes']").each(function() {
            var idValue = $(this).attr('id').replace("ctrl_","");
            toggleArea(idValue);
        });
    } else {
        $('#ctrl_'+area_id).removeClass("fa-plus-square").addClass("fa-minus-square");
        $('#ctrl_'+area_id).attr('is_open','yes');
    }
}

//添加地区
function addArea(area_id,level)
{
    art.dialog.prompt('添加新地域',function(area_name){
        if(!area_name) {
            alert('请填写地域名称');
            return;
        }
        $.ajax({
            url:  '/admin/system/areas',
            data: {"parent_id":area_id,"area_name":area_name},
            type: 'POST',
            dataType: 'json',
            success: function(jsonObject) {
                if (jsonObject.code == 200) {
                    $(this).cnAlert(jsonObject.messages, 3);
                    window.location.reload();
                    return;
                } else {
                    showErrorNotice(jsonObject.messages);
                }
            },
            error: function(xhr, type) {
                ajaxResponseError(xhr, type);
            }
        });
    });
}

//删除地区
function delArea(area_id)
{
    $(this).cnConfirm('确定删除该地区及其子地区吗？?', function() {
        $.ajax({
            url: '/admin/system/areas/' + area_id,
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

//更新地域数据
function updateArea(area_id, obj)
{
    if($.trim(obj.value) == '') {
        alert('地域信息不能为空');
        return;
    }
    if(obj.value == obj.getAttribute('data')) {
        return false;
    }

    var sendData = {};
    if (obj.name == 'area_sort') {
        sendData.sort = obj.value;
    } else {
        sendData.area_name = obj.value;
    }
    $.ajax({
        url:  '/admin/system/areas/'+area_id,
        data: sendData,
        type: 'PATCH',
        dataType: 'json',
        success: function(jsonObject) {
            if (jsonObject.code == 200) {
                obj.setAttribute('data',obj.value);
                $(this).cnAlert(jsonObject.messages, 3);
            } else {
                showErrorNotice(jsonObject.messages);
            }
        },
        error: function(xhr, type) {
            ajaxResponseError(xhr, type);
        }
    });
}
</script>
