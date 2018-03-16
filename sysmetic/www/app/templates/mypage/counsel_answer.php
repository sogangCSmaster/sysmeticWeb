<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
    $(function() {
        $('#answerFrm').submit(function() {
            if (!$.trim($('#answer_subject').val())) {
                alert('답변제목을 입력하세요');
                $('#answer_subject').focus();
                return false;
            } else if (!$.trim($('#answer').val())) {
                alert('답변내용을 입력하세요');
                $('#answer').focus();
                return false;
            } else {
                if (!confirm('저장하시겠습니까?')) {
                    return false;
                }
            }
        });
    });
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
						<div class="detail_a answer">
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
						</div>
						<form id="answerFrm" action="/mypage/counsel/<?=$req['req_id']?>/answer" method="post">
						<input type="hidden" name="hp" value="<?=$req['req_hp']?>">
						<div class="form_box answer">
								<fieldset>
									<legend class="screen_out">상담 답변</legend>
									<div class="subject">
										<div class="input_box">
											<input type="text" id="answer_subject" name="answer_subject" value="<?=$req['answer_subject']?>" placeholder="답변제목을 입력하세요." />
										</div>
									</div>
									<div class="cont">
										<textarea id="answer" name="answer" placeholder="답변내용을 입력하세요."><?=$req['answer']?></textarea>
									</div>
								</fieldset>
						</div>
						<div class="btn_wrap">
							<a href="/mypage/counsel/<?=$req['req_id']?>" class="btn_common_gray btn_cancel">취소</a>
							<button type="submit" class="btn_common_red">등록</button>
						</div>
						</form>
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

</body>
</html>
