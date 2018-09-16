        </div>
        <!--右侧内容 结束-->

        <!--顶部弹出菜单 开始-->
        <aside class="control-sidebar control-sidebar-dark">
            <ul class="control-sidebar-menu">
                <li><a href="{{url('/admin/logout')}}"><i class="fa fa-circle-o text-red"></i> <span>退出管理</span></a></li>
                <li><a href="{{url('/admin/adminUser/editPwd')}}"><i class="fa fa-circle-o text-yellow"></i> <span>修改密码</span></a></li>
                <li><a href="{{url('/admin/home')}}"><i class="fa fa-circle-o text-green"></i> <span>后台首页</span></a></li>
            </ul>
        </aside>
        <!--顶部弹出菜单 结束-->
    </div>
<script src="{{ elixir('js/52gai.js') }}"></script>
<script src="{{ elixir('js/admins/nav.js') }}"></script>
</body>
</html>
