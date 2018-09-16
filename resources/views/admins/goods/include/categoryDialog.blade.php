<div class="hidden category-template">
<style type="text/css">
.cate {font-size: 12px;overflow-x: hidden;width: 580px; height: 380px}
.cate,.cate * {padding: 0;margin: 0;list-style: none;}
.cate .cate-box {padding: 8px;overflow:hidden;}
.cate .cate-box h3 {height: 32px;line-height: 28px;text-indent: 15px;background: #F0AD4E;color: #fff;border: 1px solid #EEA236;border-radius: 5px;text-align: center;}
.cate .cate-box ul {overflow: hidden;}
.cate .cate-box ul.select {width: 32%;float: left;margin-right: 10px;}
.cate .cate-box ul:before {
	display: block;height: 32px;line-height: 30px;content: "顶级分类";text-align: center;
	border: 1px solid #357EBD;background: #428BCA;color: #fff;margin-top: 10px;border-radius: 5px;
}
.cate .cate-box ul + ul:before {content: "二级分类"}
.cate .cate-box ul + ul + ul.select {margin-right: 0;}
.cate .cate-box ul + ul + ul:before {content: "三级分类"}
.cate .cate-box ul li {margin-top: 5px;}
.cate .cate-box ul li label {}
.cate .cate-box ul li label input {display: none;}
.cate .cate-box ul li label span {
	display: block;height: 28px;line-height: 28px;text-align: center;border: 1px solid #ddd;
	border-radius: 5px; padding-left: 5px; padding-right: 5px;
}
.cate .cate-box ul li label span:hover {
	background: #f0f0f0;cursor: pointer;
}
.cate .cate-box ul li label input:checked + span {color: #fff;border-color: #4CAE4C;background: #5CB85C;}
</style>
<div class="cate" id="cate">
	<div class="cate-box">
		<h3>选择分类（选择到二级分类或三级分类）</h3>
		<div class="category-box-dialog">
			<ul class="select categroy-parent-id-0 categroy-one" name="categoryRow">
				@foreach ($categoryList as $category)
					<li onclick="showCategory({{ $category->id }}, 0);"><label><input name="categoryVal" type="radio" value="{{ $category->id }}"/> <span>{{ $category->name }}</span></label></li>
				@endforeach
			</ul>
			<ul class="select categroy-parent-id-1 categroy-second hidden" name="categoryRow">
			</ul>
			<ul class="select categroy-parent-id-3 categroy-three hidden" name="categoryRow">
			</ul>
		</div>
	</div>
</div>
</div>
