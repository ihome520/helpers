<?php
/**
 *  一些辅助函数( Some helper functions )
 */

//递归获得分类树
if ( ! function_exists('getTree')) {
    /**
     * 不使用静态数组成员获取子孙树结构
     * User: Clannad ~ ☆
     * @param array $data
     * @param string $pidFName
     * @param string $idFName
     * @param string $levelFName
     * @param int $pid
     * @param int $level
     * @return array
     */
    function getTree(array $data, string $pidFName = 'pid', string $idFName = 'id', string $levelFName = 'level', int $pid = 0, int $level = 0)
    {
        $tree = array();
        foreach ($data as $key => $value) {
            if ($value[$pidFName] == $pid) {
                $value[$levelFName] = $level;
//                $value[$showFName] = str_repeat('&nbsp;&nbsp;', $level) . '|-' . $value[$titleFName];
                $tree[] = $value;
                unset($data[$key]);
                $tempArr = getTree($data, $pidFName, $idFName, $levelFName = 'level', $value[$idFName], $level + 1);
                if ( ! empty($tempArr)) {
                    $tree = array_merge($tree, $tempArr);
                }
            }
        }
        return $tree;
    }
}

if ( ! function_exists('sendMsgToMobile')) {
    /**
     * 发送短信到手机
     * @param string $tel 电话
     * @param string $content 内容
     */
    function sendMsgToMobile(string $tel, string $content = '')
    {
        $postData['userid']   = env('SMS_USER_ID', '');
        $postData['account']  = env('SMS_ACCOUNT', '');
        $postData['password'] = '';
        $postData['content']  = $content;
        //多个手机号码用英文半角豆号‘,’分隔
        $postData['mobile'] = $tel;
        $url                = 'http://sms.kingtto.com:9999/sms.aspx?action=send';
        $o                  = '';
        foreach ($postData as $k => $v) {
            //短信内容需要用urlencode编码，否则可能收到乱码
            $o .= "$k=" . urlencode($v) . '&';
        }
        $postData = substr($o, 0, -1);
        $ch       = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
        $xml = curl_exec($ch);

        //转数组
        $xml    = simplexml_load_string($xml);
        $result = json_decode(json_encode($xml), true);
        return $result;
    }
}

if ( ! function_exists('bcCompNumber')) {

    /**
     * 高精度比较数字的大小
     * User: ihome
     * @param $leftNum float | integer 左边的数字（原比较数）
     * @param $comp string 比较符号 gt 大于 eq 等于 lt 小于
     * @param $rightNum float | integer 右边的数字（被比较数）
     * @return bool 成立返回true 不成立 返回false
     */
    function bcCompNumber(float $leftNum, string $comp = '>', float  $rightNum = 0)
    {
        //左大 +1 等于 0 右大 -1
        switch ($comp) {
            case '>':
                if (bccomp($leftNum, $rightNum, 2) == '1') {
                    return true;
                } else {
                    return false;
                }
                break;
            case '=':
                if (bccomp($leftNum, $rightNum, 2) == '0') {
                    return true;
                } else {
                    return false;
                }
                break;
            case '<':
                if (bccomp($leftNum, $rightNum, 2) == '-1') {
                    return true;
                } else {
                    return false;
                }
                break;
            case '>=':
                if (bccomp($leftNum, $rightNum, 2) == '1' || bccomp($leftNum, $rightNum, 2) == '0') {
                    return true;
                } else {
                    return false;
                }
                break;
            case '<=':
                if (bccomp($leftNum, $rightNum, 2) == '-1' || bccomp($leftNum, $rightNum, 2) == '0') {
                    return true;
                } else {
                    return false;
                }

            default:
                return false;
                break;
        }

    }
}

if ( ! function_exists('createUniqueOrderSn')) {
    /**
     * 生成唯一订单号
     * User: Clannad ~ ☆
     * @return string
     */
    function createUniqueOrderSn()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8) . mt_rand(11111, 99999);
    }
}

