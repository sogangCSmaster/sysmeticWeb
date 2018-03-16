<!doctype html>
<html lang="ko">
<head>
	<title>PB/트레이더 목록</title>
	<? require_once "common/head.php" ?>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once "common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container">

			<section class="area">
				<div class="page_title_area">
					<h2 class="page_title n_squere">PB, 트레이더 목록</h2>
					<p class="page_summary"></p>
				</div>

				<div class="content_area">
                    <?
                    foreach ($developers as $developer) {
                        $v = array_map('htmlspecialchars', $developer);
                    ?>
                    <hr />
                    <ul>
                        <li><img src="<?=$v['picture_s']?>" /></li>
                        <li><?=($v['nickname'])?> <?=($v['user_type'] == 'T') ? '트레이더' : 'PB'; ?></li>
                        <? if ($v['user_type'] == 'T') { ?>
                        <li><?=($v['sido'])?> <?=($v['gugun'])?> 
                        <? } else { ?>
                        <li><?=($v['company'])?></li>
                        <li><?=($v['sido2'])?> <?=($v['gugun2'])?> <?=($v['part'])?></li>
                        <? } ?>
                        <li>전략 <?=number_format($v['strategy_cnt'])?>개</li>
                        <li>포트폴리오 <?=number_format($v['portfolio_cnt'])?>개</li>
                        <li><a href="/developers/<?=$v['uid']?>/strategies">전략목록보기</a></li>
                    </ul>
                    <? 
                    }
                    ?>
                </div>
            </section>

		</div>
		<!-- //container -->

        <!-- footer -->
		<? require_once "common/footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->

</body>
</html> 