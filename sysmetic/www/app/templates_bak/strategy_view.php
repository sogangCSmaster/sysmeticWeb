<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - <?php echo htmlspecialchars($strategy['name']) ?></title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script src="http://code.highcharts.com/stock/highstock.js"></script>
	<!--script src="/js/chartthemes/dark-blue.js"></script-->
	<!--script src="http://code.highcharts.com/stock/modules/exporting.js"></script-->
	<script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '869896133071334',
          xfbml      : true,
          version    : 'v2.3'
        });
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));

	function ask(name){
		$('#ask_strategy_name').text(name);
		showLayer('ask');
	}

		<?php
		$series_data = array();
		foreach($strategy['daily_values'] as $v){
			foreach($v as $kk => $vv){
				if(is_numeric($kk)) continue;
				if(!isset($series_data[$kk])) $series_data[$kk] = array();
				$series_data[$kk][] = array($v['m_timestamp'] => $vv);
			}
		}
		
		echo 'var series_data = {'."\n";
		foreach($series_data as $k => $v){
			if(!in_array($k, array('sm_index','balance', 'principal', 'acc_flow', 'flow', 'daily_pl', 'daily_pl_rate', 'acc_pl', 'acc_pl_rate', 'dd', 'dd_rate', 'avg_pl', 'avg_pl_rate', 'winning_rate', 'avg_pl_rate', 'roa', 'total_profit', 'total_loss'))) continue;
			echo '\''.$k.'\' : ';
			$v_array = array();
			foreach($v as $vv){
				foreach($vv as $kkk => $vvv){
					$v_array[] = '['.$kkk.','.$vvv.']';
				}
			}
			echo '['.implode(',', $v_array).'],'."\n";
		}
		echo '};'."\n";
		
		echo 'var series_min_data = {'."\n";
		foreach($series_data as $k => $v){
			if(!in_array($k, array('sm_index','balance', 'principal', 'acc_flow', 'flow', 'daily_pl', 'daily_pl_rate', 'acc_pl', 'acc_pl_rate', 'dd', 'dd_rate', 'avg_pl', 'avg_pl_rate', 'winning_rate', 'avg_pl_rate', 'roa', 'total_profit', 'total_loss'))) continue;
			echo '\''.$k.'\' : ';
			$min_value = -9999;
			foreach($v as $vv){
				foreach($vv as $kkk => $vvv){
					if ( $min_value == -9999 ) $min_value = $vvv;
					if ( $vvv < $min_value ) $min_value = $vvv;
				}
			}
			echo '['.$min_value.'],'."\n";
		}
		echo '};'."\n";

		echo 'var series_max_data = {'."\n";
		foreach($series_data as $k => $v){
			if(!in_array($k, array('sm_index','balance', 'principal', 'acc_flow', 'flow', 'daily_pl', 'daily_pl_rate', 'acc_pl', 'acc_pl_rate', 'dd', 'dd_rate', 'avg_pl', 'avg_pl_rate', 'winning_rate', 'avg_pl_rate', 'roa', 'total_profit', 'total_loss'))) continue;
			echo '\''.$k.'\' : ';
			$max_value = 9999;
			foreach($v as $vv){
				foreach($vv as $kkk => $vvv){
					if ( $max_value == 9999 ) $max_value = $vvv;
					if ( $vvv > $max_value ) $max_value = $vvv;
				}
			}
			echo '['.$max_value.'],'."\n";
		}
		echo '}';
		?>

	$(function(){
		$('#ask_form').submit(function(){
			if(!$('#ask_body').val()){
				alert('내용을 입력해주세요.');
				return false;
			}

			$.post($(this).attr('action'), $(this).serialize(), function(data){
				if(data.result){
					$('#ask_body').val('');
					alert('문의내용이 접수되었습니다');
					closeLayer('ask');
				}
			}, 'json');
			return false;
		});

		$('#chart_select_items .iList input[type=radio]').on('click', function(){
			//console.log(series_data[$('#chart_select_items input[name=1series]:checked').val()]);
			createGraph($('#chart_select_items input[name=1series]:checked').data('label')
				, $('#chart_select_items input[name=1series]:checked').data('chart')
				, series_data[$('#chart_select_items input[name=1series]:checked').val()]
				, series_min_data[$('#chart_select_items input[name=1series]:checked').val()]
				, series_max_data[$('#chart_select_items input[name=1series]:checked').val()]
				, $('#chart_select_items input[name=2series]:checked').data('label')
				, $('#chart_select_items input[name=2series]:checked').data('chart')
				, series_data[$('#chart_select_items input[name=2series]:checked').val()]
				, series_min_data[$('#chart_select_items input[name=2series]:checked').val()]
				, series_max_data[$('#chart_select_items input[name=2series]:checked').val()]);
		});

		createGraph('누적 수익률(%)', 'line', series_data['acc_pl_rate'], series_min_data['acc_pl_rate'], series_max_data['acc_pl_rate'], '원금', 'line', series_data['principal'], series_min_data['principal'], series_max_data['principal']);

	});