if ( ! function_exists('getRandString')) {
    /**
     * 获取随机字符串
     * User: Clannad ~ ☆
     * @param int $length 需要生成的长度
     * @return string
     */
    function getRandString(int $length)
    {
        //字符组合
        $str     = 'ABCDEFGHJKLMNOPQRSTUVWXYZ23456789';
        $len     = strlen($str) - 1;
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
            $num     = mt_rand(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }
}


if ( ! function_exists('writeLog')) {
    /**
     * 记录日志写入文档
     * @param string $str 记录的日志详细信息
     * @param string $msg 日志说明
     */
    function writeLog(string $str, string $msg = "异常处理：")
    {
        if (is_array($str)) {
            $str = json_encode($str, JSON_UNESCAPED_UNICODE);
        }
        $mode = 'a';//追加方式写
        $msg  .= date('Y-m-d H:i:s', time()) . "\n";
        $msg  .= $str . "\n";
        $msg  .= "===========================================\n";

        $file    = './logs/' . date('Y-m-d', time()) . '.txt';
        $oldmask = @umask(0);
        $fp      = @fopen($file, $mode);
        @flock($fp, 3);

        if ( ! $fp) {
            return false;
        } else {
            @fwrite($fp, $msg);
            @fclose($fp);
            @umask($oldmask);
            return true;
        }
    }
}

if ( ! function_exists('getHttpType')) {
    /**
     * 获得http请求类型
     * @return string http:// 或 https://
     */
    function getHttpType()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $http_type;
    }
}

