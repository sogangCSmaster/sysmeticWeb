<!doctype html>
<html lang="ko">
<head>
    <title>포트폴리오 | SYSMETIC</title>
    <? require_once $skinDir."common/head.php" ?>
    <link rel="stylesheet" href="/css/owl.carousel.css">
    <script src="/script/owl.carousel.js"></script>
    <script src="/script/jquery.donut.js"></script>
    <!-- <script src="http://code.highcharts.com/highcharts.js"></script> -->
	<script src="http://code.highcharts.com/stock/highstock.js"></script>
    <script>
    $(function() {
        var getContent = function(page) {
            $.ajax({
                method: 'get',
                data: $('#stgFrm').serialize(),
                url: '/strategies/list',
                dataType: 'html',
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

        getContent(1);


        $('.make_step button').on('click', function() {
            var live = $(this).parent('li').hasClass('on');
            if (!live) return;

            var step = $(this).data('step');

            switch (step)
            {
            case 1:
                if (!$.trim($('#name').val())) {
                    $('#name').focus();
                    alert('포트폴리오 이름을 입력하세요');
                    return;
                } else {
                    $(this).parent('li').removeClass('on');
                    $(this).parent('li').next('li').addClass('on');
                }
            break;
            
            case 2:
                if (!$.trim($('#amount').val())) {
                    $('#amount').focus();
                    alert('초기 자본금을 입력하세요');
                    return;
                } else {
                    $(this).parent('li').removeClass('on');
                    $(this).parent('li').next('li').addClass('on');
                    $('.btn_apply').removeAttr('disabled');
                }
            break;

            case 3:
            break;

            case 4:
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
                    $('#open').val(open);
                    $.ajax({
                        method: 'post',
                        data: $('#regFrm').serialize(),
                        url: '/investment/portfolios/write',
                        dataType: 'json',
                    }).done(function(data) {
                        if (data.result) {
                            location.href="/investment/portfolios/complete?portfolio_id=" + data.portfolio_id;
                        } else {
                            alert(data.msg);
                        }
                    });
                }
            break;
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
                            loop:false,
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
                        $('.make_items .default_wrap').remove();
                        $('.make_items .detail').html(html);

                        $('.make_items .list div.chart_area').each(function(){
                            if ($(this).data('role') == 'strategy_graph_s' && !$(this).data('loaded')) {
                                loadGraph($(this).attr('id'));
                            }
                        });

                        $('.make_items .list ul').owlCarousel({
                            loop:false,
                            items:5,
                            margin:20,
                            nav:true,
                            navText: ["<img src='/images/sub/btn_prev_small.gif'>","<img src='/images/sub/btn_next_small.gif'>"]
                        });

                        $('.btn_apply').removeAttr('disabled');
                        $('.make_step li').removeClass('on');
                        $('.make_step li').last().addClass('on');
                    });

                }
            }
        });

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

	//다시수정
	function txtEdit(x){
		var class_name = $('#setp0'+x).attr('class');
		if(class_name != "no"){
			$("#setp0"+x).addClass('on');
		}
	}
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
				<p class="page_title"><img src="/images/sub/txt_page_title_make_portfolio.gif" alt="상품 포트폴리오 만들기" /></p>
				<div class="make_step">
					<ol>
						<!-- 해당하는 스텝의 li에 'on'클래스 추가  -->
						<li class="on" id="setp01">
							<strong><span>01.</span> 포트폴리오 이름</strong>
							<div class="input_box">
								<input type="text" id="name" name="name" placeholder="이름을 입력해주세요." />
							</div>
							<button type="button" class="btn_default" data-step="1">저장</button>
							<div class="mask" onclick="txtEdit(1)"></div>
						</li>
						<li  id="setp02">
							<strong><span>02.</span> 초기 자본금</strong>
							<div class="input_box">
								<input type="text" id="amount" name="amount" placeholder="초기자본금을 입력해주세요." onkeyup="inputNumberFormat(this)" />
							</div>
							<button type="button" class="btn_default" data-step="2">저장</button>
							<div class="mask"  onclick="txtEdit(2)"></div>
						</li>
						<li>
							<strong><span>03.</span> 상품선택</strong>
							<p class="txt_guide">
								하단 좌측에서 상품 선택 후<br />
								포트폴리오 반영 버튼을 <br />
								클릭 해 주세요. 
							</p>
							<div class="mask"></div>
						</li>
						<li>
							<strong><span>04.</span> 포트폴리오 저장</strong>
							<div class="agree">
								<input type="checkbox" id="agree" name="agree" />
								<label for="agree">포트폴리오 공개 동의</label>
							</div>
							<button type="button" class="btn_default" data-step="4">포트폴리오 저장</button>
							<div class="mask"></div>
						</li>
					</ol>
				</div>
				<div class="make_items">
					<div class="product_list">
                        <form id="stgFrm">
                        <input type="hidden" name="list_type" value="make_portfolio" />
                        <input type="hidden" id="strategies" name="strategies" value="<?=$strategies?>" />
                        <input type="hidden" name="count" value="" />
						<div class="search">
							<div class="input_box">
								<input type="text" id="title" name="title" placeholder="상품검색" />
								<button type="button" class="btn_search" title="검색"></button>
							</div>
							<div class="help">
								<span><a href="/포트폴리오전략별특징.pdf" target="_blank"><img src="/images/sub/ico_small_help.gif" alt="포트폴리오 상품별 특징" title="포트폴리오 상품별 특징" /></a></span>
							</div>
						</div>
						<div class="sc_area">
							<ul id='stg_list'>
							</ul>
						</div>
                        </form>
						<button type="button" class="btn_apply" disabled>포트폴리오 반영</button>
					</div>
					<div class="item_list">
						<div class="default_wrap">
							<img src="/images/sub/ico_point.png" alt="!">
							<p class="txt_guide">
								좌측에 포트폴리오에 넣을 상품을 선택해주세요.
							</p>
						</div>

						<div class="detail">
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
