<?php

/**
 * File: menu.class.php
 * Created by humooo.
 * Email: humooo@outlook.com
 * Date: 15-7-5
 * Time: 上午10:01
 */
include_once "com.function.php";

class menu
{
    public $errCode;
    public $errMsg;
    private $token_path;
    private $token_file;

    public function __construct()
    {
        $this->token_path = dirname(dirname(__FILE__)) . '/cache/';
        $this->token_file = $this->token_path . "ACCESS_TOKEN.txt";

        $debug_msg = "[DEBUG] 缓存路径: " . $this->token_path . "<br />\n";
        $debug_msg .= "[DEBUG] ACCESS TOKEN文件: " . $this->token_file . "<br />\n";
        if (DEBUG) {
            echo $debug_msg;
        }
    }

    public function showArgs()
    {
        $str = APPSECRET . "<br />\n";
        $str .= APPID . "<br />\n";
        $str .= $this->token_path . "<br />\n";
        echo $str;
    }

    /**
     * 创建菜单
     * example:
     *    array (
     *        'button' => array (
     *          0 => array (
     *            'name' => '扫码',
     *            'sub_button' => array (
     *                0 => array (
     *                  'type' => 'scancode_waitmsg',
     *                  'name' => '扫码带提示',
     *                  'key' => 'rselfmenu_0_0',
     *                ),
     *                1 => array (
     *                  'type' => 'scancode_push',
     *                  'name' => '扫码推事件',
     *                  'key' => 'rselfmenu_0_1',
     *                ),
     *            ),
     *          ),
     *          1 => array (
     *            'name' => '发图',
     *            'sub_button' => array (
     *                0 => array (
     *                  'type' => 'pic_sysphoto',
     *                  'name' => '系统拍照发图',
     *                  'key' => 'rselfmenu_1_0',
     *                ),
     *                1 => array (
     *                  'type' => 'pic_photo_or_album',
     *                  'name' => '拍照或者相册发图',
     *                  'key' => 'rselfmenu_1_1',
     *                )
     *            ),
     *          ),
     *          2 => array (
     *            'type' => 'location_select',
     *            'name' => '发送位置',
     *            'key' => 'rselfmenu_2_0'
     *          ),
     *        ),
     *    )
     * type可以选择为以下几种，其中5-8除了收到菜单事件以外，还会单独收到对应类型的信息。
     * 1、click：点击推事件
     * 2、view：跳转URL
     * 3、scancode_push：扫码推事件
     * 4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
     * 5、pic_sysphoto：弹出系统拍照发图
     * 6、pic_photo_or_album：弹出拍照或者相册发图
     * 7、pic_weixin：弹出微信相册发图器
     * 8、location_select：弹出地理位置选择器
     *
     * @param $data
     * @return bool
     */
    public function createMenu($data)
    {
        $create_menu_url = API_URL_PREFIX . MENU_CREATE_URL . 'access_token=' . $this->getAccessToken();
        $debug_msg = null;
        $result = call_user_func('httpPost', $create_menu_url, json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                $debug_msg .= "[DEBUG] 创建菜单出错，错误如下<br />\n";
                $debug_msg .= "[DEBUG] error(" . $this->errCode . ")" . Code2Text::getText($this->errCode, Code2Text::$TencentCodeText) . "<br />\n";
                if (DEBUG) {
                    echo $debug_msg;
                }
                return false;
            }
            $debug_msg .= "[DEBUG] 创建菜单成功<br />\n";
            if (DEBUG) {
                echo $debug_msg;
            }
            return true;
        }
        $debug_msg .= "[DEBUG] http post数据失败, post url: " . $create_menu_url . "<br />\n";

