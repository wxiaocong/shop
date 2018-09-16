@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="/admin/home/1">商品</a>
		</li>
		<li>
			<a href="#">模型管理</a>
		</li>
		<li class="active">模型列表</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form class="page-form model-form" action="{{ url('/admin/model') }}">
		<table class="table list-table">
			<colgroup>
				<col width="35px" />
				<col />
				<col width="120px" />
			</colgroup>
			<caption>
                <a class="btn btn-default" href="{{ url('/admin/model/create') }}">
                    <i class="fa fa-plus"></i>添加模型
                </a>
                <a class="btn btn-default" onclick="selectAll('id[]')">
                    <i class="fa fa-check"></i>全选
                </a>
                <a class="btn btn-default model-batch-del">
                    <i class="fa fa-close"></i>批量删除
                </a>
                <input type="hidden" value="1" class="curPage" name="curPage">
			</caption>
			<thead>
				<tr>
					<th></th>
					<th>模型名称</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				@if (isset($page))
					@foreach ($page->data as $data)
						<tr>
							<td><input name="id[]" type="checkbox" value="{{ $data->id }}" /></td>
							<td><a href="{{ url('/admin/model/' . $data->id . '/edit') }}">{{ $data->name }}</a></td>
							<td>
								<a href="{{ url('/admin/model/' . $data->id . '/edit') }}"><i class='operator fa fa-edit'></i></a>
								<a href="javascript:void(0)" class="model-del"><input type="hidden" value="{{ $data->id }}" /><i class='operator fa fa-close'></i></a>
							</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</form>
</div>
@include('include.page')
@include('include.message')
<script src="{{ elixir('js/admins/model.js') }}"></script>
@include('admins.footer')
