<?php
// 라우팅
$app->get('/aaaa', function () use ($app, $log) {
	// $app->halt(302);
	//$app->redirect('/fund');


        $url = $app->config('scheme').'://'.$app->config('host');

        ob_start();
		//if($user_type == 'T'){
		//	include $app->config('templates.path').'/mail/mail_signup_trader.php';
		//}elseif($user_type == 'P'){
		//	include $app->config('templates.path').'/mail/mail_signup_pb.php';
		//}else{
			include $app->config('templates.path').'/mail/mail_signup_normal.php';
		//}
        $content = ob_get_contents();
        ob_end_clean();

        $from = "gugisky77@gmail.com";
        $from_name = "gugisky";
        $to = "gugisky77@gmail.com";
        $subject = $app->config('name').' 회원가입';
        sendmail($from, $from_name, $to, $subject, $content);

});

$app->post('/sms', function () use ($app, $log) {
	$msg = $app->request->post('smsMsg');
	$hp = $app->request->post('smsHp');

	$SMSINFO['smsMsg']=$msg;
	$SMSINFO['smsHp']=$hp;

	echo sendSMS($SMSINFO);
});

// 관심그룹
$app->get('/admin_head', function () use ($app, $log) {
	//$app->render('admin_head.php', $param);
		$app->render('admin_head.php', array());
});

