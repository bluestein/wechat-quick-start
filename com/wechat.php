<?php
/**
 * Created by PhpStorm.
 * Email: humooo@outlook.com
 * User: humooo
 * Date: 15-1-31
 * Time: 下午8:58
 */
include_once "com.function.php";
include_once "logs.class.php";
include_once "db.class.php";
/**
 * response all msg
 * @param $postObj
 * @return string
 */
function response($postObj)
{
    $log = new Logs();

    $return = '';
    $msgType = $postObj->MsgType;
    $logmsg = '[user] ' . $postObj->FromUserName . ' [msg-type] ' . $msgType;

    switch ($msgType) {
        case MSGTYPE_EVENT:
            $logmsg .= ' [event] ' . $postObj->Event;
            switch ($postObj->Event) {
                case 'subscribe': //关注
                    $return = subscribe($postObj);
                    break;
                case 'unsubscribe':
                    unsubscribe($postObj);
                    break;
                case 'CLICK':
                    switch ($postObj->EventKey) {
                        case 'MENU_NEWS':
                            $return = news($postObj);
                            break;
                    }
            }
            break;
        case MSGTYPE_TEXT: //txt
            $logmsg .= ' [content] ' . $postObj->Content;
            $return = textResponse($postObj);
            break;
        default:
            $return = news($postObj);
            break;
    }

    if (!$log->write_log($logmsg, 'access') && DEBUG) {
        echo 'error: ' . $log->error;
    }

    return $return;
}

/**
 * response text msg
 * @param $postObj
 * @return string
 */
function textResponse($postObj)
{
    $content = $postObj->Content;
    $contentStr = '';
    switch ($content) {
        case '你好':
            $contentStr = responseText($postObj, '你好 :-)');
            break;
        default:
            $contentStr .= news($postObj);
            break;
    }
    return $contentStr;
}

/**
 *news
 * @param $postObj
 * @return string
itemArray = array(
 * array(
 * 'Title' => 'title',
 * 'Description' => 'description',
 * 'PicUrl' => 'www.sample.com/pic/user.png',
 * 'Url' => 'www.sample.com/sample.html',
 * )
 * )
 */
function news($postObj)
{
    $fromUserName = $postObj->FromUserName;
    $toUserName = $postObj->ToUserName;

    $db = new Db();
    $log = new Logs();

    $itemArray = null;
    $query = array(
        'select' => '*',
        'table' => TB_NEWS,
        'where' => array(
            'state' => 1
        ),
        'order' => 'rank ASC'
    );
    if (!$res = $db->fetch($query))
        $log->write($db->error);
    foreach ($res as $item) {
        $itemArray[] = array(
            'Title' => $item['title'],
            'Description' => $item['description'],
            'PicUrl' => $item['picurl'],
            'Url' => $item['url']
        );
    }
    $news = array(
        'fromUserName' => $fromUserName,
        'toUserName' => $toUserName,
        'itemArray' => $itemArray

    );
    return responseMultiNews($news);
}

/**
 * subscribe
 * @param $postObj
 * @return string
 */
function subscribe($postObj)
{
    $fromUserName = $postObj->FromUserName;
    $openID = $fromUserName;

    $db = new Db();
    $log = new Logs();

    if (!$db->insert(TB_USER, array('', $openID)))
        $log->write($db->error, $db->level);

    $contentStr = "Welcome to blublu";

    return responseText($postObj, $contentStr);
}

/**
 * 取消关注
 * @param $postObj
 */
function unsubscribe($postObj)
{
    $openID = $postObj->FromUserName;
    //一些操作
}


