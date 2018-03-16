<?php
// 브로커(UI디자인상 어드민 영역을 사용함)
$app->group('/admin', $authenticateForRole('B,T'), function () use ($app, $log) {
	$app->get('/strategies', function () use ($app, $log) {
		$current_menu = 'admin_strategies';

		$sql = 'SELECT * FROM strategy WHERE is_delete = \'0\'';
		$total_sql = 'SELECT COUNT(*) FROM strategy WHERE is_delete = \'0\'';

		$condition = array('is_delete'=>'0');

		if($_SESSION['user']['user_type'] != 'A'){
			$condition['developer_uid'] = $_SESSION['user']['uid'];
			$sql .= " AND developer_uid = ".$_SESSION['user']['uid'];
			$total_sql .= " AND developer_uid = ".$_SESSION['user']['uid'];
		}

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

		$sql .= " ORDER BY strategy_id DESC LIMIT $start, $count";

		// $strategies = $app->db->select('strategy', '*', $condition, array('strategy_id'=>'desc'), $start, $count);
		// $total = $app->db->selectCount('strategy', $condition);
		$strategies = array();

		$result = $app->db->conn->query($sql);
		while($row = $result->fetch_array()){
			$developer = $app->db->selectOne('user', '*', array('uid'=>$row['developer_uid']));
			$row['developer'] = $developer;
			$strategies[] = $row;
		}
		
		$total_result = $app->db->conn->query($total_sql);
		$total_result_row = $total_result->fetch_array();
		$total = $total_result_row[0];
		
		$total_page = ceil($total / $count);

		$app->render('admin/strategy.php', array('current_menu'=>$current_menu, 'strategies'=>$strategies, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count, 'q'=>$q));
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

		$app->db->executesp('add_strategy_daily', array(
			'p_strategy_id'=>$id,
			'p_target_date'=>$target_date,
			'p_flow'=>$flow,
			'p_pl'=>$PL
		));

		$app->m->delete('strategy_daily_value:'.$id);
		$app->m->delete('strategy_new_daily_value:'.$id);

		// 전략의 주요 지표 데이터 저장
		fetchStrategyData($id);
		$app->db->executesp('analysis_strategy',array('strategy_id'=>$id));
		
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
						$target_date = trim($Row[0]);
						$flow = trim($Row[1]);
						$PL = trim($Row[2]);
				
						$target_date = str_replace('.', '', $target_date);
						$target_date = str_replace('-', '', $target_date);
						$target_date = str_replace('/', '', $target_date);
						if(!is_numeric($target_date) || strlen($target_date) != 8){
							continue;
						}

						
						$flow = str_replace(',', '', $flow);
						if(!is_numeric($flow)){	$flow = 0;}
						
						$PL = str_replace(',', '', $PL);
						if(!is_numeric($PL)){$PL = 0;}
						


						$app->db->executesp('add_strategy_daily', array(
							'p_strategy_id'=>$id,
							'p_target_date'=>$target_date,
							'p_flow'=>$flow,
							'p_pl'=>$PL
						));

					}
				}
		
				
			}
			
		}
		catch (Exception $E)
		{
			$app->flash('error', $E -> getMessage());
		}

		$app->m->delete('strategy_daily_value:'.$id);
		$app->m->delete('strategy_new_daily_value:'.$id);
		$app->db->executesp('analysis_strategy',array('strategy_id'=>$id));
		//$app->db->executesp('score_strategies',array());

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
		
		$app->db->update('strategy_daily', array(
		//	'balance'=>$balance,
			'flow'=>$flow,
			'PL'=>$PL
		), array('strategy_id'=>$id, 'target_date'=>$target_date,));

		$app->m->delete('strategy_daily_value:'.$id);
		$app->m->delete('strategy_new_daily_value:'.$id);

		// 전략의 주요 지표 데이터 저장
		fetchStrategyData($id);
		$app->db->executesp('analysis_strategy',array('strategy_id'=>$id));
		//$app->db->executesp('score_strategies',array());

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

		$app->m->delete('strategy_daily_value:'.$id);
		$app->m->delete('strategy_new_daily_value:'.$id);

		// 전략의 주요 지표 데이터 저장
		fetchStrategyData($id);
		$app->db->executesp('analysis_strategy',array('strategy_id'=>$id));
		//$app->db->executesp('score_strategies',array());

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

		// 삭제하고 난 뒤 전략의 데이터가 2개 미만일 경우 상태를 비공개로 변경함
		$app->db->update('strategy', array(
			'is_open'=>'0'
		), array('strategy_id'=>$id));

		$app->m->delete('strategy_daily_value:'.$id);
		$app->m->delete('strategy_new_daily_value:'.$id);

		// 전략의 주요 지표 데이터 저장
		fetchStrategyData($id);

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

		$strategy = $app->db->selectOne('strategy', '*', array('strategy_id'=>$id));

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

		// 저장된 종목
		$real_items = array();
		$stored_items = $app->db->select('strategy_item', '*', array('strategy_id'=>$id));
		foreach($stored_items as $v){
			$real_items[] = $v['item_id'];
		}

		$strategy['items'] = $real_items;

		$app->render('admin/strategy_view.php', array('current_menu'=>$current_menu, 'current_tab_menu'=>$current_tab_menu, 'items'=>$items, 'tools'=>$tools, 'company_type1'=>$company_type1, 'company_type2'=>$company_type2,'strategy'=>$strategy, 'page'=>$page));
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
		$broker_type = $app->request->post('broker_type');
		$broker_id = $app->request->post('broker_id');
		$input_item_ids = $app->request->post('item_ids');
		$tool_id = $app->request->post('tool_id');
		$currency = $app->request->post('currency');
		$investment = $app->request->post('investment');
		$term = $app->request->post('term');
		$intro = $app->request->post('intro');
		$developer_uid = $app->request->post('developer_uid');
		$is_operate = $app->request->post('is_operate');
		$is_open = $app->request->post('is_open');

		if(empty($name)){
			$app->flash('error', '전략명을 입력하세요');
			$app->redirect('/admin/strategies/'.$id);
		}

		if(empty($broker_type)){
			$app->flash('error', '브로커를 선택하세요');
			$app->redirect('/admin/strategies/'.$id);
		}

		if(empty($broker_id) || !is_numeric($broker_id)){
			$app->flash('error', '브로커를 선택하세요');
			$app->redirect('/admin/strategies/'.$id);
		}

		$broker = $app->db->selectOne('broker', '*', array('broker_id'=>$broker_id));

		if(empty($broker)){
			$app->flash('error', '브로커를 선택하세요');
			$app->redirect('/admin/strategies/'.$id);
		}

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
			$developer_name = $strategy['developer_name'];			
		}else{
			$developer = $app->db->selectOne('user', '*', array('uid'=>$developer_uid));

			if(!empty($developer)){
				$developer_uid = $developer['uid'];
				$developer_name = $developer['nickname'];
			}else{
				$developer_uid = $strategy['developer_uid'];
				$developer_name = $strategy['developer_name'];	
			}
		}

		$strategy_type = $strategy['strategy_type'];
		//if($_SESSION['user']['user_type'] == 'A'){
			$strategy_type = $app->request->post('strategy_type');
			if(empty($strategy_type)){
				$strategy_type = 'M';
			}
		//}else{
		//	$strategy_type = 'M';
		//}
			
		$app->db->update('strategy', array(
			'name'=>$name,
			'strategy_type'=>$strategy_type,
			'broker_type'=>$broker_type,
			'broker_id'=>$broker_id,
			'developer_uid'=>$developer_uid,
			'developer_name'=>$developer_name,
			'tool_id'=>$tool_id,
			'currency'=>$currency,
			'investment'=>$investment,
			'strategy_term'=>$term,
			'intro'=>$intro,
			'is_operate'=>$is_operate,
			'is_open'=>$is_open
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

		$app->redirect('/admin/strategies/'.$id);
	});
	
	$app->get('/trader_search', function () use ($app, $log) {
		$nickname = $app->request->get('nickname');

		$response_traders = array();

		if(empty($nickname)){
			echo json_encode($response_traders);
			$app->stop();
		}

		$result = $app->db->conn->query('SELECT * FROM user WHERE user_type = \'T\' AND nickname LIKE \'%'.$app->db->conn->real_escape_string($nickname).'%\'');

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
				$condition['name'] = $q;
			}else if($q_type == 'mobile'){
				$condition['mobile'] = $q;
			}else if($q_type == 'birthday'){
				$condition['birthday'] = $q;
			}else if($q_type == 'user_type'){
			}else{
				$condition['email'] = $q;
			}
		}

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$users = $app->db->select('user', '*', $condition, array('uid'=>'desc'));
		$total = $app->db->selectCount('user');
		$total_page = ceil($total / $count);

		$app->render('admin/member.php', array('current_menu'=>$current_menu, 'users'=>$users, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count, 'q'=>$q, 'q_type'=>$q_type));
	});

	$app->get('/request_broker', function () use ($app, $log) {
		$current_menu = 'admin_users';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$users = $app->db->select('request_broker', '*', array(), array('request_broker_id'=>'desc'));
		$total = $app->db->selectCount('request_broker');
		$total_page = ceil($total / $count);

		foreach($users as $k => $user){
			$target_user_info = $app->db->selectOne('user', '*', array('uid'=>$user['uid']));
			$users[$k]['user'] = $target_user_info;
		}

		$app->render('admin/member_broker.php', array('current_menu'=>$current_menu, 'users'=>$users, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
	});

	$app->get('/request_trader', function () use ($app, $log) {
		$current_menu = 'admin_users';

		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;
		$count = 15;
		$start = ($page - 1) * $count;
		$page_count = 10;
		$page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

		$condition = array('is_request_trader'=>'1');

		$users = $app->db->select('user', '*', $condition, array('uid'=>'desc'));
		$total = $app->db->selectCount('user');
		$total_page = ceil($total / $count);

		$app->render('admin/member_trader.php', array('current_menu'=>$current_menu, 'users'=>$users, 'page'=>$page, 'page_count'=>$page_count, 'page_start' =>$page_start, 'total_page'=>$total_page, 'total'=>$total, 'count'=>$count));
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

		if(!empty($user_type) && in_array($user_type, array('N', 'T', 'B', 'A'))){
			$user_type_flag = $user_type;
		}else{
			$user_type_flag = 'N';
		}

		$app->db->conn->query('UPDATE user SET user_type = \''.$user_type_flag.'\', is_request_trader = \'0\' WHERE uid IN ('.implode(',', $edit_uids).')');
		$app->redirect($redirect_url);
	});

	$app->get('/items', function () use ($app, $log) {
		$current_menu = 'admin_items';

		$items = $app->db->select('item', '*', array(), array('item_id'=>'desc'));

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
		$max_file_size = 1024 * 1024;

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
		$max_file_size = 1024 * 1024;

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

		$brokers = $app->db->select('broker', '*', array(), array('broker_id'=>'desc'));
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

		$url = $app->request->post('url');
		$domestic = $app->request->post('domestic');
		$overseas = $app->request->post('overseas');
		$fx = $app->request->post('fx');
		$dma = $app->request->post('dma');

		$system_trading_name = $app->request->post('system_trading_name');
		$system_trading_name1 = $app->request->post('system_trading_name1');
		// $system_trading_image = $app->request->post('system_trading_image');

		$api_name = $app->request->post('api_name');
		$api_name1 = $app->request->post('api_name1');
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

		if(empty($url)){
			$url = '';
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

		if(empty($api_name)){
			$api_name = '';
		}

		if(empty($api_name1)){
			$api_name1 = '';
		}

		$broker_logo_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024;

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
		$max_file_size = 1024 * 1024;

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
		$max_file_size = 1024 * 1024;

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
		$max_file_size = 1024 * 1024;

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

		$api_image_url = '';
		$savePath = $app->config('broker.path');
		$max_file_size = 1024 * 1024;

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
		$max_file_size = 1024 * 1024;

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

		$new_broker_id = $app->db->insert('broker', array(
			'company'=>$company,
			'logo'=>$broker_logo_url,
			'logo_s'=>$broker_logo_s_url,
			'company_type'=>$company_type,
			'is_open'=>$is_open,
			'url'=>$url,
			'domestic'=>$domestic,
			'overseas'=>$overseas,
			'fx'=>$fx,
			'dma'=>$dma
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
		$max_file_size = 1024 * 1024;

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
		$max_file_size = 1024 * 1024;

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
			if($exist_count >= 2){
				$app->flash('error', '2개 이상 입력할수 없습니다');
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
			if($exist_count >= 2){
				$app->flash('error', '2개 이상 입력할수 없습니다');
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
			$max_file_size = 1024 * 1024;

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
			$max_file_size = 1024 * 1024;

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

		$url = $app->request->post('url');
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

		if(empty($url)){
			$url = '';
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
		$max_file_size = 1024 * 1024;

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
		$max_file_size = 1024 * 1024;

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
			'url'=>$url,
			'domestic'=>$domestic,
			'overseas'=>$overseas,
			'fx'=>$fx,
			'dma'=>$dma
		), array('broker_id'=>$id));

		$app->redirect('/admin/brokers/'.$id);
	});

	$app->get('/brokers/:id/delete', function ($id) use ($app, $log) {
		// 브로커 삭제를 허용하게 되는 경우 전략이나 문의내역 같은 부분도 무결성을 위해 수정을 해야함
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

		$app->render('admin/notice_write.php', array('current_menu'=>$current_menu));
	});

	$app->post('/notice/write', function () use ($app, $log) {
		$is_open = $app->request->post('is_open');
		$subject = $app->request->post('subject');
		$contents_body = $app->request->post('contents_body');

		if(!empty($is_open) && $is_open){
			$is_open_flag = '1';
		}else{
			$is_open_flag = '0';
		}

		if(empty($subject)){
			$app->redirect('/admin/notice/write');
		}

		if(empty($contents_body)){
			$app->redirect('/admin/notice/write');
		}

		$new_notice_id = $app->db->insert('notice', array('subject'=>$subject,'contents'=>$contents_body,'is_open'=>$is_open_flag));

		$attachment_url = '';
		$savePath = $app->config('notice.path');
		$max_file_size = 1024 * 1024;

		// 업로드 된 파일이 있는지 확인
		switch($_FILES['file']['error']){
			case UPLOAD_ERR_OK:
				$filename = $_FILES['file']['name'];
				$filesize = $_FILES['file']['size'];
				$filetmpname = $_FILES['file']['tmp_name'];
				$filetype = $_FILES['file']['type'];
				$tmpfileext = explode('.', $filename);
				$fileext = $tmpfileext[count($tmpfileext)-1];

				// check filesize
				if($filesize > $max_file_size){
					$app->flash('error', '이미지파일은 1MB 이하로 업로드해주세요.');
					$app->redirect('/notice/write');
				}

				if(strpos($filetype, 'image') === false){
					$app->flash('error', '이미지 파일만 업로드 가능합니다.');
					$app->redirect('/notice/write');
				}
			
				// check upload valid ext
				if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
					$app->flash('error', '확장자가 jpg, gif, png 파일만 업로드가 가능합니다');
					$app->redirect('/notice/write');
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
					$app->flash('error', '정상적인 방법으로 업로드해주세요');
					$app->redirect('/notice/write');
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
					$app->redirect('/notice/write');
				}

				// 썸네일생성 s, m
				// createThumbnail($finalFilename, $finalThumbFilename, 61, 61, false, true);
				// createThumbnail($finalFilename, $finalThumbFilenameM, $width, $height, false, true);
				$app->db->insert('attachment', array('notice_id'=>$new_notice_id, 'filename'=>$filename, 'filepath'=>$finalFilename, 'url'=>$attachment_url));
				break;
			case UPLOAD_ERR_INI_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/notice/write');
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$app->flash('error', '업로드 가능 용량을 초과하였습니다');
				$app->redirect('/notice/write');
				break;
			case UPLOAD_ERR_PARTIAL:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/notice/write');
				break;
			case UPLOAD_ERR_NO_FILE:
				break;
			default:
				$app->flash('error', '업로드에 실패하였습니다');
				$app->redirect('/notice/write');
		}	

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

		$app->render('admin/notice_edit.php', array('current_menu'=>$current_menu, 'post'=>$post));
	});

	$app->post('/notice/:id/edit', function ($id) use ($app, $log) {
		$page = $app->request->post('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('notice', '*', array('notice_id'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$is_open = $app->request->post('is_open');
		$subject = $app->request->post('subject');
		$contents_body = $app->request->post('contents_body');

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

		$app->db->update('notice', array('subject'=>$subject,'contents'=>$contents_body,'is_open'=>$is_open_flag), array('notice_id'=>$id));
		$app->redirect('/admin/notice/'.$id);
	});

	$app->get('/notice/:id/delete', function ($id) use ($app, $log) {
		$page = $app->request->get('page');
		if(empty($page) || !is_numeric($page)) $page = 1;

		$post = $app->db->selectOne('notice', '*', array('notice_id'=>$id));

		if(empty($post)){
			$app->halt(404, 'not found');
		}

		$app->db->delete('notice', array('notice_id'=>$id));
		$app->db->delete('attachment', array('notice_id'=>$id));
			
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

		$post['attachments'] = $app->db->select('attachment', '*', array('notice_id'=>$post['notice_id']));

		$app->render('admin/notice_view.php', array('current_menu'=>$current_menu, 'post'=>$post, 'page'=>$page));
	});

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
		$send_date = $app->request->post('send_date');
		$send_time = $app->request->post('send_time');

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

		$reserve_at = mktime($send_time, 0, 0, substr($send_date, 4, 2), substr($send_date, 6, 2), substr($send_date, 0, 4));

		$app->db->insert('mail_history', array('subject'=>$subject,'contents'=>$contents_body, 'mail_type'=>$mail_type, 'reserve_at'=>$reserve_at));

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
			include $app->config('templates.path').'/mail_promotion.php';
		}else{
			include $app->config('templates.path').'/mail_notice.php';
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
			sendmail($from, $from_name, $to, $subject, $content);
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
		include $app->config('templates.path').'/mail_observe.php';
		$content = ob_get_contents();
		ob_end_clean();

		$to = $user['email'];

		sendmail($from, $from_name, $to, $subject, $content);
	}
});
