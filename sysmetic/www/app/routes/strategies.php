<?php
/**
 * 투자하기
 */

$app->group('/strategies', function() use ($app, $log, $isLoggedIn) {


    // 전략 목록 리스트 데이터
    $app->get('/list', function() use ($app, $log, $isLoggedIn) {

        // 페이징 관련 변수
		if($app->request->get('list_type')=="make_portfolio"){
			$count = (!$app->request->get('count')) ? 100 : $app->request->get('count');
			$page_count = 100;		
		}else{
			$count = (!$app->request->get('count')) ? 10 : $app->request->get('count');
			$page_count = 10;
		}
        //$count = (!$app->request->get('count')) ? 10 : $app->request->get('count');
        //$page_count = 10;
        $page = $app->request->get('page');
        if (empty($page) || !is_numeric($page)) $page = 1;
        $start = ($page - 1) * $count;
        $page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

        // 검색 조건
        $search['is_operate']    = '1';
        $search['is_delete']     = '0';

        $search_type = $app->request->get('search_type');
        if ($search_type == 'mypage') {
            $search['developer_uid'] = $_SESSION['user']['uid'];
            unset($search['is_operate']);

		} else if ($search_type == 'mypage2') {
            $developer_uid = $app->request->get('developer_uid');
            $search['developer_uid'] = $developer_uid;
            unset($search['is_operate']);
            $search['is_open'] = '1';
        
		} else if ($search_type == 'favorite') {
            $tmp = explode('|', $app->request->get('strategies'));
            $strategy_ids = array_filter(array_map('trim',$tmp));
            $search['strategy_ids']  = $strategy_ids;
        
		} else {
            $developer_uid = $app->request->get('developer_uid');
            $search['developer_uid'] = $developer_uid;
            $search['is_open']       = '1';
        }

        if ($app->request->get('strategies')) {
            $tmp = explode('|', $app->request->get('strategies'));
            $strategy_ids = array_filter(array_map('trim',$tmp));
            $search['strategy_ids']  = $strategy_ids;
        }

        $search['isLoggedIn']    = $isLoggedIn();

        $search['search_type']   = $search_type;
        $search['title']         = $app->request->get('title');

        $search['q_kind']                = $app->request->get('kind');
        $search['q_term']                = $app->request->get('term');
        $search['q_item']                = $app->request->get('item');

        $search['search_kind']           = $app->request->get('search_kind');
        $search['search_term']           = $app->request->get('search_term');
        $search['search_item']           = $app->request->get('search_item');
        $search['search_strategy_type']  = $app->request->get('search_strategy_type');
        $search['search_broker']         = $app->request->get('search_broker');
        $search['search_principal_min']  = $app->request->get('search_principal_min');
        $search['search_principal_max']  = $app->request->get('search_principal_max');
        $search['search_mdd_min']        = $app->request->get('search_mdd_min');
        $search['search_mdd_max']        = $app->request->get('search_mdd_max');
        $search['search_sharp_ratio_min']= $app->request->get('search_sharp_ratio_min');
        $search['search_sharp_ratio_max']= $app->request->get('search_sharp_ratio_max');
        $search['search_profit_type']    = $app->request->get('search_profit_type');
        $search['search_profit_rate']    = $app->request->get('search_profit_rate');
		$search['algorithm']    = $app->request->get('algorism');
		$search['search_keyword']    = $app->request->get('search_keyword');

        $q_sort = $app->request->get('sort');
        $q_sort = (!empty($q_sort)) ? $q_sort : 'sharp_ratio';
        $sort = array(
            'field' => $q_sort,
            'order_by'  => 'desc',
        );

        $strategy = new \Model\Strategy($app->db);
        list($total, $strategies) = $strategy->getList($search, $sort, $start, $count);

        $items = $app->db->select('item', '*', array(), array('sorting'=>'asc'));

        $param = array(
            'search_type'   => $search_type,
            'strategies'    => $strategies,
            'more'          => (count($strategies) < $count) ? false : true,
        );

        switch ($app->request->get('list_type')) {
            case 'search':
                $app->render('strategies/list_search.php', $param);
            break;

            case 'make_portfolio':
                $app->render('strategies/make_portfolio.php', $param);
            break;

            default:
                $app->render('strategies/list.php', $param);
        }
    });

    // 전략 목록 상세 데이터
    $app->get('/view', function() use ($app, $log, $isLoggedIn) {

        $developer_uid = $app->request->get('developer_uid');
        $submenu = empty($developer_uid) ? 'strategy' : 'developers';

        $stg = new \Model\Strategy($app->db);
        $strategy = $stg->getInfo($id, $isLoggedIn);

        // 최소 투자금액
        $min_price = $app->config('strategy.min_price');
        $strategy['min_price'] = $strategy['min_price'] ? $min_price[$strategy['min_price']] : '미등록';

        // 최종 손익 등록일
        $last_update = $app->db->selectOne('strategy_daily_analysis', '*', array('strategy_id'=>$id, 'holiday_flag'=>'0'), array('basedate'=>'desc'), 1);
        $strategy['last_update'] = $last_update['basedate'];


        if (false == $strategy) {
            $app->halt(404, 'not found');
        }

        $strategies_list = '/investment/strategies';
        if ($developer_uid) $strategies_list .= '?developer_uid='.$developer_uid;

        $param = array(
            'submenu'       => $submenu,
            'developer_uid' => $developer_uid,
            'strategy'      => $strategy,
            'strategies_list' => $strategies_list,
        );

        $app->render('strategies/detail.php', $param);
    });
   

	$app->get('/delete', function () use ($app, $log) {
        $id = $app->request->get('id');

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		$can_delete = false;
		if ($_SESSION['user']['user_type'] == 'A') {            // 관리자..
			$can_delete = true;
		} else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
			$can_delete = true;
		} else {

		}

		if ($can_delete){
			// 삭제하지 않고 표시가 안되도록 플래그 처리
			// $app->db->delete('strategy', array('strategy_id'=>$id));
			$app->db->update('strategy', array('is_delete'=>'1'), array('strategy_id'=>$id));
		}

        echo json_encode(array('result'=>true));
	});

    // 전략 상세보기
    $app->get('/:id', function($id) use ($app, $log, $isLoggedIn) {

        $topmenu = 'invest';

        $developer_uid = $app->request->get('developer_uid');
        $submenu = empty($developer_uid) ? 'strategy' : 'developers';

        $stg = new \Model\Strategy($app->db);
        $strategy = $stg->getInfo($id, $isLoggedIn);

        if (false == $strategy) {
            $app->halt(404, 'not found');
        }

        // 최소 투자금액
        $min_price = $app->config('strategy.min_price');
        $strategy['min_price'] = $strategy['min_price'] ? $min_price[$strategy['min_price']] : '미등록';

        // 최종 손익 등록일
        $last_update = $app->db->selectOne('strategy_daily_analysis', '*', array('strategy_id'=>$id, 'holiday_flag'=>'0'), array('basedate'=>'desc'), 1);
        $strategy['last_update'] = $last_update['basedate'];
        //최초 손익 등록일
        $first_update = $app->db->selectOne('strategy_daily_analysis', '*', array('strategy_id'=>$id, 'holiday_flag'=>'0'), array('basedate'=>'asc'), 1);
        $strategy['first_update'] = $first_update['basedate'];
        // 상품종류
        $kinds = $app->db->selectOne('kind', '*', array('kind_id'=>$strategy['strategy_kind']), array('sorting'=>'asc'), 1);
        $strategy['kind'] = $kinds['name'];
        $strategy['types'] = $app->db->selectOne('type', '*', array('type_id'=>$strategy['strategy_type']));

        if ($strategy['pb_uid']) {
            $strategy['pb'] = $app->db->selectOne('user', '*', array('uid' => $strategy['pb_uid']));
        }

        $strategies_list = '/investment/strategies';
        if ($developer_uid) $strategies_list .= '?developer_uid='.$developer_uid;

        $param = array(
            'topmenu'       => $topmenu,
            'submenu'       => $submenu,
            'developer_uid' => $developer_uid,
            'strategy'      => $strategy,
            'strategies_list' => $strategies_list,
        );

        $app->render('investment/strategy_detail.php', $param);
    });

    // 전략 상세보기 출력
    $app->get('/:id/print', function($id) use ($app, $log, $isLoggedIn) {

        $topmenu = 'invest';

        $developer_uid = $app->request->get('developer_uid');
        $submenu = empty($developer_uid) ? 'strategy' : 'developers';

        $stg = new \Model\Strategy($app->db);
        $strategy = $stg->getInfo($id, $isLoggedIn);

        if (false == $strategy) {
            $app->halt(404, 'not found');
        }

        // 최소 투자금액
        $min_price = $app->config('strategy.min_price');
        $strategy['min_price'] = $strategy['min_price'] ? $min_price[$strategy['min_price']] : '미등록';

        // 최종 손익 등록일
        $last_update = $app->db->selectOne('strategy_daily_analysis', '*', array('strategy_id'=>$id, 'holiday_flag'=>'0'), array('basedate'=>'desc'), 1);
        $strategy['last_update'] = $last_update['basedate'];
        //최초 손익 등록일
        $first_update = $app->db->selectOne('strategy_daily_analysis', '*', array('strategy_id'=>$id, 'holiday_flag'=>'0'), array('basedate'=>'asc'), 1);
        $strategy['first_update'] = $first_update['basedate'];
        // 상품종류
        $kinds = $app->db->selectOne('kind', '*', array('kind_id'=>$strategy['strategy_kind']), array('sorting'=>'asc'), 1);
        $strategy['kind'] = $kinds['name'];
        $strategy['types'] = $app->db->selectOne('type', '*', array('type_id'=>$strategy['strategy_type']));

        if ($strategy['pb_uid']) {
            $strategy['pb'] = $app->db->selectOne('user', '*', array('uid' => $strategy['pb_uid']));
        }

        $strategies_list = '/investment/strategies';
        if ($developer_uid) $strategies_list .= '?developer_uid='.$developer_uid;

        $param = array(
            'topmenu'       => $topmenu,
            'submenu'       => $submenu,
            'developer_uid' => $developer_uid,
            'strategy'      => $strategy,
            'strategies_list' => $strategies_list,
        );

        $app->render('investment/strategy_detail_print.php', $param);
    });


    // 제안서 다운로드
    $app->get('/:id/download', function($id) use ($app, $log) {

        $files = $app->db->selectOne('strategy_file', '*', array('strategy_id'=>$id));

        if(empty($files)){
            $app->halt(404, 'not found');
        }

        $savePath = $app->config('strategy.path');
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


    // 전략 상세보기 통계
    $app->get('/:id/info', function($id) use ($app, $log) {

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

        if (empty($strategy)) {
            $app->halt(404, 'not found');
        }

        // 산식
        $strategy['daily_values'] = array($app->db->selectOne('strategy_daily_analysis', '*', array('strategy_id'=>$strategy['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'desc')));
		$strategy['aMaxContinue'] = $app->db->selectOne('strategy_daily_analysis', ' MAX(profit_days_continue) as max_profit_days_continue, MAX(loss_days_continue) as max_loss_days_continue, MIN(basedate) as start_day, MAX(basedate) as end_day, COUNT(*) as tot_days ', array('strategy_id'=>$id, 'holiday_flag'=>'0'));

        $app->render('strategies/info.php', array('strategy' => $strategy));
    });


    // 전략 상세보기 일별통계
    $app->get('/:id/daily', function($id) use ($app, $log) {

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

        if (empty($strategy)) {
            $app->halt(404, 'not found');
        }

        $page = $app->request->get('page');
        if (empty($page) || !is_numeric($page)) $page = 1;
        $count = 10;
        $start = ($page - 1) * $count;
        $page_count = 10;
        $page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

        $daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$id), array('basedate'=>'desc'), $start, $count);
        $total = $app->db->selectCount('strategy_daily_analysis', array('strategy_id'=>$id));
        $total_page = ceil($total / $count);

        $param = array(
                    'current_menu'  => $current_menu,
                    'strategy'      => $strategy,
                    'daily_values'  => $daily_values,
                    'page'          => $page,
                    'page_count'    => $page_count,
                    'page_start'    => $page_start,
                    'total_page'    => $total_page,
                    'total'         => $total,
                    'count'         => $count
        );

        $app->render('strategies/daily.php', $param);
    });


    // 전략 상세보기 월별통계
    $app->get('/:id/monthly', function($id) use ($app, $log) {

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

        if(empty($strategy)){
            $app->halt(404, 'not found');
        }

        $page = $app->request->get('page');
        if (empty($page) || !is_numeric($page)) $page = 1;
        $count = 10;
        $start = ($page - 1) * $count;
        $page_count = 10;
        $page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

        $monthly_values = $app->db->select('strategy_monthly_analysis', '*', array('strategy_id'=>$id), array('baseyear'=>'desc','basemonth'=>'desc'), $start, $count);
        $total = $app->db->selectCount('strategy_monthly_analysis', array('strategy_id'=>$id));
        $total_page = ceil($total / $count);

        $param = array(
                    'current_menu'  => $current_menu,
                    'strategy'      => $strategy,
                    'daily_values'  => $monthly_values,
                    'page'          => $page,
                    'page_count'    => $page_count,
                    'page_start'    => $page_start,
                    'total_page'    => $total_page,
                    'total'         => $total,
                    'count'         => $count
        );

        $app->render('strategies/monthly.php', $param);
    });


    // 전략 상세보기 계좌
    $app->get('/:id/accounts', function ($id) use ($app, $log, $isLoggedIn) {
        $current_menu = 'strategies';

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

        if(empty($strategy)){
            $app->halt(404, 'not found');
        }

        $page = $app->request->get('page');
        if(empty($page) || !is_numeric($page)) $page = 1;
        $count = 20;
        $start = ($page - 1) * $count;
        $page_count = 10;
        $page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

        $monthly_values = $app->db->select('strategy_account', '*', array('strategy_id'=>$id), array('account_id'=>'desc'), $start, $count);
        $total = $app->db->selectCount('strategy_account', array('strategy_id'=>$id));
        $total_page = ceil($total / $count);

        $param = array(
                    'current_menu'  => $current_menu,
                    'strategy'      => $strategy,
                    'monthly_values'=> $monthly_values,
                    'page'          => $page,
                    'page_count'    => $page_count,
                    'page_start'    => $page_start,
                    'total_page'    => $total_page,
                    'total'         => $total,
                    'count'         => $count
        );

        $app->render('strategies/accounts.php', $param);
    });


    // 엑셀 다운로드
    $app->get('/:id/dailyall/download', function ($id) use ($app, $log, $isLoggedIn) {
        $current_menu = 'strategies';

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

        if(empty($strategy)){
            $app->halt(404, 'not found');
        }

        $daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$id), array('basedate'=>'desc'));

        if(count($daily_values) < 2){
            $app->redirect('/strategies/'.$id);
        }

        // application/octet-stream
        $app->response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $app->response->headers->set('Pragma', 'dummy=bogus');
        $app->response->headers->set('Cache-Control', 'private');

        // 한글 안깨지도록 보정
        $original_name = stripslashes($strategy['name'])."_daily_all";

        if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')){
            $original_name = $original_name;
        } else {
            $original_name = urlencode($original_name);
        }

        $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$original_name.'.xls"');
        $contents = $app->view->fetch('excel/strategy_day_all.php', array('daily_values' => $daily_values));

        echo $contents;
    });


    $app->get('/:id/daily/download', function ($id) use ($app, $log, $isLoggedIn) {
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

        if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')){
            $original_name = $original_name;
        } else {
            $original_name = urlencode($original_name);
        }

        $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$original_name.'.xls"');
        $contents = $app->view->fetch('excel/strategy_day.php', array('daily_values' => $daily_values));

        echo $contents;
    });


    $app->get('/:id/monthly/download', function ($id) use ($app, $log, $isLoggedIn) {
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

        $original_name = stripslashes($strategy['name'])."_monthly";

        if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')){
            $original_name = $original_name;
        } else {
            $original_name = urlencode($original_name);
        }

        $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$original_name.'.xls"');
        $contents = $app->view->fetch('excel/strategy_month.php', array('monthly_values' => $monthly_values));

        echo $contents;
    });


    // follow
    $app->get('/follow/form', function() use ($app, $log, $isLoggedIn) {
        $groups = array();
        if ($isLoggedIn()) {
            $groups = $app->db->select('following_group', '*', array('uid'=>$_SESSION['user']['uid']));
        }

        $param = array(
            'groups'        => $groups,
        );
        $app->render('strategies/follow.php', $param);
    });


    // 그룹등록
    $app->post('/follow/group', function() use ($app, $log, $isLoggedIn) {
        $group_name = $app->request->post('group_name');
        $values = array('uid' => $_SESSION['user']['uid'], 'group_name' => $group_name);
        $group_id = $app->db->insert("following_group", $values);
        $result = ($group_id) ? $group_id : false;
        echo json_encode(array('result'=>$result));
        $app->stop();
    });


    // 팔로우등록
    $app->post('/:id/follow',  function($id) use ($app, $log) {
        $group_id = $app->request->post('group_id');
        if($app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id)) == 0){
            $app->db->insert('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id, 'group_id'=>$group_id));
            $app->db->conn->query('UPDATE strategy SET followers_count = followers_count + 1 WHERE strategy_id = '.$app->db->conn->real_escape_string($id));
        }

        $type = $app->request->post('type');
        if(!empty($type) && $type == 'json'){
            echo json_encode(array('result'=>true));
            $app->stop();
        }
//        $app->redirect('/strategies');
    });


    $app->get('/:id/unfollow', function($id) use ($app, $log) {
        if($app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id)) > 0){
            $app->db->delete('following_strategy', array('uid'=>$_SESSION['user']['uid'],'strategy_id'=>$id));
            $app->db->conn->query('UPDATE strategy SET followers_count = followers_count - 1 WHERE strategy_id = '.$app->db->conn->real_escape_string($id));
        }

        $type = $app->request->get('type');
        if(!empty($type) && $type == 'json'){
            echo json_encode(array('result'=>true));
            $app->stop();
        }
 //       $app->redirect('/strategies');
    });


    // 전략 문의
    $app->post('/:id/ask', function($id) use ($app, $log) {
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


    // 전략 투자하기
    $app->post('/:id/invest', function($id) use ($app, $log) {

        $mobile = $app->request->post('mobile');
        $email = $app->request->post('email');
        $s_price = $app->request->post('s_price');
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


    // 리뷰리스트
    $app->get('/:id/reviews', function ($id) use ($app, $log, $isLoggedIn) {

        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

        if(empty($strategy)){
            $app->halt(404, 'not found');
        }

        $page = $app->request->get('page');
        if(empty($page) || !is_numeric($page)) $page = 1;
        $count = 10;
        $start = ($page - 1) * $count;
        $page_count = 10;
        $page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

        $reviews = $app->db->select('strategy_review', '*', array('strategy_id'=>$id), array('review_id'=>'desc'), $start, $count);
        foreach ($reviews as $k=>$review){
            $writer = $app->db->selectOne('user', '*', array('uid'=>$review['writer_uid']));
            $reviews[$k]['writer'] = array('uid'=>$writer['uid'], 'nickname'=>$writer['nickname']);
        }
        $total = $app->db->selectCount('strategy_review', array('strategy_id'=>$id));

        $paging = getPaging($page, $total, $page_start, $count, $page_count, 'moveReview');

        $app->render('strategies/review.php', array('strategy_id'=>$id, 'reviews'=>$reviews, 'page'=>$page, 'paging'=>$paging));

        // $app->render('strategy_view_tab4.php', array('current_menu'=>$current_menu, 'strategy'=>$strategy, 'reviews'=>$reviews, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
    });


    // 리뷰등록
    $app->post('/:id/reviews/add', function ($id) use ($app, $log, $isLoggedIn) {
        $strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

        if(empty($strategy)){
            $app->halt(404, 'not found');
        }

        /*
        $star = $app->request->post('star');
        if(empty($star) || !is_numeric($star)){
            $app->redirect('/strategies/'.$id.'/reviews');
        }
        */

        $star = 0;
        $review_body = $app->request->post('review_body');
        if(empty($review_body)){
            echo json_encode(array('result'=>true));
        }

        $values = array(
                    'strategy_id'   => $id,
                    'rating'        => $star,
                    'contents'      => $review_body,
                    'writer_uid'    => $_SESSION['user']['uid'],
                    'writer_name'   => $_SESSION['user']['nickname']
        );

        $result = $app->db->insert('strategy_review', $values);

        echo json_encode(array('result'=>true));
        // $app->redirect('/strategies/'.$id.'/reviews');
    });


    // 리뷰 삭제
    $app->post('/:id/reviews/:review_id/delete', function ($id, $review_id) use ($app, $log, $isLoggedIn) {
        $review = $app->db->selectOne('strategy_review', '*', array('review_id'=>$review_id));

        if (empty($review)) {
            $app->halt(404, 'not found');
        }

        if ($_SESSION['user']['user_type'] == 'A' || $review['writer_uid'] == $_SESSION['user']['uid']) {
            $app->db->delete('strategy_review', array('review_id'=>$review_id));
            echo json_encode(array('result'=>true));
        } else {
            echo json_encode(array('result'=>false));
        }

    });

    
});