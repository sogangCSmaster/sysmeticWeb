<?php
//if(!in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
//	exit;
//}
require_once dirname(__FILE__).'/../bootstrap.php';

set_time_limit(0);

	print '<xmp>';
	printf("\n[%s] Start 345 St Analysis ======================== \n", date("Y-m-d H:i:s"));
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);

	$i = 0;

	$result = $app->db->conn->query("SELECT * FROM strategy");					//  258,260,174,179,173,177,92					 where strategy_id >= 67				  where strategy_id = 174
	while($row = $result->fetch_array()){
		// $sql_first = sprintf("SELECT daily_id, strategy_id, target_date FROM strategy_daily WHERE strategy_id = '%s' ORDER BY target_date ASC LIMIT 1", $row['strategy_id']);
		$sql_first = sprintf("SELECT daily_id, strategy_id, basedate FROM strategy_daily_analysis WHERE strategy_id = '%s' ORDER BY basedate ASC LIMIT 1", $row['strategy_id']);
		$aRowFirst = $app->db->conn->query($sql_first)->fetch_array();

		$sql_cnt = sprintf("SELECT count(*) as cnt FROM strategy_daily_analysis WHERE strategy_id='%s' AND holiday_flag = 0", $row['strategy_id']);
		$aRowCnt = $app->db->conn->query($sql_cnt)->fetch_array();

		if($aRowFirst['daily_id'] < 1) {
			$sql_del = sprintf("DELETE FROM strategy_daily_analysis WHERE strategy_id = '%s' ", $row['strategy_id']);
			$sql_del2 = sprintf("DELETE FROM strategy_smindex WHERE strategy_id = '%s' ", $row['strategy_id']);
				//			$app->db->conn->query($sql_del);
				//			$app->db->conn->query($sql_del2);
				//			printf(" %s \n %s\n=========================\n", $sql_del, $sql_del2);
		} else if($aRowCnt['cnt'] < 1) {
			$sql_del = sprintf("DELETE FROM strategy_daily_analysis WHERE strategy_id = '%s' ", $row['strategy_id']);
			$sql_del2 = sprintf("DELETE FROM strategy_smindex WHERE strategy_id = '%s' ", $row['strategy_id']);
				//			$app->db->conn->query($sql_del);
				//			$app->db->conn->query($sql_del2);
			// printf(" %s \n %s\n=========================\n", $sql_del, $sql_del2);
		} else {
				// $aRowFirst['basedate'] = date("Y-m-d", strtotime($aRowFirst['target_date']));
			$sql_del = sprintf(" DELETE FROM strategy_smindex WHERE strategy_id = '%s' AND basedate < '%s' ", $row['strategy_id'], $aRowFirst['basedate']);
				// $app->db->conn->query($sql_del);
			printf("%s \n", $sql_del);
		}
			////////////////////////////////////////////////////
			// analysis_strategy() 계산하기
				//				setStrategyAnalysis($app->db, $row['strategy_id'], $row['sStartDay'], $sToday);
				//				setStrategyAnalysisMonthly($app->db, $row['strategy_id']);
				//				setStrategyAnalysisYearly($app->db, $row['strategy_id']);
					//- $app->db->executesp('interpolate_strategy',array('p_strategy_id'=>$row['strategy_id']));		// setStrategyAnalysis() 안의 마지막에 수행됨 (2017-05-05)
			////////////////////////////////////////////////////

		// printf("%s ... ", $row['strategy_id']);
		// setStrategyAnalysisMonthly($app->db, $row['strategy_id']);
	}

	printf("\n[%s] Complete 345 St ======================== \n", date("Y-m-d H:i:s"));

	exit;
