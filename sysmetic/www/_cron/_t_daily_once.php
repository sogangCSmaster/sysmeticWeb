<?php
//if(!in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
//	exit;
//}
require_once dirname(__FILE__).'/../bootstrap.php';

	print '<xmp>';
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);

	$i = 0;
	// $sToday = Date("Y-m-d", time()-86400);
	$sToday = '2017-04-25';
	$result = $app->db->conn->query("SELECT * FROM strategy ");
	while($row = $result->fetch_array()){
//		$sql_old = sprintf("SELECT * FROM strategy_daily_analysis WHERE strategy_id='%s' ORDER BY basedate DESC LIMIT 1", $row['strategy_id']);
//		$aRowOld = $app->db->conn->query($sql_old)->fetch_array();
		$row['sStartDay'] = $sToday;
//		if($row['sStartDay'] <= $aRowOld['basedate']) {
//			continue;
//		} else if($aRowOld['basedate'] != ''){
//			$row['sStartDay'] = Date("Y-m-d", strtotime($aRowOld['basedate']) + 86400);
//		}
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