if ( ! function_exists('httpCurl')) {
    /**
     * curl工具请求数据返回
     * User: Clannad ~ ☆
     * @param string $url 请求的url
     * @param string $type 请求的方式GET/POST
     * @param string $res 返回的数据类型
     * @param array $data 请求时的参数
     * @param array $headers 请求头
     * @return mixed|string|void
     */
    function httpCurl(string $url, string $type = 'get', string $res = 'json', array $data = [], array $headers = [])
    {
        // 1,初始化curl
        $ch = curl_init();
        // 2,设置curl参数
        curl_setopt($ch, CURLOPT_URL, $url);//设置采集的url
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//1或者true 表示返回采集内容
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名

        if ($type == "post") {//如果是POST请求，这里定义2个配置项
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        // 3,采集内容
        $output = curl_exec($ch);

        if ($res == "json") {
            if (curl_errno($ch)) {
                // 4,关闭curl
                curl_close($ch);
                //请求失败，返回错误信息
                return curl_error($ch);
            } else {
                //请求成功 返回数据（数组格式）
                return json_decode($output, true);
            }
        }
    }
}

if ( ! function_exists('getFullHost')) {
    /**
     * 获得完整的地址 包含http前缀
     * @return string 例如 http://xxx.xxx.xxx
     */
    function getFullHost()
    {
        return getHttpType() . $_SERVER['HTTP_HOST'];
    }
}

if ( ! function_exists('getAllchildren')) {
    /**
     * 递归获取所有的子分类的ID
     * User: Clannad ~ ☆
     * @param array $array
     * @param int $id
     * @return array
     */
    function getAllchildren(array $array, int $id)
    {
        $arr = array();
        foreach ($array as $v) {
            if ($v['pid'] == $id) {
                $arr[] = $v['id'];
                $arr   = array_merge($arr, getAllchildren($array, $v['id']));
            };
        };
        return $arr;
    }
}

if ( ! function_exists('formatPublishTime')) {
    /**
     * 格式化发布时间
     * User: Clannad ~ ☆
     * @param int $timestamp 时间戳
     * @return string
     */
    function formatPublishTime(int $timestamp)
    {
        if (time() - $timestamp > 86400) {
            $day = floor((time() - $timestamp) / 86400);

            switch ($day) {
                case $day >= 30:
                    return '一个月前发布';
                    break;

                case $day >= 15:
                    return '15天前发布';
                    break;

                case $day >= 7:
                    return '15天前发布';
                    break;

                default:
                    return $day . '天前发布';
                    break;
            }

        }

        if (time() - $timestamp > 3600) {
            return floor((time() - $timestamp) / 3600) . '小时前发布';
        }

        if (time() - $timestamp > 60) {
            //分钟前
            return floor((time() - $timestamp) / 60) . '分钟前发布';
        }

        if (time() - $timestamp < 60) {
            //几秒前
            //return time() - $timestamp . '秒前发布';
            return '刚刚发布';
        }
    }
}

if ( ! function_exists('getLocationAddress')) {
    /**
     * 根据经纬度获取当前地址
     * User: Clannad ~ ☆
     * @param string|float $lat
     * @param string|float $lot
     * @return mixed|string|null
     */
    function getLocationAddress(string $lat, string $lot)
    {
        $url = 'https://restapi.amap.com/v3/geocode/regeo?key=5193a88b5b8f5fb820547d557e925be4&location=' . $lot . ',' . $lat;
        return httpCurl($url);
    }
}

if ( ! function_exists('base64EncodeImage')) {
    /**
     *  图片转base64编码
     * User: ihome
     * @param object $image_file 文件资源
     * @return string
     */
    function base64EncodeImage($imageFile)
    {
        $image_info   = getimagesize($imageFile);
        $image_data   = fread(fopen($imageFile, 'r'), filesize($imageFile));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }
}

if ( ! function_exists('object2array')) {
    /**
     * 对象转数组
     * User: Clannad ~ ☆
     * @param $object
     * @return mixed
     */
    function object2array(object &$object)
    {
        $object = json_decode(json_encode($object), true);
        return $object;
    }
}


if ( ! function_exists('infoEncrypt')) {
    /**
     * 加解密字符串
     * User: Clannad ~ ☆
     * @param string $string 要加密的内容
     * @param string $operation 操作方式 加密还是解密，E表示加密，D表示解密
     * @return array|string|string[]
     */
    function infoEncrypt(string $string, string $operation = 'E')
    {
        $key           = md5(env('ENCRYPT_KEY', 'IHBHnfe8Y*3g20f*87^$@'));// 密匙
        $key_length    = strlen($key);
        $string        = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);
        $rndkey        = $box = array();
        $result        = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i]    = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a       = ($a + 1) % 256;
            $j       = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result  .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'D') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }
}


if ( ! function_exists('cutTel')) {
    /**
     * 截取电话号码 中间四位为 **** 例如 135****0059
     * User: Clannad ~ ☆
     * @param string | int $tel 电话号码
     * @return string
     */
    function cutTel(string $tel)
    {
        $left  = substr($tel, 0, 3);
        $right = substr($tel, 7);
        return $left . '****' . $right;
    }
}

if ( ! function_exists('getExpressInfo')) {
    /**
     * 获取快递信息
     * User: Clannad ~ ☆
     * @param string $express_sn
     * @param $type
     * @return mixed
     */
    function getExpressInfo(string $express_sn, $type = '')
    {
        $host    = "https://wuliu.market.alicloudapi.com";//api访问链接
        $path    = "/kdi";//API访问后缀
        $method  = "GET";
        $appcode = "";//替换成自己的阿里云appcode
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "no=" . trim($express_sn);  //参数写在这里
        $bodys  = "";

        if ($type) {
            $type = '&type=' . $type;
        }

        $url = $host . $path . "?" . $querys . $type;//url拼接

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        //curl_setopt($curl, CURLOPT_HEADER, true);
        //如不输出json, 请打开这行代码，打印调试头部状态码。
        //状态码: 200 正常；400 URL无效；401 appCode错误； 403 次数用完； 500 API网管错误
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $result = curl_exec($curl);
        return json_decode($result, true);
    }
}

?>