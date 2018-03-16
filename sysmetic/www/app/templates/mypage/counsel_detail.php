
<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>

    <script>

    <?php if(!empty($flash['error'])){ ?>
    alert('<?php echo htmlspecialchars($flash['error']) ?>');
    <?php } ?>

    </script>
</head>
<body>
    <!-- wrapper -->
    <div class="wrapper">
        <!-- header -->
        <? require_once $skinDir."common/header.php" ?>
        <!-- header -->
        <!-- container -->
        <div class="container">

			<section class="area mypage">	
				<div class="cont_a">

                <? require_once $skinDir."mypage/sub_menu.php" ?>

					<div class="content my_counsel">
						<div class="detail_a">
							<div class="row_t">
								<p class="subject"><?=$req['subject']?></p>
								<span class="write_time">&nbsp;&nbsp;|&nbsp;&nbsp;<?=substr($req['reg_date'], 0, 16)?></span>
								<span class="name"><?=$req['req_name']?></span>
							</div>
							<div class="info">
                                <p>
                                <? if ($req['req_type'] == 'Offline') { ?>
								상담희망시간 : <?=$req['hope_date']?><br />
                                <? } ?>
								관심 있는 상품 : <?=$req['strategy']?> /  투자 개시 금액 : <?=$req['s_price']?>  /  투자 개시 시점 : <?=$req['s_date']?>
                                </p>
							</div>
							<p class="cont">
								<?=nl2br($req['contents'])?>
							</p>
							
                            <? if ($req['status'] && $req['req_type'] == 'Online') { ?>
							<div class="answer">
								<div class="row_t">
									<p class="subject"><?=$req['answer_subject']?></p>
									<span class="write_time">&nbsp;&nbsp;|&nbsp;&nbsp;<?=substr($req['answer_date'], 0, 16)?></span>
									<span class="name"><?=$req['pb_name']?></span>
								</div>
								<p class="cont">
									<?=nl2br($req['answer'])?>
								</p>
							</div>
                            <? 
                            } else {
                                if ($req['req_type'] == 'Online') {
                            ?>
                                <div class="answer wait">
                                    <p>현재 답변 대기 중입니다.</p>
                                </div>
                            <?
                                }
                            }
                            ?>
						</div>
						<div class="btn_area">

                            <?
                            if ($_SESSION['user']['user_type'] == 'N') {
                                if ($req['status'] == 0) {
                                ?>
							    <a href="/mypage/counsel/<?=$req['req_id']?>/modify" class="btn modify">수정</a>
                                <?
                                } else {
                                ?>
							    <a href="javascript:;" class="btn modify" onclick="commonLayerOpen('modify_info')">수정</a>
                                <?
                                }
                                ?>
							    <a href="javascript:;" class="btn delete" onclick="commonLayerOpen('delete_counsel')">삭제</a>
                            <? 
                            } else {
								if ($_SESSION['user']['user_type'] == 'P') {
									if ($req['req_type'] == 'Online') {
								?>
									<a href="/mypage/counsel/<?=$req['req_id']?>/answer" class="btn reply">답변</a>
								<?
									} else {
								?>
								
									<form id="answerFrm" action="/mypage/counsel/<?=$req['req_id']?>/answer" method="post">
									<input type="hidden" name="hp" value="<?=$req['mobile']?>">
									<button type="submit" class="btn complete">상담완료</button>
									</form>

								<?
									}
								}
                            }
                            ?>

							<a href="/mypage/counsel" class="btn list">목록</a>
						</div>
					</div>


                </div>
            </section>
        </div>
        <!-- //container -->

        <!-- footer -->
        <? require_once $skinDir."common/footer.php" ?>
        <!-- //footer -->
    </div>
    <!-- //wrapper -->

<!-- 수정 안내 레이어팝업 -->
<article class="layer_popup common_info modify_info">
	<div class="dim" onclick="commonLayerClose('modify_info')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('modify_info')"></button>
		</div>
		<div class="cont">
			<p class="txt_caution">수정은 답변 전에만 가능합니다. </p>
			<div class="btn_area">
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('modify_info')">닫기</a>
			</div>
		</div>
	</div>
</article>

<!-- 삭제 레이어팝업 -->
<article class="layer_popup delete_counsel">
	<div class="dim" onclick="commonLayerClose('delete_counsel')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('delete_counsel')"></button>
		</div>
		<div class="cont">
			<div class="summary">
				<p class="q_msg">해당내용을<br /><span class="mark">삭제하시겠습니까?</span></p>
			</div>
			<div class="btn_area half">
				<a href="/mypage/counsel/<?=$req['req_id']?>/delete" class="btn_common_red" onclick="commonLayerClose('delete_counsel')">예</a>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('delete_counsel')">아니오</a>
			</div>
		</div>
	</div>
</article>

</body>
</html>
