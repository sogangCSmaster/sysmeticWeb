<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
    $(function() {
        $('#editFrm').submit(function() {
            /*
            if (!$.trim($('#subject').val())) {
                alert('제목을 입력하세요');
                $('#subject').focus();
                return false;
            } else 
            */
            if (!$.trim($('#question').val())) {
                alert('내용을 입력하세요');
                $('#question').focus();
                return false;
            } else {
                if (!confirm('수정하시겠습니까?')) {
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
                        <div class="detail_product_name">
                            <dl>
                                <dt>상품명 :</dt>
                                <dd><?=$qna['strategy_name']?></dd>
                            </dl>
                        </div>
						<form id="editFrm" action="/mypage/request/<?=$qna['qna_id']?>/modify" method="post">
						<div class="form_box">
								<fieldset>
									<legend class="screen_out">질문수정</legend>
									<div class="subject">
										<!--div class="input_box">
											<input type="text" id="subject" name="subject" value="<?=$qna['subject']?>" />
										</div-->
									</div>
									<div class="cont">
										<textarea id="question" name="question"><?=$qna['question']?></textarea>
									</div>
								</fieldset>
						</div>
						<div class="btn_wrap">
							<a href="/mypage/request/<?=$qna['qna_id']?>" class="btn_common_gray btn_cancel">취소</a>
							<button type="submit" class="btn_common_red">수정</button>
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
