<?php
/**
 * 투자하기
 */

$app->group('/investment', function() use ($app, $log, $isLoggedIn) {


    // 투자하기 메인
    $app->get('/', function() use ($app, $log) {
        $topmenu = 'invest';

        $param = array(
            'topmenu' => $topmenu,
        );

        $app->render('investment/main.php', $param);
    });


    // 전략 목록
    $app->get('/strategies', function() use ($app, $log, $isLoggedIn) {
        $topmenu = 'invest';

        $developer_uid = $app->request->get('developer_uid');
        if (empty($developer_uid)) {
            $submenu = 'strategy';
            $developer = null;
        } else {
            $submenu = 'developers';
            $sql = "SELECT
                        *,
                        (SELECT count(*) FROM strategy WHERE (developer_uid = a.uid or pb_uid = a.uid) AND is_delete='0' AND is_operate='1' AND is_open='1') strategy_cnt,
                        (SELECT count(*) from portfolio WHERE uid=a.uid AND is_open = '1') portfolio_cnt
                    FROM
                        user a
                    WHERE uid = $developer_uid";
            $result = $app->db->conn->query($sql);
            $developer = $result->fetch_assoc();
        }

        $kinds = $app->db->select('kind', '*', array(), array('sorting'=>'asc'));
        $items = $app->db->select('item', '*', array(), array('sorting'=>'asc'));

        $param = array(
            'topmenu'       => $topmenu,
            'submenu'       => $submenu,
            'developer'     => $developer,
            'kinds'         => $kinds,
            'items'         => $items,
        );
        $app->render('investment/strategy_list.php', $param);
    });


    // 전략 목록
    $app->get('/strategies/:id', function($id) use ($app, $log, $isLoggedIn) {

        $app->redirect('/strategies/'.$id);
    });

    // 전략 상세 출력
    $app->get('/strategies/:id/print', function($id) use ($app, $log, $isLoggedIn) {

        $app->redirect('/strategies/'.$id.'/print');
    });

    // 전략 문의
    $app->post('/strategies/:id/ask', function($id) use ($app, $log) {
        $ask_body = $app->request->post('ask_body');

        if(empty($ask_body)){
            echo json_encode(array('result'=>false));
            $app->stop();
        }

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if($strategy['developer_name']==""){
			if(trim($strategy['pb_uid'])){
				$sql = "SELECT * FROM user WHERE uid=".$strategy['pb_uid'];
				$result = $app->db->conn->query($sql);
				$row = $result->fetch_array();
				$strategy['developer_name'] = $row[name];
			}else{
				$sql = "SELECT * FROM user WHERE uid=".$strategy['developer_uid'];
				$result = $app->db->conn->query($sql);
				$row = $result->fetch_array();
				$strategy['developer_name'] = $row[name];
			}
		}

        if(empty($strategy)){
            $app->halt(404, 'not found');
        }

        $zsfCode = $app->request->post('zsfCode');
		$zsfCode = stripslashes(trim($zsfCode));
		include './zmSpamFree/zmSpamFree.php';
		/*
			zsfCheck 함수는 두 개의 인수를 사용할 수 있다.
			$_POST['zsfCode'] : 사용자가 입력한 스팸방지코드 값
			'DemoPage' : 관리자가 로그파일에 남겨놓고 싶은 메모, 예를 들어 bulletin 게시판의 comment 쓰기시 스팸방지코드를 입력했다 한다면
							'bulletin|comment'라고 써 놓으면, 어떤 게시판의 어떤 상황에서 스팸차단코드가 맞거나 틀렸는지 알 수 있을 것이다.
							이외에 '제목의 일부'나 '글 내용의 일부'를 같이 넣으면, 어떤 스팸광고글이 차단되었는지도 확인할 수 있다.
							참고로 이 인수는 생략 가능하다.
		*/
		$r = zsfCheck ( $zsfCode,'DemoPage' );	# $_POST['zsfCode']는 입력된 스팸방지코드 값이고, 'DemoPage'는 기타 기록하고픈
		$zsfCode_state = $r ? 'Y' : 'N';

		if($zsfCode_state=="N" or !$_SESSION['user']['uid']){
	        echo json_encode(array('result'=>false));
			exit;
		}

		if(trim($strategy['pb_uid'])){
			$myInfo = $app->db->selectOne('user', '*', array('uid'=>$strategy['pb_uid']), array());
			//문자발송
			$SMSINFO['smsMsg']="시스메틱 상품 문의하기가 접수 되었습니다.";
			$SMSINFO['smsHp']=$myInfo['mobile'];
			sendSMS($SMSINFO);
		}else{
			$myInfo = $app->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']), array());
			//문자발송
			$SMSINFO['smsMsg']="시스메틱 상품 문의하기가 접수 되었습니다.";
			$SMSINFO['smsHp']=$myInfo['mobile'];
			sendSMS($SMSINFO);
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


    // 전략 투자하기
    $app->post('/strategies/:id/invest', function($id) use ($app, $log) {

        $mobile = $app->request->post('mobile');
        $email = $app->request->post('email');
        $s_price = str_replace(',', '', $app->request->post('s_price'));
        $s_date = $app->request->post('s_date');
        $max_loss_per = $app->request->post('max_loss_per');

        if(empty($mobile) || empty($email) || empty($s_price) || empty($s_date) || empty($max_loss_per)){
            echo json_encode(array('result'=>false));
            $app->stop();
        }

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

        if(empty($strategy)){
            $app->halt(404, 'not found');
        }

		if(trim($strategy['pb_uid'])){
			$myInfo = $app->db->selectOne('user', '*', array('uid'=>$strategy['pb_uid']), array());
			//문자발송
			$SMSINFO['smsMsg']="시스메틱 상품 투자요청이 접수 되었습니다.";
			$SMSINFO['smsHp']=$myInfo['mobile'];
			sendSMS($SMSINFO);
		}else{
			$myInfo = $app->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']), array());
			//문자발송
			$SMSINFO['smsMsg']="시스메틱 상품 투자요청이 접수 되었습니다.";
			$SMSINFO['smsHp']=$myInfo['mobile'];
			sendSMS($SMSINFO);
		}

        $app->db->insert('strategy_invest', array(
            'strategy_id'   => $id,
            'mobile'        => $mobile,
            'email'         => $email,
            's_price'       => $s_price,
            's_date'        => $s_date,
            'max_loss_per'  => $max_loss_per,
            'uid'           => $_SESSION['user']['uid'],
        ));

        echo json_encode(array('result'=>true));
    });

    // pb, trader 목록
    $app->get('/developers', function() use ($app, $log) {

        $topmenu = 'invest';
        $submenu = 'developers';

        $type = $app->request->get('type');
        $type2 = $app->request->get('type2');
        $keyword = $app->request->get('keyword');

        if ($type == 'T' || $type == 'P') {
            $where = "user_type = '$type'";
        } else {
            $where = "user_type IN ('T', 'P')";
        }

        if (!empty($keyword)) {
            $where_keyword = " AND (name LIKE '%".$app->db->conn->real_escape_string($keyword)."%' OR nickname LIKE '%".$app->db->conn->real_escape_string($keyword)."%')";
            $keyword = htmlspecialchars($keyword);
        }

        $sql = "SELECT user_type, COUNT(*) cnt FROM user WHERE user_type IN ('T', 'P') and is_delete='0' $where_keyword GROUP BY user_type";
        $result = $app->db->conn->query($sql);
        $total = array();
        while ($row = $result->fetch_array()) {
            $total[$row['user_type']] = $row['cnt'];
        }

        $param = array(
            'topmenu' => $topmenu,
            'submenu' => $submenu,
            'type' => $type,
            'type2' => $type2,
            'total' => $total,
            'paging' => $paging,
            'page' => $page,
            'keyword' => $keyword,
        );

        $app->render('investment/pb_trader_list.php', $param);
    });


    // 트레이더별 포트폴리오
    $app->get('/developers/:uid/portfolios', function($uid) use ($app, $log) {
        $topmenu = 'invest';
        $submenu = 'developers';

        $param = array(
            'topmenu' => $topmenu,
            'submenu' => $submenu,
        );

        $app->render('developer_portfolio.php', $param);
    });


    // 상세검색
    $app->get('/search', function() use ($app, $log, $isLoggedIn) {

        $topmenu = 'invest';
        $submenu = 'search';

        $items = $app->db->select('item', '*', array(), array('sorting'=>'asc'));
        $brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
        $kinds = $app->db->select('kind', '*', array(), array('sorting'=>'asc'));
        $types = $app->db->select('type', '*', array(), array('sorting'=>'asc'));

        $param = array(
            'topmenu' => $topmenu,
            'submenu'       => $submenu,
            'items'         => $items,
            'brokers'       => $brokers,
            'kinds'         => $kinds,
            'types'         => $types
        );

        $app->render('investment/strategy_search.php', $param);
    });


    $app->get('/portfolios', function() use ($app, $log) {

        $topmenu = 'invest';
        $submenu = 'portfolio';

        $developer_uid = $app->request->get('developer_uid');
        if (empty($developer_uid)) {
            $submenu = 'portfolio';
            $developer = null;
        } else {
            $submenu = 'developers';
            $sql = "SELECT
                        *,
                        (SELECT count(*) FROM strategy WHERE developer_uid = a.uid AND is_delete='0' AND is_operate='1' AND is_open='1') strategy_cnt,
                        (SELECT count(*) from portfolio WHERE uid=a.uid AND is_open = '1') portfolio_cnt
                    FROM
                        user a
                    WHERE uid = $developer_uid";
            $result = $app->db->conn->query($sql);
            $developer = $result->fetch_assoc();
        }


        $param = array(
            'topmenu' => $topmenu,
            'submenu' => $submenu,
            'developer'     => $developer,
        );

        $app->render('investment/portfolio_list.php', $param);

    });


});


$app->get('/investment/portfolios/write',  $authenticateForRole('N,T,P'), function() use ($app, $log, $isLoggedIn) {
    
    $strategies = $app->request->get('strategies');
    $topmenu = 'invest';
    $submenu = 'portfolio';

    $app->render('investment/portfolio_write.php', array('topmenu'=>$topmenu, 'submenu'=>$submenu, 'strategies'=>$strategies));
});


$app->post('/investment/portfolios/make',  $authenticateForRole('N,T,P'), function() use ($app, $log, $isLoggedIn) {
    
    $input_strategy_ids = $app->request->post('strategy_ids');    
    $portfolio_strategy = $app->request->post('portfolio_strategy');
    $input_exchange = $app->request->post('exchange');

    $portfolio_id = $app->request->post('portfolio_id');
    if ($portfolio_id) {
        $portfolio = $app->db->selectOne('portfolio', '*', array('portfolio_id'=>$portfolio_id));

        $start_date = substr($portfolio['start_date'], 0, 4).'.'.substr($portfolio['start_date'], 4, 2).'.'.substr($portfolio['start_date'], 6, 2);
        $end_date = substr($portfolio['end_date'], 0, 4).'.'.substr($portfolio['end_date'], 4, 2).'.'.substr($portfolio['end_date'], 6, 2);
        $amount = number_format($portfolio['amount']);
        $name = $portfolio['name'];
    } else {

		// 포트폴리오 생성시에는 주어지는 날짜값과 상관없이 무조건 최소일, 최대일을 구함 (2017-05-12)
        //- if (!$start_date) 
		{
            $sql = "SELECT MIN(basedate) basedate
                    FROM 
                        strategy_daily_analysis
                    WHERE 
                        strategy_id IN (".implode(',', $input_strategy_ids).")
                    GROUP BY 
                        strategy_id 
                    ORDER BY basedate ASC LIMIT 0, 1";
            $result = $app->db->conn->query($sql);
            $row = $result->fetch_array();

            $start_date = preg_replace('/[^\d]/', '', $row['basedate']);
			$portfolio['start_date'] = $start_date;
        }

        //- if (!$end_date) 
		{
            $sql = "SELECT MAX(basedate) basedate
                    FROM 
                        strategy_daily_analysis
                    WHERE 
                        strategy_id IN (".implode(',', $input_strategy_ids).")
                    GROUP BY 
                        strategy_id 
                    ORDER BY basedate DESC LIMIT 0, 1";
            $result = $app->db->conn->query($sql);
            $row = $result->fetch_array();

            $end_date = preg_replace('/[^\d]/', '', $row['basedate']);
			$portfolio['end_date'] = $end_date;

			// 포트폴리오 종료일은 무조건 현재날짜 (2017-05-22)
			$portfolio['end_date'] = date("Ymd");
			$end_date = date("Ymd");
        }

        $amount = $app->request->post('amount');
        $name = $app->request->post('name');
    
    }


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

    $temp_strategy_ids = $input_strategy_ids;
    $stg_cnt = count($temp_strategy_ids);
    if ($stg_cnt) {
        if ($app->request->post('percents')) {
            $input_percents = $app->request->post('percents');
            $temp_percents = array();
            foreach($input_percents as $v){
                if(empty($v)) continue;
                $temp_percents[] = $v;
            }
        } else {
            $p = 100;
            for ($i=0; $i<$stg_cnt; $i++) {
				$temp_percents[] = $p;				// 기본 100 퍼센트로 통일함 (2017-03-09 PHPSCHOOL)
                    //- $temp_percents[] = floor(100/$stg_cnt);
            }
            $total_percents = array_sum($temp_percents);
				//            if ($total_percents > 100) {
				//                $temp_percents[0] -= 100-$total_percents;
				//            } else if ($total_percents < 100) {
				//                $temp_percents[0] += 100-$total_percents;
				//            }
        }

        $strategy_ids = array();
        foreach($temp_strategy_ids as $v){
            if(empty($v)) continue;
            if(in_array($v, $strategy_ids)) continue;
            $strategy_ids[] = $v;

        }
    }

    if(count($strategy_ids)){
        $percents = array();
        foreach($temp_percents as $v){
            if(empty($v)) continue;
            $percents[] = $v;
        }
        $portfolio_strategies_map = array();
        foreach($strategy_ids as $k=>$v){
            /**
            echo $k."::".$v."====";
            echo $input_exchange[$k];
            echo "::".$app->request->post('exchange_strategy_'.$v);
            echo "\n";
            */
            $exchange[$k] = $input_exchange[$k] ? $input_exchange[$k] : $app->request->post('exchange_strategy_'.$v) ? $app->request->post('exchange_strategy_'.$v) : '0';
            if ($app->request->post('exchange_strategy_'.$v)) {
                $exchange_map[$v] = $app->request->post('exchange_strategy_'.$v);
            } else {
                $exchange_map[$v] = $input_exchange[$k] ? $input_exchange[$k] : 0;
            }

            $portfolio_strategies_map[$v] = empty($temp_percents[$k]) ? 0 : $temp_percents[$k];
        }
    } else {

        $portfolio_strategies = $app->db->select('portfolio_strategy', '*', array('portfolio_id'=>$portfolio_id));
        $strategy_ids = array();
        $percents = array();
        $stored_portfolio_strategies_map = array();
        $portfolio_strategies_map = array();
        foreach($portfolio_strategies as $v){
            $strategy_ids[] = $v['strategy_id'];
            $percents[] = $v['percents'];
            $exchange_map[$v['strategy_id']] = $v['exchange'];
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
	$aStStats = array('best_pl_rate'=>0, 'best_mdd_rate'=>0, 'worst_pl_rate'=>null, 'worst_mdd_rate'=>null);

    if(count($strategy_ids)){
        $result = $app->db->conn->query('SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $strategy_ids).')');
        while($row = $result->fetch_array()){
            $strategies[] = $row;
        }

        $exist_daily_data_map = array();
        foreach($strategies as $k => $v){
            $daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));
            $daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));
/*
            if(count($daily_values) < 2){
                $app->flash('error', '전략에 입력된 데이터가 부족하여 표시할수 없습니다');
                $app->redirect('/portfolios/'.$id);
            }
*/
            $strategy_daily_values[$v['strategy_id'].''] = $daily_values;

            // 최초일과 마지막일 구함
            $first_date_array[] = $daily_values[0]['basedate'];
            $last_date_array[] = $daily_values[count($daily_values)-1]['basedate'];

            $total_percent += $percents[$k];
            $strategies_percents[$v['strategy_id']] = trim(str_replace('%', '', $percents[$k]));

            $sm_index_array = array();
            foreach($daily_values_graph as $k1=>$v1){
                if ($min_value > $v1['sm_index']) {
                    $min_value = $v1['sm_index'];
                }
            }

            $strategies[$k]['daily_values'] = $daily_values;
            $strategies[$k]['daily_values_graph'] = getSMIndexArray($daily_values_graph, $start_date, $end_date);

            // 표를 그리기 위한 데이터
            //$strategies[$k]['str_c_price'] = '['.implode(',', $sm_index_array ).']';
            $strategies[$k]['str_c_price'] = getChartDataString($strategies[$k]['daily_values_graph'], 'sm_index');

            // 비율
            $strategies[$k]['percents'] = $portfolio_strategies_map[$v['strategy_id']];

            // 환율
            $strategies[$k]['exchange'] = $exchange_map[$v['strategy_id']];

			// 전략 통계 설정
			if($aStStats['best_pl_rate'] < $daily_values[count($daily_values)-1]['acc_pl_rate']) {
				$aStStats['best_pl_rate'] = $daily_values[count($daily_values)-1]['acc_pl_rate'];
				$aStStats['best_mdd_rate'] = $daily_values[count($daily_values)-1]['mdd_rate'];
			}

			if(strlen($aStStats['worst_pl_rate']) < 1 || $aStStats['worst_pl_rate'] > $daily_values[count($daily_values)-1]['acc_pl_rate']) {
				$aStStats['worst_pl_rate'] = $daily_values[count($daily_values)-1]['acc_pl_rate'];
				$aStStats['worst_mdd_rate'] = $daily_values[count($daily_values)-1]['mdd_rate'];
			}

        }

			//        $first_available_date = intval(str_replace('-', '', $first_date_array[0]));
			//        foreach($first_date_array as $v){
			//            $tmp_date = str_replace('-','',$v);
			//            if(intval($tmp_date) < $first_available_date){
			//                $first_available_date = intval($tmp_date);
			//            }
			//        }
			//
			//        $last_available_date = intval(str_replace('-', '', $last_date_array[0]));
			//        foreach($last_date_array as $v){
			//            $tmp_date = str_replace('-','',$v);
			//            if(intval($tmp_date) > $last_available_date){
			//                $last_available_date = intval($tmp_date);
			//            }
			//        }

		$aPlRateInfo = getPortfolioPlRateInfo($app->db, $strategy_ids, $portfolio_strategies_map, date("Y-m-d", strtotime($portfolio['start_date'])), date("Y-m-d", strtotime($portfolio['end_date'])));

        $unified_sm_index = calPortfolioSMIndexGraph($strategies);
		$str_unified_sm_index = getChartDataString($aPlRateInfo['arr_daily_stats'], 'sm_index');
			//- $str_unified_sm_index = getChartDataString($unified_sm_index, 'sm_index');

        // 누적수익률
		$portfolio_total_profit_rate = $aPlRateInfo['pl_rate'];
			//- $portfolio_total_profit_rate = calPortfolioPLrate($unified_sm_index);

        // 누적수익금액
        $portfolio_total_profit = $amount * $portfolio_total_profit_rate/100;
    }

    $app->render('investment/portfolio_make.php', array('developer'=>$developer, 'strategies'=>$strategies, 'min_value'=>$min_value,'str_unified_sm_index'=>$str_unified_sm_index,'portfolio_total_profit'=>$portfolio_total_profit, 'portfolio_total_profit_rate'=>$portfolio_total_profit_rate, 'first_available_date'=>$first_available_date, 'last_available_date'=>$last_available_date,'unified_c_price_array'=>$unified_c_price_array, 'start_date'=>$start_date, 'end_date'=>$end_date, 'amount'=>$amount, 'name'=>$name, 'mdd_rate'=>$aPlRateInfo['mdd_rate'], 'arr_st_stats'=>$aStStats));
});

$app->post('/investment/portfolios/write', function() use ($app, $log, $isLoggedIn) {
    $name = $app->request->post('name');
    $start_date = $app->request->post('start_date');
    $end_date = $app->request->post('end_date');
    $amount = $app->request->post('amount');
    $input_strategy_ids = $app->request->post('strategy_ids');
    $input_percents = $app->request->post('percents');
    $input_exchange = $app->request->post('exchange');
    $open = $app->request->post('open');

    if(empty($name)){
        echo json_encode(array('result'=>false, 'msg'=>'이름을 입력해주세요'));
        $app->stop();
    }

    if(empty($start_date) || strlen(preg_replace('/[^\d]/', '', $start_date)) != 8){
        echo json_encode(array('result'=>false, 'msg'=>'시작날짜를 입력해주세요'));
        $app->stop();
    }else{
        $start_date = preg_replace('/[^\d]/', '', $start_date);
    }

    if(empty($end_date) || strlen(preg_replace('/[^\d]/', '', $end_date)) != 8){
        echo json_encode(array('result'=>false, 'msg'=>'종료날짜를 입력해주세요'));
        $app->stop();
    }else{
        $end_date = preg_replace('/[^\d]/', '', $end_date);
    }

    // 시작일과 종료일의 유효성체크
    $start_date_timestamp = strtotime($start_date);
    $end_date_timestamp = strtotime($end_date);

    if($start_date_timestamp > $end_date_timestamp){
        echo json_encode(array('result'=>false, 'msg'=>'날짜가 올바르지 않습니다'));
        $app->stop();
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
        echo json_encode(array('result'=>false, 'msg'=>'금액을 입력해주세요'));
        $app->stop();
    }else{
        $amount = preg_replace('/[^\d]/', '', $amount);
    }

    if(count($strategy_ids) > 10 || count($percents) > 10){
        echo json_encode(array('result'=>false, 'msg'=>'10개까지 가능합니다'));
        $app->stop();
    }

    if(count($strategy_ids) == 0){
        echo json_encode(array('result'=>false, 'msg'=>'전략을 선택해주세요'));
        $app->stop();
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
            echo json_encode(array('result'=>false, 'msg'=>'전략에 입력된 데이터가 부족합니다'));
            $app->stop();
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
        $exchange[$strategy_id] = $input_exchange[$k];
    }

    if($total_percent < 100 || $total_percent % 100 != 0){
        echo json_encode(array('result'=>false, 'msg'=>'비율 합이 100이어야 합니다'));
        $app->stop();
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
        'end_date'=>$end_date,
        'is_open'=>$open,
    ));

    foreach($strategies_percents as $k => $v){
        $app->db->insert('portfolio_strategy', array(
            'portfolio_id'=>$new_portfolio_id,
            'strategy_id'=>$k,
            'percents'=>$v,
            'exchange'=>$exchange[$k],
        ));
    }
//__v($strategies_percents);
//__v($exchange);
    echo json_encode(array('result'=>true, 'portfolio_id'=>$new_portfolio_id));
});


