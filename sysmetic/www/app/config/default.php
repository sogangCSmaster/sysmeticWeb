<?php
// $app->config('변수', '값');
$config['app']['mode'] = $_ENV['SLIM_MODE'];
$config['app']['templates.path'] = '/home/sysmetic/www/app/templates';

$config['app']['name'] = 'SYSMETIC TRADERS';
$config['app']['system_sender_name'] = 'SYSMETIC TRADERS';
$config['app']['system_sender_email'] = 'noreply@sysmetic.com';
$config['app']['admin_email'] = 'help@sysmetic.co.kr'; // 교육, 서비스 문의하기 수신용 이메일주소
$config['app']['admin_phone'] = '0263381880';          // sms 발신번호
$config['app']['admin_phone2'] = '01098645824';         // sms

$config['app']['naver_client_id'] = '15x9THYLrLxA7nsl99Kk';
$config['app']['naver_client_secret'] = 'Da70ntaVcI';
$config['app']['facebook_client_id'] = '869896133071334';
$config['app']['facebook_client_secret'] = 'b5ad371a3e92ba75a9acb7160ffc9cfb';

// sms
$config['app']['sms.userid'] = 'sysmetic';      // sms 문자나라 id
$config['app']['sms.userpw'] = 'sys2015sys';     // sms 문자나라 pw

// 실명인증
$config['app']['auth.userid'] = 'SJQT001';             // 실명인증 id
