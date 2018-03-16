<?php
// 라우팅
$app->get('/', function () use ($app, $log, $isLoggedIn) {
	$brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
	$brokers_has_logo = array();
	foreach($brokers as $k => $broker){
		if(!empty($broker['logo'])) $brokers_has_logo[] = $broker;
	}

	/*
	$minus_broker_count = count($brokers_has_logo)%5;
	$brokers_has_logo = array_slice($brokers_has_logo, 0, count($brokers_has_logo)-$minus_broker_count);
	*/
	
	$trader_count = $app->db->selectCount('user', array('user_type'=>'T'));

	// $nomal_count = $app->db->selectCount('user', array('user_type'=>'N'));
	
	$result = $app->db->conn->query('SELECT SUM(money) FROM strategy_funding');
	$row = $result->fetch_array();
	$total_funding = $row[0];

	$result = $app->db->conn->query('SELECT SUM(investor) FROM strategy_funding');
	$row = $result->fetch_array();
	$total_investor = $row[0];

	$result = $app->db->conn->query('SELECT SUM(total_profit_rate) FROM strategy WHERE is_delete = \'0\' AND is_open = \'1\' AND is_operate = \'1\'');
	$row = $result->fetch_array();
	$sum_total_profit_rate = $row[0];

	$count_total_profit_rate = $app->db->selectCount('strategy', array('is_delete'=>'0','is_open'=>'1','is_operate'=>'1'));

	// 메인화면수익률=당일시스템트레이딩누적수익률합/당일운영중인시스템트레이딩갯수
	if ($count_total_profit_rate != 0) {
	//die('aaa');
		$main_profit_rate = round($sum_total_profit_rate/$count_total_profit_rate, 2);
	}

	$follower_top_strategies_str = '';
	$top_strategies_str = '';
	$is_first = true;
	
	$follower_top_strategies = $app->db->select('strategy', '*', array('is_delete'=>'0','is_open'=>'1','is_operate'=>'1'), array('followers_count'=>'desc'), 0, 5);
	foreach($follower_top_strategies as $k=>$strategy){
		$developer = $app->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']));
		if(empty($developer['picture_s'])) $developer['picture_s'] = $app->config('default_picture_s');
		$follower_top_strategies[$k]['developer'] = $developer;

		// 산식
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy['strategy_id']), array('basedate'=>'asc'));
		$daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$strategy['strategy_id']), array('basedate'=>'asc'));
		
		$follower_top_strategies[$k]['daily_values'] = $daily_values;

		// 표를 그리기 위한 데이터
		if ($is_first) {
			$follower_top_strategies_str = getChartDataString($daily_values_graph, 'sm_index');//'['.implode(',', $sm_index_array).']';
			$is_first = false;
		}
	}

	$is_first = true;
	$top_strategies = $app->db->select('strategy', '*', array('is_delete'=>'0','is_open'=>'1','is_operate'=>'1'), array('sharp_ratio'=>'desc'), 0, 5);
	foreach($top_strategies as $k=>$strategy){
		$developer = $app->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']));
		if(empty($developer['picture_s'])) $developer['picture_s'] = $app->config('default_picture_s');
		$top_strategies[$k]['developer'] = $developer;

		// 산식
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy['strategy_id']), array('basedate'=>'asc'));
		$daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$strategy['strategy_id']), array('basedate'=>'asc'));
		$top_strategies[$k]['daily_values'] = $daily_values;

		// 표를 그리기 위한 데이터
		if ($is_first) {
			$top_strategies_str =  getChartDataString($daily_values_graph, 'sm_index');//'['.implode(',', $sm_index_array ).']';
			$is_first = false;
		}
	}

	// 통합 기준가(모든 전략의 공통된 날짜의 통합기준가)
	$univ_values = $app->db->select('univ_index', '*', array(), array('basedate'=>'asc'));
	$univ_values_array = array();
	foreach($univ_values as $k=>$v){
		$m_timestamp = strtotime($v['basedate'])*1000;
		$univ_values_array[] = '['.$m_timestamp.','.$v['sm_index'].']';
	}
	$univ_values_str = '['.implode(',', $univ_values_array).']';
	
	$app->render('index.php', array('brokers_has_logo'=>$brokers_has_logo, 'main_profit_rate'=>$main_profit_rate, 'trader_count'=>$trader_count, 'total_investor'=>$total_investor, 'total_funding'=>$total_funding, 'top_strategies'=>$top_strategies, 'follower_top_strategies'=>$follower_top_strategies, 'univ_values_str'=>$univ_values_str, 'top_strategies_str'=>$top_strategies_str, 'follower_top_strategies_str'=>$follower_top_strategies_str ));
});

$app->get('/agreement', function() use ($app, $log, $isLoggedIn) {
	$app->render('rules.php', array());
});

$app->get('/privacy', function() use ($app, $log, $isLoggedIn) {
	$app->render('terms.php', array());
});

$app->get('/intro', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'intro';

	$app->render('intro.php', array('current_menu'=>$current_menu));
});

$app->get('/guide', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'guide';

	$app->render('guide.php', array('current_menu'=>$current_menu));
});

$app->get('/training', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'training';

	$contact_email = $app->config('admin_email');

	$app->render('training.php', array('current_menu'=>$current_menu, 'contact_email'=>$contact_email));
});

$app->get('/escrow', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'escrow';

	$contact_email = $app->config('admin_email');

	$app->render('Escrow.php', array('current_menu'=>$current_menu, 'contact_email'=>$contact_email));
});

$app->post('/training/ask', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	$ask_body = $app->request->post('ask_body');

	if(empty($ask_body)){
		echo json_encode(array('result'=>false));
		$app->stop();
	}

	$from = $_SESSION['user']['email'];
	$from_name = empty($_SESSION['user']['name']) ? '이름없음': $_SESSION['user']['name'];
	$to = $app->config('admin_email');
	$subject = $app->config('name').' 교육 문의하기';
	$content = '
	<!DOCTYPE html>
	<html>
	<head>
	<meta charset="utf-8">
	<title>'.$app->config('name').' 교육 문의하기</title>
	</head>
	<body>
	<div>'.$ask_body.'</div>
	</body>
	</html>
	';

	sendEncodedMail($from, $from_name, $to, $subject, $content);

	echo json_encode(array('result'=>true));
});

$app->get('/service', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'service';

	$contact_email = $app->config('admin_email');

	$app->render('service.php', array('current_menu'=>$current_menu, 'contact_email'=>$contact_email));
});

$app->get('/service_edu', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'service_edu';

	$contact_email = $app->config('admin_email');

	$app->render('service_3.php', array('current_menu'=>$current_menu, 'contact_email'=>$contact_email));
});

$app->post('/service/ask', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	$ask_body = $app->request->post('ask_body');

	if(empty($ask_body)){
		echo json_encode(array('result'=>false));
		$app->stop();
	}

	$from = $_SESSION['user']['email'];
	$from_name = empty($_SESSION['user']['name']) ? '이름없음': $_SESSION['user']['name'];
	$to = $app->config('admin_email');;
	$subject = $app->config('name').' 서비스 문의하기';
	$content = '
	<!DOCTYPE html>
	<html>
	<head>
	<meta charset="utf-8">
	<title>'.$app->config('name').' 서비스 문의하기</title>
	</head>
	<body>
	<div>'.$ask_body.'</div>
	</body>
	</html>
	';

	sendEncodedMail($from, $from_name, $to, $subject, $content);

	echo json_encode(array('result'=>true));
});

$app->get('/signin', function() use ($app, $log, $isLoggedIn) {
	$redirect_url = $app->request->get('redirect_url');
	if(empty($redirect_url)){
		$redirect_url = '';
	}

	if($isLoggedIn()){
		$app->redirect('/');
	}else{
		// $app->render('login.php', array('redirect_url'=>$redirect_url, 'show_signin'=>true));
		$app->render('login.php', array('redirect_url'=>$redirect_url));
	}
});

$app->get('/signin/naver', function() use ($app, $log, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}else{
		// state token으로 사용할 랜덤 문자열을 생성
		$state = generate_state();
		$_SESSION['naver_state_token'] = $state;

		$app->redirect('https://nid.naver.com/oauth2.0/authorize?client_id='.$app->config('naver_client_id').'&response_type=code&redirect_uri=http%3A%2F%2Fsysmetic.co.kr%2Fsignin%2Fnaver%2Fcallback&state='.$state);
	}
});

$app->get('/signin/naver/callback', function() use ($app, $log, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

	$platform = 'naver';

	$code = $app->request->get('code');
	$state = $app->request->get('state');

	if(empty($_SESSION['naver_state_token'])){
		$app->redirect('/signin');
	}

	$stored_state = $_SESSION['naver_state_token'];

	if( $state != $stored_state ) {
		$app->redirect('/signin');
	} else {
		// 성공	
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://nid.naver.com/oauth2.0/token?client_id='.$app->config('naver_client_id').'&client_secret='.$app->config('naver_client_secret').'&grant_type=authorization_code&state='.$state.'&code='.$code);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);

	/*
	{ "access_token":"AAAANg1stSkUX0Tc33imS/iJipIYpQlGlxSDc0W5Bq99+dEK7jBXR1wyp5Oe5Mc52U76maYG0P7TUydZ3R/UNCqs3L8=", "refresh_token":"QMQNiiHp5NgQtb3ZlU0nv0GMWsTI0Q0Y810RzHHEMNKrXm3TiiaUprw6MWiiq0RC3VM4TGT5m8bcjiiTXXbdt8Qgz8pqH7M2EwmOtKxjuc1KfDEie", "token_type":"bearer", "expires_in":"3600" }
	*/

	$response = json_decode($result, true);
	// print_r($response);
	$headers = array( 
		'Authorization: Bearer '.$response['access_token']
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://apis.naver.com/nidlogin/nid/getUserProfile.xml');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);

	/*
	<?xml version="1.0" encoding="UTF-8" ?>
	<data>
	<result>
		<resultcode>00</resultcode>
		<message>success</message>
	</result>
	<response>
	<email><![CDATA[o242o242@naver.com]]></email>
	<nickname><![CDATA[훈남]]></nickname>
	<enc_id><![CDATA[d0560da07390f7fae1a747d646a112c71025338f84994c7be4049eabdd4c6459]]></enc_id>
	<profile_image><![CDATA[https://phinf.pstatic.net/contactthumb.phinf/76/2011/2/16/o242o242_1297855567906.jpg?type=s80]]></profile_image>
	<age><![CDATA[30-39]]></age>
	<gender>M</gender>
	<id><![CDATA[5368313]]></id>
	<birthday><![CDATA[04-17]]></birthday>
	</response>
	</data>
	*/
	$xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
	// print_r($xml);

	$_SESSION['temp_account'] = array(
		'email' => (string)$xml->response->email,
		'nickname' => (string)$xml->response->nickname,
		'profile_image' => (string)$xml->response->profile_image,
		'platform' => (string)$platform,
		'platform_uid' => (string)$xml->response->id
	);

	$member_info = $app->db->selectOne('user', '*', array('platform'=>$platform,'platform_uid'=>$xml->response->id));
	if(!empty($member_info)){
		/*
		if(!empty($remember_me)){
			$token = createAuthorKey();
			$app->db->insert('auth_token', array('uid'=>$member_info['uid'], 'token'=>$token));
			setcookie('remember_uid', $member_info['uid'], time()+60*60*24*30, '/');
			setcookie('remember_me', $token, time()+60*60*24*30, '/');
		}
		*/

		unset($member_info['member_password']);
		$_SESSION['user'] = $member_info;

		/*
		if(in_array($email, $app->config('administrators'))){
			$_SESSION['user']['user_type'] = 'admin';
		}else{
			$_SESSION['user']['user_type'] = 'member';
		}
		*/
		if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');

		if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');

		$app->redirect('/');
	}else{
		$app->redirect('/signup/'.$platform);	
	}
});

$app->get('/signin/facebook', function() use ($app, $log, $isLoggedIn) {
	require_once __DIR__.'/../lib/facebook.php';

	$platform = 'facebook';

	if($isLoggedIn()){
		$app->redirect('/');
	}

	$facebook = new Facebook(array(
	  'appId'  => $app->config('facebook_client_id'),
	  'secret' => $app->config('facebook_client_secret'),
	));

	$user = $facebook->getUser();

	if ($user) {
	  try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
	  } catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	  }
	}

		/*
    [id] => 10206134201814867
    [first_name] => Kyoung Min
    [gender] => male
    [last_name] => Kim
    [link] => https://www.facebook.com/app_scoped_user_id/10206134201814867/
    [locale] => ko_KR
    [name] => Kyoung Min Kim
    [timezone] => 9
    [updated_time] => 2013-11-20T08:15:18+0000
    [verified] => 1

    [id] => 10206134201814867
    [email] => spotlight21c@dreamwiz.com
    [first_name] => Kyoung Min
    [gender] => male
    [last_name] => Kim
    [link] => https://www.facebook.com/app_scoped_user_id/10206134201814867/
    [locale] => ko_KR
    [name] => Kyoung Min Kim
    [timezone] => 9
    [updated_time] => 2013-11-20T08:15:18+0000
    [verified] => 1
		*/
	if ($user) {
		// print_r($user_profile);
		// $logoutUrl = $facebook->getLogoutUrl();
	} else {
		$params = array(
			'scope' => 'email'
			// 'redirect_uri' => 'https://www.myapp.com/post_login_page'
		);

		$app->redirect($facebook->getLoginUrl($params));
	}

	$_SESSION['temp_account'] = array(
		'email' => $user_profile['email'],
		'nickname' => $user_profile['last_name'].' '.$user_profile['first_name'],
		'profile_image' => 'https://graph.facebook.com/'.$user_profile['id'].'/picture',
		'platform' => $platform,
		'platform_uid' => $user_profile['id']
	);

	$member_info = $app->db->selectOne('user', '*', array('platform'=>$platform,'platform_uid'=>$user_profile['id']));
	if(!empty($member_info)){
		/*
		if(!empty($remember_me)){
			$token = createAuthorKey();
			$app->db->insert('auth_token', array('uid'=>$member_info['uid'], 'token'=>$token));
			setcookie('remember_uid', $member_info['uid'], time()+60*60*24*30, '/');
			setcookie('remember_me', $token, time()+60*60*24*30, '/');
		}
		*/

		unset($member_info['member_password']);
		$_SESSION['user'] = $member_info;

		/*
		if(in_array($email, $app->config('administrators'))){
			$_SESSION['user']['user_type'] = 'admin';
		}else{
			$_SESSION['user']['user_type'] = 'member';
		}
		*/
		if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');

		if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');

		$app->redirect('/');
	}else{
		$app->redirect('/signup/'.$platform);	
	}

});

// 로그인 로직은 signin, siginin/json, slim.before.router hook, set_password 여기에 사용됨
$app->post('/signin', function() use ($app, $log, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

	$email = $app->request->post('email');
	$password = $app->request->post('password');
	$redirect_url = $app->request->post('redirect_url');
	$remember_me = $app->request->post('remember_me');

	if(empty($email)){
		$app->flash('error', '아이디를 입력해주세요');
		$app->redirect('/signin');
	}

	if(empty($password)){
		$app->flash('error', '비밀번호를 입력해주세요');
		$app->redirect('/signin');
	}

	// 일반로그인시에는 플랫폼이 없는 계정만 로그인됨
	$member_info = $app->db->selectOne('user', '*', array('email'=>$email, 'platform'=>''));
	if(!empty($member_info)){
		if(validate_password($password, $member_info['user_password'])){
			if(!empty($remember_me)){
				$token = createAuthorKey();
				$app->db->insert('auth_token', array('uid'=>$member_info['uid'], 'token'=>$token));
				setcookie('remember_uid', $member_info['uid'], time()+60*60*24*30, '/');
				setcookie('remember_me', $token, time()+60*60*24*30, '/');
			}

			unset($member_info['member_password']);
			$_SESSION['user'] = $member_info;

			/*
			if(in_array($email, $app->config('administrators'))){
				$_SESSION['user']['user_type'] = 'admin';
			}else{
				$_SESSION['user']['user_type'] = 'member';
			}
			*/
			if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');

			if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');

			if(!empty($redirect_url)){
				$app->redirect($redirect_url);
			}else $app->redirect('/');
			
		}else{
			$app->flash('error', '아이디 또는 비밀번호가 일치하지 않습니다.');
			$app->redirect('/signin');
		}
	}else{
		$app->flash('error', '아이디 또는 비밀번호가 일치하지 않습니다.');
		$app->redirect('/signin');
	}

});

