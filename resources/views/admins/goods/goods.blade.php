@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="#">商品</a>
		</li>
		<li>
			<a href="#">商品管理</a>
		</li>
		<li class="active">商品列表</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<table class="table list-table">
		<colgroup>
			<col width="15px" />
			<col />
			<col width="130px" />
			<col width="130px" />
			<col width="70px" />
			<col width="70px" />
			<col width="90px" />
			<col width="80px" />
			<col width="80px" />
		</colgroup>
		<caption>
			<a class="btn btn-default" href="{{ url('/admin/good/create') }}">
			    <i class="fa fa-plus"></i>添加商品
			</a>
			<a class="btn btn-default" onclick="selectAll('id[]')">
			    <i class="fa fa-check"></i>全选
			</a>
			<a class="btn btn-default good-list-batch-del">
			    <i class="fa fa-check"></i>批量删除
			</a>
			<form action="{{ url('/admin/good') }}" method="get" class="pull-right form-inline page-form" style="margin:0">
				<input type="hidden" value="1" class="curPage" name="curPage">
				<div class="category-box" style="float:left;">
					@if (isset($goodCategory))
					<ctrlarea style="margin-right:5px;">
						<input type="hidden" value="{{ $goodCategory->id }}" name="goodCategoryId">
						<button class="btn btn-default category-del" type="button"><span>{{ $goodCategory->name }}</span></button>
					</ctrlarea>
					@endif
				</div>
				<button class="btn btn-primary goods-category-button" type="button"><i class="fa fa-list"></i> 选择分类</button>
				<input class="form-control" name="search" type="text" value="@if(isset($search) && $search != '') {{ $search }} @endif" placeholder="商品名称/SKU名称" style="width:260px;" >
				<button class="btn btn-default" type="submit">
				    <i class="fa fa-search"></i>搜 索
				</button>
			</form>
		</caption>
		<thead>
			<tr>
				<th></th>
				<th>商品名称</th>
				<th>分类</th>
				<th>销售价</th>
				<th>库存</th>
				<th>状态</th>
				<th>排序</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody class="good-list-tbody">
			@if (isset($page))
				@foreach ($page->data as $data)
					<tr>
						<td><input name="id[]" type="checkbox" value="{{ $data->id }}" /></td>
						<td>
							<a href="{{ url('/admin/good/' . $data->id . '/edit') }}">{{ $data->name }}</a>
						</td>
						<td>
							{{ $data->category->name }}
						</td>
						<td><a href="javascript:void(0);"  title="点击更新价格" class="udpate-price">@if(isset($data->goodsSpecs) && count($data->goodsSpecs) > 0) {{ round(($data->goodsSpecs)[0]->sell_price/100, 2) }} @else {{ round($data->sell_price/100, 2) }} @endif</a></td>
						<td><a href="javascript:void(0);"  title="点击更新库存" class="udpate-total-num">
							<?php $totalNum = 0;foreach ($data->goodsSpecs as $spec) {$totalNum += $spec->number;}?>
							<?php echo $totalNum; ?>
							</a>
						</td>
						<td>
							<select class="form-control input-sm good-list-state">
								<option value="0" @if($data->state == 0) selected @endif>上架</option>
								<option value="2" @if($data->state == 2) selected @endif>下架</option>
							</select>
						</td>
						<td><input type="number" class="form-control input-sm good-list-sort" value="{{ $data->sort }}" onkeyup="onlyNum(this)"/></td>
						<td>
							<a href="{{ url('/admin/good/' . $data->id . '/edit') }}"><i class='operator fa fa-edit'></i></a>
							<a href="javascript:void(0);" class="good-list-del"><i class='operator fa fa-close'></i></a>
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>
</div>
@include('include.message')
@include('admins.goods.include.categoryDialog')
@include('include.page')
<script src="{{ elixir('js/admins/goodCategory.js') }}"></script>
<script src="{{ elixir('js/admins/good.js') }}"></script>
@include('admins.footer')
