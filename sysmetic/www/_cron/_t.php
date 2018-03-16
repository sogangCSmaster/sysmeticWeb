<?php
//if(!in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
//	exit;
//}
require_once dirname(__FILE__).'/../bootstrap.php';

set_time_limit(0);

	print '<xmp>';
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);


	$nTotCnt = 0;
	$sCssTr = 'margin:0; padding:0; height:28px; text-align:center; vertical-align:middle; background-color:#f0f0f0; color:#888; font-size:13px; font-weight:normal;';
	$sCssTd = 'margin:0; padding:0; padding-top:10px; padding-bottom:10px; padding-right:8px; padding-left:8px; color:#888; font-size:12px; vertical-align:middle;';

	$sHtmlWeekTh = '';
	$aTmp = getWeekTimes(date("Y-m-d", time()-86400));
	$aWeek = array();
	for($i = $aTmp[0]; $i <= $aTmp[0] + (4 * 86400); $i+=86400) {
		$aWeek[] = Date("Y-m-d", $i);

		$sHtmlWeekTh .= '<th style="'. $sCssTr .'">'. Date("n/j", $i) .'</th>';
	}


	$sql = "SELECT uid, email FROM `user` WHERE uid IN (SELECT DISTINCT uid FROM following_strategy WHERE uid > 0) AND uid in ( 473, 5, 9, 350 )";
	$result = $app->db->conn->query($sql);
	while($row = $result->fetch_array()){

		$sHtml = '<tr>'. $sHtmlWeekTh .'</tr>';

		$row['nRow2Cnt'] = 0;
		$sql2 = sprintf("SELECT s.strategy_id, s.name FROM following_strategy fs inner join strategy s on (fs.strategy_id = s.strategy_id) where fs.uid = '%s' and s.is_delete='0' and s.is_operate='1' and s.is_open='1'", $row['uid']);
		$result2 = $app->db->conn->query($sql2);
		while($row2 = $result2->fetch_array()){
			$sHtml .= '<tr><td style="'. $sCssTd .' text-align:center;">'. $row2['name'] .'</td>';
			$aTot = array('week_pl'=>0, 'acc_pl'=>0);
			foreach((array)$aWeek as $sCurDay) {
				$sql3 = sprintf("SELECT basedate, daily_pl, acc_pl FROM strategy_daily_analysis WHERE strategy_id = '%s' AND basedate = '%s'", $row2['strategy_id'], $sCurDay);
				$row3 = $app->db->conn->query($sql3)->fetch_array();
				$sHtml .= '<td style="'. $sCssTd .' text-align:right;">'. number_format($row3['daily_pl']) .'</td>';
				$aTot['week_pl'] += $row3['daily_pl'];
				$aTot['acc_pl'] = $row3['acc_pl'];
			}
			$sHtml .= '<td style="'. $sCssTd .' text-align:right;">'. number_format($aTot['week_pl']) .'</td><td style="'. $sCssTd .' text-align:right;">'. number_format($aTot['acc_pl']) .'</td></tr>';
			++$row['nRow2Cnt'];
		}
		if($row['nRow2Cnt'] > 0) {
			sendMailFavorSt($row['email'], $sHtml);
			sendMailFavorSt('glaucos27@gmail.com', $sHtml);
			sendMailFavorSt('gugisky77@gmail.com', $sHtml);
			++$nTotCnt;
		}
	}

	printf("\n[%s] Complete Weekly Favor St Mailing ( %s    /    %s sec ) ======================== \n", date("Y-m-d H:i:s"), $nTotCnt, time() - $aDebug['start']);

	exit;

	$i = 0;
	$sToday = Date("Y-m-d", time()-(86400*1));
	$result = $app->db->conn->query("SELECT * FROM strategy");					//  258,260,174,179,173,177,92					 where strategy_id >= 67				  where strategy_id = 174
	while($row = $result->fetch_array()){

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
					//- $app->db->executesp('interpolate_strategy',array('p_strategy_id'=>$row['strategy_id']));		// setStrategyAnalysis() 안의 마지막에 수행됨 (2017-05-05)
			////////////////////////////////////////////////////
		}
	}
	printf("\n[%s] Complete Daily St ======================== \n", date("Y-m-d H:i:s"));


		//	for($i = strtotime('2011-09-01'); $i <= strtotime($sToday); $i+=86400) {
		//		$sDay = Date("Y-m-d", $i);
		//		setStrategyScoreDay($app->db, $sDay);
		//	}

	$aDebug[] = time();
	setStrategyScore($app->db);

	$aDebug['end'] = time();
	$aDebug['t_e'] = Date("H:i:s", $aDebug['end']);
	$aDebug['exec'] = $aDebug['end'] - $aDebug['start'];
	$app->db->insert('z_dev_log', array('st_id'=>$st_id, 'dl_type'=>'cron_daily_st', 'dl_memo'=>print_r($aDebug, true), 'dl_sys'=>sprintf("%s \n _SERVER : %s", print_r($_REQUEST, true), print_r($_SERVER, true))));

	printf("\n[%s] Finish All St Analysis ======================== \n", date("Y-m-d H:i:s"));



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
