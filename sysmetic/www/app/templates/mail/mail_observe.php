<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders</title>
</head>

<body>

<!-- 전략 변동 자동 알림 메일 -->
<table width="710" border="0" cellspacing="0" cellpadding="0" align="center">
    <thead>
    <tr><td colspan="2" height="20"></td></tr>
    <tr>
        <td width="513" height="60" bgcolor="#262a33" style="padding-left:20px;"><a href="<?php echo htmlspecialchars($url) ?>" target="_blank"><img src="<?php echo htmlspecialchars($url) ?>/img/logo_mail.gif" border="0" /></a></td>
        <td align="left" bgcolor="#262a33"><a href="<?php echo htmlspecialchars($url) ?>" target="_blank"><img src="<?php echo htmlspecialchars($url) ?>/img/link_mail.gif" border="0" /></a></td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan="2" bgcolor="#f0f0f0" style="font-family:돋움, Dotum; font-size:11px; line-height:21px;">
            <table border="0" cellspacing="0" cellpadding="0" width="680" align="center">
                <tr><td height="60"></td></tr>
                <tr>
                    <td height="40" style="font-family:돋움, Dotum; font-size:14px; color:#828894;">
                        <b>SYSMETIC TRADER 에서 관심전략으로 등록한 전략의 변동사항을 알려드립니다.</b>
                    </td>
                </tr>
                <tr><td height="20"></td></tr>
            </table>
             
             <table border="0" cellspacing="1" cellpadding="0" width="680" align="center" bgcolor="#c4c6cc" style="layout:fixed;">
             <col width="*" /> <col width="70" /> <col width="70" /> <col width="70" /> <col width="70" /> <col width="70" />
             <col width="90" /> <col width="90" />
                <thead>
                <tr><td colspan="8" height="1" bgcolor="#c4c6cc"></td></tr>
                <tr height="28" bgcolor="#FFFFFF">
                    <td rowspan="2" style="padding-left:15px; font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;">전략명</td>
                    <td colspan="5" align="center" style="font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;">일간손익</td>
                    <td rowspan="2" align="center" style="width:100px;font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;">주간손익</td>
                    <td rowspan="2" align="center" style="width:100px;font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;">누적손익</td>
                </tr>
                <tr height="28" align="center" bgcolor="#FFFFFF">
                    <td style="font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;"><?php $d_4=date("n/j", strtotime(date("n/j") . " - 4 day")); echo $d_4 ?></td>
                    <td style="font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;"><?php $d_3=date("n/j", strtotime(date("n/j") . " - 3 day")); echo $d_3 ?></td>
                    <td style="font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;"><?php $d_2=date("n/j", strtotime(date("n/j") . " - 2 day")); echo $d_2 ?></td>
                    <td style="font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;"><?php $d_1=date("n/j", strtotime(date("n/j") . " - 1 day")); echo $d_1 ?></td>
                    <td style="font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;"><?php $d=date("n/j"); echo $d ?></td>
                </tr>
                </thead>
                <tbody>
			<?php foreach($strategies as $strategy){ ?>
                <!-- 반복 -->
                <tr bgcolor="#f7f7f7">
                    <td style="padding:4px; font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;"><a href="http://sysmetic.co.kr/strategies/<?php echo $strategy['strategy_id'] ?>"><?php echo htmlspecialchars($strategy['name']) ?></a></td>
                    <td align="right" style="padding-right:5px; font-family:돋움, Dotum; font-size:12px;color:#828894;"><?php echo number_format($strategy['weekly_profit_values'][$d_4]) ?></td>
                    <td align="right" style="padding-right:5px;font-family:돋움, Dotum; font-size:12px;color:#828894;"><?php echo number_format($strategy['weekly_profit_values'][$d_3]) ?></td>
                    <td align="right" style="padding-right:5px; font-family:돋움, Dotum; font-size:12px;color:#828894;"><?php echo number_format($strategy['weekly_profit_values'][$d_2]) ?></td>
                    <td align="right" style="padding-right:5px;font-family:돋움, Dotum; font-size:12px;color:#828894;"><?php echo number_format($strategy['weekly_profit_values'][$d_1]) ?></td>
                    <td align="right" style="padding-right:5px; font-family:돋움, Dotum; font-size:12px;color:#828894;"><?php echo number_format($strategy['weekly_profit_values'][$d]) ?></td>
                        <?php
                                $weeklySum = $strategy['weekly_profit_values'][$d_4];
                                $weeklySum += $strategy['weekly_profit_values'][$d_3];
                                $weeklySum += $strategy['weekly_profit_values'][$d_2];
                                $weeklySum += $strategy['weekly_profit_values'][$d_1];
                                $weeklySum += $strategy['weekly_profit_values'][$d];
                        ?>
                    <td align="right" style="padding-right:5px;font-family:돋움, Dotum; font-size:12px;color:#828894;"><?php echo number_format($weeklySum) ?></td>
                    <td align="right" style="padding-right:5px;font-family:돋움, Dotum; font-size:12px;color:#828894;"><?php echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['total_profit']) ?></td>
                </tr>
                <!-- 반복 -->
			<?php } ?>
                <tr><td colspan="8" height="1" bgcolor="#c4c6cc"></td></tr>
             </table>

             <table border="0" cellspacing="0" cellpadding="0" width="680" align="center" >
                <tr><td height="50"></td></tr>
            </table>

