<!doctype html>
<html lang="ko">
<head>
    <title>상품검색 | SYSMETIC</title>
    <? require_once $skinDir."common/head.php" ?>
    <script src="http://code.highcharts.com/highcharts.js"></script>
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

            <section class="area in_snb product_search">

                <div class="product_search_wrap">
                    <div class="side">
                        <form id="searchFrm" name="searchFrm" action="/strategies/list" method="get">
                        <input type="hidden" id="list_type" name="list_type" value="search" />
                        <input type="hidden" id="page" name="page" value="" />
                        <input type="hidden" id="search_type" name="search_type" value="item" />
                        <nav class="category">
                            <ul>
                                <li class="curr" data-role="item"><a href="javascript:;">항목별</a></li>
                                <li data-role="algorithm"><a href="javascript:;">알고리즘별</a></li>
                            </ul>
                        </nav>
                        <div class="group item show">
                            <ul>
                                <li>
                                    <a href="javascript:;">운용 방식</a>
                                    <div class="hide_box">
                                        <ul class="check_list">
                                            <?php foreach($types as $k => $type){ ?>
                                            <li>
                                                <input type="checkbox"  name="search_strategy_type[]" id="search_strategy_type<?=$k?>" value="<?=$type['type_id']?>" />
                                                <label for="search_strategy_type<?=$k?>"><?=$type['name']?></label>
                                            </li>
                                            <? } ?>
                                            <!--li>
                                                <input type="checkbox" name="search_strategy_type[]" id="search_strategy_type2" value="S" />
                                                <label for="search_strategy_type2">System Trading</label>
                                            </li>
                                            <li class="two">
                                                <input type="checkbox" name="search_strategy_type[]" id="search_strategy_type3" value="H" />
                                                <label for="search_strategy_type3">Hybrid Trading<br />(Manual + System)</label>
                                            </li-->
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">상품 종류</a>
                                    <div class="hide_box">
                                        <ul class="check_list">
                                            <?php foreach($kinds as $kind){ ?>
                                            <li>
                                                <input type="checkbox" name="search_kind[]" id="search_kind<?php echo $kind['kind_id'] ?>" value="<?php echo $kind['kind_id'] ?>" /><label for="search_kind<?php echo $kind['kind_id'] ?>"><?php echo htmlspecialchars($kind['name']) ?></label>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">운용 주기</a>
                                    <div class="hide_box">
                                        <ul class="check_list">
                                            <li>
                                                <input type="checkbox" name="search_term[]" id="search_term1" value="day" /><label for="search_term1">데이</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="search_term[]" id="search_term2" value="position" /><label for="search_term2">포지션</label>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">운용 종목</a>
                                    <div class="hide_box">
                                        <ul class="check_list">
                                            <?php foreach($items as $item){ ?>
                                            <li>
                                                <input type="checkbox" name="search_item[]" id="search_item<?php echo $item['item_id'] ?>" value="<?php echo $item['item_id'] ?>" /><label for="search_item<?php echo $item['item_id'] ?>"><?php echo htmlspecialchars($item['name']) ?></label>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">중개사</a>
                                    <div class="hide_box">
                                        <ul class="check_list">
                                            <?php foreach($brokers as $broker){ ?>
                                            <li>
                                                <input type="checkbox" name="search_broker[]" id="search_broker<?php echo htmlspecialchars($broker['broker_id']) ?>" value="<?php echo htmlspecialchars($broker['broker_id']) ?>" /><label for="search_broker<?php echo htmlspecialchars($broker['broker_id']) ?>"><?php echo htmlspecialchars($broker['company']) ?></label>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">기간</a>
                                    <div class="hide_box">
                                        <ul class="check_list">
                                            <li>
                                                <input type="checkbox" id="check_item0601" name="search_term[]" value="1" />
                                                <label for="check_item0601">1년 이하</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="check_item0602" name="search_term[]" value="2" />
                                                <label for="check_item0602">1년 ~ 2년</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="check_item0603" name="search_term[]" value="3" />
                                                <label for="check_item0603">3년 이상</label>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">수익률</a>
                                    <div class="hide_box">
                                        <div class="custom_selectbox">
                                            <label for="search_profit_type">수익률</label>
                                            <select id="search_profit_type" name="search_profit_type">
                                                <option value="total_profit_rate" selected="selected">누적 수익률</option>
                                                <option value="yearly_profit_rate">년간 수익률</option>
                                            </select>
                                        </div>
                                        <ul class="check_list">
                                            <li>
                                                <input type="checkbox" id="search_profit_rate1" name="search_profit_rate[]" value="10" />
                                                <label for="search_profit_rate1">10% 이하</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="search_profit_rate2" name="search_profit_rate[]" value="30" />
                                                <label for="search_profit_rate2">10% ~ 30%</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="search_profit_rate3" name="search_profit_rate[]" value="50" />
                                                <label for="search_profit_rate3">30% ~ 50%</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="search_profit_rate4" name="search_profit_rate[]" value="100" />
                                                <label for="search_profit_rate4">50% ~ 100%</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="search_profit_rate5" name="search_profit_rate[]" value="1000" />
                                                <label for="search_profit_rate5">100% 이상</label>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">투자원금</a>
                                    <div class="hide_box">
                                        <div class="min_max">
                                            <div class="input_box">
                                                <input name="search_principal_min" type="text" value="" placeholder="최소" onkeyup="inputOnlyNumber(this)" />
                                            </div>
                                            <span class="dash" style='width:40px'>억 ~</span>
                                            <div class="input_box">
                                                <input name="search_principal_max" type="text" value="" placeholder="최대" onkeyup="inputOnlyNumber(this)" />
                                            </div>
                                            <span class="dash">억</span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">MDD</a>
                                    <div class="hide_box">
                                        <div class="min_max">
                                            <div class="input_box">
                                                <input name="search_mdd_min" type="text" value="" placeholder="최소" onkeyup="inputOnlyNumber(this)" />
                                            </div>
                                            <span class="dash" style='width:40px'>% ~</span>
                                            <div class="input_box">
                                                <input name="search_mdd_max" type="text" value="" placeholder="최대" onkeyup="inputOnlyNumber(this)" />
                                            </div>
                                            <span class="dash">%</span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">SM Score</a>
                                    <div class="hide_box">
                                        <div class="min_max">
                                            <div class="input_box">
                                                <input name="search_sharp_ratio_min" id="search_sharp_ratio_min" type="text" value="" placeholder="최소" onkeyup="inputOnlyNumber(this)" />
                                            </div>
                                            <span class="dash">~</span>
                                            <div class="input_box">
                                                <input name="search_sharp_ratio_max" id="search_sharp_ratio_max" type="text" value="" placeholder="최대" onkeyup="inputOnlyNumber(this)" />
                                            </div>
                                        </div>
                                    </div>
                                </li>
								<li>
                                    <a href="javascript:;">키워드</a>
                                    <div class="hide_box">
                                        <div class="min_max">
                                               <input name="search_keyword" type="text" value="" placeholder="" style="width:100%;height:30px;" />
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="group algorithm">
                            <ul class="check_list">
                                <li>
                                    <input type="radio" id="algorism01" name="algorism" value="1" /> 
                                    <label for="algorism01">효율형 전략</label>
                                </li>
                                <li>
                                    <input type="radio" id="algorism02" name="algorism" value="2" />
                                    <label for="algorism02">공격형 전략</label>
                                </li>
                                <li>
                                    <input type="radio" id="algorism03" name="algorism" value="3" />
                                    <label for="algorism03">방어형 전략</label>
                                </li>
                            </ul>
                        </div>
                        <a href="javascript:;searchList(1);" class="btn_search">
                            <img src="../images/sub/ico_side_search.gif" alt="" />검색
                        </a>
                    </div>
                    </form>

                    <div class="result">
                        <!-- 검색 전 출력 -->
                        <div class="default_wrap intro">
                            <img src="../images/sub/ico_point.png" alt="!" />
                            <p class="txt_guide">
                                좌측에 있는 <strong class="mark">상품검색 옵션을 선택하신후 검색버튼</strong>을 누르시면<br />
                                상품을 검색하실 수 있습니다.
                            </p>
							<p class="txt_how" style="display:none" >알고리즘 산출 방식이 <a href="#" onclick="window.open('/알고리즘.jpg','','width=570, height=400')">궁금하신가요?</a></p>
                        </div>

                       <!-- 검색결과 출력 -->
                        <div class="list_wrap">
                            <div class="list_header" style="display:none">
                                <strong class="row_tit" style="width:480px;">상품</strong>
                                <strong class="row_tit" style="width:126px;">그래프</strong>
                                <strong class="row_tit" style="width:140px;">수익률</strong>
                            </div>
                            <ul class="list_body">
                            </ul>
                            
                            <a href="javascript:;" class="btn_make_portfolio" style="display:none">선택한 전략으로 포트폴리오 만들기</a>
                        </div>
                     </div>
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
$(function() {

    //side category...
    $('.product_search .side .category a').on('click', function(){
        var idx = $('.product_search .side .category a').index(this);
        $('.product_search .side .category li').removeClass('curr').eq(idx).addClass('curr');
        $('.product_search .side .group').removeClass('show').eq(idx).addClass('show');


        $('.result .default_wrap').show();
        $('.result .list_header').hide();
        if (idx == 1) {
            $('.result .intro .txt_how').show();
        } else {
            $('.result .intro .txt_how').hide();
        }
        $(".list_body").html('');
        $('.result .btn_make_portfolio').hide();
    });

    $('.product_search .side .group li a').on('click', function(){
        if($(this).closest('li').hasClass('show')){
            $(this).closest('li').removeClass('show');
        }else{
            $(this).closest('li').addClass('show');
        }
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

    $('#searchFrm').submit(function(){
        var search_type = $('.product_search .side .category li.curr').data('role');
        $('#search_type').val(search_type);
        var page = $('#page').val();

        if ($('#search_sharp_ratio_min').val() && $('#search_sharp_ratio_min').val() > 100) {
            alert('0부터 100까지 입력 가능합니다');
            $('#search_sharp_ratio_min').focus();
            return false;
        }

        if ($('#search_sharp_ratio_max').val() && $('#search_sharp_ratio_max').val() > 100) {
            alert('0부터 100까지 입력 가능합니다');
            $('#search_sharp_ratio_max').focus();
            return false;
        }

        $.get($(this).attr('action'), $(this).serialize(), function(data){
            if (data) {

                if (page == 1) {
                    $('html, body').animate({scrollTop : 0}, 400);
                    $('.result .default_wrap').hide();
                    $('.result .list_header').show();
                    $(".list_body").html('');
                }

                $(".list_body").append(data);
                if ($(".list_body li input:checkbox").length > 0) {
                    $('.result .btn_make_portfolio').show();
                } else {
                    $('.result .btn_make_portfolio').hide();
                }

                $('.list_body div').each(function(){
                    if ($(this).data('role') == 'strategy_graph' && !$(this).data('loaded')) {
                        loadGraph($(this).attr('id'));
                    }
                });

                $('.btn_list_more').on('click', function() {
                    $(this).remove();
                    page = parseInt(page) + 1;
                    searchList(page);
                });

            } else {
                alert('처리 중 요류가 발생하였습니다');
            }
        }, 'html');
        return false;
    });


    $('.btn_make_portfolio').on('click', function() {

        var len = $('.list_body input:checkbox:checked').length;
        if (!len) {
            alert('전략을 선택해주세요');
            return;
        } else if (len > 10) {
            alert("포트폴리오 구성 시 전략은\n10개까지 추가할 수 있습니다.");
            return;
        } else {
            var strategies = '';
            $('.list_body input:checkbox:checked').each(function(){
                strategies += '|' + $(this).val();
            });
            location.href= '/investment/portfolios/write?strategies=' + strategies;
        }
    });


    $('.algorithm .check_list input:checkbox').on('click', function() {
    });
});

function searchList(page) {
    $('#page').val(page);
    $('#searchFrm').submit();
}
</script>

</body>
</html>
