<!doctype html>
<html lang="ko">
<head>
	<title>title</title>
	<? require_once $skinDir."common/head.php" ?>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once $skinDir."common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container join">
			<section class="area">
				<div class="page_title_area">
					<h2 class="page_title n_squere">회원가입</h2>
					<p class="page_summary">시스메틱 홈페이지를 찾아주셔서 감사합니다. 약관에 동의하셔야 가입이 가능합니다.</p>
				</div>
				<div class="content_area step04">
					<div class="step_view">04.회원가입 완료</div>
					<div class="complete_info">
						<p class="txt_big n_squere">시스메틱의 회원가입이 완료되었습니다.</p>
                        <? if ($flash['user_type'] == 'N') { ?>
						<p class="txt_small">
							시스메틱과 함께 투자하는 전략들을 만나보세요.<br />
							좋은 전략을 Follow 하고, 직접 투자에도 참여하실 수 있습니다. 
						</p>
                        <? } else if ($flash['user_type'] == 'T' || $flash['user_type'] == 'P') { ?>
						<p class="txt_small">
							지금 바로 좋은 투자전략을 공유해 보세요.<br />
							투자전략 분석과 함께 투자자금 매칭서비스를 제공해 드립니다. 
						</p>

                        <? } ?>
                        <!-- <div class="guide">
                            <div class="summary">
                                <p class="txt_big n_squere">지금 바로 좋은 투자전략을 공유해 보세요</p>
                                <p class="txt_small n_squere">투자전략 분석과 함께 투자자금 매칭서비스를 제공해 드립니다.</p>
                            </div>
                        </div> -->
						<div class="btn_area">
							<a href="/" class="btn_common_gray btn_home">홈으로</a>
							<a href="/signin" class="btn_common_red btn_login">로그인하기</a>
						</div>	
					</div>
				</div>
			</section>
		</div>
		<!-- //container -->

        <!-- footer -->
		<? require_once $skinDir."common/footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->

</body>
</html> 