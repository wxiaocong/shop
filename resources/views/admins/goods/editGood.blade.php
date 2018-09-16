@include('admins.header')
<div class="breadcrumbs" id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="home-icon fa fa-home"></i>
			<a href="/admin/home/1">商品</a>
		</li>
		<li>
			<a href="/admin/good">商品管理</a>
		</li>
		<li class="active">商品编辑</li>
	</ul>
</div>
@include("include.serviceMessage")
<div class="content">
	<form class="edit-good-form">
	<input type="hidden" class="good-id" name="id" value="{{ $good->id }}" />
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab1" data-toggle="tab">商品信息</a></li>
			<li><a href="#tab2" data-toggle="tab">描述</a></li>
			<li><a href="#tab3" data-toggle="tab">营销选项</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="tab1">
				<table class="table form-table">
					<colgroup>
						<col width="130px" />
						<col />
					</colgroup>

					<tr>
						<th>商品名称：</th>
						<td>
							<input class="form-control" name="name" type="text" value="{{ $good->name }}"/>
						</td>
					</tr>
					<tr>
						<th>虚实类型：</th>
						<td>
                            <label class="radio-inline">
                                <input type="radio" name="virtualType" value="1" @if($good->virtual_type != 0) checked @endif>实体商品
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="virtualType" value="0" @if($good->virtual_type == 0) checked @endif>虚拟商品
                            </label>
						</td>
					</tr>
					<tr class="good-state">
						<th>是否上架：</th>
						<td>
                            <label class="radio-inline">
                                <input type="radio" name="state" value="0" @if($good->state != 2) checked @endif>是
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="state" value="2" @if($good->state == 2) checked @endif>否
                            </label>
                            <p class="help-block">只有上架的商品才会在前台显示出来，客户是无法看到下架商品</p>
						</td>
					</tr>
					<tr>
						<th>商品推荐：</th>
						<td>
							<label class="checkbox-inline"><input name="hot" type="checkbox" value="1" @if($good->hot == 1) checked @endif>热卖</label>
							<label class="checkbox-inline"><input name="new" type="checkbox" value="1" @if($good->new == 1) checked @endif>新品 </label>
							<label class="checkbox-inline"><input name="best" type="checkbox" value="1" @if($good->best == 1) checked @endif>精品 </label>
							<label class="checkbox-inline"><input name="recommend" type="checkbox" value="1" @if($good->recommend == 1) checked @endif>推荐 </label>
							<label class="checkbox-inline"><input name="freeshipping" type="checkbox" value="1" @if($good->freeshipping == 1) checked @endif>包邮 </label>
						</td>
					</tr>
					<tr>
						<th>排序：</th>
						<td>
                            <input class="form-control" type="text" name="sort" value="{{ $good->sort }}" onkeyup="onlyNum(this)"/>
						</td>
					</tr>
					<tr>
						<th>所属商户：</th>
						<td>
							<select class="form-control" name="merchantId">
								<option value="0">52gai自营 </option>
							</select>
						</td>
					</tr>
					<tr>
						<th>所属品牌：</th>
						<td>
							<select class="form-control" name="brandId">
								<option value="0">选择品牌</option>
								@foreach ($brandList as $brand)
									<option value="{{ $brand->id }}" @if($good->brand_id == $brand->id) selected @endif>{{ $brand->short_name }}</option>
								@endforeach
							</select>
						</td>
					</tr>
					<tr>
						<th>所属分类：</th>
						<td>
							<div class="category-box" style="margin-bottom:8px">
								<ctrlarea style="margin-right:5px;">
									<input type="hidden" value="{{ $good->category->id }}" name="goodCategoryId">
									<button class="btn btn-default category-del" type="button"><span>{{ $good->category->name }}</span></button>
								</ctrlarea>
							</div>
							<button class="btn btn-primary goods-category-button" type="button"><i class="fa fa-list"></i> 选择分类</button>
						</td>
					</tr>
					<tr>
						<th>商品模型：</th>
						<td>
							<select class="form-control model-select" name="modelTypeId">
								<option value="0">通用类型 </option>
								@foreach ($modelList as $model)
									<option value="{{ $model->id }}" @if($good->goods_type == $model->id) selected @endif>{{ $model->name }}</option>
								@endforeach
							</select>
							<input type="hidden" value="{{ $good->goods_type }}" class="current-model-id" />
						</td>
					</tr>
					<tr>
						<th>商品属性：</th>
						<td class="good-attribute-table">
							<table class="table table-bordered">
								<tbody class="good-attribute-tbody">
									@if (isset($goodAttrs) && count($goodAttrs) > 0)
										@foreach ($goodAttrs as $attr)
											<tr>
												<td>{{ $attr['name'] }}</td>
												@if ($attr['type'] == 1)
													<td>
														@foreach ($attr['values'] as $value)
															<label class="radio-inline">
																<input type="radio" name="good[attribute][{{ $attr['id'] }}][]" value="{{ (is_array($value)) ? $value['value'] : $value }}" @if (is_array($value)) checked @endif/>{{ (is_array($value)) ? $value['value'] : $value }}
															</label>
														@endforeach
													</td>
												@elseif ($attr['type'] == 2)
													<td>
														@foreach ($attr['values'] as $value)
															<label class="checkbox-inline">
																<input type="checkbox" name="good[attribute][{{ $attr['id'] }}][]" value="{{ (is_array($value)) ? $value['value'] : $value }}" @if (is_array($value)) checked @endif/>{{ (is_array($value)) ? $value['value'] : $value }}
															</label>
														@endforeach
													</td>
												@elseif ($attr['type'] == 3)
													<td>
														<select class="form-control" name="good[attribute][{{ $attr['id'] }}][]">
															@foreach ($attr['values'] as $value)
																<option value="{{ (is_array($value)) ? $value['value'] : $value }}" @if (is_array($value)) selected @endif>{{ (is_array($value)) ? $value['value'] : $value }}</option>
															@endforeach
														</select>
													</td>
												@else
													<td>
														<input class="form-control" type="text" name="good[attribute][{{ $attr['id'] }}][]" value="{{ count($attr['values']) > 0 ? ((is_array($attr['values'][0])) ? $attr['values'][0]['value'] : $attr['values'][0]) : '' }}"/>
													</td>
												@endif
											</tr>
										@endforeach
									@endif
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<th>商品sku：</th>
						<td class="good-sku-table">
							<p class="help-block">*sku库存或销售价格为0,sku默认下架</p>
							<table class="table table-bordered">
								<thead class="good-sku-thead">
									<tr>
										<td style="width: 10%">sku名称</td>
										<td style="width: 9%">sku编码（料号）</td>
										<td style="width: 9%">sku条码</td>
										@if (isset($goodSpecs) && isset($goodSpecs['specNames']) && count($goodSpecs['specNames']) > 0)
										@foreach ($goodSpecs['specNames'] as $specName)
										<td class="good-sku-thead-number good-sku-spec" style="width: 5%">{{ $specName }}</td>
										@endforeach
										@endif
										<td class="good-sku-thead-number" style="width: 5%">库存</td>
										<td style="width: 5%">预警库存</td>
										<td style="width: 5%">销售价格</td>
										<td style="width: 5%">会员价格</td>
										<td style="width: 5%">批发价格</td>
										<td style="width: 5%">成本价格</td>
										<td style="width: 5%">重量(克)</td>
										<td style="width: 6%">状态</td>
										<td style="width: 3%">图片</td>
										<td style="width: 6%">关联旧数据</td>
									</tr>
								</thead>
								<tbody class="good-sku-tbody">
									@if (isset($goodSpecs) && isset($goodSpecs['specs']) && count($goodSpecs['specs']) > 0)
									@foreach ($goodSpecs['specs'] as $spec)
										<tr>
											<td>
												<input name="goodSku[id][]" type="hidden" value="{{ $spec['relationSpecId'] }}"/>
												<input class="form-control input-sm" name="goodSku[name][]" type="text" value="{{ $spec['name'] }}"/>
											</td>
											<td><input class="form-control input-sm" name="goodSku[custNo][]" type="text" value="{{ $spec['custNo'] }}"/></td>
						                    <td><input class="form-control input-sm" name="goodSku[barCode][]" type="text" value="{{ $spec['barCode'] }}"/></td>
						                    @if (isset($spec['idKeyValues']) && count($spec['idKeyValues']) > 0)
											@foreach ($spec['idKeyValues'] as $key => $value)
											@if ($key > 0)
											<td class="good-sku-tbody-spec good-sku-tbody-spec-{{ $key }}">{{ $value }}<input name="goodSku[spec][{{ $key }}][]" type="hidden" value="{{ $value }}"/></td>
											@endif
											@endforeach
						                    @endif
						                    <td class="good-sku-tbody-number">
												<input name="goodSku[specIds][]" type="hidden" value="{{ $spec['attrIds'] }}"/>
												<input name="goodSku[specValues][]" type="hidden" value="{{ $spec['values'] }}"/>
						                    	<input class="form-control input-sm" name="goodSku[storeNum][]" type="text" value="{{ $spec['storeNum'] }}" onkeyup="onlyNum(this)"/>
						                    </td>
						                    <td><input class="form-control input-sm" name="goodSku[warningNum][]" type="text" value="{{ $spec['warningNum'] }}" onkeyup="onlyNum(this)"/></td>
						                    <td><input class="form-control input-sm" name="goodSku[sellPrice][]" type="text" value="{{ $spec['sellPrice'] }}" onkeyup="onlyAmount(this)"/></td>
						                    <td><input class="form-control input-sm" name="goodSku[memberPrice][]" type="text" value="{{ $spec['memberPrice'] }}" onkeyup="onlyAmount(this)"/></td>
						                    <td><input class="form-control input-sm" name="goodSku[wholesalePrice][]" type="text" value="{{ $spec['wholesalePrice'] }}" onkeyup="onlyAmount(this)"/></td>
						                    <td><input class="form-control input-sm" name="goodSku[costPrice][]" type="text" value="{{ $spec['costPrice'] }}" onkeyup="onlyAmount(this)"/></td>
						                    <td><input class="form-control input-sm" name="goodSku[weight][]" type="text" value="{{ $spec['weight'] }}" onkeyup="onlyNum(this)"/></td>
						                    <td class="good-sku-state">
						                    	<select class="form-control input-sm" name="goodSku[state][]">
													<option value="0" @if($spec['state'] != 2) selected @endif>上架</option>
													<option value="2" @if($spec['state'] == 2) selected @endif>下架</option>
												</select>
											</td>
											<td>
												<input class="good-sku-spec-ids-str" type="hidden" value="{{ $spec['valueStr'] }}"/>
												<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#goodSku{{ $spec['valueStr'] }}" type="button">图片</button>
												<div class="modal fade" id="goodSku{{ $spec['valueStr'] }}" tabindex="-1" role="dialog" aria-labelledby="goodSku{{ $spec['valueStr'] }}Label" aria-hidden="true">
													<div class="modal-dialog modal-lg">
														<div class="modal-content">
															<div class="modal-header">
																<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
																<h4 class="modal-title" id="goodSku{{ $spec['valueStr'] }}Label">显示/上传商品Sku图片</h4>
															</div>
															<div class="modal-body">
																<input class="fileUpload file-loading" type="file" name="file" multiple>
																<input type="hidden" value="{{ $spec['values'] }}"/>
																@if(isset($spec['imgs']) && count(json_decode($spec['imgs'])) > 0)
																	@foreach (json_decode($spec['imgs']) as $img)
																		<div class="pic pull-left">
																			<img class="img-thumbnail" style="margin-top:12px;margin-right:14px;width:160px;height:160px" src="{{ $img . '?x-oss-process=image/resize,w_160,h_160' }}" alt="{{ $img . '?x-oss-process=image/resize,w_160,h_160' }}">
																			<p class="text-center">
																				<a href="javascript:void(0);" class="del-good-spec-old-img">
																					<i class="operator fa fa-close" title="删除"></i>
																					<input type="hidden" name="goodSku[pic][{{ $spec['values'] }}][]" value="{{ $img }}"/>
																				</a>
																			</p>
																		</div>
																	@endforeach
																@endif
															</div>
															<div class="modal-footer" style="clear:both;">
																<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
															</div>
														</div>
													</div>
												</div>
											</td>
											<td>
												@if ($spec['relationSpecId'] == 0)
													<button class="btn btn-primary btn-sm good-sku-new-relation-spec" type="button">关联</button>
												@endif
											</td>
										</tr>
									@endforeach
									@endif
								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div class="tab-pane" id="tab2">
				<table class="table form-table">
					<colgroup>
						<col width="130px" />
						<col />
					</colgroup>
					<tr>
						<th>产品描述：</th>
						<td><textarea id="container" name="content" style="width:100%;height:400px;">{{ $good->content }}</textarea></td>
					</tr>
				</table>
			</div>

			<div class="tab-pane" id="tab3">
				<table class="table form-table">
					<colgroup>
						<col width="130px" />
						<col />
					</colgroup>

					<tr>
						<th>SEO关键词：</th><td><input class="form-control" name="keywords" type="text" value="{{ $good->keywords }}" /><span>用于SEO，多个关键字请用英文逗号分隔</span></td>
					</tr>
					<tr>
						<th>SEO描述：</th><td><textarea class="form-control" name="description">{{ $good->description }}</textarea></td>
					</tr>
				</table>
			</div>
		</div>

		<div class="text-center">
			<button class='btn btn-primary good-submit' type="submit">发布商品</button>
		</div>
	</div>
	</form>
