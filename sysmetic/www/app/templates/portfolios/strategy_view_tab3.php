<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - <?php echo htmlspecialchars($strategy['name']) ?> - 실계좌정보</title>	
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


<!-- 실계좌 정보 -->
<div id="strategy_view0" name="strategy_view" class="strategy_view" style="display:block;">
    <div class="tab">
        <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/info" title="통계" class="tab_off"><span class="ir">통계</span></a>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/daily" title="일간분석" class="tab_off"><span class="ir">일간분석</span></a>
        <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/monthly" title="월간분석" class="tab_off"><span class="ir">월간분석</span></a>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/accounts" title="실계좌 정보" class="tab_on"><span class="ir">실계좌 정보 </span></a>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews" title="리뷰" class="tab_last"><span class="ir">리뷰</span></a>
    </div>
	<?php if(count($monthly_values) > 0){ ?>
    <ul class="certification">
		<?php foreach($monthly_values as $v){ ?>
        <li>
            <dl>
                <dd><img src="<?php echo $v['image'] ?>" onclick="parent.openImage(this.src);" /></dd>
                <dt><?php echo $v['title'] ?></dt>
            </dl>
        </li>
		<?php } ?>
    </ul>
	<?php } ?>

    <?php if($total > 0){ ?>
	<div class="paging">
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
		<?php if($page_start > $page_count){ ?><a href="/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
		<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
		<?php if($i > ceil($total / $count)) break; ?>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
		<?php } ?>
		<?php if($page_start + $page_count <= $total_page){ ?><a href="/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
	</div>
	<?php } ?>
 </div>


      </div>
    </div>
  </div>
  
</body>
</html>
