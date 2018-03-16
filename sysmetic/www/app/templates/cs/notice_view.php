<!doctype html>
<html lang="ko">
<head>
	<title>고객센터 | SYSMETIC</title>
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


			<section class="area cs_w">
				<div class="cont_a">
                
                    <? require_once $skinDir."cs/sub_menu.php" ?>

					<div class="content cs_common_detail">
						<div class="head">
							<div class="info">
								<p class="subject"><?=$info['subject']?></p>
								<span class="time"><?=$info['reg_at']?></span>
							</div>
						</div>
						<div class="cont">
                            <? if (count($imgs)) { ?>
							<div class="picture">
                                <?
                                foreach ($imgs as $v) {
                                ?>
								<img src="/notice/<?=$v['save_name']?>" alt="<?=$v['file_name']?>" /><br />
                                <?
                                }
                                ?>
							</div>
                            <? } ?>
							<p>
								<?=$info['contents']?>
							</p>

                            <? if (count($files)) { ?>
							<div class="file">
								<dl>
									<dt>첨부파일</dt>
									<dd>
										<ul>
                                            <?
                                            foreach ($files as $v) {
                                            ?>
											<li>
												<?=$v['file_name']?>
												<a href="/cs/download/notice/<?=$v['fid']?>"><img src="/images/sub/btn_file_download.gif" alt="다운로드" /></a>
											</li>
                                            <?
                                            }
                                            ?>
										</ul>
									</dd>
								</dl>
							</div>
                            <? } ?>
						</div>
						<div class="btn_area">
							<a href="/cs/notice" class="btn list">목록</a>
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
