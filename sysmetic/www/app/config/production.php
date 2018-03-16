<?php
$config['app']['host'] = 'sysmetic.co.kr';
$config['app']['scheme'] = 'http';

$config['app']['upload.root'] = '/home/sysmetic/www/';
$config['app']['upload.tmp.path'] = '/home/sysmetic/www/tmp';
$config['app']['data.path'] = '/home/sysmetic/www/html/data';
$config['app']['profile.path'] = '/home/sysmetic/www/html/profile';
$config['app']['namecard.path'] = '/home/sysmetic/www/html/namecard';
$config['app']['broker.path'] = '/home/sysmetic/www/html/broker';
$config['app']['education.path'] = '/home/sysmetic/www/html/education';
$config['app']['media.path'] = '/home/sysmetic/www/html/media';
$config['app']['item.path'] = '/home/sysmetic/www/html/item';
$config['app']['type.path'] = '/home/sysmetic/www/html/type';
$config['app']['account.path'] = '/home/sysmetic/www/html/account';
$config['app']['notice.path'] = '/home/sysmetic/www/html/notice';
$config['app']['auth.path'] = '/home/sysmetic/www/app/lib/Auth';

$config['app']['strategy.path'] = '/home/sysmetic/www/html/strategy';

$config['app']['profile.url'] = 'http://'.$config['app']['host'].'/profile';
$config['app']['broker.url'] = 'http://'.$config['app']['host'].'/broker';
$config['app']['education.url'] = 'http://'.$config['app']['host'].'/education';
$config['app']['media.url'] = 'http://'.$config['app']['host'].'/media';
$config['app']['namecard.url'] = 'http://'.$config['app']['host'].'/namecard';
$config['app']['item.url'] = 'http://'.$config['app']['host'].'/item';
$config['app']['type.url'] = 'http://'.$config['app']['host'].'/type';
$config['app']['account.url'] = 'http://'.$config['app']['host'].'/account';
$config['app']['notice.url'] = 'http://'.$config['app']['host'].'/notice';

$config['app']['default_picture'] = '/img/bg_photo.gif';
$config['app']['default_picture_s'] = '/img/bg_photo.gif';

// $config['app']['debug'] = false;

$config['app']['db'] = array(
	// 'host'=>'sysmetic.ckz9pwf0k9qx.ap-northeast-1.rds.amazonaws.com',
	// 'host'=>'localhost',
        'host'=>'sysmetic.c5hgfzyunggm.ap-northeast-2.rds.amazonaws.com',
	//'user'=>'sysmetic',
        'user'=>'web',
	//'password'=>'sys2016!@',
        'password'=>'web150302,.',
	'name'=>'sysmetic_bak'
);

$config['app']['memcached'] = array(
	'localhost'
);

// 최소 위탁 가능금액
$config['app']['strategy.min_price'] = array(
    1 => '1만원 ~500만원',
    '500만원 ~ 1000만원',
    '1000만원 ~ 2000만원',
    '2000만원 ~ 5000만원',
    '5000만원 ~ 1억',
    '1억 ~  2억',
    '2억 ~ 3억',
    '3억 ~ 4억',
    '4억 ~ 5억',
    '5억 ~ 10억',
    '10억 이상',
);
