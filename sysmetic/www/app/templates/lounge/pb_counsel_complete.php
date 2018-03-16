<!doctype html>
<html lang="ko">
<head>
	<title>라운지 | SYSMETIC</title>
	<? include_once $skinDir."/common/head.php" ?>
    <script>
    </script>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once $skinDir."/common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container">
			<section class="area pb_detail">
				<div class="area">
					<div class="head">
                        <? include $skinDir."/lounge/pb_info.php" ?>
					</div>
					<div class="content counsel_complete">	
						<p class="txt_title">
							<img src="/images/sub/txt_counsel_complete.gif" alt="상담내용이 등록완료 되었습니다." />
						</p>
						<p class="txt_info">
							담당 PB가 내용 확인 후 희망하신 시간에 연락 드립니다. <br />
							상담내용은 <strong class="bold">마이페이지 &gt; <span class="mark">나의 상담</span></strong> 페이지에서 확인 가능합니다.
						</p>
						<div class="btn_area">
							<a href="/mypage/counsel" class="btn_common_gray btn_mypage">마이페이지 가기</a>
							<a href="/lounge" class="btn_common_red btn_main">Lounge 메인 가기</a>
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

</body>
</html>
