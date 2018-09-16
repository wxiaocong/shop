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
		<li class="active">编辑角色</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form action="{{ url('admin/adminRole') }}" class="edit-admin-role-form">
		<input class="admin-role-id" name="id" type="hidden" value="{{ isset($role) ? $role->id : "" }}" />
		<table class="table form-table">
			<colgroup>
				<col width="130px" />
				<col />
			</colgroup>
			<tr>
				<th>角色名：</th>
				<td><input type="text" class="form-control" name="name" value="{{ isset($role) ? $role->name : "" }}"/></td>
			</tr>
			<tr>
				<th>角色说明：</th>
				<td><textarea class="form-control" name="description" rows="2">{{ isset($role) ? $role->description : "" }}</textarea></td>
			</tr>
			<tr>
				<th>权限：</th>
				<td><p class="help-block">* 加粗的权限表示是菜单</p></td>
			</tr>
			@if (isset($rights) && count($rights) > 0)
			@foreach ($rights as $topCategory)
            <tr>
                <th class="top-category-td">
                	<label class="checkbox-inline">
                    	<input class="top-category-checkbox top-category-{{ $topCategory['id'] }}" type="checkbox" value="{{ $topCategory['id'] }}" @if(isset($topCategory['selected'])) checked @endif/>{{ $topCategory['name'] }}
                    </label>
                </th>
                <td class="second-category-and-right-td">
                    <table class="table form-table">
                        <tbody>
                            @foreach ($topCategory['childCategories'] as $secondCategory)
                                <tr>
                                    <td class="second-category-td" style="width:220px;">
                                    	<label class="checkbox-inline">
                                        	<input class="second-category-checkbox second-category-{{ $topCategory['id'] }}-{{ $secondCategory['id'] }}" type="checkbox" value="{{ $secondCategory['id'] }}" @if(isset($secondCategory['selected'])) checked @endif />{{ $secondCategory['name'] }}
                                        </label>
                                    </td>
                                </tr>
	                            @if (isset($secondCategory['rights']) && count($secondCategory['rights']) > 0)
                                <tr>
	                                <td class="right-td">
										@foreach ($secondCategory['rights'] as $right)
										<div style="width:180px;float:left;padding-left:40px;">
											<label class="checkbox-inline">
												<input class="right-checkbox right-{{ $topCategory['id'] }}-{{ $secondCategory['id'] }}-{{ $right['id'] }}" type="checkbox" name="rightId[]" value="{{ $right['id'] }}" @if(isset($right['selected'])) checked @endif />@if($right['showMenu'] == 1) <strong>{{ $right['name'] }}</strong> @else {{ $right['name'] }} @endif
											</label>
										</div>
										@endforeach
	                                </td>
                                </tr>
								@endif
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
			@endforeach
			@endif
			<tr>
				<td></td>
				<td>
					<button class="btn btn-primary admin-role-submit" type="button">保存</button>
				</td>
			</tr>
		</table>
	</form>
</div>
@include('include.message')
<script src="{{ elixir('js/admins/adminRole.js') }}"></script>
@include('admins.footer')
