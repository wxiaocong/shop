<footer class="zyw-footer">
    <div class="zyw-container white-bgcolor">
        <div class="weui-tabbar">
            <a href="/" class="weui-tabbar__item @if(Request::path() == '/')  weui-bar__item--on @endif">
                <div class="weui-tabbar__icon"><i class="iconfont icon-home"></i></div>
                <p class="weui-tabbar__label">首页</p>
            </a>
            <a href="/category" class="weui-tabbar__item @if(strstr(Request::path(),'category'))  weui-bar__item--on @endif">
                <div class="weui-tabbar__icon"><i class="iconfont icon-leimupinleifenleileibie"></i></div>
                <p class="weui-tabbar__label">分类</p>
            </a>
            <a href="/home" class="weui-tabbar__item @if(strstr(Request::path(),'home'))  weui-bar__item--on @endif">
                <div class="weui-tabbar__icon"><i class="iconfont icon-weibiaoti2fuzhi12"></i></div>
                <p class="weui-tabbar__label">我的</p>
            </a>
        </div>
    </div>
</footer>
<script src="{{ elixir('js/users/jquery.min.js') }}"></script>
<script src="{{ elixir('js/users/jquery-weui.min.js') }}"></script>
<script src="{{ elixir('js/users/bootstrap.min.js') }}"></script>
<script src="{{elixir('js/users/front.js')}}"></script>
<script src="https://s22.cnzz.com/z_stat.php?id=1275143858&web_id=1275143858" language="JavaScript"></script>
</body>
</html>
