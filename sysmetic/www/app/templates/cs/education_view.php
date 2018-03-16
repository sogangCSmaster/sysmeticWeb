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
								<p class="subject"><strong class="t_category">[<?=($info['type']=='ON') ? '온라인' : '오프라인'; ?>]</strong> <?=$info['subject']?></p>
								<span class="time"><?=$info['reg_date']?></span>
							</div>
						</div>
						<div class="cont  no_pd_t">
                            <?
                            if ($info['type'] == 'OFF') {
                            
                                $now = date('Y-m-d H:i:s');
                            ?>
                            <div class="edu_info">
								<dl>
									<dt>교육기간</dt>
									<dd>
										<?=substr($info['e_start_date'], 0, 16)?> ~ <?=substr($info['e_end_date'], 0, 16)?> 
                                        <?
                                        if ($now > $info['e_end_date']) {
                                            $status = '<span class="status end">교육종료</span>';
                                        } else if ($now > $info['e_start_date'] && $now < $info['e_end_date']) {
                                            $status = '<span class="status ing">교육중</span>';
                                        } else {
                                            $status = '<span class="status deadline">교육대기</span>';
                                        }
                                        echo $status;
                                        ?>
									</dd>
								</dl>
								<dl>
									<dt>신청기간</dt>
									<dd>
										<?=substr($info['a_start_date'], 0, 16)?> ~ <?=substr($info['a_end_date'], 0, 16)?>
                                        <?
                                        if ($now > $info['a_end_date']) {
                                            $status = '<span class="status end">신청마감</span>';
                                        } else if ($now > $info['a_start_date'] && $now < $info['a_end_date']) {
                                            $status = '<span class="status ing">신청중</span>';
                                        } else {
                                            $status = '<span class="status deadline">신청대기</span>';
                                        }
                                        echo $status;
                                        ?>
									</dd>
								</dl>
							</div>
                            <? } ?>

                            <? if (count($imgs)) { ?>
							<div class="picture">
                                <?
                                foreach ($imgs as $v) {
                                ?>
								<img src="/education/<?=$v['save_name']?>" alt="<?=$v['file_name']?>" /><br />
                                <?
                                }
                                ?>
							</div>
                            <? } ?>
							<p>
								<?=nl2br($info['contents'])?>
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
												<a href="/cs/download/education/<?=$v['fid']?>"><img src="/images/sub/btn_file_download.gif" alt="다운로드" /></a>
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
							<a href="/cs/education?type=<?=$info['type']?>" class="btn list">목록</a>
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