function createGraph(name, charttype, data, min, max, name1, charttype1, data1, min1, max1){
        // split the data set into ohlc and volume
        var ohlc = [],
            volume = [],
            dataLength = data.length,
            i = 0;

        for (i; i < dataLength; i += 1) {
            ohlc.push([
                data[i][0], // the date
                data[i][2], // high
            ]);

            volume.push([
                data[i][0], // the date
                data[i][5] // the volume
            ]);
        }

		Highcharts.setOptions({
			global: {
				useUTC: false
			}
		});

        // create the chart
        $('#strategy_graph').highcharts('StockChart', {

            rangeSelector: {
                selected: 5
            },

            title: {
                text: null
            },
	    tooltip: { 
		enabled: true
	    },

            yAxis: [{
		//min : min,
		//max : max,
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: name
                },
                height: '60%',
                lineWidth: 2
            }, {
		//min : min1,
		//max : max1,
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: name1
                },
                top: '65%',
                height: '35%',
                offset: 0,
                lineWidth: 2
            }],
	    legend: {
            	layout: 'vertical',
            	align: 'left',
            	verticalAlign: 'top',
            	x: 30,
            	y: 60,
            	floating: true,
            	borderWidth: 1,
	        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
              	enabled: true
            },

            series: [{
                type: charttype,
                name: name,
                data: data,
		fillColor : {
                    linearGradient : {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops : [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                }
				// enableMouseTracking:false,

            }, {
                type: charttype1,
                name: name1,
                data: data1,
                yAxis: 1,
		fillColor : {
                    linearGradient : {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops : [
                        [0, Highcharts.getOptions().colors[1]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[1]).setOpacity(0).get('rgba')]
                    ]
                }
				// allowPointSelect: false,
				// enableMouseTracking:false,
				// animation:false
            }],
			navigator:{
				enabled: true
			},
			credits:{
				enabled: false
			}
        });
}

