<!doctype html>
<html lang="ko">
<head>
    <title>포트폴리오 | SYSMETIC</title>
    <? require_once $skinDir."common/head.php" ?>
    <script>
    $(function() {

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

            <? require_once $skinDir."investment/sub_menu.php" ?>

			<section class="area in_snb portfolio">
				<p class="page_title"><img src="/images/sub/txt_page_title_make_portfolio.gif" alt="상품 포트폴리오 만들기" /></p>
				<div class="make_complete">
					<img src="/images/sub/ico_point.png" alt="!">
					<p class="txt_info">
						포트폴리오 저장이 완료되었습니다.<br />
						포트폴리오 상세보기 페이지로 이동하시겠습니까?
					</p>
					<p class="txt_guide">
						지금 상세보기 페이지로 가지 않아도 마이페이지 &gt; 나의 포트폴리오에서<br />
						저장된 포트폴리오를 확인할 수 있습니다.					
					</p>
					<a href="/investment/portfolios/<?=$portfolio_id?>" class="btn_detail_view">포트폴리오 상세보기</a>
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
