#wechat quick start  
微信公众平台php快速开发模板。为简单起见，只有一些操作进行了类封装,其余均为函数封装，欢迎Fork此项目  
项目地址：**https://github.com/bluestein/wechat-quick-start**

---

## 一、简述
> 1. 所需环境: PHP, Apache, MySQL  
> 2. conf.local.php需要开发者自己填写, 用法具体看conf.local.php.dist
> 3. 需要保证 logs、cache 文件夹有读写权限
> 4. 考虑到初学者，该项目的缓存利用的是文件（如 access token 的存储），开发者可以根据自己的需求换成 redis 等缓存技术  

```php
    //利用json格式保存token
    //$token_data从腾讯后台获取
    file_put_contents($this->token_file, json_encode($token_data));
    
    //可以换成（以redis举例）
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->set("ACCESS_TOKEN", $token);//$token从腾讯后台获取
    $redis->expire("ACCESS_TOKEN", $expire)//ttl, $expire从腾讯后台获取
    $redis->close();    
```
---

## 二、使用详解
使用前需先打开微信帐号的开发模式，详细步骤请查看微信公众平台接口使用说明：  
微信公众平台： http://mp.weixin.qq.com/wiki/

---

## 三、目录
> **[index.php 微信入口](#user-content-indexphp-微信入口)**  
> **[wechat.php 微信](#user-content-1-wechatphp-微信)**  
> **[db.class.php MySQL数据库操作类](#user-content-2-dbclassphp-mysql数据库操作类)**  
> **[com.function.php 公共函数](#user-content-3-comfunctionphp-公共函数)**  
> **[logs.class.php 简单日志类](#user-content-4-logsclassphp-简单日志类)**  
> **[menu.class.php 菜单类](#user-content-5-menuclassphp-菜单类)**

---

## index.php 微信入口
所有的请求均是通过该文件进行处理。
```php
    if (valid()) {
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (isset($postObj)) {
                echo response($postObj);
            }
        }
    }
```

## 1. wechat.php 微信
调用官方API，灵活的消息分类响应；

### 主要功能
- 自动回复（文本、图片、语音、视频、音乐、图文） **（初级权限）**
- 菜单操作（查询、创建、删除） **（菜单权限）**

```bash
下面的功能还未整理，有待补充
```
- 客服消息（文本、图片、语音、视频、音乐、图文） **（认证权限）**
- 二维码（创建临时、永久二维码，获取二维码URL） **（服务号、认证权限）**
- 网页授权（基本授权，用户信息授权） **（服务号、认证权限）**
- 用户信息（查询用户基本信息、获取关注者列表） **（认证权限）**
- 多客服功能（客服管理、获取客服记录、客服会话管理） **（认证权限）**
- 媒体文件（上传、获取） **（认证权限）**
- 高级群发 **（认证权限）**
- 模板消息（设置所属行业、添加模板、发送模板消息） **（服务号、认证权限）**
- 卡券管理（创建、修改、删除、发放、门店管理等） **（认证权限）**
- 语义理解 **（服务号、认证权限）**
- 获取微信服务器IP列表 **（初级权限）**  
- 微信JSAPI授权(获取ticket、获取签名) **（初级权限）**  
- 数据统计(用户、图文、消息、接口分析数据) **（认证权限）**

> ** 备注**：  
> 初级权限：基本权限，任何正常的公众号都有此权限  
> 菜单权限：正常的服务号、认证后的订阅号拥有此权限  
> 认证权限：分为订阅号、服务号认证，如前缀服务号则仅认证的服务号有此权限，否则为认证后的订阅号、服务号都有此权限  
> 支付权限：仅认证后的服务号可以申请此权限

## 2. db.class.php MySQL数据库操作类
简单的数据库操作类（MySQL）

### 主要函数
- insert() 插入数据到数据库；
- fetch() 从数据库查询数据；

## 3. com.function.php 公共函数
包含一些常用函数

### 主要函数或类
- valid() 是否合法；
- checkSignature() 检查签名；
- responseText() 回复文本消息；
- responseMultiNews() 回复图文消息；
- responseVideo() 回复视频消息；
- httpPost() curl post；
- httpGet() curl get；
- class Code2Text 错误代码转文本类；

## 4. logs.class.php 简单日志类
日志操作，提供5个等级：ERROR、DEBUG、INFO、ACCESS、ALL

### 主要函数
- write_log() 写日志；

## 5. menu.class.php 菜单类
菜单操作类，创建菜单详见 create_menu_sample.php

### 主要函数
- createMenu() 创建菜单；
- getMenu() 获取菜单；
- deleteMenu() 删除菜单；
- getAccessToken() 获取access token；
- json_encode() json encode；  

```php
    //菜单数据格式
    $menu_data = array(
        'button' => array(
            array(
                'name' => '查询',
                'sub_button' => array(
                    array('type' => 'click', 'name' => '查询', 'key' => 'MENU_CX'),
                    array('type' => 'view', 'name' => '百度', 'url' => 'http://www.baidu.com'),
                )
            ),
        )
    );
```

```bash
    未认证的订阅号没有菜单权限
```

未认证创建菜单样例
![未认证创建菜单样例](https://raw.githubusercontent.com/bluestein/wechat-quick-start/master/pic/create_menu_sample.png)

---