function openImage(url){
	$('#show_img').attr('src', url);
	showLayer('preview');
}
	</script>
    
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <a href="/strategies" class="btn_list">목록으로</a>
        <button onclick="window.print();" class="btn_print" title="전략 프린트하기"><span class="ir">전략 프린트하기</span></button>
        <div id="content" class="view">
            <div class="strategy_info">
                <h4>
					<?php if($strategy['strategy_type'] == 'M'){ ?>
					<img src="/img/ico_menual.gif" />
					<?php }else if($strategy['strategy_type'] == 'S'){ ?>
					<img src="../img/ico_system.gif" />
					<?php }else if($strategy['strategy_type'] == 'H'){ ?>
					<img src="../img/ico_hybrid.gif" />
					<?php } ?>

					<?php if($strategy['strategy_term'] == 'day'){ ?>
					<img src="/img/ico_day.gif" />
					<?php }else if($strategy['strategy_term'] == 'position'){ ?>
					<img src="../img/ico_position.gif" />
					<?php } ?>

					<?php foreach($strategy['items'] as $v){ ?>
                    <img src="<?php echo $v['icon'] ?>" alt="<?php echo htmlspecialchars($v['name']) ?>" />
					<?php } ?>
                    <?php echo htmlspecialchars($strategy['name']) ?>
                </h4>
                <p class="info_txt">
                    <?php echo nl2br(htmlspecialchars($strategy['intro'])) ?>
                </p>
                
                <dl class="tool">
                    <dt>트레이더</dt>
                    <dd><?php if(!empty($strategy['developer']['picture'])){ ?><img src="/img/over_s1.png" class="over" /><img src="<?php echo htmlspecialchars($strategy['developer']['picture']) ?>" class="trader" /> <?php } ?><?php if(empty($strategy['developer']['nickname'])) echo htmlspecialchars($strategy['developer_name']); else echo htmlspecialchars($strategy['developer']['nickname']) ?></dd>
                    <dt>증권사</dt>
                    <dd><?php if(!empty($strategy['broker']['logo_s'])){ ?><img src="<?php echo $strategy['broker']['logo_s'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['broker']['company']) ?><?php } ?></dd>
                    <dt>매매툴</dt>
                    <dd><?php if(!empty($strategy['system_tool']['logo'])){ ?><img src="<?php echo $strategy['system_tool']['logo'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['system_tool']['name']) ?><?php } ?></dd>
                </dl>
                <div class="point">
                    <dl>
                        <dt>투자원금(<?php echo $strategy['currency'] ?>)</dt><!------ 화폐단위 ()안에 불러와서 표시해 줄 것 ------->
                        <dd>
						<?php if(!empty($strategy['daily_values'][count($strategy['daily_values'])-1])){ ?>
						<span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['principal'],'true') ?>"><?php echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['principal']) ?></span>
						<?php }else{ ?>
						<span>0</span>
						<?php } ?>
						</dd>
                    </dl>
                    <dl>
                        <dt>SM Score</dt>
                        <dd>
						<?php if(!empty($strategy['daily_values'][count($strategy['daily_values'])-1])){ ?>
						<span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'],'false') ?>"><?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'])) echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'],2); else echo '0' ?></span>
						<?php }else{ ?>
						<span>0</span>
						<?php } ?>
						</dd>
                    </dl>
                    <!------ 펀딩금액/투자자수는 공개/비공개 여부에 따라 노출됨 ------->
                    <dl>
                        <dt>펀딩금액(<?php echo $strategy['currency'] ?>)</dt><!------ 화폐단위 ()안에 불러와서 표시해 줄 것 ------->
                        <dd><span class="<?php echo getSignClass($strategy['total_funding'],'true') ?>"><?php echo number_format($strategy['total_funding']) ?></span></dd>
                    </dl>
                    <dl>
                        <dt>투자자 수</dt>
                        <dd><span class="<?php echo getSignClass($strategy['investor_count'],'true') ?>"><?php echo number_format($strategy['investor_count']) ?></span></dd>
                    </dl>
                 </div>

                 <div class="act_btn">
                    <button type="button" title="페이스북 공유하기" class="facebook" onclick="shareFB('<?php echo htmlspecialchars($strategy['name']) ?>', '<?php echo htmlspecialchars($current_url) ?>')"><span class="ir">페이스북 공유</span></button>
					<?php if($isLoggedIn()){ ?>
					<?php if($_SESSION['user']['user_type'] == 'N'){ ?>
                    <button type="button" title="전략 문의하기" class="contact" onclick="ask('<?php echo htmlspecialchars($strategy['name']) ?>');"><span class="ir">전략 문의하기</span></button>
					<?php } ?>
					<?php } ?>

                    <span class="followers">Followers<br /><b><?php echo number_format($strategy['followers_count']) ?></b></b></span>
                 </div>
                 
				<?php if($isLoggedIn()){ ?>
				<?php if($strategy['is_following']){ ?>
                <button type="button" title="Follow" class="unfollow" onclick="location.href='/strategies/<?php echo $strategy['strategy_id'] ?>/unfollow'"><span class="ir">Unfollow</span></button>
				<?php }else{ ?>
                <button type="button" title="Follow" class="follow" onclick="location.href='/strategies/<?php echo $strategy['strategy_id'] ?>/follow'"><span class="ir">Follow</span></button>
				<?php } ?>
				<?php } ?>
            </div>

            <div class="main_data">
                <dl>
                    <dt>누적 수익률</dt>
                    <dd>
					<?php if(!empty($strategy['daily_values'][count($strategy['daily_values'])-1])){ ?>
					<span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],'false') ?>">
					<?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2) ?>%</span>
					<?php }else{ ?>
					<span>0%</span>
					<?php } ?>
					</dd>
                </dl>
                <dl class="2nd" style="background-color:#f0f0f0;">
                    <dt>최대 자본인하율</dt>
                    <dd>
					<?php if(!empty($strategy['daily_values'][count($strategy['daily_values'])-1])){ ?>
					<span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd_rate'],'true') ?>"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd_rate'],2) ?>%</span>
					<?php }else{ ?>
					<span>0%</span>
					<?php } ?>
					</dd>
                </dl>
                <dl>
                    <dt>평균 손익률</dt>
                    <dd>
					<?php if(!empty($strategy['daily_values'][count($strategy['daily_values'])-1])){ ?>
					<span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate'],'false') ?>"><?php echo $strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate'] ?>%</span>
					<?php }else{ ?>
					<span>0%</span>
					<?php } ?>
					</dd>
                </dl>
                <dl class="2nd" style="background-color:#f0f0f0;">
                    <dt>Profit Factor</dt>
                    <dd>
					<?php if(!empty($strategy['daily_values'][count($strategy['daily_values'])-1])){ ?>
					<span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['profit_factor'], 'false') ?>"><?php echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['profit_factor'], 4) ?> : 1</span>
					<?php }else{ ?>
					<span>0 : 1</span>
					<?php } ?>
					</dd>
                </dl>
                <dl>
                    <dt>승률</dt>
                    <dd>
					<?php if(!empty($strategy['daily_values'][count($strategy['daily_values'])-1])){ ?>
					<span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['winning_rate'], 'true') ?>"><?php echo $strategy['daily_values'][count($strategy['daily_values'])-1]['winning_rate'] ?>%</span>
					<?php }else{ ?>
					<span>0%</span>
					<?php } ?>
					</dd>
                </dl>
            </div>

            <h5>월간 수익률</h5>
            <table border="0" cellspacing="1" cellpadding="0" class="monthly">
            <col width="60" /><col width="61" /><col width="61" /><col width="61" /><col width="61" /><col width="61" /><col width="61" />
            <col width="61" /><col width="61" /><col width="61" /><col width="61" /><col width="61" /><col width="61" /><col width="*" />
                <thead>
                <tr>
                    <td></td>
                    <td>Jan</td>
                    <td>Feb</td>
                    <td>Mar</td>
                    <td>Apr</td>
                    <td>May</td>
                    <td>Jun</td>
                    <td>Jul</td>
                    <td>Aug</td>
                    <td>Sep</td>
                    <td>Oct</td>
                    <td>Nov</td>
                    <td>Dec</td>
                    <td>YTD</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($strategy['monthly_profit_rate'] as $year => $v){ ?>
                <tr>
                    <td class="thead"><?php echo $year ?></td>
					<?php foreach($v as $month => $vv){
                    				if($strategy['monthly_profit_rate'][$year][$month] != 0) {
							echo '<td class="'.getSignClass($vv,'false').'">'.round($vv,2).'%</td>';
						} else echo '<td></td>';
					} ?>
					<?php 	echo '<td class="'.getSignClass($strategy['yearly_profit_rate'][$year],'false').'">'.round($strategy['yearly_profit_rate'][$year],2).'%</td>'; ?>
                </tr>
				<?php } ?>
                </tbody>
            </table>

            <h5>분석</h5>
            <div class="analysis">
				<!--
                <div class="btn">
                    <button id="" type="" title="1M" class="active"><span class="ir">1M</span></button>
                    <button id="" type="" title="3M"><span class="ir">3M</span></button>
                    <button id="" type="" title="6M"><span class="ir">6M</span></button>
                    <button id="" type="" title="1Y"><span class="ir">1Y</span></button>
                    <button id="" type="" title="ALL"><span class="ir">ALL</span></button>
                </div>
				-->

                <div class="select_box" id="chart_select_items">
                    <div class="select open" style="width:160px;">
                        <div class="myValue"></div>
                        <ul class="iList">
                            <!-- <li><input name="1series" id="1series0" class="option" type="radio" value="" checked="checked" /><label for="1series0">Series1 선택</label></li> -->
                            <li><input name="1series" id="1series32" class="option" type="radio" value="sm_index" data-label="기준가" data-chart="line" /><label for="1series32">기준가</label></li>
                            <li><input name="1series" id="1series0" class="option" type="radio" value="balance" data-label="잔고" data-chart="line" /><label for="1series0">잔고</label></li>
			    <li><input name="1series" id="1series8" class="option" type="radio" value="principal" data-label="원금" data-chart="line" /><label for="1series8">원금</label></li>
                            <li><input name="1series" id="1series2" class="option" type="radio" value="acc_flow" data-label="누적 입출 금액" data-chart="line" /><label for="1series2">누적 입출 금액</label></li>
                            <li><input name="1series" id="1series3" class="option" type="radio" value="flow" data-label="일별 입출 금액" data-chart="column" /><label for="1series3">일별 입출 금액</label></li>
                            <li><input name="1series" id="1series4" class="option" type="radio" value="daily_pl" data-label="일손익 금액" data-chart="column" /><label for="1series4">일손익 금액</label></li>
                            <li><input name="1series" id="1series5" class="option" type="radio" value="daily_pl_rate" data-label="일손익률(%)" data-chart="column" /><label for="1series5">일손익률(%)</label></li>
                            <li><input name="1series" id="1series6" class="option" type="radio" value="acc_pl" data-label="누적 수익 금액" data-chart="line" /><label for="1series6">누적 수익 금액</label></li>
                            <li><input name="1series" id="1series7" class="option" type="radio" value="acc_pl_rate" data-label="누적 수익률(%)" checked="checked" data-chart="line" /><label for="1series7">누적 수익률(%)</label></li>
                            <li><input name="1series" id="1series10" class="option" type="radio" value="dd"  data-label="현재 자본인하금액" data-chart="column" /><label for="1series10">현재 자본인하금액</label></li>
                            <li><input name="1series" id="1series11" class="option" type="radio" value="dd_rate" data-label="현재 자본인하율" data-chart="column" /><label for="1series11">현재 자본인하율(%)</label></li>
                            <li><input name="1series" id="1series14" class="option" type="radio" value="avg_pl" data-label="평균 손익 금액" data-chart="line" /><label for="1series14">평균 손익 금액</label></li>
                            <li><input name="1series" id="1series15" class="option" type="radio" value="avg_pl_rate" data-label="평균 손익률(%)" data-chart="line" /><label for="1series15">평균 손익률(%)</label></li>
                            <li><input name="1series" id="1series26" class="option" type="radio" value="winning_rate" data-label="승률" data-chart="line" /><label for="1series26">승률</label></li>
                            <li><input name="1series" id="1series28" class="option" type="radio" value="avg_pl_ratio" data-label="Profit Factor" data-chart="line" /><label for="1series28">Profit Factor</label></li>
                            <li><input name="1series" id="1series29" class="option" type="radio" value="roa"  data-label="ROA" data-chart="line" /><label for="1series29">ROA</label></li>
                            <li><input name="1series" id="1series30" class="option" type="radio" value="total_profit" data-label="totalProfit" data-chart="line" /><label for="1series30">totalProfit</label></li>
                            <li><input name="1series" id="1series31" class="option" type="radio" value="total_loss" data-label="totalLoss" data-chart="line" /><label for="1series31">totalLoss</label></li>
                        </ul>
                    </div>
                    
                    <div class="select open" style="width:160px;">
                        <div class="myValue"></div>
                        <ul class="iList">
                            <!-- <li><input name="2series" id="2series0" class="option" type="radio" value="" checked="checked" data-chart="line" /><label for="2series0">Series2 선택</label></li> -->
                            <li><input name="2series" id="2series32" class="option" type="radio" value="sm_index" data-label="기준가" data-chart="line" /><label for="2series32">기준가</label></li>
                            <li><input name="2series" id="2series0" class="option" type="radio" value="balance" data-label="잔고" data-chart="line" /><label for="2series0">잔고</label></li>
							<li><input name="2series" id="2series8" class="option" type="radio" value="principal" data-label="원금" checked="checked" data-chart="line" /><label for="2series8">원금</label></li>
                            <li><input name="2series" id="2series2" class="option" type="radio" value="acc_flow" data-label="누적 입출 금액" data-chart="line" /><label for="2series2">누적 입출 금액</label></li>
                            <li><input name="2series" id="2series3" class="option" type="radio" value="flow" data-label="일별 입출 금액" data-chart="column" /><label for="2series3">일별 입출 금액</label></li>
                            <li><input name="2series" id="2series4" class="option" type="radio" value="daily_pl" data-label="일손익 금액" data-chart="column" /><label for="2series4">일손익 금액</label></li>
                            <li><input name="2series" id="2series5" class="option" type="radio" value="daily_pl_rate" data-label="일손익률(%)" data-chart="column" /><label for="2series5">일손익률(%)</label></li>
                            <li><input name="2series" id="2series6" class="option" type="radio" value="acc_pl"  data-label="누적 수익 금액" data-chart="line" /><label for="2series6">누적 수익 금액</label></li>
                            <li><input name="2series" id="2series7" class="option" type="radio" value="acc_profit_rate" data-label="누적 수익률(%)" data-chart="line" /><label for="2series7">누적 수익률(%)</label></li>
                            <li><input name="2series" id="2series10" class="option" type="radio" value="dd" data-label="현재 자본인하금액" data-chart="column" /><label for="2series10">현재 자본인하금액</label></li>
                            <li><input name="2series" id="2series11" class="option" type="radio" value="dd_rate" data-label="현재 자본인하율" data-chart="column" /><label for="2series11">현재 자본인하율(%)</label></li>
                            <li><input name="2series" id="2series14" class="option" type="radio" value="avg_pl" data-label="평균 손익 금액" data-chart="line" /><label for="2series14">평균 손익 금액</label></li>
                            <li><input name="2series" id="2series15" class="option" type="radio" value="avg_pl_rate" data-label="평균 손익률(%)" data-chart="line" /><label for="2series15">평균 손익률(%)</label></li>
                            <li><input name="2series" id="2series26" class="option" type="radio" value="winning_rate" data-label="승률" data-chart="line" /><label for="2series26">승률</label></li>
                            <li><input name="2series" id="2series28" class="option" type="radio" value="avg_pl_ratio" data-label="Profit Factor" data-chart="line" /><label for="2series28">Profit Factor</label></li>
                            <li><input name="2series" id="2series29" class="option" type="radio" value="roa" data-label="ROA" data-chart="line" /><label for="2series29">ROA</label></li>
                            <li><input name="2series" id="2series30" class="option" type="radio" value="total_profit" data-label="totalProfit" data-chart="line" /><label for="2series30">totalProfit</label></li>
                            <li><input name="2series" id="2series31" class="option" type="radio" value="total_loss" data-label="totalLoss" data-chart="line" /><label for="2series31">totalLoss</label></li>
                        </ul>
                    </div>   
                </div>

                <div class="graph" id="strategy_graph" data-role="strategy_graph" style="width:892px;height:650px;">
                </div>
            </div>

            <div id="box" class="tab_frame">
              <iframe id="tab_frame" src="/strategies/<?php echo $strategy['strategy_id'] ?>/daily" scrolling="no"></iframe>