$app->get('/investment/portfolios/complete', function() use ($app, $log, $isLoggedIn) {
    $portfolio_id = $app->request->get('portfolio_id');
    $topmenu = 'invest';
    $submenu = 'portfolio';

    $app->render('investment/portfolio_complete.php', array('topmenu'=>$topmenu, 'submenu'=>$submenu, 'portfolio_id'=>$portfolio_id));

});

// 포트폴리오 상세
$app->get('/investment/portfolios/:id/delete', function($id) use ($app, $log, $isLoggedIn) {

		$app->db->delete('portfolio', array(
			'portfolio_id'=>$id
		));		

		$app->flash('error', '삭제되었습니다.');
		$app->redirect('/admin/portfolios');
});


// 포트폴리오 상세
$app->get('/investment/portfolios/:id', function($id) use ($app, $log, $isLoggedIn) {

    $topmenu = 'invest';
    $submenu = 'portfolio';

    $portfolio = $app->db->selectOne('portfolio', '*', array('portfolio_id'=>$id));

    if(empty($portfolio)){
        $app->halt(404, 'not found');
    }

	if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250__111'))) {
		// printf("<xmp align='left'>\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n%s</xmp>", print_r($portfolio, true));
	}

	$portfolio['end_date'] = date("Ymd");				// 포트폴리오 종료일은 무조건 현재날짜 (2017-05-22)

    $developer = $app->db->selectOne('user', '*', array('uid'=>$portfolio['uid']));

    $start_date = substr($portfolio['start_date'], 0, 4).'.'.substr($portfolio['start_date'], 4, 2).'.'.substr($portfolio['start_date'], 6, 2);
    $end_date = substr($portfolio['end_date'], 0, 4).'.'.substr($portfolio['end_date'], 4, 2).'.'.substr($portfolio['end_date'], 6, 2);
    $amount = $portfolio['amount'];
    //$portfolio['amount'] = number_format($portfolio['amount']);

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

    $total_percent = 0;
	foreach((array)$percents as $val) {
		$total_percent += $val;
	}

    $min_value = 1000;
    $strategies_percents = array();
    $first_date_array = array();
    $last_date_array = array();
    $strategy_daily_values = array();
    $strategies = array();
    $unified_smindex = array();
    $first_available_date = $portfolio['start_date'];
    $last_available_date = $portfolio['end_date'];
    $unified_c_price_array = array();
	$nTotPricipal = 0;

		//	if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
		//		printf("<xmp align='left'>\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n%s</xmp>", print_r($a, true));
		//		printf("<xmp align='left'>tot_percent : %s</xmp>", print_r($total_percent, true));
		//	}

	$nItemTotPercent = 0;
	$aStats = array('aItemPercent'=>array(), 'aItemMoney'=>array(), 'first_time'=>0, 'last_time'=>0, 'acc_pl_rate'=>0, 'max_acc_pl'=>0,'max_acc_pl_rate'=>0, 'max_profit_days_continue'=>0, 'max_loss_days_continue'=>0, 'after_peak_days'=>0, 'aStItem'=>array());
	$aStItemPercent = array();
	$aStItemMoney = array();
    if(count($strategy_ids)){
        $result = $app->db->conn->query('SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $strategy_ids).')');
        while($row = $result->fetch_array()){
            $strategies[] = $row;
			// $nTotPricipal += $row['principal'];
        }

		$aStDaily = array();
		$aStPercent = array();
        $exist_daily_data_map = array();
        foreach($strategies as $k => $v){
            $daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));
            $daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));
			$st_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$v['strategy_id']));
				/*
							if(count($daily_values) < 2){
								$app->flash('error', '전략에 입력된 데이터가 부족하여 표시할수 없습니다');
								$app->redirect('/portfolios/'.$id);
							}
				*/
            $strategy_daily_values[$v['strategy_id'].''] = $daily_values;

            // 최초일과 마지막일 구함
            $first_date_array[] = $daily_values[0]['basedate'];
            $last_date_array[] = $daily_values[count($daily_values)-1]['basedate'];

            $strategies_percents[$v['strategy_id']] = trim(str_replace('%', '', $percents[$k]));
			$strategies[$k]['relative_percent'] = $percents[$k] / $total_percent;

			foreach((array)$st_items as $st_item_row) {
				$aStItemPercent[$st_item_row['item_id']] += $strategies[$k]['relative_percent'] / count($st_items);
				$aStItemMoney[$st_item_row['item_id']] += ($daily_values[count($daily_values)-1]['principal'] / count($st_items));
				$aStats['aItemMoney'][$st_item_row['item_id']][] = sprintf("%s / %s / %s", $daily_values[count($daily_values)-1]['principal'], count($st_items), $strategies[$k]['relative_percent']);
			}
			$aStats['aItemPercent'][] = $daily_values[count($daily_values)-1]['principal'];
			$aStats['aRelative'][] = $percents[$k] / $total_percent;

            $sm_index_array = array();
            foreach($daily_values_graph as $k1=>$v1){
                if ($min_value > $v1['sm_index'])
                    $min_value = $v1['sm_index'];
            }

            $strategies[$k]['daily_values'] = $daily_values;
            $strategies[$k]['daily_values_graph'] = getSMIndexArray($daily_values_graph, $start_date, $end_date);

            // 표를 그리기 위한 데이터
            //$strategies[$k]['str_c_price'] = '['.implode(',', $sm_index_array ).']';
            $strategies[$k]['str_c_price'] = getChartDataString($strategies[$k]['daily_values_graph'], 'sm_index');

            // 비율
            $strategies[$k]['percents'] = $portfolio_strategies_map[$v['strategy_id']];

			$aStPercent[$v2['strategy_id']] = $strategies[$k]['percents'];

			// 통계 데이터 설정
			$aStats['balance'] += $daily_values[count($daily_values)-1]['balance'];
			$aStats['acc_flow'] += $daily_values[count($daily_values)-1]['acc_flow'];
			$aStats['principal'] += $daily_values[count($daily_values)-1]['principal'];
			$aStats['acc_pl'] += $daily_values[count($daily_values)-1]['acc_pl'];
			$aStats['acc_pl_rate'] += $daily_values[count($daily_values)-1]['acc_pl_rate'];
			$aStats['dd'] += $daily_values[count($daily_values)-1]['dd'];
			$aStats['dd_rate'] += $daily_values[count($daily_values)-1]['dd_rate'];
			$aStats['mdd'] += $daily_values[count($daily_values)-1]['mdd'];
			$aStats['mdd_rate'] += $daily_values[count($daily_values)-1]['mdd_rate'];
			$aStats['avg_pl'] += $daily_values[count($daily_values)-1]['avg_pl'];
			$aStats['avg_pl_rate'] += $daily_values[count($daily_values)-1]['avg_pl_rate'];
			$aStats['max_daily_profit'] += $daily_values[count($daily_values)-1]['max_daily_profit'];
			$aStats['max_daily_profit_rate'] += $daily_values[count($daily_values)-1]['max_daily_profit_rate'];
			$aStats['max_daily_loss'] += $daily_values[count($daily_values)-1]['max_daily_loss'];
			$aStats['max_daily_loss_rate'] += $daily_values[count($daily_values)-1]['max_daily_loss_rate'];

			$aStats['trade_days'] += $daily_values[count($daily_values)-1]['trade_days'];
			$aStats['profit_days'] += $daily_values[count($daily_values)-1]['profit_days'];
			$aStats['loss_days'] += $daily_values[count($daily_values)-1]['loss_days'];
			$aStats['winning_rate'] += $daily_values[count($daily_values)-1]['winning_rate'];
			$aStats['profit_factor'] += $daily_values[count($daily_values)-1]['profit_factor'];
			$aStats['roa'] += $daily_values[count($daily_values)-1]['roa'];

			if($aStats['max_profit_days_continue'] == 0 || $aStats['max_profit_days_continue'] < $daily_values[count($daily_values)-1]['profit_days_continue'])
				$aStats['max_profit_days_continue'] = $daily_values[count($daily_values)-1]['profit_days_continue'];

			if($aStats['max_loss_days_continue'] == 0 || $aStats['max_loss_days_continue'] < $daily_values[count($daily_values)-1]['loss_days_continue'])
				$aStats['max_loss_days_continue'] = $daily_values[count($daily_values)-1]['loss_days_continue'];

			if($aStats['after_peak_days'] == 0 || $aStats['after_peak_days'] < $daily_values[count($daily_values)-1]['after_peak_days'])
				$aStats['after_peak_days'] = $daily_values[count($daily_values)-1]['after_peak_days'];

			if($aStats['max_acc_pl'] == 0 || $aStats['max_acc_pl'] < $daily_values[count($daily_values)-1]['max_acc_pl'])
				$aStats['max_acc_pl'] = $daily_values[count($daily_values)-1]['max_acc_pl'];

			if($aStats['max_acc_pl_rate'] == 0 || $aStats['max_acc_pl_rate'] < $daily_values[count($daily_values)-1]['max_acc_pl_rate'])
				$aStats['max_acc_pl_rate'] = $daily_values[count($daily_values)-1]['max_acc_pl_rate'];

			if($aStats['first_time'] < 1 || $aStats['first_time'] > strtotime($daily_values[0]['basedate']))
				$aStats['first_time'] = strtotime($daily_values[0]['basedate']);

			if($aStats['last_time'] < 1 || $aStats['last_time'] < strtotime($daily_values[count($daily_values)-1]['basedate']))
				$aStats['last_time'] = strtotime($daily_values[count($daily_values)-1]['basedate']);
        }

				$aPlRateInfo = getPortfolioPlRateInfo($app->db, $strategy_ids, $portfolio_strategies_map, date("Y-m-d", strtotime($portfolio['start_date'])), date("Y-m-d", strtotime($portfolio['end_date'])));
				if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250__111'))) {
					printf("<xmp align='left'>\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n%s</xmp>", print_r($portfolio_strategies_map, true));
					printf("<xmp align='left'>[%s] %s    ____    %s</xmp>", $v['portfolio_id'], print_r($aPlRateInfo['pl_rate'], true), $nTotalPlRate);
						//					printf("<xmp align='left'>%s</xmp>", print_r($aStDaily['2013-09-30'], true));
						//					printf("<xmp align='left'>%s</xmp>", print_r($aStDaily['2013-10-01'], true));
						//					printf("<xmp align='left'>%s</xmp>", print_r($aStDaily['2013-10-02'], true));
						//					printf("<xmp align='left'>%s</xmp>", print_r($aStats['aPlRateDay'], true));
						//					printf("<xmp align='left'>%s</xmp>", print_r($aPlRateInfo, true));
				}

		$aStats['total_pl_rate'] = $aPlRateInfo['pl_rate'];			// PB-대표수익률,포폴-누적수익률 > 함수화 (2017-05-07)


		$aStats['acc_pl'] = round($aStats['acc_pl'] / count($strategies));
		$aStats['acc_pl_rate'] = round($aStats['acc_pl_rate'] / count($strategies),2);
		$aStats['dd'] = round($aStats['dd'] / count($strategies));
		$aStats['dd_rate'] = round($aStats['dd_rate'] / count($strategies),2);
		$aStats['mdd'] = round($aStats['mdd'] / count($strategies));
		$aStats['mdd_rate'] = round($aPlRateInfo['mdd_rate'],2);
		$aStats['avg_pl'] = round($aStats['avg_pl'] / count($strategies));
		$aStats['avg_pl_rate'] = round($aStats['avg_pl_rate'] / count($strategies),2);
		$aStats['max_daily_profit'] = round($aStats['max_daily_profit'] / count($strategies));
		$aStats['max_daily_profit_rate'] = round($aStats['max_daily_profit_rate'] / count($strategies),2);
		$aStats['max_daily_loss'] = round($aStats['max_daily_loss'] / count($strategies));
		$aStats['max_daily_loss_rate'] = round($aStats['max_daily_loss_rate'] / count($strategies),2);
		$aStats['trade_days'] = round($aStats['trade_days'] / count($strategies));
		$aStats['profit_days'] = round($aStats['profit_days'] / count($strategies));
		$aStats['loss_days'] = round($aStats['loss_days'] / count($strategies));
		$aStats['winning_rate'] = round($aStats['winning_rate'] / count($strategies),2);
		$aStats['profit_factor'] = round($aStats['profit_factor'] / count($strategies),2);
		$aStats['roa'] = round($aStats['roa'] / count($strategies),2);

		$portfolio_mdd_rate = $aStats['mdd_rate'];

		$nItemIdx = 0;
		$aItemColor = array('blue','orange','gray','red','green','yellow');
		$aItemInfo = array(
			1 => 'K200선물'
			,2 => 'K200옵션'
			,3 => '주식/ETF'
			,9 => '해외선물'
			,10 => '해외옵션'
			,11 => '주식선물'
			,13 => '해외주식'
			,4 => '중국선물'
			,15 => '원달러선물'
			,16 => '국고채선물'
			,17 => '국내ETF'
			,18 => '국내주식'
			,19 => '상품선물'
		);
		foreach((array)$aStItemPercent as $nItemKey => $nItemVal) {
			$nItemVal = $nItemVal * 100;
			$nItemTotPercent += $nItemVal;
			if($nItemTotPercent > 100) {
				$nItemVal -= ($nItemTotPercent - 100);
			}
			$aStats['aStItem'][$nItemKey] = array(
				'item_id' => $nItemKey
				,'title' => $aItemInfo[$nItemKey]
				,'percent' => $nItemVal
				,'principal' => round(($aStats['principal'] * $nItemVal) / 100)
				,'color'=>$aItemColor[$nItemIdx]
				,'money_percent'=> ($aStItemMoney[$nItemKey] / $aStats['principal']) * 100
				,'money' => $aStItemMoney[$nItemKey] / $aStats['principal']
			);
			++$nItemIdx;
		}
			//- printf("<xmp align='left' style='position:absolute; background-color:#fff; padding:10px; z-index:1000; margin-top:250px;'>%s\n%s\nitemMoney:%s\n%s</xmp>", print_r($strategies_percents, true), print_r($aItemInfo, true), print_r($aStItemMoney, true), print_r($aStats, true));

		if(floor(($aStats['last_time'] - $aStats['first_time'])/(60*60*24*30*12)) > 0) $aStats['sOperDays'] = floor(($aStats['last_time'] - $aStats['first_time'])/(60*60*24*30*12)).'년 ';
		if(ceil((($aStats['last_time'] - $aStats['first_time'])%(60*60*24*30*12))/(60*60*24*30)) > 0)
			$aStats['sOperDays'] .= ceil((($aStats['last_time'] - $aStats['first_time'])%(60*60*24*30*12))/(60*60*24*30)).'개월';

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

        $unified_sm_index = calPortfolioSMIndexGraph($strategies);
        	//- $str_unified_sm_index = getChartDataString($unified_sm_index, 'sm_index');
		$str_unified_sm_index = getChartDataString($aPlRateInfo['arr_daily_stats'], 'sm_index');

        // 누적수익률
        $portfolio_total_profit_rate = calPortfolioPLrate($unified_sm_index);

		if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250__111'))) {
			printf("<xmp align='left'>portfolio_total_profit_rate : %s</xmp>", print_r($portfolio_total_profit_rate, true));
			printf("<xmp align='left'>aPlRateInfo : %s</xmp>", print_r($aPlRateInfo, true));
		}

        // 누적수익금액
        $portfolio_total_profit = $amount * $portfolio_total_profit_rate/100;
    }


    if($isLoggedIn()){
        $is_following = $app->db->selectCount('following_portfolio', array('uid'=>$_SESSION['user']['uid'], 'portfolio_id'=>$portfolio['portfolio_id'])) > 0 ? true : false;
    }

    $portfolio['is_following'] = $is_following;

    $app->render('investment/portfolio_detail.php', array('topmenu'=>$topmenu, 'submenu'=>$submenu, 'developer'=>$developer, 'portfolio'=>$portfolio, 'strategies'=>$strategies, 'min_value'=>$min_value,'str_unified_sm_index'=>$str_unified_sm_index,'portfolio_total_profit'=>$portfolio_total_profit, 'portfolio_total_profit_rate'=>$portfolio_total_profit_rate, 'first_available_date'=>$first_available_date, 'last_available_date'=>$last_available_date,'unified_c_price_array'=>$unified_c_price_array,'portfolio_mdd_rate'=>$portfolio_mdd_rate, 'aStats'=>$aStats));

});