$app->post('/signin/json', function() use ($app, $log, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

	$email = $app->request->post('email');
	$password = $app->request->post('password');
	$remember_me = $app->request->post('remember_me');

	if(empty($email)){
		echo json_encode(array('result'=>false, 'error_type'=>'wrong_id'));
		$app->stop();
	}

	if(empty($password)){
		echo json_encode(array('result'=>false, 'error_type'=>'wrong_password'));
		$app->stop();
	}

	$member_info = $app->db->selectOne('user', '*', array('email'=>$email));
	if(!empty($member_info)){
		if(validate_password($password, $member_info['user_password'])){
			if(!empty($remember_me)){
				$token = createAuthorKey();
				$app->db->insert('auth_token', array('uid'=>$member_info['uid'], 'token'=>$token));
				setcookie('remember_uid', $member_info['uid'], time()+60*60*24*30, '/');
				setcookie('remember_me', $token, time()+60*60*24*30, '/');
			}

			unset($member_info['member_password']);
			$_SESSION['user'] = $member_info;

			/*
			if(in_array($email, $app->config('administrators'))){
				$_SESSION['user']['user_type'] = 'admin';
			}else{
				$_SESSION['user']['user_type'] = 'member';
			}
			*/
			if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');

			if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');

			echo json_encode(array('result'=>true));
			$app->stop();
		}else{
			echo json_encode(array('result'=>false, 'error_type'=>'wrong_password'));
			$app->stop();
		}
	}else{
		echo json_encode(array('result'=>false, 'error_type'=>'wrong_id'));
		$app->stop();
	}
});


$app->get('/logout', function() use ($app) {
	$remember_uid = $app->getCookie('remember_uid');
	$remember_me = $app->getCookie('remember_me');
		
	if(!empty($remember_uid) && !empty($remember_me)){
		$exist_token = $app->db->delete('auth_token', array('uid'=>$remember_uid, 'token'=>$remember_me));
	}

	setcookie('remember_uid', '', time()-60*60*24*30, '/');
	setcookie('remember_me', '', time()-60*60*24*30, '/');		

	session_unset();
	session_destroy();
	$app->redirect('/');
});


// pjh 회원가입유형 선택
$app->get('/join_select', function() use($app, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

    $type = $app->request->get('type');
    $type = (empty($type)) ? 'N' : $type;

	$platform = $app->request->get('platform');
	if(empty($platform)){
		$platform = '';
	}

	$app->render('join_select.php', array('type'=>$type, 'platform'=>$platform));
});

// 약관동의
$app->get('/agree', function() use ($app, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

    $type = $app->request->get('type');
	$platform = $app->request->get('platform');
	if(empty($platform)){
		$platform = '';
	}

	$app->render('agree.php', array('type'=>$type, 'platform'=>$platform));
});

$app->post('/agree_ok', function() use ($app, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

	$agree1 = $app->request->post('agree1');
	$agree2 = $app->request->post('agree2');
	$platform = $app->request->post('platform');

	if(!empty($agree1) && !empty($agree2)){
		$_SESSION['agree'] = true;
	}else{
		$app->redirect('/agree');
	}

	if(empty($platform)){
		$app->redirect('/signup');
	}else{
		$app->redirect('/signup/'.$platform);
	}
});

$app->get('/signup/:platform', function($platform) use ($app, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

	if(empty($_SESSION['agree'])){
		$app->redirect('/agree?platform='.$platform);
	}

	if(empty($_SESSION['temp_account'])){
		$app->redirect('/signin');
	}

	$app->render('join_platform.php');
});

$app->get('/signup', function() use ($app, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

	if(empty($_SESSION['agree'])){
		$app->redirect('/agree');
	}

	$app->render('join.php');
});

$app->post('/signup/:platform', function($platform) use ($app, $log, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

	if(empty($_SESSION['agree'])){
		$app->redirect('/agree?platform='.$platform);
	}

	$user_type = $app->request->post('user_type');
	if(empty($_SESSION['temp_account']['email'])){
		$email1 = $app->request->post('email1');
		$email2 = $app->request->post('email2');
		$email = $email1.'@'.$email2;
	}else{
		$email = $_SESSION['temp_account']['email'];
	}
	$name = $app->request->post('name');
	$nickname = $app->request->post('nickname');
	$mobile = $app->request->post('mobile');
	$birthday = $app->request->post('birthday');
	$sido = $app->request->post('sido');
	$gugun = $app->request->post('gugun');
	$gender = $app->request->post('gender');
	$alarm_feeds = $app->request->post('alarm_feeds');
	$alarm_all = $app->request->post('alarm_all');

	// 입력내용 되살리기용
	$app->flash('user_type', $user_type);
	if(empty($_SESSION['temp_account']['email'])){
		$app->flash('email1', $email1);
		$app->flash('email2', $email2);
	}
	$app->flash('name', $name);
	$app->flash('nickname', $nickname);
	$app->flash('mobile', $mobile);
	$app->flash('birthday', $birthday);
	$app->flash('sido', $sido);
	$app->flash('gugun', $gugun);
	$app->flash('gender', $gender);
	$app->flash('alarm_feeds', $alarm_feeds);
	$app->flash('alarm_all', $alarm_all);

	if(!empty($user_type) && in_array($user_type, array('N', 'T', 'B', 'A'))){
	}else{
		$user_type = 'N';
	}

	if(empty($email)){
		$app->flash('error', '이메일과 비밀번호는 필수 입력사항입니다.');
		$app->redirect('/signup/'.$platform);
	}else{
		if(!isEmail($email)){
			$app->flash('error', '이메일이 올바르지 않습니다.');
			$app->redirect('/signup/'.$platform);
		}
	}
	
	if(empty($name)){
		// $app->flash('error', 'name is empty');
		// $app->redirect('/signup');
		$name = '';
	}

	if(empty($nickname)){
		$app->flash('error', '닉네임은 필수 입력사항입니다.');
		$app->redirect('/signup/'.$platform);
		// $nickname = '';
	}

	if(!empty($mobile)){
		if(preg_match('/^[0-9]{10,11}$/', $mobile)){
		}else{
			$app->flash('error', '정확한 휴대폰 번호를 확인해 주세요.');
			$app->redirect('/signup/'.$platform);
		}
	}else{
		$mobile = '';
	}

	if(!empty($birthday)){
		if(preg_match('/^[0-9]{8}$/', $birthday)){
		}else{
			$app->flash('error', '생년월일이 올바르지 않습니다.');
			$app->redirect('/signup/'.$platform);
		}
	}else{
		$birthday = '';
	}

	if($gender != 'M'){
		$gender == 'F';
	}

	if(empty($sido)){
		$sido = '';
	}

	if(empty($gugun)){
		$gugun = '';
	}

	$exist_member = $app->db->selectOne('user', '*', array('platform'=>$_SESSION['temp_account']['platform'],'platform_uid'=>$_SESSION['temp_account']['platform_uid']));

	if(!empty($exist_member)){
		$app->flash('error', '이미 가입되었습니다');
		$app->redirect('/signin');
	}

	$now = time();
	
	$new_member = array(
		'user_type'=>$user_type,
		'email'=>$email,
		'name'=>$name,
		'nickname'=>$nickname,
		'platform'=>$_SESSION['temp_account']['platform'],
		'platform_uid'=>$_SESSION['temp_account']['platform_uid'],
		'user_password'=>'',
		'picture'=>$_SESSION['temp_account']['profile_image'],
		'picture_s'=>$_SESSION['temp_account']['profile_image'],
		'mobile'=>$mobile,
		'birthday'=>$birthday,
		'sido'=>$sido,
		'gugun'=>$gugun,
		'gender'=>$gender,
		'alarm_feeds'=>$alarm_feeds ? '1' : '0',
		'alarm_all'=>$alarm_all ? '1' : '0'
	);

	$new_member_id = $app->db->insert('user', $new_member);

	$url = $app->config('scheme').'://'.$app->config('host');
	
	ob_start();
    include $app->config('templates.path').'/mail_signup.php';
    $content = ob_get_contents();
	ob_end_clean();

	$from = $app->config('system_sender_email');
	$from_name = $app->config('system_sender_name');
	$to = $email;
	$subject = $app->config('name').' 회원가입';
	sendmail($from, $from_name, $to, $subject, $content);

	unset($_SESSION['temp_account']);

	$_SESSION['user'] = $new_member;
	$_SESSION['user']['uid'] = $new_member_id;
	if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');
	if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');

	$app->flash('success', '회원가입이 완료되었습니다');
	// $app->redirect('/');
	$app->redirect('/signin');
});

$app->post('/signup', function() use ($app, $log, $isLoggedIn) {
	if($isLoggedIn()){
		$app->redirect('/');
	}

	if(empty($_SESSION['agree'])){
		$app->redirect('/agree');
	}

	$user_type = $app->request->post('user_type');
	$email1 = $app->request->post('email1');
	$email2 = $app->request->post('email2');
	$email = $email1.'@'.$email2;
	$password = $app->request->post('password');
	$password_confirm = $app->request->post('password_confirm');
	$name = $app->request->post('name');
	$nickname = $app->request->post('nickname');
	$mobile = $app->request->post('mobile');
	$birthday = $app->request->post('birthday');
	$sido = $app->request->post('sido');
	$gugun = $app->request->post('gugun');
	$gender = $app->request->post('gender');
	$alarm_feeds = $app->request->post('alarm_feeds');
	$alarm_all = $app->request->post('alarm_all');

	// 입력내용 되살리기용
	$app->flash('user_type', $user_type);
	$app->flash('email1', $email1);
	$app->flash('email2', $email2);
	$app->flash('name', $name);
	$app->flash('nickname', $nickname);
	$app->flash('mobile', $mobile);
	$app->flash('birthday', $birthday);
	$app->flash('sido', $sido);
	$app->flash('gugun', $gugun);
	$app->flash('gender', $gender);
	$app->flash('alarm_feeds', $alarm_feeds);
	$app->flash('alarm_all', $alarm_all);

	if(!empty($user_type) && in_array($user_type, array('N', 'T', 'B', 'A'))){
	}else{
		$user_type = 'N';
	}

	if(empty($email)){
		$app->flash('error', '이메일과 비밀번호는 필수 입력사항입니다.');
		$app->redirect('/signup');
	}else{
		if(!isEmail($email)){
			$app->flash('error', '이메일이 올바르지 않습니다.');
			$app->redirect('/signup');
		}
	}

	if(empty($password) || empty($password_confirm)){
		$app->flash('error', '이메일과 비밀번호는 필수 입력사항입니다.');
		$app->redirect('/signup');
	}else{
		if($password != $password_confirm){
			$app->flash('error', '비밀번호가 일치하지 않습니다.');
			$app->redirect('/signup');
		}

		if(strlen($password) < 6 || strlen($password) >= 20){
			$app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
			$app->redirect('/signup');
		}

		if(preg_match('/^(?=.*\d)(?=.*[a-zA-Z]).{6,19}$/', $password)){

		}else{
			$app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
			$app->redirect('/signup');
		}

		$password_hash = create_hash($password);
	}
	
	if(empty($name)){
		// $app->flash('error', 'name is empty');
		// $app->redirect('/signup');
		$name = '';
	}

	if(empty($nickname)){
		$app->flash('error', '닉네임은 필수 입력사항입니다.');
		$app->redirect('/signup');
		// $nickname = '';
	}

	if(!empty($mobile)){
		if(preg_match('/^[0-9]{10,11}$/', $mobile)){
		}else{
			$app->flash('error', '정확한 휴대폰 번호를 확인해 주세요.');
			$app->redirect('/signup');
		}
	}else{
		$mobile = '';
	}

	if(!empty($birthday)){
		if(preg_match('/^[0-9]{8}$/', $birthday)){
		}else{
			$app->flash('error', '생년월일이 올바르지 않습니다.');
			$app->redirect('/signup');
		}
	}else{
		$birthday = '';
	}

	if($gender != 'M'){
		$gender == 'F';
	}

	if(empty($sido)){
		$sido = '';
	}

	if(empty($gugun)){
		$gugun = '';
	}

	$exist_member = $app->db->selectOne('user', '*', array('email'=>$email));

	if(!empty($exist_member)){
		$app->flash('error', '이미 가입된 이메일 입니다. 다른 이메일로 가입해주세요');
		$app->redirect('/signup');
	}

	$now = time();
	
	$new_member = array(
		'user_type'=>$user_type,
		'email'=>$email,
		'name'=>$name,
		'nickname'=>$nickname,
		'platform'=>'',
		'platform_uid'=>'',
		'user_password'=>$password_hash,
		'mobile'=>$mobile,
		'birthday'=>$birthday,
		'sido'=>$sido,
		'gugun'=>$gugun,
		'gender'=>$gender,
		'alarm_feeds'=>$alarm_feeds ? '1' : '0',
		'alarm_all'=>$alarm_all ? '1' : '0'
	);

	$new_member_id = $app->db->insert('user', $new_member);

	$url = $app->config('scheme').'://'.$app->config('host');
	
	ob_start();
    include $app->config('templates.path').'/mail_signup.php';
    $content = ob_get_contents();
	ob_end_clean();

	$from = $app->config('system_sender_email');
	$from_name = $app->config('system_sender_name');
	$to = $email;
	$subject = $app->config('name').' 회원가입';
	sendmail($from, $from_name, $to, $subject, $content);

	// $_SESSION['user'] = $new_member;
	// $_SESSION['user']['uid'] = $new_member_id;
	// if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');
	// if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');

	$app->flash('success', '회원가입이 완료되었습니다');
	// $app->redirect('/');
	$app->redirect('/signin');
});

$app->post('/email_check', function() use ($app, $isLoggedIn) {
	$email = $app->request->post('email');

	$is_exist = false;

	if(!empty($email)){
		$exist_member = $app->db->selectCount('user', array('email'=>$email));
		if($exist_member > 0){
			$is_exist = true;
		}
	}

	echo json_encode(array('result'=>$is_exist));
});

$app->get('/forget_password', function() use ($app, $isLoggedIn) {
	$redirect_url = '';
	$app->render('join.php', array('redirect_url'=>$redirect_url, 'show_forget_password'=>true));
});

$app->post('/forget_password', function() use ($app, $log, $isLoggedIn) {
	$email = $app->request->post('email');
	
	if(empty($email)){
		$app->flash('error', 'email is empty');
		$app->redirect('/forget_password');
	}

	$target_member = $app->db->selectOne('user', '*', array('email'=>$email));

	if(!empty($target_member)){
		$code = createAuthorKey();
		$app->db->update('user', array('password_code'=>$code), array('email'=>$target_member['email']));

		$from = $app->config('system_sender_email');
		$from_name = $app->config('system_sender_name');
		$to = $target_member['email'];
		$subject = 'SYSMETIC TRADERS 비밀번호 재설정 링크를 안내해드립니다.';
		$password_link = $app->request->getScheme().'://'.$app->request->getHost().'/set_password?uid='.$target_member['uid'].'&code='.$code;
		$url = $app->request->getScheme().'://'.$app->request->getHost();
		/*
		$content = '
		<!DOCTYPE html>
		<html>
		<head>
		<meta charset="utf-8">
		<title>'.$app->config('name').' - Reset password</title>
		</head>
		<body>
		<div>
		If you want to reset your password, click this link.<br>
		<a href="'.$app->request->getScheme().'://'.$app->request->getHost().'/set_password?uid='.$target_member['uid'].'&code='.$code.'" target="_blank">Reset password</a>
		</div>
		</body>
		</html>
		';
		*/

		ob_start();
        include $app->config('templates.path').'/mail_password.php';
        $content = ob_get_contents();
        ob_end_clean();

		sendmail($from, $from_name, $to, $subject, $content);

		$type = $app->request->post('type');
		if(!empty($type) && $type == 'json'){
			echo json_encode(array('result'=>true));
			$app->stop();
		}else{
			$app->flash('success', 'Sent email to reset password.');
		}
	}else{
		$type = $app->request->post('type');
		if(!empty($type) && $type == 'json'){
			echo json_encode(array('result'=>false));
			$app->stop();
		}else{
			$app->flash('error', 'This email is wrong');
		}
	}

	$app->redirect('/forget_password');
});

