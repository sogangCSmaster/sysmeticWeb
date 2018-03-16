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

					<div class="content withdrawal">
						<div class="page_title_area">
							<h2 class="page_title n_squere">회원탈퇴</h2>
						</div>
						<div class="box">
							<p class="thanks n_squere">그동안 시스메틱과 함께 해주셔서 고맙습니다.</p>
							<div class="info">
								<dl>
									<dt>회원탈퇴를 하게 되면,</dt>
									<dd>관심 상품과 관심 포트폴리오의 모든 내용이 삭제됩니다.</dd>
									<dd>탈퇴 즉시 회원님의 모든 개인정보는 삭제됩니다. </dd>
									<dd>2주일간 동일한 이메일주소로 회원가입이 불가합니다.</dd>
								</dl>
							</div>
							<p class="question n_squere">탈퇴 하시겠습니까?</p>
							<div class="btn_area">
								<a href="/mypage/subscribe" class="btn gray">취소</a>
								<a href="javascript:;$('#frm').submit();" class="btn red">회원 탈퇴</a>
							</div>
                            <form id="frm" action="/mypage/withdraw" method="post">
                            </form>
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

</body>
</html>
