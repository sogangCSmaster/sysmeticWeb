<div class="analysis">
    <div class="download_file">
        <a href="/strategies/<?=$strategy['strategy_id']?>/daily/download">엑셀로 다운받기</a>
        <a href="/strategies/<?=$strategy['strategy_id']?>/dailyall/download">엑셀로 다운받기(분석지표 포함)</a>
    </div>
화폐단위 : <?=$strategy['currency']?>
    <table>
        <colgroup>
            <col style="width:12.28%" />
            <col style="width:15.28%" />
            <col style="width:15.28%" />
            <col style="width:15.28%" />
            <col style="width:12.28%" />
            <col style="width:15.28%" />
            <col style="width:14.28%" />
        </colgroup>
        <thead>
            <tr>
                <th scope="col">날짜</th>
                <th scope="col">원금</th>
                <th scope="col">입출금</th>
                <th scope="col">일 손익</th>
                <th scope="col">일 손익률</th>
                <th scope="col">누적손익</th>
                <th scope="col">누적수익률</th>
            </tr>
        </thead>
        <tbody>
            <? foreach ($daily_values as $v) { ?>
            <tr>
                <td class="center"><?php echo $v['basedate'] ?></td>
                <td class="right <?=($v['principal']<0) ? 'blue' : ''?>"><?php echo number_format($v['principal']) ?></td>
                <td class="right <?=($v['flow']<0) ? 'blue' : ''?>"><?php echo number_format($v['flow']) ?></td>
                <td class="right <?=($v['daily_pl']<0) ? 'blue' : ''?>"><?php echo number_format($v['daily_pl']) ?></td>
                <td class="right <?=($v['daily_pl_rate']<0) ? 'blue' : ''?>"><?php echo number_format($v['daily_pl_rate'],2,'.','') ?>%</td>
                <td class="right <?=($v['acc_pl']<0) ? 'blue' : ''?>"><?php echo number_format($v['acc_pl']) ?></td>
                <td class="right <?=($v['acc_pl_rate']<0) ? 'blue' : ''?>"><?php echo number_format($v['acc_pl_rate'],2,'.','') ?>%</td>
            </tr>
            <? } ?>
        </tbody>
    </table>

    <? if ($total > 0) { ?>
    <nav class="page_nate">

        <a href="javascript:;movePage(1);" class="btn_page first">first</a>
        <? if ($page_start > $page_count) { ?>
        <a href="javascript:;movePage(<?=$page_start-1 ?>);" class="btn_page prev">prev</a>
        <? } ?>
        <? for ($i = $page_start;$i<=$page_start + $page_count - 1;$i++) { ?>
            <? if ($i > ceil($total / $count)) break; ?>
            <a href="javascript:;movePage(<?=$i?>);" class="direct <?=($page == $i) ? "curr" : ""?>"><?=$i?></a>
        <? } ?>
        <? if ($page_start + $page_count <= $total_page) { ?>
            <a href="javascript:;movePage(<?=($page_start + $page_count)?>);" class="btn_page next">next</a>
        <? } ?>
        <a href="javascript:;movePage(<?=($total_page)?>);" class="btn_page last">last</a>

    </nav>
    <? } ?>
</div>

<script>
function movePage(page) {
    $('.details .cont_area #daily').load('/strategies/<?=$strategy['strategy_id']?>/daily?page='+page);
}
</script>