$app->get('/set_password', function() use ($app, $log, $isLoggedIn) {
	$member_id = $app->request->get('uid');
	$code = $app->request->get('code');
	
	if(empty($member_id)){
		$app->flash('error', 'The url is invalid, please try again.');
		$app->redirect('/forget_password');
	}

	if(empty($code)){
		$app->flash('error', '링크가 유효하지 않습니다');
		$app->redirect('/signin');
	}

	$member_info = $app->db->selectOne('user', '*', array('uid'=>$member_id));
	if(empty($member_info)){
		$app->flash('error', 'The url is invalid, please try again.');
		$app->redirect('/forget_password');
	}

	if($member_info['password_code'] != $code){
		$app->flash('error', '링크가 유효하지 않습니다. 다시 시도해주세요');
		$app->redirect('/signin');
	}

	unset($member_info['user_password']);
	$_SESSION['user'] = $member_info;
	
	if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');

	if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');

	// 링크를 재활용하지 못하도록 패스워드 코드를 초기화
	$app->db->update('user', array('password_code'=>''), array('uid'=>$member_id));

	// 비밀번호 변경화면에서 현재 비밀번호를 입력하지 않아도 되게끔 변수를 설정
	$_SESSION['skip_current_password'] = true;

	$app->redirect('/settings/edit');
});

$app->get('/join_broker', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'guide';

	$brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
	foreach($brokers as $k => $broker){
		$s_tools = $app->db->select('system_trading_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
		$a_tools = $app->db->select('api_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
		$brokers[$k]['system_trading_tools'] = $s_tools;
		$brokers[$k]['api_tools'] = $a_tools;
	}

	$app->render('join_broker.php', array('current_menu'=>$current_menu, 'brokers'=>$brokers));
});

$app->post('/join_broker', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	$company = $app->request->post('company');
	$location = $app->request->post('location');
	$work_year = $app->request->post('work_year');
	$position = $app->request->post('position');
	$major = $app->request->post('major');
	$major_etc = $app->request->post('major_etc');
	$my_strategy_count = $app->request->post('my_strategy_count');
	$open_strategy_count = $app->request->post('open_strategy_count');
	$memo = $app->request->post('memo');

	if(empty($company)){
		$company = '';
	}

	if(empty($location)){
		$location = '';
	}

	if(empty($work_year) || !is_numeric($work_year)){
		$work_year = 0;
	}else $work_year = intval($work_year);

	if(empty($position)){
		$location = '';
	}

	if(empty($my_strategy_count) || !is_numeric($my_strategy_count)){
		$my_strategy_count = 0;
	}else $my_strategy_count = intval($my_strategy_count);

	if(empty($open_strategy_count) || !is_numeric($open_strategy_count)){
		$open_strategy_count = 0;
	}else $open_strategy_count = intval($open_strategy_count);

	$major_array = array();
	if(!empty($major)){
		foreach($major as $v){
			if(empty($v)) continue;
			$major_array[] = $v;
		}
	}

	// 마지막에 기타를 넣음
	if(in_array('기타', $major_array) && !empty($major_etc)){
		$major_array[] = str_replace('|', '', $major_etc);
	}

	if(empty($memo)){
		$memo = '';
	}

	$app->db->insert('request_broker', array(
		'uid'=>$_SESSION['user']['uid'],
		'company'=>$company,
		'location'=>$location,
		'work_year'=>$work_year,
		'position'=>$position,
		'my_strategy_count'=>$my_strategy_count,
		'open_strategy_count'=>$open_strategy_count,
		'major'=>empty($major_array) ? '' : implode('|',$major_array),
		'memo'=>$memo
	));

	$app->flash('error', '브로커 등록이 접수되었습니다');
	$app->redirect('/join_broker');
});

$app->get('/settings', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	$app->render('personalInfo.php');
});

$app->get('/settings/edit', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	$app->render('personalInfo_edit.php');
});

$app->post('/settings/edit', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	if(empty($_SESSION['user']['email'])){
		$email1 = $app->request->post('email1');
		$email2 = $app->request->post('email2');
		$email = $email1.'@'.$email2;
	}else{
		$email = $_SESSION['user']['email'];
	}

	$user_type = $app->request->post('user_type');
	$password = $app->request->post('password');
	$password_confirm = $app->request->post('password_confirm');
	$current_password = $app->request->post('current_password');
	$name = $app->request->post('name');
	$nickname = $app->request->post('nickname');
	$mobile = $app->request->post('mobile');
	$birthday = $app->request->post('birthday');
	$sido = $app->request->post('sido');
	$gugun = $app->request->post('gugun');
	$gender = $app->request->post('gender');
	$alarm_feeds = $app->request->post('alarm_feeds');
	$alarm_all = $app->request->post('alarm_all');

	$myinfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']));

	if(empty($myinfo)){
		$app->flash('error', 'Your account is invalid');
		$app->redirect('/logout');
	}

	// 현재 비밀번호 확인
	if(empty($_SESSION['user']['platform'])){
		if(!isset($_SESSION['skip_current_password']) || !$_SESSION['skip_current_password']){
			if(empty($current_password)){
				$app->flash('error', '사용중인 비밀번호가 일치하지 않습니다');
				$app->redirect('/settings/edit');
			}

			if(!validate_password($current_password, $myinfo['user_password'])){
				$app->flash('error', '사용중인 비밀번호가 일치하지 않습니다');
				$app->redirect('/settings/edit');
			}
		}
	}

	$password_hash = $myinfo['user_password'];
	if(!empty($password) && !empty($password_confirm)){
		if($password != $password_confirm){
			$app->flash('error', '새로운 비밀번호가 일치하지 않습니다');
			$app->redirect('/settings/edit');
		}

		if(strlen($password) < 6 || strlen($password) >= 20){
			$app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
			$app->redirect('/settings/edit');
		}

		if(preg_match('/^(?=.*\d)(?=.*[a-zA-Z]).{6,19}$/', $password)){

		}else{
			$app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
			$app->redirect('/settings/edit');
		}

		$password_hash = create_hash($password);
	}

	if(empty($user_type) || ($user_type != 'N' && $user_type != 'T')){
		$user_type = $myinfo['user_type'];
	}

	// 트레이더로 변경 요청하는 경우
	$is_request_trader = false;
	if($myinfo['user_type'] == 'N' && $user_type == 'T'){
		$is_request_trader = true;
	}

	if(empty($name)){
		$name = '';
		// $app->flash('error', '이름을 입력해주세요');
		// $app->redirect('/settings/edit');
	}

	if(empty($nickname)){
		$nickname = '';
		// $app->flash('error', '이름을 입력해주세요');
		// $app->redirect('/settings/edit');
	}

	if(!empty($mobile)){
		if(preg_match('/^[0-9]{10,11}$/', $mobile)){
		}else{
			$app->flash('error', '정확한 휴대폰 번호를 확인해 주세요.');
			$app->redirect('/settings/edit');
		}
	}else{
		$mobile = '';
	}

	if(!empty($birthday)){
		if(preg_match('/^[0-9]{8}$/', $birthday)){
		}else{
			$app->flash('error', '생년월일이 올바르지 않습니다.');
			$app->redirect('/settings/edit');
		}
	}else{
		$birthday = '';
	}

	if($gender != 'M'){
		$gender == 'F';
	}

	if(empty($sido)){
		$sido = '';
	}

	if(empty($gugun)){
		$gugun = '';
	}

	$now = time();

	$profile_url = empty($_SESSION['temp_profile_url']) ? $myinfo['picture'] : $_SESSION['temp_profile_url'];
	$profile_s_url = empty($_SESSION['temp_profile_s_url']) ? $myinfo['picture_s'] : $_SESSION['temp_profile_s_url'];
	
	$app->db->update(
		'user', 
		array(
			'user_type'=>$user_type,
			'is_request_trader'=>$is_request_trader ? '1' : '0',
			'email'=>$email,
			'name'=>$name,
			'nickname'=>$nickname,
			'user_password'=>$password_hash,
			'mobile'=>$mobile,
			'birthday'=>$birthday,
			'sido'=>$sido,
			'gugun'=>$gugun,
			'gender'=>$gender,
			'picture'=>$profile_url,
			'picture_s'=>$profile_s_url,
			'alarm_feeds'=>$alarm_feeds ? '1' : '0',
			'alarm_all'=>$alarm_all ? '1' : '0'
		),
		array('uid'=>$_SESSION['user']['uid'])
	);

	// 새로운 정보를 세션에 다시 넣음
	$updated_member_info = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']));

	if(!empty($updated_member_info)){
		$_SESSION['user'] = $updated_member_info;
		if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');

		if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');
	}
	/*
	foreach($change_member_info as $k => $v){
		// if(isset($_SESSION['user'][$k])){
			$_SESSION['user'][$k] = $v;
		// }
	}
	*/

	unset($_SESSION['skip_current_password']);
	// $app->flash('success', 'Changed your account info.');
	$app->redirect('/settings');
});

$app->post('/settings/upload_picture', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	$profile_url = '';
	$profile_s_url = '';
	$profile_m_url = '';
	$savePath = $app->config('profile.path');
	$max_file_size = 1024 * 1024;

	// 업로드 된 파일이 있는지 확인
	switch($_FILES['profile']['error']){
		case UPLOAD_ERR_OK:
			$filename = $_FILES['profile']['name'];
			$filesize = $_FILES['profile']['size'];
			$filetmpname = $_FILES['profile']['tmp_name'];
			$filetype = $_FILES['profile']['type'];
			$tmpfileext = explode('.', $filename);
			$fileext = $tmpfileext[count($tmpfileext)-1];

			// check filesize
			if($filesize > $max_file_size){
				echo '이미지파일은 1MB 이하로 업로드해주세요.';
				$app->stop();
				// $app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
				// $app->redirect('/settings/edit');
			}

			if(strpos($filetype, 'image') === false){
				echo '이미지 파일만 업로드 가능합니다.';
				$app->stop();
				// $app->flash('error', '이미지 파일만 업로드 가능합니다.');
				// $app->redirect('/settings/edit');
			}
		
			// check upload valid ext
			if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
				echo '확장자가 jpg, gif, png 파일만 업로드가 가능합니다';
				$app->stop();
				// $app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
				// $app->redirect('/settings/edit');
			}

			/*
			$image = getimagesize($filetmpname);
			$width = 360;
			$height = 360;
			
			if($image[0] < $width || $image[1] < $height){
				$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
				$app->redirect('/settings/edit');
			}
			*/
				
			// upload correct method
			if(!is_uploaded_file($filetmpname)){
				echo '정상적인 방법으로 업로드해주세요';
				$app->stop();
				// $app->flash('error', '정상적인 방법으로 업로드해주세요');
				// $app->redirect('/settings/edit');
			}

			// if folder is not exist, create folder
			if(!is_dir($savePath)){
				mkdir($savePath, 0705);
				chmod($savePath, 0707);
			}

			// filename modify
			$saveFilename = md5(uniqid(rand(), true));

			// filename same check
			while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
				$saveFilename = md5(uniqid(rand(), true));
			}

			$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
			$finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
			$finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
			$_SESSION['temp_profile_url'] = $profile_url = $app->config('profile.url').'/'.$saveFilename.'.'.$fileext;
			$_SESSION['temp_profile_s_url'] = $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
			// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

			if(!move_uploaded_file($filetmpname, $finalFilename)){
				echo '업로드에 실패하였습니다';
				$app->stop();
				// $app->flash('error', '업로드에 실패하였습니다');
				// $app->redirect('/settings/edit');
			}

			// 썸네일생성 s, m
			createThumbnail($finalFilename, $finalThumbFilename, 61, 61, false, true);
			// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
			echo $profile_s_url;
			break;
		case UPLOAD_ERR_INI_SIZE:
			echo '업로드 가능 용량을 초과하였습니다';
			$app->stop();
			// $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			// $app->redirect('/settings/edit');
			break;
		case UPLOAD_ERR_FORM_SIZE:
			echo '업로드 가능 용량을 초과하였습니다';
			$app->stop();
			// $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			// $app->redirect('/settings/edit');
			break;
		case UPLOAD_ERR_PARTIAL:
			echo '업로드에 실패하였습니다';
			$app->stop();
			// $app->flash('error', '업로드에 실패하였습니다');
			// $app->redirect('/settings/edit');
			break;
		case UPLOAD_ERR_NO_FILE:
			echo '첨부된 파일이 없습니다';
			$app->stop();
			break;
		default:
			echo '업로드에 실패하였습니다';
			$app->stop();
			// $app->flash('error', '업로드에 실패하였습니다');
			// $app->redirect('/settings/edit');
	}
});

$app->get('/brokers', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'brokers';

	$brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
	foreach($brokers as $k => $broker){
		$s_tools = $app->db->select('system_trading_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
		$a_tools = $app->db->select('api_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
		$brokers[$k]['system_trading_tools'] = $s_tools;
		$brokers[$k]['api_tools'] = $a_tools;
	}

	$app->render('broker.php', array('current_menu'=>$current_menu, 'brokers'=>$brokers));
});

$app->post('/brokers/ask', $authenticateForRole('N,T'), function () use ($app, $log) {
	$broker_id = $app->request->post('broker_id');
	$ask_body = $app->request->post('ask_body');

	if(empty($broker_id) || !is_numeric($broker_id)){
		echo json_encode(array('result'=>false));
		$app->stop();
	}

	if(empty($ask_body)){
		echo json_encode(array('result'=>false));
		$app->stop();
	}

	$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$broker_id));

	if(empty($broker)){
		// $app->halt(404, 'not found');
		echo json_encode(array('result'=>false));
		$app->stop();
	}

	$app->db->insert('qna', array(
		'target'=>'broker',
		'target_value'=>$broker['broker_id'],
		'target_value_text'=>$broker['company'],
		'uid'=>$_SESSION['user']['uid'],
		'name'=>$_SESSION['user']['name'],
		'question'=>$ask_body,
		'answer'=>''
	));

	echo json_encode(array('result'=>true));
});

