<!doctype html>
<html lang="ko">
<head>
	<title>Sysmetic Traders - 이용약관</title>
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
					<h2 class="page_title n_squere">이용 약관</h2>
					<p class="page_summary"></p>
				</div>
				<div class="content_area">
                    <!------ 본문 영역 ------->
                    <div id="wrap">
                        <div id="content" class="view">
                            <a href="/privacy" title="개인정보 취급방침 보기" class="btn_view rules2"><span class="ir">개인정보 취급방침 보기</span></a>
                            <?php require_once('agree_rules.php') ?>
                        </div>
                    </div>
                    <!------ //본문 영역 ------->
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