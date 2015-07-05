<?php
/**
 * Created by PhpStorm.
 * Email: humooo@outlook.com
 * User: humooo
 * Date: 15-1-31
 * Time: 下午1:13
 */

//经常更改
define('DEBUG', true); //true：调试模式；false：正常模式

//微信主要接口
//各种凭证
define('API_URL_PREFIX', 'https://api.weixin.qq.com/cgi-bin');
define('AUTH_URL', '/token?grant_type=client_credential&');
define('JS_AUTH_URL', '/ticket/getticket?');

//微信菜单接口
define('MENU_CREATE_URL', '/menu/create?');
define('MENU_GET_URL', '/menu/get?');
define('MENU_DELETE_URL', '/menu/delete?');

//微信其他接口
define('USER_GET_URL', '/user/get?');
define('USER_INFO_URL', '/user/info?'); //微信用户信息接口
define('CUSTOM_SEND_URL', '/message/custom/send?'); //微信客服消息接口
define('MATERIAL_URL', '/material/batchget_material?access_token='); //微信素材接口
define('GET_MATERIAL_URL', '/material/get_material?access_token='); //获取永久素材
define('GET_TMP_MATERIAL_URL', '/media/get?access_token='); //获取临时素材
define('OAUTH_URL_PREFIX', 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . APPID . '&redirect_uri=');
define('OAUTH_URL_SUFFIX', '&response_type=code&scope=snsapi_base#wechat_redirect');
define('OAUTH_TOKEN_PREFIX', 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . APPID . '&secret=' . APPSECRET . '&code=');
define('OAUTH_TOKEN_SUFFIX', '&grant_type=authorization_code'); //网页授权后缀

//定义微信消息类型
define("MSGTYPE_TEXT", "text");
define("MSGTYPE_EVENT", "event");
define("MSGTYPE_IMAGE", "image");
define("MSGTYPE_LOCATION", "location");

define('FILE_READ_MODE', '0644');
define('FILE_WRITE_MODE', '0666');
define('DIR_READ_MODE', '0755');
define('DIR_WRITE_MODE', '0777');


