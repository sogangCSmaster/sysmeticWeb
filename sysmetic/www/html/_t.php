<?php
//if(!in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
//	exit;
//}
require_once dirname(__FILE__).'/../bootstrap.php';

// role : N(일반), T(트레이더), B(브로커), A(어드민) 여러개의 롤이 가능한경우 콤마로 연결
$authenticateForRole = function ($role = 'N'){
	return function () use ($role){
		$app = \Slim\Slim::getInstance();
		$format = $app->request->get('format');

		if(!empty($_SESSION['user']) || !empty($_SESSION['user']['user_type'])){
			$role_array = explode(',', $role);
			if(in_array($_SESSION['user']['user_type'], $role_array) || $_SESSION['user']['user_type'] == 'A'){

			}else{
				if(!empty($format) && $format == 'json'){
					$app->response->setStatus(403);

					$app->response->headers->set(
						'Content-Type',
						'application/json'
					);

					echo json_encode(
						array(
							'code' => 403,
							'message' => 'forbiden'
						)
					);

					exit();
				}else{
					//$app->halt(403, 'forbiden');
                    $app->redirect('/');
				}
			}
		}else{
			if(!empty($format) && $format == 'json'){
				$app->response->setStatus(401);

				$app->response->headers->set(
					'Content-Type',
					'application/json'
				);

				echo json_encode(
					array(
						'code' => 401,
						'message' => 'Unauthorized'
					)
				);

				exit();
			}else{
				$app->redirect('/signin?redirect_url='.urlencode($app->config('scheme').'://'.$app->config('host').$_SERVER['REQUEST_URI']));
			}
		}
	};
};

// 로그인체크
$isLoggedIn = function() {
	if(isset($_SESSION['user'])) return true;
	else return false;
};

// 뷰에서 공통 사용가능한 변수
$app->view()->appendData(array(
	// 'LANG'=>$LANG,
	'isLoggedIn'    => $isLoggedIn,
    'skinDir'       => $app->config('templates.path').'/',
));

// remember me
$app->hook('slim.before.router', function () use ($app, $isLoggedIn) {
	if(!$isLoggedIn()){
		$remember_uid = $app->getCookie('remember_uid');
		$remember_me = $app->getCookie('remember_me');

		if(!empty($remember_uid) && !empty($remember_me)){
			$exist_token = $app->db->selectOne('auth_token', '*', array('uid'=>$remember_uid, 'token'=>$remember_me));

			if(!empty($exist_token)){
				$member_info = $app->db->selectOne('user', '*', array('uid'=>$remember_uid));
				if(!empty($member_info)){
					unset($member_info['member_password']);
					$_SESSION['user'] = $member_info;

					if(empty($_SESSION['user']['picture_s'])) $_SESSION['user']['picture_s'] = $app->config('default_picture_s');

					if(empty($_SESSION['user']['picture'])) $_SESSION['user']['picture'] = $app->config('default_picture');
				}else{
					setcookie('remember_uid', '', time()-60*60*24*30, '/');
					setcookie('remember_me', '', time()-60*60*24*30, '/');
				}
			}else{
				setcookie('remember_uid', '', time()-60*60*24*30, '/');
				setcookie('remember_me', '', time()-60*60*24*30, '/');
			}
		}
	}
});

	print '<xmp>';
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);

	$i = 0;
	$sToday = Date("Y-m-d", time()-86400);
	$result = $app->db->conn->query("SELECT * FROM strategy ");
	while($row = $result->fetch_array()){
		$sql_old = sprintf("SELECT * FROM strategy_daily_analysis WHERE strategy_id='%s' ORDER BY basedate DESC LIMIT 1", $row['strategy_id']);
		$aRowOld = $app->db->conn->query($sql_old)->fetch_array();
		$row['sStartDay'] = $sToday;
		if($row['sStartDay'] <= $aRowOld['basedate']) {
			continue;
		} else if($aRowOld['basedate'] != ''){
			$row['sStartDay'] = Date("Y-m-d", strtotime($aRowOld['basedate']) + 86400);
		}
		printf("{%s} [%s] %s ~ %s    (%s)\n", ++$i, $row['strategy_id'], $row['sStartDay'], $sToday, Date("H:i:s"));

		////////////////////////////////////////////////////
		// analysis_strategy() 계산하기
			setStrategyAnalysis($app->db, $row['strategy_id'], $row['sStartDay'], $sToday);
			setStrategyAnalysisMonthly($app->db, $row['strategy_id']);
			setStrategyAnalysisYearly($app->db, $row['strategy_id']);
		////////////////////////////////////////////////////
	}
	$aDebug[] = time();
	setStrategyScore($app->db);

	$aDebug['end'] = time();
	$aDebug['t_e'] = Date("H:i:s", $aDebug['end']);
	$aDebug['exec'] = $aDebug['end'] - $aDebug['start'];
	$app->db->insert('z_dev_log', array('st_id'=>$st_id, 'dl_type'=>'cron_daily_st', 'dl_memo'=>print_r($aDebug, true), 'dl_sys'=>sprintf("%s \n _SERVER : %s", print_r($_REQUEST, true), print_r($_SERVER, true))));

	print '</xmp>';

//
//
//
//
//// 라우트 파일 인클루드
//foreach (glob(dirname(__FILE__).'/../app/routes/*.php') as $routeFile) {
//    require $routeFile;
//}
//
//$app->run();