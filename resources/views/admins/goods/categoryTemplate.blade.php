@forelse ($categoryList as $val)
<tr id="category_{{ $val->id }}" name="parent_{{ $val->parent_id }}">
    <td style="padding-left:{{ $level*30 }}px">
        @if ($level == 1)
        <a href="javascript:toggleCategory({{ $val->id }},{{$level+1}});"><i id="ctrl_{{ $val->id }}" name="box_{{ $val->parent_id }}" class="operator fa fa-plus-square" is_open="no" is_cache="no"></i></a>
        @endif
        <a href="/admin/goods/category/{{$val->id}}/edit">{{ $val->name }}</a>
    </td>
    <td><input name="sort" type="number" value="{{ $val->sort }}" id="s{{ $val->id }}" name="sort" class="form-control" style="width:80px" onblur="toSort({{ $val->id }});"></td>
    <td>@if ($val->state==1)<span class="text-success">是</span>@else 否 @endif</td>
    <td>
        @if ($level == 1)
        <a href="/admin/goods/category/create?parent_id={{ $val->id }}"><i class="operator fa fa-plus"></i></a>
        @endif
        <a href="/admin/goods/category/{{$val->id}}/edit"><i class="operator fa fa-edit"></i></a>
        <a href="javascript:deleteCategory({{ $val->id }});"><i class="operator fa fa-close"></i></a>
    </td>
</tr>
@empty
@endforelse
