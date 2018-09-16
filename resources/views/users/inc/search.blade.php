<div class="weui-search-bar" id="searchBar">
  <form action="/goods/search" method="post" class="weui-search-bar__form">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="weui-search-bar__box">
      <i class="weui-icon-search"></i>
      <input type="search" name="searchKey" value="{{$searchKey ?? ''}}" class="weui-search-bar__input" id="searchInput" placeholder="输入商品名称或料号" required="">
      <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
    </div>
    <label class="weui-search-bar__label" id="searchText">
      <i class="weui-icon-search"></i>
      <span>{{$searchKey ?? '搜索'}}</span>
    </label>
  </form>
  <a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
</div>