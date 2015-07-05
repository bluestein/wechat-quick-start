<?php
/**
 * Created by humooo.
 * Email: humooo@outlook.com
 * Date: 15-6-23
 * Time: 下午5:01
 */
//include_once $_SERVER['DOCUMENT_ROOT'] . "/wx/conf.php";
include_once "../conf.global.php";
include_once '../conf.local.php';

//------------------------------------Tencent Validation functions-------------------------------------------//

/**
 * valid
 */
function valid()
{
    if (checkSignature()) {
        if (isset($_GET["echostr"])) {
            $echoStr = $_GET["echostr"];
            echo $echoStr;
        }
        return true;
    } else {
        return false;
    }

}

/**
 * checkSignature
 * @return boolean
 */
function checkSignature()
{
    $signature = isset($_GET["signature"]) ? $_GET["signature"] : '';
    $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
    $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';

    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1($tmpStr);

    if ($tmpStr == $signature) {
        return true;
    } else {
        return false;
    }
}

//------------------------------------------messages-----------------------------------//
//Extending it on your own needs
//e.g. picture message, voice message etc.

/**
 * text msg
 * @param $postObj
 * @param $contentStr
 * @return string
 */
function responseText($postObj, $contentStr)
{
    $fromUserName = $postObj->FromUserName;
    $toUserName = $postObj->ToUserName;
    $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                </xml>";

    $resultStr = sprintf($textTpl, $fromUserName, $toUserName, time(), $contentStr);
    return $resultStr;
}

/**
 * news msg
 * @param $content
 * @return string
 * usage:
 * $content = array(
 *  'fromUserName' => 'username',
 *  'toUserName' => 'username',
 *  'itemArray' =>array(
 *      array(
 *          'Title'=>'msg title',
 *          'Description'=>'summary text',
 *          'PicUrl'=>'http://www.domain.com/1.jpg',
 *          'Url'=>'http://www.domain.com/1.html'
 *      ),
 *      ...
 * )
 * );
 */
function responseMultiNews($content)
{
    if (!is_array($content)) {
        return "";
    }
    $itemTpl = "<item>
        			<Title><![CDATA[%s]]></Title>
        			<Description><![CDATA[%s]]></Description>
        			<PicUrl><![CDATA[%s]]></PicUrl>
        			<Url><![CDATA[%s]]></Url>
    		   </item>";
    $item_str = "";
    foreach ($content['itemArray'] as $item) {
        $item_str .= sprintf($itemTpl, $item ['Title'], $item ['Description'], $item ['PicUrl'], $item ['Url']);
    }
    $newsTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>%s</ArticleCount>
					<Articles>$item_str</Articles>
				</xml>";

    $result = sprintf($newsTpl, $content['fromUserName'], $content['toUserName'], time(), count($content['itemArray']));
    return $result;
}

/**
 * video msg
 * @param $data
 * @return string
 */
function responseVideo($data)
{
    $msgTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[video]]></MsgType>
                    <Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                    </Video>
               </xml>";
    return sprintf($msgTpl, $data['fromUserName'], $data['toUserName'], time(),
        $data['media_id'], $data['title'], $data['description']);
}


//------------------------------------curl: post & get-------------------------------------------//

/**
 * @param $url
 * @param $data
 * @return bool|int|mixed
 * usage:
 * $data = array('key1'=>'value1','key2'=>'value2');
 */
function httpPost($url, $data)
{
    $oCurl = curl_init();

    $aPOST = array();
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            $aPOST[] = $key . "=" . urlencode($val);
        }
    }
    $strPOST = join("&", $aPOST);

    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($oCurl, CURLOPT_POST, true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
    $sContent = curl_exec($oCurl);

    if (curl_errno($oCurl)) {
        if (DEBUG)
            echo "curl error:" . curl_error($oCurl);//you can do your own debug
        return false;
    }
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return intval($aStatus["http_code"]);
    }
}

/**
 * @param $url
 * @return bool|mixed
 */
function httpGet($url)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

//------------------------------------some other functions-------------------------------------------//

/**
 * merge spaces
 * @param $string
 * @return mixed
 */
function merge_spaces($string)
{
    return preg_replace("/\\s/", "", $string);
}


/**
 * Get a random string with length $n
 * @param $n
 * @return string
 */
function nonceStr($n)
{
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyz012345679ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $n);
}


/**
 *------------------------------------code to text class-------------------------------------------
 * literal meaning: code to text.
 * Class Code2Text
 * usage:
 * Code2Text::getText(code, Code2Text::$TencentCodeText);
 * extends:
 * e.g. $OtherCodeText = array(CODE=>TEXT) etc.
 */
class Code2Text
{

