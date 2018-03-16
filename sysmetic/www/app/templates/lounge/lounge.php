<!doctype html>
<html lang="ko">
<head>
	<title>라운지 | SYSMETIC</title>
	<? include_once $skinDir."/common/head.php" ?>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once $skinDir."/common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container">
			<section class="area lounge">
				<div class="page_title_area no_bg">
					<p class="page_title n_squere">Lounge</p>
					<p class="page_summary">시스메틱 Lounge에서 <strong class="mark"><?=number_format($total_pb)?></strong>명의 PB를 만나보세요. 
					<a href="/investment/developers?page=1&type=P" class="only_btn" style="color:#ffffff;text-decoration:none;border:0px;">전체PB 리스트 바로가기</a></p>
				</div>
				<div class="content_area my_lounge">
					<div class="pb_list">
						<ul>
							<? foreach ($pb as $k => $v) { ?>
							<li>
								<div class="photo">
									<a href="/lounge/<?=$v['uid']?>"><img src="<?=getProfileImg($v['picture'])?>" alt="" /></a>
								</div>
								<div class="name">
									<strong onclick="location.href='/lounge/<?=$v['uid']?>';" style="cursor:pointer"><?=$v['name']?></strong>
									<a href="/lounge/<?=$v['uid']?>"><img src="../images/sub/btn_lounge_coffee.gif" alt="" /></a>
								</div>
								<p class="ror">대표수익률 <?=number_format($v['aStats']['total_pl_rate'],2,'.',',') ?>%</p>
									<!-- <p class="ror">대표수익률 <?=number_format($v['total_profit_rate'],2,'.',',') ?>%</p> -->
							</li>
							<? } ?>
						</ul>
					</div>

					<div class="article_list">
                        <div class="head">
                            <p class="info">시스메틱 Lounge에 <strong class="mark"><?=number_format($total_contents)?></strong>개의 새 글이 등록되었습니다.</p>
                            <div class="view_type">
                                <a href="javascript:;" data-type='timeline' class="type timeline">타임라인형</a>
                                <a href="javascript:;" data-type='thumb' class="type thumbnail curr">썸네일형</a>
                            </div>
                        </div>
                        <div id="article_list">
                        </div>
					</div>
				</div>
			</section>
		</div>
		<!-- //container -->

        <!-- footer -->
		<? require_once $skinDir."/common/footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->
	<script>
	$(function() {
		$('.view_type a').on('click', function(){
			$(this).siblings().removeClass('curr');
			$(this).addClass('curr');

            $('#article_list').html("");
			getContent($(this).data('type'), 1);
		});

		getContent('thumbnail', 1);
	});

	function getContent(type, page) {
		$.ajax({
			mthod: 'get',
			data: {type: type, page: page},
			url: '/lounge/load_contents',
            dataType: 'html',
        }).done(function(html) {
            $('#article_list').append(html);
        });
	}
	</script>
</body>
</html>
