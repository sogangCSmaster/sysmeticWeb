<!doctype html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta property="og:type" content='website' />
	<meta property="og:title" content="SYSMETIC">
	<meta property='og:site_name' content='SYSMETIC' />
	<meta property="og:title" content='SYSMETIC' />
	<meta property="og:description" content='SYSMETIC' />
	<title>SYSMETIC</title>
	<link rel="stylesheet" href="/css/reset.css">
	<link rel="stylesheet" href="/css/common.css">
	<link rel="stylesheet" href="/css/main.css">
	<link rel="stylesheet" href="/css/jquery_ui.css">
	<link rel="stylesheet" href="/css/owl.carousel.css">
	<script src="/script/jquery-1.10.1.min.js"></script>
	<script src="/script/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="/script/html5shiv.js"></script>
	<script src="/script/owl.carousel.js"></script>
	<script src="/script/common.js"></script>
	<script src="//code.highcharts.com/stock/highstock.js"></script>
	<script>
Highcharts.setOptions({
	global: {
		useUTC: false
	}
});
	$(function(){
		$('div').each(function(){
			if($(this).data('role') == 'strategy_graph' && !$(this).data('loaded')){
				loadGraph($(this).attr('id'));
			}
		});
	});

$(function () {
    $('#uni_strategy_graph').highcharts('StockChart',{
        chart: {
	    animation: true
        },
	rangeSelector: {
            selected: 5
        },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
	exporting: { enabled: false },
	tooltip: { enabled: true},
	legend: {
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 80,
            y: 60,
            floating: true,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
            enabled: true
        },
        xAxis: {
   	    labels: {
		enabled: false
	    },
            type: 'datetime',
            minRange: 14 * 24 * 3600000 // fourteen days
        },
	/*yAxis: {
            min : 990,
            title: {
                text: null
            },
            plotLines: [{
                        value: 1000,
                        width: 5,
                        color: 'silver'
            }]
        },*/
        yAxis: [{
	    min : 990,
            title: {
                text: '기준가'
            },
	    plotLines: [{
                        value: 1000,
                        width: 5,
                        color: 'silver'
            }],
	   opposite: true	    
        },{
	    min : 990,
            title: {
                text: '통합기준가'
            },
	   opposite: false	    
        }],
        plotOptions: {
            area: {
                marker: {
		    enabled: false
                },
                lineWidth: 10,
                threshold: null
            },
	    series:{
		allowPointSelect: true,
		enableMouseTracking: true,
		animation: true
	   }
        },

	series: [{
            type: 'areaspline',
	    color: '#E62D2D',
            name: 'Sysmetic Traders 통합기준가',
	    yAxis: 1,
            data: <?php echo $univ_values_str ?>,
	    fillColor : {
                    linearGradient : {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops : [
                        [0, '#E62D2D'],
                        [1, Highcharts.Color('#E62D2D').setOpacity(0).get('rgba')]
                    ]
            }
        },{
            type: 'spline',
	    color: '#4472C4',
            name: 'SM Score 1위 : <?php echo $top_strategies[0]['name'] ?>',
            data: <?php echo $top_strategies_str ?>,
	    fillColor : {
                    linearGradient : {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops : [
                        [0, '#4472C4'],
                        [1, Highcharts.Color('#4472C4').setOpacity(0).get('rgba')]
                    ]
		}
        },{
            type: 'spline',
	    color: '#FDA11D',
            name: 'Following 1위 : <?php echo $follower_top_strategies[0]['name'] ?>',
            data: <?php echo $follower_top_strategies_str ?>,
	    fillColor : {
                    linearGradient : {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops : [
                        [0, '#FDA11D'],
                        [1, Highcharts.Color('#FDA11D').setOpacity(0).get('rgba')]
                    ]
            }
        }],
	credits:{
		enabled: false
	}
    });
});
	</script>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

	    <?php require_once('common_header.php') ?>

		<!-- container -->
		<div class="container main">
			<div class="sec01">
				<div class="area">
					<p class="summary">
						<img src="images/main/txt_section01_summary.png" alt="시스메틱은 투자자와 트레이더, PB를 연결하는 새로운 형태의 플랫폼입니다. 트레이더가 실제로 운용하는 전략을 찾아보고 PB와의 상담을 통해 상품에 투자할 수 있습니다." />
					</p>
					<a href="/guide?tab=0" class="btn_detail">자세히보기</a>
				</div>
			</div>
			<div class="sec02">
				<div class="area">
					<p class="summary">
						<img src="images/main/txt_section02_summary.png" alt="시스메틱에서 가장 좋은 투자전략을 Follow 하세요! 관심전략의 레코드를 매주 Weekly Report 발행해 드립니다. PB와의 상담을 통해 전략에 투자해 보세요." />
					</p>
					<div class="btn_area">
						<a href="/join_select?type=N">투자자 가입하기</a>
						<a href="/strategies">전략랭킹 보기</a>
					</div>
					<div class="follow_ranking_list">
						<ul>
                        
						    <?
                            $cnt = array('1st.', '2nd.', '3rd.');
                            for ($i=0;$i<=2;$i++) {
                                $price_summary = number_format($follower_top_strategies[$i]['daily_values'][count($follower_top_strategies[$i]['daily_values'])-1]['acc_pl']);
                                $summary_len = strlen($price_summary);
                                for ($k=$summary_len-1, $j=0; $k >= 0; $k--, $j++) {
                                    $tmp_summary[$j] = $price_summary[$k];
                                }
                            ?>
							<li>
								<div class="title_area">
									<strong class="n_gothic">Following Ranking <span><?=$cnt[$i];?></span></strong>
									<span class="n_gothic price_summary">* 누적수익금액</span>
								</div>
								<div class="price">
                                    <? for ($a=11; $a >= 0; $a--) { ?>
                                        <? if ($summary_len == $a) { ?>
                                        <span class="n_gothic won">￦</span>
                                        <? } else { ?>
                                            <span class="n_gothic"><?=(isset($tmp_summary[$a])) ? $tmp_summary[$a] : '';?></span>
                                        <? } ?>
                                    <? } ?>
								</div>
								<div class="user_info">
									<div class="photo">
										<img src="<?php echo $follower_top_strategies[$i]['developer']['picture_s'] ?>" alt="" />
									</div>
									<div class="info">
										<strong class="name n_gothic"><?php echo htmlspecialchars($follower_top_strategies[$i]['name']) ?></strong>
										<a href="/strategies/<?=$follower_top_strategies[$i]['strategy_id'] ?>" class="btn_visit n_gothic">라운지 방문하기</a>
									</div>
								</div>
							</li>
                            <? } ?>

						</ul>
					</div>
				</div>
			</div>
			<div class="sec03">
				<div class="area">
					<p class="summary">
						<img src="images/main/txt_section03_summary.png" alt="개발자이신가요? 지금 바로 좋은 투자전략을 공유해보세요. 투자전략 분석과 함께 투자자금 매칭서비스를 제공해 드립니다." />
					</p>
					<div class="btn_area">
						<a href="/join_select?type=T">트레이더 가입하기</a>
						<a href="javascript:;">트레이더 목록보기</a>
					</div>
					<p class="cnt_summary n_gothic"><strong><?php echo number_format($trader_count) ?></strong> 명의 트레이더가  <strong>1,843</strong> 개의 전략을 공유하고 있습니다.</p>
				</div>
			</div>
			<div class="sec04">
				<div class="area">
					<p class="summary">
						<img src="images/main/txt_section04_summary.png" alt="PB 이신가요? 새로운 고객을 만나볼 기회를 제공해 드립니다. 고객과 직접 소통할 수 있는 Lounge 서비스를 제공해 드립니다." />
					</p>
					<div class="btn_area">
						<a href="/join_select?type=P">PB 가입하기</a>
						<a href="javascript:;">PB 목록보기</a>
					</div>
					<div class="box">
						<p class="txt_top n_gothic">
							<strong>2,562</strong> 명의 PB와 함꼐
						</p>
						<p class="txt_bottom n_gothic">
							<strong>362,167,361</strong> 원의 펀딩금액이 이루어졌습니다.
						</p>
					</div>
				</div>
			</div>
			<div class="sec05">
				<div class="area">
					<p class="title"><img src="images/main/txt_section05_title.gif" alt="대표 전략 통합 평균 지표" /></p>
					<p class="title_summary">시스메틱이 제공하는 대표 전략들의 통합 지표입니다.</p>
					<!-- 실제 차트를 넣을 시 아래 bg클래스는 삭제해주세요. size : 894 * 550 -->
                    <div class="chart_area main_graph" id="uni_strategy_graph" style="height:550px;">
                    </div>
				</div>
			</div>

			<div class="sec06">
				<div class="area">
					<p class="title"><img src="images/main/txt_section06_title.png" alt="SM SCORE 랭킹 Top5" /></p>
					<p class="title_summary">시스메틱의 스코어 TOP 랭킹 입니다.</p>
					<ul class="ranking_list">
                        <?php foreach($top_strategies as $k => $strategy){ ?>
                        <!-- <li class="list<?php echo $k + 1 ?>"> 
                            <a href="/strategies/<?php echo $strategy['strategy_id'] ?>">
			                <span class="pl_rate <?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'], 'always') ?>"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2) ; ?>%</span>
			                <span class="pl_rate <?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['daily_pl_rate'], 'always') ?>"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['daily_pl_rate'],2) ; ?>%</span>
			                <span class="title"><?php echo htmlspecialchars($strategy['name']) ?></span> 
			                <span class="profile"><?php if(!empty($strategy['developer']['picture_s'])){ ?><img src="/img/over_s1.png" class="over" /><img src="<?php echo  $strategy['developer']['picture_s'] ?>" /> <?php } ?><?php if(!empty($strategy['developer']['nickname'])) echo $strategy['developer']['nickname']; else echo htmlspecialchars($strategy['developer_name']) ?></span></a>
                        </li> -->

						<li>
							<div class="num winner"><span class="n_gothic"><?php echo $k + 1 ?></span></div>
							<div class="user_info">
								<div class="photo"><img src="<?php echo  $strategy['developer']['picture_s'] ?>" alt="" /></div>
								<strong class="name"><?php if(!empty($strategy['developer']['nickname'])) echo $strategy['developer']['nickname']; else echo htmlspecialchars($strategy['developer_name']) ?></strong>
							</div>
							<div class="chart">
								<div class="chart_area bg">
									chart_area
								</div>
							</div>
							<div class="option">
								<p class="name"><?php echo htmlspecialchars($strategy['name']) ?></p>
								<div class="options">
									<span class="op_s">S</span>
									<span class="op_d">D</span>
									<span class="op_etf">주식/ETF</span>
									<span class="op_k200_sun">K200선물</span>
									<span class="op_k200_op">K200옵션</span>
									<span class="op_out_sun">해외선물</span>
									<span class="op_out_op">해외옵션</span>
								</div>
							</div>
							<div class="cnt_stat">
								<dl>
									<dt>SM Score</dt>
									<dd class="n_gothic">60.33</dd>
								</dl>
							</div>
							<div class="cnt_stat">
								<dl>
									<dt>MDD</dt>
									<dd class="n_gothic"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2) ; ?>%</dd>
								</dl>
							</div>
							<div class="cnt_stat">
								<dl>
									<dt>누적수익률</dt>
									<dd class="n_gothic txt_red"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['daily_pl_rate'],2) ; ?>%</dd>
								</dl>
							</div>
						</li>
						<?php } ?>

					</ul>

				</div>
			</div>

			<div class="sec07">
				<div class="area">
					<div class="bigdata_area">
						<a href="javascript:;">
							<img src="images/main/img_section07_banner.jpg" alt="주식빅데이터 모든 주식에 대한 데이터를 한곳에서!!! 시스메틱의 주식 빅데이터 서비스" />
						</a>
					</div>
					<p class="title"><img src="images/main/txt_section07_title.gif" alt="파트너" /></p>
					<p class="title_summary">시스메틱의 스코어 TOP 랭킹 입니다.</p>
					<div class="partner_list">
						<ul>
							<li><a href="javascript:;"><img src="images/main/partner_hana.gif" alt="하나대투증권" /></a></li>
							<li><a href="javascript:;"><img src="images/main/partner_shinhan.gif" alt="신한금융투자" /></a></li>
							<li><a href="javascript:;"><img src="images/main/partner_hi.gif" alt="하이투자증권" /></a></li>
							<li><a href="javascript:;"><img src="images/main/partner_kyobo.gif" alt="교보증권" /></a></li>
							<li><a href="javascript:;"><img src="images/main/partner_kdb.gif" alt="KDB대우증권" /></a></li>
							<li><a href="javascript:;"><img src="images/main/partner_daishin.gif" alt="대신증권" /></a></li>
						</ul>
						<button class="btn_more">더보기 +</button>
					</div>
					<div class="owl-carousel banner_list"> 
						<div class="item">
							<a href="javascript;">
								<img src="images/main/img_banner01.jpg" alt="시스메틱 리뉴얼 오픈 이벤트 | 시스메틱 홈페이지 리뉴얼 기념 이벤트로 신규 회원가입후 포트폴리오 생성하면 많은 혜택을 드립니다.">
							</a>
						</div>
						<div class="item">
							<a href="javascript;">
								<img src="images/main/img_banner01.jpg" alt="시스메틱 리뉴얼 오픈 이벤트 | 시스메틱 홈페이지 리뉴얼 기념 이벤트로 신규 회원가입후 포트폴리오 생성하면 많은 혜택을 드립니다.">
							</a>
						</div>
						<div class="item">
							<a href="javascript;">
								<img src="images/main/img_banner01.jpg" alt="시스메틱 리뉴얼 오픈 이벤트 | 시스메틱 홈페이지 리뉴얼 기념 이벤트로 신규 회원가입후 포트폴리오 생성하면 많은 혜택을 드립니다.">
							</a>
						</div>
					</div>
				</div>
			</div>
			<nav class="section_nav">
				<ul>
					<li class="curr"><a href="javascript:;">1</a></li>
					<li><a href="javascript:;">2</a></li>
					<li><a href="javascript:;">3</a></li>
					<li><a href="javascript:;">4</a></li>
					<li><a href="javascript:;">5</a></li>
					<li><a href="javascript:;">6</a></li>
					<li><a href="javascript:;">7</a></li>
				</ul>
			</nav>
		</div>
		<!-- //container -->

		
	    <?php require_once('common_footer.php') ?>

	</div>
	<!-- //wrapper -->

