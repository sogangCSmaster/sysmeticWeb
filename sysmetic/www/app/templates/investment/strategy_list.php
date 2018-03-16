<!doctype html>
<html lang="ko">
<head>
    <title>전략랭킹 | SYSMETIC</title>
    <? require_once $skinDir."common/head.php" ?>
    <script src="http://code.highcharts.com/highcharts.js"></script>

    <script>
    $(function () {

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
                url: '/strategies/list',
                dataType: 'html',
            }).done(function(html) {
                $('.list_body').append(html);

                $('div').each(function(){
                    if ($(this).data('role') == 'strategy_graph' && !$(this).data('loaded')) {
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
                    getContent(loadPage);
                });
            });
        }

        getContent(page);

        var select = $('select');
        for(var i = 0; i < select.length; i++){
            var idxData = select.eq(i).children('option:selected').text();
            select.eq(i).siblings('label').text(idxData);
        }
        select.change(function(){
            var select_name = $(this).children("option:selected").text();
            $(this).siblings("label").text(select_name);

            $('.list_body').children().remove();
            getContent(1);
        });


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
        <? require_once $skinDir."common/header.php" ?>
        <!-- header -->

        <!-- container -->
        <div class="container">

            <? require_once $skinDir."investment/sub_menu.php" ?>


            <section class="area in_snb pb_trader_detail">

                <? if ($developer) { ?>
                <div class="info_area">
                    <div class="photo"><img src="<?=getProfileImg($developer['picture'])?>" alt="" /></div>
                    <div class="user_info">
                        <strong class="name"><?if($developer['nickname']){echo $developer['nickname'];}else{echo $developer['name'];}?></strong>
                        <? if ($developer['user_type']== 'P') { ?>
                        <a href="/lounge/<?=$developer['uid']?>" class="btn_coffee"><img src="/images/sub/ico_list_coffee.gif" alt="" /></a>
                        <span class="job pb">PB</span>
                        <? } else { ?>
                        <span class="job trader">트레이더</span>
                        <? } ?>
                    </div>
                    <div class="job_info">
					<? if ($developer['user_type'] == 'P') { ?>
                        <strong class="company"><?=$developer['company']?></strong>
                        <span class="address"><?=$developer['area2']?> <?=$developer['part']?></span>
                    <? } ?>
                    </div>
                </div>
                <div class="page_header">
                    <nav class="category">
                        <ul>
                            <li class="curr"><a href="javascript:;" class="black">상품 <strong class="cnt">(<?=number_format($developer['strategy_cnt'])?>)</strong></a></li>
                            <li><a href="/investment/portfolios?developer_uid=<?=$developer['uid']?>">포트폴리오 <strong class="cnt">(<?=number_format($developer['portfolio_cnt'])?>)</strong></a></li>
                        </ul>
                    </nav>
                </div>
                <form id="searchFrm" action="/investment/strategies" method="get">
                <input type="hidden" id="page" name="page" value="" />
                <input type="hidden" name="developer_uid" value="<?=$developer['uid']?>" />
                </form>
                <? } else { ?>

                <div class="page_header">
                <form id="searchFrm" action="/investment/strategies" method="get">
                <input type="hidden" id="page" name="page" value="" />
                <input type="hidden" name="developer_uid" value="<?=$developer['uid']?>" />
                    <div class="custom_selectbox">
                        <label for="">상품종류</label>
                        <select name="kind">
                            <option selected="selected" value="">상품종류</option>
                            <? foreach ($kinds as $kind) { ?>
                            <option value="<?=$kind['kind_id']?>" <?=($search['q_kind']==$kind['kind_id']) ? 'selected' : '';?>><?=htmlspecialchars($kind['name'])?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div class="custom_selectbox">
                        <label for="">종목 선택</label>
                        <select id="item" name="item">
                            <option selected="selected" value="">종목 선택</option>
                            <? foreach ($items as $item) { ?>
                            <option value="<?=$item['item_id']?>" <?=($search['q_item']==$item['item_id']) ? 'selected' : '';?>><?=htmlspecialchars($item['name'])?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div class="custom_selectbox">
                        <label for="">주기 선택</label>
                        <select name="term">
                            <option selected="selected" value="">주기 선택</option>
                            <option value="day" <?=($search['q_term'] == 'day') ? 'selected' : ''?>>데이</option>
                            <option value="position" <?=($search['q_term'] == 'position') ? 'selected' : ''?>>포지션</option>
                        </select>
                    </div>
                    <div class="custom_selectbox">
                        <label for="">정렬기준</label>
                        <select name="sort">
                            <option selected="selected" value="">정렬기준</option>
                            <option value="total_profit_rate" <?=($search['q_sort'] == 'total_profit_rate') ? 'selected' : ''?>>수익률</option>
                            <option value="mdd" <?=($search['q_sort'] == 'mdd') ? 'selected' : ''?>>MDD</option>
                            <option value="sharp_ratio" <?=($search['q_sort'] == 'sharp_ratio') ? 'selected' : ''?>>SM Score</option>
                            <option value="reg_at" <?=($search['q_sort'] == 'reg_at') ? 'selected' : ''?>>최근등록일</option>
                        </select>
                    </div>
                    <a href="/investment/search" class="btn_default"><img src="../images/sub/ico_detail_search.gif" alt="" />상세조건 검색</a>
                </form>
                </div>
                <?
                }
                ?>
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
            </section>
        </div>
        <!-- //container -->

        <!-- footer -->
        <? require_once $skinDir."common/footer.php" ?>
        <!-- // footer -->

    </div>
    <!-- //wrapper -->


<script>
</script>
</body>
</html>