</div>
<div class="hidden good-attribute-current-template">
<table class="table table-bordered">
	<tbody class="good-attribute-tbody">
		@if (isset($goodAttrs) && count($goodAttrs) > 0)
			@foreach ($goodAttrs as $attr)
				<tr>
					<td>{{ $attr['name'] }}</td>
					@if ($attr['type'] == 1)
						<td>
							@foreach ($attr['values'] as $value)
								<label class="radio-inline">
									<input type="radio" name="good[attribute][{{ $attr['id'] }}][]" value="{{ (is_array($value)) ? $value['value'] : $value }}" @if (is_array($value)) checked @endif/>{{ (is_array($value)) ? $value['value'] : $value }}
								</label>
							@endforeach
						</td>
					@elseif ($attr['type'] == 2)
						<td>
							@foreach ($attr['values'] as $value)
								<label class="checkbox-inline">
									<input type="checkbox" name="good[attribute][{{ $attr['id'] }}][]" value="{{ (is_array($value)) ? $value['value'] : $value }}" @if (is_array($value)) checked @endif/>{{ (is_array($value)) ? $value['value'] : $value }}
								</label>
							@endforeach
						</td>
					@elseif ($attr['type'] == 3)
						<td>
							<select class="form-control" name="good[attribute][{{ $attr['id'] }}][]">
								@foreach ($attr['values'] as $value)
									<option value="{{ (is_array($value)) ? $value['value'] : $value }}" @if (is_array($value)) selected @endif>{{ (is_array($value)) ? $value['value'] : $value }}</option>
								@endforeach
							</select>
						</td>
					@else
						<td>
							<input class="form-control" type="text" name="good[attribute][{{ $attr['id'] }}][]" value="{{ count($attr['values']) > 0 ? ((is_array($attr['values'][0])) ? $attr['values'][0]['value'] : $attr['values'][0]) : '' }}"/>
						</td>
					@endif
				</tr>
			@endforeach
		@endif
	</tbody>
