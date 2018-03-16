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
								<p><?=$profile['career']?></p>
							</dd>
						</dl>
						<dl>
							<dt>자격증</dt>
							<dd><p><?=$profile['license']?></p></dd>
						</dl>
						<dl>
							<dt>자기소개</dt>
							<dd><p><?=$profile['introduce']?></p></dd>
						</dl>
						<dl>
							<dt>기타</dt>
							<dd><p><?=$profile['etc']?></p></dd>
						</dl>
						<? if ($mine) { ?>
						<div class="btn_area">
							<a href="/lounge/<?=$pb['uid']?>/profile/write" class="btn_manage">프로필 관리</a>
						</div>
						<? } ?>
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
