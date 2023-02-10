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
     * @param string | array $info 记录的日志详细信息
     * @param string $msg 日志说明
     * @param string $folderName 目录名称
     */
    function writeLog($info, string $msg = "异常处理：", string $folderName = 'logs')
    {
        if (is_array($info)) {
            $info = json_encode($info, JSON_UNESCAPED_UNICODE);
        }
        $mode = 'a';//追加方式写
        $msg  .= date('Y-m-d H:i:s', time()) . "\n";
        $msg  .= $info . "\n";
        $msg  .= "===========================================\n";

        $folderPath = getcwd() . '/' . $folderName;
        // 判断文件夹是否存在 不存在就创建
        if ( ! file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $file    = $folderPath . '/' . date('Y-m-d', time()) . '.txt';
        $oldMask = @umask(0);
        $fp      = @fopen($file, $mode);
        @flock($fp, 3);

        if ( ! $fp) {
            return false;
        } else {
            @fwrite($fp, $msg);
            @fclose($fp);
            @umask($oldMask);
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
        $httpType = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $httpType;
    }
}

if ( ! function_exists('httpCurl')) {
    /**
     * curl工具请求数据返回
     * User: Clannad ~ ☆
     * @param string $url 请求的url
     * @param string $type 请求的方式GET/POST
     * @param string $res 返回的数据类型 支持json和xml格式返回
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

        if (curl_errno($ch)) {
            // 4,关闭curl
            curl_close($ch);
            //请求失败，返回错误信息
            return curl_error($ch);
        }

        //请求成功 返回数据（数组格式）
        if ($res == "json") {
            return json_decode($output, true);
        }else if($res == 'xml'){
            return json_decode(json_encode(simplexml_load_string($output)), true);
        }else{
            return $res;
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
     * 递归获取所有的下级ID集合
     * User: Clannad ~ ☆
     * @param array $array
     * @param int $id
     * @return array
     */
    function getAllchildren(string $id = 'id', array $data = [], string $pid = 'pid')
    {
        $childIds = [];
        foreach ($data as $v) {
            if ($v[$pid] == $id) {
                $childIds[] = $v[$id];
                $childIds   = array_merge($childIds, getAllchildren($v[$id], $data, $pid));
            };
        };
        return $childIds;
    }
}

if(! function_exists('getAllParentIds')){
    /**
     * 获取所有上级ID的集合
     * User: ❤ CLANNAD ~ After Story By だんご
     * @param string $id
     * @param array $data
     * @param string $pid
     * @return array
     */
    function getAllParentIds(string $id = 'id', array $data = [], string $pid = 'pid')
    {
        $parentIds = [];
        foreach($data as $v){
            //从小到大 排列
            if($v[$id] == $id){
                $parentIds[] = $v[$id];
                if($v[$pid] > 0){
                    $parentIds = array_merge(getAllParentIds($v[$pid],$data), $parentIds);
                }
            }
        }

        return $parentIds;
    }
}

if ( ! function_exists('formatPublishTime')) {
    /**
     * 格式化发布时间
     * User: Clannad ~ ☆
     * @param int $timestamp 时间戳
     * @return string
     */
    function formatPublishTime(int $timestamp, $formatSecond = false)
    {
        if (time() - $timestamp > 86400) {
            $day = floor((time() - $timestamp) / 86400);

            switch ($day) {
                case $day >= 30:
                    return '一个月';
                    break;

                case $day >= 15:
                    return '15天';
                    break;

                case $day >= 7:
                    return '7天';
                    break;

                default:
                    return $day . '天';
                    break;
            }

        }

        if (time() - $timestamp > 3600) {
            return floor((time() - $timestamp) / 3600) . '小时';
        }

        if (time() - $timestamp > 60) {
            //分钟
            return floor((time() - $timestamp) / 60) . '分钟';
        }

        if (time() - $timestamp < 60) {
            if($formatSecond){
                return '刚刚';
            }else{
                //几秒
                return time() - $timestamp . '秒';
            }
        }
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
        $imageInfo   = getimagesize($imageFile);
        $imageData   = fread(fopen($imageFile, 'r'), filesize($imageFile));
        $base64Image = 'data:' . $imageInfo['mime'] . ';base64,' . chunk_split(base64_encode($imageData));
        return $base64Image;
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


if ( ! function_exists('stringEncrypt')) {
    /**
     * 加解密字符串
     * User: Clannad ~ ☆
     * @param string $string 要加密的内容
     * @param string $operation 操作方式 加密还是解密，E表示加密，D表示解密
     * @return array|string|string[]
     */
    function stringEncrypt(string $string, string $operation = 'E')
    {
        $key           = md5(env('ENCRYPT_KEY', 'crypt_key'));// 密匙
        $keyLength    = strlen($key);
        $string        = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $stringLength = strlen($string);
        $rndkey        = $box = array();
        $result        = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $keyLength]);
            $box[$i]    = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $stringLength; $i++) {
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
?>