$app->get('/', function () use ($app, $log, $isLoggedIn) {

    // 파트너(브로커)메인고정.
    $partners_max = 6;
    $partners_main = $app->db->select('broker', '*', array('is_open'=> '1','is_main' =>'1'), array('sorting'=>'asc'), 0, $partners_max);
    if (count($partners_main) - $partners_max < 0) {
        $limit = $partners_max - count($partners_main);
        // 파트너 메인 나머지
        $partners_rand = $app->db->select('broker', '*', array('is_open'=> '1','is_main' =>'0'), array(''=>'rand()'), 0, $limit);
        foreach ($partners_rand as $k => $v) {
            array_push($partners_main, $v);
        }
    }

    // 트레이더 수
	$trader_count = $app->db->selectCount('user', array('user_type'=>'T'));
    // 트레이더 전략 수
    /*
    $sql = "
            SELECT count(*)
            FROM
                strategy a INNER JOIN user b ON (a.developer_uid = b.uid)
            WHERE
                b.user_type = 'T'
                AND a.is_delete = '0'
                AND a.is_operate='1'
                AND a.is_open = '1'";
    */
    $sql = "
            SELECT count(*)
            FROM
                strategy
            WHERE
                is_delete = '0'
                AND is_operate='1'
                AND is_open = '1'";

    $result = $app->db->conn->query($sql);
	$row = $result->fetch_array();
    $trader_strategy_count = $row[0];

    // pb수
	$pb_count = $app->db->selectCount('user', array('user_type'=>'P'));

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

	// 그래프 조회 제한일자 - 최근 3년 (2017-05-07)
	$sLimitDay = date("Y-m-d", mktime(0,0,0,date("n") - 36, date("d"), date("Y")));

	$follower_top_strategies_str = '';
	$top_strategies_str = '';
	$is_first = true;

	$follower_top_strategies = $app->db->select('strategy', '*', array('is_delete'=>'0','is_open'=>'1','is_operate'=>'1'), array('followers_count'=>'desc'), 0, 1);
	foreach($follower_top_strategies as $k=>$strategy){
		$developer = $app->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']));
		if(empty($developer['picture_s'])) $developer['picture_s'] = $app->config('default_picture_s');
		$follower_top_strategies[$k]['developer'] = $developer;

		// 산식
		$daily_values = $app->db->getAll(sprintf("SELECT * FROM strategy_daily_analysis WHERE strategy_id='%s' AND holiday_flag=0 AND basedate >= '%s' ORDER BY basedate ASC", $strategy['strategy_id'], $sLimitDay));
		$daily_values_graph = $app->db->getAll(sprintf("SELECT * FROM strategy_smindex WHERE strategy_id='%s' AND basedate >= '%s' ORDER BY basedate ASC", $strategy['strategy_id'], $sLimitDay));
			//- $daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));
			//- $daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$strategy['strategy_id']), array('basedate'=>'asc'));

		$follower_top_strategies[$k]['daily_values'] = $daily_values;

		// 표를 그리기 위한 데이터
		if ($is_first) {
			$follower_top_strategies_str = getChartDataString($daily_values_graph, 'sm_index');//'['.implode(',', $sm_index_array).']';
			$is_first = false;
		}
	}


    $sm_top_strategies = $app->db->selectOne('strategy', '*', array('is_delete'=>'0','is_open'=>'1','is_operate'=>'1'), array('sharp_ratio'=>'desc'));			// c_price
        //- $sm_top_strategies = $app->db->selectOne('strategy', '*', array('is_delete'=>'0','is_open'=>'1','is_operate'=>'1'), array('sharp_ratio'=>'desc'));
    $developer = $app->db->selectOne('user', '*', array('uid'=>$sm_top_strategies['developer_uid']));
    if(empty($developer['picture_s'])) $developer['picture_s'] = $app->config('default_picture_s');
    $sm_top_strategies['developer'] = $developer;
    $daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$sm_top_strategies['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));
    $sm_top_strategies['daily_values'] = $daily_values;



    $sql = "SELECT 
                a.strategy_id, sum(a.money) as money 
            FROM 
                strategy_funding a INNER JOIN strategy b ON a.strategy_id=b.strategy_id
            WHERE
                b.is_delete = '0' AND b.is_open = '1' AND b.is_operate = '1'
            GROUP BY a.strategy_id ORDER BY money DESC LIMIT 0, 1";
            //echo $sql;
    $result = $app->db->conn->query($sql);
    $row = $result->fetch_array();
    $fund_top_strategies = $app->db->selectOne('strategy', '*', array('strategy_id'=>$row['strategy_id']));
    $developer = $app->db->selectOne('user', '*', array('uid'=>$fund_top_strategies['developer_uid']));
    if(empty($developer['picture_s'])) $developer['picture_s'] = $app->config('default_picture_s');
    $fund_top_strategies['developer'] = $developer;
    $daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$fund_top_strategies['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));
    $fund_top_strategies['daily_values'] = $daily_values;




	$is_first = true;
	// $top_strategies = $app->db->select('strategy', '*', array('is_delete'=>'0','is_open'=>'1','is_operate'=>'1'), array('sharp_ratio'=>'desc'), 0, 5);			// c_price
	$top_strategies = array();
	$sql_st = sprintf("select * from strategy WHERE is_delete='0' AND is_open='1' AND is_operate='1' AND strategy_id not in (338) order by sharp_ratio DESC LIMIT 5");
	$result = $app->db->conn->query($sql_st);
		while($row = $result->fetch_array()){
			$top_strategies[] = $row;
		}

		//- $top_strategies = $app->db->select('strategy', '*', array('is_delete'=>'0','is_open'=>'1','is_operate'=>'1'), array('sharp_ratio'=>'desc'), 0, 5);
	foreach($top_strategies as $k=>$strategy){
		$developer = $app->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']));
		if(empty($developer['picture_s'])) $developer['picture_s'] = $app->config('default_picture_s');
		$top_strategies[$k]['developer'] = $developer;

        $top_strategies[$k]['types'] = $app->db->selectOne('type', '*', array('type_id'=>$strategy['strategy_type']));

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

		$top_strategies[$k]['items'] = $strategy_items_value;


		// 산식
		$daily_values = $app->db->getAll(sprintf("SELECT * FROM strategy_daily_analysis WHERE strategy_id='%s' AND holiday_flag=0 AND basedate >= '%s' ORDER BY basedate ASC", $strategy['strategy_id'], $sLimitDay));
		$daily_values_graph = $app->db->getAll(sprintf("SELECT * FROM strategy_smindex WHERE strategy_id='%s' AND basedate >= '%s' ORDER BY basedate ASC", $strategy['strategy_id'], $sLimitDay));
			//- $daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));
			//- $daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$strategy['strategy_id']), array('basedate'=>'asc'));

		$top_strategies[$k]['daily_values'] = $daily_values;
		$sm_index_array = array();
        foreach($daily_values as $k1=>$v1){
                $m_timestamp = strtotime($v1['basedate'])*1000;
                $sm_index_array[] = '['.$m_timestamp.','.$v1['sm_index'].']';
        }

		// 표를 그리기 위한 데이터
		$top_strategies[$k]['str_c_price'] = '['.implode(',', $sm_index_array ).']';

		// 표를 그리기 위한 데이터
		if ($is_first) {
			$top_strategies_str =  getChartDataString($daily_values_graph, 'sm_index');//'['.implode(',', $sm_index_array ).']';
			$is_first = false;
		}
	}

	// 통합 기준가(모든 전략의 공통된 날짜의 통합기준가)
	$univ_values = $app->db->getAll(sprintf("SELECT * FROM univ_index WHERE basedate >= '%s' ORDER BY basedate ASC", $sLimitDay));
		//- $univ_values = $app->db->select('univ_index', '*', array(), array('basedate'=>'asc'));
	$univ_values_array = array();
	foreach($univ_values as $k=>$v){
		$m_timestamp = strtotime($v['basedate'])*1000;
		$univ_values_array[] = '['.$m_timestamp.','.$v['sm_index'].']';
	}
	$univ_values_str = '['.implode(',', $univ_values_array).']';


    // banner
    $banners = array();
    $sql = "select * from banner WHERE start_date < now() and end_date > now()";
    $result = $app->db->conn->query($sql);
    while ($row = $result->fetch_array()) {
        $banners[] = $row;
    }

	$app->render('index.php',
            array(
                'partners_main'                 => $partners_main,
                'main_profit_rate'              => $main_profit_rate,
                'trader_count'                  => $trader_count,
                'trader_strategy_count'         => $trader_strategy_count,
                'pb_count'                      => $pb_count,
                'total_investor'                => $total_investor,
                'total_funding'                 => $total_funding,
                'top_strategies'                => $top_strategies,
                'follower_top_strategies'       => $follower_top_strategies,
                'univ_values_str'               => $univ_values_str,
                'top_strategies_str'            => $top_strategies_str,
                'follower_top_strategies_str'   => $follower_top_strategies_str,
                'fund_top_strategies'           => $fund_top_strategies,
                'sm_top_strategies'             => $sm_top_strategies,
                'banners'                       => $banners,
     ));
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

$app->get('/history', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'history';

	$app->render('history.php', array('current_menu'=>$current_menu));
});

$app->get('/business_area', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'business_area';

	$app->render('business_area.php', array('current_menu'=>$current_menu));
});

$app->get('/network', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'network';

	$app->render('network.php', array('current_menu'=>$current_menu));
});

$app->get('/recruit', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'recruit';

	$app->render('recruit.php', array('current_menu'=>$current_menu));
});