$app->get('/strategies', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$start = $app->request->get('start');
	if(empty($start) || !is_numeric($start)) $start = 0;
	$count = $app->request->get('count');
	if(empty($count) || !is_numeric($count) || $count > 20) $count = 20;
	$open_search = $app->request->get('open_search');
	$is_open_search = false;
	if(!empty($open_search) && $open_search == '1'){
		$is_open_search = true;
	}

	$sql = 'SELECT * FROM strategy WHERE is_open = \'1\' AND is_operate = \'1\' AND is_delete = \'0\'';

	$q_item = $app->request->get('item');
	$q_term = $app->request->get('term');
	$q = $app->request->get('q');
	$extend_search = $app->request->get('extend_search');
	
	$is_extend_search = false;
	if(!empty($extend_search)){
		$is_extend_search = true;
	}

	$search_params = array();
	if(!$is_extend_search){
		if(!empty($q_term) && ($q_term != 'day' && $q_term != 'position')){
			$q_term = '';
		}

		$q_item_strategy_ids = array();
		if(!empty($q_item)){
			$rows = $app->db->select('strategy_item', '*', array('item_id'=>$q_item));
			foreach($rows as $row){
				$q_item_strategy_ids[] = $row['strategy_id'];
			}
			if(count($q_item_strategy_ids)) $sql .= ' AND strategy_id IN ('.implode(',', $q_item_strategy_ids).')';
			else $sql .= 'AND strategy_id = 0'; // 매칭되는게 없으면 검색결과가 없다는 의미임
		}

		if(!empty($q_term)){
			$sql .= ' AND strategy_term = \''.$app->db->conn->real_escape_string($q_term).'\'';
		}

		if(!empty($q)){
			$sql .= ' AND name LIKE \'%'.$app->db->conn->real_escape_string($q).'%\'';
		}
	}else{
		$sub_sql_array = array();
		$is_open_search = true;

		$search_params['search_strategy_type'] = $app->request->get('search_strategy_type');
		$search_params['search_term'] = $app->request->get('search_term');
		$search_params['search_item'] = $app->request->get('search_item');
		$search_params['search_broker'] = $app->request->get('search_broker');
		$search_params['search_total_profit_rate_min'] = $app->request->get('search_total_profit_rate_min');
		$search_params['search_total_profit_rate_max'] = $app->request->get('search_total_profit_rate_max');
		$search_params['search_yearly_profit_rate_min'] = $app->request->get('search_yearly_profit_rate_min');
		$search_params['search_yearly_profit_rate_max'] = $app->request->get('search_yearly_profit_rate_max');
		$search_params['search_principal_min'] = $app->request->get('search_principal_min');
		$search_params['search_principal_max'] = $app->request->get('search_principal_max');
		$search_params['search_mdd_min'] = $app->request->get('search_mdd_min');
		$search_params['search_mdd_max'] = $app->request->get('search_mdd_max');
		$search_params['search_sharp_ratio_min'] = $app->request->get('search_sharp_ratio_min');
		$search_params['search_sharp_ratio_max'] = $app->request->get('search_sharp_ratio_max');

		if(isset($search_params['search_item']) && is_array($search_params['search_item']) && count($search_params['search_item'])){
			$safe_search_item = array();
			foreach($search_params['search_item'] as $v){
				$safe_search_item[] = $app->db->conn->real_escape_string($v);
			}

			$q_item_strategy_ids = array();
			$result = $app->db->conn->query('SELECT * FROM strategy_item WHERE item_id IN (\''.implode('\',\'', $safe_search_item).'\')');
			while($row = $result->fetch_array()){
				$q_item_strategy_ids[] = $row['strategy_id'];
			}

			if(count($q_item_strategy_ids)) $sql .= ' AND strategy_id IN ('.implode(',', $q_item_strategy_ids).')';
			else $sql .= ' AND strategy_id = 0'; // 매칭되는게 없으면 검색결과가 없다는 의미임
		}
		
		if(isset($search_params['search_strategy_type']) && is_array($search_params['search_strategy_type']) && count($search_params['search_strategy_type'])){
			$safe_search_strategy_type = array();
			foreach($search_params['search_strategy_type'] as $v){
				$safe_search_strategy_type[] = $app->db->conn->real_escape_string($v);
			}

			$sql .= ' AND strategy_type IN (\''.implode('\',\'', $safe_search_strategy_type).'\')';
		}

		if(isset($search_params['search_term']) && is_array($search_params['search_term']) && count($search_params['search_term'])){
			$safe_search_term = array();
			foreach($search_params['search_term'] as $v){
				$safe_search_term[] = $app->db->conn->real_escape_string($v);
			}

			$sql .= ' AND strategy_term IN (\''.implode('\',\'', $safe_search_term).'\')';
		}

		if(isset($search_params['search_broker']) && is_array($search_params['search_broker']) && count($search_params['search_broker'])){
			$safe_search_broker = array();
			foreach($search_params['search_broker'] as $v){
				$safe_search_broker[] = $app->db->conn->real_escape_string($v);
			}

			$sql .= ' AND broker_id IN ('.implode(',', $safe_search_broker).')';
		}

		if(isset($search_params['search_total_profit_rate_min']) && is_numeric($search_params['search_total_profit_rate_min'])){
			$sql .= ' AND total_profit_rate >= '.$app->db->conn->real_escape_string($search_params['search_total_profit_rate_min']);
		}

		if(isset($search_params['search_total_profit_rate_max']) && is_numeric($search_params['search_total_profit_rate_max'])){
			$sql .= ' AND total_profit_rate <= '.$app->db->conn->real_escape_string($search_params['search_total_profit_rate_max']);
		}

		if(isset($search_params['search_yearly_profit_rate_min']) && is_numeric($search_params['search_yearly_profit_rate_min'])){
			$sql .= ' AND yearly_profit_rate >= '.$app->db->conn->real_escape_string($search_params['search_yearly_profit_rate_min']);
		}

		if(isset($search_params['search_yearly_profit_rate_max']) && is_numeric($search_params['search_yearly_profit_rate_max'])){
			$sql .= ' AND yearly_profit_rate <= '.$app->db->conn->real_escape_string($search_params['search_yearly_profit_rate_max']);
		}

		if(isset($search_params['search_principal_min']) && is_numeric($search_params['search_principal_min'])){
			$sql .= ' AND principal >= '.$app->db->conn->real_escape_string($search_params['search_principal_min']);
		}

		if(isset($search_params['search_principal_max']) && is_numeric($search_params['search_principal_max'])){
			$sql .= ' AND principal <= '.$app->db->conn->real_escape_string($search_params['search_principal_max']);
		}

		if(isset($search_params['search_mdd_min']) && is_numeric($search_params['search_mdd_min'])){
			$sql .= ' AND mdd >= '.$app->db->conn->real_escape_string($search_params['search_mdd_min']);
		}

		if(isset($search_params['search_mdd_max']) && is_numeric($search_params['search_mdd_max'])){
			$sql .= ' AND mdd <= '.$app->db->conn->real_escape_string($search_params['search_mdd_max']);
		}

		if(isset($search_params['search_sharp_ratio_min']) && is_numeric($search_params['search_sharp_ratio_min'])){
			$sql .= ' AND sharp_ratio >= '.$app->db->conn->real_escape_string($search_params['search_sharp_ratio_min']);
		}

		if(isset($search_params['search_sharp_ratio_max']) && is_numeric($search_params['search_sharp_ratio_max'])){
			$sql .= ' AND sharp_ratio <= '.$app->db->conn->real_escape_string($search_params['search_sharp_ratio_max']);
		}
	}

	$sort = $app->request->get('sort');
	$sort_by = $app->request->get('sort_by');

	if(!empty($sort_by) && $sort_by == 'asc'){
		$sort_by = 'asc';
	}else{
		$sort_by = 'desc';
	}

	if(!empty($sort)){
		if($sort == 'total_profit_rate'){
			$sql .= ' ORDER BY total_profit_rate '.$sort_by;
		}else if($sort == 'mdd'){
			$sql .= ' ORDER BY mdd '.$sort_by;
		}else if($sort == 'sharp_ratio'){
			$sql .= ' ORDER BY sharp_ratio '.$sort_by;
		}else if($sort == 'followers_count'){
			$sql .= ' ORDER BY followers_count '.$sort_by;
		}else{
			$sort = '';
			$sql .= ' ORDER BY strategy_id '.$sort_by;
		}
	}else{
		$sort = '';
		$sql .= ' ORDER BY sharp_ratio '.$sort_by;
	}

	$sql .= ' LIMIT '.$start.','.$count;
	//echo $sql;

	// 종목
	$items = $app->db->select('item', '*', array(), array('sorting'=>'asc'));

	// 브로커
	$brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));

	$strategies = array();
	$result = $app->db->conn->query($sql);
	while($row = $result->fetch_array()){
		$strategies[] = $row;
	}

	foreach($strategies as $k => $v){
		// 종목
		$strategy_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$v['strategy_id']));

		$item_id_array = array();
		$strategy_items_value = array();
		foreach($strategy_items as $kk=>$vv){
			$item_id_array[] = $vv['item_id'];
		}
		if(count($item_id_array)){
			$result = $app->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
			while($row = $result->fetch_array()){
				$strategy_items_value[] = $row;
			}			
		}

		$strategies[$k]['items'] = $strategy_items_value;

		// 브로커
		$strategies[$k]['broker'] = $app->db->selectOne('broker', '*', array('broker_id'=>$v['broker_id']));

		// 매매툴
		$strategies[$k]['system_tool'] = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$v['tool_id']));

		// 트레이더
		$developer = $app->db->selectOne('user', '*', array('uid'=>$v['developer_uid']));
		$strategies[$k]['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

		// 팔로워
		/*
		$followers_count = $app->db->selectCount('following_strategy', array('strategy_id'=>$v['strategy_id']));
		$strategies[$k]['followers_count'] = $followers_count;
		*/

		// 팔로잉 여부
		$is_following = false;
		if($isLoggedIn()){
			$is_following = $app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'], 'strategy_id'=>$v['strategy_id'])) > 0 ? true : false;
		}		

		$strategies[$k]['is_following'] = $is_following;

		// 산식
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));

		$strategies[$k]['daily_values'] = $daily_values;
		$sm_index_array = array();
                foreach($daily_values as $k1=>$v1){
                        $m_timestamp = strtotime($v1['basedate'])*1000;
                        $sm_index_array[] = '['.$m_timestamp.','.$v1['sm_index'].']';
                }

		// 표를 그리기 위한 데이터
		$strategies[$k]['str_c_price'] = '['.implode(',', $sm_index_array ).']';

		// 펀딩금액
		$total_funding = $app->m->get('strategy_total_funding:'.$v['strategy_id']);
		if($total_funding === false){
			$result = $app->db->conn->query('SELECT SUM(money) FROM strategy_funding WHERE strategy_id = '.$v['strategy_id']);
			$row = $result->fetch_array();
			$total_funding = empty($row[0]) ? 0 : $row[0];
			$app->m->set('strategy_total_funding:'.$v['strategy_id'], $total_funding);
		}
		$strategies[$k]['total_funding'] = $total_funding;

		// 캐시저장
		// $app->m->set('strategy:'.$v['strategy_id'], $strategies[$k]);
	}

	$format = $app->request->get('format');
	if(!empty($format) && $format == 'json'){
		echo json_encode(array('items'=>$strategies));
		$app->stop();
	}

	$app->render('strategy_list.php', array('current_menu'=>$current_menu, 'strategies'=>$strategies, 'items'=>$items, 'brokers'=>$brokers, 'start'=>$start, 'count'=>$count, 'q_item'=>$q_item, 'q_term'=>$q_term, 'sort'=>$sort, 'sort_by'=>$sort_by, 'is_open_search'=>$is_open_search, 'is_extend_search'=>$is_extend_search, 'search_params'=>$search_params));
});

$app->get('/portfolios/strategies', function() use ($app, $log, $isLoggedIn) {
	// 포트폴리오용 전략 검색(일간데이터가 없는것을 제외하고 출력한다)
	// 일간데이터의 시작날짜와 마지막날짜 데이터를 추가
	$current_menu = 'strategies';

	$start = $app->request->get('start');
	if(empty($start) || !is_numeric($start)) $start = 0;
	$count = $app->request->get('count');
	if(empty($count) || !is_numeric($count) || $count > 20) $count = 20;

	$sql = 'SELECT * FROM strategy WHERE is_open = \'1\' AND is_operate = \'1\' AND is_delete = \'0\'';
	$q_item = $app->request->get('item');
	$q_term = $app->request->get('term');
	$q = $app->request->get('q');
	$sort = $app->request->get('sort');

	$q_item_strategy_ids = array();
	if(!empty($q_item)){
		$rows = $app->db->select('strategy_item', '*', array('item_id'=>$q_item));
		foreach($rows as $row){
			$q_item_strategy_ids[] = $row['strategy_id'];
		}

		if(count($q_item_strategy_ids)) $sql .= ' AND strategy_id IN ('.implode(',', $q_item_strategy_ids).')';
	}

	if(!empty($q_term)){
		$sql .= ' AND strategy_term = \''.$app->db->conn->real_escape_string($q_term).'\'';
	}

	if(!empty($q)){
		$sql .= ' AND name LIKE \'%'.$app->db->conn->real_escape_string($q).'%\'';
	}

	if(!empty($sort)){
		if($sort == 'total_profit_rate'){
			$sql .= ' ORDER BY total_profit_rate DESC';
		}else if($sort == 'mdd'){
			$sql .= ' ORDER BY mdd DESC';
		}else if($sort == 'sharp_ratio'){
			$sql .= ' ORDER BY sharp_ratio DESC';
		}else if($sort == 'followers_count'){
			$sql .= ' ORDER BY followers_count DESC';
		}else{
			$sort = '';
			$sql .= ' ORDER BY strategy_id DESC';
		}
	}else{
		$sort = '';
		$sql .= ' ORDER BY strategy_id DESC';
	}

	$sql .= ' LIMIT '.$start.','.$count;

	// 종목
	$items = $app->db->select('item', '*', array(), array('sorting'=>'asc'));

	$strategies = array();
	$result = $app->db->conn->query($sql);
	while($row = $result->fetch_array()){
		$strategies[] = $row;
	}

	$response_strategies = array();
	foreach($strategies as $k => $v){
		// 종목
		$strategy_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$v['strategy_id']));

		$item_id_array = array();
		$strategy_items_value = array();
		foreach($strategy_items as $kk=>$vv){
			$item_id_array[] = $vv['item_id'];
		}
		if(count($item_id_array)){
			$result = $app->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
			while($row = $result->fetch_array()){
				$strategy_items_value[] = $row;
			}			
		}

		$strategies[$k]['items'] = $strategy_items_value;

		// 브로커
		$strategies[$k]['broker'] = $app->db->selectOne('broker', '*', array('broker_id'=>$v['broker_id']));

		// 매매툴
		$strategies[$k]['system_tool'] = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$v['tool_id']));

		// 트레이더
		$developer = $app->db->selectOne('user', '*', array('uid'=>$v['developer_uid']));
		$strategies[$k]['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

		// 산식
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));

		if(count($daily_values) < 2) continue;

		$c_price_array = array();
		foreach($daily_values as $v){
			$c_price_array[] = $v['sm_index'];	
		}

		$strategies[$k]['daily_values'] = $daily_values;

		// 표를 그리기 위한 데이터
		$strategies[$k]['str_sm_index'] = '['.implode(',', $c_price_array).']';

		$strategies[$k]['first_date'] = $daily_values[0]['basedate'];
		$strategies[$k]['last_date'] = $daily_values[count($daily_values)-1]['basedate'];

		$response_strategies[] = $strategies[$k];
	}

	$format = $app->request->get('format');
	if(!empty($format) && $format == 'json'){
		echo json_encode(array('items'=>$response_strategies));
		$app->stop();
	}

	$app->render('strategy_list.php', array('current_menu'=>$current_menu, 'strategies'=>$strategies, 'items'=>$items, 'start'=>$start, 'count'=>$count, 'q_item'=>$q_item, 'q_term'=>$q_term, 'sort'=>$sort));
});

// 모든 전략들의 지표를 소팅 가능하게 레코드에 담는 배치
// 계산된 마지막 지표를 strategy 컬럼에 넣어서 소팅가능하도록 함
// 전체 지표를 한꺼번에 계산하면 memory가 초과해버려서 하나씩 하는것으로 변경함
$app->get('/strategies/:id/fetch', function($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	// 삭제되지 않은것은 전부 업데이트
	$strategies = $app->db->select('strategy', '*', array('strategy_id'=>$id, 'is_delete'=>'0'));
	foreach($strategies as $k => $v){
		// 산식
		$daily_values = $app->m->get('strategy_daily_value:'.$v['strategy_id']);
		if($daily_values === false){
			$daily_values = $app->db->select('strategy_daily', '*', array('strategy_id'=>$v['strategy_id']), array('target_date'=>'asc'));
			$app->m->set('strategy_daily_value:'.$v['strategy_id'], $daily_values);
		}
	
		$new_daily_values = $app->m->get('strategy_new_daily_value:'.$v['strategy_id']);
		if($new_daily_values === false){
			$new_daily_values = calculateData($daily_values);
			$app->m->set('strategy_new_daily_value:'.$v['strategy_id'], $new_daily_values);
		}
		$c_price_array = array();
		foreach($new_daily_values as $v){
			$c_price_array[] = $v['c_price'];	
		}

		$strategies[$k]['daily_values'] = $new_daily_values;

		// 표를 그리기 위한 데이터
		$strategies[$k]['str_c_price'] = '['.implode(',', $c_price_array).']';

		// 월간수익률
		$strategies[$k]['monthly_profit_rate'] = calMonthlyProfitRate($new_daily_values);
		// 년간수익률
		$strategies[$k]['yearly_profit_rate'] = calYearlyProfitRate($new_daily_values);

		if(count($daily_values)){
			$total_profit_rate = $new_daily_values[count($new_daily_values)-1]['total_profit_rate'];
			$yearly_profit_rate = 0;
			foreach($strategies[$k]['yearly_profit_rate'] as $y_k => $y_v){
				$yearly_profit_rate = $y_v;
			}

			$principal = $new_daily_values[count($new_daily_values)-1]['principal'];
			$mdd = $new_daily_values[count($new_daily_values)-1]['mdd'];
			$sharp_ratio = $new_daily_values[count($new_daily_values)-1]['sharp_ratio'];
			$c_price = $new_daily_values[count($new_daily_values)-1]['c_price'];
		}else{
			$total_profit_rate = 0;
			$yearly_profit_rate = 0;
			$principal = 0;
			$mdd = 0;
			$sharp_ratio = 0;
			$c_price = 1000;
		}

		$app->db->update('strategy', 
			array(
				'total_profit_rate'=>$total_profit_rate,
				'yearly_profit_rate'=>$yearly_profit_rate,
				'principal'=>$principal,
				'mdd'=>$mdd,
				'sharp_ratio'=>$sharp_ratio,
				'c_price'=>$c_price
			),
			array('strategy_id'=>$v['strategy_id'])
		);
	}

	/*
	// 사용안함
	$result = $app->db->conn->query('SELECT SUM(c_price) FROM strategy WHERE is_delete = \'0\' AND is_open = \'1\' AND is_operate = \'1\'');
	$row = $result->fetch_array();
	$sum_c_price = $row[0];

	$count_c_price = $app->db->selectCount('strategy', array('is_delete'=>'0','is_open'=>'1','is_operate'=>'1'));

	$univ_value = round($sum_c_price/$count_c_price, 2);

	$exist_count = $app->db->selectCount('univ_value', array('target_date'=>date('Ymd', strtotime('-1 day'))));
	if($exist_count == 0){
		$app->db->insert('univ_value', array('target_date'=>date('Ymd', strtotime('-1 day')),'value'=>$univ_value));
	}
	*/

	echo 'success';
});

