@include('users.inc.header')
@include('users.inc.search')
<style type="text/css">
.weui-toast{
    left:60%;
}
.col-xs-4{
    padding-left:5px;
    padding-right:5px;
}
.category-img{
    height:80px;
}
.category-title{
    line-height:20px;
}
.bd-box-info a:nth-child(even) .info-item{
    padding-right:0;
    padding-left:5px;
}
.bd-box-info a:nth-child(odd) .info-item{
    padding-right:5px;
    padding-left:0;
}
.no-category{
    text-align: center;
    color: gray;
}
</style>
<section class="zyw-container">
    <div class="class-cont clearfix">
        <ul id="52Tab" class="nav nav-tabs nav-stacked class-hd">
            @forelse ($firstCategoryList as $val)
                <li @if($val->id == $parent_id) class="active" @endif >
                <a href="#" data="{{ $val->id }}" data-toggle="tab">{{ $val->name }}</a>
                </li>
            @empty
            @endforelse
        </ul>
        <div id="52TabContent" class="tab-content class-bd white-bgcolor">
            <div class="tab-pane fade in active" id="nznz">
                <div class="class-bd-cont">
                    <div class="bd-box">
                        <div class="bd-box-info">
                            <div class="row-content">
                                @if(!empty($secondCategoryList))
                                @foreach ($secondCategoryList as $v)
                                <a href="/category/{{ $v->id }}">
                                <div class="col-xs-6 info-item">
                                    <div class="category-img">@if(!empty($v->pic))<img src="{{$v->pic}}" alt="">@endif</div>
                                    <div class="category-title">{{ mb_substr($v->name,0,6) }}</div>
                                </div>
                                </a>
                                @endforeach
                                @else
                                <p class="no-category">缺少分类数据</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('users.inc.footer')