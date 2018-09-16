<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Services\Admins\AdminCategoryService;
use App\Services\Admins\AdminRightService;
use App\Services\Admins\AdminRoleService;
use App\Services\Admins\AdminUserService;
use App\Services\CaptchaService;
use Hash;
use RSA;
use Session;

class LoginController extends Controller {
    public function index() {
        if (session('adminUser')) {
            return redirect($this->generateRedirectUrl());
        }
        return view('admins.login')
            ->with('captcha', CaptchaService::getCaptcha());
    }

    public function login() {
        $name = trim(request('name', ''));
        $password = trim(request('password', ''));
        $captcha = trim(request('captcha', ''));

        $rightCaptcha = session('captcha', '');
        if (strtoupper($rightCaptcha) !== strtoupper($captcha)) {
            return response()->json(
                array(
                    'code' => 500,
                    'messages' => array('请填写正确验证码'),
                    'url' => '',
                )
            );
        }

        $user = AdminUserService::findByName($name);
        if (!$user) {
            return response()->json(
                array(
                    'code' => 500,
                    'messages' => array('用户名不存在'),
                    'url' => '',
                )
            );
        }

        $password = RSA::decrypt($password);
        if (!Hash::check(env('ADMIN_PASSWORD_SALT') . $password, $user->password)) {
            return response()->json(
                array(
                    'code' => 500,
                    'messages' => array('用户名/密码不正确'),
                    'url' => '',
                )
            );
        }

        //保存登录IP，时间
        $user->last_ip = request()->getClientIp();
        $user->last_time = date('Y-m-d H:i:s');
        $user = AdminUserService::update($user);

        //最终的超级管理员,拥有所有权限
        if ($user->id == 1) {
            //将登录用户存储到session
            $this->setSessionSuperUser($user);
        } else {
            //将登录用户存储到session
            $this->setSessionUser($user);
        }

        return response()->json(
            array(
                'code' => 200,
                'messages' => array('登录成功'),
                'url' => $this->generateRedirectUrl(),
            )
        );
    }

    /**
     * 将登录的超级用户存储到session,最终的超级管理员,拥有所有权限
     * @param App\Models\Admins\AdminUser $user
     */
    private function setSessionSuperUser($user) {
        session(array('adminUser' => $user));
        Session::forget('captcha');

        $rights = AdminRightService::findByParams(array('orderBy' => array('category_id' => 'asc')));
        $rights = $rights->keyBy('id')->toArray();

        //权限action数组
        $actionList = array();
        //处理一个action内容由多个路径组成的数据
        foreach ($rights as $key => $right) {
            $actions = explode(';', $right['action']);
            $actionList = array_merge($actionList, $actions);
        }

        //将用户权限actions保存到session
        $this->setSessionActions(array_unique($actionList));
        //将用户菜单保存到session
        $this->setSessionMenus($rights);
    }

    /**
     * 将登录用户存储到session
     * @param App\Models\Admins\AdminUser $user
     */
    private function setSessionUser($user) {
        session(array('adminUser' => $user));
        Session::forget('captcha');

        //将用户菜单及权限保存到session
        $this->setSessionMenuAndActions($user);
    }

    /**
     * 将用户菜单及权限保存到session
     * @param App\Models\Admins\AdminUser $user
     */
    private function setSessionMenuAndActions($user) {
        $roleIds = $user->roles->pluck('id')->all();
        if (!isset($roleIds) || count($roleIds) == 0) {
            return null;
        }
        $roles = AdminRoleService::findByParams(array('ids' => $roleIds));

        //获取角色所对应的权限，并去重
        $rightList = array();
        foreach ($roles as $role) {
            if (count($role->rights) > 0) {
                foreach ($role->rights->keyBy('id')->toArray() as $key => $right) {
                    $rightList[$key] = $right;
                }
            }
        }

        array_multisort(array_column($rightList, 'category_id'), SORT_ASC, $rightList);

        $actionList = array(); //权限action数组
        //处理一个action内容由多个路径组成的数据
        foreach ($rightList as $key => $right) {
            $actions = explode(';', $right['action']);
            $actionList = array_merge($actionList, $actions);
        }

        //将用户权限actions保存到session
        $this->setSessionActions(array_unique($actionList));
        //将用户菜单保存到session
        $this->setSessionMenus($rightList);
    }