</table>
</div>
<div class="hidden good-sku-current-template">
<p class="help-block">*sku库存或销售价格为0,sku默认下架</p>
<table class="table table-bordered">
	<thead class="good-sku-thead">
		<tr>
			<td style="width: 10%">sku名称</td>
			<td style="width: 9%">sku编码</td>
			<td style="width: 9%">sku条码</td>
			@if (isset($goodSpecs) && isset($goodSpecs['specNames']) && count($goodSpecs['specNames']) > 0)
			@foreach ($goodSpecs['specNames'] as $specName)
			<td class="good-sku-thead-number good-sku-spec" style="width: 5%">{{ $specName }}</td>
			@endforeach
			@endif
			<td class="good-sku-thead-number" style="width: 5%">库存</td>
			<td style="width: 5%">预警库存</td>
			<td style="width: 5%">销售价格</td>
			<td style="width: 5%">会员价格</td>
			<td style="width: 5%">批发价格</td>
			<td style="width: 5%">成本价格</td>
			<td style="width: 5%">重量(克)</td>
			<td style="width: 5%">状态</td>
			<td style="width: 5%">图片</td>
			<td style="width: 6%">关联旧数据</td>
		</tr>
	</thead>
	<tbody class="good-sku-tbody">
		@if (isset($goodSpecs) && isset($goodSpecs['specs']) && count($goodSpecs['specs']) > 0)
		@foreach ($goodSpecs['specs'] as $spec)
			<tr>
				<td>
					<input name="goodSku[id][]" type="hidden" value="{{ $spec['relationSpecId'] }}"/>
					<input class="form-control input-sm" name="goodSku[name][]" type="text" value="{{ $spec['name'] }}"/>
				</td>
				<td><input class="form-control input-sm" name="goodSku[custNo][]" type="text" value="{{ $spec['custNo'] }}"/></td>
                <td><input class="form-control input-sm" name="goodSku[barCode][]" type="text" value="{{ $spec['barCode'] }}"/></td>
                @if (isset($spec['idKeyValues']) && count($spec['idKeyValues']) > 0)
				@foreach ($spec['idKeyValues'] as $key => $value)
				@if ($key > 0)
				<td class="good-sku-tbody-spec good-sku-tbody-spec-{{ $key }}">{{ $value }}<input name="goodSku[spec][{{ $key }}][]" type="hidden" value="{{ $value }}"/></td>
				@endif
				@endforeach
                @endif
                <td class="good-sku-tbody-number">
					<input name="goodSku[specIds][]" type="hidden" value="{{ $spec['attrIds'] }}"/>
					<input name="goodSku[specValues][]" type="hidden" value="{{ $spec['values'] }}"/>
                	<input class="form-control input-sm" name="goodSku[storeNum][]" type="text" value="{{ $spec['storeNum'] }}" onkeyup="onlyNum(this)"/>
                </td>
                <td><input class="form-control input-sm" name="goodSku[warningNum][]" type="text" value="{{ $spec['warningNum'] }}" onkeyup="onlyNum(this)"/></td>
                <td><input class="form-control input-sm" name="goodSku[sellPrice][]" type="text" value="{{ $spec['sellPrice'] }}" onkeyup="onlyAmount(this)"/></td>
                <td><input class="form-control input-sm" name="goodSku[memberPrice][]" type="text" value="{{ $spec['memberPrice'] }}" onkeyup="onlyAmount(this)"/></td>
                <td><input class="form-control input-sm" name="goodSku[wholesalePrice][]" type="text" value="{{ $spec['wholesalePrice'] }}" onkeyup="onlyAmount(this)"/></td>
                <td><input class="form-control input-sm" name="goodSku[costPrice][]" type="text" value="{{ $spec['costPrice'] }}" onkeyup="onlyAmount(this)"/></td>
                <td><input class="form-control input-sm" name="goodSku[weight][]" type="text" value="{{ $spec['weight'] }}" onkeyup="onlyNum(this)"/></td>
                <td class="good-sku-state">
                	<select class="form-control input-sm" name="goodSku[state][]">
						<option value="0" @if($spec['state'] != 2) checked @endif>上架</option>
						<option value="2" @if($spec['state'] == 2) checked @endif>下架</option>
					</select>
				</td>
				<td>
					<input class="good-sku-spec-ids-str" type="hidden" value="{{ $spec['valueStr'] }}"/>
					<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#goodSku{{ $spec['valueStr'] }}" type="button">图片</button>
					<div class="modal fade" id="goodSku{{ $spec['valueStr'] }}" tabindex="-1" role="dialog" aria-labelledby="goodSku{{ $spec['valueStr'] }}Label" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h4 class="modal-title" id="goodSku{{ $spec['valueStr'] }}Label">显示/上传商品Sku图片</h4>
								</div>
								<div class="modal-body">
									<input class="fileUpload file-loading" type="file" name="file" multiple>
									<input type="hidden" value="{{ $spec['values'] }}"/>
									@if(isset($spec['imgs']) && count(json_decode($spec['imgs'])) > 0)
										@foreach (json_decode($spec['imgs']) as $img)
											<div class="pic pull-left">
												<img class="img-thumbnail" style="margin-top:12px;margin-right:14px;width:160px;height:160px" src="{{ $img . '?x-oss-process=image/resize,w_160,h_160' }}" alt="{{ $img . '?x-oss-process=image/resize,w_160,h_160' }}">
												<p class="text-center">
													<a href="javascript:void(0);" class="del-good-spec-old-img">
														<i class="operator fa fa-close" title="删除"></i>
														<input type="hidden" name="goodSku[pic][{{ $spec['values'] }}][]" value="{{ $img }}"/>
													</a>
												</p>
											</div>
										@endforeach
									@endif
								</div>
								<div class="modal-footer" style="clear:both;">
									<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
								</div>
							</div>
						</div>
					</div>
				</td>
				<td>
					@if ($spec['relationSpecId'] == 0)
						<button class="btn btn-primary btn-sm good-sku-new-relation-spec" type="button">关联</button>
					@endif
				</td>
			</tr>
		@endforeach
		@endif
	</tbody>
