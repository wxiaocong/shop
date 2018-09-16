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
		<li class="active">权限资源</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<table class="table list-table">
		<caption>
			<a class="btn btn-default" href="{{ url('/admin/adminRight/create') }}">
			    <i class="fa fa-plus"></i>添加权限
			</a>
			<a class="btn btn-default" onclick="selectAll('id[]')">
			    <i class="fa fa-check"></i>全选
			</a>
			<a class="btn btn-default right-list-batch-del">
			    <i class="fa fa-check"></i>批量删除
			</a>
			<form action="{{ url('/admin/adminRight') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
				<input type="hidden" value="1" class="curPage" name="curPage">
				权限分类<select class="form-control" name="categoryId" style="margin-left:5px;margin-right:5px;">
					<option value="0">选择分类</option>
					@foreach ($categoryList as $category)
						<option value="{{ $category->id }}" @if($categoryId == $category->id) selected @endif>{{ $category->name }}</option>
					@endforeach
				</select>
				<input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="权限名称" style="width:250px;" >
				<button class="btn btn-default" type="submit">
				    <i class="fa fa-search"></i>搜 索
				</button>
			</form>
		</caption>
		<colgroup>
			<col width="35px" />
			<col width="180px" />
			<col width="180px" />
			<col width="200px" />
			<col width="50px" />
			<col width="50px" />
			<col width="80px" />
		</colgroup>
		<thead>
			<tr>
				<th></th>
				<th>名字</th>
				<th>所属分类</th>
				<th>权限url</th>
				<th>是否菜单</th>
				<th>排序</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody class="right-list-tbody">
			@if (isset($page))
				@foreach ($page->data as $data)
					<tr>
						<td><input name="id[]" type="checkbox" value="{{ $data->id }}" /></td>
						<td>
							{{ $data->name }}
						</td>
						<td>
							@if (isset($data->rightCategory->parentCategory))
								{{ $data->rightCategory->parentCategory->name }}->{{ $data->rightCategory->name }}
							@else
								{{ $data->rightCategory->name }}
							@endif
						</td>
						<td>
							{{ $data->url }}
						</td>
						<td>
							<select class="input-sm right-list-show-menu">
								@foreach (config('statuses.adminRight.showMenu') as $showMenu)
									<option value="{{ $showMenu['code'] }}" @if($data->show_menu == $showMenu['code']) selected @endif>{{ $showMenu['text'] }}</option>
								@endforeach
							</select>
						</td>
						<td><input type="number" name="sortNum" class="form-control input-sm right-list-sort" value="{{ $data->sort_num }}" onkeyup="onlyNum(this)"/></td>
						<td>
							<a href="{{ url('/admin/adminRight/' . $data->id . '/edit') }}"><i class='operator fa fa-edit'></i></a>
							<a href="javascript:void(0);" class="right-list-del"><i class='operator fa fa-close'></i></a>
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>
</div>
@include('include.message')
@include('include.page')
<script src="{{ elixir('js/admins/adminRight.js') }}"></script>
@include('admins.footer')