$app->post('/strategies/:id/ask', function ($id) use ($app, $log, $isLoggedIn) {
	$ask_body = $app->request->post('ask_body');

	if(empty($ask_body)){
		echo json_encode(array('result'=>false));
		$app->stop();
	}

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	$app->db->insert('qna', array(
		'target'=>'strategy',
		'target_value'=>$id,
		'target_value_text'=>$strategy['developer_name'],
		'strategy_name'=>$strategy['name'],
		'uid'=>$_SESSION['user']['uid'],
		'name'=>$_SESSION['user']['name'],
		'question'=>$ask_body,
		'answer'=>''
	));

	echo json_encode(array('result'=>true));
});

$app->get('/strategies/:id/info', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	// 산식
	$strategy['daily_values'] = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy['strategy_id']), array('basedate'=>'asc'));

	$app->render('strategy_view_tab1.php', array('current_menu'=>$current_menu, 'strategy'=>$strategy));
});

$app->get('/strategies/:id/daily', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	$page = $app->request->get('page');
	if(empty($page) || !is_numeric($page)) $page = 1;
	$count = 15;
	$start = ($page - 1) * $count;
	$page_count = 10;
	$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

	$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$id), array('basedate'=>'asc'));
	$total = $app->db->selectCount('strategy_daily_analysis', array('strategy_id'=>$id));
	$total_page = ceil($total / $count);

	// 산식
	$reversed_values = array_reverse($daily_values);
	$reversed_values = array_slice($reversed_values, $start, $count);

	$app->render('strategy_view_tab2.php', array('current_menu'=>$current_menu, 'strategy'=>$strategy, 'daily_values'=>$reversed_values, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
});

$app->get('/strategies/:id/monthly', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	$page = $app->request->get('page');
	if(empty($page) || !is_numeric($page)) $page = 1;
	$count = 15;
	$start = ($page - 1) * $count;
	$page_count = 10;
	$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

	$daily_values = $app->db->select('strategy_monthly_analysis', '*', array('strategy_id'=>$id), array('baseyear'=>'asc','basemonth'=>'asc'));
	$total = $app->db->selectCount('strategy_monthly_analysis', array('strategy_id'=>$id));
	$total_page = ceil($total / $count);

	// 산식
	// $new_daily_values = calculateData($daily_values);

	$reversed_values = array_reverse($daily_values);
	$reversed_values = array_slice($reversed_values, $start, $count);

	$app->render('strategy_view_tab5.php', array('current_menu'=>$current_menu, 'strategy'=>$strategy, 'daily_values'=>$reversed_values, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
});

$app->get('/strategies/:id/dailyall/download', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	// 산식
	//$daily_values = $app->m->get('strategy_daily_value:'.$id);
	//if($daily_values === false){
	//	$daily_values = $app->db->select('strategy_daily', '*', array('strategy_id'=>$id), array('target_date'=>'asc'));
	//	$app->m->set('strategy_daily_value:'.$id, $daily_values);
	//}
	$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$id), array('basedate'=>'desc'));
	//	$app->m->set('strategy_daily_value:'.$id, $daily_values);

	if(count($daily_values) < 2){
		$app->redirect('/strategies/'.$id);
	}

	//$new_daily_values = $app->m->get('strategy_new_daily_value:'.$id);
	//if($new_daily_values === false){
	//	$new_daily_values = calculateData($daily_values);
	//	$app->m->set('strategy_new_daily_value:'.$id, $new_daily_values);
	//}

	// application/octet-stream
	$app->response->headers->set('Content-Type', 'application/vnd.ms-excel');
	$app->response->headers->set('Pragma', 'dummy=bogus');
	$app->response->headers->set('Cache-Control', 'private');

	// 한글 안깨지도록 보정
	$original_name = stripslashes($strategy['name'])."_daily_all";
	/*if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')){
		$original_name = urlencode($original_name);
	}elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')){
		$original_name = $original_name;
	}*/

	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')){
		$original_name = $original_name;
	} else {
		$original_name = urlencode($original_name);
	}

	$app->response->headers->set('Content-Disposition', 'attachment; filename="'.$original_name.'.xls"');
	// $app->response->headers->set('Content-Length', $f->size);
	echo '<html>';
	echo '<head>';
	echo '<title></title>';
	echo '<style>table {border-callapse:collapse;} td {border:1px solid black;}</style>';
	echo '</head>';
	echo '<body>';
	echo '<table>';
	echo '<thead>';
	echo '<tr>';
	echo '<th>일자</th>';
	echo '<th>입출금</th>';
	echo '<th>일손익</th>';
	echo '<th>KP Ratio</th>';
	echo '<th>SM Score</th>';
	echo '<th>기준가</th>';
	echo '<th>거래일수</th>';
	echo '<th>잔고</th>';
	echo '<th>원금</th>';
	echo '<th>당일원금출금액</th>';
	echo '<th>누적입출금</th>';
	echo '<th>입금</th>';
	echo '<th>누적입금</th>';
	echo '<th>출금</th>';
	echo '<th>누적출금</th>';
	echo '<th>실현손익</th>';
	echo '<th>누적실현손익</th>';
	echo '<th>일손익(%)</th>';
	echo '<th>일손일률(LN%)</th>';
	echo '<th>최대일이익</th>';
	echo '<th>최대일이익(%)</th>';
	echo '<th>최대일손일</th>';
	echo '<th>최대일손실(%)</th>';
	echo '<th>총이익</th>';
	echo '<th>이익일수</th>';
	echo '<th>평균이익</th>';
	echo '<th>총손실</th>';
	echo '<th>손실일수</th>';
	echo '<th>평균손실</th>';
	echo '<th>누적손익</th>';
	echo '<th>누적손익(실현손익제외)</th>';
	echo '<th>누적손익(%)</th>';
	echo '<th>누적손익률(LN%)</th>';
	echo '<th>최대누적손익</th>';
	echo '<th>최대누적손익(%)</th>';
	echo '<th>평균손익</th>';
	echo '<th>평균손익(%)</th>';
	echo '<th>Peak</th>';
	echo '<th>Peak(%)</th>';
	echo '<th>Peak(LN%)</th>';
	echo '<th>고점후 경과일</th>';
	echo '<th>현재자본인하금액</th>';
	echo '<th>현재자본인하(%)</th>';
	echo '<th>현재자본인하(LN%)</th>';
	echo '<th>최대자본인하금액</th>';
	echo '<th>최대자본인하금액(%)</th>';
	echo '<th>승률</th>';
	echo '<th>Profit Factor</th>';
	echo '<th>ROA</th>';
	echo '<th>평균손익비</th>';
	echo '<th>변동계수</th>';
	echo '<th>Sharp Ratio</th>';

	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($daily_values as $v){
		echo '<tr>';

		echo '<td>'.$v['basedate'].'</td>';
		echo '<td>'.$v['flow'].'</td>';
		echo '<td>'.$v['daily_pl'].'</td>';
		echo '<td>'.$v['kp_ratio'].'</td>';
		echo '<td>'.$v['sm_score'].'</td>';
		echo '<td>'.$v['sm_index'].'</td>';
		echo '<td>'.$v['trade_days'].'</td>';
		echo '<td>'.$v['balance'].'</td>';
		echo '<td>'.$v['principal'].'</td>';
		echo '<td>'.$v['withdraw_principal'].'</td>';
		echo '<td>'.$v['acc_flow'].'</td>';
		echo '<td>'.$v['inflow'].'</td>';
		echo '<td>'.$v['acc_inflow'].'</td>';
		echo '<td>'.$v['outflow'].'</td>';
		echo '<td>'.$v['acc_outflow'].'</td>';
		echo '<td>'.$v['realized_pl'].'</td>';
		echo '<td>'.$v['acc_realized_pl'].'</td>';
		echo '<td>'.$v['daily_pl_rate'].'</td>';
		echo '<td>'.$v['daily_pl_ln_rate'].'</td>';
		echo '<td>'.$v['max_daily_profit'].'</td>';
		echo '<td>'.$v['max_daily_profit_rate'].'</td>';
		echo '<td>'.$v['max_daily_loss'].'</td>';
		echo '<td>'.$v['max_daily_loss_rate'].'</td>';
		echo '<td>'.$v['total_profit'].'</td>';
		echo '<td>'.$v['profit_days'].'</td>';
		echo '<td>'.$v['avg_profit'].'</td>';
		echo '<td>'.$v['total_loss'].'</td>';
		echo '<td>'.$v['loss_days'].'</td>';
		echo '<td>'.$v['avg_loss'].'</td>';
		echo '<td>'.$v['acc_pl'].'</td>';
		echo '<td>'.$v['acc_pl_without_realized'].'</td>';
		echo '<td>'.$v['acc_pl_rate'].'</td>';
		echo '<td>'.$v['acc_pl_ln_rate'].'</td>';
		echo '<td>'.$v['max_acc_pl'].'</td>';
		echo '<td>'.$v['max_acc_pl_rate'].'</td>';
		echo '<td>'.$v['avg_pl'].'</td>';
		echo '<td>'.$v['avg_pl_rate'].'</td>';
		echo '<td>'.$v['peak'].'</td>';
		echo '<td>'.$v['peak_rate'].'</td>';
		echo '<td>'.$v['peak_ln_rate'].'</td>';
		echo '<td>'.$v['after_peak_days'].'</td>';
		echo '<td>'.$v['dd'].'</td>';
		echo '<td>'.$v['dd_rate'].'</td>';
		echo '<td>'.$v['dd_ln_rate'].'</td>';
		echo '<td>'.$v['mdd'].'</td>';
		echo '<td>'.$v['mdd_rate'].'</td>';
		echo '<td>'.$v['winning_rate'].'</td>';
		echo '<td>'.$v['profit_factor'].'</td>';
		echo '<td>'.$v['roa'].'</td>';
		echo '<td>'.$v['avg_pl_ratio'].'</td>';
		echo '<td>'.$v['variation_factor'].'</td>';
		echo '<td>'.$v['sharp_ratio'].'</td>';

		
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</body>';
	echo '</html>';
});

$app->get('/strategies/:id/daily/download', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	// 산식
	$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$id), array('basedate'=>'desc'));
	if(count($daily_values) < 2){
		$app->redirect('/strategies/'.$id);
	}

	// application/octet-stream
	$app->response->headers->set('Content-Type', 'application/vnd.ms-excel');
	$app->response->headers->set('Pragma', 'dummy=bogus');
	$app->response->headers->set('Cache-Control', 'private');

	// 한글 안깨지도록 보정
	$original_name = stripslashes($strategy['name'])."_daily";
	/*if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false){
		$original_name = urlencode($original_name);
	}elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false){
		$original_name = $original_name;
	}*/

	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')){
		$original_name = $original_name;
	} else {
		$original_name = urlencode($original_name);
	}

	$app->response->headers->set('Content-Disposition', 'attachment; filename="'.$original_name.'.xls"');
	// $app->response->headers->set('Content-Length', $f->size);
	echo '<html>';
	echo '<head>';
	echo '<title></title>';
	echo '<style>table {border-callapse:collapse;} td {border:1px solid black;}</style>';
	echo '</head>';
	echo '<body>';
	echo '<table>';
	echo '<thead>';
	echo '<tr>';
	echo '<th>일자</th>';
	echo '<th>원금</th>';
	echo '<th>입출금</th>';
	echo '<th>일손익</th>';
	echo '<th>일손익(%)</th>';
	echo '<th>누적손익</th>';
	echo '<th>누적손익(%)</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($daily_values as $v){
		echo '<tr>';

		echo '<td>'.$v['basedate'].'</td>';
		echo '<td>'.$v['principal'].'</td>';
		echo '<td>'.$v['flow'].'</td>';
		echo '<td>'.$v['daily_pl'].'</td>';
		echo '<td>'.$v['daily_pl_rate'].'</td>';
		echo '<td>'.$v['acc_pl'].'</td>';
		echo '<td>'.$v['acc_pl_rate'].'</td>';
		
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</body>';
	echo '</html>';
});

$app->get('/strategies/:id/monthly/download', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	// 산식
	$monthly_values = $app->db->select('strategy_monthly_analysis', '*', array('strategy_id'=>$id), array('baseyear'=>'desc','basemonth'=>'desc'));
	if(count($monthly_values) < 2){
		$app->redirect('/strategies/'.$id);
	}

	// application/octet-stream
	$app->response->headers->set('Content-Type', 'application/vnd.ms-excel');
	$app->response->headers->set('Pragma', 'dummy=bogus');
	$app->response->headers->set('Cache-Control', 'private');

	// 한글 안깨지도록 보정
	$original_name = stripslashes($strategy['name'])."_monthly";
	/*if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false){
		$original_name = urlencode($original_name);
	}elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false){
		$original_name = $original_name;
	}*/

	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')){
		$original_name = $original_name;
	} else {
		$original_name = urlencode($original_name);
	}

	$app->response->headers->set('Content-Disposition', 'attachment; filename="'.$original_name.'.xls"');
	// $app->response->headers->set('Content-Length', $f->size);
	echo '<html>';
	echo '<head>';
	echo '<title></title>';
	echo '<style>table {border-callapse:collapse;} td {border:1px solid black;}</style>';
	echo '</head>';
	echo '<body>';
	echo '<table>';
	echo '<thead>';
	echo '<tr>';
	echo '<th>월</th>';
	echo '<th>평균원금</th>';
	echo '<th>입출금</th>';
	echo '<th>월손익</th>';
	echo '<th>월손익(%)</th>';
	echo '<th>누적손익</th>';
	echo '<th>누적수익(%)</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($monthly_values as $v){
		echo '<tr>';

		echo '<td>'.$v['baseyear'].'.'.$v['basemonth'].'</td>';
		echo '<td>'.$v['avg_principal'].'</td>';
		echo '<td>'.$v['flow'].'</td>';
		echo '<td>'.$v['monthly_pl'].'</td>';
		echo '<td>'.$v['monthly_pl_rate'].'</td>';
		echo '<td>'.$v['acc_pl'].'</td>';
		echo '<td>'.$v['acc_pl_rate'].'</td>';
		
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</body>';
	echo '</html>';
});

