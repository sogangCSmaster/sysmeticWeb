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
                url: '/mypage/customer/list',
                dataType: 'html',
            }).done(function(html) {
                $('.list_wrap ul').append(html);
                //alert($('.list_wrap ul li').length);
                if ($('.list_wrap ul li').length < $('.info .cnt').text())
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
							<p class="info">총 <strong class="cnt"><?=number_format($total)?></strong>개의 상담내역이있습니다. <p>
						</div>
						<div class="list_wrap pb">
							<ul>
							</ul>
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
