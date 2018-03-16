<?php
/**
 */

$app->group('/portfolios', function() use ($app, $log, $isLoggedIn) {


    // 목록 리스트 데이터
    $app->get('/list', function() use ($app, $log, $isLoggedIn) {

        // 페이징 관련 변수
        $count = (!$app->request->get('count')) ? 10 : $app->request->get('count');
        $page_count = 10;
        $page = $app->request->get('page');
        if (empty($page) || !is_numeric($page)) $page = 1;
        $start = ($page - 1) * $count;
        $page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

        $search_type = $app->request->get('search_type');
        $uid = $app->request->get('uid');
        $mine = $app->request->get('mine');
        $sort = $app->request->get('sort');
        if ($sort) {
            $orderby = 'total_profit_rate desc' ;
        } else {
            $orderby = 'portfolio_id desc';
        }

        $where = array();

        if ($search_type == 'mypage') {
            $where[] = "uid = ".$_SESSION['user']['uid'];
        } else if ($search_type == 'favorite') {
            $tmp = explode('|', $app->request->get('portfolios'));
            $portfolio_ids = array_filter(array_map('trim',$tmp));
            $where[]  = "portfolio_id IN ('".implode("', '", $portfolio_ids)."')";
        } else {
            $where[] = 'is_open = 1';
            if ($uid) $where[] = "uid = ".$uid;
        }

        if (count($where)) {
            $where_str = " WHERE ".implode(" AND ", $where);
        }

        $sql = "SELECT * FROM portfolio $where_str ORDER BY $orderby LIMIT $start, $count";

        $lists = array();
        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_array()) {
            $lists[] = $row;
        }

        $idx = $start;
        foreach($lists as $key => $v) {
            if($isLoggedIn()){
                $is_following = $app->db->selectCount('following_portfolio', array('uid'=>$_SESSION['user']['uid'], 'portfolio_id'=>$v['portfolio_id'])) > 0 ? true : false;
            }

            $lists[$key]['is_following'] = $is_following;
            $lists[$key]['idx'] = $idx++;
            $lists[$key]['user'] = $app->db->selectOne('user', '*', array('uid'=>$v['uid']));

			$v['end_date'] = date("Ymd");				// 포트폴리오 종료일은 무조건 현재날짜 (2017-05-22)

            // 그래프 데이터 산출...
            $start_date = substr($v['start_date'], 0, 4).'.'.substr($v['start_date'], 4, 2).'.'.substr($v['start_date'], 6, 2);
            $end_date = substr($v['end_date'], 0, 4).'.'.substr($v['end_date'], 4, 2).'.'.substr($v['end_date'], 6, 2);
            $amount = $v['amount'];

            $portfolio_strategies = $app->db->select('portfolio_strategy', '*', array('portfolio_id'=>$v['portfolio_id']));
            $strategy_ids = array();
            $percents = array();
            $stored_portfolio_strategies_map = array();
            $portfolio_strategies_map = array();
            foreach($portfolio_strategies as $v2){
                $strategy_ids[] = $v2['strategy_id'];
                $percents[] = $v2['percents'];
                $stored_portfolio_strategies_map[$v2['strategy_id']] = $v2['percents'];
                $portfolio_strategies_map[$v2['strategy_id']] = $v2['percents'];
            }

            if(count($strategy_ids)){
                $result = $app->db->conn->query('SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $strategy_ids).')');
                while($row = $result->fetch_array()){
                    $strategies[] = $row;
                }

                $exist_daily_data_map = array();
                foreach($strategies as $k => $v2){
                    $daily_values_graph = $app->db->select('strategy_smindex', '*', array('strategy_id'=>$v2['strategy_id']), array('basedate'=>'asc'));

                    $strategies[$k]['daily_values_graph'] = getSMIndexArray($daily_values_graph, $start_date, $end_date);
                    $strategies[$k]['percents'] = $portfolio_strategies_map[$v2['strategy_id']];
                }


				$aPlRateInfo = getPortfolioPlRateInfo($app->db, $strategy_ids, $portfolio_strategies_map, date("Y-m-d", strtotime($v['start_date'])), date("Y-m-d", strtotime($v['end_date'])));
				if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250__111'))) {
					printf("<xmp align='left'>[%s] %s    ____    %s</xmp>", $v['portfolio_id'], print_r($aPlRateInfo['pl_rate'], true), $nTotalPlRate);
					printf("<xmp align='left'>%s</xmp>", print_r($aStDaily['2013-09-30'], true));
					printf("<xmp align='left'>%s</xmp>", print_r($aStDaily['2013-10-01'], true));
					printf("<xmp align='left'>%s</xmp>", print_r($aStDaily['2013-10-02'], true));
					printf("<xmp align='left'>%s</xmp>", print_r($aStats['aPlRateDay'], true));
					printf("<xmp align='left'>%s</xmp>", print_r($aPlRateInfo, true));
				}

				$lists[$key]['total_pl_rate'] = $aPlRateInfo['pl_rate'];			// PB-대표수익률,포폴-누적수익률 > 함수화 (2017-05-07)
				$lists[$key]['mdd_rate'] = $aPlRateInfo['mdd_rate'];

                $unified_sm_index = calPortfolioSMIndexGraph($strategies);
					//- $lists[$key]['str_unified_sm_index'] = getChartDataString($unified_sm_index, 'sm_index');
				$lists[$key]['str_unified_sm_index'] = getChartDataString($aPlRateInfo['arr_daily_stats'], 'sm_index');

				$lists[$key]['end_date'] = date("Ymd");	
            }
            // 그래프 데이터 산출...
        }

        $param = array(
            'portfolios'=>$lists,
            'search_type' => $search_type,
            'uid' => $uid,
            'more'      => (count($lists) < $count) ? false : true,
        );

        $app->render('portfolios/list.php', $param);
    });


	$app->get('/delete', function () use ($app, $log) {
        $id = $app->request->get('id');

		$portfolio = $app->db->selectOne('portfolio', '*', array('portfolio_id'=>$id));

		if(empty($portfolio)){
			$app->halt(404, 'not found');
		}

		$can_delete = false;
		if ($_SESSION['user']['user_type'] == 'A') {            // 관리자..
			$can_delete = true;
		} else if($portfolio['uid'] == $_SESSION['user']['uid']){
			$can_delete = true;
		} else {

		}

		if ($can_delete){
			$app->db->delete('portfolio', array('portfolio_id'=>$id));
            $app->db->delete('portfolio_strategies', array('portfolio_id'=>$id));
            $app->db->delete('portfolio_review', array('portfolio_id'=>$id));

            $app->db->delete('following_portfolio', array('portfolio_id'=>$id));
		}

        echo json_encode(array('result'=>true));
	});


    // 리뷰리스트
    $app->get('/:id/reviews', function ($id) use ($app, $log, $isLoggedIn) {

        $portfolio = $app->db->selectOne('portfolio', '*', array('portfolio_id'=>$id));

        if(empty($portfolio)){
            $app->halt(404, 'not found');
        }

        $page = $app->request->get('page');
        if(empty($page) || !is_numeric($page)) $page = 1;
        $count = 5;
        $start = ($page - 1) * $count;
        $page_count = 10;
        $page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

        $reviews = $app->db->select('portfolio_review', '*', array('portfolio_id'=>$id), array('review_id'=>'desc'), $start, $count);
        foreach ($reviews as $k=>$review){
            $writer = $app->db->selectOne('user', '*', array('uid'=>$review['writer_uid']));
            $reviews[$k]['writer'] = array('uid'=>$writer['uid'], 'nickname'=>$writer['nickname']);
        }
        $total = $app->db->selectCount('portfolio_review', array('portfolio_id'=>$id));

        $paging = getPaging($page, $total, $page_start, $count, $page_count, 'moveReview');

        $app->render('portfolios/review.php', array('portfolio_id'=>$id, 'reviews'=>$reviews, 'page'=>$page, 'paging'=>$paging));
    });


    // 리뷰등록
    $app->post('/:id/reviews/add', function ($id) use ($app, $log, $isLoggedIn) {
        $portfolio = $app->db->selectOne('portfolio', '*', array('portfolio_id'=>$id));

        if(empty($portfolio)){
            $app->halt(404, 'not found');
        }

        $star = 0;
        $review_body = $app->request->post('review_body');
        if(empty($review_body)){
            echo json_encode(array('result'=>true));
        }

        $values = array(
                    'portfolio_id'   => $id,
                    'rating'        => $star,
                    'contents'      => $review_body,
                    'writer_uid'    => $_SESSION['user']['uid'],
                    'writer_name'   => $_SESSION['user']['nickname']
        );

        $result = $app->db->insert('portfolio_review', $values);

        echo json_encode(array('result'=>true));
        // $app->redirect('/strategies/'.$id.'/reviews');
    });


    // 리뷰 삭제
    $app->post('/:id/reviews/:review_id/delete', function ($id, $review_id) use ($app, $log, $isLoggedIn) {
        $review = $app->db->selectOne('portfolio_review', '*', array('review_id'=>$review_id));

        if (empty($review)) {
            $app->halt(404, 'not found');
        }

        if ($_SESSION['user']['user_type'] == 'A' || $review['writer_uid'] == $_SESSION['user']['uid']) {
            $app->db->delete('portfolio_review', array('review_id'=>$review_id));
            echo json_encode(array('result'=>true));
        } else {
            echo json_encode(array('result'=>false));
        }

    });

    // 포트폴리오 팔로우 등록 폼
    $app->get('/follow/form', function() use ($app, $log, $isLoggedIn) {
        $groups = array();

        if ($isLoggedIn()) {
            $groups = $app->db->select('following_group', '*', array('uid'=>$_SESSION['user']['uid']));
        }

        $param = array(
            'groups'        => $groups,
        );
        $app->render('/portfolios/follow.php', $param);
    });

    // 팔로우등록
    $app->post('/:id/follow',  function($id) use ($app, $log) {
        $group_id = $app->request->post('group_id');
        if($app->db->selectCount('following_portfolio', array('uid'=>$_SESSION['user']['uid'],'portfolio_id'=>$id)) == 0){
            $app->db->insert('following_portfolio', array('uid'=>$_SESSION['user']['uid'],'portfolio_id'=>$id, 'group_id'=>$group_id));
            $app->db->conn->query('UPDATE portfolio SET followers_count = followers_count + 1 WHERE portfolio_id = '.$app->db->conn->real_escape_string($id));
        }

        $type = $app->request->post('type');
        if(!empty($type) && $type == 'json'){
            echo json_encode(array('result'=>true));
            $app->stop();
        }
    });


    // 포트폴리오 언팔로우
    $app->get('/:id/unfollow', function($id) use ($app, $log) {
        if($app->db->selectCount('following_portfolio', array('uid'=>$_SESSION['user']['uid'],'portfolio_id'=>$id)) > 0){
            $app->db->delete('following_portfolio', array('uid'=>$_SESSION['user']['uid'],'portfolio_id'=>$id));
            $app->db->conn->query('UPDATE portfolio SET followers_count = followers_count - 1 WHERE portfolio_id = '.$app->db->conn->real_escape_string($id));
        }

        $type = $app->request->get('type');
        if(!empty($type) && $type == 'json'){
            echo json_encode(array('result'=>true));
            $app->stop();
        }
    });

});