<?php
require_once dirname(__FILE__).'/../bootstrap.php';

set_time_limit(0);

	print '<xmp>';
	printf("\n[%s] Start Weekly Favor St Mailing ======================== \n", date("Y-m-d H:i:s"));
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);


	$nTotCnt = 0;
	$sCssTr = 'margin:0; padding:0; height:28px; text-align:center; vertical-align:middle; background-color:#f0f0f0; color:#888; font-size:13px; font-weight:normal;';
	$sCssTd = 'margin:0; padding:0; padding-top:10px; padding-bottom:10px; padding-right:8px; padding-left:8px; color:#888; font-size:12px; vertical-align:middle;';

	$sHtmlWeekTh = '';
	$aTmp = getWeekTimes(date("Y-m-d", time() - (86400 * 3) ));
	$aWeek = array();
	for($i = $aTmp[0]; $i <= $aTmp[0] + (4 * 86400); $i+=86400) {
		$aWeek[] = Date("Y-m-d", $i);

		$sHtmlWeekTh .= '<th style="'. $sCssTr .'">'. Date("n/j", $i) .'</th>';
	}


	$sql = "SELECT uid, email FROM `user` WHERE uid IN (SELECT DISTINCT uid FROM following_strategy WHERE uid > 0)";
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
			sleep(rand(1,5));
			++$nTotCnt;
		}
	}

	printf("\n[%s] Complete Weekly Favor St Mailing ( %s    /    %s sec ) ======================== \n", date("Y-m-d H:i:s"), $nTotCnt, time() - $aDebug['start']);