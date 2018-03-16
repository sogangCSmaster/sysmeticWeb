<!doctype html>
<html lang="ko">
<head>
	<title>고객센터 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
    $(function () {
        var totalCnt = <?=$total?>;
        var type = '<?=$type?>';
        var page = 1;
        var getCounsel = function(loadPage) {
            $.ajax({
                mthod: 'get',
                data: {type: type, page: loadPage},
                url: '/cs/education/list',
                dataType: 'html',
            }).done(function(html) {
                <? if ($type == 'ON') { ?>
                $('.list_wrap ul').append(html);
                if ($('.list_wrap ul li').length < totalCnt)
                {
                    $('.edu_list .btn_list_more').show().on('click', function() {
                        $(this).hide();
                        page = page + 1;
                        getCounsel(page);
                    });
                }
                <? } else { ?>
                $('.list_tbl tbody').append(html);
                if ($('.list_tbl tbody tr').length < totalCnt)
                {
                    $('.edu_list .btn_list_more').show().on('click', function() {
                        $(this).hide();
                        page = page + 1;
                        getCounsel(page);
                    });
                }
                <? } ?>
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
					<div class="content edu_list">
						<div class="category">
							<ul>
								<li class="<?=($type!='OFF') ? 'curr' : '';?>"><a href="/cs/education">온라인</a></li>
								<li class="<?=($type=='OFF') ? 'curr' : '';?>"><a href="/cs/education?type=OFF">오프라인</a></li>
							</ul>
						</div>

                        <? if ($type == 'ON') { ?>
						<div class="list_wrap">
							<ul>
							</ul>
							<a href="javascript:;" class="btn_list_more" style="display:none">+ 더보기</a>
						</div>
                        <? } else { ?>

                        <table class="list_tbl">
							<colgroup>
								<col style="width:654px;">
								<col style="width:168px;">
								<col style="width:168px;">
							</colgroup>
							<thead>
								<tr>
									<th>제목</th>
									<th>교육기간</th>
									<th>신청기간</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<a href="javascript:;" class="btn_list_more" style="display:none">+ 더보기</a>

                        <? } ?>
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
