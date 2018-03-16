<div class="stats">
화폐단위 : <?=$strategy['currency']?>
<table>
    <colgroup>
        <col style="width:22%" />
        <col style="width:28%" />
        <col style="width:22%" />
        <col style="width:28%" />
    </colgroup>
    <tbody>
        <tr>
            <th scope="col">표준편차</th>
            <td><?=$strategy['daily_values'][count($strategy['daily_values'])-1]['daily_pl_stdev']?></td>
            <th scope="col">Sharp ratio</th>
            <td><?=number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['sharp_ratio'],2,'.','')?>%</td>
		</tr>
        <tr>
            <th scope="col">잔고</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['balance']<0) ? 'class="blue"' : ''?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['balance']); else echo 0 ?></td>
            <th scope="col">운영기간</th>
            <td>
            <?php
            // if(count($strategy['daily_values']))
			if($strategy['aMaxContinue']['start_day'] != '' && $strategy['aMaxContinue']['end_day'] != '')
			{
                $last_time = mktime(0, 0, 0, substr($strategy['aMaxContinue']['end_day'], 5, 2), substr($strategy['aMaxContinue']['end_day'], 8, 2), substr($strategy['aMaxContinue']['end_day'], 0, 4));
                $first_time = mktime(0, 0, 0, substr($strategy['aMaxContinue']['start_day'], 5, 2), substr($strategy['aMaxContinue']['start_day'], 8, 2), substr($strategy['aMaxContinue']['start_day'], 0, 4));
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
            <th scope="col">누적 입출금액</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['acc_flow']<0) ? 'class="blue"' : ''?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_flow']); else echo 0 ?></td>
            <th scope="col">시작일자</th>
            <td><?php echo $strategy['aMaxContinue']['start_day']; ?></td>
        </tr>
        <tr>
            <th scope="col">원금</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['principal']<0) ? 'class="blue"' : ''?>"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['principal']); else echo 0 ?></td>
            <th scope="col">최종일자</th>
            <td><?php echo $strategy['aMaxContinue']['end_day']; ?></td>
        </tr>
    </tbody>
</table>
<table>
    <colgroup>
        <col style="width:22%" />
        <col style="width:28%" />
        <col style="width:22%" />
        <col style="width:28%" />
    </colgroup>
    <tbody>
        <tr>
            <th scope="col">누적 수익 금액</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl']); else echo 0 ?></td>
            <th scope="col">누적 수익률(%)</th>
            <td class="right"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2,'.',''); else echo 0 ?>%</td>
        </tr>
        <tr>
            <th scope="col">최대 누적수익금액</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['max_acc_pl']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_acc_pl']); else echo 0 ?></td>
            <th scope="col">최대 누적수익률(%)</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['max_acc_pl_rate']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_acc_pl_rate'],2,'.',''); else echo 0 ?>%</td>
        </tr>
    </tbody>
</table>
<table>
    <colgroup>
        <col style="width:22%" />
        <col style="width:28%" />
        <col style="width:22%" />
        <col style="width:28%" />
    </colgroup>
    <tbody>
        <tr>
            <th scope="col">현재 자본인하금액</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['dd']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['dd']); else echo 0 ?></td>
            <th scope="col">현재 자본인하율(%)</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['dd_rate']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['dd_rate'],2,'.',''); else echo 0 ?>%</td>
        </tr>
        <tr>
            <th scope="col">최대 자본인하금액</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['mdd']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd']); else echo 0 ?></td>
            <th scope="col">최대 자본인하율(%)</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['mdd_rate']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd_rate'],2,'.',''); else echo 0 ?>%</td>
        </tr>
    </tbody>
</table>
<table>
    <colgroup>
        <col style="width:22%" />
        <col style="width:28%" />
        <col style="width:22%" />
        <col style="width:28%" />
    </colgroup>
    <tbody>
        <tr>
            <th scope="col">평균 손익 금액</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl']); else echo 0 ?></td>
            <th scope="col">평균손익률(%)</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate'],2,'.',''); else echo 0 ?>%</td>
        </tr>
        <tr>
            <th scope="col">최대 일수익 금액</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_profit']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_profit']); else echo 0 ?></td>
            <th scope="col">최대 일수익율(%)</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_profit_rate']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_profit_rate'],2,'.',''); else echo 0 ?>%</td>
        </tr>
        <tr>
            <th scope="col">최대 일손실 금액</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss']); else echo 0 ?></td>
            <th scope="col">최대 일손실율(%)</th>
            <td <?=(count($strategy['daily_values']) && $strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss_rate']<0) ? 'class="blue"' : ''?>">
            <?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss_rate'],2,'.',''); else echo 0 ?>%</td>
        </tr>
    </tbody>
</table>
<table>
    <colgroup>
        <col style="width:22%" />
        <col style="width:28%" />
        <col style="width:22%" />
        <col style="width:28%" />
    </colgroup>
    <tbody>
        <tr>
            <th scope="col">총 매매 일수</th>
            <td><?php echo number_format($strategy['aMaxContinue']['tot_days']) ?>일</td>
            <th scope="col">현재 연속 손익일수</th>
            <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['trade_days']); else echo 0 ?>일</td>
        </tr>
        <tr>
            <th scope="col">총 이익 일수</th>
            <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['profit_days']); else echo 0 ?>일</td>
            <th scope="col">최대 연속 이익일수</th>
            <td><?php echo number_format($strategy['aMaxContinue']['max_profit_days_continue']); /*if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss_days']); else echo 0i */ ?>일</td>
        </tr>
        <tr>
            <th scope="col">총 손실 일수</th>
            <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['loss_days']); else echo 0 ?>일</td>
            <th scope="col">최대 연속 손실일수</th>
            <td><?php echo number_format($strategy['aMaxContinue']['max_loss_days_continue']); /*if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['max_daily_loss_days']); else echo 0i */ ?>일</td>
        </tr>
    </tbody>
</table>
<table>
    <colgroup>
        <col style="width:22%" />
        <col style="width:28%" />
        <col style="width:22%" />
        <col style="width:28%" />
    </colgroup>
    <tbody>
        <tr>
            <th scope="col">승률</th>
            <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['winning_rate'],2,'.',''); else echo 0 ?>%</td>
            <th scope="col">고점갱신 후 경과일</th>
            <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['after_peak_days']); else echo 0 ?>일</td>
        </tr>
        <tr>
            <th scope="col">Profit Factor</th>
            <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['profit_factor'],4,'.',''); else echo 0 ?></td>
            <th scope="col">ROA</th>
            <td><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['roa'],4,'.',''); else echo 0 ?></td>
        </tr>
    </tbody>
</table>
</div>