$app->get('/strategies/:id/accounts', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	$page = $app->request->get('page');
	if(empty($page) || !is_numeric($page)) $page = 1;
	$count = 25;
	$start = ($page - 1) * $count;
	$page_count = 10;
	$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

	$monthly_values = $app->db->select('strategy_account', '*', array('strategy_id'=>$id));
	$total = $app->db->selectCount('strategy_account', array('strategy_id'=>$id));
	$total_page = ceil($total / $count);

	$app->render('strategy_view_tab3.php', array('current_menu'=>$current_menu, 'strategy'=>$strategy, 'monthly_values'=>$monthly_values, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
});

$app->get('/strategies/:id/reviews', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	$page = $app->request->get('page');
	if(empty($page) || !is_numeric($page)) $page = 1;
	$count = 25;
	$start = ($page - 1) * $count;
	$page_count = 10;
	$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

	$reviews = $app->db->select('strategy_review', '*', array('strategy_id'=>$id), array('review_id'=>'desc'), $start, $count);
	foreach($reviews as $k=>$review){
		$writer = $app->db->selectOne('user', '*', array('uid'=>$review['writer_uid']));
		$reviews[$k]['writer'] = array('uid'=>$writer['uid'], 'nickname'=>$writer['nickname']);
	}
	$total = $app->db->selectCount('strategy_review', array('strategy_id'=>$id));
	$total_page = ceil($total / $count);

	$app->render('strategy_view_tab4.php', array('current_menu'=>$current_menu, 'strategy'=>$strategy, 'reviews'=>$reviews, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
});

$app->post('/strategies/:id/reviews/add', $authenticateForRole('N,T,B'), function ($id) use ($app, $log, $isLoggedIn) {
	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	$star = $app->request->post('star');
	$review_body = $app->request->post('review_body');

	if(empty($star) || !is_numeric($star)){
		$app->redirect('/strategies/'.$id.'/reviews');
	}
	
	if(empty($review_body)){
		$app->redirect('/strategies/'.$id.'/reviews');
	}

	$app->db->insert('strategy_review', array('strategy_id'=>$id, 'rating'=>$star, 'contents'=>$review_body, 'writer_uid'=>$_SESSION['user']['uid'], 'writer_name'=>$_SESSION['user']['nickname']));

	$app->redirect('/strategies/'.$id.'/reviews');
});

$app->get('/strategies/:id/reviews/:review_id/delete', function ($id, $review_id) use ($app, $log, $isLoggedIn) {
	$review = $app->db->selectOne('strategy_review', '*', array('review_id'=>$review_id));

	if(empty($review)){
		$app->halt(404, 'not found');
	}

	if($_SESSION['user']['user_type'] == 'A' || $review['writer_uid'] == $_SESSION['user']['uid']){
		$app->db->delete('strategy_review', array('review_id'=>$review_id));
	}

	$app->redirect('/strategies/'.$id.'/reviews');
});

$app->get('/strategies/:id/daily', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	$app->render('strategy_view.php', array('current_menu'=>$current_menu, 'strategy'=>$strategy));
});

$app->get('/strategies/:id', function ($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'strategies';

	$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

	if(empty($strategy)){
		$app->halt(404, 'not found');
	}

	$current_url = $app->config('scheme').'://'.$app->config('host').'/strategies/'.$id;

	// 종목
	$strategy_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$strategy['strategy_id']));

	$item_id_array = array();
	$strategy_items_value = array();
	foreach($strategy_items as $kk=>$vv){
		$item_id_array[] = $vv['item_id'];
	}
	if(count($item_id_array)){
		$result = $app->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
		while($row = $result->fetch_array()){
			$strategy_items_value[] = $row;
		}			
	}

	$strategy['items'] = $strategy_items_value;

	// 브로커
	$strategy['broker'] = $app->db->selectOne('broker', '*', array('broker_id'=>$strategy['broker_id']));

	// 매매툴
	$strategy['system_tool'] = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$strategy['tool_id']));

	// 트레이더
	$developer = $app->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']));
	$strategy['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

	// 팔로워
	$followers_count = $app->db->selectCount('following_strategy', array('strategy_id'=>$strategy['strategy_id']));
	$strategy['followers_count'] = $followers_count;

	// 팔로잉 여부
	$is_following = false;
	if($isLoggedIn()){
		$is_following = $app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'], 'strategy_id'=>$strategy['strategy_id'])) > 0 ? true : false;
	}		

	$strategy['is_following'] = $is_following;

	// 투자자 수
	$strategy['investor_count'] = 0;
	$result = $app->db->conn->query('SELECT SUM(investor) FROM strategy_funding WHERE strategy_id = '.$strategy['strategy_id']);
	while($row = $result->fetch_array()){
		$strategy['investor_count'] = $row[0];
	}

	// 펀딩금액
	$strategy['total_funding'] = 0;
	$result = $app->db->conn->query('SELECT SUM(money) FROM strategy_funding WHERE strategy_id = '.$strategy['strategy_id']);
	while($row = $result->fetch_array()){
		$strategy['total_funding'] = empty($row[0]) ? 0 : $row[0];
	}

	// 산식
	$strategy['daily_values'] = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy['strategy_id']), array('basedate'=>'asc'));;
	
	foreach($strategy['daily_values'] as $k => $v){
		$strategy['daily_values'][$k]['m_timestamp'] = strtotime($strategy['daily_values'][$k]['basedate']) * 1000;
	}

	// 월간수익률
	$monthly_values = $app->db->select('strategy_monthly_analysis', '*', array('strategy_id'=>$strategy['strategy_id']), array('baseyear'=>'asc','basemonth'=>'asc'));
	$strategy['monthly_profit_rate'] = calMonthlyPLRate($monthly_values);

	// 년간수익률
	$yearly_values = $app->db->select('strategy_yearly_analysis', '*', array('strategy_id'=>$strategy['strategy_id']), array('baseyear'=>'asc'));
	$strategy['yearly_profit_rate'] = calYearlyPLRate($yearly_values);

	$app->render('strategy_view.php', array('current_menu'=>$current_menu, 'strategy'=>$strategy, 'current_url'=>$current_url));
});

$app->get('/strategies/:id/follow', $authenticateForRole('N,T,B'), function ($id) use ($app, $log) {
	if($app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id)) == 0){
		$app->db->insert('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id));
		$app->db->conn->query('UPDATE strategy SET followers_count = followers_count + 1 WHERE strategy_id = '.$app->db->conn->real_escape_string($id));
	}

	$type = $app->request->get('type');
	if(!empty($type) && $type == 'json'){
		echo json_encode(array('result'=>true));
		$app->stop();
	}

	$app->redirect('/strategies');
});

$app->get('/strategies/:id/unfollow', $authenticateForRole('N,T,B'), function ($id) use ($app, $log) {
	if($app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id)) > 0){
		$app->db->delete('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id));
		$app->db->conn->query('UPDATE strategy SET followers_count = followers_count - 1 WHERE strategy_id = '.$app->db->conn->real_escape_string($id));
	}

	$type = $app->request->get('type');
	if(!empty($type) && $type == 'json'){
		echo json_encode(array('result'=>true));
		$app->stop();
	}

	$app->redirect('/strategies');
});

$app->get('/followings/:basedate', $authenticateForRole('N,T,B'), function($basedate) use ($app, $log, $isLoggedIn) {
	$current_menu = 'followings';

	$start = $app->request->get('start');
	if(empty($start) || !is_numeric($start)) $start = 0;
	$count = 20;

	$following_ids = $app->db->select('following_strategy', '*', array('uid'=>$_SESSION['user']['uid']), array('following_id'=>'desc'));
	$following_ids_array = array();
	foreach($following_ids as $v){
		$following_ids_array[] = $v['strategy_id'];
	}

	$sql = 'SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $following_ids_array).') AND is_open = \'1\' AND is_operate = \'1\'';

	$sort = $app->request->get('sort');
	$sort_by = $app->request->get('sort_by');

	if(!empty($sort_by) && $sort_by == 'asc'){
		$sort_by = 'asc';
	}else{
		$sort_by = 'desc';
	}

	if(!empty($sort)){
		if($sort == 'total_profit_rate'){
			$sql .= ' ORDER BY total_profit_rate '.$sort_by;
		}else if($sort == 'mdd'){
			$sql .= ' ORDER BY mdd '.$sort_by;
		}else if($sort == 'sharp_ratio'){
			$sql .= ' ORDER BY sharp_ratio '.$sort_by;
		}else if($sort == 'followers_count'){
			$sql .= ' ORDER BY followers_count '.$sort_by;
		}else{
			$sort = '';
			$sql .= ' ORDER BY strategy_id '.$sort_by;
		}
	}else{
		$sort = '';
		$sql .= 'ORDER BY sharp_ratio '.$sort_by;
	}

	$sql .= ' LIMIT '.$start.', '.$count;

	$strategies = array();
	if(count($following_ids_array) != 0){
		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_array()){
			$strategies[] = $row;
		}
	}

	foreach($strategies as $k => $v){
		// 종목
		$strategy_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$v['strategy_id']));

		$item_id_array = array();
		$strategy_items_value = array();
		foreach($strategy_items as $kk=>$vv){
			$item_id_array[] = $vv['item_id'];
		}
		if(count($item_id_array)){
			$result = $app->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
			while($row = $result->fetch_array()){
				$strategy_items_value[] = $row;
			}			
		}

		$strategies[$k]['items'] = $strategy_items_value;

		// 브로커
		$strategies[$k]['broker'] = $app->db->selectOne('broker', '*', array('broker_id'=>$v['broker_id']));

		// 매매툴
		$strategies[$k]['system_tool'] = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$v['tool_id']));

		// 트레이더
		$developer = $app->db->selectOne('user', '*', array('uid'=>$v['developer_uid']));
		$strategies[$k]['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

		// 팔로워
		/*
		$followers_count = $app->db->selectCount('following_strategy', array('strategy_id'=>$v['strategy_id']));
		$strategies[$k]['followers_count'] = $followers_count;
		*/

		// 팔로잉 여부
		$is_following = false;
		if($isLoggedIn()){
			$is_following = $app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'], 'strategy_id'=>$v['strategy_id'])) > 0 ? true : false;
		}		

		$strategies[$k]['is_following'] = $is_following;

		// 산식
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));

		$strategies[$k]['daily_values'] = $daily_values;
		$sm_index_array = array();
                foreach($daily_values as $k1=>$v1){
                        $m_timestamp = strtotime($v1['basedate'])*1000;
                        $sm_index_array[] = '['.$m_timestamp.','.$v1['sm_index'].']';
                }

		// 표를 그리기 위한 데이터
		$strategies[$k]['str_c_price'] = '['.implode(',', $sm_index_array ).']';

		// 펀딩금액
		$total_funding = $app->m->get('strategy_total_funding:'.$v['strategy_id']);
		if($total_funding === false){
			$result = $app->db->conn->query('SELECT SUM(money) FROM strategy_funding WHERE strategy_id = '.$v['strategy_id']);
			$row = $result->fetch_array();
			$total_funding = empty($row[0]) ? 0 : $row[0];
			$app->m->set('strategy_total_funding:'.$v['strategy_id'], $total_funding);
		}
		$strategies[$k]['total_funding'] = $total_funding;

		/*
		// 종목
		$strategy_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$v['strategy_id']));

		$item_id_array = array();
		$strategy_items_value = array();
		foreach($strategy_items as $kk=>$vv){
			$item_id_array[] = $vv['item_id'];
		}
		if(count($item_id_array)){
			$result = $app->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
			while($row = $result->fetch_array()){
				$strategy_items_value[] = $row;
			}			
		}

		$strategies[$k]['items'] = $strategy_items_value;

		// 브로커
		$strategies[$k]['broker'] = $app->db->selectOne('broker', '*', array('broker_id'=>$v['broker_id']));

		// 매매툴
		$strategies[$k]['system_tool'] = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$v['tool_id']));

		// 트레이더
		$developer = $app->db->selectOne('user', '*', array('uid'=>$v['developer_uid']));
		$strategies[$k]['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

		// 팔로워
		/*
		$followers_count = $app->db->selectCount('following_strategy', array('strategy_id'=>$v['strategy_id']));
		$strategies[$k]['followers_count'] = $followers_count;
		*/
/*
		// 팔로잉 여부
		$is_following = false;
		if($isLoggedIn()){
			$is_following = $app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'], 'strategy_id'=>$v['strategy_id'])) > 0 ? true : false;
		}		

		$strategies[$k]['is_following'] = $is_following;

		// 산식
		$daily_values = $app->m->get('strategy_daily_value:'.$v['strategy_id']);
		if($daily_values === false){
			$daily_values = $app->db->select('strategy_daily', '*', array('strategy_id'=>$v['strategy_id']), array('target_date'=>'asc'));
			$app->m->set('strategy_daily_value:'.$v['strategy_id'], $daily_values);
		}

		$new_daily_values = $app->m->get('strategy_new_daily_value:'.$v['strategy_id']);
		if($new_daily_values === false){
			$new_daily_values = calculateData($daily_values);
			$app->m->set('strategy_new_daily_value:'.$v['strategy_id'], $new_daily_values);
		}

		$c_price_array = array();
		foreach($new_daily_values as $v){
			$c_price_array[] = $v['c_price'];	
		}

		$strategies[$k]['daily_values'] = $new_daily_values;
		$strategies[$k]['weekly_profit_values'] = calWeeklyProfitValues($new_daily_values,date("n/j", strtotime($basedate)));

		// 표를 그리기 위한 데이터
		$strategies[$k]['str_c_price'] = '['.implode(',', $c_price_array).']';
		*/
	}

	$format = $app->request->get('format');
	if(!empty($format) && $format == 'json'){
		echo json_encode(array('items'=>$strategies));
		$app->stop();
	}

	$app->render('follow_list3.php', array('current_menu'=>$current_menu, 'basedate'=>$basedate, 'strategies'=>$strategies, 'start'=>$start, 'count'=>$count, 'sort'=>$sort, 'sort_by'=>$sort_by));
});

$app->get('/followings2/:basedate', $authenticateForRole('N,T,B'), function($basedate) use ($app, $log, $isLoggedIn) {
	$current_menu = 'followings2';

	$start = $app->request->get('start');
	if(empty($start) || !is_numeric($start)) $start = 0;
	$count = 20;

	$following_ids = $app->db->select('following_strategy', '*', array('uid'=>$_SESSION['user']['uid']), array('following_id'=>'desc'));
	$following_ids_array = array();
	foreach($following_ids as $v){
		$following_ids_array[] = $v['strategy_id'];
	}

	$sql = 'SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $following_ids_array).') AND is_open = \'1\' AND is_operate = \'1\'';

	$sort = $app->request->get('sort');
	$sort_by = $app->request->get('sort_by');

	if(!empty($sort_by) && $sort_by == 'asc'){
		$sort_by = 'asc';
	}else{
		$sort_by = 'desc';
	}

	if(!empty($sort)){
		if($sort == 'total_profit_rate'){
			$sql .= ' ORDER BY total_profit_rate '.$sort_by;
		}else if($sort == 'mdd'){
			$sql .= ' ORDER BY mdd '.$sort_by;
		}else if($sort == 'sharp_ratio'){
			$sql .= ' ORDER BY sharp_ratio '.$sort_by;
		}else if($sort == 'followers_count'){
			$sql .= ' ORDER BY followers_count '.$sort_by;
		}else{
			$sort = '';
			$sql .= ' ORDER BY strategy_id '.$sort_by;
		}
	}else{
		$sort = '';
		$sql .= 'ORDER BY sharp_ratio '.$sort_by;
	}

	$sql .= ' LIMIT '.$start.', '.$count;

	$strategies = array();
	if(count($following_ids_array) != 0){
		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_array()){
			$strategies[] = $row;
		}
	}

	foreach($strategies as $k => $v){
		// 종목
		$strategy_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$v['strategy_id']));

		$item_id_array = array();
		$strategy_items_value = array();
		foreach($strategy_items as $kk=>$vv){
			$item_id_array[] = $vv['item_id'];
		}
		if(count($item_id_array)){
			$result = $app->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
			while($row = $result->fetch_array()){
				$strategy_items_value[] = $row;
			}			
		}

		$strategies[$k]['items'] = $strategy_items_value;

		// 브로커
		$strategies[$k]['broker'] = $app->db->selectOne('broker', '*', array('broker_id'=>$v['broker_id']));

		// 매매툴
		$strategies[$k]['system_tool'] = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$v['tool_id']));

		// 트레이더
		$developer = $app->db->selectOne('user', '*', array('uid'=>$v['developer_uid']));
		$strategies[$k]['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

		// 팔로잉 여부
		$is_following = false;
		if($isLoggedIn()){
			$is_following = $app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'], 'strategy_id'=>$v['strategy_id'])) > 0 ? true : false;
		}		

		$strategies[$k]['is_following'] = $is_following;

		// 산식
		/*
		$daily_values = $app->m->get('strategy_daily_value:'.$v['strategy_id']);
		if($daily_values === false){
			$daily_values = $app->db->select('strategy_daily', '*', array('strategy_id'=>$v['strategy_id']), array('target_date'=>'asc'));
			$app->m->set('strategy_daily_value:'.$v['strategy_id'], $daily_values);
		}

		$new_daily_values = $app->m->get('strategy_new_daily_value:'.$v['strategy_id']);
		if($new_daily_values === false){
			$new_daily_values = calculateData($daily_values);
			$app->m->set('strategy_new_daily_value:'.$v['strategy_id'], $new_daily_values);
		}

		$c_price_array = array();
		foreach($new_daily_values as $v){
			$c_price_array[] = $v['c_price'];	
		}*/
		
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));

		$strategies[$k]['daily_values'] = $daily_values;
		$strategies[$k]['weekly_profit_values'] = calWeeklyProfitValues($daily_values,date("n/j", strtotime($basedate)));

		// 표를 그리기 위한 데이터
		//$strategies[$k]['str_c_price'] = '['.implode(',', $c_price_array).']';

	}

	$format = $app->request->get('format');
	if(!empty($format) && $format == 'json'){
		echo json_encode(array('items'=>$strategies));
		$app->stop();
	}

	$app->render('follow_list2.php', array('current_menu'=>$current_menu, 'basedate'=>$basedate, 'strategies'=>$strategies, 'start'=>$start, 'count'=>$count, 'sort'=>$sort, 'sort_by'=>$sort_by));
});

