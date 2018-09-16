@if (isset($page) && $page->totalRecord > 0)
<div style="clear:both;text-align:center;">
	<div style="margin:0 auto;">
	<ul class="pagination">
		<li><a href="javascript:pageHref(1)">首页</a></li>
		<!-- 上一页 -->
		@if ($page->curPage > 1)
			<li><a href="javascript:pageHref({{ $page->curPage - 1 }})">上一页</a></li>
		@endif
		<!-- 中间循环分页 -->
		@foreach ($page->pageNumbers as $key => $value)
		    @if ($page->curPage == $value)
		        <li class="active"><a href="javascript:pageHref({{ $value }})">{{ $value }}</a></li>
		    @else
		        <li><a href="javascript:pageHref({{ $value }})">{{ $value }}</a></li>
		    @endif
	    @endforeach
		<!-- 下一页 -->
		@if ($page->curPage < $page->totalPage)
			<li><a href="javascript:pageHref({{ $page->curPage + 1 }})">下一页</a></li>
		@endif
		<!-- 尾页 -->
		<li><a href="javascript:pageHref({{ $page->totalPage }})">尾页</a></li>
		<li><a>当前第{{ $page->curPage }}页/共{{ $page->totalPage }}页</a></li>
	</ul>
	</div>
</div>
@endif
