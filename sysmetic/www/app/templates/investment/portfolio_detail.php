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
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script>
    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    $(function () {

        <?php if(!empty($flash['error'])){ ?>
        alert('<?php echo htmlspecialchars($flash['error']) ?>');
        <?php } ?>


        $('#chart01').attr({
            'width':168,
            'height':168
        });
        $('#chart01').donut();

        $('#chart02').attr({
            'width':168,
            'height':168
        });
        $('#chart02').donut();



        $('div').each(function(){
            if($(this).data('role') == 'strategy_graph_s' && !$(this).data('loaded')){
                loadGraph($(this).attr('id'));
            }
        });

        //product list swipe
        $('.product_list ul').owlCarousel({
            loop:true,
            margin:25,
            items:6,
            nav:true,
            navText: ["<img src='/images/sub/btn_prev_small.gif'>","<img src='/images/sub/btn_next_small.gif'>"]
        });

        var seriesOptions = [],
            // create the chart when all data is loaded
            createChart = function () {

                $('#graph').highcharts('StockChart', {

                    rangeSelector: {
                        selected: 5,
                    inputEnabled : false
                    },

                    yAxis: [{
                title: {
                text : '개별전략 기준가'
                },
                        labels: {
                            formatter: function () {
                                return (this.value > 0 ? ' + ' : '') + this.value + '';
                            }
                        },
                min: <?php echo $min_value ?>,
                        plotLines: [{
                            value: 1000,
                            width: 5,
                            color: 'silver'
                        }],
                opposite: true
                    },{
                title: {
                text : '포트폴리오 기준가'
                },
                        labels: {
                            formatter: function () {
                                return (this.value > 0 ? ' + ' : '') + this.value + '';
                            }
                        },
                min: <?php echo $min_value ?>,
                opposite: false
                    }],

                    plotOptions: {
                        series: {
                            // compare: 'percent'
                        }
                    },

                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change})<br/>',
                        valueDecimals: 2
                    },
            legend: {
                        enabled: true
                },

                    series: seriesOptions,
                    credits:{
                        enabled: false
                    }
                });
            };

            <?php foreach($strategies as $k => $strategy){ ?>
            seriesOptions[<?php echo $k+1 ?>] = {
                name: '<?php echo htmlspecialchars($strategy['name']) ?>',
                yAxis: 0,
                data: <?php echo $strategy['str_c_price'] ?>
            };
            <?php } ?>

            seriesOptions[0] = {
                name: '포트폴리오 기준가',
                data: <?php echo $str_unified_sm_index; ?>,
                type: 'areaspline',
                yAxis: 1,
                color: '#E62D2D',
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
            };

        createChart();

        // follow
        var follow_load = false;
        $('.follow').on('click', 'button', function(){
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

        // 리뷰로드
        $('.review_area').load('/portfolios/<?=$portfolio['portfolio_id']?>/reviews');
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

            <section class="area in_snb portfolio short">
                <div class="page_header">
                    <a href="/investment/portfolios" class="btn_back">포트폴리오목록</a>
                    <a href="javascript:;window.print();" class="btn_default">포트폴리오 프린트하기</a>
                </div>
                <div class="portfolio_detail_wrap">
                    <div class="side">
                        <div class="follow">
                            <dl class="info">
                                <dt>Followers : </dt>
                                <dd id="follows_count<?=$portfolio['portfolio_id']?>" ><?php echo number_format($portfolio['followers_count']) ?></dd>
                            </dl>

                            <? if ($isLoggedIn()) { ?>
                                <? if ($portfolio['is_following']) { ?>
                                <button id="btn_follow<?=$portfolio['portfolio_id']?>" type="button" class="btn_unfollow" data-role="unfollow" data-portfolio-id="<?=$portfolio['portfolio_id']?>" data-portfolio-name="<?=htmlspecialchars($portfolio['name'])?>">unFollow -</button>
                                <? } else if ($portfolio['uid'] == $_SESSION['user']['uid']) { ?>
                                <button type="button" class="btn_follow" data-role="mine">Follow +</button>
                                <? } else { ?>
                                <button id="btn_follow<?=$portfolio['portfolio_id']?>" type="button" class="btn_follow" data-role="followForm" data-portfolio-id="<?=$portfolio['portfolio_id']?>" data-portfolio-name="<?=htmlspecialchars($portfolio['name'])?>">Follow +</button>
                                <? } ?>
                            <? } else { ?>
                            <button type="button" class="btn_follow" data-role="login">Follow +</button>
                            <? } ?>
                        </div>
                        <div class="user_info">
                            <div class="photo"><img src="<?=getProfileImg($developer['picture']) ?>" /></div>
                            <p class="nickname"><?php if(empty($developer['nickname'])) echo htmlspecialchars($developer_name); else echo htmlspecialchars($developer['nickname']) ?></p>
                        </div>
                        <div class="investment_info first">
                            <dl>
                                <dt>기간</dt>
                                <dd class="n_gothic">
                                    <?=getDateString($portfolio['start_date']) ?> ~<br />
                                    <?=getDateString($portfolio['end_date']) ?>
                                </dd>
                            </dl>
                        </div>
                        <div class="investment_info">
                            <dl>
                                <dt>투자원금(KRW)</dt>
                                <dd class="n_gothic"><?=number_format($portfolio['amount'])?></dd>
                            </dl>
                            <dl>
                                <dt>투자수익(KRW)</dt>
								<dd class="n_gothic"><?=number_format(($portfolio['amount'] * $aStats['total_pl_rate'])/100) ?></dd>
									<!-- <dd class="n_gothic"><?=number_format($portfolio['result_amount']) ?></dd> -->
                            </dl>
                            <dl>
                                <dt>평가금액(KRW)</dt>
                                <dd class="n_gothic"><?=number_format($portfolio['amount'] + (($portfolio['amount'] * $aStats['total_pl_rate']) / 100))?></dd>
                            </dl>
                            <dl>
                                <dt>누적 수익률</dt>
                                <dd class="n_gothic mark"><?=round($aStats['total_pl_rate'],2) // $portfolio_total_profit_rate ?> %</dd>
                            </dl>
                            <dl>
                                <dt>MDD</dt>
                                <dd class="n_gothic"><?=round($portfolio_mdd_rate,2)?> %</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="details">
                        <div class="head">
                            <p class="subject"><?=$portfolio['name'] ?></p>
                        </div>
                        <div class="data_view">
                            <div class="analysis">
                                <!-- 실제 차트 적용 시 아래 bg 클래스 삭제해주세요 size : 716 * 650px -->
                                <div  id="graph" class="chart_area">
                                </div>
                            </div>
                        </div>
                        <div class="product_list">
                            <div class="row_top">
                                <h3>상품 리스트</h3>
                                <p class="summary">총 <strong class="cnt"><?=count($strategies)?></strong> 개의 전략이 있습니다.</p>
                            </div>
                            <ul>
                                <? foreach($strategies as $strategy) { ?>
                                <li class="item">
                                    <a href="javascript:;">
                                        <!-- chart : 실제 서비스 시에는 아래 sample클래스 제거, 70px * 70px -->
                                        <div class="chart_area" id="astrategy_graph<?php echo $strategy['strategy_id'] ?>" data-role="strategy_graph_s" data-graph-data="<?=$strategy['str_c_price']?>">
                                        </div>
                                        <p class="subject"><?=htmlspecialchars($strategy['name']) ?></p>
                                    </a>
                                </li>
                                <? } ?>
                            </ul>
                        </div>
                        
                        <div class="composition_ratio">
							<div class="chart">
								<div class="area">
									<div class="left_a">
										<h3>상품 구성비율</h3>
										<div class="box">
											<canvas id="chart01" class="view">
												<?php foreach((array)$aStats['aStItem'] as $aItemInfo) { ?>
													<div data-value="<?=round($aItemInfo['percent'])?>"></div>
												<?php } ?>
												<!-- 주식/ETF 
												<div data-value="42"></div>
												<!-- K200선물 
												<div data-value="38"></div>
												<!-- 해외옵션 
												<div data-value="20"></div>
												-->
											</canvas>
											<div class="summary">
												<?php foreach((array)$aStats['aStItem'] as $aItemInfo) { ?>
													<div class="row <?=$aItemInfo['color']?>">
														<span class="color"></span>
														<strong class="per"><?=round($aItemInfo['percent'])?>%</strong>
														<span class="item"><?=$aItemInfo['title']?></span>
													</div>
												<?php } ?>
												<!--
												<div class="row blue">
													<span class="color"></span>
													<strong class="per">52%</strong>
													<span class="item">주식/ETF</span>
												</div>
												<div class="row orange">
													<span class="color"></span>
													<strong class="per">28%</strong>
													<span class="item">K200선물</span>
												</div>
												<div class="row gray">
													<span class="color"></span>
													<strong class="per">20%</strong>
													<span class="item">해외옵션	</span>
												</div>
												-->
											</div>
										</div>
									</div>
									<div class="right_a">
										<h3>금액 구성비율</h3>
										<div class="box">
											<canvas id="chart02" class="view right">
												<?php foreach((array)$aStats['aStItem'] as $aItemInfo) { ?>
													<div data-value="<?=round($aItemInfo['money_percent'])?>"></div>
												<?php } ?>
											</canvas>
											<div class="summary">
												<?php foreach((array)$aStats['aStItem'] as $aItemInfo) { ?>
													<div class="row <?=$aItemInfo['color']?>">
														<span class="color"></span>
														<strong class="per"><?=round($aItemInfo['money_percent'])?>%</strong>
														<span class="item"><?=$aItemInfo['title']?></span>
													</div>
												<?php } ?>
												<!--
												<div class="row blue">
													<span class="color"></span>
													<strong class="per">52%</strong>
													<span class="item">주식/ETF</span>
												</div>
												<div class="row orange">
													<span class="color"></span>
													<strong class="per">28%</strong>
													<span class="item">K200선물</span>
												</div>
												<div class="row gray">
													<span class="color"></span>
													<strong class="per">20%</strong>
													<span class="item">해외옵션	</span>
												</div>
												-->
											</div>
										</div>
									</div>
								</div>
							</div>

                            <div class="table">
                                <table>
                                    <colgroup>
                                        <col style="width:35%;" />
                                        <col style="width:13%;" />
                                        <col style="width:13%;" />
                                        <col style="width:13%;" />
                                        <col style="width:13%;" />
                                        <col style="width:13%;" />
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>상품명</th>
                                            <th>비율</th>
                                            <th>운용기간</th>
                                            <th>누적수익률</th>
                                            <th>평균손익률</th>
                                            <th>승률</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <? foreach($strategies as $strategy) { ?>
                                        <tr>
                                            <td class="left"><?=htmlspecialchars($strategy['name']) ?></td>
                                            <td><?=$strategy['percents'] ?>%</td>
                                            <td>
                                            <?php
                                            if(count($strategy['daily_values'])){
                                                $last_time = strtotime($strategy['daily_values'][count($strategy['daily_values'])-1]['basedate']);
                                                $first_time = strtotime($strategy['daily_values'][0]['basedate']);
                                                if($last_time - $first_time > 0){
                                                    if(floor(($last_time - $first_time)/(60*60*24*30*12)) > 0) echo floor(($last_time - $first_time)/(60*60*24*30*12)).'년 ';
                                                    if(ceil((($last_time - $first_time)%(60*60*24*30*12))/(60*60*24*30)) > 0)
                                                        echo ceil((($last_time - $first_time)%(60*60*24*30*12))/(60*60*24*30)).'개월';
                                                }
                                            }
                                            ?>
                                            </td>
                                            <td class="right"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2,'.',''); else echo 0 ?> %</td>
                                            <td class="right"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate'],2,'.',''); else echo 0 ?> %</td>
                                            <td class="right"><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['winning_rate'],2,'.',''); else echo 0 ?> %</td>
                                        </tr>
                                        <? } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="etc_info" style="display:none;">
                           <div class="row_top">
                                <h3>통계</h3>
                                <a href="javascript:;" class="btn_faq">상품통계 FAQ</a>
                            </div>
                            <div class="cont_area">
                               <div class="cont show">
                                    <div class="stats">
                                        <table>
                                            <colgroup>
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th scope="col">잔고</th>
                                                    <td <?=($aStats['balance'] < 0 ? 'class="blue"' : '')?>><?=number_format($aStats['balance'])?></td>
                                                    <th scope="col">운영기간</th>
                                                    <td><?=$aStats['sOperDays']?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="col">누적 입출금액</th>
                                                    <td <?=($aStats['acc_flow'] < 0 ? 'class="blue"' : '')?>><?=number_format($aStats['acc_flow'])?></td>
                                                    <th scope="col">시작일자</th>
                                                    <td><?=date("Y-m-d", $aStats['first_time'])?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="col">원금</th>
                                                    <td <?=($aStats['principal'] < 0 ? 'class="blue"' : '')?>><?=number_format($aStats['principal'])?></td>
                                                    <th scope="col">최종일자</th>
                                                    <td><?=date("Y-m-d", $aStats['last_time'])?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table>
                                            <colgroup>
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th scope="col">누적 수익 금액</th>
                                                    <td><?=number_format($aStats['acc_pl'])?></td>
                                                    <th scope="col">누적 수익률(%)</th>
                                                    <td class="right"><?=round($aStats['total_pl_rate'],2)?>%</td>
                                                </tr>
                                                <tr>
                                                    <th scope="col">최대 누적수익금액</th>
                                                    <td><?=number_format($aStats['max_acc_pl'])?></td>
                                                    <th scope="col">최대 누적수익률(%)</th>
                                                    <td><?=number_format($aStats['max_acc_pl_rate'])?>%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table>
                                            <colgroup>
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th scope="col">현재 자본인하금액</th>
                                                    <td class="blue"><?=number_format($aStats['dd'])?></td>
                                                    <th scope="col">현재 자본인하율(%)</th>
                                                    <td class="blue"><?=$aStats['dd_rate']?>%</td>
                                                </tr>
                                                <tr>
                                                    <th scope="col">최대 자본인하금액</th>
                                                    <td class="blue"><?=number_format($aStats['mdd'])?></td>
                                                    <th scope="col">최대 자본인하율(%)</th>
                                                    <td class="blue"><?=$aStats['mdd_rate']?>%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table>
                                            <colgroup>
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th scope="col">평균 손익 금액</th>
                                                    <td><?=number_format($aStats['avg_pl'])?></td>
                                                    <th scope="col">평균 수익률(%)</th>
                                                    <td><?=$aStats['avg_pl_rate']?>%</td>
                                                </tr>
                                                <tr>
                                                    <th scope="col">최대 일수익 금액</th>
                                                    <td><?=number_format($aStats['max_daily_profit'])?></td>
                                                    <th scope="col">최대 일수익율(%)</th>
                                                    <td><?=$aStats['max_daily_profit_rate']?>%</td>
                                                </tr>
                                                <tr>
                                                    <th scope="col">최대 일손실 금액</th>
                                                    <td class="blue"><?=number_format($aStats['max_daily_loss'])?></td>
                                                    <th scope="col">최대 일손실율(%)</th>
                                                    <td class="blue"><?=$aStats['max_daily_loss_rate']?>%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table>
                                            <colgroup>
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th scope="col">총 매매 일수</th>
                                                    <td colspan="3"><?=number_format($aStats['trade_days'])?>일</td>
													<!--
														<th scope="col">현재 연속 손익일수</th>
														<td>6일</td>
													-->
                                                </tr>
                                                <tr>
                                                    <th scope="col">총 이익 일수</th>
                                                    <td><?=number_format($aStats['profit_days'])?>일</td>
                                                    <th scope="col">최대 연속 이익일수</th>
                                                    <td><?=number_format($aStats['max_profit_days_continue'])?>일</td>
                                                </tr>
                                                <tr>
                                                    <th scope="col">총 손실 일수</th>
                                                    <td><?=number_format($aStats['loss_days'])?>일</td>
                                                    <th scope="col">최대 연속 손실일수</th>
                                                    <td><?=number_format($aStats['max_loss_days_continue'])?>일</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table>
                                            <colgroup>
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                                <col style="width:22%" />
                                                <col style="width:28%" />
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th scope="col">승률</th>
                                                    <td><?=$aStats['winning_rate']?>%</td>
                                                    <th scope="col">고점갱신 후 경과일</th>
                                                    <td><?=number_format($aStats['after_peak_days'])?>일</td>
                                                </tr>
                                                <tr>
                                                    <th scope="col">Profit Factor</th>
                                                    <td><?=$aStats['profit_factor']?></td>
                                                    <th scope="col">ROA</th>
                                                    <td><?=$aStats['roa']?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="review_area">
                            
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
