<!doctype html>
<html lang="ko">
<head>
	<title>고객센터 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
    $(function () {
        var totalCnt = <?=$total?>;
        var page = 1;
        var getCounsel = function(loadPage) {
            $.ajax({
                mthod: 'get',
                data: {page: loadPage},
                url: '/cs/media/list',
                dataType: 'html',
            }).done(function(html) {
                $('.list_wrap .list_tbl tbody').append(html);
                if ($('.list_wrap .list_tbl tbody tr').length < totalCnt)
                {
                    $('.cs_common_list .btn_list_more').show().on('click', function() {
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


			<section class="area cs_w">
				<div class="cont_a">
                
                    <? require_once $skinDir."cs/sub_menu.php" ?>

					<div class="content cs_common_list">
						<div class="list_wrap">
							<table class="list_tbl">
								<colgroup>
									<col style="width:48px;">
									<col style="width:774px;">
									<col style="width:168px;">
								</colgroup>
								<thead>
									<tr>
										<th style="border-right:none;">&nbsp;</th>
										<th style="border-left:none;">제목</th>
										<th>작성일</th>
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