<script type="text/javascript">
function resize_frame(id) {
var frm = document.getElementById("tab_frame");
function resize() {
frm.style.height = "auto"; // set default height for Opera
contentHeight = frm.contentWindow.document.documentElement.scrollHeight;
frm.style.height = contentHeight + 0 + "px"; // 23px for IE7
}
if (frm.addEventListener) {
frm.addEventListener('load', resize, false);
} else {
frm.attachEvent('onload', resize);
}
}
resize_frame('tab_frame');
</script>

            </div>
        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

<!-- 문의하기 -->
<div id="ask" class="layer" style="top:1000px; left:20px; display:none;">
    <div class="layer_head">
        <p class="ask">전략 문의하기</p>
        <span class="layer_close" onclick="closeLayer('ask');">X</span>
    </div>
    
    <div class="ask_form">  
        <p id="ask_strategy_name">전략명 : ETF 레버리지/인버스(0.1억)</p>
        <!-- 브로커명 : 삼성증권 
        // 교육문의, 서비스 문의의 경우에는 p영역은 노출되지 않음 -->
		<form action="/strategies/<?php echo $strategy['strategy_id'] ?>/ask" method="post" id="ask_form">
        <fieldset>
            <legend>문의하기</legend>
            <textarea name="ask_body" id="ask_body" required="required"></textarea>
            <p class="btn_layer">
                <button type="submit" title="문의하기" class="submit"><span class="ir">문의하기</span></button>
                <button type="button" title="닫기" class="cancel" onclick="closeLayer('ask');"><span class="ir">닫기</span></button>
            </p>
        </fieldset>
		</form>
    </div>
</div>

<!-- 이미지 보기 레이어 -->
<div id="preview" class="layer" style="width:600px;display:none;">
	<div class="layer_head">
		<span class="layer_close" onclick="closeLayer('preview');">X</span>
	</div>

	<div class="layer_photo">
		<img src="" id="show_img" />
	</div>
</div>

</body>
</html>
