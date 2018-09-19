<?php

namespace App\Http\Middleware;

use App\Services\Admins\AdminRightService;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redis;

class AdminMenuSelect
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            return $next($request);
        } else {
            if (!Redis::exists('menu.select')) {
                $menus = $this->generateRedisMenu();
                Redis::setex('menu.select', (60 * 60 * 24 * 30), serialize($menus)); //保存一个月
            }

            $action = $request->route()->getActionName();
            $menus  = unserialize(Redis::get('menu.select'));
            if (array_key_exists($action, $menus)) {
                $menu = array();
                foreach ($menus[$action] as $key => $menuArr) {
                    $menu = $menuArr;
                    break;
                }
                session(array('currentTopMenuId' => $menu['topMenuId']));
                session(array('currentTopMenuName' => $menu['topMenuName']));
                setcookie('selectedSubMenu', $menu['menuId'], time() + 24 * 60 * 60 * 1000, '/');
            }
        }

        return $next($request);
    }

    private function generateRedisMenu()
    {
        $rights = AdminRightService::findByParams(array('orderBy' => array('category_id' => 'asc')));

        //将所有用作菜单的action及其相对应的一二级分类整理出来
        $showMenuActions = array();
        foreach ($rights as $right) {
            if ($right->show_menu == config('statuses.adminRight.showMenu.yes.code')) {
                $actions = explode(';', $right->action);

                $secondRightCategory          = $right->rightCategory;
                $topRightCategory             = $secondRightCategory->parentCategory;
                $showMenuActions[$actions[0]] = array(
                    'menuId'         => $right->id,
                    'topMenuId'      => $topRightCategory->id,
                    'topMenuName'    => $topRightCategory->name,
                    'secondMenuId'   => $secondRightCategory->id,
                    'secondMenuName' => $secondRightCategory->name,
                );
            }
        }

        //将所有action及其相对应的一二级分类整理出来
        $actionAll = array();
        foreach ($rights as $right) {
            $actionArr = array();
            $actions   = explode(';', $right->action);
            if ($right->show_menu != config('statuses.adminRight.showMenu.yes.code')) {
                $str = substr($actions[0], 0, strpos($actions[0], '@')) . '@index';
                if (array_key_exists($str, $showMenuActions)) {
                    $actionArr = $showMenuActions[$str];
                }
            } else {
                $actionArr = $showMenuActions[$actions[0]];
            }
            if (count($actionArr) > 0) {
                foreach ($actions as $action) {
                    $actionAll[$action][$right->id] = $actionArr;
                }
            }
        }

        return $actionAll;
    }
}
