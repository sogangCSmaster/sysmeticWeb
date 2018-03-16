<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
    $(function () {
        var page = 1;
        var getCounsel = function(loadPage) {
            $.ajax({
                mthod: 'get',
                data: {page: loadPage},
                url: '/mypage/invest/list',
                dataType: 'html',
            }).done(function(html) {
                $('.list_tbl tbody').append(html);
                if ($('.list_tbl tbody tr').length < $('.info .cnt').text())
                {
                    $('.my_counsel .btn_list_more').show().on('click', function() {
                        $(this).hide();
                        page = page + 1;
                        getCounsel(page);
                    });
                }
            });
        }

        getCounsel(page);
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

			<section class="area mypage">	
				<div class="cont_a">

                <? require_once $skinDir."mypage/sub_menu.php" ?>

					<div class="content my_counsel">
						<div class="list_info">
							<p class="info">총 <strong class="cnt"><?=number_format($total)?></strong>개의 전략에 투자시작. <p>
						</div>
						<div class="list_wrap">
							<table class="list_tbl">
								<colgroup>
									<col style="width:52px;" />
									<col style="width:370px;" />
									<col style="width:162px;" />
									<col style="width:129px;" />
									<col style="width:174px;" />
									<col style="width:97px;" />
								</colgroup>
								<thead>
									<tr>
										<th style="border-right:none;">&nbsp;</th>
										<th style="border-left:none;">상품명</th>
										<th>투자가입금액</th>
										<th>투자개시시점</th>
										<th>최대손실 한도율 설정</th>
										<th>상태</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<a href="javascript:;" class="btn_list_more" style="display:none">+ 더보기</a>
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