        if (DEBUG) {
            echo $debug_msg;
        }
        return false;
    }

    /**
     * 获取菜单
     * @return array('menu'=>array(....s))
     */
    public function getMenu()
    {
        $get_menu_url = API_URL_PREFIX . MENU_GET_URL . 'access_token=' . $this->getAccessToken();
        $debug_msg = null;
        $result = call_user_func('httpGet', $get_menu_url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                $debug_msg .= "[DEBUG] 获取菜单失败，错误如下<br />\n";
                $debug_msg .= "[DEBUG] error(" . $this->errCode . ")" . Code2Text::getText($this->errCode, Code2Text::$TencentCodeText) . "<br />\n";
                if (DEBUG) {
                    echo $debug_msg;
                }
                return false;
            }
            $debug_msg .= "[DEBUG] 获取菜单成功<br />\n";
            if (DEBUG) {
                echo $debug_msg;
            }
            return $json;
        }
        $debug_msg .= "[DEBUG] 获取菜单失败<br />\n";
        if (DEBUG) {
            echo $debug_msg;
        }
        return false;
    }

    /**
     * 删除菜单
     * @return boolean
     */
    public function deleteMenu()
    {
        $delete_menu_url = API_URL_PREFIX . MENU_DELETE_URL . 'access_token=' . $this->getAccessToken();
        $debug_msg = null;
        $result = call_user_func('httpGet', $delete_menu_url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                $debug_msg .= "[DEBUG] 删除菜单失败，错误如下<br />\n";
                $debug_msg .= "[DEBUG] error(" . $this->errCode . ")" . Code2Text::getText($this->errCode, Code2Text::$TencentCodeText) . "<br />\n";
                if (DEBUG) {
                    echo $debug_msg;
                }
                return false;
            }
            $debug_msg .= "[DEBUG] 删除菜单成功<br />\n";
            if (DEBUG) {
                echo $debug_msg;
            }
            return true;
        }
        $debug_msg .= "[DEBUG] 删除菜单失败<br />\n";
        if (DEBUG) {
            echo $debug_msg;
        }
        return false;
    }


    public function getAccessToken()
    {
        $accessToken = self::checkAccessToken();
        $debug_msg = null;
        if (!$accessToken) {
            $debug_msg .= "[DEBUG] 获取新token<br />\n";
            if (!$accessToken = self::_getAccessToken()) {
                $debug_msg .= "[DEBUG] token获取失败<br />\n";
                if (DEBUG) {
                    echo $debug_msg;
                }
                return false;
            }
        }
        return $accessToken;
    }

    private function _getAccessToken()
    {
        $token_url = API_URL_PREFIX . AUTH_URL . 'appid=' . APPID . '&secret=' . APPSECRET;
        $debug_msg = null;

        if (!$accessToken = call_user_func('httpGet', $token_url)) {
            $debug_msg .= "[DEBUG] http get token失败, get url: " . $token_url . "<br />\n";
            if (DEBUG) {
                echo $debug_msg;
            }
            return false;
        }

        $json = json_decode($accessToken, true);

        if (!empty($json['errcode'])) {
            $this->errCode = $json['errcode'];
            $this->errMsg = $json['errmsg'];
            return false;
        }

        $token = $json['access_token'];
        $expire = $json['expires_in'];
        $token_data = array(
            'TOKEN' => $token,
            'EXPIRE' => $expire,
            'CREATE' => time(),
        );

        if (!file_put_contents($this->token_file, json_encode($token_data))) {
            $debug_msg .= "[DEBUG] 写入到TOKEN文件失败<br />\n";
            if (DEBUG) {
                echo $debug_msg;
            }
            return false;
        }

        $debug_msg .= "[DEBUG] 从腾讯后台获取token成功<br />\n";
        if (DEBUG) {
            echo $debug_msg;
        }

        return $token;
    }

    private function checkAccessToken()
    {
        $path = $this->token_file;
        $debug_msg = null;
        if ($data = file_get_contents($path)) {
            $data = json_decode($data, true);

            $expire = isset($data['EXPIRE']) ? (intval($data['EXPIRE']) - 5) : null;
            $create_time = isset($data['CREATE']) ? intval($data['CREATE']) : null;
            $actual_expire = time() - $create_time;

            if ($create_time && $expire && $actual_expire <= $expire) {
                $debug_msg .= "[DEBUG] token在缓存中，并且有效，可直接使用<br />\n";
                $token = $data['TOKEN'];
                if (DEBUG) {
                    echo $debug_msg;
                }
                return $token;
            }
            $debug_msg .= "[DEBUG] token在缓存中，但已过期，需重新获取<br />\n";
            if (DEBUG) {
                echo $debug_msg;
            }
            return false;
        }
        if (is_writable($this->token_path)) {
            $debug_msg .= "[DEBUG] token不在缓存中，需从腾讯后台获取<br />\n";
        } else {
            $debug_msg .= "[DEBUG] 文件夹：" . $this->token_path . " 没有写权限<br />\n";
        }
        if (DEBUG) {
            echo $debug_msg;
        }
        return false;
    }

    /**
     * 微信api不支持中文转义的json结构
     * @param $arr
     * @return string
     */
    static function json_encode($arr)
    {
        $parts = array();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length)) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = json_encode($value); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . json_encode($value); /* :RECURSION: */
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (is_numeric($value) && $value < 2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; //All other things
                $parts [] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

}