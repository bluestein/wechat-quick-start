<?php
/**
 * File: create_menu_sample.php
 * Created by humooo.
 * Email: humooo@outlook.com
 * Date: 15-7-5
 * Time: 下午1:57
 */
include_once "conf.global.php";
include_once "conf.local.php";
include_once 'com/menu.class.php';
include_once 'com/com.function.php';

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

$menu = new menu();
$menu->createMenu($menu_data);