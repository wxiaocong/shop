@forelse ($areasTree as $val)
<tr id="area_{{ $val->id }}" name="parent_{{ $val->parent_id }}" style="display: table-row;">
    <td style="padding-left:{{ $level*30 }}px">
        <a href="javascript:toggleArea({{ $val->id }},{{ $level+1 }});">
            <i id="ctrl_{{ $val->id }}" name="box_{{ $val->id }}" class="operator fa fa-plus-square" is_open="no" is_cache="no"></i>
        </a>
        <input type="text" value="{{ $val->area_name }}" data="{{ $val->area_name }}" name="area_name" class="form-control w-auto" style="width:150px" onblur="updateArea({{ $val->id }},this);">
    </td>
    <td><input type="text" value="{{ $val->sort }}" data="{{ $val->sort }}" name="area_sort" class="form-control" style="width:80px" onblur="updateArea({{ $val->id }},this);"></td>
    <td>
        <a href="javascript:addArea({{ $val->id }},{{ $level+1 }});"><i class="operator fa fa-plus"></i></a>
        <a href="javascript:delArea({{ $val->id }});"><i class="operator fa fa-close"></i></a>
    </td>
</tr>
@empty
@endforelse
