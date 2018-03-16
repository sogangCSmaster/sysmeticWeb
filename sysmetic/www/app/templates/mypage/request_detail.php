
<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
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
                        <div class="detail_product_name">
                            <dl>
                                <dt>상품명 :</dt>
                                <dd><?=$qna['strategy_name']?></dd>
                            </dl>
                        </div>
						<div class="detail_a">

							<div class="row_t">
								<p class="subject"><?//=$qna['subject']?></p>
								<span class="write_time">&nbsp;&nbsp;|&nbsp;&nbsp;<?=substr($qna['reg_at'], 0, 16)?></span>
								<span class="name"><?=$qna['name']?></span>
							</div>
							<p class="cont">
								<?=nl2br($qna['question'])?>
							</p>
							
                            <? if ($qna['answer']) { ?>
							<div class="answer">
								<div class="row_t">
									<p class="subject"><?//=$qna['answer_subject']?></p>
									<span class="write_time">&nbsp;&nbsp;|&nbsp;&nbsp;<?=date('Y-m-d H:i', $qna['answer_at'])?></span>
									<span class="name"><?=$qna['developer_name']?></span>
								</div>
								<p class="cont">
									<?=nl2br($qna['answer'])?>
								</p>
							</div>
                            <? 
                            } else {
                            ?>
                                <div class="answer wait">
                                    <p>현재 답변 대기 중입니다.</p>
                                </div>
                            <?
                            }
                            ?>
						</div>
						<div class="btn_area">

                            <?
                            if ($_SESSION['user']['user_type'] == 'N') {
                                if (!$qna['answer']) {
                                ?>
							    <a href="/mypage/request/<?=$qna['qna_id']?>/modify" class="btn modify">수정</a>
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
                            ?>
                                <a href="/mypage/request/<?=$qna['qna_id']?>/answer" class="btn reply">답변</a>
                            <?
                            }
                            ?>

							<a href="/mypage/request" class="btn list">목록</a>
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
				<a href="/mypage/request/<?=$qna['qna_id']?>/delete" class="btn_common_red" onclick="commonLayerClose('delete_counsel')">예</a>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('delete_counsel')">아니오</a>
			</div>
		</div>
	</div>
</article>

</body>
</html>