/*
	$st_id = 174;
	$aPrev = array('base_mon'=>'', 'sm_index'=>0);
	$sql = sprintf("SELECT LEFT(basedate,7) AS base_mon FROM strategy_daily_analysis WHERE strategy_id = '%s' AND holiday_flag = 0 GROUP BY base_mon ORDER BY base_mon", $st_id);
	$result = $app->db->conn->query($sql);
	while($row = $result->fetch_array()){
		if($aPrev['base_mon'] == '') {
			$sql_first = sprintf("SELECT basedate, ROUND(sm_index) as sm_index FROM strategy_daily_analysis WHERE strategy_id = '%s' AND basedate like '%s%%' AND holiday_flag = 0 ORDER BY basedate ASC LIMIT 1", $st_id, $row['base_mon']);
			$aRow = $app->db->conn->query($sql_first)->fetch_array();
			if($aRow['basedate'] != '') {
				$aPrev['base_mon'] = substr($aRow['basedate'], 0, 7);
				$aPrev['sm_index'] = $aRow['sm_index'];
			}
		}

		$sql_last = sprintf("SELECT basedate, ROUND(sm_index) as sm_index FROM strategy_daily_analysis WHERE strategy_id = '%s' AND basedate like '%s%%' AND holiday_flag = 0 ORDER BY basedate DESC LIMIT 1", $st_id, $row['base_mon']);
		$aRow2 = $app->db->conn->query($sql_last)->fetch_array();
		if($aRow2['basedate'] != '') {
			$row['monthly_pl_rate'] = round((($aRow2['sm_index'] - $aPrev['sm_index']) / $aPrev['sm_index']) * 100, 3);
			printf("    (%s - %s) / %s = %s\n", $aRow2['sm_index'], $aPrev['sm_index'], $aPrev['sm_index'], $row['monthly_pl_rate']);

			$row['timeBase'] = strtotime($aRow2['basedate']);
			$sql_up = sprintf("UPDATE strategy_monthly_analysis SET monthly_pl_rate='%s' WHERE strategy_id = '%s' AND baseyear='%s' AND basemonth='%s'", $row['monthly_pl_rate'], $st_id, date("Y", $row['timeBase']), date("n", $row['timeBase']));
			printf("%s\n", $sql_up);
			//- $app->db->conn->query($sql_up);
			$app->db->update('strategy_monthly_analysis', array('monthly_pl_rate'=>$row['monthly_pl_rate']), array('strategy_id'=>$st_id, 'baseyear'=>date("Y", $row['timeBase']), 'basemonth'=>date("n", $row['timeBase'])));

			$aPrev['base_mon'] = substr($aRow2['basedate'], 0, 7);
			$aPrev['sm_index'] = $aRow2['sm_index'];
		}
	}

	exit;
*/


	$sToday = Date("Y-m-d", time()-86400);
	$result = $app->db->conn->query("SELECT * FROM strategy where strategy_id > 119");					//  258,260,174,179,173,177,92					 where strategy_id >= 67				  where strategy_id = 174
	while($row = $result->fetch_array()){


		$app->db->executesp('interpolate_strategy',array('p_strategy_id'=>$row['strategy_id']));
		printf("%s.\t", $row['strategy_id']);
		continue;

		//- printf("%s \t", $row['strategy_id']);
		/*
		$sql_stdev = sprintf("
					SELECT a.strategy_id, a.basedate, (SELECT ROUND(STDDEV(daily_pl),4) AS pl_stdev FROM strategy_daily_analysis WHERE strategy_id = a.strategy_id AND basedate <= a.basedate AND holiday_flag=0) AS daily_pl_stdev
					FROM strategy_daily_analysis a WHERE strategy_id='%s' AND holiday_flag=0 ORDER BY basedate ASC", $row['strategy_id']);
		$result2 = $app->db->conn->query($sql_stdev);
		while($row2 = $result2->fetch_array()){
			$app->db->update('strategy_daily_analysis', array('daily_pl_stdev'=>$row2['daily_pl_stdev']*1), array('strategy_id'=>$row2['strategy_id'], 'basedate'=>$row2['basedate']));
		}

		$sql_holy = sprintf("SELECT strategy_id, basedate FROM strategy_daily_analysis a WHERE strategy_id='%s' AND holiday_flag != 0 ORDER BY basedate ASC", $row['strategy_id']);
		$result2 = $app->db->conn->query($sql_holy);
		while($row2 = $result2->fetch_array()){
			$sql3 = sprintf("SELECT principal FROM strategy_daily_analysis WHERE strategy_id='%s' AND basedate < '%s' AND holiday_flag = 0 ORDER BY basedate DESC LIMIT 1", $row2['strategy_id'], $row2['basedate']);
			$result3 = $app->db->conn->query($sql3);
			$row3 = $result3->fetch_array();
			$app->db->update('strategy_daily_analysis', array('principal'=>$row3['principal']*1), array('strategy_id'=>$row2['strategy_id'], 'basedate'=>$row2['basedate']));
		}
		*/


		$sql_old = sprintf("SELECT * FROM strategy_daily WHERE strategy_id='%s' AND (flow != 0 or PL != 0) ORDER BY target_date ASC LIMIT 1", $row['strategy_id']);
		$aRowOld = $app->db->conn->query($sql_old)->fetch_array();
		if($aRowOld['daily_id'] != '' && strtotime($aRowOld['target_date']) > 0) {
			$row['sStartDay'] = Date("Y-m-d", strtotime($aRowOld['target_date']));
			printf("{%s} [%s] %s ~ %s    (%s)\n", ++$i, $row['strategy_id'], $row['sStartDay'], $sToday, Date("H:i:s"));

			////////////////////////////////////////////////////
			// analysis_strategy() 계산하기
				setStrategyAnalysis($app->db, $row['strategy_id'], $row['sStartDay'], $sToday);
				setStrategyAnalysisMonthly($app->db, $row['strategy_id']);
				setStrategyAnalysisYearly($app->db, $row['strategy_id']);
			////////////////////////////////////////////////////
		}
	}
	print "\n\n Complete \n";

	exit;


	for($i = strtotime('2011-09-01'); $i <= strtotime($sToday); $i+=86400) {
		$sDay = Date("Y-m-d", $i);
		setStrategyScoreDay($app->db, $sDay);
	}

	$aDebug[] = time();
	setStrategyScore($app->db, 166);

	$aDebug['end'] = time();
	$aDebug['t_e'] = Date("H:i:s", $aDebug['end']);
	$aDebug['exec'] = $aDebug['end'] - $aDebug['start'];
	$app->db->insert('z_dev_log', array('st_id'=>$st_id, 'dl_type'=>'cron_daily_st', 'dl_memo'=>print_r($aDebug, true), 'dl_sys'=>sprintf("%s \n _SERVER : %s", print_r($_REQUEST, true), print_r($_SERVER, true))));



		//	while($row = $result->fetch_array()){
		//		$sql_old = sprintf("SELECT * FROM strategy_daily_analysis WHERE strategy_id='%s' ORDER BY basedate DESC LIMIT 1", $row['strategy_id']);
		//		$aRowOld = $app->db->conn->query($sql_old)->fetch_array();
		//		$row['sStartDay'] = $sToday;
		//		if($row['sStartDay'] <= $aRowOld['basedate']) {
		//			continue;
		//		} else if($aRowOld['basedate'] != ''){
		//			$row['sStartDay'] = Date("Y-m-d", strtotime($aRowOld['basedate']) + 86400);
		//		}
		//		printf("{%s} [%s] %s ~ %s    (%s)\n", ++$i, $row['strategy_id'], $row['sStartDay'], $sToday, Date("H:i:s"));
		//
		//		////////////////////////////////////////////////////
		//		// analysis_strategy() 계산하기
		//			setStrategyAnalysis($app->db, $row['strategy_id'], $row['sStartDay'], $sToday);
		//			setStrategyAnalysisMonthly($app->db, $row['strategy_id']);
		//			setStrategyAnalysisYearly($app->db, $row['strategy_id']);
		//		////////////////////////////////////////////////////
		//	}
		//	$aDebug[] = time();
		//	setStrategyScore($app->db);
		//
		//	$aDebug['end'] = time();
		//	$aDebug['t_e'] = Date("H:i:s", $aDebug['end']);
		//	$aDebug['exec'] = $aDebug['end'] - $aDebug['start'];
		//	$app->db->insert('z_dev_log', array('st_id'=>$st_id, 'dl_type'=>'cron_daily_st', 'dl_memo'=>print_r($aDebug, true), 'dl_sys'=>sprintf("%s \n _SERVER : %s", print_r($_REQUEST, true), print_r($_SERVER, true))));

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
