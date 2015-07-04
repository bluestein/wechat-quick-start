<?php
/**
 * Created by PhpStorm.
 * Email: humooo@outlook.com
 * User: humooo
 * Date: 15-1-31
 * Time: 下午1:13
 */
include_once 'com/wechat.function.php';
include_once 'conf.global.php';
include_once 'conf.local.php';

if (valid()) {
    $postStr = file_get_contents("php://input");
    if (!empty($postStr)) {
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (isset($postObj)) {
            echo response($postObj);
        }
    }
}
else
{
?>
<html>
<head>
    <meta charset="utf-8">
    <title>禁止访问</title>
</head>
<body>
<p style="text-align: center">你无权访问该页 :-)</p>
</body>
<?php
}
?>
