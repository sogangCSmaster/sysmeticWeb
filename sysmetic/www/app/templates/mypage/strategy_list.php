<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
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

        $('.list_body').on('click', 'button', function(){
            switch ($(this).data('role')) {
                case 'manage':
                    var strategy_id = $(this).data('strategy-id');
                    location.href='/mypage/strategies/' + strategy_id + '/analysis';
                break;

                case 'delete':
                    if (!confirm('삭제하시겠습니까?')) {
                        return false;
                    }

                    var btn_el = $(this);
                    var strategy_id = $(this).data('strategy-id');
                    $.get('/strategies/delete', {type:'json', id:strategy_id}, function(data){
                        if (data.result) {
                            $(btn_el).closest('li').remove();
                            $('.list_info .cnt').text($('.list_info .cnt').text() -1 );
                        } else {

                        }
                    }, 'json');
                break;
            }

            return false;
        });

        getContent(1);
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
                    
                    <div class="content my_products">
                    
                        <form id="searchFrm">
                        <input type="hidden" id="page" name="page" value="" />
                        <input type="hidden" name="search_type" value="mypage" />
                        </form>

						<div class="list_info">
                            <p class="info"><strong class="cnt"><?=number_format($cnt)?></strong>개의 나의 상품<p>
							<a href="/fund/strategies/write" class="btn_product_regist">+ 상품 등록</a>
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
        <? require_once $skinDir."common/footer.php" ?>
        <!-- //footer -->
    </div>
    <!-- //wrapper -->

</body>
</html>
