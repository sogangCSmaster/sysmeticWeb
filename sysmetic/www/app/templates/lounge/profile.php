<!doctype html>
<html lang="ko">
<head>
	<title>라운지 | SYSMETIC</title>
	<? include_once $skinDir."/common/head.php" ?>
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
					<div class="content profile">
						<dl class="first">
							<dt>경력</dt>
							<dd>
								<p>
									- 2006.03 ~ 현재 : 하나대투증권 서울 여의도 지점 PB<br />
									- 2003.01 ~ 2006.02 :
								</p>
							</dd>
						</dl>
						<dl>
							<dt>자격증</dt>
							<dd><p>- CFP(Certified FinancialPlanner,국제재무설계사) 자격증 </p></dd>
						</dl>
						<dl>
							<dt>자기소개</dt>
							<dd><p>책임감이 뛰어나고 성실해서 어쩌고 저쩌고</p> </dd>
						</dl>
						<dl>
							<dt>기타</dt>
							<dd><p>기타사항이 어쩌고 저쩌고</p></dd>
						</dl>
						<div class="btn_area">
							<a href="javascript:;" class="btn_manage">프로필 관리</a>
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
