<?php
$app->group('/admin', $authenticateForRole('A'), function () use ($app, $log) {

	$app->get('/strategies', function () use ($app, $log) {
		$current_menu = 'admin_strategies';

        $op_cnt = $app->db->selectCount('strategy', array('is_delete'=>'0', 'is_operate'=>'0'));
        $grps = $app->db->select('strategy_grp', '*', array('uid'=>$_SESSION['user']['uid']), array('name'=>'asc'));

        $q_type = $app->request->get('q_type');
		$q = $app->request->get('q');
        $q_stats = $app->request->get('q_stats');
        $q_term = $app->request->get('q_term');
        $q_types = $app->request->get('q_types');
        $q_grp = $app->request->get('q_grp');

        if (empty($q_type)) {
            $q_type = 'name';
            $q_str = '&q_type='.$q_type;
        }

		if (!empty($q)) {
            switch ($q_type) {
                case 'name':
			        $where[] = " a.name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'trader':
			        $where[] = " (d.name LIKE '%".$app->db->conn->real_escape_string($q)."%' or d.nickname LIKE '%".$app->db->conn->real_escape_string($q)."%')";
                    break;

                case 'pb':
			        $where[] = " c.name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'broker':
			        $where[] = " e.company LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;
            }
            $q_str = '&q='.urlencode($q);
		}
        
        if (!empty($q_stats)) {
            switch ($q_stats) {
                case 'is_open_1': $where[] = "a.is_open = '1'"; break;
                case 'is_open_0': $where[] = "a.is_open = '0'"; break;
                case 'is_operate_1': $where[] = "a.is_operate = '1'"; break;
                case 'is_operate_0': $where[] = "a.is_operate = '0'"; break;
            }
            $q_str = '&q_stats='.$q_stats;
        }
        
        if (!empty($q_term)) {
            $where[] = "a.strategy_term = '".$q_term."'";
            $q_str = '&q_term='.$q_term;
        }
        
        if (!empty($q_types)) {
            //$where[] = "strategy_term = '".$q_term."'";
            $q_str = '&q_types='.$q_types;
        }

        if (!empty($q_type)) {
            //$where[] = "strategy_term = '".$q_term."'";
            $q_str = $q_str.'&q_type='.$q_type;
        }

        if (!empty($q_grp)) {
            $where[] = "b.grp_id = '".$q_grp."'";
            $q_str = '&q_grp='.$q_grp;
        }

        if (is_array($where)) {
            $where_str = " AND ".implode(" AND ", $where);
        }

		$sql = 'SELECT a.* 
                FROM 
                    strategy a 
                    LEFT JOIN strategy_adm_grp b ON a.strategy_id=b.strategy_id 
                    LEFT JOIN user c ON a.pb_uid = c.uid
                    INNER JOIN user d ON a.developer_uid = d.uid 
                    INNER JOIN broker e ON a.broker_id = e.broker_id
                WHERE a.is_delete = \'0\''.$where_str;
         

		$total_sql = 'SELECT COUNT(*) 
                        FROM 
                            strategy a 
                            LEFT JOIN strategy_adm_grp b ON a.strategy_id=b.strategy_id 
                            LEFT JOIN user c ON a.pb_uid = c.uid
                            INNER JOIN user d ON a.developer_uid = d.uid 
                            INNER JOIN broker e ON a.broker_id = e.broker_id
                        WHERE a.is_delete = \'0\''.$where_str;

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 20;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$sql .= " ORDER BY a.strategy_id DESC LIMIT $start, $count";

		$strategies = array();

        //유형
        $types = $app->db->select('type', '*', array(), array('sorting'=>'asc'));

        // 운용사
		$_broker = $app->db->select('broker', 'broker_id, company', array(), array('sorting'=>'asc'));
        foreach ($_broker as $v) {
            $broker[$v['broker_id']] = $v['company'];
        }

		$result = $app->db->conn->query($sql);

		while($row = $result->fetch_array()){
            if ($row['developer_uid']) {
			    $developer = $app->db->selectOne('user', '*', array('uid'=>$row['developer_uid']));
                $row['developer'] = $developer;
            }

            if ($row['pb_uid']) {
			    $pb = $app->db->selectOne('user', '*', array('uid'=>$row['pb_uid']));
    			$row['pb'] = $pb;
            }

            $row['broker_name'] = $broker[$row['broker_id']];
			$strategies[] = $row;
		}

		$total_result = $app->db->conn->query($total_sql);
		$total_result_row = $total_result->fetch_array();
		$total = $total_result_row[0];

		$total_page = ceil($total / $count);

        $param = array(
                    'current_menu'=>$current_menu,
                    'op_cnt'=>$op_cnt,
                    'strategies'=>$strategies,
                    'page'=>$page,
                    'page_count'=>$page_count,
                    'page_start' =>$page_start,
                    'total_page'=>$total_page,
                    'total'=>$total,
                    'count'=>$count,
                    'types'=>$types,
                    'q_type'=>$q_type,
                    'q'=>$q,
                    'q_str'=>$q_str,
                    'q_stats'=>$q_stats,
                    'q_term'=>$q_term,
                    'q_types'=>$q_types,
                    'q_grp' => $q_grp,
                    'grps' => $grps
            );

		$app->render('admin/strategy.php', $param);
	});


    // 관심그룹
	$app->get('/strategies_grp', function () use ($app, $log) {
		$current_menu = 'admin_items';

        $op_cnt = $app->db->selectCount('strategy', array('is_delete'=>'0', 'is_operate'=>'0'));
		$grp = $app->db->select('strategy_grp', '*', array('uid'=>$_SESSION['user']['uid']), array('name'=>'asc'));

		$app->render('admin/strategy_grp.php', array('current_menu'=>$current_menu, 'grp'=>$grp, 'op_cnt'=>$op_cnt));
	});

	$app->post('/strategies_grp/set', function () use ($app, $log) {

        $q_type = $app->request->post('q_type');
		$q = $app->request->post('q');
        $q_stats = $app->request->post('q_stats');
        $q_term = $app->request->post('q_term');
        $q_types = $app->request->post('q_types');
        $q_grp = $app->request->post('q_grp');
        $grp_id = $app->request->post('grp_id');
        
        $strategy_ids = $app->request->post('strategy_ids');
        /*
        if (empty($q_type)) {
            $q_type = 'name';
            $q_str = '&q_type='.$q_type;
        }

		if (!empty($q)) {
            switch ($q_type) {
                case 'name':
			        $where[] = " name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'trader':
                    break;

                case 'pb':
                    break;
            }
		}
        
        if (!empty($q_stats)) {
            switch ($q_stats) {
                case 'is_open_1': $where[] = "is_open = '1'"; break;
                case 'is_open_0': $where[] = "is_open = '0'"; break;
                case 'is_operate_1': $where[] = "is_operate = '1'"; break;
                case 'is_operate_0': $where[] = "is_operate = '0'"; break;
            }
        }
        
        if (!empty($q_term)) {
            $where[] = "strategy_term = '".$q_term."'";
        }
        
        if (!empty($q_types)) {
        }

        if (!empty($q_grp)) {
            $where[] = "b.grp_id = '".$q_grp."'";
        }

        if (is_array($where)) {
            $where_str = " AND ".implode(" AND ", $where);
        }

        */
        $where_str = ' AND a.strategy_id IN ('.implode(',', $strategy_ids).')';

		$sql = 'SELECT a.strategy_id, b.adm_grp_id FROM strategy a LEFT JOIN strategy_adm_grp b ON a.strategy_id=b.strategy_id WHERE is_delete = \'0\''.$where_str;
		$sql .= " ORDER BY a.strategy_id DESC";

		$result = $app->db->conn->query($sql);

		while($row = $result->fetch_array()){
			$strategies[] = $row;
		}

        if (is_array($strategies)) {
            foreach ($strategies as $v) {
                $strategy_id = $v['strategy_id'];
                
                if ($grp_id) {
                    if ($v['adm_grp_id']) {
                        $app->db->update('strategy_adm_grp', array('grp_id'=>$grp_id), array('adm_grp_id'=>$v['adm_grp_id']));
                    } else {
                        $app->db->insert('strategy_adm_grp', array('strategy_id'=>$strategy_id, 'grp_id'=>$grp_id));
                    }
                } else {
                    $app->db->delete('strategy_adm_grp', array('adm_grp_id'=>$v['adm_grp_id']));
                }
            }
        }

		$app->redirect('/admin/strategies');
	});

	$app->post('/strategies_grp/add', function () use ($app, $log) {
		$name = $app->request->post('name');

		if(empty($name)){
			$app->flash('error', '종류를 입력하세요');
			$app->redirect('/admin/strategies_grp');
		}

        $cnt = $app->db->selectCount('strategy_grp', array('name'=>$name, 'uid'=>$_SESSION['user']['uid']));
        if ($cnt) {
			$app->flash('error', "동일한 이름의 그룹이 있습니다");
			$app->redirect('/admin/strategies_grp');
        }

		$app->db->insert('strategy_grp', array(
			'name'=>$name,
            'uid'=>$_SESSION['user']['uid'],
		));

		$app->redirect('/admin/strategies_grp');
	});

	$app->post('/strategies_grp/edit', function () use ($app, $log) {
		$edit_grp_id = $app->request->post('edit_grp_id');
		$name = $app->request->post('name');
		$sorting = $app->request->post('sorting');

		if(empty($edit_grp_id)){
			$app->halt(404, 'not found');
		}

		if(empty($name)){
			$app->flash('error', '종류를 입력하세요');
			$app->redirect('/admin/strategies_grp');
		}

		if(empty($sorting) || !is_numeric($sorting)){
			$sorting = 1;
		}

		$grp = $app->db->selectOne('strategy_grp', '*', array('grp_id'=>$edit_grp_id));

		if(empty($grp)){
			$app->halt(404, 'not found');
		}

		$app->db->update('strategy_grp', array(
			'name'=>$name
		), array('grp_id'=>$edit_grp_id));

		$app->redirect('/admin/strategies_grp');
	});

	$app->get('/strategies_grp/:id/delete', function ($id) use ($app, $log) {
		$cate = $app->db->selectOne('strategy_grp', '*', array('grp_id'=>$id));

		if(empty($cate)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('strategy_grp', array('grp_id'=>$id));

		$app->redirect('/admin/strategies_grp');
	});



    // 승인요청
	$app->get('/strategies_op', function () use ($app, $log) {
		$current_menu = 'admin_strategies';

        $op_cnt = $app->db->selectCount('strategy', array('is_delete'=>'0', 'is_operate'=>'0'));

        $q_type = $app->request->get('q_type');
        if (empty($q_type)) {
            $q_type = 'name';
        }

		$q = $app->request->get('q');
		if (!empty($q)) {
            switch ($q_type) {
                case 'name':
			        $where .= " AND name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'trader':
                    break;

                case 'pb':
                    break;
            }

            $q_str = '&q_type='.$q_type.'&q='.urlencode($q);
		}


		$sql = 'SELECT * FROM strategy WHERE is_delete = \'0\' AND is_operate = \'0\''.$where;
		$total_sql = 'SELECT COUNT(*) FROM strategy WHERE is_delete = \'0\' AND is_operate = \'0\''.$where;

		$condition = array('is_delete'=>'0');

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 20;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$sql .= " ORDER BY strategy_id DESC LIMIT $start, $count";

		$strategies = array();

        // 운용사
		$_broker = $app->db->select('broker', 'broker_id, company', array(), array('sorting'=>'asc'));
        foreach ($_broker as $v) {
            $broker[$v['broker_id']] = $v['company'];
        }

		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_array()){
			$developer = $app->db->selectOne('user', '*', array('uid'=>$row['developer_uid']));
			$row['developer'] = $developer;
            $row['broker_name'] = $broker[$row['broker_id']];
			$strategies[] = $row;
		}

		$total_result = $app->db->conn->query($total_sql);
		$total_result_row = $total_result->fetch_array();
		$total = $total_result_row[0];

		$total_page = ceil($total / $count);

		$app->render('admin/strategy_op.php', array('current_menu'=>$current_menu, 'op_cnt'=>$op_cnt, 'strategies'=>$strategies, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count, 'q_type'=>$q_type, 'q'=>$q));
	});



    // 승인
	$app->post('/strategies/operate', function () use ($app, $log) {
        
        $strategies = implode(',', $app->request->post('ids'));
        $sql = "update strategy set is_operate = '1' where strategy_id in ($strategies)";
        $app->db->conn->query($sql);

		$app->redirect('/admin/strategies_op');

    });


	$app->get('/portfolios', function () use ($app, $log) {

        $op_cnt = $app->db->selectCount('strategy', array('is_delete'=>'0', 'is_operate'=>'0'));

        $q_type = $app->request->get('q_type');
        if(empty($q_type)) {
            $q_type = 'name';
        }
		$q = $app->request->get('q');
		if(!empty($q)){

            switch ($q_type) {
                case 'name':
			        $where .= " WHERE a.name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'uid':
			        $where .= " WHERE b.nickname LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;
            }

            $q_str = '&q_type='.$q_type.'&q='.urlencode($q);
		}



		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 20;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$total = $app->db->selectCount('portfolio', array());

        $total_sql = "SELECT count(*) FROM portfolio a INNER JOIN user b ON a.uid = b.uid".$where;
		$total_result = $app->db->conn->query($total_sql);
		$total_result_row = $total_result->fetch_array();
		$total = $total_result_row[0];


		$total_page = ceil($total / $count);

        $sql = "SELECT a.*, b.nickname
                FROM portfolio a INNER JOIN user b ON a.uid = b.uid
                $where 
                ORDER BY a.portfolio_id DESC 
                LIMIT $start, $count";
		$result = $app->db->conn->query($sql);
        $portfolios = array();
		while($row = $result->fetch_array()){
			$portfolios[] = $row;
		}

        $param = array(
            'current_menu'=>$current_menu, 
            'portfolios'=>$portfolios, 
            'op_cnt'=>$op_cnt, 
            'page'=>$page, 
            'page_count'=>$page_count, 
            'page_start' =>$page_start, 
            'total_page'=>$total_page, 
            'total'=>$total, 
            'count'=>$count, 
            'q_type'=>$q_type,
            'q'=>$q,
            'q_str'=> $q_str,
        );

		$app->render('admin/portfolios.php', $param);
    });

	$app->get('/strategies/write', function () use ($app, $log) {
		$current_menu = 'admin_strategies';

		// 종목
		$items = $app->db->select('item', '*', array(), array('item_id'=>'desc'));

		// 브로커
		$brokers = $app->db->select('broker', '*', array(), array('broker_id'=>'desc'));
		$company_type1 = array();
		$company_type2 = array();
		foreach($brokers as $broker){
			if($broker['company_type'] == '증권사') $company_type1[] = array('id'=>$broker['broker_id'], 'name'=>$broker['company']);
			else if($broker['company_type'] == '선물사') $company_type2[] = array('id'=>$broker['broker_id'], 'name'=>$broker['company']);
		}

		// 매매툴
		$tools = array();
		$result = $app->db->conn->query('SELECT * FROM system_trading_tool JOIN broker ON system_trading_tool.broker_id = broker.broker_id WHERE broker.is_open = \'1\'');
		while($row = $result->fetch_array()){
			// if(empty($row['name'])) continue;
			// if(in_array($row['name'], $tools)) continue;
			if(!isset($tools['broker'.$row['broker_id']])) $tools['broker'.$row['broker_id']] = array();
			$tools['broker'.$row['broker_id']][] = $row;
		}

		$app->render('admin/strategy_write.php', array('current_menu'=>$current_menu, 'items'=>$items, 'company_type1'=>$company_type1,'company_type2'=>$company_type2, 'tools'=>$tools));
	});

	$app->post('/strategies/write', function () use ($app, $log) {
		$name = $app->request->post('name');
		$broker_type = $app->request->post('broker_type');
		$broker_id = $app->request->post('broker_id');
		$input_item_ids = $app->request->post('item_ids');
		$tool_id = $app->request->post('tool_id');
		$currency = $app->request->post('currency');
		$investment = $app->request->post('investment');
		$term = $app->request->post('term');
		$intro = $app->request->post('intro');

		if(empty($name)){
			$app->flash('error', '전략명을 입력하세요');
			$app->redirect('/admin/strategies/write');
		}

		if(empty($broker_type)){
			$app->flash('error', '브로커를 선택하세요');
			$app->redirect('/admin/strategies/write');
		}

		if(empty($broker_id) || !is_numeric($broker_id)){
			$app->flash('error', '브로커를 선택하세요');
			$app->redirect('/admin/strategies/write');
		}

		$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$broker_id));

		if(empty($broker)){
			$app->flash('error', '브로커를 선택하세요');
			$app->redirect('/admin/strategies/write');
		}

		if(empty($tool_id)){
			$app->flash('error', '매매툴을 선택하세요');
			$app->redirect('/admin/strategies/write');
		}

		$tool = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$tool_id));

		if(empty($tool)){
			$app->flash('error', '매매툴을 선택하세요');
			$app->redirect('/admin/strategies/write');
		}

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

		$new_strategy_id = $app->db->insert('strategy', array(
			'name'=>$name,
			'broker_type'=>$broker_type,
			'broker_id'=>$broker_id,
			'developer_uid'=>$_SESSION['user']['uid'],
			'developer_name'=>empty($_SESSION['user']['nickname']) ? '' : $_SESSION['user']['nickname'],
			'tool_id'=>$tool_id,
			'currency'=>$currency,
			'investment'=>$investment,
			'strategy_term'=>$term,
			'intro'=>$intro,
			'is_open'=>'0'
		));

		foreach($input_item_ids as $v){
			if(empty($v)) continue;
			$app->db->insert('strategy_item', array(
				'strategy_id'=>$new_strategy_id,
				'item_id'=>$v
			));
		}

		$app->redirect('/admin/strategies');
	});

	$app->get('/strategies/:id/delete', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		$can_delete = false;
		if($_SESSION['user']['user_type'] == 'A'){
			$can_delete = true;
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
			$can_delete = true;
		}else{
		}

		if($can_delete){
			// 삭제하지 않고 표시가 안되도록 플래그 처리
			// $app->db->delete('strategy', array('strategy_id'=>$id));
			$app->db->update('strategy', array('is_delete'=>'1'), array('strategy_id'=>$id));
		}

		$app->flash('error', '삭제되었습니다.');
		$app->redirect('/admin/strategies');
	});

	$app->get('/strategies/:id/daily', function ($id) use ($app, $log) {
		$current_menu = 'admin_strategies';
		$current_tab_menu = 'daily';

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
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

		$reversed_values = array_reverse($daily_values);
		$reversed_values = array_slice($reversed_values, $start, $count);

		$app->render('admin/strategy_view_daily.php', array('current_menu'=>$current_menu, 'current_tab_menu'=>$current_tab_menu, 'strategy'=>$strategy, 'daily_values'=>$reversed_values, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->post('/strategies/:id/daily/add', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){ $app->halt(404, 'not found');}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{$app->halt(403, 'forbidden');}

		// 기준일자
		$target_date = $app->request->post('basedate');
		$target_date = str_replace('.','',str_replace('-', '', $target_date));
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/admin/strategies/'.$id.'/daily');
		}

		// 입출금
		$flow = $app->request->post('flow');
		$flow = str_replace(',', '', $flow);
		if(!is_numeric($flow)){ $flow = 0;}

		// 손익
		$PL = $app->request->post('PL');
		$PL = str_replace(',', '', $PL);
		if(!is_numeric($PL)){$PL = 0;}


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
			,'flow'=>$flow
			,'PL'=>$PL
		));

		////////////////////////////////////////////////////
		// analysis_strategy() 계산하기
			setStrategyAnalysis($app->db, $id, $start_date, Date("Y-m-d", strtotime($target_date)));
			setStrategyAnalysisMonthly($app->db, $id);
			setStrategyAnalysisYearly($app->db, $id);
			setStrategyScore($app->db, $id);
		////////////////////////////////////////////////////

			//		$app->db->executesp('add_strategy_daily', array(
			//			'p_strategy_id'=>$id,
			//			'p_target_date'=>$target_date,
			//			'p_flow'=>$flow,
			//			'p_pl'=>$PL
			//		));
			//
			//		$app->m->delete('strategy_daily_value:'.$id);
			//		$app->m->delete('strategy_new_daily_value:'.$id);
			//
			//		// 전략의 주요 지표 데이터 저장
			//		fetchStrategyData($id);
			//		$app->db->executesp('analysis_strategy',array('strategy_id'=>$id));

		$app->redirect('/admin/strategies/'.$id.'/daily');
	});

	$app->post('/strategies/:id/daily/upload', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){$app->halt(404, 'not found');}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{$app->halt(403, 'forbidden');}

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
						$target_date = date("Ymd", strtotime(trim($Row[0])));
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

		$app->redirect('/admin/strategies/'.$id.'/daily');
	});

	$app->post('/strategies/:id/daily/edit', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$target_date = $app->request->post('basedate');
		//$balance = $app->request->post('balance');
		$flow = $app->request->post('flow');
		$PL = $app->request->post('PL');

		$target_date = str_replace('-', '', $target_date);
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/admin/strategies/'.$id.'/daily');
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

			//		$app->db->update('strategy_daily', array(
			//		//	'balance'=>$balance,
			//			'flow'=>$flow,
			//			'PL'=>$PL
			//		), array('strategy_id'=>$id, 'target_date'=>$target_date));


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


			//		$app->m->delete('strategy_daily_value:'.$id);
			//		$app->m->delete('strategy_new_daily_value:'.$id);
			//
			//		// 전략의 주요 지표 데이터 저장
			//		fetchStrategyData($id);
			//		$app->db->executesp('analysis_strategy',array('strategy_id'=>$id));
			//		//$app->db->executesp('score_strategies',array());

		$app->redirect('/admin/strategies/'.$id.'/daily');
	});

	$app->get('/strategies/:id/daily/delete', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));
		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$target_date = $app->request->get('basedate');

		$target_date = str_replace('-', '', $target_date);
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/admin/strategies/'.$id.'/daily');
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

			//		$app->m->delete('strategy_daily_value:'.$id);
			//		$app->m->delete('strategy_new_daily_value:'.$id);
			//
			//		// 전략의 주요 지표 데이터 저장
			//		fetchStrategyData($id);
			//		$app->db->executesp('analysis_strategy',array('strategy_id'=>$id));
			//		//$app->db->executesp('score_strategies',array());

		$app->redirect('/admin/strategies/'.$id.'/daily');
	});

	$app->get('/strategies/:id/daily/deleteall', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));
		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

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

		$app->redirect('/admin/strategies/'.$id.'/daily');
	});

	$app->get('/strategies/:id/accounts', function ($id) use ($app, $log) {
		$current_menu = 'admin_strategies';
		$current_tab_menu = 'accounts';

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 10;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$daily_values = $app->db->select('strategy_account', '*', array('strategy_id'=>$id));
		$total = $app->db->selectCount('strategy_account', array('strategy_id'=>$id));
		$total_page = ceil($total / $count);

		$app->render('admin/strategy_view_account.php', array('current_menu'=>$current_menu, 'current_tab_menu'=>$current_tab_menu, 'strategy'=>$strategy, 'daily_values'=>$daily_values, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->post('/strategies/:id/accounts/add', function ($id) use ($app, $log) {
			$app->flash('error', '업로드에 실패하였습니다');
		//$target_date_array = $app->request->post('target_date');
		$title_array = $app->request->post('title');

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		if(empty($title_array)){
			$app->redirect('/admin/strategies/'.$id.'/accounts');
		}

		// $target_date가 데이트형태에서 그냥 순번으로 변경되었음
		foreach($title_array as $k => $title){
			//$target_date = str_replace('.', '', $target_date);
			//$title = $title_array[$k];

			$account_image_url = '';
			$savePath = $app->config('account.path');
			$max_file_size = 1024 * 1024 * 5;

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
						// $app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
						// $app->redirect('/admin/brokers/'.$id.'/accounts');
					}

					if(strpos($filetype, 'image') === false){
						// $app->flash('error', '이미지 파일만 업로드 가능합니다.');
						// $app->redirect('/admin/brokers/'.$id.'/accounts');
						continue;
					}

					// check upload valid ext
					if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
						// $app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
						// $app->redirect('/admin/brokers/'.$id.'/accounts');
						continue;
					}

					/*
					$image = getimagesize($filetmpname);
					$width = 360;
					$height = 360;

					if($image[0] < $width || $image[1] < $height){
						$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
						$app->redirect('/admin/brokers/'.$id);
					}
					*/

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
					// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
					// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
					$account_image_url = $app->config('account.url').'/'.$saveFilename.'.'.$fileext;
					// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
					// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

					if(!move_uploaded_file($filetmpname, $finalFilename)){
						//$app->flash('error', '업로드에 실패하였습니다');
						// $app->redirect('/admin/brokers/'.$id.'/accounts');
						continue;
					}

					// 썸네일생성 s, m
					// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
					// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
					break;
				case UPLOAD_ERR_INI_SIZE:
					// $app->flash('error', '업로드 가능 용량을 초과하였습니다');
					// $app->redirect('/admin/brokers/'.$id.'/accounts');
					continue;
					break;
				case UPLOAD_ERR_FORM_SIZE:
					// $app->flash('error', '업로드 가능 용량을 초과하였습니다');
					// $app->redirect('/admin/brokers/'.$id.'/accounts');
					continue;
					break;
				case UPLOAD_ERR_PARTIAL:
					// $app->flash('error', '업로드에 실패하였습니다');
					// $app->redirect('/admin/brokers/'.$id.'/accounts');
					continue;
					break;
				case UPLOAD_ERR_NO_FILE:
					break;
				default:
					// $app->flash('error', '업로드에 실패하였습니다');
					// $app->redirect('/admin/brokers/'.$id.'/accounts');
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

		$app->redirect('/admin/strategies/'.$id.'/accounts');
	});

	$app->post('/strategies/:id/accounts/delete', function ($id) use ($app, $log) {

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$input_account_ids = $app->request->post('account_ids');

		if(empty($input_account_ids)){
			// $app->flash('error', '날짜를 선택하세요');
			$app->redirect('/admin/strategies/'.$id.'/accounts');
		}

		$account_ids = array();
		foreach($input_account_ids as $v){
			if(empty($v)) continue;
			if(!is_numeric($v)) continue;
			if(in_array($v, $account_ids)) continue;

			$account_ids[] = $v;
		}

		if(count($account_ids) == 0) $app->redirect('/admin/strategies/'.$id.'/accounts');

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

		$app->redirect('/admin/strategies/'.$id.'/accounts');
	});

	$app->get('/strategies/:id/funding', function ($id) use ($app, $log) {
		$current_menu = 'admin_strategies';
		$current_tab_menu = 'funding';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 10;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$daily_values = $app->db->select('strategy_funding', '*', array('strategy_id'=>$id), array('target_date'=>'desc'));
		$total = $app->db->selectCount('strategy_funding', array('strategy_id'=>$id));
		$total_page = ceil($total / $count);

		$app->render('admin/strategy_view_funding.php', array('current_menu'=>$current_menu, 'current_tab_menu'=>$current_tab_menu, 'strategy'=>$strategy, 'daily_values'=>$daily_values, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->post('/strategies/:id/funding/add', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$target_date = $app->request->post('target_date');
		$money = $app->request->post('money');
		$investor = $app->request->post('investor');

		$target_date = str_replace('.', '', $target_date);
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/admin/strategies/'.$id.'/funding');
		}

		if($app->db->selectCount('strategy_funding', array('target_date'=>$target_date))){
			$app->flash('error', '이미 입력된 데이터가 있습니다.');
			$app->redirect('/admin/strategies/'.$id.'/funding');
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

		$app->m->delete('strategy_total_funding:'.$id);

		$app->redirect('/admin/strategies/'.$id.'/funding');
	});

	$app->post('/strategies/:id/funding/upload', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$savePath = $app->config('upload.tmp.path');

		switch($_FILES['excel']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['excel']['name'];
				$filesize = $_FILES['excel']['size'];
				$filetmpname = $_FILES['excel']['tmp_name'];
				$filetype = $_FILES['excel']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				/*
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/strategies/'.$id.'/daily');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/strategies/'.$id.'/daily');
				}
				*/

				// check upload valid ext
				if(!preg_match('/\.(xls|xlsx)$/i', $filename)){
					$app->flash('error', '확장자가 xls, xlsx 파일만 업로드가 가능합니다');
					$app->redirect('/admin/strategies/'.$id.'/funding');
				}

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/strategies/'.$id.'/funding');
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
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				// $broker_logo_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/strategies/'.$id.'/funding');
				}

				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/strategies/'.$id.'/funding');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/strategies/'.$id.'/funding');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/strategies/'.$id.'/funding');
				break;
			case UPLOAD_ERR_NO_FILE:
				$app->flash('error', '업로드한 파일이 없습니다');
				$app->redirect('/admin/strategies/'.$id.'/funding');
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/strategies/'.$id.'/funding');
		}

		require(dirname(__FILE__).'/../lib/spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
		require(dirname(__FILE__).'/../lib/spreadsheet-reader-master/SpreadsheetReader.php');

		$Filepath = $finalFilename;
		// $Filepath = '/var/www/html/t.xlsx';

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
							// $app->flash('error', '날짜를 선택하세요');
							// $app->redirect('/admin/strategies/'.$id.'/daily');
							continue;
						}

						if($app->db->selectCount('strategy_funding', array('strategy_id'=>$id, 'target_date'=>$target_date))){
							// $app->flash('error', '이미 입력된 데이터가 있습니다.');
							// $app->redirect('/admin/strategies/'.$id.'/daily');
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

		$app->m->delete('strategy_total_funding:'.$id);

		$app->redirect('/admin/strategies/'.$id.'/funding');
	});

	$app->post('/strategies/:id/funding/edit', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$target_date = $app->request->post('target_date');
		$money = $app->request->post('money');
		$investor = $app->request->post('investor');

		$target_date = str_replace('.', '', $target_date);
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/admin/strategies/'.$id.'/funding');
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

		$app->m->delete('strategy_total_funding:'.$id);

		$app->redirect('/admin/strategies/'.$id.'/funding');
	});

	$app->get('/strategies/:id/funding/delete', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));
		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$target_date = $app->request->get('target_date');

		$target_date = str_replace('.', '', $target_date);
		if(strlen($target_date) != 8){
			$app->flash('error', '날짜를 선택하세요');
			$app->redirect('/admin/strategies/'.$id.'/funding');
		}

		$app->db->delete('strategy_funding', array(
			'strategy_id'=>$id,
			'target_date'=>$target_date
		));

		$app->m->delete('strategy_total_funding:'.$id);

		$app->redirect('/admin/strategies/'.$id.'/funding');
	});

	$app->get('/strategies/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_strategies';
		$current_tab_menu = 'basic';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		// 전체 종목
		$items = $app->db->select('item', '*', array(), array('item_id'=>'desc'));
        $kinds = $app->db->select('kind', '*', array(), array('sorting'=>'asc'));
        $fund_price = $app->config('strategy.min_price');
        $types = $app->db->select('type', '*', array(), array('sorting'=>'asc'));

		// 브로커
		$brokers = $app->db->select('broker', '*', array(), array('broker_id'=>'desc'));
		$company_type1 = array();
		$company_type2 = array();
		foreach($brokers as $broker){
            $company[] = array('id'=>$broker['broker_id'], 'name'=>$broker['company']);
			if($broker['company_type'] == '증권사') $company_type1[] = array('id'=>$broker['broker_id'], 'name'=>$broker['company']);
			else if($broker['company_type'] == '선물사') $company_type2[] = array('id'=>$broker['broker_id'], 'name'=>$broker['company']);
		}

		// 매매툴
		$tools = array();
		$result = $app->db->conn->query('SELECT * FROM system_trading_tool JOIN broker ON system_trading_tool.broker_id = broker.broker_id WHERE broker.is_open = \'1\'');
		while($row = $result->fetch_array()){
			// if(empty($row['name'])) continue;
			// if(in_array($row['name'], $tools)) continue;
			if(!isset($tools['broker'.$row['broker_id']])) $tools['broker'.$row['broker_id']] = array();
			$tools['broker'.$row['broker_id']][] = $row;
		}

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));
		// pb
		$pb_list = array();
		$result = $app->db->conn->query('SELECT * FROM user a JOIN broker b ON a.broker_id = b.broker_id WHERE a.user_type=\'P\' and a.is_request_pb=\'0\' and b.is_open = \'1\' and a.is_delete = \'0\' ');
		while($row = $result->fetch_array()){
			// if(empty($row['name'])) continue;
			// if(in_array($row['name'], $tools)) continue;
			if(!isset($pb_list['broker'.$row['broker_id']])) $pb_list['broker'.$row['broker_id']] = array();
			$pb_list['broker'.$row['broker_id']][] = $row;
		}

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		// 트레이더
		$developer = $app->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']));
		$strategy['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);
        
        // pb
        if ($strategy['pb_uid']) {
            $pb = $app->db->selectOne('user', '*', array('uid'=>$strategy['pb_uid']));
            $strategy['pb'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);
        }

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

        $param = array(
                    'current_menu'=>$current_menu, 
                    'current_tab_menu'=>$current_tab_menu, 
                    'items'=>$items, 
                    'kinds'=>$kinds,
                    'types'=>$types,
                    'tools'=>$tools, 
                    'fund_price'=>$fund_price,
                    'company'=>$company,
                    'strategy'=>$strategy,
                    'pb_list'=>$pb_list,
                    'page'=>$page
            );

		$app->render('admin/strategy_view.php', $param);
	});

	$app->post('/strategies/:id', function ($id) use ($app, $log) {
		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

		if(empty($strategy)){
			$app->halt(404, 'not found');
		}

		if($_SESSION['user']['user_type'] == 'A'){
		}else if($strategy['developer_uid'] == $_SESSION['user']['uid']){
		}else{
			$app->halt(403, 'forbidden');
		}

		$name = $app->request->post('name');
		$strategy_type = $app->request->post('strategy_type');
		$strategy_kind = $app->request->post('strategy_kind');
		$min_price = $app->request->post('min_price');
		$broker_type = $app->request->post('broker_type');
		$broker_id = $app->request->post('broker_id');
		$input_item_ids = $app->request->post('item_ids');
		$tool_id = $app->request->post('tool_id');
		$currency = $app->request->post('currency');
		$investment = $app->request->post('investment');
		$term = $app->request->post('term');
		$intro = $app->request->post('intro');
		$developer_uid = $app->request->post('developer_uid');
        $pb_uid = $app->request->post('pb_uid');
		$is_operate = $app->request->post('is_operate');
		$is_open = $app->request->post('is_open');
		$is_fund = $app->request->post('is_fund');
        $attached_file_del = $app->request->post('attached_file_del');
        $save_name = $app->request->post('save_name');

		if(empty($name)){
			$app->flash('error', '전략명을 입력하세요');
			$app->redirect('/admin/strategies/'.$id);
		}
/*
		if(empty($broker_type)){
			$app->flash('error', '브로커를 선택하세요');
			$app->redirect('/admin/strategies/'.$id);
		}
*/

		if(empty($broker_id) || !is_numeric($broker_id)){
			$app->flash('error', '브로커를 선택하세요');
			$app->redirect('/admin/strategies/'.$id);
		}

		$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$broker_id));

		if(empty($broker)){
			$app->flash('error', '브로커를 선택하세요');
			$app->redirect('/admin/strategies/'.$id);
		}

        $broker_type = $broker['company_type'];

		if(empty($tool_id)){
			$app->flash('error', '매매툴을 선택하세요');
			$app->redirect('/admin/strategies/'.$id);
		}

		$tool = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$tool_id));

		if(empty($tool)){
			$app->flash('error', '매매툴을 선택하세요');
			$app->redirect('/admin/strategies/'.$id);
		}

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

		if(!empty($is_open) && $is_open == '1'){
			$is_open = '1';
		}else{
			$is_open = '0';
		}

		if(!empty($is_fund) && $is_fund == '1'){
			$is_fund = '1';
		}else{
			$is_fund = '0';
		}

		// 전략의 데이터가 2개 미만일 경우 상태는 무조건 비공개상태가 됨
		$daily_values_count = $app->db->selectCount('strategy_daily', array('strategy_id'=>$id));
		if($daily_values_count < 2){
			$is_open = '0';
		}

		if(!empty($is_operate) && $is_operate == '1'){
			$is_operate = '1';
		}else{
			$is_operate = '0';
		}

		if(empty($developer_uid)){
			$developer_uid = $strategy['developer_uid'];
            $trader_uid = $strategy['trader_uid'];
		}else{
			$developer_uid = $developer_uid;
            $trader_uid = $developer_uid;
		}

        /*
		$strategy_type = $strategy['strategy_type'];
		//if($_SESSION['user']['user_type'] == 'A'){
			$strategy_type = $app->request->post('strategy_type');
			if(empty($strategy_type)){
				$strategy_type = 'M';
			}
		//}else{
		//	$strategy_type = 'M';
		//}
        */

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

		$app->db->update('strategy', array(
			'name'=>$name,
			'strategy_type'=>$strategy_type,
			'strategy_kind'=>$strategy_kind,
            'min_price'=>$min_price,
			'broker_type'=>$broker_type,
			'broker_id'=>$broker_id,
			'developer_uid'=>$developer_uid,
			'developer_name'=>$developer_name,
            'pb_uid'=>$pb_uid,
            'trader_uid'=>$trader_uid,
			'tool_id'=>$tool_id,
			'currency'=>$currency,
			'investment'=>$investment,
			'strategy_term'=>$term,
			'intro'=>$intro,
			'is_operate'=>$is_operate,
			'is_open'=>$is_open,
            'is_fund'=>$is_fund
		), array('strategy_id'=>$id));

		// 종목을 지운뒤 재등록
		$app->db->delete('strategy_item', array('strategy_id'=>$id));
		foreach($input_item_ids as $v){
			if(empty($v)) continue;
			$app->db->insert('strategy_item', array(
				'strategy_id'=>$id,
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
		$app->redirect('/admin/strategies/'.$id);
	});

	$app->get('/trader_search', function () use ($app, $log) {
		$nickname = $app->request->get('nickname');
		
		$response_traders = array();

		if(empty($nickname)){
			echo json_encode($response_traders);
			$app->stop();
		}

		//$result = $app->db->conn->query('SELECT * FROM user WHERE user_type = \'T\' AND nickname LIKE \'%'.$app->db->conn->real_escape_string($nickname).'%\'');
		$result = $app->db->conn->query('SELECT * FROM user WHERE user_type = \'T\' AND (nickname LIKE \'%'.$app->db->conn->real_escape_string($nickname).'%\' or name LIKE \'%'.$app->db->conn->real_escape_string($nickname).'%\')');

		while($row = $result->fetch_array()){
			$response_traders[] = array('uid'=>$row['uid'], 'name'=>$row['name'], 'nickname'=>$row['nickname']);
		}

		echo json_encode(array('items'=>$response_traders));
	});

});