</table>
</div>
<div class="modal fade" id="oldGoodSku" tabindex="-1" role="dialog" aria-labelledby="oldGoodSkuLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="oldGoodSkuLabel">旧sku</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered">
					<thead class="old-good-sku-thead">
						<tr>
							<td style="width: 10%">sku名称</td>
							<td style="width: 9%">sku编码</td>
							<td style="width: 9%">sku条码</td>
							@if (isset($goodSpecs) && isset($goodSpecs['skuSpecNames']) && count($goodSpecs['skuSpecNames']) > 0)
							@foreach ($goodSpecs['skuSpecNames'] as $specName)
							<td style="width: 5%">{{ $specName }}</td>
							@endforeach
							@endif
							<td style="width: 5%">库存</td>
							<td style="width: 6%">预警数</td>
							<td style="width: 5%">销售价</td>
							<td style="width: 5%">会员价</td>
							<td style="width: 5%">批发价</td>
							<td style="width: 5%">成本价</td>
							<td style="width: 6%">重量(克)</td>
							<td style="width: 5%">状态</td>
						</tr>
					</thead>
					<tbody class="old-good-sku-tbody">
						@if (isset($goodSpecs) && isset($goodSpecs['skus']) && count($goodSpecs['skus']) > 0)
						@foreach ($goodSpecs['skus'] as $spec)
							<tr>
								<td class="good-radio">
									<div class="radio">
									<label>
									<input type="radio" class="old-good-sku-id" value="{{ $spec['id'] }}" @if($spec['relationValues'] != 0) disabled @endif/>
									{{ $spec['name'] }}
									<input class="old-good-sku-name" type="hidden" value="{{ $spec['name'] }}"/>
									<input class="old-good-sku-relation-attr-values" type="hidden" value="{{ $spec['relationValues'] }}"/>
									</label>
									</div>
								</td>
								<td>{{ $spec['custNo'] }}<input class="old-good-sku-cust-no" type="hidden" value="{{ $spec['custNo'] }}"/></td>
			                    <td>{{ $spec['barCode'] }}<input class="old-good-sku-bar-code" type="hidden" value="{{ $spec['barCode'] }}"/></td>
			                    @if (isset($spec['idKeyValues']) && count($spec['idKeyValues']) > 0)
								@foreach ($spec['idKeyValues'] as $key => $value)
								@if ($key > 0)
								<td>{{ $value }}<input class="old-good-sku-spec-value" type="hidden" value="{{ $value }}"/></td>
								@endif
								@endforeach
			                    @endif
			                    <td>
			                    	{{ $spec['storeNum'] }}
									<input class="old-good-sku-spec-ids" type="hidden" value="{{ $spec['attrIds'] }}"/>
									<input class="old-good-sku-spec-values" type="hidden" value="{{ $spec['values'] }}"/>
			                    	<input class="old-good-sku-store-num" type="hidden"  value="{{ $spec['storeNum'] }}"/>
			                    </td>
			                    <td>{{ $spec['warningNum'] }}<input class="old-good-sku-warning-num" type="hidden" value="{{ $spec['warningNum'] }}"/></td>
			                    <td>{{ $spec['sellPrice'] }}<input class="old-good-sku-sell-price" type="hidden" value="{{ $spec['sellPrice'] }}"/></td>
			                    <td>{{ $spec['memberPrice'] }}<input class="old-good-sku-member-price" type="hidden" value="{{ $spec['memberPrice'] }}"/></td>
			                    <td>{{ $spec['wholesalePrice'] }}<input class="old-good-sku-wholesale-price" type="hidden" value="{{ $spec['wholesalePrice'] }}"/></td>
			                    <td>{{ $spec['costPrice'] }}<input class="old-good-sku-cost-price" type="hidden" value="{{ $spec['costPrice'] }}"/></td>
			                    <td>{{ $spec['weight'] }}<input class="old-good-sku-weight" type="hidden" value="{{ $spec['weight'] }}"/></td>
			                    <td>@if($spec['state'] != 2) 上架  @else 下架 @endif<input class="old-good-sku-state" type="hidden" value="{{ $spec['state'] }}"/>
									@if(isset($spec['imgs']) && count(json_decode($spec['imgs'])) > 0)
										@foreach (json_decode($spec['imgs']) as $img)
											<input class="old-good-sku-img" type="hidden" value="{{ $img }}"/>
										@endforeach
									@endif
			                    </td>
							</tr>
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-default good-sku-new-relation-spec-close" data-dismiss="modal">关闭</button>
				<button type="button" class="btn btn-sm btn-danger good-sku-new-relation-spec-submit" data-dismiss="modal">确定</button>
			</div>
		</div>
	</div>
</div>
@include('include.message')
@include('admins.goods.include.categoryDialog')
<script src="{{ asset('js/common/fileinput/js/plugins/sortable.js') }}"></script>
<script src="{{ asset('js/common/fileinput/js/fileinput.js') }}"></script>
<script src="{{ asset('js/common/fileinput/js/locales/zh.js') }}"></script>
<script src="{{ asset('js/common/fileinput/themes/explorer/theme.js') }}"></script>
<link href="{{ asset('js/common/ueditor/themes/default/css/ueditor.css') }}" rel="stylesheet">
<script src="{{ asset('js/common/ueditor/ueditor.config.js') }}"></script>
<script src="{{ asset('js/common/ueditor/ueditor.all.js') }}"></script>
<script type="text/javascript">
	var ue = UE.getEditor('container',{
	    initialFrameWidth : '100%',//宽度
	    initialFrameHeight: 400//高度
	});
</script>
<script src="{{ elixir('js/common/fileUpload.js') }}"></script>
<script src="{{ elixir('js/admins/goodCategory.js') }}"></script>
<script src="{{ elixir('js/admins/good.js') }}"></script>
<script src="{{ elixir('js/admins/editGood.js') }}"></script>
@include('admins.footer')