$app->get('/partent', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'partent';

	$app->render('partent.php', array('current_menu'=>$current_menu));
});

$app->get('/contact', function() use ($app, $log, $isLoggedIn) {
	$current_menu = 'contact';

	$app->render('contact.php', array('current_menu'=>$current_menu));
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

// 로그인
$app->post('/login_chk', function() use ($app, $log, $isLoggedIn) {
	$mb_id = $app->request->post('UserID');
	$mb_pw = $app->request->post('UserPW');
	$mb_key = $app->request->post('Key');
	$today = date("Ymd");
	$key = md5($mb_id."^^".$today);
	if($key != $mb_key){
		echo json_encode(array('Result_code'=>'100'));
	}else{
		// 일반로그인시에는 플랫폼이 없는 계정만 로그인됨
		$member_info = $app->db->selectOne('user', '*', array('email'=>$mb_id, 'is_delete'=>'0'));
		if(!empty($member_info)){
			echo json_encode(array('Result_code'=>'000'));
			//if(validate_password($mb_pw, $member_info['user_password'])){
			//	echo json_encode(array('Result_code'=>'000'));
			//}else{
			//	echo json_encode(array('Result_code'=>'102'));
			//}
		}else{
			echo json_encode(array('Result_code'=>'101'));
		}
	}
});

// 로그인
$app->get('/signin', function() use ($app, $log, $isLoggedIn) {
	$redirect_url = $app->request->get('redirect_url');
	if(empty($redirect_url)){
		$redirect_url = '';
	}
//__v($_COOKIE);
	$remember_id = $app->getCookie('remember_id');

	if($isLoggedIn()){
		$app->redirect('/');
	}else{
		// $app->render('login.php', array('redirect_url'=>$redirect_url, 'show_signin'=>true));
		$app->render('login.php', array('redirect_url'=>$redirect_url, 'remember_id'=>$remember_id));
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
	$remember_id = $app->request->post('remember_id');

	if(empty($email)){
		$app->flash('error', '아이디를 입력해주세요');
		$app->redirect('/signin');
	}

	if(empty($password)){
		$app->flash('error', '비밀번호를 입력해주세요');
		$app->redirect('/signin');
	}

	// 일반로그인시에는 플랫폼이 없는 계정만 로그인됨
	$member_info = $app->db->selectOne('user', '*', array('email'=>$email, 'is_delete'=>'0'));
	if(!empty($member_info)){
		if(validate_password($password, $member_info['user_password'])){

			//실명인증 체크
			if($member_info['live_name_chk']!="Y" and $member_info['user_type'] != 'A'){
				$app->redirect('/member/mem_update?email='.$email);
				exit;
			}

			if(!empty($remember_id)){
				//$token = createAuthorKey();
				//$app->db->insert('auth_token', array('uid'=>$member_info['uid'], 'token'=>$token));
				setcookie('remember_id', $member_info['email'], time()+60*60*24*30, '/');
				//setcookie('remember_me', $token, time()+60*60*24*30, '/');

			} else {
	            setcookie('remember_id', '', time()-60*60*24*30, '/');
            }

            if ($member_info['user_type'] == 'T' && $member_info['is_request_trader'] != '0') {
                $app->flash('error', '승인 대기중입니다.');
                $app->redirect('/signin');
            } else if ($member_info['user_type'] == 'P' && $member_info['is_request_pb'] != '0') {
                $app->flash('error', '승인 대기중입니다.');
                $app->redirect('/signin');
            } else {

                unset($member_info['member_password']);
                $_SESSION['user'] = $member_info;

                if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');
                if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');

                if (!empty($redirect_url)) {
                    $app->redirect($redirect_url);
                } else {
                    $app->redirect('/');
                }
            }

		}else{
			$app->flash('error', '아이디 또는 비밀번호가 일치하지 않습니다.');
			$app->redirect('/signin');
		}
	}else{
		$app->flash('error', '아이디 또는 비밀번호가 일치하지 않습니다.');
		$app->redirect('/signin');
	}

});

$app->get('/logout', function() use ($app) {
    /*
	$remember_uid = $app->getCookie('remember_uid');
	$remember_me = $app->getCookie('remember_me');

	if(!empty($remember_uid) && !empty($remember_me)){
		$exist_token = $app->db->delete('auth_token', array('uid'=>$remember_uid, 'token'=>$remember_me));
	}
    */
	setcookie('remember_uid', '', time()-60*60*24*30, '/');
	setcookie('remember_me', '', time()-60*60*24*30, '/');


	session_unset();
	session_destroy();
	$app->redirect('/');
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
		$code = substr(createAuthorKey(), 0, 6);
		$app->db->update('user', array('user_password'=>create_hash($code)), array('email'=>$target_member['email']));

		$from = $app->config('system_sender_email');
		$from_name = $app->config('system_sender_name');
		$to = $target_member['email'];
		$subject = 'SYSMETIC TRADERS 임시 비밀번호를 안내해드립니다.';

		//$password_link = $app->request->getScheme().'://'.$app->request->getHost().'/set_password?uid='.$target_member['uid'].'&code='.$code;
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
        include $app->config('templates.path').'/mail/mail_password.php';
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
	/*
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
	*/

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

// 이미지 삭제
$app->post('/settings/delete_picture', function() use ($app, $log, $isLoggedIn) {
    $img = $app->request->post('img');
    $type = $app->request->post('type');
	$savePath = $app->config("$type.path");

    $file_name = basename($img);

    if (is_file($savePath.'/'.$file_name)) {
        if (unlink($savePath.'/'.$file_name)) {
            unlink($savePath.'/'.str_replace('.', '_s.', $file_name));
			$_SESSION['temp_'.$type.'_url'] = '';
			$_SESSION['temp_'.$type.'_s_url'] = '';

            echo 'success';
        } else {
         //   echo $savePath.'/'.$file_name;
          //  echo 'error';
            echo 'success';
        }
    }

    $app->stop();
});

// 네임카드 이미지 등록 (pb)
$app->post('/settings/upload_namecard', function() use ($app, $log, $isLoggedIn) {
	$profile_url = '';
	$profile_s_url = '';
	$profile_m_url = '';
	$savePath = $app->config('namecard.path');
	$max_file_size = 1024 * 1024;

	// 업로드 된 파일이 있는지 확인
	switch($_FILES['namecard']['error']){
		case UPLOAD_ERR_OK:
			$filename = $_FILES['namecard']['name'];
			$filesize = $_FILES['namecard']['size'];
			$filetmpname = $_FILES['namecard']['tmp_name'];
			$filetype = $_FILES['namecard']['type'];
			$tmpfileext = explode('.', $filename);
			$fileext = $tmpfileext[count($tmpfileext)-1];

			// check filesize
			if($filesize > $max_file_size){
				echo '<script>alert("이미지파일은 1MB 이하로 업로드해주세요.")</script>';
				$app->stop();
			}

			if(strpos($filetype, 'image') === false){
				echo '<script>alert("이미지 파일만 업로드 가능합니다.")</script>';
				$app->stop();
			}

			// check upload valid ext
			if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
				echo '<script>alert("확장자가 jpg, gif, png 파일만 업로드가 가능합니다.")</script>';
				$app->stop();
			}

			// upload correct method
			if(!is_uploaded_file($filetmpname)){
				echo '정상적인 방법으로 업로드해주세요';
				$app->stop();
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
			$_SESSION['temp_namecard_url'] = $img_url = $app->config('namecard.url').'/'.$saveFilename.'.'.$fileext;
			$_SESSION['temp_namecard_s_url'] = $img_s_url = $app->config('namecard.url').'/'.$saveFilename.'_s.'.$fileext;

			if(!move_uploaded_file($filetmpname, $finalFilename)){
				echo '업로드에 실패하였습니다';
				$app->stop();
			}

			createThumbnail($finalFilename, $finalThumbFilename, 220, 120, false, true);
			echo $img_url;
			break;
		case UPLOAD_ERR_INI_SIZE:
			echo '업로드 가능 용량을 초과하였습니다';
			$app->stop();
			break;
		case UPLOAD_ERR_FORM_SIZE:
			echo '업로드 가능 용량을 초과하였습니다';
			$app->stop();
			break;
		case UPLOAD_ERR_PARTIAL:
			echo '업로드에 실패하였습니다';
			$app->stop();
			break;
		case UPLOAD_ERR_NO_FILE:
			echo '첨부된 파일이 없습니다';
			$app->stop();
			break;
		default:
			echo '업로드에 실패하였습니다';
			$app->stop();
	}
});

// 프로필 이미지 등록 (pb, 트레이더)
$app->post('/settings/upload_picture', function() use ($app, $log, $isLoggedIn) {
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
			echo $profile_url;
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

    $app->redirect('/investment/strategies');
    $app->stop();

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
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));

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
	if (empty($start) || !is_numeric($start)) $start = 0;
	$count = $app->request->get('count');
	if (empty($count) || !is_numeric($count) || $count > 20) $count = 20;

	$search['q_item'] = $app->request->get('item');
	$search['q_term'] = $app->request->get('term');
	$search['q'] = $app->request->get('q');
	$search['is_open'] = '1';
	$search['is_operate'] = '1';
	$search['is_delete'] = '0';
	$sort['field'] = $app->request->get('sort');

	$stg = new \Model\Strategy($app->db);
	$aStResult = $stg->getList($search, $sort, $start, $count);
	$strategies = $aStResult[1];

	$items = $app->db->select('item', '*', array(), array('sorting'=>'asc'));

	$format = $app->request->get('format');
	if(!empty($format) && $format == 'json'){
		$response_strategies = array();
		foreach ($strategies as $k => $v) {
			if ($v['daily_values_cnt'] < 2) continue;
			$response_strategies[] = $v;
		}
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

	$stg = new \Model\Strategy($app->db);
	list($total, $reviews) = $stg->getReviewList($id, $start, $count);
	/*
	$reviews = $app->db->select('strategy_review', '*', array('strategy_id'=>$id), array('review_id'=>'desc'), $start, $count);
	foreach($reviews as $k=>$review){
		$writer = $app->db->selectOne('user', '*', array('uid'=>$review['writer_uid']));
		$reviews[$k]['writer'] = array('uid'=>$writer['uid'], 'nickname'=>$writer['nickname']);
	}
	$total = $app->db->selectCount('strategy_review', array('strategy_id'=>$id));
	*/

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

	$stg = new \Model\Strategy($app->db);
	$param['star'] = $star;
	$param['review_body'] = $review_body;
	$stg->insertReview($id, $param);

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

/*
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
	$strategy['daily_values'] = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));;

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
*/

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
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));

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

		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));

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

$app->get('/portfolios', $authenticateForRole('N,T,B,P'), function() use ($app, $log) {
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

$app->get('/portfolios/create', $authenticateForRole('N,T,B,P'), function() use ($app, $log, $isLoggedIn) {
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
			$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));
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
		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy_id, 'holiday_flag'=>'0'), array('basedate'=>'asc'));
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
	$pfo = new \Model\Portfolio($app->db);
	if ($pfo->delete($id) == false) {
		$app->halt(403, 'fobidden');
	}

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

		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy_id, 'holiday_flag'=>'0'), array('basedate'=>'asc'));
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
			$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));
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

	$app->render('cs/notice.php', array('current_menu'=>$current_menu, 'posts'=>$posts, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
});

$app->get('/bbs/notice_list', function () use ($app, $log) {
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

	$app->render('cs/notice_list.php', array('current_menu'=>$current_menu, 'posts'=>$posts, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
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


///////////////////////////////////////////////////////

// pb lounge
$app->get('/lounge', $authenticateForRole('P'), function() use ($app, $log) {

	$param = array();

	$app->render('lounge.php', $param);
});

// 파트너스 (기존 브로커)
$app->get('/partners', function() use ($app, $log, $isLoggedIn) {
	$brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
	foreach($brokers as $k => $broker){
		$s_tools = $app->db->select('system_trading_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
		$a_tools = $app->db->select('api_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
		$brokers[$k]['system_trading_tools'] = $s_tools;
		$brokers[$k]['api_tools'] = $a_tools;
	}

	$app->render('partner_list.php', array('brokers'=>$brokers));
});
