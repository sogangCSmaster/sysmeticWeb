<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <link rel="stylesheet" href="/css/owl.carousel.css">
    <script src="/script/owl.carousel.js"></script>
    <script src="/script/jquery.donut.js"></script>
	<script src="http://code.highcharts.com/stock/highstock.js"></script>

    <script>
    $(function() {
        var getContent = function(page) {
            $.ajax({
                method: 'get',
                data: $('#stgFrm').serialize(),
                url: '/strategies/list',
                dataType: 'html'
            }).done(function(html) {
                $('#stg_list').html(html);

                $('#stg_list div').each(function(){
                    if ($(this).data('role') == 'strategy_graph' && !$(this).data('loaded')) {
                        loadGraph($(this).attr('id'));
                    }
                });

                $('#strategies').val('');
            });
        }


        $('.modify').on('click', function() {
            var percent = 0;
            $('.list input[name^="percents"]').each(function() {
                percent += parseInt($(this).val());
            });
            
            if (percent < 100 || percent % 100 != 0) {
                alert('비율의 합은 100이 되어야 합니다');
                return false;
            }

            if (!confirm('저장하시겠습니까?')) {
                return false;
            } else {
                var open = $('#agree').is(':checked') ? '1' : '0';
                var amount = $('#amount').val();
                var name = $('#name').val();
                $('#regFrm input[name="open"]').val(open);
                $('#regFrm input[name="amount"]').val(amount);
                $('#regFrm input[name="name"]').val(name);
                $.ajax({
                    method: 'post',
                    data: $('#regFrm').serialize(),
                    url: '/mypage/portfolios/<?=$portfolio['portfolio_id']?>/edit',
                    dataType: 'json',
                }).done(function(data) {
                    if (data.result) {
                        alert('수정되었습니다');
                        location.reload();
                    } else {
                        alert(data.msg);
                    }
                });
            }
        });

        $('.btn_search').on('click', function() {
            if (!$('#title').val()) {
                alert('검색어를 입력해주세요');
                return false;
            }

            getContent(1);
        });


        $('.btn_apply').on('click', function() {
            var len = $('#stg_list input:checkbox:checked').length;
            if (!len) {
                alert('전략을 선택해주세요');
                return;
            } else if (len > 10) {
                alert("포트폴리오 구성 시 전략은\n10개까지 추가할 수 있습니다.");
                return;
            } else {
                
                if ($('#regFrm').length) {
                    $('#stg_list input:checkbox:checked').each(function() {
                        if (!$('#strategy_'+$(this).val()).length) {
                            $('#regFrm').append('<input type="hidden" name="strategy_ids[]" value="'+ $(this).val() + '" />');
                            $('#regFrm').append('<input type="hidden" name="percents[]" value="100" />');
                            $('#regFrm').append('<input type="hidden" name="exchange[]" value="' + $('#exchange_strategy_' + $(this).val()).val() + '" />');
                        }
                    });

                    $.ajax({
                        method: 'post',
                        data: $('#regFrm').serialize(),
                        url: '/investment/portfolios/make',
                        dataType: 'html',
                    }).done(function(html) {
                        $('.item_list .detail').html(html);
                        $('.item_list .list div.chart_area').each(function(){
                            if ($(this).data('role') == 'strategy_graph_s') {

                                loadGraph($(this).attr('id'));
                            }
                        });

                        $('.item_list .list ul').owlCarousel({
                            loop:true,
                            items:5,
                            margin:20,
                            nav:true,
                            navText: ["<img src='/images/sub/btn_prev_small.gif'>","<img src='/images/sub/btn_next_small.gif'>"]
                        });

                    });

                } else {

                    $.ajax({
                        method: 'post',
                        data: $('#stgFrm').serialize()+'&amount='+$('#amount').val()+'&name='+$('#name').val(),
                        url: '/investment/portfolios/make',
                        dataType: 'html',
                    }).done(function(html) {
                        $('.item_list .detail').html(html);
                        $('.item_list .list div.chart_area').each(function(){
                            if ($(this).data('role') == 'strategy_graph_s' && !$(this).data('loaded')) {
                                loadGraph($(this).attr('id'));
                            }
                        });

                        $('.item_list .list ul').owlCarousel({
                            loop:true,
                            items:5,
                            margin:20,
                            nav:true,
                            navText: ["<img src='/images/sub/btn_prev_small.gif'>","<img src='/images/sub/btn_next_small.gif'>"]
                        });

                    });

                }
            }
        });


        getContent(1);

        $.ajax({
            method: 'post',
            data: {portfolio_id: '<?=$portfolio['portfolio_id']?>'},
            url: '/investment/portfolios/make',
            dataType: 'html',
            async: false
        }).done(function(html) {
            $('.item_list .detail').html(html);
            $('.item_list .list div.chart_area').each(function(){
                if ($(this).data('role') == 'strategy_graph_s' && !$(this).data('loaded')) {
                    loadGraph($(this).attr('id'));
                }
            });

            $('.item_list .list ul').owlCarousel({
                loop:true,
                items:5,
                margin:20,
                nav:true,
                navText: ["<img src='/images/sub/btn_prev_small.gif'>","<img src='/images/sub/btn_next_small.gif'>"]
            });

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
                    

					<div class="content my_portfolios">
						<div class="detail_head">
							<div class="form_box">
								<div class="row">
									<label for="" class="title">포트폴리오 이름</label>
									<div class="input_box">
										<input type="text" id="name" name="name" value="<?=$portfolio['name']?>" />
									</div>
								</div>
								<div class="row">
									<label for="" class="title">초기 자본금</label>
									<div class="input_box">
										<input type="text" id="amount" name="amount" value="<?=number_format($portfolio['amount'])?>" onkeyup="inputNumberFormat(this)" />
									</div>
								</div>
								<div class="row">
									<label for="" class="title">공개 동의</label>
									<div class="agree">
										<input type="checkbox" id="agree" name="is_open" <?=($portfolio['is_open']) ? 'checked' : ''?> />
										<label for="agree">포트폴리오 공개 동의</label>
									</div>
								</div>								
							</div>
							<div class="btns">
								<a href="javascript:;" class="btn modify">포트폴리오<br />변경</a>
								<a href="/mypage/portfolios" class="btn list">목록으로</a>
							</div>
						</div>

						<div class="detail_a">
                            <div class="product_list">
                                <form id="stgFrm">
                                <input type="hidden" name="list_type" value="make_portfolio" />
                                <input type="hidden" name="count" value="" />
                                <input type="hidden" name="start_date" value="<?=$portfolio['start_date']?>" />
                                <input type="hidden" name="end_date" value="<?=$portfolio['end_date']?>" />

                                <div class="search">
                                    <div class="input_box">
                                        <input type="text" id="title" name="title" placeholder="상품검색" />
                                        <button type="button" class="btn_search" title="검색"></button>
                                    </div>
                                    <div class="help">
                                        <span><img src="/images/sub/ico_small_help.gif" alt="포트폴리오 상품별 특징" title="포트폴리오 상품별 특징" /></span>
                                    </div>
                                </div>
                                <div class="sc_area">
                                    <ul id='stg_list'>
                                    </ul>
                                </div>
                                </form>
                                <button type="button" class="btn_apply">포트폴리오 반영</button>
                            </div>

                            <div class="item_list">
                                <div class="detail">
                                </div>
                            </div>
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


<!-- 레이어팝업 : 안내(환율정보 입력) -->
<article class="layer_popup input_exchange">
	<div class="dim" onclick="commonLayerClose('input_exchange')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('input_exchange')"></button>
		</div>
		<div class="cont">
			<div class="summary">
				<p class="txt_info">해외상품을 선택하셨습니다.</p>
				<p class="txt_guide">
					적용할 환율을 입력해주세요.
				</p>
			</div>
			<div class="input_box">
                <input type="hidden" id="sid" name="sid" value="" />
				<input type="text" id="exchange" name="exchange" />
			</div>
			<div class="btn_area">
				<button type="button" class="btn_common_gray" onclick="setExchange();">적용</button>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 안내(환율정보 입력) -->
<script>
	//product list swipe
	$('.detail_a .list ul').owlCarousel({
		loop:true,
		items:5,
		margin:20,
		nav:true,
		navText: ["<img src='../images/sub/btn_prev_small.gif'>","<img src='../images/sub/btn_next_small.gif'>"]
	});
	//$('.make_items .list ul').data('owlCarousel').destroy();

	//percent control
	$('.per_stat .btn').on('click', function(){
		var resultPer = 0;
		var perStat = parseInt($(this).siblings('.per').children('em').text());
		if($(this).hasClass('minus')){
			if(perStat > 0){
				resultPer = perStat - 10;
			}
		}else{
			resultPer = perStat + 10;
		}
		$(this).siblings('.per').children('em').text(resultPer);
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


    function chkV(sid, v) {
        if (v == 1 && !$('#check'+sid).is(":checked")) {
            $('#sid').val(sid);
            $('#exchange').val($('#exchange_strategy_'+sid).val());
            commonLayerOpen('input_exchange');
            return;
        }
    }

    function setExchange() {
        var sid = $('#sid').val();
        var exchange = $('#exchange').val();
        $('#exchange_strategy_'+sid).val(exchange);

        commonLayerClose('input_exchange');
    }

</script>
</body>
</html>
