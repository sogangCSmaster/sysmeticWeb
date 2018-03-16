<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 시스메틱 소개</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="/js/jquery.bxslider.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="/js/jquery-syaku.rolling.js" charset="utf-8"></script>
    <script type="text/javascript" src="/js/common.js"></script>
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
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="outer">
            <div id="inner"><a href="http://www.hyundaifutures.com/" target="_black"><img src="./img/hdf.jpg" /></a></div>
        </div>
        <div class="banner">
            <ul class="bxslider">
                <li>
                    <img src="/img/main_banner01.jpg" />
                    <a href="/guide?tab=0">투자 시작하기<!-- 랭킹1위 전략 상세보기로 이동 --></a>
                    <div class="lank_title"><?php echo htmlspecialchars($top_strategies[0]['name']) ?></div>
                    <div class="graph" id="strategy_graph1" data-role="strategy_graph" data-graph-data="<?php echo $top_strategies_str ?>"></div>
                    <dl class="lank1">
                        <dt>전략랭킹 1위</dt>
                        <dd><?php echo number_format($top_strategies[0]['daily_values'][count($top_strategies[0]['daily_values'])-1]['principal']) ?><!-- 추종금액 --></dd>
                        <dd><?php echo number_format($top_strategies[0]['daily_values'][count($top_strategies[0]['daily_values'])-1]['acc_pl']) ?> <!-- 주 --></dd>
                        <dd><?php echo number_format($top_strategies[0]['followers_count']) ?> <!-- 추종자 --></dd>
                        <dd><?php echo number_format($top_strategies[0]['daily_values'][count($top_strategies[0]['daily_values'])-1]['acc_pl_rate']) ?> %<!-- ROI 투자수익률 --></dd>
                    </dl>
                </li>
                <li>
                    <img src="/img/main_banner02.jpg" />
                    <a href="/guide?tab=0">투자 시작하기</a>
                    <ul class="banner2">
						<?php for($i=0;$i<=2;$i++){ ?>
                        <li>
                            <div><p><span><?php echo number_format($follower_top_strategies[$i]['daily_values'][count($follower_top_strategies[$i]['daily_values'])-1]['acc_pl']) ?></span></p></div>
                            <img src="img/over_s2.png" class="over" />
                            <img src="<?php echo $follower_top_strategies[$i]['developer']['picture_s'] ?>" />
                            <b><a href="/strategies/<?php echo $follower_top_strategies[$i]['strategy_id'] ?>"><?php echo htmlspecialchars($follower_top_strategies[$i]['name']) ?></a></b>
                        </li>
						<?php } ?>
                    </ul>
                </li>
                <li>
                    <img src="/img/main_banner03.jpg" />                
                    <a href="/guide?tab=2">트레이더로 가입하기</a>
                </li>
                <li>
                    <img src="/img/main_banner04.jpg" />                
                    <a href="/guide?tab=1">브로커 제휴하기</a>
                </li>
            </ul>   
            <!--
            <?php if(!$isLoggedIn()){ ?>
             <style>
                .bx-pager-item a {height:64px; }
             </style>
		    <?php } ?>   
            -->
            <script>
            var params = {
                defaultSpeed: 1000,
                speedOverrides: {
                    "3": 2000
                    // you can add other slides here,
                    // with the slide number used being the
                    // first slide of the slide transition,
                    // i.e., modifying the speed of 3-4, like
                    // above means you'd enter "2" as the
                    // property name, b/c again we're using
                    // 0-based indices.
                }
            };
            var slider = $('.bxslider').bxSlider({
                mode:'horizontal', //default : 'horizontal', options: 'horizontal', 'vertical', 'fade'
                auto: true, //default:false 자동 시작
                autoHover: true,
                captions: true, // 이미지의 title 속성이 노출된다.
                autoControls: true, //default:false 정지,시작 콘트롤 노출, css 수정이 필요
                randomStart : true,
                speed: params.defaultSpeed,
                onSlideAfter: function (slide, oldIndex, newIndex) {
                    if (params.speedOverrides[newIndex]) {
                        slider.stopAuto();
                        setTimeout(function () {
                            if (newIndex<3) {
                                slider.goToSlide(newIndex + 1);
                            } else {
                            }
                            slider.startAuto();
                        }, params.speedOverrides[newIndex]);
                    }
                }
            });
            </script>
        </div>
        <div id="content" class="view">           
            <div class="stats">
                <dl class="stats1">
                    <dt>시스템 트레이딩 수익률</dt>
                    <dd>
                        <?php echo $main_profit_rate ?>%
                    </dd>
                </dl>

                <dl class="stats2">
                    <dt>트레이더 수 (전략개발자)</dt>
                    <dd>
                        <?php echo number_format($trader_count) ?>
                    </dd>
                </dl>
                <dl class="stats3">
                    <dt>투자금 규모 (펀딩금액)</dt>
                    <dd>
                        <?php echo number_format($total_funding) ?>
                    </dd>
                </dl>
                <dl class="stats4">
                    <dt>시스템 트레이딩 투자자 수</dt>
                    <dd>
                        <?php echo number_format($total_investor) ?>
                    </dd>
                </dl>
            </div>

            <div class="main_graph" id="uni_strategy_graph" style="height:550px;">
            </div>
        </div>

        <div class="lank_list">
            <dl>
                <dt class="self">SM Score 랭킹 TOP5</dt>
                <dd>
                    <ul>
                        <?php foreach($top_strategies as $k => $strategy){ ?>
                        <li class="list<?php echo $k + 1 ?>"> 
                            <a href="/strategies/<?php echo $strategy['strategy_id'] ?>">
			    <span class="pl_rate <?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'], 'always') ?>"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2) ; ?>%</span>
			    <span class="pl_rate <?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['daily_pl_rate'], 'always') ?>"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['daily_pl_rate'],2) ; ?>%</span>
			    <span class="title"><?php echo htmlspecialchars($strategy['name']) ?></span> 
			    <span class="profile"><?php if(!empty($strategy['developer']['picture_s'])){ ?><img src="/img/over_s1.png" class="over" /><img src="<?php echo  $strategy['developer']['picture_s'] ?>" /> <?php } ?><?php if(!empty($strategy['developer']['nickname'])) echo $strategy['developer']['nickname']; else echo htmlspecialchars($strategy['developer_name']) ?></span></a>
                        </li>
						<?php } ?>
                    </ul>
                </dd>
            </dl>
            <dl>
                <dt class="lank">FOLLOW 랭킹 TOP5</dt>
                <dd>
                    <ul>
						<?php foreach($follower_top_strategies as $k => $strategy){ ?>
                        <li class="list<?php echo $k + 1 ?>">
			    <a href="/strategies/<?php echo $strategy['strategy_id'] ?>">
			    <span class="pl_rate <?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'], 'always') ?>"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2) ; ?>%</span>
			    <span class="pl_rate <?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['daily_pl_rate'], 'always') ?>"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['daily_pl_rate'],2) ; ?>%</span>
			    <span class="title"><?php echo htmlspecialchars($strategy['name']) ?></span> 
			    <span class="profile"><?php if(!empty($strategy['developer']['picture_s'])){ ?><img src="/img/over_s1.png" class="over" /><img src="<?php echo $strategy['developer']['picture_s'] ?>" /> <?php } ?><?php if(!empty($strategy['developer']['nickname'])) echo $strategy['developer']['nickname']; else echo htmlspecialchars($strategy['developer_name']) ?></span></a></li>
						<?php } ?>
                    </ul>
                </dd>
            </dl>
        </div>

        <div class="main_brokers">
            <p class="text_bloker">Brokers</p>
            <div class="prev">이전</div>
            <div class="blokersList">
                <div id="brokers">
					<?php foreach($brokers_has_logo as $broker){?>
						<a href="<?php if($broker['broker_id']==28) {echo htmlspecialchars($broker['url']);} else {echo "/brokers#broker".htmlspecialchars($broker['broker_id']-1);} ?>"><img src="<?php echo $broker['logo'] ?>" /></a>
					<?php } ?>
                    <a href="/brokers#broker26"><img src="http://sysmetic.co.kr/broker/logo_aa0db7bfb7cafac455ca3b1f1797a6a0.png" /></a>
                </div>
            </div>
            <div class="next">다음</div>
            <script type="text/javascript">
                jQuery(function() {

                    jQuery("#brokers").srolling({
                        data : $("#brokers > a"),	// 노출될 아이템
                        auto : true,					//자동 롤링	true , false
                        width : 172,					// 노출될 아이템 크기
                        height : 50,					// 노출될 아이템 크기
                        item_count : 5,			// 이동 될 아이템 수
                        cache_count :10,			// 임시로 불러올 아이템 수
                        delay : 4000,				// 이동 아이템 딜레이
                        delay_frame : 3000,		// 아이템 흐르는 속도
                        move : 'left',				// 이동 방향 left , right , top , down
                        prev : '.prev',			// < 이동 버튼
                        next : '.next'			// > 이동 버튼
                    });
                });
            </script>
        </div>
    </div>
<style type="text/css">
#outer {
    width:100px; height:300px;
    border: 1px solid #eeeeee;
    position: absolute;
    top: 0px;
    left: -110px;
    z-index: 0;
    color:#000000;
}
#inner {
    margin: 0 auto;
    text-align: center;
    width: 100px;
}
</style>


    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
