@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="/admin/home/1">商品</a>
		</li>
		<li>
			<a href="/admin/model">模型管理</a>
		</li>
		<li class="active">模型编辑</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form class="edit-model-form" action="{{ url('/admin/model') }}">
		<input class="model-id" name="id" type="hidden" value="{{ isset($data) ? $data->id : "" }}" />
		<table class="table form-table">
			<colgroup>
				<col width="130px" />
				<col />
			</colgroup>

			<tr>
				<th>模型名称：</th>
				<td>
					<input class="form-control" name="name" type="text" value="{{ isset($data) ? $data->name : "" }}"  />
				</td>
			</tr>
			<tr>
				<th>添加扩展属性：</th>
				<td><button class="btn btn-default add-attribute" type="button">添加扩展属性</button></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<table class="table">
						<thead>
							<tr>
								<td>属性名</td>
								<td>操作类型</td>
								<td>选择项数据【每项数据之间请用英文逗号','分隔】</td>
								<td>是否规格</td>
								<td>是否为商品筛选项</td>
								<td>操作</td>
							</tr>
						</thead>
						<tbody class="attribute-list">
							@if(isset($data) && isset($data->attributes) && count($data->attributes) > 0)
								@foreach ($data->attributes as $attribute)
									<tr class="attribute-tr">
										<td>
											<input name="attribute[id][]" type="hidden" value="{{ $attribute->id }}">
											<input name="attribute[name][]" class="form-control" type="text" value="{{ $attribute->name }}">
										</td>
										<td>
											<select class="form-control" name="attribute[showType][]">
												@if ($attribute->spec == 1)
												<option value="1">单选框</option>
												@else
													@foreach (config('statuses.attribute.type') as $type)
				                                        <option value="{{ $type['code'] }}" {{ ($attribute->type == $type['code']) ? 'selected':'' }}>{{ $type['text'] }}</option>
				                                    @endforeach
												@endif
											</select>
										</td>
										<td>
											<input class="form-control" name="attribute[value][]" type="text" value="{{ $attribute->value }}">
										</td>
										<td>
											<input type="checkbox" class="attribute-spec-check" @if($attribute->spec == 1) checked @endif>
											<input type="hidden" name="attribute[isSpec][]" value="{{ $attribute->spec }}">
										</td>
										<td>
											<input type="checkbox" class="attribute-search-check" @if($attribute->search == 1) checked @endif>
											<input type="hidden" name="attribute[isSearch][]" value="{{ $attribute->search }}">
										</td>
										<td>
											<a class="del-attribute"><i class="operator fa fa-close"></i></a>
										</td>
									</tr>
								@endforeach
							@else
								<tr class="attribute-tr">
									<td>
										<input name="attribute[id][]" type="hidden" value="">
										<input name="attribute[name][]" class="form-control" type="text" value="">
									</td>
									<td>
										<select class="form-control" name="attribute[showType][]">
											@foreach (config('statuses.attribute.type') as $type)
							                    <option value="{{ $type['code'] }}">{{ $type['text'] }}</option>
							                @endforeach
										</select>
									</td>
									<td>
										<input class="form-control" name="attribute[value][]" type="text" value="">
									</td>
									<td>
										<input type="checkbox" class="attribute-spec-check">
										<input type="hidden" name="attribute[isSpec][]" value="0">
									</td>
									<td>
										<input type="checkbox" class="attribute-search-check" checked>
										<input type="hidden" name="attribute[isSearch][]" value="1">
									</td>
									<td>
										<a class="del-attribute"><i class="operator fa fa-close"></i></a>
									</td>
								</tr>
							@endif
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<p class="help-block">* 模型属性的变更会影响相关联的商品,请谨慎</p>
					<button class='btn btn-primary model-submit' type="submit">保存</button>
				</td>
			</tr>
		</table>
	</form>
</div>
<div class="hidden">
	<table>
		<tbody class="attribute-row-template">
			<tr class="attribute-tr">
				<td>
					<input name="attribute[id][]" type="hidden" value="">
					<input name="attribute[name][]" class="form-control" type="text" value="">
				</td>
				<td>
					<select class="form-control" name="attribute[showType][]">
						@foreach (config('statuses.attribute.type') as $type)
		                    <option value="{{ $type['code'] }}">{{ $type['text'] }}</option>
		                @endforeach
					</select>
				</td>
				<td>
					<input class="form-control" name="attribute[value][]" type="text" value="">
				</td>
				<td>
					<input type="checkbox" class="attribute-spec-check">
					<input type="hidden" name="attribute[isSpec][]" value="0">
				</td>
				<td>
					<input type="checkbox" class="attribute-search-check" checked>
					<input type="hidden" name="attribute[isSearch][]" value="1">
				</td>
				<td>
					<a class="del-attribute"><i class="operator fa fa-close"></i></a>
				</td>
			</tr>
		</tbody>
	</table>
</div>
@include('include.message')
<script src="{{ elixir('js/admins/model.js') }}"></script>
@include('admins.footer')
