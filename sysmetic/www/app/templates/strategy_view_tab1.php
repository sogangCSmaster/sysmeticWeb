<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - <?php echo htmlspecialchars($strategy['name']) ?> - 통계</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
</head>

<body>
<!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content">
            <div class="frame_page">

            <!-- 통계 -->
            <div id="strategy_view0" name="strategy_view" class="strategy_view" style="display:block;">
                <div class="tab">
                    <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/info" title="통계" class="tab_on"><span class="ir">통계</span></a>
                    <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/daily" title="일간분석" class="tab_off"><span class="ir">일간분석</span></a>
                    <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/monthly" title="월간분석" class="tab_off"><span class="ir">월간분석</span></a>
                    <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/accounts" title="실계좌 정보" class="tab_off"><span class="ir">실계좌 정보 </span></a>
                    <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews" title="리뷰" class="tab_last"><span class="ir">리뷰</span></a>
                </div>

                <p class="data_info">화폐단위 : <?php echo $strategy['currency'] ?></p><!------ 화폐단위 불러와서 표시해 줄 것 ------->
                <table berder="0" cellspacing="1" cellpadding="0" class="statistics">
                <col width="225" /> <col width="222" /><col width="225" /> <col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">KP Ratio</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['kp_ratio'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo $strategy['daily_values'][count($strategy['daily_values'])-1]['kp_ratio']; else echo 0 ?></span></td>
                        <td class="thead">SM Score</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo $strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score']; else echo 0 ?></span></td>
                    </tr>
                </tbody>
                </table>

                <table berder="0" cellspacing="1" cellpadding="0" class="statistics">
                <col width="225" /> <col width="222" /><col width="225" /> <col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">잔고</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['balance'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['balance']); else echo 0 ?></span></td>
                        <td class="thead">운영기간</td>
                        <td>
						<?php
						if(count($strategy['daily_values'])){
							$last_time = mktime(0, 0, 0, substr($strategy['daily_values'][count($strategy['daily_values'])-1]['basedate'], 5, 2), substr($strategy['daily_values'][count($strategy['daily_values'])-1]['basedate'], 8, 2), substr($strategy['daily_values'][count($strategy['daily_values'])-1]['basedate'], 0, 4));
							$first_time = mktime(0, 0, 0, substr($strategy['daily_values'][0]['basedate'], 5, 2), substr($strategy['daily_values'][0]['basedate'], 8, 2), substr($strategy['daily_values'][0]['basedate'], 0, 4));
							if($last_time - $first_time > 0){
								if(floor(($last_time - $first_time)/(60*60*24*30*12)) > 0) echo floor(($last_time - $first_time)/(60*60*24*30*12)).'년 ';
								if(ceil((($last_time - $first_time)%(60*60*24*30*12))/(60*60*24*30)) > 0) 
									echo ceil((($last_time - $first_time)%(60*60*24*30*12))/(60*60*24*30)).'월';
							}
						}
						?>
						</td>
                    </tr>
                    <tr>
                        <td class="thead">누적 입출금액</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_flow'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_flow']); else echo 0 ?></span></td>
                        <td class="thead">시작일자</td>
                        <td><?php if(count($strategy['daily_values'])) echo $strategy['daily_values'][0]['basedate']; ?></td>
                    </tr>
                    <tr>
                        <td class="thead">원금</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['principal'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['principal']); else echo 0 ?></span></td>
                        <td class="thead">최종일자</td>
                        <td><?php if(count($strategy['daily_values'])) echo $strategy['daily_values'][count($strategy['daily_values'])-1]['basedate']; ?></td>
                    </tr>
                </tbody>
                </table>
                
                <table border="0" cellspacing="1" cellpadding="0" class="statistics">
                <col width="225" /> <col width="222" /><col width="225" /> <col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">누적 수익금액</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl']); else echo 0 ?></span></td>
                        <td class="thead">누적 수익률(%)</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2,'.',''); else echo 0 ?>%</span></td>
                    </tr>
                    <tr>
                        <td class="thead">최대 누적 수익금액</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['max_acc_pl'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_acc_pl']); else echo 0 ?></span></td>
                        <td class="thead">최대 누적 수익률(%)</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['max_acc_pl_rate'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_acc_pl_rate'],2,'.',''); else echo 0 ?>%</span></td>
                    </tr>
                </tbody>
                </table>
                
                <table border="0" cellspacing="1" cellpadding="0" class="statistics">
                <col width="225" /> <col width="222" /><col width="225" /> <col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">현재 자본인하금액</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['dd'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['dd']); else echo 0 ?></span></td>
                        <td class="thead">현재 자본인하율(%)</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['dd_rate'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['dd_rate'],2,'.',''); else echo 0 ?>%</span></td>
                    </tr>
                    <tr>
                        <td class="thead">최대 자본인하금액</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd']); else echo 0 ?></span></td>
                        <td class="thead">최대 자본인하율(%)</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd_rate'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd_rate'],2,'.',''); else echo 0 ?>%</span></td>
                    </tr>
                </tbody>
                </table>

                <table border="0" cellspacing="1" cellpadding="0" class="statistics">
                <col width="225" /> <col width="222" /><col width="225" /> <col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">평균 손익 금액</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl']); else echo 0 ?></span></td>
                        <td class="thead">평균손익률(%)</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate'],2,'.',''); else echo 0 ?>% </span></td>
                    </tr>
                    <tr>
                        <td class="thead">최대 일수익 금액</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_profit'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_profit']); else echo 0 ?></span></td>
                        <td class="thead">최대 일수익률(%)</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_profit_rate'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_profit_rate'],2,'.',''); else echo 0 ?>%</span></td>
                    </tr>
                    <tr>
                        <td class="thead">최대 일손실 금액</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss']); else echo 0 ?></span></td>
                        <td class="thead">최대 일손실률(%)</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss_rate'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss_rate'],2,'.',''); else echo 0 ?>%</span></td>
                    </tr>
                </tbody>
                </table>

                <table border="0" cellspacing="1" cellpadding="0" class="statistics">
                <col width="225" /> <col width="222" /><col width="225" /> <col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">총 매매 일수</td>
                        <td><?php echo number_format(count($strategy['daily_values'])) ?>일</td>
                        <td class="thead">현재 연속 손익일수</td>
                        <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['trade_days']); else echo 0 ?>일</td>
                    </tr>
                    <tr>
                        <td class="thead">총 이익 일수</td>
                        <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['profit_days']); else echo 0 ?>일</td>
                        <td class="thead">최대 연속 이익일수</td>
                        <td><?php /*if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_profit_days']); else echo 0*/ ?>일</td>
                    </tr>
                    <tr>
                        <td class="thead">총 손실 일수</td>
                        <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['loss_days']); else echo 0 ?>일</td>
                        <td class="thead">최대 연속 손실일수</td>
                        <td><?php /*if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss_days']); else echo 0i */ ?>일</td>
                    </tr>
                </tbody>
                </table>

                <table border="0" cellspacing="1" cellpadding="0" class="statistics">
                <col width="225" /> <col width="222" /><col width="225" /> <col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">승률</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['winning_rate'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['winning_rate'],2,'.',''); else echo 0 ?>%</span></td>
                        <td class="thead">고점갱신 후 경과일 </td>
                        <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['after_peak_days']); else echo 0 ?>일</td>
                    </tr>
                    <tr>
                        <td class="thead">Profit Factor</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['profit_factor'], 'true') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['profit_factor'],4,'.',''); else echo 0 ?></span></td>
                        <td class="thead">ROA</td>
                        <td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['roa'], 'false') ?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['roa'],4,'.',''); else echo 0 ?></span></td>
                    </tr>
                </tbody>
                </table>
            </div>
	
			</div>
		</div>
	</div>
</body>
</html>
