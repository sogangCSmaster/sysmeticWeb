<?php
/**
 * 마이페이지 관련 라우터
 */

$app->group('/mypage', function() use ($app, $log) {
/*
    $app->get('/', function() use ($app, $log) {

        $param = array();

        $app->render('mypage/mypage.php', $param);
    });
*/

    $app->get('/modify', function() use ($app, $log) {
    
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());
        $brokers = $app->db->select('broker', 'broker_id, company', array(), array('company'=>'asc'));

        $param = array(
            'myInfo'    => $myInfo,
            'brokers'   => $brokers,

        );

        $app->render('mypage/info_modify.php', $param);
    });

    $app->post('/modify', function() use ($app, $log) {

        $pre_password = $app->request->post('pre_password');
        $password = $app->request->post('password');
        $password_confirm = $app->request->post('password_confirm');
        $mobile = $app->request->post('mobile');
        $birthday = $app->request->post('birthday');
        $sido = $app->request->post('sido');
        $gugun = $app->request->post('gugun');
        $gender = $app->request->post('gender');
        $alarm_feeds = $app->request->post('alarm_feeds');
        $alarm_all = $app->request->post('alarm_all');
        $broker_id = $app->request->post('broker_id');
        $sido2 = $app->request->post('sido2');
        $gugun2 = $app->request->post('gugun2');
        $part = $app->request->post('part');
        $addr = $app->request->post('addr');
        $pre_profile = $app->request->post('pre_profile');
        $pre_profile_s = $app->request->post('pre_profile_s');
        $pre_namecard = $app->request->post('pre_namecard');
        $pre_namecard_s = $app->request->post('pre_namecard_s');

        if ($broker_id) {
            $broker = $app->db->selectOne('broker', 'company', array('broker_id' => $broker_id));
            $company = $broker['company'];
        }

        $broker_id = empty($broker_id) ? '' : $broker_id;
        $company = empty($company) ? '' : $company;
        $sido2 = empty($sido2) ? '' : $sido2;
        $gugun2 = empty($gugun2) ? '' : $gugun2;
        $part = empty($part) ? '' : $part;
        $addr = empty($part) ? '' : $addr;
        $pre_profile = empty($pre_profile) ? '' : $pre_profile;
        $pre_profile_s = empty($pre_profile_s) ? '' : $pre_profile_s;
        $pre_namecard = empty($pre_namecard) ? '' : $pre_namecard;
        $pre_namecard_s = empty($pre_namecard_s) ? '' : $pre_namecard_s;

        // 프로필 이미지
        $profile_url = empty($_SESSION['temp_profile_url']) ? $pre_profile : $_SESSION['temp_profile_url'];
        $profile_s_url = empty($_SESSION['temp_profile_s_url']) ? $pre_profile_s : $_SESSION['temp_profile_s_url'];

        // 네임카드 이미지
        $namecard_url = empty($_SESSION['temp_namecard_url']) ? $pre_namecard : $_SESSION['temp_namecard_url'];
        $namecard_s_url = empty($_SESSION['temp_namecard_s_url']) ? $pre_namecard_s : $_SESSION['temp_namecard_s_url'];

        if(!validate_password($pre_password, $_SESSION['user']['user_password'])){
            $app->flash('error', '비밀번호가 일치하지 않습니다.');
            $app->redirect('/mypage/modify');
        }
        
        if(!empty($password) && !empty($password_confirm)){
            if($password != $password_confirm){
                $app->flash('error', '비밀번호가 일치하지 않습니다.');
                $app->redirect('/mypage/modify');
            }

            if(strlen($password) < 6 || strlen($password) >= 20){
                $app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
                $app->redirect('/mypage/modify');
            }

            if(preg_match('/^(?=.*\d)(?=.*[a-zA-Z]).{6,19}$/', $password)){

            }else{
                $app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
                $app->redirect('/mypage/modify');
            }

            $password_hash = create_hash($password);
        }

        if(!empty($mobile)){
            if(preg_match('/^[0-9]{10,11}$/', $mobile)){
            }else{
                $app->flash('error', '정확한 휴대폰 번호를 확인해 주세요.');
                $app->redirect('/mypage/modify');
            }
        }else{
            $mobile = '';
        }

        if(!empty($birthday)){
            if(preg_match('/^[0-9]{8}$/', $birthday)){
            }else{
                $app->flash('error', '생년월일이 올바르지 않습니다.');
                $app->redirect('/mypage/modify');
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

        $param = array(
            'mobile'=>$mobile,
            'birthday'=>$birthday,
            'sido'=>$sido,
            'gugun'=>$gugun,
            'gender'=>$gender,
            'broker_id'=>$broker_id,
            'company'=>$company,
            'picture'=>$profile_url,
            'picture_s'=>$profile_s_url,
            'sido2'=>$sido2,
            'gugun2'=>$gugun2,
            'part'=>$part,
            'addr'=>$addr,
            'namecard'=>$namecard_url,
            'namecard_s'=>$namecard_s_url,
            'alarm_feeds'=>$alarm_feeds ? '1' : '0',
            'alarm_all'=>$alarm_all ? '1' : '0'
        );

        if ($password_hash) {
            $param['user_password'] = $password_hash;
        }

        $app->db->update('user', $param, array('uid'=>$_SESSION['user']['uid']));

        $_SESSION['temp_profile_url'] = '';
        $_SESSION['temp_profile_s_url'] = '';
        $_SESSION['temp_namecard_url'] = '';
        $_SESSION['temp_namecard_s_url'] = '';

        $app->flash('error', '수정되었습니다.');
        $app->redirect('/mypage/modify');
    });

    $app->get('/withdraw', function() use ($app, $log) {
    
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $param = array(
            'myInfo'    => $myInfo,
        );

        $app->render('mypage/withdraw.php', $param);
    });

    $app->post('/withdraw', function() use ($app, $log) {

        $uid = $_SESSION['user']['uid'];
		$app->db->conn->query("UPDATE user SET is_delete = '1', delete_at = now() WHERE uid = $uid");

        // 상품
		$app->db->conn->query("UPDATE strategy SET is_delete = '1' WHERE developer_uid = $uid");
        $app->db->conn->query("DELETE FROM strategy_invest WHERE uid= $uid");
		$app->db->conn->query("DELETE FROM strategy_review WHERE writer_uid = $uid");

        // 포트폴리오
		$app->db->conn->query("DELETE FROM portfolio WHERE uid = $uid");
		$app->db->conn->query("DELETE FROM portfolio_review WHERE writer_uid = $uid");
        // 1:1 문의
        $app->db->conn->query("DELETE FROM customer WHERE uid= $uid");
        // 팔로우
        $app->db->conn->query("DELETE FROM following_group WHERE uid= $uid");
        $app->db->conn->query("DELETE FROM following_portfolio WHERE uid= $uid");
        $app->db->conn->query("DELETE FROM following_strategy WHERE uid= $uid");
        // pb게시판
        $app->db->conn->query("DELETE FROM pb_board WHERE uid= $uid");
        $app->db->conn->query("DELETE FROM pb_board_comment WHERE uid= $uid");
        // pb컬럼, 공지
        $app->db->conn->query("DELETE FROM pb_contents WHERE uid= $uid");
        $app->db->conn->query("DELETE FROM pb_contents_comment WHERE uid= $uid");
        // pb평가 (pb이거나 등록자)
        $app->db->conn->query("DELETE FROM pb_evaluation WHERE uid= $uid OR pid=$uid");
        // pb 프로필
        $app->db->conn->query("DELETE FROM pb_profile WHERE uid= $uid");
        // pb문의
        $app->db->conn->query("DELETE FROM pb_request WHERE uid= $uid OR pid=$uid");
        // qna
        $app->db->conn->query("DELETE FROM qna WHERE uid= $uid");
        // request_broker
        $app->db->conn->query("DELETE FROM request_broker WHERE uid= $uid");


        session_unset();
        session_destroy();
        $app->redirect('/');
    });

    $app->get('/subscribe', function() use ($app, $log) {
        $submenu = 'subscribe';
        
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $tmp = $app->db->select('subscribe', '*', array('reg_uid' => $_SESSION['user']['uid']));
        $pbCnt = count($tmp);
        
        $pbList = array();
        $uids = array();
        foreach ($tmp as $k => $v) {
            $pbList[$v['uid']] = $v;
            $uids[] = $v['uid'];
        }

        $uids = "'".implode("','", $uids)."'";
        $sql = "SELECT COUNT(*) FROM pb_contents WHERE uid IN ($uids) "; //AND reg_date >= date_add(now(), interval -7 day)
        $result = $app->db->conn->query($sql);
        //echo $sql;
        $row = $result->fetch_array();
        $newCnt = $row[0];

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'pbCnt'     => $pbCnt,
            'newCnt'    => $newCnt,
        );

        $app->render('mypage/subscribe_list.php', $param);
    });
    
    

    $app->get('/subscribe/list', function() use ($app, $log) {

        $count = (!$app->request->get('count')) ? 4 : $app->request->get('count');
        $page = $app->request->get('page');
        if (empty($page) || !is_numeric($page)) $page = 1;
        $start = ($page - 1) * $count;

        $tmp = $app->db->select('subscribe', '*', array('reg_uid' => $_SESSION['user']['uid']));
        $uids = array();
        foreach ($tmp as $k => $v) {
            $uids[] = $v['uid'];
        }

        $uids = "'".implode("','", $uids)."'";
        $sql = "SELECT a.*, b.* FROM pb_contents a INNER JOIN user b ON a.uid=b.uid WHERE a.uid IN ($uids) ORDER BY a.reg_date DESC LIMIT $start, $count"; //AND a.reg_date >= date_add(now(), interval -7 day)

        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $images = $app->db->selectOne('pb_contents_file', 'save_name', array('cidx'=>$row['cidx']), array('fid'=>'asc'));
            $row['images'] = $images;
            $lists[] = $row;
        }

        $param = array(
            'lists'    => $lists,
            'more'      => (count($lists) < $count) ? false : true,
        );

        $app->render('mypage/subscribe_data.php', $param);
    });
    

    $app->get('/favorite', function() use ($app, $log) {
        $submenu = 'favorite';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        // 그룹목록
        $groups = $app->db->select('following_group', '*', array('uid'=>$_SESSION['user']['uid']), array());
        if (!is_array($groups) || !count($groups)) {
            // 그룹생성
            $group_name = '기본그룹';
            $values = array('uid' => $_SESSION['user']['uid'], 'group_name' => $group_name);
            $group_id = $app->db->insert("following_group", $values);

            $groups = $app->db->select('following_group', '*', array('uid'=>$_SESSION['user']['uid']), array());
        }

        $group_id = $app->request->get('group_id') ? $app->request->get('group_id') : $groups[0]['group_id'];

        // 전략목록
        $s_list = $app->db->select('following_strategy', 'strategy_id', array('uid'=>$_SESSION['user']['uid'], 'group_id' => $group_id), array());
        foreach ($s_list as $v) {
            $strategies .= $v['strategy_id'].'|';
        }

        // 퐅폴리오목록
        $p_list = $app->db->select('following_portfolio', 'portfolio_id', array('uid'=>$_SESSION['user']['uid'], 'group_id' => $group_id), array());
        foreach ($p_list as $v) {
            $portfolios .= $v['portfolio_id'].'|';
        }

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'groups'    => $groups,
            'group_id' => $group_id,
            'strategy_cnt' => count($s_list),
            'strategies'=> $strategies,
            'portfolio_cnt'=> count($p_list),
            'portfolios'=> $portfolios,
        );

        $app->render('mypage/favorite_list.php', $param);
    });



    $app->post('/follow/group/edit', function() use($app, $log) {

        $group_id = $app->request->post('group_id');
        $group_name = $app->request->post('group_name');

        if ($app->db->selectCount('following_group',  array('uid' => $_SESSION['user']['uid'], 'group_name' => $group_name)) > 0){
            echo json_encode(array('result'=>false, 'msg'=>'이미 사용중인 폴더명입니다'));
            $app->stop();
        }

        $app->db->update("following_group", array('group_name' => $group_name), array('uid' => $_SESSION['user']['uid'], 'group_id' => $group_id));
        echo json_encode(array('result'=>true));
        $app->stop();
    
    });


    $app->post('/follow/group/delete', function() use ($app, $log) {

        $group_id = $app->request->post('group_id');
        $app->db->delete("following_group", array('uid' => $_SESSION['user']['uid'], 'group_id' => $group_id));
        $app->db->delete("following_strategy", array('uid'=>$_SESSION['user']['uid'], 'group_id' => $group_id));
        $app->db->delete("following_portfolio", array('uid'=>$_SESSION['user']['uid'], 'group_id' => $group_id));
        echo json_encode(array('result'=>true));
        $app->stop();

    });


    $app->get('/strategies', function() use ($app, $log) {
        $submenu = 'strategy';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());
        
        $uid = $_SESSION['user']['uid'];
        $sql = "SELECT COUNT(*) FROM strategy WHERE is_delete = '0' AND (developer_uid = '$uid' OR trader_uid = '$uid' OR pb_uid = '$uid')";
        $result = $app->db->conn->query($sql);
        $row = $result->fetch_array();
        $cnt = $row[0];

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'cnt'       => $cnt,
        );
        $app->render('mypage/strategy_list.php', $param);
    });


    $app->post('/strategies/:id/update', function($id) use ($app, $log) {

        $name = $app->request->post('name');
        $broker_id = $app->request->post('broker_id');
        $input_item_ids = $app->request->post('item_ids');
        $tool_id = $app->request->post('tool_id');
        $currency = $app->request->post('currency');
        $term = $app->request->post('term');
        $intro = $app->request->post('intro');
        $strategy_type = $app->request->post('strategy_type');
        $strategy_kind = $app->request->post('strategy_kind');
        $min_price = $app->request->post('min_price');
        $investment = $app->request->post('investment');
        $attached_file_del = $app->request->post('attached_file_del');
        $save_name = $app->request->post('save_name');
        $trader_uid = $app->request->post('trader_uid');
        $pb_uid = $app->request->post('pb_uid');

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=> $id));

        if (($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])) {
            alert('잘못된 접근입니다');
            $app->stop();
        }

        $brokers = $app->db->selectOne('broker', 'company_type', array('broker_id'=> $broker_id));
        $broker_type = $brokers['company_type'];

        if(empty($currency)){
            $currency = 'KRW';
        }

        if(empty($investment)){
            $investment = 0;
        }else{
            $investment = intval(str_replace(',', '', $investment));
        }

        if(empty($term)){
            $term = 'day';
        }

        if(empty($intro)){
            $intro = '';
        }

        if(empty($min_price)){
            $min_price = 0;
        }

        // 업로드 된 파일이 있는지 확인
        $savePath = $app->config('strategy.path');
        if ($_FILES['attach_file']['name'] && is_uploaded_file($_FILES['attach_file']['tmp_name'])) {

            $filename = $_FILES['attach_file']['name'];
            $filesize = $_FILES['attach_file']['size'];
            $filetmpname = $_FILES['attach_file']['tmp_name'];
            $filetype = $_FILES['attach_file']['type'];
            $tmpfileext = explode('.', $filename);
            $fileext = $tmpfileext[count($tmpfileext)-1];

            // filename modify
            $saveFilename = md5(uniqid(rand(), true)).'.'.$fileext;

            // filename same check
            while(file_exists($savePath.'/'.$saveFilename)){
                $saveFilename = md5(uniqid(rand(), true));
            }

            $finalFilename = $savePath.'/'.$saveFilename;

            if(!move_uploaded_file($filetmpname, $finalFilename)) {
                alert('업로드에 실패하였습니다');
                $app->stop();
            }

            $isUpload = true;
        }

        $param = array (
            'name'=>$name,
            'broker_type'=>$broker_type,
            'broker_id'=>$broker_id,
            'tool_id'=>$tool_id,
            'currency'=>$currency,
            'investment'=>$investment,
            'strategy_type'=>$strategy_type,
            'strategy_kind'=>$strategy_kind,
            'strategy_term'=>$term,
            'intro'=>$intro,
            'min_price'=>$min_price,
            'mod_at'=>date('Y-m-d H:i:s')
        );

        if (is_numeric($pb_uid)) {
            $param['pb_uid'] = $pb_uid;
        }
        if (is_numeric($trader_uid)) {
            $param['trader_uid'] = $trader_uid;
            $param['developer_uid'] = $trader_uid;
        }


        $app->db->update('strategy', $param, array('strategy_id'=>$strategy['strategy_id']));

		// 종목을 지운뒤 재등록
		$app->db->delete('strategy_item', array('strategy_id'=>$strategy['strategy_id']));
		foreach($input_item_ids as $v){
			if(empty($v)) continue;
			$app->db->insert('strategy_item', array(
				'strategy_id'=>$strategy['strategy_id'],
				'item_id'=>$v
			));
		}

        if ($isUpload) {
            if ($save_name) {
                $app->db->delete('strategy_file', array('strategy_id'=>$strategy['strategy_id']));
                unlink($savePath.'/'.$save_name);
            }

            $app->db->insert('strategy_file', array(
                'strategy_id'=>$strategy['strategy_id'],
                'file_name'=>$filename,
                'save_name'=>$saveFilename)
            );
        } else {
            if ($attached_file_del) {
                $app->db->delete('strategy_file', array('strategy_id'=>$strategy['strategy_id']));
                unlink($savePath.'/'.$save_name);
            }
        }

        $app->flash('error', '수정되었습니다.');
        $app->redirect('/mypage/strategies/'.$strategy['strategy_id'].'/basic');

    });


    $app->post('/strategies/:id/set', function($id) use ($app, $log) {

        $is_open = $app->request->post('is_open');
        $is_fund = $app->request->post('is_fund');

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=> $id));
        
        if (($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])) {
//        if (!$strategy) {
			$result['result'] = false;
            $result['msg'] = '잘못된 요청입니다.';
        } else {
            $cnt = $app->db->selectCount('strategy_daily_analysis', array('strategy_id'=> $strategy['strategy_id']));
            if ($is_open && $cnt < 3) {
                $result['result'] = false;
                $result['msg'] = '3일 이상의 일간분석 내용이 있어야 공개로 변경 가능합니다.';
            } else {
                $app->db->update('strategy',  array('is_open'=>$is_open, 'is_fund'=>$is_fund), array('strategy_id'=> $strategy['strategy_id']));
                $result['result'] = true;
            }
        }
		
        echo json_encode($result);

    });

    $app->get('/strategies/:id/basic', function($id) use ($app, $log) {
        $submenu = 'strategy';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=> $id));

        switch ($_SESSION['user']['user_type']) {
            case 'T':
                $brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
                $tools_id = $app->db->select('system_trading_tool', 'tool_id, name', array('broker_id'=>$strategy['broker_id']));

                break;
            case 'P';
                $brokers = $app->db->selectOne('broker', '*', array('broker_id'=>$_SESSION['user']['broker_id']));
                $tools_id = $app->db->select('system_trading_tool', 'tool_id, name', array('broker_id'=>$_SESSION['user']['broker_id']));
                break;
            case 'A';
                $brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
                $tools_id = $app->db->select('system_trading_tool', 'tool_id, name', array('broker_id'=>$strategy['broker_id']));

                break;
            default: $template = '';
        }

        $items = $app->db->select('item', '*', array(), array('sorting'=>'asc'));
        $kinds = $app->db->select('kind', '*', array(), array('sorting'=>'asc'));

        $fund_price = $app->config('strategy.min_price');

		if ($_SESSION['user']['user_type'] == 'A'){
		} else if (
            $strategy['developer_uid'] == $_SESSION['user']['uid']
            || $strategy['trader_uid'] == $_SESSION['user']['uid']
            || $strategy['pb_uid'] == $_SESSION['user']['uid']
            ) {
		} else {
			$app->halt(403, 'forbidden');
		}

        if ($strategy['broker_id']) {
            // pb
            $pb = $app->db->select('user', 'uid, name, nickname', array('user_type'=>'P', 'is_request_pb'=>'0', 'is_delete'=>'0', 'broker_id'=>$strategy['broker_id']));
        }

        if ($strategy['trader_uid']) {
            // trader
            $trader = $app->db->selectOne('user', 'uid, name,nickname, picture', array('is_delete'=>'0', 'uid'=>$strategy['trader_uid']));
        }

		// 등록자
		$developer = $app->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']));
		$strategy['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

		// 저장된 종목
		$real_items = array();
		$stored_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$id));
		foreach($stored_items as $v){
			$real_items[] = $v['item_id'];
		}
		$strategy['items'] = $real_items;

        // 저장된 파일
        $files = $app->db->selectOne('strategy_file', '*', array('strategy_id'=>$id));
        $strategy['file'] = $files;

        $types = $app->db->select('type', '*', array(), array('sorting'=>'asc'));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'strategy'  => $strategy,
            'brokers'   => $brokers,
            'tools_id'  => $tools_id,
            'kinds'     => $kinds,
            'items'     => $items,
            'fund_price'=> $fund_price,
            'pb'        => $pb,
            'trader'    => $trader,
            'types'     => $types
        );

        $app->render('mypage/strategy_basic.php', $param);
    });

    $app->get('/strategies/:id/analysis', function($id) use ($app, $log) {
        $submenu = 'strategy';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=> $id));

        if (($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])) {
            alert('잘못된 요청입니다');
            $app->stop();
        }

		$total = $app->db->selectCount('strategy_daily_analysis', array('strategy_id'=>$id));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'strategy'  => $strategy,
            'total'     => $total,
        );

        $app->render('mypage/strategy_analysis.php', $param);
    });

    $app->get('/strategies/:id/analysis/list', function($id) use ($app, $log) {

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 10;
		$start = ($page - 1) * $count;

		$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$id), array('basedate'=>'desc'), $start, $count);

        $param = array(
            'daily_values'  => $daily_values,
        );

        $app->render('mypage/strategy_analysis_list.php', $param);
    });

	$app->post('/strategies/:id/analysis/add', function($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){$app->halt(404, 'not found');}

		//if($_SESSION['user']['user_type'] == 'A'){
		//}else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
		//}else{$app->halt(403, 'forbidden');}

		$nInsert = 0;
		$sMinStartDate = '';
		$sMaxEndDate = '';
		$aBaseDate = $app->request->post('basedate');
		$aFlow = $app->request->post('flow');
		$aPL = $app->request->post('PL');
		foreach((array)$aBaseDate as $key => $sBaseDate) {

			// 기준일자
			if($sBaseDate != '') {
				$target_date = Date("Ymd", strtotime($sBaseDate));
				if(strlen($target_date) != 8 && $target_date > '1970-01-01'){
					$app->flash('error', '날짜를 선택하세요');
					$app->redirect('/mypage/strategies/'.$id.'/analysis');
				}

				// 입출금
				$sFlow = str_replace(',', '', $aFlow[$key]);
				if(!is_numeric($sFlow)){ $sFlow = 0;}

				// 손익
				$sPL = str_replace(',', '', $aPL[$key]);
				if(!is_numeric($sPL)){$sPL = 0;}


				// 입력일자에 따른 시작일자를 구해줘야함
				$start_date = Date("Y-m-d", strtotime($target_date));
				$sql_old = sprintf("SELECT * FROM strategy_daily_analysis WHERE strategy_id='%s' AND basedate < '%s' ORDER BY basedate DESC LIMIT 1", $id, Date("Y-m-d", strtotime($target_date)));
				$aRowOld = $app->db->conn->query($sql_old)->fetch_array();
				if($aRowOld['basedate'] != '' && strtotime($aRowOld['basedate']) < strtotime($start_date)) {
					$start_date = Date("Y-m-d", strtotime($aRowOld['basedate']));
				}

				$app->db->delete('strategy_daily', array(
					'strategy_id'=>$id
					,'target_date'=>$target_date
				));
				$app->db->insert('strategy_daily', array(
					'strategy_id'=>$id
					,'target_date'=>$target_date
					,'flow'=>$sFlow
					,'PL'=>$sPL
				));

				++$nInsert;
				if($sMaxEndDate == '' || $sMaxEndDate < $target_date) {
					$sMaxEndDate = $target_date;
				}
				if($sMinStartDate == '' || $sMinStartDate > $start_date) {
					$sMinStartDate = $start_date;
				}
			}

		}

		////////////////////////////////////////////////////
		// analysis_strategy() 계산하기
		if($nInsert > 0) {
			setStrategyAnalysis($app->db, $id, $sMinStartDate, Date("Y-m-d", strtotime($sMaxEndDate)));
			setStrategyAnalysisMonthly($app->db, $id);
			setStrategyAnalysisYearly($app->db, $id);
			setStrategyScore($app->db, $id);
		}
		////////////////////////////////////////////////////

		$app->redirect('/mypage/strategies/'.$id.'/analysis');
	});

	$app->post('/strategies/:id/analysis/upload', function($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){$app->halt(404, 'not found');}

		// if($_SESSION['user']['user_type'] == 'A'){
		// }else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
		// }else{$app->halt(403, 'forbidden');}

		$savePath = $app->config('upload.tmp.path');

		switch($_FILES['excel']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['excel']['name'];
				$filesize = $_FILES['excel']['size'];
				$filetmpname = $_FILES['excel']['tmp_name'];
				$filetype = $_FILES['excel']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check upload valid ext
				if(!preg_match('/\.(xls|xlsx)$/i', $filename)){
					$app->flash('error', '확장자가 xls, xlsx 파일만 업로드가 가능합니다');
					$app->redirect('/admin/strategies/'.$id.'/daily');
				}

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/strategies/'.$id.'/daily');
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

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/strategies/'.$id.'/daily');
				}

				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/strategies/'.$id.'/daily');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/strategies/'.$id.'/daily');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/strategies/'.$id.'/daily');
				break;
			case UPLOAD_ERR_NO_FILE:
				$app->flash('error', '업로드한 파일이 없습니다');
				$app->redirect('/admin/strategies/'.$id.'/daily');
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/strategies/'.$id.'/daily');
		}

		require(dirname(__FILE__).'/../lib/spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
		require(dirname(__FILE__).'/../lib/spreadsheet-reader-master/SpreadsheetReader.php');

		$Filepath = $finalFilename;

		$sMinTargetDay = '';

		try
		{
			$Spreadsheet = new SpreadsheetReader($Filepath);
			$BaseMem = memory_get_usage();

			$Sheets = $Spreadsheet -> Sheets();

			foreach ($Sheets as $Index => $Name)
			{
				$Time = microtime(true);
				$Spreadsheet -> ChangeSheet($Index);

				foreach ($Spreadsheet as $Key => $Row)
				{
					if ($Row)
					{
						$target_date = (trim($Row[0]));
						$flow = trim($Row[1]);
						$PL = trim($Row[2]);

						$target_date = str_replace('.', '', $target_date);
						$target_date = str_replace('-', '', $target_date);
						$target_date = str_replace('/', '', $target_date);

						if(!is_numeric($target_date) || strlen($target_date) != 8 || strtotime($target_date) <= '1990-01-01'){
							// $target_date = date("Ymd", strtotime(trim($Row[0])));
							// if((!is_numeric($target_date) || strlen($target_date) != 8) || $target_date <= '19900101') {
								continue;
							// }
						}

						if($sMinTargetDay == '' || (strtotime($sMinTargetDay) > strtotime($target_date)))
							$sMinTargetDay = Date("Y-m-d", strtotime($target_date));


						$flow = str_replace(',', '', $flow);
						if(!is_numeric($flow)){	$flow = 0;}

						$PL = str_replace(',', '', $PL);
						if(!is_numeric($PL)){$PL = 0;}


						$app->db->delete('strategy_daily', array(
							'strategy_id'=>$id
							,'target_date'=>$target_date
						));
						$app->db->insert('strategy_daily', array(
							'strategy_id'=>$id
							,'target_date'=>$target_date
							,'flow'=>$flow
							,'PL'=>$PL
						));

							//						$app->db->executesp('add_strategy_daily', array(
							//							'p_strategy_id'=>$id,
							//							'p_target_date'=>$target_date,
							//							'p_flow'=>$flow,
							//							'p_pl'=>$PL
							//						));

					}
				}


			}

		}
		catch (Exception $E)
		{
			$app->flash('error', $E -> getMessage());
		}

		////////////////////////////////////////////////////
		// analysis_strategy() 계산하기
			setStrategyAnalysis($app->db, $id, $sMinTargetDay);
			setStrategyAnalysisMonthly($app->db, $id);
			setStrategyAnalysisYearly($app->db, $id);
			setStrategyScore($app->db, $id);
		////////////////////////////////////////////////////

			//		$app->m->delete('strategy_daily_value:'.$id);
			//		$app->m->delete('strategy_new_daily_value:'.$id);
			//		$app->db->executesp('analysis_strategy',array('strategy_id'=>$id));
			//		//$app->db->executesp('score_strategies',array());

		$app->redirect('/mypage/strategies/'.$id.'/analysis');
	});

	$app->post('/strategies/:id/analysis/edit', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		// if($_SESSION['user']['user_type'] == 'A'){
		// }else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
		// }else{ $app->halt(403, 'forbidden'); }

		$target_date = $app->request->post('basedate');
		//$balance = $app->request->post('balance');
		$flow = $app->request->post('flow');
		$PL = $app->request->post('PL');

		$target_date = str_replace('-', '', $target_date);
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/mypage/strategies/'.$id.'/analysis');
		}

		//$balance = str_replace(',', '', $balance);
		//if(!is_numeric($balance)){
		//	$balance = 0;
		//}

		$flow = str_replace(',', '', $flow);
		if(!is_numeric($flow)){
			$flow = 0;
		}

		$PL = str_replace(',', '', $PL);
		if(!is_numeric($PL)){
			$PL = 0;
		}

		// 삭제된 값일수 있으므로 (null) 삭제 후 다시 저장해줌
		$app->db->delete('strategy_daily', array(
			'strategy_id'=>$id
			,'target_date'=>$target_date
		));
		$app->db->insert('strategy_daily', array(
			'strategy_id'=>$id
			,'target_date'=>$target_date
			,'flow'=>$flow
			,'PL'=>$PL
		));

		// 수정일자에 따른 최근일자를 구해줘야함
		$start_date = Date("Y-m-d", strtotime($target_date));
		$end_date = Date("Y-m-d", strtotime($target_date));

		$sql_old = sprintf("SELECT * FROM strategy_daily_analysis WHERE strategy_id='%s' AND basedate > '%s' ORDER BY basedate DESC LIMIT 1", $id, Date("Y-m-d", strtotime($target_date)));
		$aRowOld = $app->db->conn->query($sql_old)->fetch_array();
		if($aRowOld['basedate'] != '' && strtotime($aRowOld['basedate']) > strtotime($start_date)) {
			$end_date = Date("Y-m-d", strtotime($aRowOld['basedate']));
		}

		////////////////////////////////////////////////////
		// analysis_strategy() 계산하기
			// 수정한 값이 평일일 때만 재계산을 해줌
			//- if( Date("N", strtotime($target_date)) < 6 ) 
			{
				setStrategyAnalysis($app->db, $id, $start_date, $end_date);
				setStrategyAnalysisMonthly($app->db, $id);
				setStrategyAnalysisYearly($app->db, $id);
				setStrategyScore($app->db, $id);
			}
		////////////////////////////////////////////////////


		$app->redirect('/mypage/strategies/'.$id.'/analysis');
	});

    $app->get('/strategies/:id/analysis/delete', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));
		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		// if($_SESSION['user']['user_type'] == 'A'){
		// }else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
		// }else{ $app->halt(403, 'forbidden'); }

		$target_date = $app->request->get('basedate');

		$target_date = str_replace('-', '', $target_date);
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/mypage/strategies/'.$id.'/analysis');
		}

		$app->db->delete('strategy_daily', array(
			'strategy_id'=>$id,
			'target_date'=>$target_date
		));

		// 삭제하고 난 뒤 전략의 데이터가 2개 미만일 경우 상태를 비공개로 변경함
		$daily_values_count = $app->db->selectCount('strategy_daily', array('strategy_id'=>$id));
		if($daily_values_count < 2){
			$app->db->update('strategy', array(
				'is_open'=>'0'
			), array('strategy_id'=>$id));
		}

		// 삭제일자에 따른 최근일자를 구해줘야함
		$start_date = Date("Y-m-d", strtotime($target_date));
		$end_date = Date("Y-m-d", strtotime($target_date));

		$sql_old = sprintf("SELECT * FROM strategy_daily_analysis WHERE strategy_id='%s' AND basedate > '%s' ORDER BY basedate DESC LIMIT 1", $id, Date("Y-m-d", strtotime($target_date)));
		$aRowOld = $app->db->conn->query($sql_old)->fetch_array();
		if($aRowOld['basedate'] != '' && strtotime($aRowOld['basedate']) > strtotime($start_date)) {
			$end_date = Date("Y-m-d", strtotime($aRowOld['basedate']));
		}

		////////////////////////////////////////////////////
		// analysis_strategy() 계산하기
			// 삭제한 값이 평일일 때만 재계산을 해줌
			//- if( Date("N", strtotime($target_date)) < 6 )
			{
				if($start_date == $end_date) {
					$app->db->delete('strategy_daily_analysis', array(
						'strategy_id'=>$id,
						'basedate'=>$start_date
					));
				} else {
					setStrategyAnalysis($app->db, $id, $start_date, $end_date);
				}
				setStrategyAnalysisMonthly($app->db, $id);
				setStrategyAnalysisYearly($app->db, $id);
				setStrategyScore($app->db, $id);
			}
		////////////////////////////////////////////////////

		$app->redirect('/mypage/strategies/'.$id.'/analysis');
	});

	$app->get('/strategies/:id/analysis/deleteall', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));
		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		// if($_SESSION['user']['user_type'] == 'A'){
		// }else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
		// }else{ $app->halt(403, 'forbidden'); }

		$app->db->delete('strategy_daily', array(
			'strategy_id'=>$id,
		));

		$app->db->delete('strategy_daily_analysis', array(
			'strategy_id'=>$id,
		));

		/////////////////////////////////////////////////////////////////////////////////////
		// 월별 년별 삭제도 추가시킴 (2017-02-23 PHPSCHOOL)
			$app->db->delete('strategy_monthly_analysis', array(
				'strategy_id'=>$id,
			));

			$app->db->delete('strategy_yearly_analysis', array(
				'strategy_id'=>$id,
			));
		/////////////////////////////////////////////////////////////////////////////////////

		// 삭제하고 난 뒤 전략의 데이터가 2개 미만일 경우 상태를 비공개로 변경함
		$app->db->update('strategy', array(
			'is_open'=>'0'
		), array('strategy_id'=>$id));

			//		$app->m->delete('strategy_daily_value:'.$id);
			//		$app->m->delete('strategy_new_daily_value:'.$id);
			//
			//		// 전략의 주요 지표 데이터 저장
			//		fetchStrategyData($id);
		
		setStrategyScore($app->db, $id);

		$app->redirect('/mypage/strategies/'.$id.'/analysis');
	});

    $app->get('/strategies/:id/fund', function($id) use ($app, $log) {
        $submenu = 'strategy';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=> $id));

        if (($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])) {
            alert('잘못된 요청입니다');
            $app->stop();
        }

		$total = $app->db->selectCount('strategy_funding', array('strategy_id'=>$id));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'strategy'  => $strategy,
            'total'     => $total,
        );

        $app->render('mypage/strategy_fund.php', $param);
    });


    $app->get('/strategies/:id/fund/list', function($id) use ($app, $log) {

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 10;
		$start = ($page - 1) * $count;

		$daily_values = $app->db->select('strategy_funding', '*', array('strategy_id'=>$id), array('target_date'=>'desc'), $start, $count);

        $param = array(
            'daily_values'  => $daily_values,
        );

        $app->render('mypage/strategy_fund_list.php', $param);
    });
    
    $app->post('/strategies/:id/fund/add', function($id) use ($app, $log) {

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if (empty($strategy)){
			$app->halt(404, 'not found');
		}

        if (($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])) {
			$app->halt(404, 'not found');
        }

		$target_date = $app->request->post('target_date');
		$money = $app->request->post('money');
		$investor = $app->request->post('investor');

		$target_date = str_replace('.', '', $target_date);
		if (strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/mypage/strategies/'.$id.'/fund');
		}

		if ($app->db->selectCount('strategy_funding', array('strategy_id'=>$id, 'target_date'=>$target_date))){
			$app->flash('error', '이미 입력된 데이터가 있습니다.');
			$app->redirect('/mypage/strategies/'.$id.'/fund');
		}

		$money = str_replace(',', '', $money);
		if(!is_numeric($money)){
			$money = 0;
		}

		$investor = str_replace(',', '', $investor);
		if(!is_numeric($investor)){
			$investor = 0;
		}

		$app->db->insert('strategy_funding', array(
			'strategy_id'=>$id,
			'target_date'=>$target_date,
			'money'=>$money,
			'investor'=>$investor
		));
    
		$app->redirect('/mypage/strategies/'.$id.'/fund');
    });

	$app->post('/strategies/:id/fund/upload', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		// if($_SESSION['user']['user_type'] == 'A'){
		// }else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
		// }else{ $app->halt(403, 'forbidden'); }

		$savePath = $app->config('upload.tmp.path');

		switch($_FILES['excel']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['excel']['name'];
				$filesize = $_FILES['excel']['size'];
				$filetmpname = $_FILES['excel']['tmp_name'];
				$filetype = $_FILES['excel']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];


				// check upload valid ext
				if(!preg_match('/\.(xls|xlsx)$/i', $filename)){
					$app->flash('error', '확장자가 xls, xlsx 파일만 업로드가 가능합니다');
					$app->redirect('/mypage/strategies/'.$id.'/fund');
				}

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/mypage/strategies/'.$id.'/fund');
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
				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/mypage/strategies/'.$id.'/fund');
				}

				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/mypage/strategies/'.$id.'/fund');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/mypage/strategies/'.$id.'/fund');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/mypage/strategies/'.$id.'/fund');
				break;
			case UPLOAD_ERR_NO_FILE:
				$app->flash('error', '업로드한 파일이 없습니다');
				$app->redirect('/mypage/strategies/'.$id.'/fund');
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/mypage/strategies/'.$id.'/fund');
		}

		require(dirname(__FILE__).'/../lib/spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
		require(dirname(__FILE__).'/../lib/spreadsheet-reader-master/SpreadsheetReader.php');

		$Filepath = $finalFilename;

		try
		{
			$Spreadsheet = new SpreadsheetReader($Filepath);
			$BaseMem = memory_get_usage();

			$Sheets = $Spreadsheet -> Sheets();

			foreach ($Sheets as $Index => $Name)
			{
				$Time = microtime(true);

				$Spreadsheet -> ChangeSheet($Index);

				foreach ($Spreadsheet as $Key => $Row)
				{
					if($Key == 0) continue;
					// echo $Key.': ';
					if ($Row)
					{
						// print_r($Row);

						$target_date = trim($Row[0]);
						$money = trim($Row[1]);
						$investor = trim($Row[2]);

						$target_date = str_replace('.', '', $target_date);
						$target_date = str_replace('-', '', $target_date);
						$target_date = str_replace('/', '', $target_date);
						if(!is_numeric($target_date) || strlen($target_date) != 8){
							continue;
						}

						if($app->db->selectCount('strategy_funding', array('strategy_id'=>$id, 'target_date'=>$target_date))){
							continue;
						}

						$money = str_replace(',', '', $money);
						if(!is_numeric($money)){
							$money = 0;
						}

						$investor = str_replace(',', '', $investor);
						if(!is_numeric($investor)){
							$investor = 0;
						}

						$app->db->insert('strategy_funding', array(
							'strategy_id'=>$id,
							'target_date'=>$target_date,
							'money'=>$money,
							'investor'=>$investor
						));

					}
				}

			}

		}
		catch (Exception $E)
		{
			$app->flash('error', $E -> getMessage());
		}

		//$app->m->delete('strategy_total_funding:'.$id);

		$app->redirect('/mypage/strategies/'.$id.'/fund');
	});

	$app->post('/strategies/:id/fund/edit', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		// if($_SESSION['user']['user_type'] == 'A'){
		// } else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
		// } else { $app->halt(403, 'forbidden'); }

		$target_date = $app->request->post('target_date');
		$money = $app->request->post('money');
		$investor = $app->request->post('investor');

		$target_date = str_replace('.', '', $target_date);
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/mypage/strategies/'.$id.'/fund');
		}

		$money = str_replace(',', '', $money);
		if(!is_numeric($money)){
			$money= 0;
		}

		$investor = str_replace(',', '', $investor);
		if(!is_numeric($investor)){
			$investor = 0;
		}

		$app->db->update('strategy_funding', array(
			'money'=>$money,
			'investor'=>$investor
		), array('strategy_id'=>$id, 'target_date'=>$target_date,));

		//$app->m->delete('strategy_total_funding:'.$id);

		$app->redirect('/mypage/strategies/'.$id.'/fund');
	});

	$app->get('/strategies/:id/fund/delete', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));
		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		// if($_SESSION['user']['user_type'] == 'A'){
		// }else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
		// }else{ $app->halt(403, 'forbidden'); }

		$target_date = $app->request->get('target_date');

		$target_date = str_replace('.', '', $target_date);
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/mypage/strategies/'.$id.'/fund');
		}

		$app->db->delete('strategy_funding', array(
			'strategy_id'=>$id,
			'target_date'=>$target_date
		));

		//$app->m->delete('strategy_total_funding:'.$id);

		$app->redirect('/mypage/strategies/'.$id.'/fund');
	});

    $app->get('/strategies/:id/account', function($id) use ($app, $log) {
        $submenu = 'strategy';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());
        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=> $id));

        if (($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])) {
            alert('잘못된 요청입니다');
            $app->stop();
        }

		$total = $app->db->selectCount('strategy_account', array('strategy_id'=>$id));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'strategy'  => $strategy,
            'total'     => $total,
        );

        $app->render('mypage/strategy_account.php', $param);
    });

    $app->get('/strategies/:id/account/list', function($id) use ($app, $log) {

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 10;
		$start = ($page - 1) * $count;

		$daily_values = $app->db->select('strategy_account', '*', array('strategy_id'=>$id), array('account_id'=>'desc'), $start, $count);

        $param = array(
            'daily_values'  => $daily_values,
        );

        $app->render('mypage/strategy_account_list.php', $param);
    });


	$app->post('/strategies/:id/account/add', function ($id) use ($app, $log) {
		//	$app->flash('error', '업로드에 실패하였습니다');
		//	$app->redirect('/mypage/strategies/'.$id.'/account');
		//$target_date_array = $app->request->post('target_date');
		$title_array = $app->request->post('title');

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->flash('error', '업로드에 실패하였습니다');
			$app->redirect('/mypage/strategies/'.$id.'/account');
		}

			//		if($_SESSION['user']['user_type'] == 'A'){
			//		}else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
			//		}else{
			//			$app->flash('error', '업로드에 실패하였습니다');
			//			$app->redirect('/mypage/strategies/'.$id.'/account');
			//		}

		if(!count($title_array)){
			$app->flash('error', '업로드에 실패하였습니다');
			$app->redirect('/mypage/strategies/'.$id.'/account');
		}

		if(empty($title_array)){
			$app->flash('error', '업로드에 실패하였습니다');
			$app->redirect('/mypage/strategies/'.$id.'/account');
		}

		// $target_date가 데이트형태에서 그냥 순번으로 변경되었음
		foreach($title_array as $k => $title){

			$account_image_url = '';
			$savePath = $app->config('account.path');
			$max_file_size = 1024 * 1024;

			if(isset($_FILES['account_img'])){
			if(isset($_FILES['account_img']['error'][$k])){
			switch($_FILES['account_img']['error'][$k]){
				case UPLOAD_ERR_OK:
					$filename = $_FILES['account_img']['name'][$k];
					$filesize = $_FILES['account_img']['size'][$k];
					$filetmpname = $_FILES['account_img']['tmp_name'][$k];
					$filetype = $_FILES['account_img']['type'][$k];
					$tmpfileext = explode('.', $filename);
					$fileext = $tmpfileext[count($tmpfileext)-1];

					// check filesize
					if($filesize > $max_file_size){
						$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					    $app->redirect('/mypage/strategies/'.$id.'/account');
					}

					if(strpos($filetype, 'image') === false){
						$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					    $app->redirect('/mypage/strategies/'.$id.'/account');
						continue;
					}

					// check upload valid ext
					if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
						$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					    $app->redirect('/mypage/strategies/'.$id.'/account');
						continue;
					}


					// upload correct method
					if(!is_uploaded_file($filetmpname)){
						// $app->flash('error', '정상적인 방법으로 업로드해주세요');
						// $app->redirect('/admin/brokers/'.$id.'/accounts');
						continue;
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
					$account_image_url = $app->config('account.url').'/'.$saveFilename.'.'.$fileext;

					if(!move_uploaded_file($filetmpname, $finalFilename)){
						$app->flash('error', '업로드에 실패하였습니다');
						$app->redirect('/mypage/strategies/'.$id.'/account');
						continue;
					}
					break;
				case UPLOAD_ERR_INI_SIZE:
					$app->flash('error', '업로드 가능 용량을 초과하였습니다');
					$app->redirect('/mypage/strategies/'.$id.'/account');
					continue;
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$app->flash('error', '업로드 가능 용량을 초과하였습니다');
					$app->redirect('/mypage/strategies/'.$id.'/account');
					continue;
					break;
				case UPLOAD_ERR_PARTIAL:
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/mypage/strategies/'.$id.'/account');
					continue;
					break;
				case UPLOAD_ERR_NO_FILE:
					break;
				default:
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/mypage/strategies/'.$id.'/account');
					continue;
			}
			}
			}

			/*
			if(!empty($title) && !empty($account_image_url)){
				if($app->db->selectCount('strategy_account', array('strategy_id'=>$id,'target_date'=>$target_date))){
					$app->db->update('strategy_account', array(
						'strategy_id'=>$id,
						'image'=>$account_image_url
					), array('target_date'=>$target_date));
				}else{
					$app->db->insert('strategy_account', array(
						'strategy_id'=>$id,
					//	'target_date'=>$target_date,
						'image'=>$account_image_url
					));
				}
			}
			*/

			if($account_image_url){
				$app->db->insert('strategy_account', array(
					'strategy_id'=>$id,
					'target_date'=>'',
					'title'=>$title,
					'image'=>$account_image_url
				));
			}
		}

		$app->redirect('/mypage/strategies/'.$id.'/account');
	});

	$app->post('/strategies/:id/account/delete', function ($id) use ($app, $log) {

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

			//		if($_SESSION['user']['user_type'] == 'A'){
			//		}else if(($strategy['developer_uid'] != $_SESSION['user']['uid']) && ($strategy['pb_uid'] != $_SESSION['user']['uid'])){
			//		}else{
			//			$app->halt(403, 'forbidden');
			//		}

		$input_account_ids = $app->request->post('account_ids');

		if(empty($input_account_ids)){
			// $app->flash('error', '날짜를 선택하세요');
			$app->redirect('/mypage/strategies/'.$id.'/account');
		}

		$account_ids = array();
		foreach($input_account_ids as $v){
			if(empty($v)) continue;
			if(!is_numeric($v)) continue;
			if(in_array($v, $account_ids)) continue;

			$account_ids[] = $v;
		}

		if(count($account_ids) == 0) $app->redirect('/mypage/strategies/'.$id.'/account');

		// 이미지 경로
		$images_path = array();
		$result = $app->db->conn->query('SELECT * FROM strategy_account WHERE account_id IN ('.implode(',', $account_ids).')');
		while($row = $result->fetch_array()){
			$images_path[] = $row['image'];
		}

		$app->db->conn->query('DELETE FROM strategy_account WHERE account_id IN ('.implode(',', $account_ids).')');

		foreach($images_path as $path){
			if(file_exists($path)) unlink($path);
		}

		$app->redirect('/mypage/strategies/'.$id.'/account');
	});



    $app->get('/portfolios', function() use ($app, $log) {
        $submenu = 'portfolio';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());
        $cnt = $app->db->selectCount('portfolio', array('uid'=>$_SESSION['user']['uid']));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'cnt'       => $cnt,
        );
        $app->render('mypage/portfolio_list.php', $param);
    });


    $app->get('/portfolios/:id', function($id) use ($app, $log) {
        $submenu = 'portfolio';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $portfolio = $app->db->selectOne('portfolio', '*', array('uid'=>$_SESSION['user']['uid'], 'portfolio_id'=>$id));
        $tmp = $app->db->select('portfolio_strategy', 'strategy_id', array('portfolio_id'=>$id));
        $portfolio_strategy = array();
        foreach ($tmp as $v) {
            $portfolio_strategy[] = $v['strategy_id'];
        }

        if(empty($portfolio)){
            $app->halt(404, 'not found');
        }


        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'portfolio' => $portfolio,
            'portfolio_strategy'=> $portfolio_strategy,
        );

        $app->render('mypage/portfolio_detail.php', $param);
    });



    $app->post('/portfolios/:id/edit', function($id) use ($app, $log, $isLoggedIn) {
        
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
        $open = $app->request->post('open');

        if(empty($name)){
            $app->flash('error', '이름을 입력해주세요');
            $app->redirect('/mypage/portfolios/'.$id);
        }

        if(empty($start_date) || strlen(preg_replace('/[^\d]/', '', $start_date)) != 8){
            $app->flash('error', '시작날짜를 입력해주세요');
            $app->redirect('/mypage/portfolios/'.$id);
        }else{
            $start_date = preg_replace('/[^\d]/', '', $start_date);
        }

        if(empty($end_date) || strlen(preg_replace('/[^\d]/', '', $end_date)) != 8){
            $app->flash('error', '종료날짜를 입력해주세요');
            $app->redirect('/mypage/portfolios/'.$id);
        }else{
            $end_date = preg_replace('/[^\d]/', '', $end_date);
        }

        // 시작일과 종료일의 유효성체크
        $start_date_timestamp = strtotime($start_date);
        $end_date_timestamp = strtotime($end_date);

        if($start_date_timestamp > $end_date_timestamp){
            $app->flash('error', '날짜가 올바르지 않습니다');
            $app->redirect('/mypage/portfolios/'.$id);
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
            $app->redirect('/mypage/portfolios/'.$id);
        }else{
            $amount = preg_replace('/[^\d]/', '', $amount);
        }

        if(count($strategy_ids) > 10 || count($percents) > 10){
            $app->flash('error', '10개까지 가능합니다');
            $app->redirect('/mypage/portfolios/'.$id);
        }

        if(count($strategy_ids) == 0){
            $app->flash('error', '전략을 선택해주세요');
            $app->redirect('/mypage/portfolios/'.$id);
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
                $app->redirect('/mypage/portfolios/'.$id);
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

        if($total_percent < 100 || $total_percent % 100 != 0){
            $app->flash('error', '비율 합이 100이어야 합니다');
            $app->redirect('/mypage/portfolios/'.$id);
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
            'end_date'=>$end_date,
            'is_open'=>$open,
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

        echo json_encode(array('result'=>true));
    });


    $app->get('/counsel', function() use ($app, $log) {
        $submenu = 'counsel';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        if ($_SESSION['user']['user_type'] == 'N' or $_SESSION['user']['user_type'] == 'T') {
            $total = $app->db->selectCount('pb_request', array('uid'=>$_SESSION['user']['uid']));
        }

        if ($_SESSION['user']['user_type'] == 'P') {
            $total = $app->db->selectCount('pb_request', array('pid'=>$_SESSION['user']['uid']));
        }

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'total'     => $total,
        );

        $app->render('mypage/counsel.php', $param);
    });

    $app->get('/counsel/list', function() use ($app, $log) {

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 5;
		$start = ($page - 1) * $count;

        $counsels = array();

        if ($_SESSION['user']['user_type'] == 'N' or $_SESSION['user']['user_type'] == 'T') {

            $sql = "SELECT a.*, b.name as req_name, c.name as pb_name
                    FROM pb_request a 
                        INNER JOIN user b ON a.uid = b.uid
                        INNER JOIN user c ON a.pid = c.uid
                    WHERE a.uid = {$_SESSION[user][uid]}
                    ORDER BY req_id DESC 
                    LIMIT $start, $count";

            $result = $app->db->conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $counsels[] = $row;
            }

            $tpl = 'counsel_n_list.php';
        }

        if ($_SESSION['user']['user_type'] == 'P') {
            $sql = "SELECT a.*, b.name as req_name, c.name as pb_name
                    FROM pb_request a 
                        INNER JOIN user b ON a.uid = b.uid
                        INNER JOIN user c ON a.pid = c.uid
                    WHERE a.pid = {$_SESSION[user][uid]}
                    ORDER BY req_id DESC 
                    LIMIT $start, $count";

            $result = $app->db->conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $counsels[] = $row;
            }

            $tpl = 'counsel_p_list.php';
        }

        $param = array(
            'counsels'    => $counsels,
        );

        $app->render('mypage/'.$tpl, $param);
    });

    
    $app->get('/counsel/:req_id', function($req_id) use ($app, $log) {
        $submenu = 'counsel';
        
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $req = $app->db->selectOne('pb_request', '*', array('req_id'=>$req_id));
        $req_user = $app->db->selectOne('user', 'name', array('uid'=>$req['uid']), array());
        $pb_user = $app->db->selectOne('user', 'name', array('uid'=>$req['pid']), array());
        
        $req['req_name'] = $req_user['name'];
        $req['pb_name'] = $pb_user['name'];

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'req'    => $req,
        );

        $app->render('mypage/counsel_detail.php', $param);
    });


    $app->get('/counsel/:req_id/modify', function($req_id) use ($app, $log) {
        $submenu = 'counsel';
        
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $req = $app->db->selectOne('pb_request', '*', array('req_id'=>$req_id));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'req'    => $req,
        );

        $app->render('mypage/counsel_modify.php', $param);
    });

    $app->post('/counsel/:req_id/modify', function($req_id) use ($app, $log) {

        $subject = htmlspecialchars($app->request->post('subject'));
        $contents = htmlspecialchars($app->request->post('contents'));

        $req = $app->db->update('pb_request', array('subject'=>$subject, 'contents'=>$contents), array('req_id'=>$req_id, 'uid'=>$_SESSION['user']['uid']));

        $app->redirect('/mypage/counsel/'.$req_id);
    });


    $app->get('/counsel/:req_id/delete', function($req_id) use ($app, $log) {
        $req = $app->db->delete('pb_request', array('req_id'=>$req_id, 'uid'=>$_SESSION['user']['uid']));
        $app->redirect('/mypage/counsel');
    });

    
    $app->get('/counsel/:req_id/answer', function($req_id) use ($app, $log) {
        $submenu = 'counsel';
        
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $req = $app->db->selectOne('pb_request', '*', array('req_id'=>$req_id));
        $req_user = $app->db->selectOne('user', '*', array('uid'=>$req['uid']), array());
        $req['req_name'] = $req_user['name'];
        $req['req_hp'] = $req_user['mobile'];

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'req'    => $req,
        );

        $app->render('mypage/counsel_answer.php', $param);
    });

    $app->post('/counsel/:req_id/answer', function($req_id) use ($app, $log) {

        $answer_subject = htmlspecialchars($app->request->post('answer_subject'));
        $answer = htmlspecialchars($app->request->post('answer'));
        $mobile = htmlspecialchars($app->request->post('hp'));

        $req = $app->db->update('pb_request', 
                                array('status'=>1, 'answer_subject'=>$answer_subject, 'answer'=>$answer, 'answer_date'=>date('Y-m-d H:i:s')), 
                                array('req_id'=>$req_id, 'pid'=>$_SESSION['user']['uid']));

		//문자발송
		$SMSINFO['smsMsg']="시스메틱에서 문의하신 상담의 답변이 완료되었습니다.";
		$SMSINFO['smsHp']=$mobile;
		sendSMS($SMSINFO);

        $app->flash('error', '상담이 완료되었습니다.');
        $app->redirect('/mypage/counsel/'.$req_id);
    });


    
    $app->get('/request', function() use ($app, $log) {
        $submenu = 'request';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        if ($_SESSION['user']['user_type'] == 'N') {
            $total = $app->db->selectCount('qna', array('target'=>'strategy', 'uid'=>$_SESSION['user']['uid']));
        }

        if ($_SESSION['user']['user_type'] == 'P' || $_SESSION['user']['user_type'] == 'T' ) {

            $sql = "SELECT count(*) cnt
                    FROM qna a 
                        INNER JOIN strategy b ON a.target_value = b.strategy_id
                    WHERE 
                        a.target = 'strategy'
                        AND (b.developer_uid = {$_SESSION[user][uid]} or b.pb_uid = {$_SESSION[user][uid]})
                    ORDER BY qna_id DESC";
				//echo $sql;
            $result = $app->db->conn->query($sql);
            $row = $result->fetch_assoc();
            $total = $row['cnt'];
        }

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'total'     => $total,
        );

        $app->render('mypage/request.php', $param);
    });

    $app->get('/request/list', function() use ($app, $log) {

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 5;
		$start = ($page - 1) * $count;

        $qnas = array();

        if ($_SESSION['user']['user_type'] == 'N') {

            $qnas = $app->db->select('qna', '*', array('target'=>'strategy', 'uid'=>$_SESSION['user']['uid']), array('qna_id'=>'desc'), $start, $count);

            $tpl = 'request_n_list.php';
        }

        if ($_SESSION['user']['user_type'] == 'P' || $_SESSION['user']['user_type'] == 'T'  || $_SESSION['user']['user_type'] == 'A' ) {
            $sql = "SELECT a.*
                    FROM qna a 
                        INNER JOIN strategy b ON a.target_value = b.strategy_id
                    WHERE 
                        a.target = 'strategy'
                        AND (b.developer_uid = {$_SESSION[user][uid]} or b.pb_uid = {$_SESSION[user][uid]})
                    ORDER BY qna_id DESC 
                    LIMIT $start, $count";

            $result = $app->db->conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $qnas[] = $row;
            }

            $tpl = 'request_d_list.php';
        }

        $param = array(
            'qnas'    => $qnas,
        );

        $app->render('mypage/'.$tpl, $param);
    });

    
    $app->get('/request/:qna_id', function($qna_id) use ($app, $log) {
        $submenu = 'request';
        
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());
        $qna = $app->db->selectOne('qna', '*', array('qna_id'=>$qna_id));
        $sql = "SELECT b.name
                FROM strategy a INNER JOIN user b ON a.developer_uid = b.uid
                WHERE 
                    a.strategy_id = '$qna[target_value]'";

        $result = $app->db->conn->query($sql);
        $row = $result->fetch_assoc();

        $sql2 = "SELECT b.name
                FROM strategy a INNER JOIN user b ON a.pb_uid = b.uid
                WHERE 
                    a.strategy_id = '$qna[target_value]'";

        $result2 = $app->db->conn->query($sql2);
        $row2 = $result2->fetch_assoc();

		if($row2['name']){
			$qna['developer_name'] = $row2['name'];
		}else{
			$qna['developer_name'] = $row['name'];
		}

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'qna'    => $qna,
        );

        $app->render('mypage/request_detail.php', $param);
    });


    $app->get('/request/:qna_id/modify', function($qna_id) use ($app, $log) {
        $submenu = 'request';
        
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $qna = $app->db->selectOne('qna', '*', array('qna_id'=>$qna_id));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'qna'    => $qna,
        );

        $app->render('mypage/request_modify.php', $param);
    });

    $app->post('/request/:qna_id/modify', function($qna_id) use ($app, $log) {

        //$subject = htmlspecialchars($app->request->post('subject'));
        $question = htmlspecialchars($app->request->post('question'));

        $qna = $app->db->update('qna', array('question'=>$question), array('qna_id'=>$qna_id, 'uid'=>$_SESSION['user']['uid']));

        $app->redirect('/mypage/request/'.$qna_id);
    });


    $app->get('/request/:qna_id/delete', function($qna_id) use ($app, $log) {
        $qna = $app->db->delete('qna', array('qna_id'=>$qna_id, 'uid'=>$_SESSION['user']['uid']));
        $app->redirect('/mypage/request');
    });

    $app->get('/request/:qna_id/answer', function($qna_id) use ($app, $log) {
        $submenu = 'request';
        
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $qna = $app->db->selectOne('qna', '*', array('qna_id'=>$qna_id));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'qna'    => $qna,
        );

        $app->render('mypage/request_answer.php', $param);
    });

    $app->post('/request/:qna_id/answer', function($qna_id) use ($app, $log) {

        //$answer_subject = htmlspecialchars($app->request->post('answer_subject'));
        $answer = htmlspecialchars($app->request->post('answer'));

        $req = $app->db->update('qna', 
                                array('answer'=>$answer, 'answer_at'=>time()),
                                array('qna_id'=>$qna_id));

		$qna = $app->db->selectOne('qna', '*', array('qna_id'=>$qna_id));
		$myInfo = $app->db->selectOne('user', '*', array('uid'=>$qna['uid']), array());

		//문자발송
		$SMSINFO['smsMsg']="시스메틱에서 문의하신 상담의 답변이 완료되었습니다.";
		$SMSINFO['smsHp']=$myInfo['mobile'];
		sendSMS($SMSINFO);

        $app->redirect('/mypage/request/'.$qna_id);
    });



    
    $app->get('/invest', function() use ($app, $log) {
        $submenu = 'invest';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        if ($_SESSION['user']['user_type'] == 'N') {
            $total = $app->db->selectCount('strategy_invest', array('uid'=>$_SESSION['user']['uid']));

            $param = array(
                'submenu'   => $submenu,
                'myInfo'    => $myInfo,
                'total'     => $total,
            );

            $app->render('mypage/invest.php', $param);
        }

        if ($_SESSION['user']['user_type'] == 'P' || $_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'A' ) {
            
            $strategy_id = $app->request->get('strategy_id');

            $sql = "
            SELECT a.strategy_id, a.name, if (isnull(b.cnt), 0, b.cnt ) as stg_cnt
                    FROM strategy a 
                        LEFT JOIN (
                            SELECT strategy_id, count(strategy_id) as cnt 
                            FROM strategy_invest GROUP BY strategy_id
                        ) b ON a.strategy_id = b.strategy_id
                    WHERE 
                        (a.developer_uid = {$_SESSION[user][uid]} or a.pb_uid = {$_SESSION[user][uid]})
                    ORDER BY stg_cnt DESC, name ASC";

            $result = $app->db->conn->query($sql);
            $strategies = array();
            while ($row = $result->fetch_assoc()) {
                $strategies[] = $row;
            }

            $sql = "SELECT count(*) as cnt
                    FROM strategy_invest a 
                        INNER JOIN strategy b ON a.strategy_id = b.strategy_id
                    WHERE 
                        (b.developer_uid = {$_SESSION[user][uid]} or b.pb_uid = {$_SESSION[user][uid]})";
            if ($strategy_id) $sql .= " AND a.strategy_id = '$strategy_id'";

            $result = $app->db->conn->query($sql);
            $row = $result->fetch_assoc();
            $total = $row['cnt'];

            $param = array(
                'submenu'   => $submenu,
                'myInfo'    => $myInfo,
                'strategies'=> $strategies,
                'strategy_id'=> $strategy_id,
                'total'     => $total,
            );

            $app->render('mypage/invest_d.php', $param);
        }
    });

    $app->get('/invest/list', function() use ($app, $log) {

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 5;
		$start = ($page - 1) * $count;

        $invests = array();

        if ($_SESSION['user']['user_type'] == 'N') {

            $sql = "SELECT a.*, b.name strategy_name
                    FROM strategy_invest a 
                        INNER JOIN strategy b ON a.strategy_id = b.strategy_id
                    WHERE 
                        a.uid = {$_SESSION[user][uid]}
                    ORDER BY invest_id DESC 
                    LIMIT $start, $count";

            $result = $app->db->conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $invests[] = $row;
            }

            $tpl = 'invest_n_list.php';
        }

        if ($_SESSION['user']['user_type'] == 'P' || $_SESSION['user']['user_type'] == 'T' ) {

		    $strategy_id = $app->request->get('strategy_id');

            $sql = "SELECT a.*, b.name strategy_name, c.name as user_name
                    FROM strategy_invest a 
                        INNER JOIN strategy b ON a.strategy_id = b.strategy_id
                        INNER JOIN user c ON a.uid = c.uid
                    WHERE 
                        a.strategy_id= $strategy_id
                        AND (b.developer_uid = {$_SESSION[user][uid]} or b.pb_uid = {$_SESSION[user][uid]})
                    ORDER BY invest_id DESC 
                    LIMIT $start, $count";

            $result = $app->db->conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $invests[] = $row;
            }

            $tpl = 'invest_d_list.php';
        }

        $param = array(
            'invests'    => $invests,
        );

        $app->render('mypage/'.$tpl, $param);
    });


    $app->post('/invest/chg', function() use ($app, $log) {

        $invest_id = $app->request->post('invest_id');
        $status = $app->request->post('status');

        $req = $app->db->update('strategy_invest', array('status'=>$status), array('invest_id'=>$invest_id));
        
        echo json_encode(array('result'=>true));
    });




    
    $app->get('/customer', function() use ($app, $log) {
        $submenu = 'customer';

        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $total = $app->db->selectCount('customer', array('uid'=>$_SESSION['user']['uid']));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'total'     => $total,
        );

        $app->render('mypage/customer.php', $param);
    });

    $app->get('/customer/list', function() use ($app, $log) {

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 5;
		$start = ($page - 1) * $count;

        $qnas = array();

        $qnas = $app->db->select('customer', '*', array('uid'=>$_SESSION['user']['uid']), array('cus_id'=>'desc'), $start, $count);

        $param = array(
            'qnas'    => $qnas,
        );

        $app->render('mypage/customer_list.php', $param);
    });

    
    $app->get('/customer/:cus_id', function($cus_id) use ($app, $log) {
        $submenu = 'customer';
        
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());
        $qna = $app->db->selectOne('customer', '*', array('cus_id'=>$cus_id));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'qna'    => $qna,
        );

        $app->render('mypage/customer_detail.php', $param);
    });


    $app->get('/customer/:cus_id/modify', function($cus_id) use ($app, $log) {
        $submenu = 'customer';
        
        $myInfo = $app->db->selectOne('user', '*', array('uid'=>$_SESSION['user']['uid']), array());

        $qna = $app->db->selectOne('customer', '*', array('cus_id'=>$cus_id));

        $param = array(
            'submenu'   => $submenu,
            'myInfo'    => $myInfo,
            'qna'    => $qna,
        );

        $app->render('mypage/customer_modify.php', $param);
    });

    $app->post('/customer/:cus_id/modify', function($cus_id) use ($app, $log) {

        $subject = htmlspecialchars($app->request->post('subject'));
        $question = htmlspecialchars($app->request->post('question'));

        $qna = $app->db->update('customer', array('subject'=>$subject, 'question'=>$question), array('cus_id'=>$cus_id, 'uid'=>$_SESSION['user']['uid']));

        $app->redirect('/mypage/customer/'.$cus_id);
    });


    $app->get('/customer/:cus_id/delete', function($cus_id) use ($app, $log) {
        $qna = $app->db->delete('customer', array('cus_id'=>$cus_id, 'uid'=>$_SESSION['user']['uid']));
        $app->redirect('/mypage/customer');
    });

});