// 관리자
$app->group('/admin', $authenticateForRole('A'), function () use ($app, $log) {
	$app->get('/', function () use ($app, $log) {
		// $app->render('admin.index.php');
		$app->redirect('/admin/users');
	});

	$app->get('/users', function () use ($app, $log) {
		$current_menu = 'admin_users';
		$q = $app->request->get('q');
		$q_type = $app->request->get('q_type');

		if(empty($q_type)){
			$q_type = 'email';
		}

		$condition = array();
		if(!empty($q)){
			if($q_type == 'name'){
				$where = "and name like '%$q%'";
			}else if($q_type == 'nickname'){
				$where = "and nickname like '%$q%'";
			}else if($q_type == 'mobile'){
				$where = "and mobile like '%$q%'";
			}else if($q_type == 'birthday'){
				$where = "and birthday like '%$q%'";
			}else if($q_type == 'user_type'){
			}else{
				$where = "and email like '%$q%'";
			}
		}

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;
        $limit = 15;

        $sql = "select * from user where is_delete='0' $where order by uid desc limit $start, $limit";
        $result = $app->db->conn->query($sql);
        $users = array();
		while($row = $result->fetch_array()){
			$users[] = $row;
		}

        $sql = "select count(*) cnt from user where is_delete='0' $where order by uid desc";

        $result = $app->db->conn->query($sql);
		$row = $result->fetch_array();
		$total = $row['cnt'];

		$total_page = ceil($total / $count);

		$app->render('admin/member.php', array('current_menu'=>$current_menu, 'users'=>$users, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count, 'q'=>$q, 'q_type'=>$q_type));
	});

	$app->get('/request_normal', function () use ($app, $log) {
		$current_menu = 'admin_users';
		$q = $app->request->get('q');
		$q_type = $app->request->get('q_type');

		if(empty($q_type)){
			$q_type = 'email';
		}

		$condition = array();
		if(!empty($q)){
			if($q_type == 'name'){
				$where = "and name like '%$q%'";
			}else if($q_type == 'nickname'){
				$where = "and nickname like '%$q%'";
			}else if($q_type == 'mobile'){
				$where = "and mobile like '%$q%'";
			}else if($q_type == 'birthday'){
				$where = "and birthday like '%$q%'";
			}else if($q_type == 'user_type'){
			}else{
				$where = "and email like '%$q%'";
			}
		}

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;
        $limit = 15;

        $sql = "select * from user where is_delete='0' and user_type='N' $where order by uid desc limit $start, $limit";
        $result = $app->db->conn->query($sql);
        $users = array();
		while($row = $result->fetch_array()){
			$users[] = $row;
		}

        $sql = "select count(*) cnt from user where is_delete='0' and user_type='N' $where order by uid desc";

        $result = $app->db->conn->query($sql);
		$row = $result->fetch_array();
		$total = $row['cnt'];

		$total_page = ceil($total / $count);

		$app->render('admin/member_normal.php', array('current_menu'=>$current_menu, 'users'=>$users, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count, 'q'=>$q, 'q_type'=>$q_type));
	});


	$app->get('/request_trader', function () use ($app, $log) {
		$current_menu = 'admin_users';
		$q = $app->request->get('q');
		$q_type = $app->request->get('q_type');

		if(empty($q_type)){
			$q_type = 'email';
		}

		$condition = array();
		if(!empty($q)){
			if($q_type == 'name'){
				$where = "and name like '%$q%'";
			}else if($q_type == 'nickname'){
				$where = "and nickname like '%$q%'";
			}else if($q_type == 'mobile'){
				$where = "and mobile like '%$q%'";
			}else if($q_type == 'birthday'){
				$where = "and birthday like '%$q%'";
			}else if($q_type == 'user_type'){
			}else{
				$where = "and email like '%$q%'";
			}
		}

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
        $limit = 15;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

        $sql = "select * from user where is_delete='0' and user_type='T' $where order by uid desc limit $start, $limit";
        $result = $app->db->conn->query($sql);
        $users = array();
		while($row = $result->fetch_array()){
			$users[] = $row;
		}

        $sql = "select count(*) cnt from user where is_delete='0' and user_type='T' $where order by uid desc";

        $result = $app->db->conn->query($sql);
		$row = $result->fetch_array();
		$total = $row['cnt'];
		$total_page = ceil($total / $count);

		$app->render('admin/member_trader.php', array('current_menu'=>$current_menu, 'users'=>$users, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count, 'q'=>$q, 'q_type'=>$q_type));
	});

    $app->get('/users/:uid/modify', function($uid) use ($app, $log) {
            
        $uInfo = $app->db->selectOne('user', '*', array('uid'=>$uid), array());
        $brokers = $app->db->select('broker', 'broker_id, company', array(), array('company'=>'asc'));

        $param = array(
            'uInfo'    => $uInfo,
            'brokers'   => $brokers,

        );

        $app->render('admin/member_edit.php', $param);
        
    });

    $app->post('/users/modify', function() use ($app, $log) {

        $uid = $app->request->post('uid');
        $nickname = $app->request->post('nickname');
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

        if(!empty($password) && !empty($password_confirm)){
            if($password != $password_confirm){
                $app->flash('error', '비밀번호가 일치하지 않습니다.');
                $app->redirect('/admin/users/'.$uid.'/modify');
            }

            if(strlen($password) < 6 || strlen($password) >= 20){
                $app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
                $app->redirect('/admin/users/'.$uid.'/modify');
            }

            if(preg_match('/^(?=.*\d)(?=.*[a-zA-Z]).{6,19}$/', $password)){

            }else{
                $app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
                $app->redirect('/admin/users/'.$uid.'/modify');
            }

            $password_hash = create_hash($password);
        }

        if(!empty($mobile)){
            if(preg_match('/^[0-9]{10,11}$/', $mobile)){
            }else{
                $app->flash('error', '정확한 휴대폰 번호를 확인해 주세요.');
                $app->redirect('/admin/users/'.$uid.'/modify');
            }
        }else{
            $mobile = '';
        }

        if(!empty($birthday)){
            if(preg_match('/^[0-9]{8}$/', $birthday)){
            }else{
                $app->flash('error', '생년월일이 올바르지 않습니다.');
                $app->redirect('/admin/users/'.$uid.'/modify');
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
            'nickname' => $nickname,
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
            'namecard_s'=>$namecard_s_url
        );

        if ($password_hash) {
            $param['user_password'] = $password_hash;
        }

        $app->db->update('user', $param, array('uid'=>$uid));

        $_SESSION['temp_profile_url'] = '';
        $_SESSION['temp_profile_s_url'] = '';
        $_SESSION['temp_namecard_url'] = '';
        $_SESSION['temp_namecard_s_url'] = '';

        $app->redirect('/admin/users/'.$uid.'/modify');
    });

	$app->post('/request/approve', function () use ($app, $log) {
		$input_uids = $app->request->post('uids');
		$user_type = $app->request->post('user_type');
        if ($user_type == 'T') $req_page = "/admin/request_trader";
        else $req_page = "/admin/request_pb";

		$edit_uids = array();
		foreach($input_uids as $uid){
			if(empty($uid)) continue;
			if(!is_numeric($uid)) continue;
			if(in_array($uid, $edit_uids)) continue;

			$edit_uids[] = $uid;
		}

		if (count($edit_uids) == 0) $app->redirect($req_page);

        if ($user_type == 'T') {
		    $app->db->conn->query('UPDATE user SET is_request_trader = \'0\' WHERE uid IN ('.implode(',', $edit_uids).') AND user_type = \'T\'');
        }
        if ($user_type == 'P') {
            $app->db->conn->query('UPDATE user SET is_request_pb = \'0\' WHERE uid IN ('.implode(',', $edit_uids).') AND user_type = \'P\'');
        }
        
        $app->redirect($req_page);
	});


	$app->post('/request/delete', function () use ($app, $log) {
		$input_uids = $app->request->post('uids');
		$redirect_url = $app->request->post('redirect_url');

		$edit_uids = array();
		foreach($input_uids as $uid){
			if(empty($uid)) continue;
			if(!is_numeric($uid)) continue;
			if(in_array($uid, $edit_uids)) continue;

			$edit_uids[] = $uid;
		}

		if (count($edit_uids) == 0) $app->redirect($redirect_url);

		$app->db->conn->query('UPDATE user SET is_delete = \'1\', delete_at = now() WHERE uid IN ('.implode(',', $edit_uids).')');
        
        $app->redirect($redirect_url);
	});


	$app->get('/request_pb', function () use ($app, $log) {
		$current_menu = 'admin_users';
		$q = $app->request->get('q');
		$q_type = $app->request->get('q_type');

		if(empty($q_type)){
			$q_type = 'email';
		}

		$condition = array();
		if(!empty($q)){
			if($q_type == 'name'){
				$where = "and name like '%$q%'";
			}else if($q_type == 'mobile'){
				$where = "and mobile like '%$q%'";
			}else if($q_type == 'birthday'){
				$where = "and birthday like '%$q%'";
			}else if($q_type == 'user_type'){
			}else{
				$where = "and email like '%$q%'";
			}
		}

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;
        $limit = 15;


        $sql = "select * from user where is_delete='0' and user_type='P' $where order by uid desc limit $start, $limit";
        $result = $app->db->conn->query($sql);
        $users = array();
		while($row = $result->fetch_array()){
			$users[] = $row;
		}

        $sql = "select count(*) cnt from user where is_delete='0' and user_type='P' $where order by uid desc";

        $result = $app->db->conn->query($sql);
		$row = $result->fetch_array();
		$total = $row['cnt'];
		$total_page = ceil($total / $count);

		$app->render('admin/member_pb.php', array('current_menu'=>$current_menu, 'users'=>$users, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count, 'q'=>$q, 'q_type'=>$q_type));
	});

	$app->get('/request_broker/:id/approve', function ($id) use ($app, $log) {
		$current_menu = 'admin_users';

		$request_broker_info = $app->db->selectOne('request_broker', '*', array('request_broker_id'=>$id));

		if(empty($request_broker_info)){
			$app->halt(404, 'not found');
		}

		$app->db->update('user', array('user_type'=>'B'), array('uid'=>$request_broker_info['uid']));

		$app->redirect('/admin/request_broker');
	});

	$app->get('/request_broker/:id/cancel', function ($id) use ($app, $log) {
		$current_menu = 'admin_users';

		$request_broker_info = $app->db->selectOne('request_broker', '*', array('request_broker_id'=>$id));

		if(empty($request_broker_info)){
			$app->halt(404, 'not found');
		}

		$app->db->update('user', array('user_type'=>'N'), array('uid'=>$request_broker_info['uid']));

		$app->redirect('/admin/request_broker');
	});

	$app->get('/request_broker', function () use ($app, $log) {
		$current_menu = 'admin_users';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
        $limit = 15;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$users = $app->db->select('request_broker', '*', array(), array('request_broker_id'=>'desc'), $start, $limit);
		$total = $app->db->selectCount('request_broker');
		$total_page = ceil($total / $count);

		foreach($users as $k => $user){
			$target_user_info = $app->db->selectOne('user', '*', array('uid'=>$user['uid']));
			$users[$k]['user'] = $target_user_info;
		}

		$app->render('admin/member_broker.php', array('current_menu'=>$current_menu, 'users'=>$users, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->get('/request_broker/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_users';

		$request_broker_info = $app->db->selectOne('request_broker', '*', array('request_broker_id'=>$id));

		if(empty($request_broker_info)){
			$app->halt(404, 'not found');
		}

		$request_broker_info['user'] = $app->db->selectOne('user', '*', array('uid'=>$request_broker_info['uid']));

		$app->render('admin/member_broker_view.php', array('current_menu'=>$current_menu, 'request_broker_info'=>$request_broker_info));
	});

	$app->post('/users/edit', function () use ($app, $log) {
		$user_type = $app->request->post('user_type');
		$input_uids = $app->request->post('uids');
		$redirect_url = $app->request->post('redirect_url');

		if(empty($redirect_url)){
			$redirect_url = '/admin/users';
		}

		$edit_uids = array();
		foreach($input_uids as $uid){
			if(empty($uid)) continue;
			if(!is_numeric($uid)) continue;
			if(in_array($uid, $edit_uids)) continue;

			$edit_uids[] = $uid;
		}

		if(count($edit_uids) == 0) $app->redirect($redirect_url);

		if(!empty($user_type) && in_array($user_type, array('N', 'T', 'B', 'P', 'A'))){
			$user_type_flag = $user_type;
		}else{
			$user_type_flag = 'N';
		}

		$app->db->conn->query('UPDATE user SET user_type = \''.$user_type_flag.'\', is_request_trader = \'1\', is_request_pb = \'1\' WHERE uid IN ('.implode(',', $edit_uids).')');
		$app->redirect($redirect_url);
	});

	$app->get('/items', function () use ($app, $log) {
		$current_menu = 'admin_items';

		$items = $app->db->select('item', '*', array(), array('sorting'=>'asc'));

		$app->render('admin/items.php', array('current_menu'=>$current_menu, 'items'=>$items));
	});

	$app->post('/items/add', function () use ($app, $log) {
		$name = $app->request->post('name');
		$sorting = $app->request->post('sorting');

		if(empty($name)){
			$app->flash('error', '종목을 입력하세요');
			$app->redirect('/admin/items');
		}

		if(empty($sorting) || !is_numeric($sorting)){
			$sorting = 1;
		}

		$item_image_url = '';
		$savePath = $app->config('item.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['item_image']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['item_image']['name'];
				$filesize = $_FILES['item_image']['size'];
				$filetmpname = $_FILES['item_image']['tmp_name'];
				$filetype = $_FILES['item_image']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/items');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/items');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/items');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/items');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/items');
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
				$item_image_url = $app->config('item.url').'/'.$saveFilename.'.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/items');
				}

				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/items');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/items');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/items');
				break;
			case UPLOAD_ERR_NO_FILE:
				$app->flash('error', '선택된 파일이 없습니다');
				$app->redirect('/admin/items');
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/items');
		}

		$app->db->insert('item', array(
			'name'=>$name,
			'icon'=>$item_image_url,
			'sorting'=>$sorting
		));

		$app->redirect('/admin/items');
	});

	$app->post('/items/edit', function () use ($app, $log) {
		$edit_item_id = $app->request->post('edit_item_id');
		$name = $app->request->post('name');
		$sorting = $app->request->post('sorting');

		if(empty($edit_item_id)){
			$app->halt(404, 'not found');
		}

		if(empty($name)){
			$app->flash('error', '종목을 입력하세요');
			$app->redirect('/admin/items');
		}

		if(empty($sorting) || !is_numeric($sorting)){
			$sorting = 1;
		}

		$item = $app->db->selectOne('item', '*', array('item_id'=>$edit_item_id));

		if(empty($item)){
			$app->halt(404, 'not found');
		}

		$item_image_url = $item['icon'];
		$savePath = $app->config('item.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['item_image']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['item_image']['name'];
				$filesize = $_FILES['item_image']['size'];
				$filetmpname = $_FILES['item_image']['tmp_name'];
				$filetype = $_FILES['item_image']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/items');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/items');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/items');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/items');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/items');
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
				$item_image_url = $app->config('item.url').'/'.$saveFilename.'.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/items');
				}

				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/items');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/items');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/items');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/items');
		}

		$app->db->update('item', array(
			'name'=>$name,
			'icon'=>$item_image_url,
			'sorting'=>$sorting
		), array('item_id'=>$edit_item_id));

		$app->redirect('/admin/items');
	});

	$app->get('/items/:id/delete', function ($id) use ($app, $log) {
		$item = $app->db->selectOne('item', '*', array('item_id'=>$id));

		if(empty($item)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('item', array('item_id'=>$id));
		// 해당 종목을 가진 전략에서 종목 부분만 제거
		$app->db->delete('strategy_item', array('item_id'=>$id));

		$app->redirect('/admin/items');
	});


    // 유형

	$app->get('/types', function () use ($app, $log) {
		$current_menu = 'admin_items';

		$types = $app->db->select('type', '*', array(), array('sorting'=>'asc'));

		$app->render('admin/types.php', array('current_menu'=>$current_menu, 'types'=>$types));
	});

	$app->post('/types/add', function () use ($app, $log) {
		$name = $app->request->post('name');
		$sorting = $app->request->post('sorting');

		if(empty($name)){
			$app->flash('error', '종목을 입력하세요');
			$app->redirect('/admin/types');
		}

		if(empty($sorting) || !is_numeric($sorting)){
			$sorting = 1;
		}

		$type_image_url = '';
		$savePath = $app->config('type.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['type_image']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['type_image']['name'];
				$filesize = $_FILES['type_image']['size'];
				$filetmpname = $_FILES['type_image']['tmp_name'];
				$filetype = $_FILES['type_image']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/items');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/items');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/items');
				}

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/items');
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
				$type_image_url = $app->config('type.url').'/'.$saveFilename.'.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/types');
				}

				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/types');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/types');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/types');
				break;
			case UPLOAD_ERR_NO_FILE:
				$app->flash('error', '선택된 파일이 없습니다');
				$app->redirect('/admin/types');
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/types');
		}

		$app->db->insert('type', array(
			'name'=>$name,
			'icon'=>$type_image_url,
			'sorting'=>$sorting
		));

		$app->redirect('/admin/types');
	});

	$app->post('/types/edit', function () use ($app, $log) {
		$edit_type_id = $app->request->post('edit_type_id');
		$name = $app->request->post('name');
		$sorting = $app->request->post('sorting');

		if(empty($edit_type_id)){
			$app->halt(404, 'not found');
		}

		if(empty($name)){
			$app->flash('error', '종목을 입력하세요');
			$app->redirect('/admin/types');
		}

		if(empty($sorting) || !is_numeric($sorting)){
			$sorting = 1;
		}

		$type = $app->db->selectOne('type', '*', array('type_id'=>$edit_type_id));

		if(empty($type)){
			$app->halt(404, 'not found');
		}

		$type_image_url = $type['icon'];
		$savePath = $app->config('type.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['type_image']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['type_image']['name'];
				$filesize = $_FILES['type_image']['size'];
				$filetmpname = $_FILES['type_image']['tmp_name'];
				$filetype = $_FILES['type_image']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/types');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/types');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/types');
				}

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/types');
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
				$type_image_url = $app->config('type.url').'/'.$saveFilename.'.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/types');
				}

				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/types');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/types');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/types');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/types');
		}

		$app->db->update('type', array(
			'name'=>$name,
			'icon'=>$type_image_url,
			'sorting'=>$sorting
		), array('type_id'=>$edit_type_id));

		$app->redirect('/admin/types');
	});

	$app->get('/types/:id/delete', function ($id) use ($app, $log) {
		$type = $app->db->selectOne('type', '*', array('type_id'=>$id));

		if(empty($type)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('type', array('type_id'=>$id));

		$app->redirect('/admin/types');
	});


    // 종류
	$app->get('/kinds', function () use ($app, $log) {
		$current_menu = 'admin_items';

		$kinds = $app->db->select('kind', '*', array(), array('sorting'=>'asc'));

		$app->render('admin/kinds.php', array('current_menu'=>$current_menu, 'kinds'=>$kinds));
	});

	$app->post('/kinds/add', function () use ($app, $log) {
		$name = $app->request->post('name');
		$sorting = $app->request->post('sorting');

		if(empty($name)){
			$app->flash('error', '종류를 입력하세요');
			$app->redirect('/admin/kinds');
		}

		if(empty($sorting) || !is_numeric($sorting)){
			$sorting = 1;
		}

		$app->db->insert('kind', array(
			'name'=>$name,
			'sorting'=>$sorting
		));

		$app->redirect('/admin/kinds');
	});

	$app->post('/kinds/edit', function () use ($app, $log) {
		$edit_kind_id = $app->request->post('edit_kind_id');
		$name = $app->request->post('name');
		$sorting = $app->request->post('sorting');

		if(empty($edit_kind_id)){
			$app->halt(404, 'not found');
		}

		if(empty($name)){
			$app->flash('error', '종류를 입력하세요');
			$app->redirect('/admin/kinds');
		}

		if(empty($sorting) || !is_numeric($sorting)){
			$sorting = 1;
		}

		$kind = $app->db->selectOne('kind', '*', array('kind_id'=>$edit_kind_id));

		if(empty($kind)){
			$app->halt(404, 'not found');
		}

		$app->db->update('kind', array(
			'name'=>$name,
			'sorting'=>$sorting
		), array('kind_id'=>$edit_kind_id));

		$app->redirect('/admin/kinds');
	});

	$app->get('/kinds/:id/delete', function ($id) use ($app, $log) {
		$kind = $app->db->selectOne('kind', '*', array('kind_id'=>$id));

		if(empty($kind)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('kind', array('kind_id'=>$id));

		$app->redirect('/admin/kinds');
	});

	$app->get('/strategies_invest/del/:id', function ($id) use ($app, $log) {

		$current_menu = 'admin_contacts';
        
        $sql = "delete FROM strategy_invest where invest_id = ".$id;
		$result = $app->db->conn->query($sql);

		$app->redirect('/admin/strategies_invest');
	});

	$app->get('/strategies_invest', function () use ($app, $log) {
		$current_menu = 'admin_contacts';
        
        $status = $app->request->get('status');
        $q_type = $app->request->get('q_type');
		$q = $app->request->get('q');

        if ($status != '') {
            $where[] = "status = '$status'";
            $q_str = '&status='.$status;
        }

        if (empty($q_type)) {
            $q_type = 'name';
        }

		if (!empty($q)){
            switch ($q_type) {
                case 'name':
			        $where[] = " b.name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'user':
			        $where[] = " c.name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;
            }

            $q_str .= '&q_type='.$q_type.'&q='.urlencode($q);
		}
        
        $where_str = (is_array($where)) ? ' WHERE '.implode(' AND ', $where) : '';

        $total_sql = 'SELECT COUNT(*) cnt FROM 
                strategy_invest a INNER JOIN strategy b ON a.strategy_id = b.strategy_id
                INNER JOIN user c ON a.uid = c.uid'.$where_str;

        $sql = "SELECT a.*, b.name as strategy_name, c.name FROM 
                strategy_invest a INNER JOIN strategy b ON a.strategy_id = b.strategy_id
                INNER JOIN user c ON a.uid = c.uid".$where_str;

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$sql .= " ORDER BY invest_id DESC LIMIT $start, $count";

		$invests = array();

		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_array()){
			$invests[] = $row;
		}

		$total_result = $app->db->conn->query($total_sql);
		$total_result_row = $total_result->fetch_array();
		$total = $total_result_row[0];

		$total_page = ceil($total / $count);
        
        $param =  array(
                    'current_menu'=>$current_menu,
                    'invests'=>$invests,
                    'page'=>$page,
                    'page_count'=>$page_count,
                    'page_start' =>$page_start,
                    'total_page'=>$total_page,
                    'total'=>$total,
                    'count'=>$count,
                    'status'=>$status,
                    'q_type'=>$q_type,
                    'q'=>$q,
                    'q_str'=>$q_str
                );

		$app->render('admin/strategy_invest.php', $param);
	});

    
	$app->get('/strategies_qna/del/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_contacts';

        $sql = 'delete FROM qna WHERE qna_id='.$id;
		$result = $app->db->conn->query($sql);

        $param =  array(
                    'current_menu'=>$current_menu,
                    'post'=>$post,
                );

		$app->redirect('/admin/strategies_qna');
	});


	$app->get('/strategies_qna/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_contacts';

        $sql = 'SELECT * FROM qna WHERE qna_id='.$id;
		$result = $app->db->conn->query($sql);
		$post = $result->fetch_array();

        $param =  array(
                    'current_menu'=>$current_menu,
                    'contacts'=>$contacts,
                    'post'=>$post,
                );

		$app->render('admin/strategy_qna_view.php', $param);
	});

    
	$app->get('/strategies_qna', function () use ($app, $log) {
		$current_menu = 'admin_contacts';
        
        $answer = $app->request->get('answer');
        $q_type = $app->request->get('q_type');
		$q = $app->request->get('q');

        if (!empty($answer)) {
            if ($answer == 'Y') $where = " AND answer_at <> 0";
            else $where = " AND answer_at = 0";

            
            $q_str = '&answer='.$answer;
        }

        if (empty($q_type)) {
            $q_type = 'name';
        }

		if (!empty($q)){
            switch ($q_type) {
                case 'name':
			        $where .= " AND strategy_name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'pb':
			        $where .= " AND strategy_name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'user':
			        $where .= " AND name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;
            }
            
            $q_str .= '&q_type='.$q_type.'&q='.urlencode($q);
		}

        $sql = 'SELECT *, (select email from user where uid=qna.uid limit 1) as email, (select mobile from user where uid=qna.uid limit 1) as mobile FROM qna WHERE target = \'strategy\''.$where;
		$total_sql = 'SELECT COUNT(*) FROM qna WHERE target = \'strategy\''.$where;

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$sql .= " ORDER BY qna_id DESC LIMIT $start, $count";

		$contacts = array();

		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_array()){
			/*
			$myInfo_t['nickname']=$myInfo_p['name']="";
			$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$row['target_value']));
			if($strategy['pb_uid'])$myInfo_p = $app->db->selectOne('user', '*', array('uid'=>$strategy['pb_uid']), array());
			if($strategy['trader_uid'])$myInfo_t = $app->db->selectOne('user', '*', array('uid'=>$strategy['trader_uid']), array());
			$row['target_value_t']=$myInfo_t['nickname'];
			$row['target_value_p']=$myInfo_p['name'];
			*/
			$contacts[] = $row;
		}

		$total_result = $app->db->conn->query($total_sql);
		$total_result_row = $total_result->fetch_array();
		$total = $total_result_row[0];

		$total_page = ceil($total / $count);
        
        $param =  array(
                    'current_menu'=>$current_menu,
                    'contacts'=>$contacts,
                    'page'=>$page,
                    'page_count'=>$page_count,
                    'page_start' =>$page_start,
                    'total_page'=>$total_page,
                    'total'=>$total,
                    'count'=>$count,
                    'q_type'=>$q_type,
                    'q'=>$q,
                    'answer'=>$answer,
                    'q_str'=>$q_str
                );

		$app->render('admin/strategy_qna.php', $param);
	});


	$app->get('/contacts', function () use ($app, $log) {
		$current_menu = 'admin_contacts';

		$sql = 'SELECT * FROM qna WHERE target = \'broker\'';
		$total_sql = 'SELECT COUNT(*) FROM qna WHERE target = \'broker\'';

		$condition = array('target'=>'broker');
		$q = $app->request->get('q');
		if(!empty($q)){
			$condition['name'] = $q;
			$sql .= " AND name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
			$total_sql .= " AND name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
		}

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$sql .= " ORDER BY qna_id DESC LIMIT $start, $count";

		// $contacts = $app->db->select('qna', '*', $condition, array('qna_id'=>'desc'), $start, $count);
		// $total = $app->db->selectCount('qna', $condition);
		$contacts = array();

		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_array()){
			$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$row['target_value']));
			$writer = $app->db->selectOne('user', '*', array('uid'=>$row['uid']));

			$row['broker'] = $broker;
			$row['writer'] = $writer;

			$contacts[] = $row;
		}

		$total_result = $app->db->conn->query($total_sql);
		$total_result_row = $total_result->fetch_array();
		$total = $total_result_row[0];

		$total_page = ceil($total / $count);

		$app->render('admin/contact.php', array('current_menu'=>$current_menu, 'contacts'=>$contacts, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count, 'q'=>$q));
	});

	$app->get('/contacts/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_contacts';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$contact = $app->db->selectOne('qna', '*', array('qna_id'=>$id,'target'=>'broker'));

		if(empty($contact)){
			$app->halt(404, 'not found');
		}

		$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$contact['target_value']));
		$writer = $app->db->selectOne('user', '*', array('uid'=>$contact['uid']));

		$contact['broker'] = $broker;
		$contact['writer'] = $writer;

		$app->render('admin/contact_view.php', array('current_menu'=>$current_menu, 'contact'=>$contact, 'page'=>$page));
	});

	$app->post('/contacts/:id/answer', function ($id) use ($app, $log) {
		$qna_id = $app->request->post('qna_id');
		$answer = $app->request->post('answer');

		$now = time();

		if(empty($qna_id)){
			$app->halt(404, 'not found');
		}

		if(empty($answer)){
			$app->redirect('/admin/contacts/'.$qna_id);
		}

		$app->db->update('qna', array(
			'answer'=>$answer,
			'answer_at'=>$now,
		), array('qna_id'=>$qna_id));

		$app->redirect('/admin/contacts/'.$qna_id);
	});

	$app->get('/brokers', function () use ($app, $log) {

		$current_menu = 'admin_brokers';

		$brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
		foreach($brokers as $k => $broker){
			$s_tools = $app->db->select('system_trading_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
			$a_tools = $app->db->select('api_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
			$brokers[$k]['system_trading_tools'] = $s_tools;
			$brokers[$k]['api_tools'] = $a_tools;
		}

		$app->render('admin/broker.php', array('current_menu'=>$current_menu, 'brokers'=>$brokers));
	});

	$app->get('/brokers/write', function () use ($app, $log) {
		$current_menu = 'admin_brokers';

		$app->render('admin/broker_write.php', array('current_menu'=>$current_menu));
	});

	$app->post('/brokers/write', function () use ($app, $log) {
		$company = $app->request->post('company');
		// $logo = $app->request->post('logo');
		$company_type = $app->request->post('company_type');
		$is_open = $app->request->post('is_open');
		$is_main = $app->request->post('is_main');

		$url = $app->request->post('url');
		$url2 = $app->request->post('url2');
		$domestic = $app->request->post('domestic');
		$overseas = $app->request->post('overseas');
		$fx = $app->request->post('fx');
		$dma = $app->request->post('dma');

		$system_trading_name = $app->request->post('system_trading_name');
		$system_trading_name1 = $app->request->post('system_trading_name1');
		$system_trading_name2 = $app->request->post('system_trading_name2');

		$api_name = $app->request->post('api_name');
		$api_name1 = $app->request->post('api_name1');
		$api_name2 = $app->request->post('api_name2');

		if(empty($company)){
			$company = '';
		}

		if(empty($company_type)){
			$company_type = '증권사';
		}

		if(!empty($is_open) && $is_open == '1'){
			$is_open = '1';
		}else{
			$is_open = '0';
		}

		if(!empty($is_main) && $is_main == '1'){
			$is_main = '1';
		}else{
			$is_main = '0';
		}

		if(empty($url)){
			$url = '';
		}

		if(empty($url2)){
			$url2 = '';
		}

		if(empty($domestic)){
			$domestic = '';
		}

		if(empty($overseas)){
			$overseas = '';
		}

		if(empty($fx)){
			$fx = '';
		}

		if(empty($dma)){
			$dma = '';
		}

		if(empty($system_trading_name)){
			$system_trading_name = '';
		}

		if(empty($system_trading_name1)){
			$system_trading_name1 = '';
		}

		if(empty($system_trading_name2)){
			$system_trading_name2 = '';
		}

		if(empty($api_name)){
			$api_name = '';
		}

		if(empty($api_name1)){
			$api_name1 = '';
		}

		if(empty($api_name2)){
			$api_name2 = '';
		}

		$broker_logo_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['logo']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['logo']['name'];
				$filesize = $_FILES['logo']['size'];
				$filetmpname = $_FILES['logo']['tmp_name'];
				$filetype = $_FILES['logo']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/write');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/write');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/write');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'logo_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'logo_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$broker_logo_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/write');
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
		}

		$broker_logo_s_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['logo_s']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['logo_s']['name'];
				$filesize = $_FILES['logo_s']['size'];
				$filetmpname = $_FILES['logo_s']['tmp_name'];
				$filetype = $_FILES['logo_s']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/write');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/write');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/write');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'logo_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'logo_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$broker_logo_s_url = $app->config('broker.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/write');
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
		}

		$system_trading_image_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['system_trading_image']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['system_trading_image']['name'];
				$filesize = $_FILES['system_trading_image']['size'];
				$filetmpname = $_FILES['system_trading_image']['tmp_name'];
				$filetype = $_FILES['system_trading_image']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/write');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/write');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/write');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$system_trading_image_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/write');
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
		}

		$system_trading_image1_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['system_trading_image1']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['system_trading_image1']['name'];
				$filesize = $_FILES['system_trading_image1']['size'];
				$filetmpname = $_FILES['system_trading_image1']['tmp_name'];
				$filetype = $_FILES['system_trading_image1']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/write');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/write');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/write');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$system_trading_image1_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/write');
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
		}

		$system_trading_image2_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['system_trading_image2']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['system_trading_image2']['name'];
				$filesize = $_FILES['system_trading_image2']['size'];
				$filetmpname = $_FILES['system_trading_image2']['tmp_name'];
				$filetype = $_FILES['system_trading_image2']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/write');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/write');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/write');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$system_trading_image2_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/write');
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
		}

		$api_image_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['api_image']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['api_image']['name'];
				$filesize = $_FILES['api_image']['size'];
				$filetmpname = $_FILES['api_image']['tmp_name'];
				$filetype = $_FILES['api_image']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/write');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/write');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/write');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'api_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'api_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$api_image_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/write');
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
		}

		$api_image1_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['api_image1']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['api_image1']['name'];
				$filesize = $_FILES['api_image1']['size'];
				$filetmpname = $_FILES['api_image1']['tmp_name'];
				$filetype = $_FILES['api_image1']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/write');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/write');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/write');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'api_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'api_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$api_image1_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/write');
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
		}

		$api_image2_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['api_image2']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['api_image2']['name'];
				$filesize = $_FILES['api_image2']['size'];
				$filetmpname = $_FILES['api_image2']['tmp_name'];
				$filetype = $_FILES['api_image2']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/write');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/write');
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/write');
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'api_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'api_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$api_image2_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/write');
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/write');
		}

        $max = $app->db->selectOne('broker', 'sorting', array(), array('sorting'=>'desc'));
        
		$new_broker_id = $app->db->insert('broker', array(
			'company'=>$company,
			'logo'=>$broker_logo_url,
			'logo_s'=>$broker_logo_s_url,
			'company_type'=>$company_type,
			'is_open'=>$is_open,
			'is_main'=>$is_main,
			'url'=>$url,
			'url2'=>$url2,
			'domestic'=>$domestic,
			'overseas'=>$overseas,
			'fx'=>$fx,
			'dma'=>$dma,
            'sorting'=>$max['sorting'] + 1
		));

		if(!empty($system_trading_name) || !empty($system_trading_image_url)){
			$app->db->insert('system_trading_tool', array(
				'broker_id'=>$new_broker_id,
				'name'=>$system_trading_name,
				'logo'=>$system_trading_image_url
			));
		}

		if(!empty($system_trading_name1) || !empty($system_trading_image1_url)){
			$app->db->insert('system_trading_tool', array(
				'broker_id'=>$new_broker_id,
				'name'=>$system_trading_name1,
				'logo'=>$system_trading_image1_url
			));
		}

		if(!empty($system_trading_name2) || !empty($system_trading_image2_url)){
			$app->db->insert('system_trading_tool', array(
				'broker_id'=>$new_broker_id,
				'name'=>$system_trading_name2,
				'logo'=>$system_trading_image1_url
			));
		}

		if(!empty($api_name) || !empty($api_image_url)){
			$app->db->insert('api_tool', array(
				'broker_id'=>$new_broker_id,
				'name'=>$api_name,
				'logo'=>$api_image_url
			));
		}

		if(!empty($api_name1) || !empty($api_image1_url)){
			$app->db->insert('api_tool', array(
				'broker_id'=>$new_broker_id,
				'name'=>$api_name1,
				'logo'=>$api_image1_url
			));
		}

		if(!empty($api_name2) || !empty($api_image2_url)){
			$app->db->insert('api_tool', array(
				'broker_id'=>$new_broker_id,
				'name'=>$api_name2,
				'logo'=>$api_image1_url
			));
		}

		$app->redirect('/admin/brokers');
	});

	$app->post('/brokers/edit', function () use ($app, $log) {
		$brokers = $app->db->select('broker');

		foreach($brokers as $broker){
			$target_sorting_no = $app->request->post('broker_'.$broker['broker_id']);
			if(is_numeric($target_sorting_no)){
				$app->db->update('broker', array('sorting'=>intval($target_sorting_no)), array('broker_id'=>$broker['broker_id']));
			}
		}

		$app->redirect('/admin/brokers');
	});

	$app->get('/brokers/delete_tool', function () use ($app, $log) {
		$broker_id = $app->request->get('broker_id');
		$tool_type = $app->request->get('tool_type');
		$tool_id = $app->request->get('tool_id');

		if(empty($broker_id)){
			$app->flash('error', '오류발생');
			$app->redirect('/admin/brokers');
		}

		if(empty($tool_type)){
			$app->flash('error', '오류발생');
			$app->redirect('/admin/brokers/'.$broker_id);
		}

		if(empty($tool_id)){
			$app->flash('error', '오류발생');
			$app->redirect('/admin/brokers/'.$broker_id);
		}

		$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$broker_id));

		if(empty($broker)){
			$app->halt(404, 'not found');
		}

		if($tool_type == 'a'){
			// $a_tools = $app->db->selectCount('api_tool', array('broker_id'=>$broker_id));
			$app->db->delete('api_tool', array('broker_id'=>$broker_id, 'tool_id'=>$tool_id));
		}else{
			$s_tools_count = $app->db->selectCount('system_trading_tool', array('broker_id'=>$broker_id));
			// 1개밖에 없으면 안됨
			if($s_tools_count == 1){
				$app->flash('error', '시스템트레이딩이 최소 1개 있어야합니다');
				$app->redirect('/admin/brokers/'.$broker_id);
			}
			// 시스템트레이딩 값을 지우면 해당 값을 가지고 있던 전략 테이블의 값을 0으로 변경
			$app->db->update('strategy', array('tool_id'=>0), array('tool_id'=>$tool_id));
			// 삭제
			$app->db->delete('system_trading_tool', array('broker_id'=>$broker_id, 'tool_id'=>$tool_id));
		}

		$app->redirect('/admin/brokers/'.$broker_id);
	});

	$app->post('/brokers/:id/add_tool', function ($id) use ($app, $log) {
		$system_trading_name = $app->request->post('system_trading_name');
		$api_name = $app->request->post('api_name');

		$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$id));

		if(empty($broker)){
			$app->halt(404, 'not found');
		}

		$system_trading_image_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		if(isset($_FILES['system_trading_image'])){
		switch($_FILES['system_trading_image']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['system_trading_image']['name'];
				$filesize = $_FILES['system_trading_image']['size'];
				$filetmpname = $_FILES['system_trading_image']['tmp_name'];
				$filetype = $_FILES['system_trading_image']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/'.$id);
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/'.$id);
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/'.$id);
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/'.$id);
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/'.$id);
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$system_trading_image_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/'.$id);
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/'.$id);
		}
		}

		$api_image_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		if(isset($_FILES['api_image'])){
		switch($_FILES['api_image']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['api_image']['name'];
				$filesize = $_FILES['api_image']['size'];
				$filetmpname = $_FILES['api_image']['tmp_name'];
				$filetype = $_FILES['api_image']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/'.$id);
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/'.$id);
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/'.$id);
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/'.$id);
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'api_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'api_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$api_image_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/'.$id);
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/'.$idgjgg);
		}
		}

		if(!empty($system_trading_name) || !empty($system_trading_image_url)){
			$exist_count = $app->db->selectCount('system_trading_tool', array('broker_id'=>$id));
			if($exist_count >= 3){
				$app->flash('error', '3개 이상 입력할수 없습니다');
				$app->redirect('/admin/brokers/'.$id);
			}

			$app->db->insert('system_trading_tool', array(
				'broker_id'=>$id,
				'name'=>$system_trading_name,
				'logo'=>$system_trading_image_url
			));
		}

		if(!empty($api_name) || !empty($api_image_url)){
			$exist_count = $app->db->selectCount('api_tool', array('broker_id'=>$id));
			if($exist_count >= 3){
				$app->flash('error', '3개 이상 입력할수 없습니다');
				$app->redirect('/admin/brokers/'.$id);
			}

			$app->db->insert('api_tool', array(
				'broker_id'=>$id,
				'name'=>$api_name,
				'logo'=>$api_image_url
			));
		}

		$app->redirect('/admin/brokers/'.$id);
	});

	$app->post('/brokers/:id/edit_tool', function ($id) use ($app, $log) {
		$tool_type = $app->request->post('tool_type');
		$tool_id = $app->request->post('tool_id');
		$system_trading_name = $app->request->post('system_trading_name');
		$api_name = $app->request->post('api_name');

		$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$id));

		if(empty($broker)){
			$app->halt(404, 'not found');
		}

		if(empty($tool_type)){
			$tool_type == 's';
		}

		if($tool_type == 'a'){
			$tool = $app->db->selectOne('api_tool', '*', array('tool_id'=>$tool_id));

			if(empty($tool)){
				$app->halt(404, 'not found');
			}

			$api_image_url = $tool['logo'];
			$savePath = $app->config('broker.path');
			$max_file_size = 1024 * 1024 * 5;

			if(isset($_FILES['api_image'])){
			switch($_FILES['api_image']['error']){
				case UPLOAD_ERR_OK:
					$filename = $_FILES['api_image']['name'];
					$filesize = $_FILES['api_image']['size'];
					$filetmpname = $_FILES['api_image']['tmp_name'];
					$filetype = $_FILES['api_image']['type'];
					$tmpfileext = explode('.', $filename);
					$fileext = $tmpfileext[count($tmpfileext)-1];

					// check filesize
					if($filesize > $max_file_size){
						$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
						$app->redirect('/admin/brokers/'.$id);
					}

					if(strpos($filetype, 'image') === false){
						$app->flash('error', '이미지 파일만 업로드 가능합니다.');
						$app->redirect('/admin/brokers/'.$id);
					}

					// check upload valid ext
					if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
						$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
						$app->redirect('/admin/brokers/'.$id);
					}

					/*
					$image = getimagesize($filetmpname);
					$width = 360;
					$height = 360;

					if($image[0] < $width || $image[1] < $height){
						$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
						$app->redirect('/admin/brokers/write');
					}
					*/

					// upload correct method
					if(!is_uploaded_file($filetmpname)){
						$app->flash('error', '정상적인 방법으로 업로드해주세요');
						$app->redirect('/admin/brokers/'.$id);
					}

					// if folder is not exist, create folder
					if(!is_dir($savePath)){
						mkdir($savePath, 0705);
						chmod($savePath, 0707);
					}

					// filename modify
					$saveFilename = 'api_'.md5(uniqid(rand(), true));

					// filename same check
					while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
						$saveFilename = 'api_'.md5(uniqid(rand(), true));
					}

					$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
					// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
					// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
					$api_image_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
					// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
					// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

					if(!move_uploaded_file($filetmpname, $finalFilename)){
						$app->flash('error', '업로드에 실패하였습니다');
						$app->redirect('/admin/brokers/'.$id);
					}

					// 썸네일생성 s, m
					// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
					// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
					break;
				case UPLOAD_ERR_INI_SIZE:
					$app->flash('error', '업로드 가능 용량을 초과하였습니다');
					$app->redirect('/admin/brokers/'.$id);
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$app->flash('error', '업로드 가능 용량을 초과하였습니다');
					$app->redirect('/admin/brokers/'.$id);
					break;
				case UPLOAD_ERR_PARTIAL:
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/'.$id);
					break;
				case UPLOAD_ERR_NO_FILE:
					break;
				default:
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/'.$idgjgg);
			}
			}

			if(!empty($api_name) || !empty($api_image_url)){
				$app->db->update('api_tool', array(
					'name'=>$api_name,
					'logo'=>$api_image_url
				), array('tool_id'=>$tool_id));
			}
		}else{
			$tool = $app->db->selectOne('system_trading_tool', '*', array('tool_id'=>$tool_id));

			if(empty($tool)){
				$app->halt(404, 'not found');
			}

			$system_trading_image_url = $tool['logo'];
			$savePath = $app->config('broker.path');
			$max_file_size = 1024 * 1024 * 5;

			if(isset($_FILES['system_trading_image'])){
			switch($_FILES['system_trading_image']['error']){
				case UPLOAD_ERR_OK:
					$filename = $_FILES['system_trading_image']['name'];
					$filesize = $_FILES['system_trading_image']['size'];
					$filetmpname = $_FILES['system_trading_image']['tmp_name'];
					$filetype = $_FILES['system_trading_image']['type'];
					$tmpfileext = explode('.', $filename);
					$fileext = $tmpfileext[count($tmpfileext)-1];

					// check filesize
					if($filesize > $max_file_size){
						$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
						$app->redirect('/admin/brokers/'.$id);
					}

					if(strpos($filetype, 'image') === false){
						$app->flash('error', '이미지 파일만 업로드 가능합니다.');
						$app->redirect('/admin/brokers/'.$id);
					}

					// check upload valid ext
					if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
						$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
						$app->redirect('/admin/brokers/'.$id);
					}

					/*
					$image = getimagesize($filetmpname);
					$width = 360;
					$height = 360;

					if($image[0] < $width || $image[1] < $height){
						$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
						$app->redirect('/admin/brokers/'.$id);
					}
					*/

					// upload correct method
					if(!is_uploaded_file($filetmpname)){
						$app->flash('error', '정상적인 방법으로 업로드해주세요');
						$app->redirect('/admin/brokers/'.$id);
					}

					// if folder is not exist, create folder
					if(!is_dir($savePath)){
						mkdir($savePath, 0705);
						chmod($savePath, 0707);
					}

					// filename modify
					$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));

					// filename same check
					while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
						$saveFilename = 'system_trading_'.md5(uniqid(rand(), true));
					}

					$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
					// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
					// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
					$system_trading_image_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
					// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
					// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

					if(!move_uploaded_file($filetmpname, $finalFilename)){
						$app->flash('error', '업로드에 실패하였습니다');
						$app->redirect('/admin/brokers/'.$id);
					}

					// 썸네일생성 s, m
					// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
					// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
					break;
				case UPLOAD_ERR_INI_SIZE:
					$app->flash('error', '업로드 가능 용량을 초과하였습니다');
					$app->redirect('/admin/brokers/'.$id);
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$app->flash('error', '업로드 가능 용량을 초과하였습니다');
					$app->redirect('/admin/brokers/'.$id);
					break;
				case UPLOAD_ERR_PARTIAL:
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/'.$id);
					break;
				case UPLOAD_ERR_NO_FILE:
					break;
				default:
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/'.$id);
			}
			}

			if(!empty($system_trading_name) || !empty($system_trading_image_url)){
				$app->db->update('system_trading_tool', array(
					'name'=>$system_trading_name,
					'logo'=>$system_trading_image_url
				), array('tool_id'=>$tool_id));
			}
		}

		$app->redirect('/admin/brokers/'.$id);
	});

	$app->post('/brokers/:id/edit', function ($id) use ($app, $log) {
		$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$id));

		if(empty($broker)){
			$app->halt(404, 'not found');
		}

		$company = $app->request->post('company');
		// $logo = $app->request->post('logo');
		$company_type = $app->request->post('company_type');
		$is_open = $app->request->post('is_open');
		$is_main = $app->request->post('is_main');

		$url = $app->request->post('url');
		$url2 = $app->request->post('url2');
		$domestic = $app->request->post('domestic');
		$overseas = $app->request->post('overseas');
		$fx = $app->request->post('fx');
		$dma = $app->request->post('dma');

		// $system_trading_name = $app->request->post('system_trading_name');
		// $system_trading_name1 = $app->request->post('system_trading_name1');
		// $system_trading_image = $app->request->post('system_trading_image');

		// $api_name = $app->request->post('api_name');
		// $api_name1 = $app->request->post('api_name1');
		// $api_image = $app->request->post('api_image');

		if(empty($company)){
			$company = '';
		}

		if(empty($company_type)){
			$company_type = '증권사';
		}

		if(!empty($is_open) && $is_open == '1'){
			$is_open = '1';
		}else{
			$is_open = '0';
		}

		if(!empty($is_main) && $is_main == '1'){
			$is_main = '1';
		}else{
			$is_main = '0';
		}

		if(empty($url)){
			$url = '';
		}
		if(empty($url2)){
			$url2 = '';
		}

		if(empty($domestic)){
			$domestic = '';
		}

		if(empty($overseas)){
			$overseas = '';
		}

		if(empty($fx)){
			$fx = '';
		}

		if(empty($dma)){
			$dma = '';
		}

		/*
		if(empty($system_trading_name)){
			$system_trading_name = '';
		}

		if(empty($system_trading_name1)){
			$system_trading_name1 = '';
		}

		if(empty($api_name)){
			$api_name = '';
		}

		if(empty($api_name1)){
			$api_name1 = '';
		}
		*/

		$broker_logo_url = $broker['logo'];
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['logo']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['logo']['name'];
				$filesize = $_FILES['logo']['size'];
				$filetmpname = $_FILES['logo']['tmp_name'];
				$filetype = $_FILES['logo']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/'.$id);
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/'.$id);
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/'.$id);
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/'.$id);
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'logo_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'logo_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$broker_logo_url = $app->config('broker.url').'/'.$saveFilename.'.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/'.$id);
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/'.$id);
		}

		$broker_logo_s_url = $broker['logo_s'];
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024 * 5;

		switch($_FILES['logo_s']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['logo_s']['name'];
				$filesize = $_FILES['logo_s']['size'];
				$filetmpname = $_FILES['logo_s']['tmp_name'];
				$filetype = $_FILES['logo_s']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/admin/brokers/'.$id);
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/admin/brokers/'.$id);
				}

				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/admin/brokers/'.$id);
				}

				/*
				$image = getimagesize($filetmpname);
				$width = 360;
				$height = 360;

				if($image[0] < $width || $image[1] < $height){
					$app->flash('error', $width.' * '.$height.' 이상 크기의 이미지를 업로드해주세요');
					$app->redirect('/admin/brokers/write');
				}
				*/

				// upload correct method
				if(!is_uploaded_file($filetmpname)){
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/admin/brokers/'.$id);
				}

				// if folder is not exist, create folder
				if(!is_dir($savePath)){
					mkdir($savePath, 0705);
					chmod($savePath, 0707);
				}

				// filename modify
				$saveFilename = 'logo_'.md5(uniqid(rand(), true));

				// filename same check
				while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
					$saveFilename = 'logo_'.md5(uniqid(rand(), true));
				}

				$finalFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilename = $savePath.'/'.$saveFilename.'_s.'.$fileext;
				// $finalThumbFilenameM = $savePath.'/'.$saveFilename.'_m.'.$fileext;
				$broker_logo_s_url = $app->config('broker.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_s_url = $app->config('profile.url').'/'.$saveFilename.'_s.'.$fileext;
				// $profile_m_url = $app->config('profile.url').'/'.$saveFilename.'_m.'.$fileext;

				if(!move_uploaded_file($filetmpname, $finalFilename)){
					$app->flash('error', '업로드에 실패하였습니다');
					$app->redirect('/admin/brokers/'.$id);
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 128, 128, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/'.$id);
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/admin/brokers/'.$id);
		}

		$app->db->update('broker', array(
			'company'=>$company,
			'logo'=>$broker_logo_url,
			'logo_s'=>$broker_logo_s_url,
			'company_type'=>$company_type,
			'is_open'=>$is_open,
			'is_main'=>$is_main,
			'url'=>$url,
			'url2'=>$url2,
			'domestic'=>$domestic,
			'overseas'=>$overseas,
			'fx'=>$fx,
			'dma'=>$dma
		), array('broker_id'=>$id));

		$app->redirect('/admin/brokers/'.$id);
	});

	$app->get('/brokers/:id/delete', function ($id) use ($app, $log) {
		// 브로커 삭제를 허용하게 되는 경우 전략이나 문의내역 같은 부분도 무결성을 위해 수정을 해야함
        $cnt = $app->db->selectCount('strategy', array('broker_id'=>$id, 'is_delete'=>'0'));
        if ($cnt) {
			$app->flash('error', '사용중인 전략이 있습니다');
		    $app->redirect('/admin/brokers');
        }

        $app->db->delete('broker', array('broker_id'=>$id));
        $app->db->delete('system_trading_tool', array('broker_id'=>$id));
        $app->db->delete('api_tool', array('broker_id'=>$id));

		$app->redirect('/admin/brokers');
	});

	$app->get('/brokers/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_brokers';

		$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$id));

		if(empty($broker)){
			$app->halt(404, 'not found');
		}

		$s_tools = $app->db->select('system_trading_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
		$a_tools = $app->db->select('api_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
		$broker['system_trading_tools'] = $s_tools;
		$broker['api_tools'] = $a_tools;

		$app->render('admin/broker_view.php', array('current_menu'=>$current_menu, 'broker'=>$broker));
	});

	$app->get('/notice', function () use ($app, $log) {
		$current_menu = 'admin_notice';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$posts = $app->db->select('notice', '*', array(), array('notice_id'=>'desc'), $start, $count);
		$total = $app->db->selectCount('notice');
		$total_page = ceil($total / $count);

		$app->render('admin/notice.php', array('current_menu'=>$current_menu, 'posts'=>$posts, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->get('/notice/write', function () use ($app, $log) {
		$current_menu = 'admin_notice';

        $post['open_h'] = '00';
        $post['open_m'] = '00';
        $post['open_date'] = date('Y-m-d');

		$app->render('admin/notice_write.php', array('current_menu'=>$current_menu, 'post'=>$post));
	});

	$app->post('/notice/write', function () use ($app, $log) {
		$is_open = $app->request->post('is_open');
		$subject = $app->request->post('subject');
		$contents = $app->request->post('contents');

		if(!empty($is_open) && $is_open){
			$is_open_flag = '1';
		}else{
			$is_open_flag = '0';
		}

		if(empty($subject)){
			$app->redirect('/admin/notice/write');
		}

		if(empty($contents)){
			$app->redirect('/admin/notice/write');
		}

        $param['open_date'] = sprintf("%s %02d:%02d:00", $app->request->post('open_day'), $app->request->post('open_h'), $app->request->post('open_m'));
        $param['subject'] = $subject;
        $param['contents'] = $contents;
        $param['is_open'] = $is_open_flag;

		$new_notice_id = $app->db->insert('notice', $param);

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['img']['name'])) {

            $attachment_url = '';
            $savePath = $app->config('notice.path');
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['img']['name'] as $key => $val){
                switch($_FILES['img']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['img']['name'][$key];
                        $filesize = $_FILES['img']['size'][$key];
                        $filetmpname = $_FILES['img']['tmp_name'][$key];
                        $filetype = $_FILES['img']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // check filesize
                        if($filesize > $max_file_size){
                            $app->flash('error', '이미지파일은 5MB 이하로 업로드해주세요.');
                            $app->redirect('/admin/notice/write');
                        }

                        if(strpos($filetype, 'image') === false){
                            $app->flash('error', '이미지 파일만 업로드 가능합니다.');
                            $app->redirect('/admin/notice/write');
                        }

                        // check upload valid ext
                        if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
                            $app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
                            $app->redirect('/admin/notice/write');
                        }


                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
                            $app->redirect('/admin/notice/write');
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
                        $attachment_url = $app->config('notice.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
                            $app->redirect('/admin/notice/write');
                        }

                        $app->db->insert('notice_file', array('notice_id'=>$new_notice_id, 'file_type'=>'IMG', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/notice/write');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/notice/write');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/notice/write');
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/notice/write');
                }
            }
        }

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['att']['name'])) {

            $attachment_url = '';
            $savePath = $app->config('notice.path');
            $max_file_size = 1024 * 1024 * 50;

            foreach($_FILES['att']['name'] as $key => $val){
                switch($_FILES['att']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['att']['name'][$key];
                        $filesize = $_FILES['att']['size'][$key];
                        $filetmpname = $_FILES['att']['tmp_name'][$key];
                        $filetype = $_FILES['att']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
                            $app->redirect('/admin/notice/write');
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
                        $attachment_url = $app->config('notice.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
                            $app->redirect('/admin/notice/write');
                        }

                        $app->db->insert('notice_file', array('notice_id'=>$new_notice_id, 'file_type'=>'FILE', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/notice/write');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/notice/write');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/notice/write');
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/notice/write');
                }
            }
        }
        
		$app->flash('error', '등록되었습니다.');
		$app->redirect('/admin/notice');
	});

	$app->post('/notice/edit', function () use ($app, $log) {
		$exec = $app->request->post('exec');
		$input_notice_ids = $app->request->post('notice_ids');

		$edit_notice_ids = array();
		foreach($input_notice_ids as $notice_id){
			if(empty($notice_id)) continue;
			if(!is_numeric($notice_id)) continue;
			if(in_array($notice_id, $edit_notice_ids)) continue;

			$edit_notice_ids[] = $notice_id;
		}

		if(count($edit_notice_ids) == 0) $app->redirect('/admin/notice');

		if($exec == 'edit'){
			$is_open_flag = $app->request->post('is_open_flag');

			if(!empty($is_open_flag) && $is_open_flag){
				$is_open_flag = '1';
			}else{
				$is_open_flag = '0';
			}

			$app->db->conn->query('UPDATE notice SET is_open = \''.$is_open_flag.'\' WHERE notice_id IN ('.implode(',', $edit_notice_ids).')');
		}else{
			$app->db->conn->query('DELETE FROM notice WHERE notice_id IN ('.implode(',', $edit_notice_ids).')');
		}

		$app->redirect('/admin/notice');
	});

	$app->get('/notice/:id/edit', function ($id) use ($app, $log) {
		$current_menu = 'admin_notice';

		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('notice', '*', array('notice_id'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}
        preg_match('/(..):(..):(..)$/', $post['open_date'], $match);
        $post['open_h'] = $match[1];
        $post['open_m'] = $match[2];

        $imgs = $app->db->select('notice_file', '*', array('notice_id'=>$id, 'file_type'=>'IMG'));
        $atts = $app->db->select('notice_file', '*', array('notice_id'=>$id, 'file_type'=>'FILE'));

		$app->render('admin/notice_write.php', array('current_menu'=>$current_menu, 'post'=>$post, 'imgs'=>$imgs, 'atts'=>$atts));
	});

	$app->post('/notice/:id/edit', function ($id) use ($app, $log) {
		$page = $app->request->post('page');
        $redirect = "/notice/".$id."/edit";

		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('notice', '*', array('notice_id'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$del_files = $app->request->post('del_files');
		$is_open = $app->request->post('is_open');
		$subject = $app->request->post('subject');
		$contents = $app->request->post('contents');

		if(empty($subject)){
			$subject = $post['subject'];
		}

		if(empty($contents_body)){
			$contents_body = $post['contents'];
		}

		if(!empty($is_open) && $is_open){
			$is_open_flag = '1';
		}else{
			$is_open_flag = '0';
		}

        $param['open_date'] = sprintf("%s %02d:%02d:00", $app->request->post('open_day'), $app->request->post('open_h'), $app->request->post('open_m'));
        $param['subject'] = $subject;
        $param['contents'] = $contents;
        $param['is_open'] = $is_open_flag;

		$app->db->update('notice', $param, array('notice_id'=>$id));


        $savePath = $app->config('notice.path');

        if (is_array($del_files)) {
            foreach ($del_files as $v) {
                $t = $app->db->selectOne('notice_file', '*', array('fid'=>$v));
                unlink($savePath.'/'.$t['save_name']);
                $app->db->delete('notice_file', array('fid'=>$v));
            }
        }

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['img']['name'])) {

            $attachment_url = '';
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['img']['name'] as $key => $val){
                switch($_FILES['img']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['img']['name'][$key];
                        $filesize = $_FILES['img']['size'][$key];
                        $filetmpname = $_FILES['img']['tmp_name'][$key];
                        $filetype = $_FILES['img']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // check filesize
                        if($filesize > $max_file_size){
                            $app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
			                $app->redirect($redirect);
                        }

                        if(strpos($filetype, 'image') === false){
                            $app->flash('error', '이미지 파일만 업로드 가능합니다.');
			                $app->redirect($redirect);
                        }

                        // check upload valid ext
                        if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
                            $app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
			                $app->redirect($redirect);
                        }


                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
			                $app->redirect($redirect);
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
                        $attachment_url = $app->config('education.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
			                $app->redirect($redirect);
                        }

                        $app->db->insert('notice_file', array('notice_id'=>$id, 'file_type'=>'IMG', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			                $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			                $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
			                $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
			            $app->redirect($redirect);
                }
            }
        }

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['att']['name'])) {

            $attachment_url = '';
            $savePath = $app->config('notice.path');
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['att']['name'] as $key => $val){
                switch($_FILES['att']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['att']['name'][$key];
                        $filesize = $_FILES['att']['size'][$key];
                        $filetmpname = $_FILES['att']['tmp_name'][$key];
                        $filetype = $_FILES['att']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
			                $app->redirect($redirect);
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
                        $attachment_url = $app->config('notice.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
			                $app->redirect($redirect);
                        }

                        $app->db->insert('notice_file', array('notice_id'=>$id, 'file_type'=>'FILE', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			            $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			            $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
			            $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
			            $app->redirect($redirect);
                }
            }
        }


		$app->flash('error', '수정되었습니다.');
		$app->redirect('/admin/notice');
	});

	$app->get('/notice/:id/delete', function ($id) use ($app, $log) {
		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('notice', '*', array('notice_id'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('notice', array('notice_id'=>$id));
        
        $savePath = $app->config('notice.path');
        $files = $app->db->select('notice_file', '*', array('notice_id'=>$id));
        foreach ($files as $v) {
            unlink($savePath.'/'.$v['save_name']);
        }
		$app->db->delete('notice_file', array('notice_id'=>$id));


		$app->flash('error', '삭제되었습니다.');
		$app->redirect('/admin/notice?page='.$page);
	});

	$app->get('/notice/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_notice';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('notice', '*', array('notice_id'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$atts = $app->db->select('notice_file', '*', array('notice_id'=>$id, 'file_type'=>'FILE'));

		$post['attachments'] = $app->db->select('attachment', '*', array('notice_id'=>$post['notice_id']));

		$app->render('admin/notice_view.php', array('current_menu'=>$current_menu, 'post'=>$post, 'page'=>$page, 'atts'=>$atts));
	});


    // 교육관리
   
	$app->get('/education', function () use ($app, $log) {
		$current_menu = 'admin_notice';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$posts = $app->db->select('education', '*', array(), array('eidx'=>'desc'), $start, $count);
		$total = $app->db->selectCount('education');
		$total_page = ceil($total / $count);

		$app->render('admin/education.php', array('current_menu'=>$current_menu, 'posts'=>$posts, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->get('/education/write', function () use ($app, $log) {
		$current_menu = 'admin_notice';

        $post['e_start_h'] = '00';
        $post['e_start_m'] = '00';
        $post['e_end_h'] = '00';
        $post['e_end_m'] = '00';
        $post['a_start_h'] = '00';
        $post['a_start_m'] = '00';
        $post['a_end_h'] = '00';
        $post['a_end_m'] = '00';

		$app->render('admin/education_write.php', array('current_menu'=>$current_menu, 'post'=>$post));
	});

	$app->post('/education/write', function () use ($app, $log) {
		$type = $app->request->post('type');
		$subject = $app->request->post('subject');
		$contents_body = $app->request->post('contents_body');

		if(empty($type)){
			$app->redirect('/admin/education/write');
		}

		if(empty($subject)){
			$app->redirect('/admin/education/write');
		}

		if(empty($contents_body)){
			$app->redirect('/admin/education/write');
		}

        $param = array(
                    'type'  => $type,
                    'subject'=>$subject,
                    'contents'=>$contents_body,
                    'uid' => $_SESSION['user']['uid']
                );

        if ($type == 'OFF') {
            $param['e_start_date'] = sprintf("%s %02d:%02d:00", $app->request->post('e_start_day'), $app->request->post('e_start_h'), $app->request->post('e_start_m'));
            $param['e_end_date'] = sprintf("%s %02d:%02d:00", $app->request->post('e_end_day'), $app->request->post('e_end_h'), $app->request->post('e_end_m'));

            $param['a_start_date'] = sprintf("%s %02d:%02d:00", $app->request->post('a_start_day'), $app->request->post('a_start_h'), $app->request->post('a_start_m'));
            $param['a_end_date'] = sprintf("%s %02d:%02d:00", $app->request->post('a_end_day'), $app->request->post('a_end_h'), $app->request->post('a_end_m'));
        }


		$new_eidx = $app->db->insert('education', $param);


		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['img']['name'])) {

            $attachment_url = '';
            $savePath = $app->config('education.path');
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['img']['name'] as $key => $val){
                switch($_FILES['img']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['img']['name'][$key];
                        $filesize = $_FILES['img']['size'][$key];
                        $filetmpname = $_FILES['img']['tmp_name'][$key];
                        $filetype = $_FILES['img']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // check filesize
                        if($filesize > $max_file_size){
                            $app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
                            $app->redirect('/admin/education/write');
                        }

                        if(strpos($filetype, 'image') === false){
                            $app->flash('error', '이미지 파일만 업로드 가능합니다.');
                            $app->redirect('/admin/education/write');
                        }

                        // check upload valid ext
                        if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
                            $app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
                            $app->redirect('/admin/education/write');
                        }


                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
                            $app->redirect('/admin/education/write');
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
                        $attachment_url = $app->config('education.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
                            $app->redirect('/admin/education/write');
                        }

                        $app->db->insert('education_file', array('eidx'=>$new_eidx, 'file_type'=>'IMG', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/education/write');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/education/write');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/education/write');
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/education/write');
                }
            }
        }

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['att']['name'])) {

            $attachment_url = '';
            $savePath = $app->config('education.path');
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['att']['name'] as $key => $val){
                switch($_FILES['att']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['att']['name'][$key];
                        $filesize = $_FILES['att']['size'][$key];
                        $filetmpname = $_FILES['att']['tmp_name'][$key];
                        $filetype = $_FILES['att']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
                            $app->redirect('/admin/education/write');
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
                        $attachment_url = $app->config('education.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
                            $app->redirect('/admin/education/write');
                        }

                        $app->db->insert('education_file', array('eidx'=>$new_eidx, 'file_type'=>'FILE', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/education/write');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/education/write');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/education/write');
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/education/write');
                }
            }
        }

		$app->redirect('/admin/education');
	});


	$app->get('/education/:id/edit', function ($id) use ($app, $log) {
		$current_menu = 'admin_notice';

		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('education', '*', array('eidx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

        if ($post['type'] == 'ON') {
            $post['e_start_h'] = '00';
            $post['e_start_m'] = '00';
            $post['e_end_h'] = '00';
            $post['e_end_m'] = '00';
            $post['a_start_h'] = '00';
            $post['a_start_m'] = '00';
            $post['a_end_h'] = '00';
            $post['a_end_m'] = '00';
        } else {
            preg_match('/(..):(..):(..)$/', $post['e_start_date'], $match);
            $post['e_start_h'] = $match[1];
            $post['e_start_m'] = $match[2];
            preg_match('/(..):(..):(..)$/', $post['e_end_date'], $match);
            $post['e_end_h'] = $match[1];
            $post['e_end_m'] = $match[2];
            preg_match('/(..):(..):(..)$/', $post['a_start_date'], $match);
            $post['a_start_h'] = $match[1];
            $post['a_start_m'] = $match[2];
            preg_match('/(..):(..):(..)$/', $post['a_end_date'], $match);
            $post['a_end_h'] = $match[1];
            $post['a_end_m'] = $match[2];
        }

        $imgs = $app->db->select('education_file', '*', array('eidx'=>$id, 'file_type'=>'IMG'));
        $atts = $app->db->select('education_file', '*', array('eidx'=>$id, 'file_type'=>'FILE'));

		$app->render('admin/education_write.php', array('current_menu'=>$current_menu, 'post'=>$post, 'imgs'=>$imgs, 'atts'=>$atts));
	});


	$app->post('/education/:id/edit', function ($id) use ($app, $log) {
        $redirect = "/admin/education/".$id."/edit";

		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('education', '*', array('eidx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$type = $app->request->post('type');
		$subject = $app->request->post('subject');
		$contents_body = $app->request->post('contents_body');
		$del_files = $app->request->post('del_files');

		if(empty($type)){
			$app->redirect($redirect);
		}

		if(empty($subject)){
			$app->redirect($redirect);
		}

		if(empty($contents_body)){
			$app->redirect($redirect);
		}

        $param = array(
                    'type'  => $type,
                    'subject'=>$subject,
                    'contents'=>$contents_body,
                    'uid' => $_SESSION['user']['uid']
                );

        if ($type == 'OFF') {
            $param['e_start_date'] = sprintf("%s %02d:%02d:00", $app->request->post('e_start_day'), $app->request->post('e_start_h'), $app->request->post('e_start_m'));
            $param['e_end_date'] = sprintf("%s %02d:%02d:00", $app->request->post('e_end_day'), $app->request->post('e_end_h'), $app->request->post('e_end_m'));

            $param['a_start_date'] = sprintf("%s %02d:%02d:00", $app->request->post('a_start_day'), $app->request->post('a_start_h'), $app->request->post('a_start_m'));
            $param['a_end_date'] = sprintf("%s %02d:%02d:00", $app->request->post('a_end_day'), $app->request->post('a_end_h'), $app->request->post('a_end_m'));
        }

		$app->db->update('education', $param, array('eidx'=>$id));

        $savePath = $app->config('education.path');

        if (is_array($del_files)) {
            foreach ($del_files as $v) {
                $t = $app->db->selectOne('education_file', '*', array('fid'=>$v));
                unlink($savePath.'/'.$t['save_name']);
                $app->db->delete('education_file', array('fid'=>$v));
            }
        }

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['img']['name'])) {

            $attachment_url = '';
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['img']['name'] as $key => $val){
                switch($_FILES['img']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['img']['name'][$key];
                        $filesize = $_FILES['img']['size'][$key];
                        $filetmpname = $_FILES['img']['tmp_name'][$key];
                        $filetype = $_FILES['img']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // check filesize
                        if($filesize > $max_file_size){
                            $app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
			                $app->redirect($redirect);
                        }

                        if(strpos($filetype, 'image') === false){
                            $app->flash('error', '이미지 파일만 업로드 가능합니다.');
			                $app->redirect($redirect);
                        }

                        // check upload valid ext
                        if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
                            $app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
			                $app->redirect($redirect);
                        }


                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
			                $app->redirect($redirect);
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
                        $attachment_url = $app->config('education.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
			                $app->redirect($redirect);
                        }

                        $app->db->insert('education_file', array('eidx'=>$id, 'file_type'=>'IMG', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			                $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			                $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
			                $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
			            $app->redirect($redirect);
                }
            }
        }

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['att']['name'])) {

            $attachment_url = '';
            $savePath = $app->config('education.path');
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['att']['name'] as $key => $val){
                switch($_FILES['att']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['att']['name'][$key];
                        $filesize = $_FILES['att']['size'][$key];
                        $filetmpname = $_FILES['att']['tmp_name'][$key];
                        $filetype = $_FILES['att']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
			                $app->redirect($redirect);
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
                        $attachment_url = $app->config('education.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
			                $app->redirect($redirect);
                        }

                        $app->db->insert('education_file', array('eidx'=>$id, 'file_type'=>'FILE', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			            $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			            $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
			            $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
			            $app->redirect($redirect);
                }
            }
        }

		$app->redirect('/admin/education');
	});

	$app->get('/education/:id/delete', function ($id) use ($app, $log) {
		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('education', '*', array('eidx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('education', array('eidx'=>$id));
        
        $savePath = $app->config('education.path');
        $files = $app->db->select('education_file', '*', array('eidx'=>$id));
        foreach ($files as $v) {
            unlink($savePath.'/'.$v['save_name']);
        }
		$app->db->delete('education_file', array('eidx'=>$id));

		$app->redirect('/admin/education?page='.$page);
	});
    // 교육관리 끝..


    // 미디어룸
   
	$app->get('/media', function () use ($app, $log) {
		$current_menu = 'admin_notice';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$posts = $app->db->select('media', '*', array(), array('midx'=>'desc'), $start, $count);
		$total = $app->db->selectCount('media');
		$total_page = ceil($total / $count);

		$app->render('admin/media.php', array('current_menu'=>$current_menu, 'posts'=>$posts, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->get('/media/write', function () use ($app, $log) {
		$current_menu = 'admin_notice';

		$app->render('admin/media_write.php', array('current_menu'=>$current_menu, 'post'=>$post));
	});

	$app->post('/media/write', function () use ($app, $log) {
		$subject = $app->request->post('subject');
		$contents = $app->request->post('contents');

		if(empty($subject)){
			$app->redirect('/admin/media/write');
		}

		if(empty($contents)){
			$app->redirect('/admin/media/write');
		}

        $param = array(
                    'subject'=>$subject,
                    'contents'=>$contents,
                    'uid' => $_SESSION['user']['uid']
                );


		$new_midx = $app->db->insert('media', $param);


		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['img']['name'])) {

            $attachment_url = '';
            $savePath = $app->config('media.path');
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['img']['name'] as $key => $val){
                switch($_FILES['img']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['img']['name'][$key];
                        $filesize = $_FILES['img']['size'][$key];
                        $filetmpname = $_FILES['img']['tmp_name'][$key];
                        $filetype = $_FILES['img']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // check filesize
                        if($filesize > $max_file_size){
                            $app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
                            $app->redirect('/admin/media/write');
                        }

                        if(strpos($filetype, 'image') === false){
                            $app->flash('error', '이미지 파일만 업로드 가능합니다.');
                            $app->redirect('/admin/media/write');
                        }

                        // check upload valid ext
                        if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
                            $app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
                            $app->redirect('/admin/media/write');
                        }


                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
                            $app->redirect('/admin/media/write');
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
                        $attachment_url = $app->config('media.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
                            $app->redirect('/admin/media/write');
                        }

                        $app->db->insert('media_file', array('midx'=>$new_midx, 'file_type'=>'IMG', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/media/write');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/media/write');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/media/write');
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/media/write');
                }
            }
        }

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['att']['name'])) {

            $attachment_url = '';
            $savePath = $app->config('media.path');
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['att']['name'] as $key => $val){
                switch($_FILES['att']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['att']['name'][$key];
                        $filesize = $_FILES['att']['size'][$key];
                        $filetmpname = $_FILES['att']['tmp_name'][$key];
                        $filetype = $_FILES['att']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
                            $app->redirect('/admin/media/write');
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
                        $attachment_url = $app->config('media.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
                            $app->redirect('/admin/media/write');
                        }

                        $app->db->insert('media_file', array('midx'=>$new_midx, 'file_type'=>'FILE', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/media/write');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
                        $app->redirect('/admin/media/write');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/media/write');
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
                        $app->redirect('/admin/media/write');
                }
            }
        }

		$app->redirect('/admin/media');
	});


	$app->get('/media/:id/edit', function ($id) use ($app, $log) {
		$current_menu = 'admin_notice';

		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('media', '*', array('midx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

        $imgs = $app->db->select('media_file', '*', array('midx'=>$id, 'file_type'=>'IMG'));
        $atts = $app->db->select('media_file', '*', array('midx'=>$id, 'file_type'=>'FILE'));

		$app->render('admin/media_write.php', array('current_menu'=>$current_menu, 'post'=>$post, 'imgs'=>$imgs, 'atts'=>$atts));
	});


	$app->post('/media/:id/edit', function ($id) use ($app, $log) {
        $redirect = "/admin/media/".$id."/edit";

		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('media', '*', array('midx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$subject = $app->request->post('subject');
		$contents = $app->request->post('contents');
		$del_files = $app->request->post('del_files');

		if(empty($subject)){
			$app->redirect($redirect);
		}

		if(empty($contents)){
			$app->redirect($redirect);
		}

        $param = array(
                    'subject'=>$subject,
                    'contents'=>$contents
                );

		$app->db->update('media', $param, array('midx'=>$id));

        $savePath = $app->config('media.path');

        if (is_array($del_files)) {
            foreach ($del_files as $v) {
                $t = $app->db->selectOne('media_file', '*', array('fid'=>$v));
                unlink($savePath.'/'.$t['save_name']);
                $app->db->delete('media_file', array('fid'=>$v));
            }
        }

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['img']['name'])) {

            $attachment_url = '';
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['img']['name'] as $key => $val){
                switch($_FILES['img']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['img']['name'][$key];
                        $filesize = $_FILES['img']['size'][$key];
                        $filetmpname = $_FILES['img']['tmp_name'][$key];
                        $filetype = $_FILES['img']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // check filesize
                        if($filesize > $max_file_size){
                            $app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
			                $app->redirect($redirect);
                        }

                        if(strpos($filetype, 'image') === false){
                            $app->flash('error', '이미지 파일만 업로드 가능합니다.');
			                $app->redirect($redirect);
                        }

                        // check upload valid ext
                        if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
                            $app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
			                $app->redirect($redirect);
                        }


                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
			                $app->redirect($redirect);
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
                        $attachment_url = $app->config('media.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
			                $app->redirect($redirect);
                        }

                        $app->db->insert('media_file', array('midx'=>$id, 'file_type'=>'IMG', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			                $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			                $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
			                $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
			            $app->redirect($redirect);
                }
            }
        }

		// 업로드 된 파일이 있는지 확인
        if (is_array($_FILES['att']['name'])) {

            $attachment_url = '';
            $savePath = $app->config('media.path');
            $max_file_size = 1024 * 1024 * 5;

            foreach($_FILES['att']['name'] as $key => $val){
                switch($_FILES['att']['error'][$key]){
                    case UPLOAD_ERR_OK:
                        $filename = $_FILES['att']['name'][$key];
                        $filesize = $_FILES['att']['size'][$key];
                        $filetmpname = $_FILES['att']['tmp_name'][$key];
                        $filetype = $_FILES['att']['type'][$key];
                        $tmpfileext = explode('.', $filename);
                        $fileext = $tmpfileext[count($tmpfileext)-1];

                        // upload correct method
                        if(!is_uploaded_file($filetmpname)){
                            $app->flash('error', '정상적인 방법으로 업로드해주세요');
			                $app->redirect($redirect);
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
                        $attachment_url = $app->config('media.url').'/'.$saveFilename.'.'.$fileext;

                        if(!move_uploaded_file($filetmpname, $finalFilename)){
                            $app->flash('error', '업로드에 실패하였습니다');
			                $app->redirect($redirect);
                        }

                        $app->db->insert('media_file', array('midx'=>$id, 'file_type'=>'FILE', 'save_name'=>$saveFilename.'.'.$fileext, 'file_name'=>$filename));
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			            $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $app->flash('error', '업로드 가능 용량을 초과하였습니다');
			            $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $app->flash('error', '업로드에 실패하였습니다');
			            $app->redirect($redirect);
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    default:
                        $app->flash('error', '업로드에 실패하였습니다');
			            $app->redirect($redirect);
                }
            }
        }

		$app->redirect('/admin/media');
	});

	$app->get('/media/:id/delete', function ($id) use ($app, $log) {
		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('media', '*', array('midx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('media', array('midx'=>$id));
        
        $savePath = $app->config('media.path');
        $files = $app->db->select('media_file', '*', array('midx'=>$id));
        foreach ($files as $v) {
            unlink($savePath.'/'.$v['save_name']);
        }
		$app->db->delete('media_file', array('midx'=>$id));

		$app->redirect('/admin/media?page='.$page);
	});
    // 미디어룸 끝..



    // faq
   
	$app->get('/faq', function () use ($app, $log) {
		$current_menu = 'admin_notice';

		$cate = $app->request->get('cate');
		$cates = $app->db->select('faq_category', '*', array(), array('sorting'=>'asc'));
        $cates_name = array();
        foreach ($cates as $k => $v) {
            $cates_name[$v['cate_id']] = $v['name'];
        }

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;
        
        if ($cate) {
            $search['cate_id'] = $cate;
        }

		$posts = $app->db->select('faq', '*', $search, array('fidx'=>'desc'), $start, $count);
		$total = $app->db->selectCount('faq');
		$total_page = ceil($total / $count);

		$app->render('admin/faq.php', array('current_menu'=>$current_menu, 'posts'=>$posts, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count, 'cate'=>$cate, 'cates'=>$cates, 'cates_name'=>$cates_name));
	});

	$app->get('/faq/write', function () use ($app, $log) {
		$current_menu = 'admin_notice';
		$cates = $app->db->select('faq_category', '*', array(), array('sorting'=>'asc'));

		$app->render('admin/faq_write.php', array('current_menu'=>$current_menu, 'post'=>$post, 'cates'=>$cates));
	});

	$app->post('/faq/write', function () use ($app, $log) {
		$cate_id = $app->request->post('cate_id');
		$subject = $app->request->post('subject');
		$contents = $app->request->post('contents');

		if(empty($subject)){
			$app->redirect('/admin/faq/write');
		}

		if(empty($contents)){
			$app->redirect('/admin/faq/write');
		}

        $param = array(
                    'cate_id'=>$cate_id,
                    'subject'=>$subject,
                    'contents'=>$contents,
                    'uid' => $_SESSION['user']['uid']
                );


		$new_fidx = $app->db->insert('faq', $param);

		$app->redirect('/admin/faq');
	});


	$app->get('/faq/:id/edit', function ($id) use ($app, $log) {
		$current_menu = 'admin_notice';
		$cates = $app->db->select('faq_category', '*', array(), array('sorting'=>'asc'));

		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('faq', '*', array('fidx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

        $app->render('admin/faq_write.php', array('current_menu'=>$current_menu, 'post'=>$post, 'cates'=>$cates));
	});


	$app->post('/faq/:id/edit', function ($id) use ($app, $log) {
        $redirect = "/admin/faq/".$id."/edit";

		$cate_id = $app->request->post('cate_id');
		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('faq', '*', array('fidx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$subject = $app->request->post('subject');
		$contents = $app->request->post('contents');

		if(empty($subject)){
			$app->redirect($redirect);
		}

		if(empty($contents)){
			$app->redirect($redirect);
		}

        $param = array(
                    'cate_id'=>$cate_id,
                    'subject'=>$subject,
                    'contents'=>$contents
                );

		$app->db->update('faq', $param, array('fidx'=>$id));

		$app->redirect('/admin/faq');
	});


	$app->post('/faq/move', function () use ($app, $log) {

		$cate = $app->request->post('cate');
		$cate_id = $app->request->post('cate_id');
		$input_fidx = $app->request->post('fidxs');

		$edit_fidx = array();
		foreach($input_fidx as $fidx){
			if(empty($fidx)) continue;
			if(!is_numeric($fidx)) continue;
			if(in_array($fidx, $edit_fidx)) continue;

			$edit_fidx[] = $fidx;
		}

		if(count($edit_fidx) == 0) $app->redirect('/admin/faq');

		$app->db->conn->query('UPDATE faq SET cate_id = \''.$cate_id.'\' WHERE fidx IN ('.implode(',', $edit_fidx).')');

		$app->redirect('/admin/faq?cate='.$cate);
	});

	$app->get('/faq/:id/delete', function ($id) use ($app, $log) {
		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('faq', '*', array('fidx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('faq', array('fidx'=>$id));
        
		$app->redirect('/admin/faq?page='.$page);
	});


    // 카테고리
	$app->get('/faq_cate', function () use ($app, $log) {
		$current_menu = 'admin_items';

		$cates = $app->db->select('faq_category', '*', array(), array('sorting'=>'asc'));

		$app->render('admin/faq_category.php', array('current_menu'=>$current_menu, 'cates'=>$cates));
	});

	$app->post('/faq_cate/add', function () use ($app, $log) {
		$name = $app->request->post('name');
		$sorting = $app->request->post('sorting');

		if(empty($name)){
			$app->flash('error', '종류를 입력하세요');
			$app->redirect('/admin/faq_cate');
		}

		if(empty($sorting) || !is_numeric($sorting)){
			$sorting = 1;
		}

		$app->db->insert('faq_category', array(
			'name'=>$name,
			'sorting'=>$sorting
		));

		$app->redirect('/admin/faq_cate');
	});

	$app->post('/faq_cate/edit', function () use ($app, $log) {
		$edit_cate_id = $app->request->post('edit_cate_id');
		$name = $app->request->post('name');
		$sorting = $app->request->post('sorting');

		if(empty($edit_cate_id)){
			$app->halt(404, 'not found');
		}

		if(empty($name)){
			$app->flash('error', '종류를 입력하세요');
			$app->redirect('/admin/faq_cate');
		}

		if(empty($sorting) || !is_numeric($sorting)){
			$sorting = 1;
		}

		$cate = $app->db->selectOne('faq_category', '*', array('cate_id'=>$edit_cate_id));

		if(empty($cate)){
			$app->halt(404, 'not found');
		}

		$app->db->update('faq_category', array(
			'name'=>$name,
			'sorting'=>$sorting
		), array('cate_id'=>$edit_cate_id));

		$app->redirect('/admin/faq_cate');
	});

	$app->get('/faq_cate/:id/delete', function ($id) use ($app, $log) {
		$cate = $app->db->selectOne('faq_category', '*', array('cate_id'=>$id));

		if(empty($cate)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('faq_category', array('cate_id'=>$id));

		$app->redirect('/admin/faq_cate');
	});
    // faq 끝..




    // banner

	$app->get('/banner', function () use ($app, $log) {
		$current_menu = 'admin_notice';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$posts = $app->db->select('banner', '*', $search, array('bidx'=>'desc'), $start, $count);
		$total = $app->db->selectCount('banner');
		$total_page = ceil($total / $count);

		$app->render('admin/banner.php', array('current_menu'=>$current_menu, 'posts'=>$posts, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->get('/banner/write', function () use ($app, $log) {
		$current_menu = 'admin_notice';

        $post['start_h'] = '00';
        $post['start_m'] = '00';
        $post['end_h'] = '00';
        $post['end_m'] = '00';

		$app->render('admin/banner_write.php', array('current_menu'=>$current_menu, 'post'=>$post));
	});

	$app->post('/banner/write', function () use ($app, $log) {
		$banner_image = $app->request->post('banner_image');
		$subject = $app->request->post('subject');
		$url = $app->request->post('url');

		if(empty($subject)){
			$app->redirect('/admin/banner/write');
		}

        $start_date = sprintf("%s %02d:%02d:00", $app->request->post('start_day'), $app->request->post('start_h'), $app->request->post('start_m'));
        $end_date = sprintf("%s %02d:%02d:00", $app->request->post('end_day'), $app->request->post('end_h'), $app->request->post('end_m'));

        $param = array(
                    'subject'=>$subject,
                    'start_date'=>$start_date,
                    'end_date'=>$end_date,
                    'url'=>$url,
                    'banner_image'=>$banner_image,
                    'uid' => $_SESSION['user']['uid']
                );

		$new_fidx = $app->db->insert('banner', $param);

		$app->redirect('/admin/banner');
	});


	$app->post('/banner/upload_images', function () use ($app, $log) {

        $savePath = $app->config('data.path').'/banner/';
        $max_file_size = 1024 * 1024 * 5;

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

	$app->get('/banner/:id/edit', function ($id) use ($app, $log) {
		$current_menu = 'admin_notice';

		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('banner', '*', array('bidx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

        preg_match('/(..):(..):(..)$/', $post['start_date'], $match);
        $post['start_h'] = $match[1];
        $post['start_m'] = $match[2];
        preg_match('/(..):(..):(..)$/', $post['end_date'], $match);
        $post['end_h'] = $match[1];
        $post['end_m'] = $match[2];

        $app->render('admin/banner_write.php', array('current_menu'=>$current_menu, 'post'=>$post));
	});


	$app->post('/banner/:id/edit', function ($id) use ($app, $log) {
        $redirect = "/admin/banner/".$id."/edit";

		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('banner', '*', array('bidx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

        $banner_image = $app->request->post('banner_image');
		$subject = $app->request->post('subject');
		$url = $app->request->post('url');

		if(empty($subject)){
			$app->redirect($redirect);
		}

        $start_date = sprintf("%s %02d:%02d:00", $app->request->post('start_day'), $app->request->post('start_h'), $app->request->post('start_m'));
        $end_date = sprintf("%s %02d:%02d:00", $app->request->post('end_day'), $app->request->post('end_h'), $app->request->post('end_m'));

        $param = array(
                    'subject'=>$subject,
                    'url'=>$url,
                    'start_date'=>$start_date,
                    'end_date'=>$end_date,
                    'banner_image'=>$banner_image
                );

		$app->db->update('banner', $param, array('bidx'=>$id));

		$app->redirect('/admin/banner');
	});

	$app->get('/banner/:id/delete', function ($id) use ($app, $log) {
		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('banner', '*', array('bidx'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

        @unlink($app->config('data.path').'/banner/'.$post['banner_image']);
		$app->db->delete('banner', array('bidx'=>$id));
        
		$app->redirect('/admin/banner?page='.$page);
	});
    // banner 끝

    // lounge

    
	$app->get('/pb_request', function () use ($app, $log) {
		$current_menu = 'admin_contacts';
        
        $req_type = $app->request->get('req_type');
        $q_type = $app->request->get('q_type');
		$q = $app->request->get('q');

        if (!empty($req_type)) {
            if ($req_type == 'Online') $where[] = " a.req_type = 'Online'";
            else $where[] = " a.req_type = 'Offline'";

            
            $q_str = '&req_type='.$req_type;
        }

        if (empty($q_type)) {
            $q_type = 'subject';
        }

		if (!empty($q)){
            switch ($q_type) {
                case 'subject':
			        $where[] = " a.subject LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'pb':
			        $where[] = " c.name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'user':
			        $where[] = " b.name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;
            }
            
            $q_str .= '&q_type='.$q_type.'&q='.urlencode($q);
		}
        if (is_array($where)) {
            $where_str = " WHERE ".implode(' AND', $where);
        }

        $sql = "SELECT a.*, b.name as user_name, c.name as pb_name, b.email as b_email, b.mobile as b_mobile 
                FROM pb_request a INNER JOIN user b ON a.uid = b.uid
                INNER JOIN user c ON a.pid = c.uid".$where_str;
		$total_sql = "SELECT COUNT(*) 
                FROM pb_request a INNER JOIN user b ON a.uid = b.uid
                INNER JOIN user c ON a.pid = c.uid".$where_str;

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$sql .= " ORDER BY a.req_id DESC LIMIT $start, $count";

		$contacts = array();

		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_array()){
			$contacts[] = $row;
		}

		$total_result = $app->db->conn->query($total_sql);
		$total_result_row = $total_result->fetch_array();
		$total = $total_result_row[0];

		$total_page = ceil($total / $count);
        
        $param =  array(
                    'current_menu'=>$current_menu,
                    'contacts'=>$contacts,
                    'page'=>$page,
                    'page_count'=>$page_count,
                    'page_start' =>$page_start,
                    'total_page'=>$total_page,
                    'total'=>$total,
                    'count'=>$count,
                    'q_type'=>$q_type,
                    'q'=>$q,
                    'req_type'=>$req_type,
                    'q_str'=>$q_str
                );

		$app->render('admin/pb_request.php', $param);
	});

	$app->get('/pb_request/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_contacts';
        

        $sql = "SELECT a.*, b.name as user_name, c.name as pb_name 
                FROM pb_request a INNER JOIN user b ON a.uid = b.uid
                INNER JOIN user c ON a.pid = c.uid WHERE req_id='$id'";
		$result = $app->db->conn->query($sql);
		$post = $result->fetch_array();
        
        $param =  array(
                    'current_menu'=>$current_menu,
                    'contacts'=>$contacts,
                    'post'=>$post,
                );

		$app->render('admin/pb_request_view.php', $param);
	});

	$app->get('/pb_request/del/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_contacts';
        

        $sql = "delete FROM pb_request WHERE req_id='$id'";
		$result = $app->db->conn->query($sql);
       
		$app->redirect('/admin/pb_request');
	});
    // lounge end


    // 고객센터
	$app->get('/customer', function () use ($app, $log) {
		$current_menu = 'admin_contacts';

		$condition = array('target'=>'broker');
		$answer = $app->request->get('answer');
		$q = $app->request->get('q');

        if (!empty($answer)) {
            if ($answer == 'Y') $where = " AND answer_at <> 0";
            else $where = " AND answer_at = 0";

            
            $q_str = '&answer='.$answer;
        }

        if (empty($q_type)) {
            $q_type = 'subject';
        }

		if (!empty($q)){
            switch ($q_type) {
                case 'name':
			        $where .= " AND strategy_name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'pb':
			        $where .= " AND strategy_name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;

                case 'user':
			        $where .= " AND name LIKE '%".$app->db->conn->real_escape_string($q)."%'";
                    break;
            }
            
            $q_str .= '&q_type='.$q_type.'&q='.urlencode($q);
		}

		$sql = 'SELECT * FROM customer WHERE 1 = 1'.$where;
		$total_sql = 'SELECT COUNT(*) FROM customer WHERE 1 = 1'.$where;

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$sql .= " ORDER BY cus_id DESC LIMIT $start, $count";

		$contacts = array();

		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_array()){
			$writer = $app->db->selectOne('user', '*', array('uid'=>$row['uid']));
			$row['writer'] = $writer;

			$contacts[] = $row;
		}

		$total_result = $app->db->conn->query($total_sql);
		$total_result_row = $total_result->fetch_array();
		$total = $total_result_row[0];

		$total_page = ceil($total / $count);

        $param =  array(
                    'current_menu'=>$current_menu,
                    'contacts'=>$contacts,
                    'page'=>$page,
                    'page_count'=>$page_count,
                    'page_start' =>$page_start,
                    'total_page'=>$total_page,
                    'total'=>$total,
                    'count'=>$count,
                    'q_type'=>$q_type,
                    'q'=>$q,
                    'answer'=>$answer,
                    'q_str'=>$q_str
                );

		$app->render('admin/customer.php', $param);
	});

	$app->get('/customer/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_contacts';

		$answer = $app->request->get('answer');
		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$contact = $app->db->selectOne('customer', '*', array('cus_id'=>$id));

		if(empty($contact)){
			$app->halt(404, 'not found');
		}

		$app->render('admin/customer_view.php', array('current_menu'=>$current_menu, 'contact'=>$contact, 'page'=>$page,'answer'=>$answer));
	});

	$app->get('/customer/del/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_contacts';

        $sql = "delete FROM customer WHERE cus_id='$id'";
		$result = $app->db->conn->query($sql);
       
		$app->redirect('/admin/customer');
	});

	$app->post('/customer/:id/answer', function ($id) use ($app, $log) {
		$cus_id = $app->request->post('cus_id');
		$answer = $app->request->post('answer');
		$mobile = $app->request->post('mobile');

		$now = time();

		if(empty($cus_id)){
			$app->halt(404, 'not found');
		}

		if(empty($answer)){
			$app->redirect('/admin/customer/'.$cus_id);
		}

		$app->db->update('customer', array(
			'answer'=>$answer,
			'answer_at'=>$now,
		), array('cus_id'=>$cus_id));

		//문자발송
		$SMSINFO['smsMsg']="시스메틱에서 문의하신 상담의 답변이 완료되었습니다";
		$SMSINFO['smsHp']=$mobile;
		sendSMS($SMSINFO);

		$app->flash('error', '답변완료!!');
		$app->redirect('/admin/customer/'.$cus_id);
	});

    //고객센터 끝

	$app->get('/mail', function () use ($app, $log) {
		$current_menu = 'admin_mail';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$mails = $app->db->select('mail_history', '*', array(), array('mail_id'=>'desc'), $start, $count);
		$total = $app->db->selectCount('mail_history');
		$total_page = ceil($total / $count);

		$app->render('admin/mail.php', array('current_menu'=>$current_menu, 'mails'=>$mails, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->get('/mail/write', function () use ($app, $log) {
		$current_menu = 'admin_mail';

		$app->render('admin/mail_write.php', array('current_menu'=>$current_menu));
	});

	$app->post('/mail/write', function () use ($app, $log) {
		$subject = $app->request->post('subject');
		$contents_body = $app->request->post('contents_body');
		$mail_type = $app->request->post('mail_type');
		$target_type1 = $app->request->post('target_type1');
		$target_type2 = $app->request->post('target_type2');
		$target_type3 = $app->request->post('target_type3');
		$target_type4_txt = $app->request->post('target_type4_txt');
		//$send_date = $app->request->post('send_date');
		//$send_time = $app->request->post('send_time');
		$send_date = date("Y.m.d");
		$send_time = date("H:i:s");

		if(empty($subject)){
			$app->redirect('/admin/mail/write');
		}

		if(empty($contents_body)){
			$app->redirect('/admin/mail/write');
		}

		if(empty($send_date)){
			$app->redirect('/admin/mail/write');
		}else{
			$send_date = str_replace('.', '', $send_date);
		}

		if($send_time == ''){
			$app->redirect('/admin/mail/write');
		}else{
			$send_time = intval($send_time);
		}

		//$reserve_at = mktime($send_time, 0, 0, substr($send_date, 4, 2), substr($send_date, 6, 2), substr($send_date, 0, 4));
		$reserve_at = time();

		//즉시 메일 발송
		$from = $app->config('system_sender_email');
		$from_name = $app->config('system_sender_name');
		$notice_content = $contents_body;

		ob_start();
		if($mail_type == 'promotion'){
			include $app->config('templates.path').'/mail/mail_promotion.php';
		}else{
			include $app->config('templates.path').'/mail/mail_notice.php';
		}
		$content = ob_get_contents();
		ob_end_clean();

		$where = "is_delete='0' ";

		if($mail_type == 'promotion'){
			$where .= "and alarm_all='1' ";
		}

		//타겟 구분
		if($target_type1=="N"){ //일반회원
			$user_type_chk[] = "N";
			$target_type_txt[]="일반회원";
		}
		if($target_type2=="P"){ //PB회원
			$user_type_chk[] = "P";
			$target_type_txt[]="PB회원";
		}
		if($target_type3=="T"){ //트레이너회원
			$user_type_chk[] = "T";
			$target_type_txt[]="트레이너회원";
		}
		if($target_type4_txt){ //직접입력
			$to = $target_type4_txt;
			$target_type_txt[]=$target_type4_txt;
			sendmail($from, $from_name, $to, $subject, $content);			
		}

		if(count($user_type_chk)){
			$user_types = implode("','", $user_type_chk);
			$where .= "and user_type in ('".$user_types."') ";

			$result = $app->db->conn->query('SELECT * FROM user WHERE '.$where);
			while($user = $result->fetch_array()){
				if(empty($user['email'])) continue;
				$to = $user['email'];
				sendmail($from, $from_name, $to, $subject, $content);			
			}
		}
		
		$target_type = implode(", ", $target_type_txt);
		$app->db->insert('mail_history', array('subject'=>$subject,'contents'=>$contents_body, 'mail_type'=>$mail_type, 'reserve_at'=>$reserve_at, 'status'=>'sent', 'target_type'=>$target_type));

		$app->redirect('/admin/mail');
	});

	$app->get('/mail/:id/delete', function ($id) use ($app, $log) {
		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$mail = $app->db->selectOne('mail_history', '*', array('mail_id'=>$id));

		if(empty($mail)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('mail_history', array('mail_id'=>$id));

		$app->redirect('/admin/mail?page='.$page);
	});

	$app->get('/mail/:id', function ($id) use ($app, $log) {
		$current_menu = 'admin_mail';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$mail = $app->db->selectOne('mail_history', '*', array('mail_id'=>$id));

		if(empty($mail)){
			$app->halt(404, 'not found');
		}

		$app->render('admin/mail_view.php', array('current_menu'=>$current_menu, 'mail'=>$mail, 'page'=>$page));
	});
});

$app->get('/strategies/batch', function () use ($app, $log) {
	$app->db->executesp('analysis_strategy_all',array());
	$app->db->executesp('score_strategies',array());


});

$app->get('/mail/batch', function () use ($app, $log) {
	$now = time();
	$url = $app->config('scheme').'://'.$app->config('host');
	$result = $app->db->conn->query('SELECT * FROM mail_history WHERE status = \'queued\' AND reserve_at <= '.$now);
	while($row = $result->fetch_array()){
		$from = $app->config('system_sender_email');
		$from_name = $app->config('system_sender_name');

		$subject = $row['subject'];
		$notice_content = $row['contents'];

		ob_start();
		if($row['mail_type'] == 'promotion'){
			include $app->config('templates.path').'/mail/mail_promotion.php';
		}else{
			include $app->config('templates.path').'/mail/mail_notice.php';
		}
		$content = ob_get_contents();
		ob_end_clean();

		$condition = array('is_delete'=>'0');

		if($row['mail_type'] == 'promotion'){
			$condition['alarm_all'] = '1';
		}else{
		}

		$users = $app->db->select('user', '*', $condition);
		foreach($users as $user){
			if(empty($user['email'])) continue;

			$to = $user['email'];
			//sendmail($from, $from_name, $to, $subject, $content);
		}

		$mails = $app->db->update('mail_history', array('status'=>'sent'), array('mail_id'=>$row['mail_id']));
	}
});

$app->get('/mail/feeds', function () use ($app, $log) {
	$now = time();
	$url = $app->config('scheme').'://'.$app->config('host');

	$from = $app->config('system_sender_email');
	$from_name = $app->config('system_sender_name');

	$users = $app->db->select('user', '*', array('is_delete'=>'0', 'alarm_feeds'=>'1'), array('uid'=>'desc'));

	// $subject = 'SYSMETIC TRADER 에서 관심전략으로 등록한 전략의 변동사항을 알려드립니다.';
	$subject = '[시스메틱] '.date('Y').'년 '.date('n').'월 '.date('j').'일 기준 관심전략으로 등록한 전략 손익 변동사항 알림';

	foreach($users as $user){
		if(empty($user['email'])) continue;

		$following_ids = $app->db->select('following_strategy', '*', array('uid'=>$user['uid']), array('following_id'=>'desc'));
		$following_ids_array = array();
		foreach($following_ids as $v){
			$following_ids_array[] = $v['strategy_id'];
		}

		$strategies = array();
		if(count($following_ids_array) != 0){
			$result = $app->db->conn->query('SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $following_ids_array).') AND is_open = \'1\' AND is_operate = \'1\' ORDER BY sharp_ratio DESC LIMIT 10');
			while($row = $result->fetch_array()){
				$strategies[] = $row;
			}
		}else{
			continue;
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
			$strategies[$k]['developer'] = array('name'=>$developer['name'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

			// 팔로워
			$followers_count = $app->db->selectCount('following_strategy', array('strategy_id'=>$v['strategy_id']));
			$strategies[$k]['followers_count'] = $followers_count;

			// 팔로잉 여부
			$is_following = false;
			/*
			if($isLoggedIn()){
				$is_following = $app->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'], 'strategy_id'=>$v['strategy_id'])) > 0 ? true : false;
			}
			*/

			$strategies[$k]['is_following'] = $is_following;

			// 산식
			$daily_values = $app->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id']), array('basedate'=>'asc'));

			$strategies[$k]['daily_values'] = $daily_values;
			$strategies[$k]['weekly_profit_values'] = calWeeklyProfitValues($daily_values,date("n/j"));

		}

		ob_start();
		include $app->config('templates.path').'/mail/mail_observe.php';
		$content = ob_get_contents();
		ob_end_clean();

		$to = $user['email'];

		sendmail($from, $from_name, $to, $subject, $content);
	}
});
