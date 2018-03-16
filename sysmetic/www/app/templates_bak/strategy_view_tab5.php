<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - <?php echo htmlspecialchars($strategy['name']) ?> - 월간분석</title>	
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


<!-- 월간분석 -->
<div id="strategy_view0" name="strategy_view" class="strategy_view" style="display:block;">
    <div class="tab">
        <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/info" title="통계" class="tab_off"><span class="ir">통계</span></a>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/daily" title="일간분석" class="tab_off"><span class="ir">일간분석</span></a>
        <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/monthly" title="월간분석" class="tab_on"><span class="ir">월간분석</span></a>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/accounts" title="실계좌 정보" class="tab_off"><span class="ir">실계좌 정보 </span></a>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews" title="리뷰" class="tab_last"><span class="ir">리뷰</span></a>
    </div>
	<button onclick="location.href='/strategies/<?php echo $strategy['strategy_id'] ?>/monthly/download'" class="act" title="엑셀로 다운받기"><span class="ir">엑셀로 다운받기</span></button>
    <p class="data_info">화폐단위 : <?php echo $strategy['currency'] ?></p><!------ 화폐단위 불러와서 표시해 줄 것 ------->
    <table border="0" cellspacing="1" cellpadding="0" class="daily">
    <col width="*" /><col width="128" /><col width="128" /><col width="128" />
    <col width="128" /><col width="128" /><col width="128" />
        <thead>
        <tr>
            <td>월</td>
            <td>월평균원금</td>
            <td>입출금</td>
            <td>월 손익</td>
            <td>월 손익률</td>
            <td>누적 손익</td>
            <td>누적 손익률</td>
        </tr>
        </thead>
        <tbody>
		<?php foreach($daily_values as $v){ ?>
        <tr>
            <td class="thead"><?php echo $v['baseyear'].'-'.(($v['basemonth']<10)?"0":"").$v['basemonth'] ?></td>
            <td class="data"><span class="<?php echo getSignClass($v['avg_principal'], 'true') ?>"><?php echo number_format($v['avg_principal']) ?></span></td>
            <td class="data"><span class="<?php echo getSignClass($v['flow'], 'false') ?>"><?php echo number_format($v['flow']) ?></span></td>
            <td class="data"><span class="<?php echo getSignClass($v['monthly_pl'], 'false') ?>"><?php echo number_format($v['monthly_pl']) ?></span></td>
            <td class="data"><span class="<?php echo getSignClass($v['monthly_pl_rate'], 'false') ?>"><?php echo number_format($v['monthly_pl_rate'],2,'.','') ?>%</span></td>
            <td class="data"><span class="<?php echo getSignClass($v['acc_pl'], 'false') ?>"><?php echo number_format($v['acc_pl']) ?></span></td>
            <td class="data"><span class="<?php echo getSignClass($v['acc_pl_rate'], 'false') ?>"><?php echo number_format($v['acc_pl_rate'],2,'.','') ?>%</span></td>
        </tr>
		<?php } ?>
        </tbody>
    </table>

	<?php if($total > 0){ ?>
	<div class="paging">
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/monthly?page=1" class="first<?php if($page == 1) echo '_no' ?>">first</a>
		<?php if($page_start > $page_count){ ?><a href="/strategies/<?php echo $strategy['strategy_id'] ?>/monthly?page=<?php echo $page_start-1 ?>" class="prev">prev</a><?php } ?><!-- class="prev_no" -->
		<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
		<?php if($i > ceil($total / $count)) break; ?>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/monthly?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
		<?php } ?>
		<?php if($page_start + $page_count <= $total_page){ ?><a href="/strategies/<?php echo $strategy['strategy_id'] ?>/monthly?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/monthly?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
	</div>
	<?php } ?>
 </div>


      </div>
    </div>
  </div>
</body>
</html>
