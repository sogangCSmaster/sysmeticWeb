<!doctype html>
<html lang="ko">
<head>
	<title>고객센터 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
	<script src="/js/masonry.pkgd.js"></script>

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

					<div class="content partners">
						<ul>
                            <?
							$a=0;
                            foreach ($brokers as $broker) {
                            ?>
							<li <?if($a==0){?>class="left"<?}?>>
								<div class="row_top">
									<div class="logo">
										<img src="<?=$broker['logo']?>" alt="" />
									</div>
									<a href="<?=($broker['url2']) ? $broker['url2'] : $broker['url']; ?>" target="_blank">계좌 개설하기</a>
								</div>
								<div class="row_bottom">
									<table>
										<colgroup>
											<col style="width:90px;" />
											<col style="width:230px;" />
										</colgroup>
										<tbody>
											<tr>
												<th>국내상품</th>
												<td><?=$broker['domestic']?></td>
											</tr>
											<tr>
												<th>해외상품</th>
												<td><?=$broker['overseas']?></td>
											</tr>
											<tr>
												<th>매매툴</th>
												<td>
													<? foreach ($broker['system_trading_tools'] as $v) { ?>
														<? if ($v['logo']) { ?>
														<img src="<?=$v['logo']?>" alt="<?=$v['name']?>" />
														<? } else {?>
														<?=$v['name']?>
														<? } ?>
													<? } ?>
												</td>
											</tr>
											<tr>
												<th>API</th>
												<td>
													<? foreach ($broker['api_tools'] as $v) { ?>
														<? if ($v['logo']) { ?>
														<img src="<?=$v['logo']?>" alt="<?=$v['name']?>" />
														<? } else {?>
														<?=$v['name']?>
														<? } ?>
													<? } ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</li>
                            <?
								$a++;
								if($a==3)$a=0;
                            }
                            ?>
						</ul>
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

<script>
	//list layout
	$('.partners ul').masonry({
		itemSelector: '.partners li',
		gutter: 10
	});

	//custom selectbox
    var select = $('select');
    for(var i = 0; i < select.length; i++){
        var idxData = select.eq(i).children('option:selected').text();
        select.eq(i).siblings('label').text(idxData);
    }
    select.change(function(){
        var select_name = $(this).children("option:selected").text();
        $(this).siblings("label").text(select_name);
    });
</script>
</html>