<!--
            <table border="0" cellspacing="0" cellpadding="0" width="580" align="center">
                <tr><td height="60"></td></tr>
                <tr>
                    <td height="40" style="font-family:돋움, Dotum; font-size:14px; color:#828894;">
                        <b>SYSMETIC TRADER 에서 관심전략으로 등록한 전략의 변동사항을 알려드립니다.</b>
                    </td>
                </tr>
            </table>
             <table border="0" cellspacing="0" cellpadding="0" width="680" align="center">
                <thead>
                <tr><td colspan="4" height="20"></td></tr>
                <tr><td colspan="4" height="2" bgcolor="#c4c6cc"></td></tr>
                <tr height="40" bgcolor="#FFFFFF">
                    <td style="width:240px;padding-left:15px; font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;">전략</td>
                    <td align="center" style="width:200px;font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;"><span class="sorting">수익률</span></td>
                    <td align="center" style="width:120px;font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;">MDD</td>
                    <td align="center" style="width:100px;font-family:돋움, Dotum; font-size:12px;color:#828894; font-weight:bold;">SM Score</td>
                </tr>
                <tr><td colspan="4" height="1" bgcolor="#c4c6cc"></td></tr>
                </thead>
                <tbody>
				<?php foreach($strategies as $strategy){ ?>
                <tr bgcolor="#FFFFFF">
                    <td style="padding:15px 0 15px 15px;font-family:돋움, Dotum; font-size:12px;color:#828894;">
                        <?php if($strategy['strategy_type'] == 'M'){ ?>
						<img src="<?php echo htmlspecialchars($url) ?>/img/ico_menual.gif" />
						<?php }else if($strategy['strategy_type'] == 'S'){ ?>
						<img src="<?php echo htmlspecialchars($url) ?>/img/ico_system.gif" />
						<?php } ?>

						<?php foreach($strategy['items'] as $v){ ?>
                        <img src="<?php echo $v['icon'] ?>" alt="<?php echo htmlspecialchars($v['name']) ?>" />
						<?php } ?><br />
                        <font style="font-size:14px"><b><?php echo htmlspecialchars($strategy['name']) ?></b></font><br /><br />
                        
                        <span style="line-height:24px;">
                        트레이더 : <?php if(!empty($strategy['developer']['picture'])){ ?><img src="<?php echo htmlspecialchars($strategy['developer']['picture']) ?>" class="trader" /> <?php } ?><?php if(empty($strategy['developer']['nickname'])) echo htmlspecialchars($strategy['developer_name']); else echo htmlspecialchars($strategy['developer']['nickname']) ?><br />
                        증권사 : <?php if(!empty($strategy['broker']['logo'])){ ?><img src="<?php echo $strategy['broker']['logo'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['broker']['company']) ?><?php } ?><br />
                        매매툴 : <?php if(!empty($strategy['system_tool']['logo'])){ ?><img src="<?php echo $strategy['system_tool']['logo'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['system_tool']['name']) ?><?php } ?>
                        </span>
                    </td>
                    <td style="padding:15px 0 15px 10px;">
                        <table border="0" cellspacing="0" cellpadding="0" width="180" align="center">
                            <tr height="28">
                                <td width="80" style="font-family:돋움, Dotum; font-size:12px;color:#828894;">누적 수익률</td>
                                <td width="100" align="right" style="font-family:돋움, Dotum; font-size:14px;color:#828894;"><b><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['total_profit_rate']);else echo '0' ?>%</b></td>
                            </tr>
                            <tr height="28">
                                <td width="80" style="font-family:돋움, Dotum; font-size:12px;color:#828894;">연간 수익률</td>
                                <td width="100" align="right" style="font-family:돋움, Dotum; font-size:14px;color:#828894;"><b><?php if(isset($strategy['yearly_profit_rate'][date('Y').''])) echo $strategy['yearly_profit_rate'][date('Y').'']; else echo '0' ?>%</b></td>
                            </tr>
                            <tr height="28">
                                <td width="80" style="font-family:돋움, Dotum; font-size:12px;color:#828894;">원금(<?php echo htmlspecialchars($strategy['currency']) ?>)</td>
                                <td width="100" align="right" style="font-family:돋움, Dotum; font-size:14px;color:#828894;"><b><?php echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['principal']) ?></b></td>
                            </tr>
                        </table>                    
                    </td>
                    <td align="center" style="padding:15px 0 15px 15px;font-family:돋움, Dotum; font-size:11px;color:#828894;">
                        <font style="font-size:14px;">
						<?php if($strategy['currency'] == 'KRW'){ ?>
						<b><?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd'])) echo $strategy['daily_values'][count($strategy['daily_values'])-1]['mdd'] / 10000; else echo '0'; ?></b></font><br />(Ten thousand)
						<?php }else{ ?>
						<b><?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd'])) echo $strategy['daily_values'][count($strategy['daily_values'])-1]['mdd']; else echo '0'; ?></b></font><br />
						<?php } ?>
						<br /><?php echo $strategy['currency'] ?>
                    </td>
                    <td align="center" style="padding:15px 0 15px 0;font-family:돋움, Dotum; font-size:14px;color:#828894;"><b><?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['sharp_ratio'])) echo $strategy['daily_values'][count($strategy['daily_values'])-1]['sharp_ratio']; else echo '0' ?></b></td>
                </tr>
                <tr><td colspan="4" height="1" bgcolor="#c4c6cc"></td></tr>
				<?php } ?>
                <tr><td colspan="4" height="50"></td></tr>
                </tbody>
            </table>
              //반복 -->
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr><td colspan="2" height="8"></td></tr>
    <tr>
        <td colspan="2" style="font-family:돋움, Dotum; font-size:11px; color:#a4a9b3; line-height:21px;">
            이 메일은 SYMETIC  에서 <?php echo date('Y') ?>년 <?php echo date('n') ?>월 <?php echo date('j') ?>일 기준 메일수신동의한 사용자에 한해 발송된 메일로 메일주소는 발신전용입니다.<br />
            수신을 원치 않는 경우, 홈페이지 > 개인정보 변경에서 수신동의를 변경해 주세요.
        </td>
    </tr>
    <tr><td colspan="2" height="40"></td></tr>
    </tfoot>
</table>

</body>
</html>