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
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
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
					//- name: 'SM Score 1위 : <?php echo $top_strategies[0]['name'] ?>',
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


        $('div').each(function(){
            if($(this).data('role') == 'strategy_graph_s' && !$(this).data('loaded')){
                loadGraph($(this).attr('id'));
            }
        });
    });
	</script>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

	    <?php require_once('common/header.php') ?>

		<!-- container -->
		<div class="container main">
			<div class="sec01">
				<div class="area">
					<p class="summary">
						<img src="images/main/txt_section01_summary.png" alt="시스메틱은 투자자와 트레이더, PB를 연결하는 새로운 형태의 플랫폼입니다. 트레이더가 실제로 운용하는 전략을 찾아보고 PB와의 상담을 통해 상품에 투자할 수 있습니다." />
					</p>
					<a href="/cs/guide" class="btn_detail">자세히보기</a>
				</div>
			</div>
			<div class="sec02">
				<div class="area">
					<p class="summary">
						<img src="images/main/txt_section02_summary.png" alt="시스메틱에서 가장 좋은 투자전략을 Follow 하세요! 관심전략의 레코드를 매주 Weekly Report 발행해 드립니다. PB와의 상담을 통해 전략에 투자해 보세요." />
					</p>
					<div class="btn_area">
						<a href="/member/join_select?type=N">투자자 가입하기</a>
						<a href="/investment/strategies">상품랭킹 보기</a>
					</div>
					<div class="follow_ranking_list">
						<ul>

						    <?
                            $cnt = array('1st.', '2nd.', '3rd.');
                            for ($i=0;$i<1;$i++) {
                                $tmp_summary = '';
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
										<img src="<?=getProfileImg($follower_top_strategies[$i]['developer']['picture']) ?>" alt="" />
									</div>
									<div class="info">
										<a href="/strategies/<?=$follower_top_strategies[$i]['strategy_id']?>"><strong class="name n_gothic"><?php echo htmlspecialchars($follower_top_strategies[$i]['name']) ?></strong></a>
                                        <? if ($follower_top_strategies[$i]['developer']['user_type'] == 'P') { ?>
										<a href="/lounge/<?=$follower_top_strategies[$i]['developer']['uid']?>" class="btn_visit n_gothic">라운지 방문하기</a>
                                        <? } ?>
									</div>
								</div>
							</li>
                            <? } ?>
                            
                            <?
                            $tmp_summary = '';
                            $price_summary = number_format($sm_top_strategies['daily_values'][count($sm_top_strategies['daily_values'])-1]['acc_pl']);
                            $summary_len = strlen($price_summary);
                            for ($k=$summary_len-1, $j=0; $k >= 0; $k--, $j++) {
                                $tmp_summary[$j] = $price_summary[$k];
                            }
                            ?>
							<li>
								<div class="title_area">
									<strong class="n_gothic">SM Score Ranking <span>1st.</span></strong>
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
										<img src="<?=getProfileImg($sm_top_strategies['developer']['picture']) ?>" alt="" />
									</div>
									<div class="info">
										<a href="/strategies/<?=$sm_top_strategies['strategy_id']?>"><strong class="name n_gothic"><?php echo htmlspecialchars($sm_top_strategies['name']) ?></strong></a>
                                        <? if ($sm_top_strategies['developer']['user_type'] == 'P') { ?>
										<a href="/lounge/<?=$sm_top_strategies['developer']['uid']?>" class="btn_visit n_gothic">라운지 방문하기</a>
                                        <? } ?>
									</div>
								</div>
							</li>

                            <?
                            $tmp_summary = '';
                            $price_summary = number_format($fund_top_strategies['daily_values'][count($fund_top_strategies['daily_values'])-1]['acc_pl']);
                            $summary_len = strlen($price_summary);
                            for ($k=$summary_len-1, $j=0; $k >= 0; $k--, $j++) {
                                $tmp_summary[$j] = $price_summary[$k];
                            }
                            ?>
							<li>
								<div class="title_area">
									<strong class="n_gothic">Funding Ranking <span>1st.</span></strong>
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
										<img src="<?=getProfileImg($fund_top_strategies['developer']['picture']) ?>" alt="" />
									</div>
									<div class="info">
										<a href="/strategies/<?=$fund_top_strategies['strategy_id']?>"><strong class="name n_gothic"><?php echo htmlspecialchars($fund_top_strategies['name']) ?></strong></a>
                                        <? if ($fund_top_strategies['developer']['user_type'] == 'P') { ?>
										<a href="/lounge/<?=$fund_top_strategies['developer']['uid']?>" class="btn_visit n_gothic">라운지 방문하기</a>
                                        <? } ?>
									</div>
								</div>
							</li>


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
						<a href="/member/join_select?type=T">트레이더 가입하기</a>
						<a href="/investment/developers?type=T">트레이더 목록보기</a>
					</div>
					<p class="cnt_summary n_gothic"><strong><?php echo number_format($trader_count) ?></strong> 명의 트레이더가  <strong><?=number_format($trader_strategy_count);?></strong> 개의 상품을 공유하고 있습니다.</p>
				</div>
			</div>
			<div class="sec04">
				<div class="area">
					<p class="summary">
						<img src="images/main/txt_section04_summary.png" alt="PB 이신가요? 새로운 고객을 만나볼 기회를 제공해 드립니다. 고객과 직접 소통할 수 있는 Lounge 서비스를 제공해 드립니다." />
					</p>
					<div class="btn_area">
						<a href="/member/join_select?type=P">PB 가입하기</a>
						<a href="/investment/developers?type=P">PB 목록보기</a>
					</div>
					<div class="box">
						<p class="txt_top n_gothic">
							<strong><?=number_format($pb_count);?></strong> 명의 PB와 함꼐
						</p>
						<p class="txt_bottom n_gothic">
							<strong><?=number_format($total_funding);?></strong> 원의 펀딩금액이 이루어졌습니다.
						</p>
					</div>
				</div>
			</div>
			<div class="sec05">
				<div class="area">
					<p class="title"><img src="images/main/txt_section05_title.gif" alt="대표 전략 통합 평균 지표" /></p>
					<p class="title_summary">시스메틱이 제공하는 대표 상품들의 통합 지표입니다.</p>
                    <div class="chart_area main_graph" id="uni_strategy_graph" style="height:550px;"></div>
				</div>
			</div>

			<div class="sec06">
				<div class="area">
					<p class="title"><img src="images/main/txt_section06_title.png" alt="SM Score 랭킹 Top5" /></p>
					<p class="title_summary">시스메틱의 스코어 TOP 랭킹 입니다.</p>
					<ul class="ranking_list">
                        <?
                        foreach($top_strategies as $k => $strategy){
                           // __v($strategy);

                           $strategy_term = strtoupper($strategy['strategy_term']);
                        ?>
						<li>
							<div class="num <?=($k==0) ? 'winner' : '';?>"><span class="n_gothic">0<?php echo $k + 1 ?></span></div>
							<div class="user_info">
								<div class="photo"><img src="<?=getProfileImg($strategy['developer']['picture']) ?>" alt="" /></div>
								<strong class="name"><?php if(!empty($strategy['developer']['nickname'])) echo $strategy['developer']['nickname']; else echo htmlspecialchars($strategy['developer_name']) ?></strong>
							</div>
							<div class="chart">
								<div class="chart_area" id="astrategy_graph<?php echo $strategy['strategy_id'] ?>" data-role="strategy_graph_s" data-graph-data="<?=$strategy['str_c_price']?>" style="width:127px;height:120px;">
								</div>
							</div>
							<div class="option">
								<a href="/investment/strategies/<?php echo $strategy['strategy_id'] ?>"><p class="name"><?php echo htmlspecialchars($strategy['name']) ?></p></a>
								<div class="options">
									<!--span class="op_s"><?=$strategy['types']['icon']?></span-->
                                    
                                    <img src="<?=$strategy['types']['icon']?>" />
                                    <?
                                    if($strategy['strategy_term']=="day")echo "<img src='/images/sysm_d.png'>";
                                    if($strategy['strategy_term']=="position")echo "<img src='/images/sysm_p.png'>";
                                    ?>
									<!--span class="op_d"><?=$strategy_term[0]?></span-->
                                    <?
                                    foreach ($strategy['items'] as $k => $v) {
                                        switch ($v['name']) {
                                            case "주식/ETF": $class="op_etf"; break;
                                            case "K200선물": $class="op_k200_sun"; break;
                                            case "K200옵션" : $class="op_k200_op"; break;
                                            case "해외선물" : $class="op_out_sun"; break;
                                            case "해외옵션" : $class="op_out_op"; break;
                                            default : $class="";
                                        }
                                    ?>
									<!--span class="<?=$class?>"><?=$v['name']?></span-->
                                    <img src="<?=$v['icon']?>" />
                                    <?
                                    }
                                    ?>
								</div>
							</div>
							<div class="cnt_stat">
								<dl>
									<dt>SM Score</dt>
									<dd class="n_gothic"><?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'])) echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'],2); else echo '0' ?></dd>
								</dl>
								<!--
									<dl>
										<dt>SM Score</dt>
										<dd class="n_gothic"><?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'])) echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'],2); else echo '0' ?></dd>
									</dl>
								-->
							</div>
							<div class="cnt_stat">
								<dl>
									<dt>MDD</dt>
									<dd class="n_gothic">
                                    <?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd_rate'],2) ?> %
					                <?php //if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd']); else echo '0'; ?></dd>
								</dl>
							</div>
							<div class="cnt_stat">
								<dl>
									<dt>누적수익률</dt>
									<dd class="n_gothic txt_red"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate']);else echo '0' ?>%</dd>
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
						<a href="javascript:;bigData();">
							<img src="images/main/img_section07_banner.jpg" alt="주식빅데이터 모든 주식에 대한 데이터를 한곳에서!!! 시스메틱의 주식 빅데이터 서비스" />
						</a>
					</div>
				<p class="title"><h1 style="font-size:30px !important">중개사</h1> <!--img src="images/main/txt_section07_title.gif" alt="파트너" /--></p>
					<!--p class="title_summary">시스메틱의 파트너 입니다.</p-->
					<div class="partner_list">
						<ul>
                            <? foreach ($partners_main as $k => $v) { //318 154 ?>
							<li><a href="<?=$v['url']?>" target="_blank"><img src="<?=$v['logo']?>" alt="<?=$v['company']?>" style="margin-top:30px" /></a></li>
                            <? } ?>
						</ul>
						<button class="btn_more" onclick="location.href='/cs/partners';">더보기 +</button>
					</div>
					<div class="owl-carousel banner_list">
                        <?
						$cnt=0;
						foreach ($banners as $v) {
							$cnt++;
						?>
						<div class="item">
							<a href="<?=$v['url']?>" target="_blank">
								<img src="/data/banner/<?=$v['banner_image']?>" alt="<?=$v['subject']?>" style="width:990px; height:136px;" />
							</a>
						</div>
                        <?
						}
						if($cnt < 2){
							$loopChk="false";
						}else{
							$loopChk="true";
						}
						?>
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


	    <?php require_once('common/footer.php') ?>

	</div>
	<!-- //wrapper -->

<script>
	//banner list
	$(".banner_list").owlCarousel({
		slideSpeed : 300,
		paginationSpeed : 400,
		autoplay: true,
		autoplayTimeout: 3000,
		autoplayHoverPause: true,
		items : 1,
		loop : <?=$loopChk?>
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