<script>
	//banner list
	$(".banner_list").owlCarousel({
		slideSpeed : 300,
		paginationSpeed : 400,
		autoPlay: 5000,
		items : 1,
		loop : true
	});

	//window scroll	
	var sec02Top = $('.sec01').outerHeight();
	var sec03Top = sec02Top + $('.sec02').outerHeight();
	var sec04Top = sec03Top + $('.sec03').outerHeight();
	var sec05Top = sec04Top + $('.sec04').outerHeight();
	var sec06Top = sec05Top + $('.sec05').outerHeight();
	var sec07Top = sec06Top + $('.sec06').outerHeight();
	var $sideNav = $('.section_nav ul li');
	$sideNav.children('a').on('click', function(){
		var idx = $(this).parent('li').index();
		console.log(idx);
		if(idx == 0){
			$('html, body').animate({scrollTop : 0}, 400, 'easeInQuart');
		}else if(idx == 1){
			$('html, body').animate({scrollTop : sec02Top + 20}, 400, 'easeInQuart');
		}else if(idx == 2){
			$('html, body').animate({scrollTop : sec03Top + 20}, 400, 'easeInQuart');
		}else if(idx == 3){
			$('html, body').animate({scrollTop : sec04Top + 20}, 400, 'easeInQuart');
		}else if(idx == 4){
			$('html, body').animate({scrollTop : sec05Top + 20}, 400, 'easeInQuart');
		}else if(idx == 5){
			$('html, body').animate({scrollTop : sec06Top + 20}, 400, 'easeInQuart');
		}else{
			$('html, body').animate({scrollTop : sec07Top + 20}, 400, 'easeInQuart');
		}
	});
	$(window).scroll(function(){
		var scTop = $(window).scrollTop();	
		console.log(scTop);
		if(scTop < sec02Top){
			$sideNav.removeClass('curr').eq(0).addClass('curr');
		}else if(scTop > sec02Top && scTop < sec03Top){
			$sideNav.removeClass('curr').eq(1).addClass('curr');
		}else if(scTop > sec03Top && scTop < sec04Top){
			$sideNav.removeClass('curr').eq(2).addClass('curr');
		}else if(scTop > sec04Top && scTop < sec05Top){
			$sideNav.removeClass('curr').eq(3).addClass('curr');
		}else if(scTop > sec05Top && scTop < sec06Top){
			$sideNav.removeClass('curr').eq(4).addClass('curr');
		}else if(scTop > sec06Top && scTop < sec07Top){
			$sideNav.removeClass('curr').eq(5).addClass('curr');
		}else if(scTop > sec07Top){
			$sideNav.removeClass('curr').eq(6).addClass('curr');
		}
	});
	
</script>
</body>
</html>