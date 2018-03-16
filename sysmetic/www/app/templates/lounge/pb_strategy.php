<!doctype html>
<html lang="ko">
<head>
	<title>라운지 | SYSMETIC</title>
	<? include_once $skinDir."/common/head.php" ?>
    <script src="http://code.highcharts.com/highcharts.js"></script>

    <script>
    $(function () {
        var getContent = function(page) {
            $('#page').val(page);
            $.ajax({
                mthod: 'get',
                data: $('#searchFrm').serialize(),
                url: '/strategies/list',
                dataType: 'html',
            }).done(function(html) {
                $('.list_body').append(html);

                $('div').each(function(){
                    if ($(this).data('role') == 'strategy_graph' && !$(this).data('loaded')) {
                        loadGraph($(this).attr('id'));
                    }
                });

                $('.btn_list_more').on('click', function() {
                    $(this).remove();
                    page = page + 1;
                    getContent(page);
                });

            });
        }

        getContent(1);

        var follow_load = false;
        $('.list_body').on('click', 'button', function(){
            switch ($(this).data('role')) {
                case 'followForm':
                    if (follow_load == false) {
                        $.ajaxSetup({ async:false });
                        $.get('/strategies/follow/form', function(data){
                            content= data;
                            $('body').append(content);
                            follow_load = true;
                        });
                        $.ajaxSetup({ async:true });
                    }

                    $('.layer_popup .name').text($(this).data('strategy-name'));
                    $('.layer_popup #strategy_id').val($(this).data('strategy-id'));
                    commonLayerOpen('strategy_follow');
                break;

                case 'unfollow':
                    var el = $(this);
                    var callback = function() {
                        el.attr('title', 'Follow').attr('class', 'btn_follow').data('role', 'followForm').html('Follow +');
                        $('#follows_count'+el.data('strategy-id')).text(parseInt($('#follows_count'+el.data('strategy-id')).text()) - 1);
                    };

                    unfollow('strategies', $(this).data('strategy-id'), callback);
                break;

                case 'mine':
                    alert('자신의 상품은 follow 할 수 없습니다');
                break;

                case 'login':
                    login();
                break;
            }

            return false;
        });

    });
        
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
					<div class="content product">
                    
                        <form id="searchFrm" action="/investment/strategies" method="get">
                        <input type="hidden" id="page" name="page" value="" />
                        <input type="hidden" name="developer_uid" value="<?=$pb['uid']?>" />
                        <input type="hidden" name="search_type" value="mypage2" />
                        </form>

						<div class="head">
							<strong class="cnt">상품 <span class="mark"><?=number_format($total)?></span>개</strong>
							<? if ($mine) { ?>
							<a href="/mypage/strategies" class="btn_product_manage">상품 관리</a>
							<? } ?>
						</div>
						<div class="list_wrap">
							<div class="list_header">
								<strong class="row_tit" style="width:480px;">상품</strong>
								<strong class="row_tit" style="width:126px;">그래프</strong>
								<strong class="row_tit" style="width:129px;">수익률</strong>
								<strong class="row_tit" style="width:152px;">SM Score / MDD</strong>
								<strong class="row_tit" style="width:97px;">Follow</strong>
							</div>


		                    <ul class="list_body">
		                    </ul>

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
