@forelse ($secondCategoryList as $v)
<a href="/category/{{ $v->id }}">
<div class="col-xs-6 info-item">
    <div class="category-img"><img src="{{empty($v->pic)?'/images/users/carnetmotors.jpg':$v->pic}}" alt=""></div>
    <div class="category-title">{{ mb_substr($v->name,0,6) }}</div>
</div>
</a>
@empty
<p>no category</p>
@endforelse