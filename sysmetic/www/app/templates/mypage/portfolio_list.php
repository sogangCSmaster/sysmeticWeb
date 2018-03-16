<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script src="http://code.highcharts.com/highcharts.js"></script>

    <script>
    $(function () {

        var getPortfolios = function(page) {
            $('#page').val(page);
            $.ajax({
                mthod: 'get',
                data: $('#searchFrm2').serialize(),
                url: '/portfolios/list',
                dataType: 'html',
            }).done(function(html) {
                $('.my_portfolios .list_body').append(html);

                $('div').each(function(){
                    if ($(this).data('role') == 'portfolio_graph' && !$(this).data('loaded')) {
                        loadGraph($(this).attr('id'));
                    }
                });

                $('.my_portfolios .btn_list_more').on('click', function() {
                    $(this).remove();
                    page = page + 1;
                    getPortfolios(page);
                });
            });
        }

        getPortfolios(1);


        $('.list_body').on('click', 'button', function(){
            switch ($(this).data('role')) {
                case 'manage':
                    var portfolio_id = $(this).data('portfolio-id');
                    location.href='/mypage/portfolios/' + portfolio_id;
                break;

                case 'delete':
                    if (!confirm('삭제하시겠습니까?')) {
                        return false;
                    }

                    var btn_el = $(this);
                    var portfolio_id = $(this).data('portfolio-id');
                    $.get('/portfolios/delete', {type:'json', id:portfolio_id}, function(data){
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
                    
                    <form id="searchFrm2">
                    <input type="hidden" id="page" name="page" value="" />
                    <input type="hidden" name="search_type" value="mypage" />
                    </form>

                    <div class="content my_portfolios">

                        <div class="list_info">
                            <p class="info"><strong class="cnt"><?=number_format($cnt)?></strong>개의 나의 포트폴리오<p>
                            <a href="/investment/portfolios/write" class="btn_make_portfolio">+ 포트폴리오 만들기</a>
                        </div>

                        <div class="list_wrap">
                            <div class="list_header">
                                <strong class="row_tit" style="width:540px;">포트폴리오 / 정보</strong>
                                <strong class="row_tit" style="width:143px;">그래프</strong>
                                <strong class="row_tit" style="width:205px;">수익률 / 기간 / MDD</strong>
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