$app->get('/followings2/:id/unfollow', $authenticateForRole('N,T,B'), function ($id) use ($app, $log) {
	if($app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id)) > 0){
		$app->db->delete('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id));
		$app->db->conn->query('UPDATE strategy SET followers_count = followers_count - 1 WHERE strategy_id = '.$app->db->conn->real_escape_string($id));
	}

	$type = $app->request->get('type');
	if(!empty($type) && $type == 'json'){
		echo json_encode(array('result'=>true));
		$app->stop();
	}

	$app->redirect('/followings2/'.date("Ymd"));
});

$app->get('/portfolios', $authenticateForRole('N,T,B'), function() use ($app, $log) {
	$current_menu = 'portfolios';

	$start = $app->request->get('start');
	if(empty($start) || !is_numeric($start)) $start = 0;
	$count = 20;

	$portfolios = $app->db->select('portfolio', '*', array('uid'=>$_SESSION['user']['uid']), array('portfolio_id'=>'desc'), $start, $count);

	$format = $app->request->get('format');
	if(!empty($format) && $format == 'json'){
		foreach($portfolios as $k => $v){
			$portfolios[$k]['str_create_at'] = date('Y/m/d', strtotime($portfolio['reg_at']));
			$portfolios[$k]['str_start_date'] = substr($portfolio['start_date'], 0, 4).'.'.substr($portfolio['start_date'], 4, 2).'.'.substr($portfolio['start_date'], 6, 2);
			$portfolios[$k]['str_end_date'] = substr($portfolio['end_date'], 0, 4).'.'.substr($portfolio['end_date'], 4, 2).'.'.substr($portfolio['end_date'], 6, 2);
		}
		echo json_encode(array('items'=>$portfolios));
		$app->stop();
	}

	$app->render('portfolio.php', array('current_menu'=>$current_menu, 'portfolios'=>$portfolios, 'start'=>$start, 'count'=>$count));
});

$app->get('/portfolios/create', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'portfolios';

	$name = $app->request->get('name');
	$start_date = $app->request->get('start_date');
	$end_date = $app->request->get('end_date');
	$amount = $app->request->get('amount');
	$input_strategy_ids = $app->request->get('strategy_ids');
	$input_percents = $app->request->get('percents');

	if(empty($name)) $name = '';
	if(empty($start_date)) $start_date = date('Y.m.d', strtotime('-7 days'));
	if(empty($end_date)) $end_date = date('Y.m.d');
	if(empty($amount)) $amount = number_format(1000000);

	// 시작일과 종료일의 유효성체크
	$start_date_timestamp = strtotime($start_date);
	$end_date_timestamp = strtotime($end_date);

	if($start_date_timestamp > $end_date_timestamp){
		$start_date = date('Y.m.d', strtotime('-7 days'));
		$end_date = date('Y.m.d');
	}

	$temp_strategy_ids = explode(',', $input_strategy_ids);
	$temp_percents = explode(',', $input_percents);

	$strategy_ids = array();
	foreach($temp_strategy_ids as $v){
		if(empty($v)) continue;
		if(in_array($v, $strategy_ids)) continue;
		$strategy_ids[] = $app->db->conn->real_escape_string($v);
	}

	$percents = array();
	foreach($temp_percents as $v){
		if(empty($v)) continue;
		$percents[] = $v;
	}

	if(empty($amount) || !is_numeric(preg_replace('/[^\d]/', '', $amount))){
                $app->flash('error', '금액을 입력해주세요');
                $app->redirect('/portfolios/create');
        }else{
                $amount = preg_replace('/[^\d]/', '', $amount);
        }

	
	$portfolio_strategies_map = array();
	foreach($strategy_ids as $k=>$v){
		$portfolio_strategies_map[$v] = empty($temp_percents[$k]) ? 0 : $temp_percents[$k];
	}

	$min_value = 1000;
	$total_percent = 0;
	$strategies_percents = array();
	$first_date_array = array();
	$last_date_array = array();
	$strategy_daily_values = array();
	$strategies = array();
	$portfolio_total_profit = 0;
	$portfolio_total_profit_rate = 0;
	$first_available_date = date('Ymd', $start_date_timestamp);
	$last_available_date = date('Ymd', $end_date_timestamp);
	$unified_c_price_array = array();
	$str_unified_sm_index = '';
	if(count($strategy_ids)){
		$result = $app->db->conn->query('SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $strategy_ids).')');
		while($row = $result->fetch_array()){
			$strategies[] = $row;
		}

		$exist_daily_data_map = array();
		foreach($strategies as $k => $v){
			$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));
			$daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));

			if(count($daily_values) < 2){
				$app->flash('error', '전략에 입력된 데이터가 부족합니다');
				$app->redirect('/portfolios/create');
			}

			$strategy_daily_values[$v['strategy_id'].''] = $daily_values;

			// 최초일과 마지막일 구함
			$first_date_array[] = $daily_values[0]['basedate'];
			$last_date_array[] = $daily_values[count($daily_values)-1]['basedate'];

			$total_percent += $percents[$k];
			$strategies_percents[$v['strategy_id']] = trim(str_replace('%', '', $percents[$k]));

			// 종목
			$strategy_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$v['strategy_id']));

			$item_id_array = array();
			$strategy_items_value = array();
			foreach($strategy_items as $kk=>$vv){
				$item_id_array[] = $vv['item_id'];
			}
			if(count($item_id_array)){
				$result = $app->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
				while($row = $result->fetch_array()){
					$strategy_items_value[] = $row;
				}			
			}

			$strategies[$k]['items'] = $strategy_items_value;

			// 브로커
			$strategies[$k]['broker'] = $app->db->selectOne('broker', '*', array('broker_id'=>$v['broker_id']));

			// 매매툴
			$strategies[$k]['system_tool'] = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$v['tool_id']));

			// 트레이더
			$developer = $app->db->selectOne('user', '*', array('uid'=>$v['developer_uid']));
			$strategies[$k]['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

			foreach($daily_values_graph as $k1=>$v1){
                        	if( $min_value > $v1['sm_index'] )
                                	$min_value = $v1['sm_index'];
                        }

			$strategies[$k]['daily_values'] = $daily_values;
			$strategies[$k]['daily_values_graph'] = getSMIndexArray($daily_values_graph, $start_date, $end_date);

			// 표를 그리기 위한 데이터
			$strategies[$k]['str_c_price'] = getChartDataString($strategies[$k]['daily_values_graph'] , 'sm_index');
			// 비율
			$strategies[$k]['percents'] = $portfolio_strategies_map[$v['strategy_id']];
		}

		// 전략별 데이터의 날짜 합집합
		$first_available_date = intval($first_date_array[0]);
		foreach($first_date_array as $v){
			$tmp_date = str_replace('-','',$v); 
			if(intval($tmp_date) < $first_available_date){
				$first_available_date = intval($tmp_date);
			}
		}

		$last_available_date = intval($last_date_array[0]);
		foreach($last_date_array as $v){
			$tmp_date = str_replace('-','',$v); 
			if(intval($tmp_date) > $last_available_date){
				$last_available_date = intval($tmp_date);
			}
		}

		if(intval(date('Ymd', $start_date_timestamp)) < $first_available_date){
			$app->flash('error', '시작일로 설정할수 없습니다');
			$app->redirect('/portfolios/create');
		}

		if(intval(date('Ymd', $end_date_timestamp)) > $last_available_date){
			$app->flash('error', '종료일로 설정할수 없습니다');
			$app->redirect('/portfolios/create');
		}

		if($total_percent != 100){
			$app->flash('error', '비율 합이 100이어야 합니다');
			$app->redirect('/portfolios/create');
		}
	
		
		$unified_sm_index = calPortfolioSMIndexGraph($strategies);
		if (count($unified_sm_index ) > 0)
                	$str_unified_sm_index = getChartDataString($unified_sm_index, 'sm_index');

		// 누적수익률
		$portfolio_total_profit_rate = calPortfolioPLrate($unified_sm_index);;

		// 누적수익금액
		$portfolio_total_profit = $amount * $portfolio_total_profit_rate/100;
	}

	$app->render('portfolio_write.php', array('current_menu'=>$current_menu, 'strategies'=>$strategies, 'name'=>$name,'start_date'=>$start_date, 'end_date'=>$end_date,'min_value'=>$min_value,'str_unified_sm_index'=>$str_unified_sm_index,'amount'=>$amount, 'strategies'=>$strategies, 'portfolio_total_profit'=>$portfolio_total_profit, 'portfolio_total_profit_rate'=>$portfolio_total_profit_rate, 'first_available_date'=>$first_available_date, 'last_available_date'=>$last_available_date));
});

$app->post('/portfolios/create', $authenticateForRole('N,T,B'), function() use ($app, $log, $isLoggedIn) {
	$name = $app->request->post('name');
	$start_date = $app->request->post('start_date');
	$end_date = $app->request->post('end_date');
	$amount = $app->request->post('amount');
	$input_strategy_ids = $app->request->post('strategy_ids');
	$input_percents = $app->request->post('percents');

	if(empty($name)){
		$app->flash('error', '이름을 입력해주세요');
		$app->redirect('/portfolios/create');
	}

	if(empty($start_date) || strlen(preg_replace('/[^\d]/', '', $start_date)) != 8){
		$app->flash('error', '시작날짜를 입력해주세요');
		$app->redirect('/portfolios/create');
	}else{
		$start_date = preg_replace('/[^\d]/', '', $start_date);
	}

	if(empty($end_date) || strlen(preg_replace('/[^\d]/', '', $end_date)) != 8){
		$app->flash('error', '종료날짜를 입력해주세요');
		$app->redirect('/portfolios/create');
	}else{
		$end_date = preg_replace('/[^\d]/', '', $end_date);
	}

	// 시작일과 종료일의 유효성체크
	$start_date_timestamp = strtotime($start_date);
	$end_date_timestamp = strtotime($end_date);

	if($start_date_timestamp > $end_date_timestamp){
		$app->flash('error', '날짜가 올바르지 않습니다');
		$app->redirect('/portfolios/create');
	}

	$strategy_ids = array();
	foreach($input_strategy_ids as $v){
		if(empty($v)) continue;
		if(in_array($v, $strategy_ids)) continue;
		$strategy_ids[] = $v;
	}

	$percents = array();
	foreach($input_percents as $v){
		if(empty($v)) continue;
		$percents[] = $v;
	}

	if(empty($amount) || !is_numeric(preg_replace('/[^\d]/', '', $amount))){
		$app->flash('error', '금액을 입력해주세요');
		$app->redirect('/portfolios/create');
	}else{
		$amount = preg_replace('/[^\d]/', '', $amount);
	}

	if(count($strategy_ids) > 10 || count($percents) > 10){
		$app->flash('error', '10개까지 가능합니다');
		$app->redirect('/portfolios/create');
	}

	if(count($strategy_ids) == 0){
		$app->flash('error', '전략을 선택해주세요');
		$app->redirect('/portfolios/create');
	}

	$total_percent = 0;
	$strategies = array();
	$portfolio = array();
	$strategies_percents = array();
	$first_date_array = array();
	$last_date_array = array();
	$strategy_daily_values = array();
	$unified_c_price_array = array();
	$exist_daily_data_map = array();
	foreach($strategy_ids as $k => $strategy_id){
		if(empty($strategy_id)) continue;
		$exist_strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$strategy_id));
		if(empty($exist_strategy)) continue;

		if(empty($percents[$k])) continue;
		$strategies[$k] = $exist_strategy;
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy_id), array('basedate'=>'asc'));
		$daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$strategy_id), array('basedate'=>'asc'));

		if(count($daily_values) < 2){
			$app->flash('error', '전략에 입력된 데이터가 부족합니다');
			$app->redirect('/portfolios/create');
		}

		$strategy_daily_values[$strategy_id.''] = $daily_values;
		$strategies[$k]['daily_values_graph'] = getSMIndexArray($daily_values_graph, $start_date, $end_date);


		// 최초일과 마지막일 구함
		$first_date_array[] = $daily_values[0]['basedate'];
		$last_date_array[] = $daily_values[count($daily_values)-1]['basedate'];

		$total_percent += $percents[$k];
		$strategies_percents[$strategy_id] = trim(str_replace('%', '', $percents[$k]));
		$strategies[$k]['percents'] = trim(str_replace('%', '', $percents[$k]));
		$portfolio[$k]['percents'] = trim(str_replace('%', '', $percents[$k]));
	}

	if($total_percent != 100){
		$app->flash('error', '비율 합이 100이어야 합니다');
		$app->redirect('/portfolios/create');
	}

	$unified_sm_index = calPortfolioSMIndexGraph($strategies);

	// 누적수익률
	$portfolio_total_profit_rate = calPortfolioPLrate($unified_sm_index);

	// 누적수익금액
	$portfolio_total_profit = $amount * $portfolio_total_profit_rate/100;

	$new_portfolio_id = $app->db->insert('portfolio', array(
		'name'=>$name,
		'uid'=>$_SESSION['user']['uid'],
		'amount'=>$amount,
		'total_profit_rate'=>$portfolio_total_profit_rate,
		'result_amount'=>$portfolio_total_profit,
		'start_date'=>$start_date,
		'end_date'=>$end_date
	));

	foreach($strategies_percents as $k => $v){
		$app->db->insert('portfolio_strategy', array(
			'portfolio_id'=>$new_portfolio_id,
			'strategy_id'=>$k,
			'percents'=>$v
		));
	}

	$app->redirect('/portfolios');
});

$app->get('/portfolios/:id/delete', $authenticateForRole('N,T,B'), function($id) use ($app, $log, $isLoggedIn) {
	$portfolio = $app->db->selectOne('portfolio', '*', array('portfolio_id'=>$id));

	if(empty($portfolio)){
		$app->halt(404, 'not found');
	}

	if($portfolio['uid'] != $_SESSION['user']['uid']){
		$app->halt(403, 'fobidden');
	}

	// 전략은 모두 지우고 재등록
	$app->db->delete('portfolio_strategy', array('portfolio_id'=>$id));

	$app->db->delete('portfolio', array('portfolio_id'=>$id));

	$app->redirect('/portfolios');
});

