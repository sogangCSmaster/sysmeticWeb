<?php
/**
 * 라운지 관련 라우터
 * PB 회원 전용 메뉴
 */

// 라운지
$app->group('/lounge', function() use ($app, $log, $isLoggedIn, $authenticateForRole) {

    // 라운지 메인
    $app->get('/', function() use ($app, $log) {
        
        $topmenu = 'lounge';

        $total_pb = $app->db->selectOne('user', 'count(*) cnt', array('user_type'=>'P', 'is_request_pb'=>'0', 'is_delete'=>'0'));
        $total_pb = $total_pb['cnt'];

        $stg = new \Model\Strategy($app->db);

			if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250__111'))) {
				printf("<xmp align='left'>\n\n\n\n\n\n\n\n\n\n\n\n%s\n nSumGap : %s\n nTotalPlRate : %s\n======================================</xmp>", print_r($aStats['aPlRateDay'], true), $nSumGap, $nTotalPlRate);
			}

        // pb랜덤 5명
        $sql = "SELECT * FROM user a WHERE user_type = 'P' AND is_request_pb = '0' AND is_delete = '0' AND (SELECT COUNT(*) AS cnt FROM strategy WHERE (developer_uid = a.uid or pb_uid = a.uid) and is_delete='0' and is_operate='1' and is_open='1' and sharp_ratio != 0) > 0 ORDER BY RAND() LIMIT 5";
        $result = $app->db->conn->query($sql);
        $developer_uid = array();
        while ($row = $result->fetch_array()) {
            $row['total_profit_rate'] = $stg->getBasicStg($row['uid'], 'total_profit_rate');

			$aStIds = array();
			$aStDaily = array();
			$aStats = array('aItemPercent'=>array(), 'aItemMoney'=>array(), 'first_time'=>0, 'last_time'=>0, 'acc_pl_rate'=>0, 'max_acc_pl'=>0,'max_acc_pl_rate'=>0, 'max_profit_days_continue'=>0, 'max_loss_days_continue'=>0, 'after_peak_days'=>0, 'aStItem'=>array(), 'aPlRateDay'=>array());
			$strategies = array();
			$result2 = $app->db->conn->query("SELECT * FROM strategy WHERE is_delete='0' and is_operate='1' and is_open='1' AND ( developer_uid IN (". $row['uid'] .") OR pb_uid in ( ". $row['uid'] ." ) )");
			if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250__111'))) {
				// printf("<xmp align='left'>%s</xmp>", "SELECT * FROM strategy WHERE is_delete='0' and is_operate='1' and is_open='1' AND ( developer_uid IN (". $row['uid'] .") OR pb_uid in ( ". $row['uid'] ." ) )");

			}
			while($row2 = $result2->fetch_array()){
				$strategies[] = $row2;
				$aStIds[] = $row2['strategy_id'];

				// $nTotPricipal += $row['principal'];
			}

			if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250__111'))) {
				// printf("<xmp align='left'>%s</xmp>", print_r($strategies, true));
			}

			foreach($strategies as $k => $v){
				$strategies[$k]['percents'] = 100 / count($strategies);
				$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));

				// 통계 데이터 설정
				$aStats['acc_pl'] += $daily_values[count($daily_values)-1]['acc_pl'];
				$aStats['acc_pl_rate'] += $daily_values[count($daily_values)-1]['acc_pl_rate'];

				if($aStats['first_time'] < 1 || $aStats['first_time'] > strtotime($daily_values[0]['basedate']))
					$aStats['first_time'] = strtotime($daily_values[0]['basedate']);

				if($aStats['last_time'] < 1 || $aStats['last_time'] < strtotime($daily_values[count($daily_values)-1]['basedate']))
					$aStats['last_time'] = strtotime($daily_values[count($daily_values)-1]['basedate']);
			}
			$aStats['acc_pl'] = @round($aStats['acc_pl'] / count($strategies));
			$aStats['acc_pl_rate'] = @round($aStats['acc_pl_rate'] / count($strategies),2);

			$aPlRateInfo = getPortfolioPlRateInfo($app->db, $aStIds);						// PB-대표수익률,포폴-누적수익률 (2017-05-05)
			$aStats['total_pl_rate'] = $aPlRateInfo['pl_rate'];

			$row['aStats'] = $aStats;

            $pb[] = $row;
        }

        // 대표

        // 컨텐츠 개수
        /*
        $sql = "SELECT COUNT(*) cnt
                FROM pb_contents a INNER JOIN . user b ON a.uid=b.uid
                WHERE a.reg_date >= date_add(now(), interval -7 day)";
        */
        // 컨텐츠 개수
        $sql = "SELECT COUNT(*) cnt
                FROM pb_contents a INNER JOIN . user b ON a.uid=b.uid";

        $result = $app->db->conn->query($sql);
        $row = $result->fetch_array();
        $total_contents = $row['cnt'];

        $app->render('lounge/lounge.php', array('topmenu'=>$topmenu, 'total_pb' => $total_pb, 'total_contents'=>$total_contents, 'pb'=>$pb));
    });

    // 메인 게시물 자료..
    $app->get('/load_contents', function() use ($app, $log) {

        $page = $app->request->get('page');

        if ($app->request->get('type') == 'timeline') {
            $tpl = 'lounge/main_timeline.php';
            $limit = 4;
        } else {
            $tpl = 'lounge/main_thumb.php';
            $limit = 8;
        }
        $start = ($page - 1) * $limit;

        $end = $limit + 1;

        /*
        $sql = "SELECT a.*, b.*
                FROM 
                    pb_contents a INNER JOIN user b ON a.uid=b.uid
                WHERE a.reg_date >= date_add(now(), interval -7 day)
                ORDER BY a.cidx DESC limit $start, $end";
        */

        $sql = "SELECT a.*, b.*
                FROM 
                    pb_contents a INNER JOIN user b ON a.uid=b.uid
                ORDER BY a.cidx DESC limit $start, $end";
        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_array()) {
            $images = $app->db->selectOne('pb_contents_file', 'save_name', array('cidx'=>$row['cidx']), array('fid'=>'asc'));
            $row['images'] = $images;
            $lists[] = $row;
        }

echo "<script>console.log('".$limit." < ".count($lists)." / ".$next."')</script>";

        $next = 0;
        if ($limit < count($lists)) {
            $next = $page + 1;

            array_pop($lists);
        }

        $app->render($tpl, array('lists'=>$lists, 'next'=>$next));
    });

    // pb메인
    $app->get('/:uid', function($uid) use ($app, $log, $isLoggedIn) {

        $topmenu = 'lounge';
        if ($isLoggedIn()) {
            $subscribe_chk = $app->db->selectCount('subscribe', array('uid'=>$uid, 'reg_uid'=>$_SESSION['user']['uid']));
        }
        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;

        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));

        // 대표 전략...
        $stg = new \Model\Strategy($app->db);
        $strategy_id = $stg->getBasicStg($uid);

        if (!$strategy_id) {
            // $app->halt(404, 'not found');
            // 전략이 없을경우 프로필로 이동
            $app->redirect('/lounge/'.$uid.'/profile');
        } else {
            $strategy = $stg->getInfo($strategy_id, $isLoggedIn);
        }

		$aStList = array();
        $sql = "SELECT strategy_id
                FROM 
                    strategy
                WHERE (developer_uid = $uid or pb_uid = $uid) and is_delete='0' and is_operate='1' and is_open='1' ";
		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_assoc()){
			$aStList[] = $row['strategy_id'];
		}
		$total = count($aStList);
		$aPlRateInfo = getPortfolioPlRateInfo($app->db, $aStList);

		$aChartPlRate = array();
		foreach($aPlRateInfo['arr_daily_pl_rate'] as $k=>$v){
			$m_timestamp = strtotime($k)*1000;
			$aChartPlRate[] = '['.$m_timestamp.','.$v.']';
		}
		$sChartPlRate = '['.implode(',', $aChartPlRate).']';
			//        $result = $app->db->conn->query($sql);
			//        $row = $result->fetch_array();
			//		$total = $row['cnt'];

        $total_contents = $app->db->selectOne('pb_contents', 'count(*) cnt',array('uid'=>$uid),  array());
        $total_contents = $total_contents['cnt'];
        $contents = $app->db->select('pb_contents', 'uid, cidx, subject, contents', array('uid'=>$uid), array('cidx'=>'desc'), 0, 4);

        $param = array(
            'topmenu'   => $topmenu
            ,'pb'        => $pb
            ,'subscribe_cnt' => $subscribe_cnt
            ,'subscribe_chk' => $subscribe_chk
            ,'strategy'  => $strategy
            ,'contents'  => $contents
            ,'total_contents' => $total_contents
			,'total' => $total
			,'total_pl_rate'=>$aPlRateInfo['pl_rate']
			,'total_mdd_rate'=>$aPlRateInfo['mdd_rate']
			,'chart_pl_rate'=>$sChartPlRate
        );

        $app->render('lounge/pb_main.php', $param);
    });

    // 프로필
    $app->get('/:uid/profile', function($uid) use ($app, $log, $isLoggedIn) {

        $topmenu = 'lounge';
        if ($isLoggedIn()) {
            $subscribe_chk = $app->db->selectCount('subscribe', array('uid'=>$uid, 'reg_uid'=>$_SESSION['user']['uid']));
        }
        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));
        $mine = ($uid == $_SESSION['user']['uid'] or $_SESSION['user']['user_type']=='A') ? true : false;

        $profile = $app->db->selectOne('pb_profile', '*', array('uid'=>$uid));
        if ($profile) $profile = array_map('nl2br', $profile);


        $param = array(
            'topmenu'   => $topmenu, 
            'pb' => $pb,
            'subscribe_cnt' => $subscribe_cnt,
            'subscribe_chk' => $subscribe_chk,
            'profile' => $profile,
            'menu' => 'profile',
            'mine' => $mine,
        );

        $app->render('lounge/pb_profile.php', $param);
    });

    // 프로필
    $app->get('/:uid/profile/write', function($uid) use ($app, $log, $isLoggedIn) {

        $topmenu = 'lounge';

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));

        if ($uid !== $_SESSION['user']['uid'] and $_SESSION['user']['user_type']!='A') {
            $app->redirect("/$uid/profile");
        }

        $profile = $app->db->selectOne('pb_profile', '*', array('uid'=>$uid));
        $mode = 'reg';
        if ($profile) {
            $mode = 'edit';
        }

        $param = array(
            'pb' => $pb,
            'subscribe_cnt' => $subscribe_cnt,
            'profile' => $profile,
            'menu' => 'profile',
            'mine' => $mine,
            'mode' => $mode
        );

        $app->render('lounge/pb_profile_write.php', $param);
    });

    // 프로필
    $app->post('/:uid/profile/write', function($uid) use ($app, $log, $isLoggedIn) {

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));

        if ($uid !== $_SESSION['user']['uid'] and $_SESSION['user']['user_type']!='A') {
            $app->redirect("/lounge/$uid/profile");
        }

        $career = $app->request->post('career');
        $license = $app->request->post('license');
        $introduce = $app->request->post('introduce');
        $etc = $app->request->post('etc');


        if ($app->request->post('mode') == 'reg') {
            $param = array(
                'uid' => $uid,
                'career' => $career,
                'license' => $license,
                'introduce' => $introduce,
                'etc' => $etc
            );

            $app->db->insert('pb_profile', $param);
        } else {
            $param = array(
                'career' => $career,
                'license' => $license,
                'introduce' => $introduce,
                'etc' => $etc
            );

            $app->db->update('pb_profile', $param, array('uid'=>$uid));
        }

        $app->redirect("/lounge/$uid/profile");
    });

    // 전략목록
    $app->get('/:uid/strategies', function($uid) use ($app, $log, $isLoggedIn) {

        $topmenu = 'lounge';
        if ($isLoggedIn()) {
            $subscribe_chk = $app->db->selectCount('subscribe', array('uid'=>$uid, 'reg_uid'=>$_SESSION['user']['uid']));
        }
        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));

        $mine = ($uid == $_SESSION['user']['uid']) ? true : false;

        //$row = $app->db->selectOne('strategy', 'COUNT(*) cnt', array('developer_uid'=>$uid,'is_delete'=>'0', 'is_operate'=>'1', 'is_open'=>'1'));
        $sql = "SELECT COUNT(*) cnt
                FROM 
                    strategy
                WHERE (developer_uid = $uid or pb_uid = $uid or trader_uid = $uid) and is_delete='0' and is_operate='1' and is_open='1' ";
        $result = $app->db->conn->query($sql);
        $row = $result->fetch_array();
        $total = $row['cnt'];

        $param = array(
            'topmenu'       => $topmenu,
            'menu'          => 'strategies',
            'pb'            => $pb,
            'subscribe_cnt' => $subscribe_cnt,
            'subscribe_chk' => $subscribe_chk,
            'mine'          => $mine,
            'total'=>$total,
        );

        $app->render('lounge/pb_strategy.php', $param);

    });

    // 포트폴리오 목록
    $app->get('/:uid/portfolios', function($uid) use ($app, $log, $isLoggedIn) {

        $topmenu = 'lounge';
        if ($isLoggedIn()) {
            $subscribe_chk = $app->db->selectCount('subscribe', array('uid'=>$uid, 'reg_uid'=>$_SESSION['user']['uid']));
        }
        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));

        $mine = ($uid == $_SESSION['user']['uid']) ? true : false;

        $row = $app->db->selectOne('portfolio', 'COUNT(*) cnt', array('uid'=>$uid, 'is_open'=>'1'));
        $total = $row['cnt'];

        $param = array(
            'topmenu'       => $topmenu,
            'menu' => 'portfolios',
            'pb' => $pb,
            'subscribe_cnt' => $subscribe_cnt,
            'subscribe_chk' => $subscribe_chk,
            'mine' => $mine,
            'total'=>$total,
        );

        $app->render('lounge/pb_portfolios.php', $param);
    });


    // 게시판
    $app->get('/:uid/contents', function($uid) use ($app, $log, $isLoggedIn) {

        $topmenu = 'lounge';
        if ($isLoggedIn()) {
            $subscribe_chk = $app->db->selectCount('subscribe', array('uid'=>$uid, 'reg_uid'=>$_SESSION['user']['uid']));
        }

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));
        $mine = ($uid == $_SESSION['user']['uid'] or $_SESSION['user']['user_type']=='A') ? true : false;

        // 컨텐츠 개수
        $total_contents = $app->db->selectCount('pb_contents', array('uid'=>$uid), array());

        $param = array(
            'topmenu'       => $topmenu,
            'menu' => 'contents',
            'pb' => $pb,
            'subscribe_cnt' => $subscribe_cnt,
            'subscribe_chk' => $subscribe_chk,
            'total_contents' => $total_contents,
            'mine' => $mine,
        );

        $app->render('lounge/pb_contents.php', $param);
    });

    // 메인 게시물 자료..
    $app->get('/:uid/load_contents', function($uid) use ($app, $log, $isLoggedIn) {

        $page = $app->request->get('page');

        $tpl = 'lounge/main_thumb.php';
        $limit = 8;
        $start = ($page - 1) * $limit;

        $end = $limit + 1;

        $lists = array();
        $sql = "SELECT a.*, b.*, (select count(*) from pb_contents_file where cidx=a.cidx) as filecnt
                FROM 
                    pb_contents a INNER JOIN user b ON a.uid=b.uid
                WHERE a.uid = $uid
                ORDER BY a.cidx DESC limit $start, $end";

        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_array()) {
            $images = $app->db->selectOne('pb_contents_file', 'save_name', array('cidx'=>$row['cidx']), array('fid'=>'asc'));
            $row['images'] = $images;
            $lists[] = $row;
        }

        $next = 0;
        if ($limit < count($lists)) {
            $next = $page + 1;
            array_pop($lists);
        }

        $app->render('lounge/pb_contents_list.php', array('lists'=>$lists, 'next'=>$next));
    });


    // 게시판 쓰기
    $app->get('/:uid/contents/write', function($uid) use ($app, $log, $isLoggedIn) {

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));

        $mine = ($uid == $_SESSION['user']['cidx'] or $_SESSION['user']['user_type']=='A') ? true : false;


        $info['images'] = array();
        $info['files'] = array();

        $param = array(
            'pb' => $pb,
            'menu' => 'contents',
            'subscribe_cnt' => $subscribe_cnt,
            'info' => $info,
            'mine' => $mine,
        );

        $app->render('lounge/pb_contents_write.php', $param);
    });

    // 게시판 쓰기
    $app->post('/:uid/contents/write', function($uid) use ($app, $log, $isLoggedIn) {

        $subject = $app->request->post('subject');
        $contents = $app->request->post('contents');
        $attach_images_filename = $app->request->post('attach_images_filename');
        $attach_images_savename = $app->request->post('attach_images_savename');
        $attach_files_filename = $app->request->post('attach_files_filename');
        $attach_files_savename = $app->request->post('attach_files_savename');

        $type = 'C';

        $param = array(
            'type' => $type,
            'uid'  => $uid,
            'subject' => $subject,
            'contents'=> $contents,
        );
        
        $cidx = $app->db->insert('pb_contents', $param);

		//구독중인 회원에게 문자 발송
        $sql = "SELECT * FROM subscribe as A, user as B WHERE A.reg_uid=B.uid and A.uid = '".$uid."' and B.uid != '".$uid."' ";
        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_array()) {
			//문자발송
			//if($row['mobile']=="01075110716"){
				$pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
				$SMSINFO['smsMsg']="[시스메틱] 구독중이신 ".$pb['name']." PB의 라운지에 새로운 글이 발행되었습니다.";
				//$SMSINFO['smsMsg']="시스메틱에서 구독한 새로운 글이 업데이트되었습니다";
				$SMSINFO['smsHp']=$row['mobile'];
				sendSMS($SMSINFO);
			//}
        }		

        if (is_array($attach_images_filename)) {
            foreach ($attach_images_filename as $k => $v) {
                $param = array(
                    'cidx'  => $cidx,
                    'file_type' => 'IMG',
                    'save_name' => $attach_images_savename[$k],
                    'file_name' => $attach_images_filename[$k],
                );

                $app->db->insert('pb_contents_file', $param);
            }
        }
        
        if (is_array($attach_files_filename)) {
            foreach ($attach_files_filename as $k => $v) {

                $param = array(
                    'cidx'  => $cidx,
                    'file_type' => 'FILE',
                    'save_name' => $attach_files_savename[$k],
                    'file_name' => $attach_files_filename[$k],
                );

                $app->db->insert('pb_contents_file', $param);
            }
        }

        $app->redirect('/lounge/'.$uid.'/contents');
    });



    // 게시판 수정
    $app->get('/:uid/contents/:cidx/modify', function($uid, $cidx) use ($app, $log, $isLoggedIn) {

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));

        $info = $app->db->selectOne('pb_contents', '*', array('cidx'=>$cidx));
        $mine = ($info['uid'] == $_SESSION['user']['uid'] or $_SESSION['user']['user_type']=='A') ? true : false;

        $images = array();
        $files = array();
        $info['images'] = $app->db->select('pb_contents_file', '*', array('cidx'=>$cidx, 'file_type'=>'IMG'));
        $info['files'] = $app->db->select('pb_contents_file', '*', array('cidx'=>$cidx, 'file_type'=>'FILE'));
        
        $param = array(
            'pb' => $pb,
            'menu' => 'contents',
            'subscribe_cnt' => $subscribe_cnt,
            'info' => $info,
            'mine' => $mine,
        );

        $app->render('lounge/pb_contents_write.php', $param);
    });

    // 게시판 수정
    $app->post('/:uid/contents/:cidx/modify', function($uid, $cidx) use ($app, $log, $isLoggedIn) {

        $subject = $app->request->post('subject');
        $contents = $app->request->post('contents');
        $attach_images_filename = $app->request->post('attach_images_filename');
        $attach_images_savename = $app->request->post('attach_images_savename');
        $attach_files_filename = $app->request->post('attach_files_filename');
        $attach_files_savename = $app->request->post('attach_files_savename');

        $type = 'C';

        $param = array(
            'type' => $type,
            'subject' => $subject,
            'contents'=> $contents,
        );
        
        //$app->db->update('pb_contents', $param, array('cidx'=>$cidx, 'uid'=>$_SESSION['user']['uid']));
		$app->db->update('pb_contents', $param, array('cidx'=>$cidx));

        $app->db->delete('pb_contents_file', array('cidx'=>$cidx, 'file_type'=>'IMG'));
        if (is_array($attach_images_filename)) {
            foreach ($attach_images_filename as $k => $v) {
                $param = array(
                    'cidx'  => $cidx,
                    'file_type' => 'IMG',
                    'save_name' => $attach_images_savename[$k],
                    'file_name' => $attach_images_filename[$k],
                );

                $app->db->insert('pb_contents_file', $param);
            }
        }
        
        $app->db->delete('pb_contents_file', array('cidx'=>$cidx, 'file_type'=>'FILE'));
        if (is_array($attach_files_filename)) {

            foreach ($attach_files_filename as $k => $v) {

                $param = array(
                    'cidx'  => $cidx,
                    'file_type' => 'FILE',
                    'save_name' => $attach_files_savename[$k],
                    'file_name' => $attach_files_filename[$k],
                );

                $app->db->insert('pb_contents_file', $param);
            }
        }

        $app->redirect('/lounge/'.$uid.'/contents/'.$cidx);
    });


    // 게시판 삭제
    $app->get('/:uid/contents/:cidx/delete', function($uid, $cidx) use ($app, $log, $isLoggedIn) {

        //$app->db->delete('pb_contents', array('cidx'=>$cidx, 'uid'=>$_SESSION['user']['uid']));
		$app->db->delete('pb_contents', array('cidx'=>$cidx));

        $savePath = $app->config('data.path').'/contents/';
        $info['images'] = $app->db->select('pb_contents_file', '*', array('cidx'=>$cidx, 'file_type'=>'IMG'));
        foreach ($info['images'] as $v) {
            @unlink($savePath.$v['save_name']); 
        }

        $info['files'] = $app->db->select('pb_contents_file', '*', array('cidx'=>$cidx, 'file_type'=>'FILE'));
        foreach ($info['files'] as $v) {
            @unlink($savePath.$v['save_name']); 
        }
        $app->db->delete('pb_contents_file', array('cidx'=>$cidx));

        $app->redirect('/lounge/'.$uid.'/contents');
    });

    // 게시판 파일
    $app->post('/contents/delete_file', function() use ($app, $log, $isLoggedIn) {

        $savePath = $app->config('data.path').'/contents/';
        @unlink($savePath.$app->request->post('savename'));

        echo "success";
    });

    // 게시판 파일
    $app->post('/contents/upload_images', function() use ($app, $log, $isLoggedIn) {

        $savePath = $app->config('data.path').'/contents/';
        $max_file_size = 1024 * 1024;

        // 업로드 된 파일이 있는지 확인
        switch($_FILES['images']['error']){
            case UPLOAD_ERR_OK:
                $filename = $_FILES['images']['name'];
                $filesize = $_FILES['images']['size'];
                $filetmpname = $_FILES['images']['tmp_name'];
                $filetype = $_FILES['images']['type'];
                $tmpfileext = explode('.', $filename);
                $fileext = $tmpfileext[count($tmpfileext)-1];


                if(strpos($filetype, 'image') === false){
                    $result['success'] = false;
                    $result['msg'] = '이미지 파일만 업로드 가능합니다';
                    echo json_encode($result);
                    $app->stop();
                }

                if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
                    $result['success'] = false;
                    $result['msg'] = '확장자가 jpg, gif, png 파일만 업로드가 가능합니다';
                    echo json_encode($result);
                    $app->stop();
                }

                if($filesize > $max_file_size){
                    $result['success'] = false;
                    $result['msg'] = '이미지파일은 1MB 이하로 업로드해주세요';
                    echo json_encode($result);
                    $app->stop();
                }

                if(!is_uploaded_file($filetmpname)){
                    $result['success'] = false;
                    $result['msg'] = '정상적인 방법으로 업로드해주세요';
                    echo json_encode($result);
                    $app->stop();
                }

                if(!is_dir($savePath)){
                    mkdir($savePath, 0705);
                    chmod($savePath, 0707);
                }

                $saveFilename = md5(uniqid(rand(), true));
                while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
                    $saveFilename = md5(uniqid(rand(), true));
                }

                $finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
                if(!move_uploaded_file($filetmpname, $finalFilename)){
                    $result['success'] = false;
                    $result['msg'] = '업로드에 실패하였습니다';
                    echo json_encode($result);
                    $app->stop();
                }
                
                $result['success'] = true;
                $result['filename'] = $filename;
                $result['savename'] = $saveFilename.'.'.$fileext;
                $result['filesize'] = $filesize;

		        echo json_encode($result);
                $app->stop();
                break;

            case UPLOAD_ERR_INI_SIZE:
                $result['success'] = false;
                $result['msg'] = '업로드 가능 용량을 초과하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $result['success'] = false;
                $result['msg'] = '업로드 가능 용량을 초과하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_PARTIAL:
                $result['success'] = false;
                $result['msg'] = '업로드에 실패하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_NO_FILE:
                $result['success'] = false;
                $result['msg'] = '첨부된 파일이 없습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            default:
                $result['success'] = false;
                $result['msg'] = '업로드에 실패하였습니다';
		        echo json_encode($result);
                $app->stop();
        }

    });

    // 게시판 파일
    $app->post('/contents/upload_files', function() use ($app, $log, $isLoggedIn) {

        $savePath = $app->config('data.path').'/contents/';
        $max_file_size = 1024 * 1024 * 5;

        // 업로드 된 파일이 있는지 확인
        switch($_FILES['files']['error']){
            case UPLOAD_ERR_OK:
                $filename = $_FILES['files']['name'];
                $filesize = $_FILES['files']['size'];
                $filetmpname = $_FILES['files']['tmp_name'];
                $filetype = $_FILES['files']['type'];
                $tmpfileext = explode('.', $filename);
                $fileext = $tmpfileext[count($tmpfileext)-1];

                if($filesize > $max_file_size){
                    $result['success'] = false;
                    $result['msg'] = '파일은 5MB 이하로 업로드해주세요';
                    echo json_encode($result);
                    $app->stop();
                }

                if(!is_uploaded_file($filetmpname)){
                    $result['success'] = false;
                    $result['msg'] = '정상적인 방법으로 업로드해주세요';
                    echo json_encode($result);
                    $app->stop();
                }


                if(!is_dir($savePath)){
                    mkdir($savePath, 0705);
                    chmod($savePath, 0707);
                }

                $saveFilename = md5(uniqid(rand(), true));
                while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
                    $saveFilename = md5(uniqid(rand(), true));
                }

                $finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
                if(!move_uploaded_file($filetmpname, $finalFilename)){
                    $result['success'] = false;
                    $result['msg'] = '업로드에 실패하였습니다';
                    echo json_encode($result);
                    $app->stop();
                }
                
                $result['success'] = true;
                $result['filename'] = $filename;
                $result['savename'] = $saveFilename.'.'.$fileext;
                $result['filesize'] = $filesize;

		        echo json_encode($result);
                $app->stop();
                break;

            case UPLOAD_ERR_INI_SIZE:
                $result['success'] = false;
                $result['msg'] = '업로드 가능 용량을 초과하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $result['success'] = false;
                $result['msg'] = '업로드 가능 용량을 초과하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_PARTIAL:
                $result['success'] = false;
                $result['msg'] = '업로드에 실패하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_NO_FILE:
                $result['success'] = false;
                $result['msg'] = '첨부된 파일이 없습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            default:
                $result['success'] = false;
                $result['msg'] = '업로드에 실패하였습니다';
		        echo json_encode($result);
                $app->stop();
        }

    });

    // 게시판 내용
    $app->get('/:uid/contents/:cidx', function($uid, $cidx) use ($app, $log, $isLoggedIn) {

        
        if ($isLoggedIn()) {
            $subscribe_chk = $app->db->selectCount('subscribe', array('uid'=>$uid, 'reg_uid'=>$_SESSION['user']['uid']));
        }

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));

        $info = $app->db->selectOne('pb_contents', '*', array('cidx'=>$cidx));
        $mine = ($info['uid'] == $_SESSION['user']['uid'] or $_SESSION['user']['user_type']=='A') ? true : false;

        $images = array();
        $files = array();
        $info['images'] = $app->db->select('pb_contents_file', '*', array('cidx'=>$cidx, 'file_type'=>'IMG'));
        $info['files'] = $app->db->select('pb_contents_file', '*', array('cidx'=>$cidx, 'file_type'=>'FILE'));
        
        $param = array(
            'pb' => $pb,
            'menu' => 'contents',
            'subscribe_cnt' => $subscribe_cnt,
            'subscribe_chk' => $subscribe_chk,
            'info' => $info,
            'mine' => $mine,
			'vuid' => $uid, 
			'vcidx' => $cidx,
        );

        $app->render('lounge/pb_contents_detail.php', $param);
    });


    // 게시판 코멘트
    $app->get('/:uid/contents/:cidx/reply', function($uid, $cidx) use ($app, $log, $isLoggedIn) {

        $count = $app->db->selectCount('pb_contents_comment', array('cidx'=>$cidx));

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));

        $param = array(
            'pb' => $pb,
            'cidx' => $cidx,
            'count' => $count,
        );

        $app->render('lounge/pb_contents_comment.php', $param);
    });

    // 게시판 코멘트
    $app->post('/:uid/contents/:cidx/reply/write', function($uid, $cidx) use ($app, $log, $isLoggedIn) {

        $data = array(
                    'cidx' => $cidx,
                    'uid'  => $_SESSION['user']['uid'],
                    'contents' => $app->request->post('contents'),
                    'secret' => $app->request->post('secret') ? $app->request->post('secret') : '0',
                );

        if ($app->db->insert('pb_contents_comment', $data)) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['msg'] = '처리중 오류가 발생하였습니다';
        }

		echo json_encode($result);
    });

    // 게시판 코멘트
    $app->post('/:uid/contents/:cidx/reply/modify', function($uid, $cidx) use ($app, $log, $isLoggedIn) {

        $data = array(
                    'contents' => $app->request->post('contents'),
                );

        if ($app->db->update('pb_contents_comment', $data, array('cid'=>$app->request->post('cid'), 'uid'=>$_SESSION['user']['uid']))) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['msg'] = '처리중 오류가 발생하였습니다';
        }

		echo json_encode($result);
    });

    // 게시판 코멘트
    $app->post('/:uid/contents/:cidx/reply/delete', function($uid, $cidx) use ($app, $log, $isLoggedIn) {

        if ($app->db->delete('pb_contents_comment', array('cid'=>$app->request->post('cid'), 'uid'=>$_SESSION['user']['uid']))) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['msg'] = '처리중 오류가 발생하였습니다';
        }

		echo json_encode($result);
    });

    // 게시판 코멘트
    $app->get('/:uid/contents/:cidx/reply_list', function($uid, $cidx) use ($app, $log, $isLoggedIn) {

        $page = $app->request->get('page');
        $page = ($page) ? $page : 1;
        $limit = 10;
        $start = ($page - 1) * $limit;

        $sql = "SELECT a.*, b.nickname, b.picture, b.user_type
                FROM pb_contents_comment a INNER JOIN user b ON a.uid = b.uid
                WHERE a.cidx = '$cidx'
                ORDER BY cid DESC
                LIMIT $start, $limit";

        $result = $app->db->conn->query($sql);

        $lists = array();
        while ($row = $result->fetch_array()) {
            $lists[] = $row;
        }

        $param = array(
            'lists' => $lists,
        );

        $app->render('lounge/pb_contents_comment_list.php', $param);
    });

    // 다운로드
    $app->get('/:uid/contents/download/:fid', function ($uid, $fid) use ($app, $log, $isLoggedIn) {

        $files = $app->db->selectOne('pb_contents_file', '*', array('fid'=>$fid));

        if(empty($files)){
            $app->halt(404, 'not found');
        }


        $savePath = $app->config('data.path').'/contents/';
        $filepath = $savePath.'/'.$files['save_name'];
        $filesize = filesize($filepath);
        $filename = urlencode($files['file_name']);

        $app->response->headers->set('Content-Type', 'application/octet-stream');
        $app->response->headers->set('Pragma', 'dummy=bogus');
        $app->response->headers->set('Cache-Control', 'private');
        $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $app->response->headers->set('Content-Transfer-Encoding', 'binary');
        $app->response->headers->set('Content-Length', $filesize);

        readfile($filepath);
    });

    // 상담완료
    $app->get('/:uid/counsel/complete', function($uid) use ($app, $log) {

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));
        $subscribe_chk = $app->db->selectCount('subscribe', array('uid'=>$uid, 'reg_uid'=>$_SESSION['user']['uid']));

        $param = array(
            'pb' => $pb,
            'menu' => 'contents',
            'subscribe_cnt' => $subscribe_cnt,
            'subscribe_chk' => $subscribe_chk,
        );

        $app->render('lounge/pb_counsel_complete.php', $param);
    });

    // 상담하기
    $app->get('/:uid/counsel/:req_type', function($uid, $req_type) use ($app, $log, $isLoggedIn) {

        if (!$isLoggedIn()) {
            $app->redirect('/signin?redirect_url='.urlencode("/lounge/$uid/counsel/$req_type"));
            $app->stop();
        }

        if ($_SESSION['user']['user_type'] == 'P') {
            alert('PB 회원은 사용이 불가능합니다.');
            $app->stop();
        }
		/*
        if ($_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'P') {
            alert('트레이더, PB 회원은 등록이 불가능합니다.');
            $app->stop();
        }
		*/

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));
        $subscribe_chk = $app->db->selectCount('subscribe', array('uid'=>$uid, 'reg_uid'=>$_SESSION['user']['uid']));

        $param = array(
            'pb' => $pb,
            'menu' => 'contents',
            'subscribe_cnt' => $subscribe_cnt,
            'subscribe_chk' => $subscribe_chk,
            'req_type' => $req_type
        );

        $app->render('lounge/pb_counsel.php', $param);

    });

    // 상담하기
    $app->post('/:uid/counsel/:req_type', function($pid, $req_type) use ($app, $log, $isLoggedIn) {

        if (!$isLoggedIn()) {
            $app->redirect('/signin?redirect_url='.urlencode("/lounge/$uid/counsel/$req_type"));
            $app->stop();
        }

        $mobile = $app->request->post('mobile');
        $subject = $app->request->post('subject');
        $strategy = $app->request->post('strategy');
        $s_price = $app->request->post('s_price');
        $s_date = $app->request->post('s_date');
        $contents = $app->request->post('contents');
        $hope_date = $app->request->post('hope_date').' '.$app->request->post('hour').'시'.$app->request->post('min').'분';

		$pb = $app->db->selectOne('user', '*', array('uid'=>$pid));
		$SMSINFO['smsMsg']="시스메틱에서 상담글이 등록되었습니다.";
		$SMSINFO['smsHp']=$pb['mobile'];
		sendSMS($SMSINFO);

        $param = array(
            'pid'   => $pid,
            'uid'   => $_SESSION['user']['uid'],
            'req_type'  => $req_type,
            'mobile'    => $mobile ? $mobile : '',
            'subject'   => $subject,
            'strategy'  => $strategy,
            's_price'   => $s_price,
            's_date'    => $s_date,
            'contents'  => $contents,
            'hope_date' => $hope_date ? $hope_date : ''
        );
        
        if (!$app->db->insert('pb_request', $param)) {
            $app->redirect("/lounge/$pid/counsel/$req_type");
        } else {
            $app->redirect("/lounge/$pid/counsel/complete");
        }
    });


    // 구독하기
    $app->get('/subscribe/reg', function() use ($app, $log) {
        
        $param = array(
            'uid' => $app->request->get('uid'),
            'reg_uid' => $_SESSION['user']['uid'],
        );

        $chk = $app->db->selectCount('subscribe', $param);
        if ($chk) {
            $result['result'] = false;
            $result['msg'] = '이미 구독중입니다.';
        } else {
            if ($app->db->insert('subscribe', $param)) {
                $result['result'] = true;
            } else {
                $result['result'] = false;
                $result['msg'] = '처리중 오류가 발생하였습니다';
            }
        }

		echo json_encode($result);
    });

    // 구독취소
    $app->get('/subscribe/del', function() use ($app, $log) {
        
        $param = array(
            'uid' => $app->request->get('uid'),
            'reg_uid' => $_SESSION['user']['uid'],
        );

        if ($app->db->delete('subscribe', $param)) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['msg'] = '처리중 오류가 발생하였습니다';
        }

		echo json_encode($result);
    });


    // pb평가
    $app->get('/:uid/appraise', function($uid) use ($app, $log, $isLoggedIn) {
        
        $topmenu = 'lounge';

		if(!$_SESSION['user']['uid']){
			alert('로그인이 필요합니다.','/signin');
			$app->stop();
		}

        $pb = $app->db->selectOne('user', '*', array('uid'=>$uid));
        $n = $app->db->selectOne('pb_evaluation', 'AVG(num) avg', array('pid'=>$uid));
        $pb['num'] = ($n['avg']) ? round($n['avg']) : 0;
        $subscribe_cnt = $app->db->selectCount('subscribe', array('uid'=>$uid));
        $subscribe_chk = $app->db->selectCount('subscribe', array('uid'=>$uid, 'reg_uid'=>$_SESSION['user']['uid']));
        $mine = ($uid == $_SESSION['user']['uid']) ? true : false;

        $total = $app->db->selectCount('pb_evaluation', array('pid'=>$uid));

        $param = array(
            'topmenu'   => $topmenu, 
            'pb' => $pb,
            'subscribe_cnt' => $subscribe_cnt,
            'subscribe_chk' => $subscribe_chk,
            'menu' => 'appraise',
            'mine' => $mine,
            'total' => $total,
        );

        $app->render('lounge/pb_appraise.php', $param);
    });


    // pb평가
    $app->post('/:uid/appraise', function($uid) use ($app, $log, $isLoggedIn) {
        
        if ($_SESSION['user']['user_type'] == 'P') {

            $result['result'] = false;
            $result['msg'] = 'PB 회원은 등록이 불가능합니다.';
		    echo json_encode($result);
            $app->stop();
        }

        $num = $app->request->post('num');
        $contents = strip_tags($app->request->post('contents'));
        
        $param = array(
            'num'   => $num,
            'contents' => $contents,
            'pid'      => $uid,
            'uid'      => $_SESSION['user']['uid'],
        );

        $chk = $app->db->selectCount('pb_evaluation', array('pid'=>$uid, 'uid'=>$_SESSION['user']['uid']));
        if ($chk) {
            $result['result'] = false;
            $result['msg'] = '이미 평가에 참여하였습니다';
		    echo json_encode($result);
            $app->stop();
        }

        $idx = $app->db->insert('pb_evaluation', $param);
        
        if ($idx) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['msg'] = '처리중 오류가 발생하였습니다';
        }

		echo json_encode($result);
    });


    // pb평가
    $app->get('/:uid/appraise/delete', function($uid) use ($app, $log, $isLoggedIn) {
        
        $param = array(
            'pid'      => $uid,
            'uid'      => $_SESSION['user']['uid'],
        );

        $idx = $app->db->delete('pb_evaluation', $param);
        
        if ($idx) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['msg'] = '처리중 오류가 발생하였습니다';
        }

		echo json_encode($result);
    });

    // pb평가
    $app->get('/:uid/appraise/list', function($uid) use ($app, $log, $isLoggedIn) {

        $page = $app->request->get('page');
        $page = ($page) ? $page : 1;
        $limit = 10;
        $start = ($page - 1) * $limit;

        $sql = "SELECT a.*, b.*
                FROM pb_evaluation a INNER JOIN user b ON a.uid = b.uid
                WHERE a.pid = $uid
                ORDER BY a.reg_date DESC
                LIMIT $start, $limit";
        
        $result = $app->db->conn->query($sql);
        $lists = array();
        while ($row = $result->fetch_array()) {
            $lists[] = $row;
        }

        $param = array(
            'lists'   => $lists,
        );

        $app->render('lounge/pb_appraise_list.php', $param);
    });


});
