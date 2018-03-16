<!doctype html>
<html lang="ko">
<head>
	<title>회원가입 | SYSMETIC</title>
	<? require_once "common_head.php" ?>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once "common_header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container join">
			<section class="area">
				<div class="page_title_area">
					<h2 class="page_title n_squere">회원가입</h2>
					<p class="page_summary">시스메틱 홈페이지를 찾아주셔서 감사합니다. 약관에 동의하셔야 가입이 가능합니다.</p>
				</div>
				<div class="content_area step02">
			        <form action="/agree_ok" method="post">
				    <input type="hidden" name="platform" value="<?php echo htmlspecialchars($platform) ?>" />
				    <input type="hidden" name="type" value="<?=$type?>" />

						<fieldset>
							<legend class="screen_out">약관 동의</legend>
							<div class="step_view">02.약관 동의</div>
							<div class="group">
								<div class="check_area">
									<input type="checkbox" name="agree1" id="check01">
									<label for="check01">시스메틱 이용약관에 동의합니다.</label>
								</div>
								<div class="cont_area">
									<p class="cont">
                                    <?php require_once('agree_rules.php') ?>
									</p>
								</div>
							</div>
							<div class="group">
								<div class="check_area">
									<input type="checkbox" name="agree2" id="check02">
									<label for="check02">개인정보 취급방침에 동의합니다.</label>
								</div>
								<div class="cont_area">
									<p class="cont">
                                    <?php require_once('agree_terms.php') ?>
									</p>
								</div>
							</div>
							<div class="group">
								<div class="check_area">
									<input type="checkbox" name="agree3" id="check03">
									<label for="check03">제3자 정보제공에 동의합니다. </label>
								</div>
								<div class="cont_area">
									<p class="cont">
                                    <?php require_once('agree_provision.php') ?>
									</p>
								</div>
							</div>

							
							<div class="btn_area">
								<a href="javascript:;history.back();" class="btn_common_gray btn_cancel">취소</a>
								<button type="submit" class="btn_common_red btn_next_step">다음단계</button>
							</div>
						</fieldset>
					</form>					
				</div>
			</section>
		</div>
		<!-- //container -->

        <!-- footer -->
		<? require_once "common_footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->

<script>
	
</script>
</body>
</html>