$app->post('/portfolios/:id/edit', $authenticateForRole('N,T,B'), function($id) use ($app, $log, $isLoggedIn) {
	$portfolio = $app->db->selectOne('portfolio', '*', array('portfolio_id'=>$id));

	if(empty($portfolio)){
		$app->halt(404, 'not found');
	}

	if($portfolio['uid'] != $_SESSION['user']['uid']){
		$app->halt(403, 'fobidden');
	}

	$name = $app->request->post('name');
	$start_date = $app->request->post('start_date');
	$end_date = $app->request->post('end_date');
	$amount = $app->request->post('amount');
	$input_strategy_ids = $app->request->post('strategy_ids');
	$input_percents = $app->request->post('percents');

	if(empty($name)){
		$app->flash('error', '이름을 입력해주세요');
		$app->redirect('/portfolios/'.$id);
	}

	if(empty($start_date) || strlen(preg_replace('/[^\d]/', '', $start_date)) != 8){
		$app->flash('error', '시작날짜를 입력해주세요');
		$app->redirect('/portfolios/'.$id);
	}else{
		$start_date = preg_replace('/[^\d]/', '', $start_date);
	}

	if(empty($end_date) || strlen(preg_replace('/[^\d]/', '', $end_date)) != 8){
		$app->flash('error', '종료날짜를 입력해주세요');
		$app->redirect('/portfolios/'.$id);
	}else{
		$end_date = preg_replace('/[^\d]/', '', $end_date);
	}

	// 시작일과 종료일의 유효성체크
	$start_date_timestamp = strtotime($start_date);
	$end_date_timestamp = strtotime($end_date);

	if($start_date_timestamp > $end_date_timestamp){
		$app->flash('error', '날짜가 올바르지 않습니다');
		$app->redirect('/portfolios/'.$id);
	}

	$strategy_ids = array();
	foreach($input_strategy_ids as $v){
		if(empty($v)) continue;
		if(in_array($v, $strategy_ids)) continue;
		$strategy_ids[] = $v;
	}

	$percents = array();
	foreach($input_percents as $v){
		if(empty($v)) continue;
		$percents[] = $v;
	}

	if(empty($amount) || !is_numeric(preg_replace('/[^\d]/', '', $amount))){
		$app->flash('error', '금액을 입력해주세요');
		$app->redirect('/portfolios/'.$id);
	}else{
		$amount = preg_replace('/[^\d]/', '', $amount);
	}

	if(count($strategy_ids) > 10 || count($percents) > 10){
		$app->flash('error', '10개까지 가능합니다');
		$app->redirect('/portfolios/'.$id);
	}

	if(count($strategy_ids) == 0){
		$app->flash('error', '전략을 선택해주세요');
		$app->redirect('/portfolios/'.$id);
	}

	$total_percent = 0;
	$strategies_percents = array();
	$first_date_array = array();
	$last_date_array = array();
	$strategy_daily_values = array();
	$unified_c_price_array = array();
	$exist_daily_data_map = array();
	foreach($strategy_ids as $k => $strategy_id){
		if(empty($strategy_id)) continue;
		$exist_strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$strategy_id));
		if(empty($exist_strategy)) continue;

		if(empty($percents[$k])) continue;
		$strategies[$k] = $exist_strategy;

		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy_id), array('basedate'=>'asc'));
		$daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$strategy_id), array('basedate'=>'asc'));
		if(count($daily_values) < 2){
			$app->flash('error', '전략에 입력된 데이터가 없습니다');
			$app->redirect('/portfolios/'.$id);
		}
		$strategies[$k]['daily_values'] = $daily_values;
		$strategies[$k]['daily_values_graph'] = getSMIndexArray($daily_values_graph, $start_date, $end_date);
		$strategy_daily_values[$strategy_id.''] = $daily_values;


		// 최초일과 마지막일 구함
		$first_date_array[] = $daily_values[0]['basedate'];
		$last_date_array[] = $daily_values[count($daily_values)-1]['basedate'];

		$total_percent += $percents[$k];
		$strategies_percents[$strategy_id] = trim(str_replace('%', '', $percents[$k]));
		$strategies[$k]['percents'] = trim(str_replace('%', '', $percents[$k]));
	}

	if($total_percent != 100){
		$app->flash('error', '비율 합이 100이어야 합니다');
		$app->redirect('/portfolios/'.$id);
	}

        $unified_sm_index = calPortfolioSMIndexGraph($strategies);

        // 누적수익률
        $portfolio_total_profit_rate = calPortfolioPLrate($unified_sm_index);

	// 누적수익금액
	$portfolio_total_profit = $amount * $portfolio_total_profit_rate/100;

	$app->db->update('portfolio', array(
		'name'=>$name,
		'amount'=>$amount,
		'total_profit_rate'=>$portfolio_total_profit_rate,
		'result_amount'=>$portfolio_total_profit,
		'start_date'=>$start_date,
		'end_date'=>$end_date
	), array('portfolio_id'=>$id));

	// 전략은 모두 지우고 재등록
	$app->db->delete('portfolio_strategy', array('portfolio_id'=>$id));

	foreach($strategies_percents as $k => $v){
		$app->db->insert('portfolio_strategy', array(
			'portfolio_id'=>$id,
			'strategy_id'=>$k,
			'percents'=>$v
		));
	}

	$app->redirect('/portfolios/'.$id);
});

$app->get('/portfolios/:id', $authenticateForRole('N,T,B'), function($id) use ($app, $log, $isLoggedIn) {
	$current_menu = 'portfolios';

	$portfolio = $app->db->selectOne('portfolio', '*', array('portfolio_id'=>$id));

	if(empty($portfolio)){
		$app->halt(404, 'not found');
	}

	$name = $app->request->get('name');
	$start_date = $app->request->get('start_date');
	$end_date = $app->request->get('end_date');
	$amount = $app->request->get('amount');
	$input_strategy_ids = $app->request->get('strategy_ids');
	$input_percents = $app->request->get('percents');

	if(!empty($name)) $portfolio['name'] = $name;
	if(!empty($start_date)) $portfolio['start_date'] = preg_replace('/[^\d]/', '', $start_date);
	else{
		$start_date = substr($portfolio['start_date'], 0, 4).'.'.substr($portfolio['start_date'], 4, 2).'.'.substr($portfolio['start_date'], 6, 2);
	}
	if(!empty($end_date)) $portfolio['end_date'] = preg_replace('/[^\d]/', '', $end_date);
	else{
		$end_date = substr($portfolio['end_date'], 0, 4).'.'.substr($portfolio['end_date'], 4, 2).'.'.substr($portfolio['end_date'], 6, 2);
	}
	if(!empty($amount)){
		$portfolio['amount'] = $amount;
		$amount = intval(str_replace(',','',$amount));
	}else{
		$amount = $portfolio['amount'];
		$portfolio['amount'] = number_format($portfolio['amount']);
	}
	// 시작일과 종료일의 유효성체크
	$start_date_timestamp = strtotime($start_date);
	$end_date_timestamp = strtotime($end_date);

	if($start_date_timestamp > $end_date_timestamp){
		$start_date = date('Y.m.d', strtotime('-7 days'));
		$end_date = date('Y.m.d');
	}

	$temp_strategy_ids = explode(',', $input_strategy_ids);
	$temp_percents = explode(',', $input_percents);

	$strategy_ids = array();
	foreach($temp_strategy_ids as $v){
		if(empty($v)) continue;
		if(in_array($v, $strategy_ids)) continue;
		$strategy_ids[] = $v;
	}

	if(count($strategy_ids)){
		$percents = array();
		foreach($temp_percents as $v){
			if(empty($v)) continue;
			$percents[] = $v;
		}
		$portfolio_strategies_map = array();
		foreach($strategy_ids as $k=>$v){
			$portfolio_strategies_map[$v] = empty($temp_percents[$k]) ? 0 : $temp_percents[$k];
		}
	}else{
		$portfolio_strategies = $app->db->select('portfolio_strategy', '*', array('portfolio_id'=>$id));
		$strategy_ids = array();
		$percents = array();
		$stored_portfolio_strategies_map = array();
		$portfolio_strategies_map = array();
		foreach($portfolio_strategies as $v){
			$strategy_ids[] = $v['strategy_id'];
			$percents[] = $v['percents'];
			$stored_portfolio_strategies_map[$v['strategy_id']] = $v['percents'];
			$portfolio_strategies_map[$v['strategy_id']] = $v['percents'];
		}
	}

	$min_value = 1000;
	$total_percent = 0;
	$strategies_percents = array();
	$first_date_array = array();
	$last_date_array = array();
	$strategy_daily_values = array();
	$strategies = array();
	$unified_smindex = array();
	$first_available_date = $portfolio['start_date'];
	$last_available_date = $portfolio['end_date'];
	$unified_c_price_array = array();
	if(count($strategy_ids)){
		$result = $app->db->conn->query('SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $strategy_ids).')');
		while($row = $result->fetch_array()){
			$strategies[] = $row;
		}			

		$exist_daily_data_map = array();
		foreach($strategies as $k => $v){
			$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));
			$daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));

			if(count($daily_values) < 2){
				$app->flash('error', '전략에 입력된 데이터가 부족하여 표시할수 없습니다');
				$app->redirect('/portfolios/'.$id);
			}

			$strategy_daily_values[$v['strategy_id'].''] = $daily_values;

			// 최초일과 마지막일 구함
			$first_date_array[] = $daily_values[0]['basedate'];
			$last_date_array[] = $daily_values[count($daily_values)-1]['basedate'];

			$total_percent += $percents[$k];
			$strategies_percents[$v['strategy_id']] = trim(str_replace('%', '', $percents[$k]));

			// 종목
			$strategy_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$v['strategy_id']));

			$item_id_array = array();
			$strategy_items_value = array();
			foreach($strategy_items as $kk=>$vv){
				$item_id_array[] = $vv['item_id'];
			}
			if(count($item_id_array)){
				$result = $app->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
				while($row = $result->fetch_array()){
					$strategy_items_value[] = $row;
				}			
			}

			$strategies[$k]['items'] = $strategy_items_value;

			// 브로커
			$strategies[$k]['broker'] = $app->db->selectOne('broker', '*', array('broker_id'=>$v['broker_id']));

			// 매매툴
			$strategies[$k]['system_tool'] = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$v['tool_id']));

			// 트레이더
			$developer = $app->db->selectOne('user', '*', array('uid'=>$v['developer_uid']));
			$strategies[$k]['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

			$sm_index_array = array();
	                foreach($daily_values_graph as $k1=>$v1){
				if( $min_value > $v1['sm_index'] )
					$min_value = $v1['sm_index'];
               		}

			$strategies[$k]['daily_values'] = $daily_values;
			$strategies[$k]['daily_values_graph'] = getSMIndexArray($daily_values_graph, $start_date, $end_date);

			// 표를 그리기 위한 데이터
			//$strategies[$k]['str_c_price'] = '['.implode(',', $sm_index_array ).']';
			$strategies[$k]['str_c_price'] = getChartDataString($strategies[$k]['daily_values_graph'], 'sm_index');

			// 비율
			$strategies[$k]['percents'] = $portfolio_strategies_map[$v['strategy_id']];
		}

		$first_available_date = intval($first_date_array[0]);
		foreach($first_date_array as $v){
			$tmp_date = str_replace('-','',$v); 
			if(intval($tmp_date) < $first_available_date){
				$first_available_date = intval($tmp_date);
			}
		}

		$last_available_date = intval($last_date_array[0]);
		foreach($last_date_array as $v){
			$tmp_date = str_replace('-','',$v); 
			if(intval($tmp_date) > $last_available_date){
				$last_available_date = intval($tmp_date);
			}
		}

		if($total_percent != 100){
			$app->flash('error', '비율 합이 100이어야 합니다');
			$app->redirect('/portfolios/'.$id);
		}
		
		$unified_sm_index = calPortfolioSMIndexGraph($strategies);
		$str_unified_sm_index = getChartDataString($unified_sm_index, 'sm_index');

		// 누적수익률
		$portfolio_total_profit_rate = calPortfolioPLrate($unified_sm_index);

		// 누적수익금액
		$portfolio_total_profit = $amount * $portfolio_total_profit_rate/100;
	}

	$app->render('portfolio_view.php', array('current_menu'=>$current_menu, 'portfolio'=>$portfolio, 'strategies'=>$strategies, 'min_value'=>$min_value,'str_unified_sm_index'=>$str_unified_sm_index,'portfolio_total_profit'=>$portfolio_total_profit, 'portfolio_total_profit_rate'=>$portfolio_total_profit_rate, 'first_available_date'=>$first_available_date, 'last_available_date'=>$last_available_date,'unified_c_price_array'=>$unified_c_price_array));
});

$app->get('/bbs/notice', function () use ($app, $log) {
	$current_menu = 'notice';

	$page = $app->request->get('page');
	if(empty($page) || !is_numeric($page)) $page = 1;
	$count = 15;
	$start = ($page - 1) * $count;
	$page_count = 10;
	$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

	$posts = $app->db->select('notice', '*', array('is_open'=>'1'), array('notice_id'=>'desc'), $start, $count);
	$total = $app->db->selectCount('notice');
	$total_page = ceil($total / $count);

	/*
	foreach($posts as $k=>$v){
		$posts[$k]['attachments'] = $app->db->select('attachment', '*', array('notice_id'=>$v['notice_id']));
	}
	*/

	$app->render('notice.php', array('current_menu'=>$current_menu, 'posts'=>$posts, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
});

$app->get('/bbs/notice/:id', function ($id) use ($app, $log) {
	$current_menu = 'notice';

	$page = $app->request->get('page');
	if(empty($page) || !is_numeric($page)) $page = 1;

	$post = $app->db->selectOne('notice', '*', array('notice_id'=>$id));

	if(empty($post)){
		$app->halt(404, 'not found');
	}

	if(!$post['is_open']){
		$app->halt(404, 'not found');
	}

	$post['attachments'] = $app->db->select('attachment', '*', array('notice_id'=>$post['notice_id']));

	$app->render('notice_view.php', array('current_menu'=>$current_menu, 'post'=>$post, 'page'=>$page));
});

$app->get('/qna', $authenticateForRole('N'), function () use ($app, $log) {
	$qs = $app->db->select('qna', '*', array('uid'=>$_SESSION['user']['uid']), array('qna_id'=>'desc'));
	$app->render('qna.php', array('qs'=>$qs));
});

$app->get('/qna/:id', $authenticateForRole('N'), function ($id) use ($app, $log) {
	$q_item = $app->db->selectOne('qna', '*', array('qna_id'=>$id, 'uid'=>$_SESSION['user']['uid']));

	if(empty($q_item)){
		$q_item->halt(404, 'not found');
	}

	$app->render('qna_view.php', array('q_item'=>$q_item));
});

$app->get('/my_answers', $authenticateForRole('N,T,B'), function () use ($app, $log) {
	$qs = $app->db->select('qna', '*', array('target'=>'trader', 'target_value'=>$_SESSION['user']['uid']), array('qna_id'=>'desc'));

	$app->render('trader_qna.php', array('qs'=>$qs));
});

$app->get('/my_answers/:id', $authenticateForRole('N,T,B'), function ($id) use ($app, $log) {
	$q_item = $app->db->selectOne('qna', '*', array('qna_id'=>$id, 'target'=>'trader', 'target_value'=>$_SESSION['user']['uid']));

	if(empty($q_item)){
		$q_item->halt(404, 'not found');
	}

	$app->render('trader_qna_view.php', array('q_item'=>$q_item));
});

$app->post('/my_answers/:id/answer', $authenticateForRole('T'), function ($id) use ($app, $log) {
	$answer = $app->request->post('answer');
	if(empty($answer)){
		$app->redirect('/my_answers/'.$id);
	}

	$q_item = $app->db->selectOne('qna', '*', array('qna_id'=>$id, 'target'=>'trader', 'target_value'=>$_SESSION['user']['uid']));

	if(empty($q_item)){
		$q_item->halt(404, 'not found');
	}

	if(!empty($q_item['answer'])){
		$app->redirect('/my_answers/'.$id);
	}

	$now = time();

	$app->db->update('qna', array('answer'=>$answer, 'answer_at'=>$now), array('qna_id'=>$id));

	$app->render('trader_qna_view.php', array('q_item'=>$q_item));
});

$app->get('/test', function () use ($app, $log) {

});

$app->get('/hook', function () use ($app, $log) {
	// apache
	// 테스트
	// echo exec('whoami');
	exec('cd /var/www && git pull origin master');
});

/*
$app->get('/:filename', function ($filename) use ($app, $log) {
	$url = $app->config('scheme').'://'.$app->config('host');
	$name = '김경민';
	$app->render($filename.'.php', array('url'=>$url, 'name'=>$name));
});
*/