    /**
     * errCode::getText($errCode);
     * @param $code
     * @param $type
     * @return bool
     */
    public static function getText($code, $type)
    {
        if (isset($type[$code])) {
            return $type[$code];
        } else {
            return false;
        }
    }

    public static $TencentCodeText = array(
        '-1' => '系统繁忙，此时请开发者稍候再试',
        '0' => '请求成功',
        '40001' => '获取access_token时AppSecret错误，或者access_token无效。请开发者认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口',
        '40002' => '不合法的凭证类型',
        '40003' => '不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID',
        '40004' => '不合法的媒体文件类型',
        '40005' => '不合法的文件类型',
        '40006' => '不合法的文件大小',
        '40007' => '不合法的媒体文件id',
        '40008' => '不合法的消息类型',
        '40009' => '不合法的图片文件大小',
        '40010' => '不合法的语音文件大小',
        '40011' => '不合法的视频文件大小',
        '40012' => '不合法的缩略图文件大小',
        '40013' => '不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写',
        '40014' => '不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
        '40015' => '不合法的菜单类型',
        '40016 ' => '不合法的按钮个数',
        '40017' => '不合法的按钮个数',
        '40018' => '不合法的按钮名字长度',
        '40019' => '不合法的按钮KEY长度',
        '40020' => '不合法的按钮URL长度',
        '40021' => '不合法的菜单版本号',
        '40022' => '不合法的子菜单级数',
        '40023' => '不合法的子菜单按钮个数',
        '40024' => '不合法的子菜单按钮类型',
        '40025' => '不合法的子菜单按钮名字长度',
        '40026' => '不合法的子菜单按钮KEY长度',
        '40027' => '不合法的子菜单按钮URL长度',
        '40028' => '不合法的自定义菜单使用用户',
        '40029' => '不合法的oauth_code',
        '40030' => '不合法的refresh_token',
        '40031' => '不合法的openid列表',
        '40032' => '不合法的openid列表长度',
        '40033' => '不合法的请求字符，不能包含\uxxxx格式的字符',
        '40035' => '不合法的参数',
        '40038' => '不合法的请求格式',
        '40039' => '不合法的URL长度',
        '40050' => '不合法的分组id',
        '40051' => '分组名字不合法',
        '41001' => '缺少access_token参数',
        '41002' => '缺少appid参数',
        '41003' => '缺少refresh_token参数',
        '41004' => '缺少secret参数',
        '41005' => '缺少多媒体文件数据',
        '41006' => '缺少media_id参数',
        '41007' => '缺少子菜单数据',
        '41008' => '缺少oauth code',
        '41009' => '缺少openid',
        '42001' => 'access_token超时，请检查access_token的有效期，请参考基础支持-获取access_token中，对access_token的详细机制说明',
        '42002' => 'refresh_token超时',
        '42003' => 'oauth_code超时',
        '43001' => '需要GET请求',
        '43002' => '需要POST请求',
        '43003' => '需要HTTPS请求',
        '43004' => '需要接收者关注',
        '43005' => '需要好友关系',
        '44001' => '多媒体文件为空',
        '44002' => 'POST的数据包为空',
        '44003' => '图文消息内容为空',
        '44004' => '文本消息内容为空',
        '45001' => '多媒体文件大小超过限制',
        '45002' => '消息内容超过限制',
        '45003' => '标题字段超过限制',
        '45004' => '描述字段超过限制',
        '45005' => '链接字段超过限制',
        '45006' => '图片链接字段超过限制',
        '45007' => '语音播放时间超过限制',
        '45008' => '图文消息超过限制',
        '45009' => '接口调用超过限制',
        '45010' => '创建菜单个数超过限制',
        '45015' => '回复时间超过限制',
        '45016' => '系统分组，不允许修改',
        '45017' => '分组名字过长',
        '45018' => '分组数量超过上限',
        '46001' => '不存在媒体数据',
        '46002' => '不存在的菜单版本',
        '46003' => '不存在的菜单数据',
        '46004' => '不存在的用户',
        '47001' => '解析JSON/XML内容错误',
        '48001' => 'api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限',
        '50001' => '用户未授权该api',
        '61451' => '参数错误(invalid parameter)',
        '61452' => '无效客服账号(invalid kf_account)',
        '61453' => '客服帐号已存在(kf_account exsited)',
        '61454' => '客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)(invalid kf_acount length)',
        '61455' => '客服帐号名包含非法字符(仅允许英文+数字)(illegal character in kf_account)',
        '61456' => '客服帐号个数超过限制(10个客服账号)(kf_account count exceeded)',
        '61457' => '无效头像文件类型(invalid file type)',
        '61450' => '系统错误(system error)'
    );

}
	