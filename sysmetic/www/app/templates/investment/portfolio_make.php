							<form id="regFrm">
                            <input type="hidden" name="name" value="<?=$name?>" />
                            <input type="hidden" name="amount" value="<?=$amount?>" />
                            <input type="hidden" name="start_date" value="<?=$start_date?>" />
                            <input type="hidden" name="end_date" value="<?=$end_date?>" />
                            <input type="hidden" id="open" name="open" value="" />
                            <div class="data_view">
								<div class="analysis">
                                    <div  id="graph" class="chart_area">
									</div>
								</div>
							</div>
							<div class="list">
								<div class="row_top">
									<h3>상품 리스트</h3>
									<p class="summary">총 <strong class="cnt"><?=count($strategies)?></strong> 개의 전략이 있습니다.</p>
								</div>
								<ul>
                                    <? foreach($strategies as $strategy) { ?>
                                    <li class="item">
                                        <input type="hidden" name="exchange[]" id="exchange_<?=$strategy['strategy_id']?>" value="<?=$strategy['exchange']?>" />
                                        <input type="hidden" name="strategy_ids[]" id="strategy_<?=$strategy['strategy_id']?>" value="<?=$strategy['strategy_id']?>" />
                                        <div class="chart_area" id="astrategy_graph<?php echo $strategy['strategy_id'] ?>" data-role="strategy_graph_s" data-graph-data="<?=$strategy['str_c_price']?>">
                                        </div>
                                        <p class="subject"><?=htmlspecialchars($strategy['name']) ?></p>
                                        <div class="per_stat">
                                            <button type="button" class="btn minus" title="빼기"></button>
                                            <span class="per"><em><input type="text" name="percents[]" _readonly_ style="width:20px;border:0;text-align:center;" value="<?=$strategy['percents']?>" maxlength="3" class="input_number_only" /></em>%</span>
                                            <button type="button" class="btn plus" title="더하기"></button>
                                        </div>
										<button type="button" class="btn_item_del" title="삭제" data-sid="<?=$strategy['strategy_id']?>" ></button>
                                    </li>
                                    <? } ?>
								</ul>
								<div class="btn_area">
									<button type="button" class="btn_save" id="save_percent">비율저장</button>
								</div>
							</div>
                            </form>

                            <!--
                            start : <?=$start_date?>
                            <br />
                            end : <?=$end_date?>
                            <br />
                            first : <?=$first_available_date?>
                            <br />
                            last : <?=$last_available_date?>
                            //-->
							<div class="pf_result">
								<h3 class="title">포트폴리오 성과</h3>
								<table class="top">
									<colgroup>
										<col style="width:25%;" />
										<col style="width:25%;" />
										<col style="width:25%;" />
										<col style="width:25%;" />
									</colgroup>
									<tbody>
										<tr>
											<th colspan="2">누적수익률</th>
											<th colspan="2">MDD</th>
										</tr>
										<tr>
											<td colspan="2" class="total mark"><?=round($portfolio_total_profit_rate,2)?>%</td>
											<td colspan="2" class="total"><?=round($mdd_rate,2)?>%</td>
										</tr>
										<tr>
											<th>베스트 전략 수익률</th>
											<th>베스트 전략 MDD</th>
											<th>워스트 전략 수익률</th>
											<th>워스트 전략 MDD</th>
										</tr>
										<tr>
											<td class="mark"><?=round($arr_st_stats['best_pl_rate'],2)?>%</td>
											<td><?=round($arr_st_stats['best_mdd_rate'],2)?>%</td>
											<td class="mark"><?=round($arr_st_stats['worst_pl_rate'],2)?>%</td>
											<td><?=round($arr_st_stats['worst_mdd_rate'],2)?>%</td>
										</tr>
									</tbody>
								</table>
								<table>
									<colgroup>
										<col style="width:35%;">
										<col style="width:13%;">
										<col style="width:13%;">
										<col style="width:13%;">
										<col style="width:13%;">
										<col style="width:13%;">
									</colgroup>
									<thead>
										<tr>
											<th>상품명</th>
											<th>비율</th>
											<th>운용기간</th>
											<th>누적 수익률</th>
											<th>평균 수익률</th>
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


    <script>

    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    $(function () {
        $('.btn_item_del').on('click', function() {
            $(this).parent().remove();
            $('#check'+$(this).data('sid')).attr('checked', false);

            $.ajax({
                method: 'post',
                data: $('#regFrm').serialize(),
                url: '/investment/portfolios/make',
                dataType: 'html',
            }).done(function(html) {
                $('.detail').html(html);

                $('.list div.chart_area').each(function(){
                    if ($(this).data('role') == 'strategy_graph_s' && !$(this).data('loaded')) {
                        loadGraph($(this).attr('id'));
                    }
                });

                $('.list ul').owlCarousel({
                    loop:false,
                    items:5,
                    margin:20,
                    nav:true,
                    navText: ["<img src='/images/sub/btn_prev_small.gif'>","<img src='/images/sub/btn_next_small.gif'>"]
                });
            });
        });

        $('#save_percent').on('click', function() {
            /*
            var len = $('#stg_list input:checkbox:checked').length;
            if (!len) {
                alert('전략을 선택해주세요');
                return;
            } else if (len > 10) {
                alert("포트폴리오 구성 시 전략은\n10개까지 추가할 수 있습니다.");
                return;
            } else {
            */
                var percent = 0;
                $('.list input[name^="percents"]').each(function() {
                    percent += parseInt($(this).val());
                });
                
                if (percent < 100 || percent % 100 != 0) {
                    alert('비율의 합은 100이 되어야 합니다');
                    return false;
                }

                $.ajax({
                    method: 'post',
                    data: $('#regFrm').serialize(),
                    url: '/investment/portfolios/make',
                    dataType: 'html',
                }).done(function(html) {
                    $('.detail').html(html);

                    $('.list div.chart_area').each(function(){
                        if ($(this).data('role') == 'strategy_graph_s' && !$(this).data('loaded')) {
                            loadGraph($(this).attr('id'));
                        }
                    });

                    $('.list ul').owlCarousel({
                        loop:false,
                        items:5,
                        margin:20,
                        nav:true,
                        navText: ["<img src='/images/sub/btn_prev_small.gif'>","<img src='/images/sub/btn_next_small.gif'>"]
                    });

					alert('비율이 저장되었습니다.');
                });
            //}
        });

        $('.item button').on('click', function() {
            var input = $(this).siblings('span').find('input');
            var val = parseInt($(input).val());
            if ($(this).hasClass("plus") === true) {
                if (val >= 1000) return;
                $(input).val(val+=100);
            }
            if ($(this).hasClass("minus") === true) {
                if (val <= 0) return;
                $(input).val(val-=100);
            }
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
    });
    </script>