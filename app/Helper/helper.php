<?php
if (!function_exists('translateStatus')) {
    /**
     * Translate the status from char to text to display in the front end.
     *
     * @param [string] $key
     *            what type of the status belongs to.
     * @param [string] $code
     *            the status code.
     *
     * @return [string] The text of the code.
     */
    function translateStatus($key, $code)
    {
        $statuses = config('statuses.' . $key);
        if ($statuses == null) {
            return '';
        }

        foreach ($statuses as $key => $value) {
            if ($value['code'] == $code) {
                return $value['text'];
            }
        }
        return '';
    }
}

if (!function_exists('trimSpace')) {
    /**
     * 去掉前后中英文空格
     *
     * @param string $date
     *            yyyy-mm-dd defaut ''
     *
     * @return int
     */
    function trimSpace($str = '')
    {
        $str = mb_ereg_replace('^(　| )+', '', $str);
        $str = mb_ereg_replace('(　| )+$', '', $str);
        return trim($str);
    }
}

if (!function_exists('getTree')) {
    /**
     * 生成树
     *
     * @param array $data
     * @param int $pId
     *
     * @return array
     */
    function getTree($data)
    {
        $tree = $items = array();
        //构造数据
        foreach ($data as $value) {
            $items[$value['id']] = $value;
        }

        //遍历数据 生成树状结构
        foreach ($items as $item) {
            if (isset($items[$item['parent_id']])) {
                $items[$item['parent_id']]['sub'][] = &$items[$item['id']];
            } else {
                $tree[] = &$items[$item['id']];
            }
        }
        return $tree;

    }
}

if (!function_exists('getRealIp')) {
    /**
     * 获取真实ip
     *
     * @return string
     */
    function getRealIp()
    {
        $realip = '';
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        return $realip;
    }
}

if (!function_exists('createOrderSn')) {
    /**
     * 创建唯一订单号
     *
     * @return string
     */
    function createOrderSn()
    {
        return date('YmdHis') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}

if (!function_exists('createOutTradeNo')) {
    /**
     * 创建唯一商户订单号
     *
     * @return string
     */
    function createOutTradeNo($orderSn = '')
    {
        return $orderSn . date('dHis') . rand(10,99);
    }
}

if (!function_exists('isWeixin')) {
    /**
     * 是否微信浏览器
     * @return boolean
     */
    function isWeixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
}

if (!function_exists('replaceSpecialChar')) {
    /**
     * 替换特殊字符
     * @param string $param
     *
     * @return string
     */
    function replaceSpecialChar($param)
    {
        $regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\s|\.|\/|\;|\'|\`|\=|\\\|\|/";
        return preg_replace($regex, '', $param);
    }
}
