<!doctype html>
<html lang="ko">
<head>
    <title>포트폴리오 | SYSMETIC</title>
    <? require_once $skinDir."common/head.php" ?>
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script>
    $(function() {

        var page = 1;
        var loadPage = page;
        var isLoadingFinished = true;
        $(window).scroll(function(){
            if($(window).scrollTop() >= $(document).height() - $(window).height() - 100){
                if(!isLoadingFinished) {return;}
                
                $('.btn_list_more').remove();
                getContent(loadPage);
            }
        });

        var getContent = function(page) {

            isLoadingFinished = false;

            $('#page').val(page);
            $.ajax({
                mthod: 'get',
                data: $('#searchFrm').serialize(),
                url: '/portfolios/list',
                dataType: 'html',
            }).done(function(html) {
                $('.list_body').append(html);

                $('div').each(function(){
                    if ($(this).data('role') == 'portfolio_graph' && !$(this).data('loaded')) {
                        loadGraph($(this).attr('id'));
                    }
                });

                if ($('.endList').length) {
                    isLoadingFinished = false;
                } else {
                    isLoadingFinished = true;
                }
                loadPage = page + 1;

                $('.btn_list_more').on('click', function() {
                    $(this).remove();
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
                        $.get('/portfolios/follow/form', function(data){
                            content= data;
                            $('body').append(content);
                            follow_load = true;
                        });
                        $.ajaxSetup({ async:true });
                    }

                    $('.layer_popup .name').text($(this).data('portfolio-name'));
                    $('.layer_popup #portfolio_id').val($(this).data('portfolio-id'));
                    commonLayerOpen('strategy_follow');
                break;

                case 'unfollow':
                    var el = $(this);
                    var callback = function() {
                        el.attr('title', 'Follow').attr('class', 'btn_follow').data('role', 'followForm').html('Follow +');
                        $('#follows_count'+el.data('portfolio-id')).text(parseInt($('#follows_count'+el.data('portfolio-id')).text()) - 1);
                    };

                    unfollow('portfolios', $(this).data('portfolio-id'), callback);
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
        <? require_once $skinDir."common/header.php" ?>
        <!-- header -->
        <!-- container -->
        <div class="container">

            <? require_once $skinDir."investment/sub_menu.php" ?>

            <section class="area in_snb portfolio">
                <? if ($developer) { ?>
                <div class="info_area">
                    <div class="photo"><img src="<?=$developer['picture_s']?>" alt="" /></div>
                    <div class="user_info">
                        <strong class="name"><?=$developer['nickname']?></strong>
                        <? if ($developer['user_type']== 'P') { ?>
                        <a href="/lounge/<?=$developer['uid']?>" class="btn_coffee"><img src="/images/sub/ico_list_coffee.gif" alt="" /></a>
                        <span class="job pb">PB</span>
                        <? } else { ?>
                        <span class="job trader">트레이더</span>
                        <? } ?>
                    </div>
                    <div class="job_info">
                        <strong class="company"><?=$developer['company']?></strong>
                        <span class="address"><?=$developer['area2']?> <?=$developer['part']?></span>
                    </div>
                </div>
                <div class="page_header">
                    <nav class="category">
                        <ul>
                            <li><a href="/investment/strategies?developer_uid=<?=$developer['uid']?>" class="black">전략 <strong class="cnt">(<?=number_format($developer['strategy_cnt'])?>)</strong></a></li>
                            <li class="curr"><a href="javascript:;">포트폴리오 <strong class="cnt">(<?=number_format($developer['portfolio_cnt'])?>)</strong></a></li>
                        </ul>
                    </nav>
                </div>
                <form id="searchFrm">
                <input type="hidden" id="page" name="page" value="" />
                <input type="hidden" name="uid" value="<?=$developer['uid']?>" />
                <div class="custom_selectbox">
                </div>
                </form>
                <? } else { ?>

                <p class="page_title" style="margin-bottom:30px"><img src="/images/sub/txt_page_title_portfolio.gif" alt="포트폴리오 랭킹입니다." /></p>
                <div class="page_header">
                    <form id="searchFrm">
                    <input type="hidden" id="page" name="page" value="" />
                    <input type="hidden" id="sort" name="sort" value="total_profit_rate" />
                    <input type="hidden" name="uid" value="<?=$developer['uid']?>" />
                    <!-- <div class="custom_selectbox">
                        <label for="">상품종류</label>
                        <select>
                            <option selected="selected" value="">상품종류</option>
                            <option value="">상품종류2<nm/option>
                            <option value="">상품종류3</option>
                        </select>
                    </div> -->
                    </form>
                    <a href="/investment/portfolios/write" class="btn_default" id="makePortfolio">+ 포트폴리오 만들기</a>
                </div>
                <? } ?>
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
            </section>
        </div>
        <!-- //container -->

        <!-- footer -->
        <? require_once $skinDir."common/footer.php" ?>
        <!-- //footer -->
    </div>
    <!-- //wrapper -->


<script>
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
</body>
</html>
