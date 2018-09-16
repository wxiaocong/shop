@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="#">营销</a>
		</li>
		<li>
			<a href="#">营销活动管理</a>
		</li>
		<li class="active">限时抢购</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form action="{{ url('/admin/speed') }}" method="get" class="page-form">
		<table class="table list-table">
			<colgroup>
				<col />
				<col width="250px" />
				<col width="100px" />
				<col width="100px" />
			</colgroup>
			<caption>
                <a class="btn btn-default" href="{{ url('/admin/speed/create') }}">
                    <i class="fa fa-plus"></i>添加限时抢购
                </a>
			</caption>
			<thead>
				<tr>
					<th>限时抢购名称</th>
					<th>活动时间</th>
					<th>是否开启</th>
					<th>操作</th>
				</tr>
			</thead>

			<tbody>
				@if (isset($page))
					@foreach ($page->data as $data)
						<tr>
							<td><a href="{{ url('/admin/speed/' . $data->id . '/edit') }}">{{ $data->name }}</a></td>
							<td>{{ $data->start_time }} ～ {{ $data->end_time }}</td>
							<td>@if ($data->is_close != 1) 是 @else 否 @endif</td>
							<td>
								<a href="{{ url('/admin/speed/' . $data->id . '/edit') }}">
									<i class='operator fa fa-edit'></i>
								</a>
								<a href="javascript:void(0);" class="good-speed-list-del">
									<i class='operator fa fa-close'></i>
								</a>
								<input type="hidden" value="{{ $data->id }}" />
							</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</form>
</div>
@include('include.message')
@include('include.page')
<script src="{{ elixir('js/admins/goodSpeed.js') }}"></script>
@include('admins.footer')
