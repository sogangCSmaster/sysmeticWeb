<?php
$config['app']['host'] = 'suwon.telpost.co.kr';
$config['app']['scheme'] = 'http';

$config['app']['upload.tmp.path'] = '/home/bizmnc/sysmetic/tmp';
$config['app']['profile.path'] = '/home/bizmnc/sysmetic/html/profile';
$config['app']['broker.path'] = '/home/bizmnc/sysmetic/html/broker';
$config['app']['item.path'] = '/home/bizmnc/sysmetic/html/item';
$config['app']['account.path'] = '/home/bizmnc/sysmetic/html/account';
$config['app']['notice.path'] = '/home/bizmnc/sysmetic/html/notice';
$config['app']['profile.url'] = 'http://suwon.telpost.co.kr/profile';
$config['app']['broker.url'] = 'http://suwon.telpost.co.kr/broker';
$config['app']['item.url'] = 'http://suwon.telpost.co.kr/item';
$config['app']['account.url'] = 'http://suwon.telpost.co.kr/account';
$config['app']['notice.url'] = 'http://suwon.telpost.co.kr/notice';

$config['app']['default_picture'] = '/img/bg_photo.gif';
$config['app']['default_picture_s'] = '/img/bg_photo.gif';

// $config['app']['debug'] = false;

$config['app']['db'] = array(
	// 'host'=>'sysmetic.ckz9pwf0k9qx.ap-northeast-1.rds.amazonaws.com',
	'host'=>'localhost',
	'user'=>'sysmetic',
	'password'=>'sys2016!@',
	'name'=>'sysmetic_db'
);

$config['app']['memcached'] = array(
	'localhost'
);
