<?php

namespace App\Http\Middleware;

use Closure;

class RouteHistory
{
    private $notRecordRoute = array('address'); //不记录
    private $toHistoryBack = array('purchase'); //返回上一页
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //ajax不记录
        if(! $request->ajax()){
            $referer = empty($_SERVER['HTTP_REFERER']) ? env('APP_URL') : $_SERVER['HTTP_REFERER'];
            $routeHistory = array(); //历史记录
            if (!empty($_COOKIE['routeHistory'])) {
                $routeHistory = unserialize($_COOKIE['routeHistory']);
            }
            $flag = true; //是否添加
            //不记录
            if(!empty($this->notRecordRoute)) {
                foreach ($this->notRecordRoute as $route) {
                    if (strpos($referer, $route) !== false) {
                        $flag = false;
                        //如果是返回,删除最后一个
                        if (strpos(end($routeHistory), $_SERVER['REQUEST_URI']) !== false) {
                            array_pop($routeHistory);
                            $_COOKIE['lastRecord'] = "self.location='" . (end($routeHistory) ?? env('APP_URL')) ."'";
                            setcookie('routeHistory', serialize($routeHistory),time()+3600*24,'/');
                            setcookie('lastRecord', $_COOKIE['lastRecord'], time()+3600*24,'/');
                        }
                        break;
                    }
                }
            }
            if ($flag) {
                //刷新页面不记录
                if ($referer == env('APP_URL') || strpos($referer, $_SERVER['REQUEST_URI']) === false) {
                    if (strpos(end($routeHistory), $_SERVER['REQUEST_URI']) !== false) {
                        //返回上一页删除最后一个
                        array_pop($routeHistory);
                        $_COOKIE['lastRecord'] = "self.location='" . (end($routeHistory) ?? env('APP_URL')) ."'";
                    } else {
                        array_push($routeHistory, $referer);
                        if (count($routeHistory) > 10) { //最多记录10条
                            array_shift($routeHistory);
                        }
                        $_COOKIE['lastRecord'] = "self.location='" . $referer ."'";
                    }
                    //history(-1) 表单
                    if(!empty($this->toHistoryBack)) {
                        foreach ($this->toHistoryBack as $back) {
                            if (strpos($_COOKIE['lastRecord'], $back) !== false) {
                                $_COOKIE['lastRecord'] = "history.back(-1)";
                            }
                        }
                    }
                    setcookie('routeHistory', serialize($routeHistory),time()+3600*24,'/');
                    setcookie('lastRecord', $_COOKIE['lastRecord'], time()+3600*24,'/');
                }
            }
        };
        return $next($request);
    }
}