    /**
     * 将登录用户所有权限存储到session
     * @param [array] $actions
     */
    private function setSessionActions($actions) {
        if (isset($actions) and count($actions) > 0) {
            //将actions存储到session
            session(array('adminRightActions' => $actions));
        }
    }

    /**
     * 将登录用户菜单存储到session
     * @param [array] $rights
     */
    private function setSessionMenus($rights) {
        if (isset($rights) and count($rights) > 0) {
            $categoryIds = array_unique(array_column($rights, 'category_id'));

            $params = array('showMenu' => '1', 'ids' => $categoryIds, 'orderBy' => array('sort_num' => 'asc'));
            $rightCategories = AdminCategoryService::findByParams($params);

            //一级菜单
            $topMenus = array();
            //二级菜单
            $leftMenus = array();
            foreach ($rightCategories as $category) {
                if (!array_key_exists($category->parent_id, $topMenus)) {
                    $topMenus[$category->parent_id] = array('id' => $category->parent_id, 'name' => $category->parentCategory->name, 'isShow' => 0);
                }

                $count = 0;
                $subMenus = array();
                foreach ($rights as $right) {
                    if ($category->id == $right['category_id']) {
                        //1-显示
                        if ($right['show_menu'] == '1') {
                            $urls = explode(';', $right['url']);
                            $subMenus[] = array('id' => $right['id'], 'name' => $right['name'], 'url' => $urls[0]);
                        }

                        $count++;
                    } else if ($count > 0) {
                        break;
                    }
                }
                $isShow = 0;
                if (count($subMenus) > 0) {
                    $isShow = 1;
                    $topMenus[$category->parent_id]['isShow'] = $isShow;
                }
                $leftMenus[$category->parent_id][] = array('id' => $category->id, 'name' => $category->name,
                    'icon' => $category->menu_icon, 'subMenus' => $subMenus, 'isShow' => $isShow);
            }

            if (count($topMenus) > 0) {
                //将topMenus存储到session
                session(array('adminTopMenus' => $topMenus));
            }
            if (count($leftMenus) > 0) {
                //将leftMenus存储到session
                session(array('adminLeftMenus' => $leftMenus));
            }
        }
    }

    /**
     * 返回登录后需要跳转的url
     * @return string
     */
    private function generateRedirectUrl() {
        $url = '/admin/home';

        $adminRightActions = session('adminRightActions');
        //有首页权限,跳转到首页
        if (in_array('App\\Http\\Controllers\\Admins\\HomeController@index', $adminRightActions)) {
            return $url;
        }

        $adminTopMenus = session('adminTopMenus');
        $adminLeftMenus = session('adminLeftMenus');
        foreach ($adminLeftMenus as $topMenuId => $leftMenus) {
            foreach ($leftMenus as $leftMenu) {
                //没有首页权限,取第一个菜单url返回
                if ($leftMenu['isShow'] == 1) {
                    $subMenu = $leftMenu['subMenus'][0];

                    session(array('currentTopMenuId' => $topMenuId));
                    session(array('currentTopMenuName' => $adminTopMenus[$topMenuId]['name']));
                    setcookie('selectedSubMenu', $subMenu['id'], time() + 24 * 60 * 60 * 1000, '/');

                    $url = $subMenu['url'];
                    break;
                }
            }
            if ($url != '/admin/home') {
                break;
            }
        }

        return $url;
    }

    /**
     * login out
     *
     * @return Response
     */
    public function logout() {
        Session::forget('adminUser');
        Session::forget('adminRightActions');
        Session::forget('adminTopMenus');
        Session::forget('adminLeftMenus');
        Session::forget('currentTopMenuId');
        Session::forget('currentTopMenuName');

        return redirect('/admin');
    }
}
