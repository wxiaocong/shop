@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="{{ url('/admin/home/6') }}">系统</a>
		</li>
		<li>
			<a href="#">权限管理</a>
		</li>
		<li class="active">编辑权限</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form action="{{ url('admin/right') }}" class="edit-right-form">
		<input class="right-id" name="id" type="hidden" value="{{ isset($right) ? $right->id : "" }}" />
		<table class="table form-table">
			<colgroup>
				<col width="130px" />
				<col />
			</colgroup>
			<tr>
				<th>所属分类：</th>
				<td><select class="form-control" name="categoryId">
					<option value="0">--请选择--</option>
						@foreach ($categoryList as $category)
							<option value="{{ $category->id }}" @if(isset($right) && $right->category_id == $category->id) selected @endif>{{ $category->name }}</option>
						@endforeach
					</select></td>
			</tr>
			<tr>
				<th>权限名称：</th>
				<td><input type="text" class="form-control" name="name" value="{{ isset($right) ? $right->name : "" }}" placeholder="请填写权限名称" /><p class="help-block">* 权限名称</p></td>
			</tr>
			<tr>
				<th>权限action集合：</th>
				<td><textarea class="form-control" name="action" rows="3">{{ isset($right) ? $right->action : "" }}</textarea><p class="help-block">* 如有多个action,请用";"隔开 </p></td>
			</tr>
			<tr>
				<th>权限url集合：</th>
				<td><textarea class="form-control" name="url" rows="2">{{ isset($right) ? $right->url : "" }}</textarea><p class="help-block">* 如有多个url,请用";"隔开 </p></td>
			</tr>
			<tr>
				<th>是否菜单：</th>
				<td>
					<select class="form-control" name="showMenu">
						@foreach (config('statuses.adminRight.showMenu') as $showMenu)
							<option value="{{ $showMenu['code'] }}" @if(isset($right) && $right->show_menu == $showMenu['code']) selected @endif>{{ $showMenu['text'] }}</option>
						@endforeach
					</select>
				</td>
			</tr>
			<tr>
				<th>排序：</th>
				<td>
                    <input class="form-control" type="number" name="sortNum" value="{{ isset($right) ? $right->sort_num : "" }}" onkeyup="onlyNum(this)"/>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<button class="btn btn-primary right-submit" type="button">保存</button>
				</td>
			</tr>
		</table>
	</form>
</div>
@include('include.message')
<script src="{{ elixir('js/admins/adminRight.js') }}"></script>
@include('admins.footer')
