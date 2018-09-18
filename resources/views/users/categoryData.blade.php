@forelse ($secondCategoryList as $v)
<a href="/category/{{ $v->id }}">
<div class="col-xs-6 info-item">
    <div class="category-img">@if(!empty($v->pic))<img src="{{$v->pic}}" alt="">@endif</div>
    <div class="category-title">{{ mb_substr($v->name,0,6) }}</div>
</div>
</a>
@empty
<p>no category</p>
@endforelse