<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 포트폴리오</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="/js/calendar.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script src="http://code.highcharts.com/stock/highstock.js"></script>
	<script>
var isLoading = false;
var hasMore = true;
var start = 0;
var count = 10;
var countTimer;
var is_first_click = true;

var checking_start_date = '<?php echo preg_replace('/[^\d]/', '', $first_available_date) ?>';
var checking_end_date = '<?php echo preg_replace('/[^\d]/', '', $last_available_date) ?>';

Highcharts.setOptions({
	global: {
		useUTC: false
	}
});

	$(function(){
		$('#search_result').scroll(function(){
			if(isLoading) return;
			if(!hasMore) return;

			if(($('#search_result').prop("scrollHeight") - $('#search_result').height() * 3) <= ($('#search_result').height() + $('#search_result').scrollTop())){
				isLoading = true;
				loadList();
			}
		});

		$('#strategy_search_form').submit(function(){
			/*
			if(!$('#q').val()){
				$('#q').focus();
				return false;
			}
			*/
			start = 0;
			$('#search_result').html('');

			loadList();
			return false;
		});

		$('#attach_strategy_form').submit(function(){
			if($('#attach_strategy_form input[type=checkbox]:checked').length == 0){
				alert('전략을 선택해주세요');
				return false;
			}

			for(var i = 0;i<$('#attach_strategy_form input[type=checkbox]:checked').length;i++){
				if($('#portfolio_strategies tbody tr').length == 0){
					// 첫번째로 추가되는 전략인 경우
					checking_end_date = String($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('last-date'));
					$('#end_date').val(checking_end_date);

					checking_start_date = String($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('first-date'));
					$('#start_date').val(checking_start_date);
				}else{
					if(parseInt(checking_end_date) < parseInt($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('last-date'))){
						checking_end_date = String($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('last-date'));
						$('#end_date').val(checking_end_date);
					}

					if(parseInt(checking_start_date) > parseInt($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('first-date'))){
						checking_start_date = String($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('first-date'));
						$('#start_date').val(checking_start_date);
					}

					if(checking_start_date > checking_end_date){
						checking_start_date = String($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('first-date'));
						$('#start_date').val(checking_start_date);
					}
				}

				if($('#percent'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('strategy-id')).length){
					continue;
				}

				var html = '';
					html += '<tr>';
						var first_date = String($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('first-date'));
						var end_date = String($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('last-date'));
                        html += '<td class="first"><a href="/strategies/'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('strategy-id')+'" target="_blank">'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('name')+'</a></td>';
			if ($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('companylogo')){
	                        html += '<td><img width="120" src="'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('companylogo')+'" /></td>';
			} else {
	                        html += '<td>'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('company')+'</td>';
			}
			
			if ($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('toollogo')) {
                        	html += '<td><img src="'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('toollogo')+'" /></td>';
			} else {
                        	html += '<td>'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('tool')+'</td>';
			}
						html += '<td>'+first_date+'~<br />' + end_date+'</td>';
						if($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('percent') > 0){
                        html += '<td><span class="plus">'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('percent')+'%</span></td>';
						}else if($($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('percent') < 0){
						html += '<td><span class="minus">'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('percent')+'%</span></td>';
						}else{
						html += '<td><span>'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('percent')+'%</span></td>';
						}
                        html += '<td>';
                            html += '<p class="ratio">';
                                html += '<a onmousedown="continueMinus(\'percent'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('strategy-id')+'\')" onmouseup="stopMinus(\'percent'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('strategy-id')+'\')" class="subtraction">-</a>';
                                html += '<input name="percents[]" id="percent'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('strategy-id')+'" type="text" title="비율" value="0%"  />';
								html += '<input type="hidden" name="strategy_ids[]" value="'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).val()+'">';
                                html += '<a onmousedown="continuePlus(\'percent'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('strategy-id')+'\')" onmouseup="stopPlus(\'percent'+$($('#attach_strategy_form input[type=checkbox]:checked')[i]).data('strategy-id')+'\')" class="add">+</a>';
                            html += '</p>';
                        html += '</td>';
                        html += '<td class="delete"><button type="button" onclick="$(this).parents(\'tr\').remove();" title="삭제" class="delete"><span class="ir">삭제</span></button></td>';
                    html += '</tr>';
				$('#portfolio_strategies tbody').append(html);

			}

			$('#search_result').html('');

			closeLayer('add_portfolio');

			return false;
		});

		$('#portfolio_form').submit(function(){
			if(!$('#name').val()){
				$('#name').focus();
				alert('이름을 입력하세요');
				return false;
			}

			if(!$('#start_date').val()){
				$('#start_date').focus();
				alert('시작일을 입력하세요');
				return false;
			}

			if(!$('#end_date').val()){
				$('#end_date').focus();
				alert('종료일을 입력하세요');
				return false;
			}

			if(!$('#amount').val()){
				$('#amount').focus();
				alert('금액을 입력하세요');
				return false;
			}

			var percents_array = [];
			var inputs = $('#portfolio_form input');
			
			for(var i =0;i<inputs.length;i++){
				if($(inputs[i]).attr('name') == 'percents[]'){
					percents_array.push($(inputs[i]).val().replace(/[^\d-]+/g, ''));
				}
			}

			if(percents_array.length == 0){
				alert('전략을 선택하세요');
				return false;
			}

			if(percents_array.length > 10){
				alert('10개까지 가능합니다');
				return false;
			}

			var total_percent = 0;
			for(var i=0;i<percents_array.length;i++){
				total_percent += parseInt(percents_array[i]);
			}

			if(total_percent != 100){
				alert('합이 100%이어야 합니다');
				return false;
			}

			if(parseInt($('#start_date').val().replace(/[^\d-]+/g, '')) < parseInt(checking_start_date)){
				alert('시작일은 ' + checking_start_date + ' 이후로 설정해주세요');
				return false;
			}

			if(parseInt($('#end_date').val().replace(/[^\d-]+/g, '')) > parseInt(checking_end_date)){
				alert('종료일은 ' + checking_end_date + ' 이전으로 설정해주세요');
				return false;
			}

			return true;
		});
	});

function continueMinus(el_id){
	minus(el_id);
	if(is_first_click){
		is_first_click = false;
		countTimer = setTimeout(function(){ continueMinus(el_id) }, 1000);
	}else{
		countTimer = setTimeout(function(){ continueMinus(el_id) }, 100);
	}
}

function stopMinus(el_id){
	is_first_click = true;
	clearTimeout(countTimer);
}

function continuePlus(el_id){
	plus(el_id);
	if(is_first_click){
		is_first_click = false;
		countTimer = setTimeout(function(){ continuePlus(el_id) }, 1000);
	}else{
		countTimer = setTimeout(function(){ continuePlus(el_id) }, 100);
	}
}

function stopPlus(el_id){
	is_first_click = true;
	clearTimeout(countTimer);
}

function loadList(){
	var html = '';
	if($('.loading').length == 0) $('#search_result').append('<div class="loading"><img src="/img/loading.gif" /></div>');
	$.getJSON('/portfolios/strategies', {format:'json', q:$('#q').val(), start:start, count:count}, function(data){
		/*
		if(data.items.length > 0){
			$('.no_result').remove();
		}
		*/
		$('.loading').remove();

		$.each(data.items, function(key, val){
			html += '<dl>';
				html += '<dd class="check"><p><input type="checkbox" name="strategy_ids[]" id="choice'+val.strategy_id+'" value="'+val.strategy_id+'" data-name="'+escapedHTML(val.name)+'" data-companylogo="'+val.broker.logo+'" data-company="'+escapedHTML(val.broker.company)+'" data-toollogo="'+val.system_tool.logo+'" data-tool="'+escapedHTML(val.system_tool.name)+'" data-percent="'+val.daily_values[val.daily_values.length - 1].acc_pl_rate+'"  data-first-date="'+val.first_date+'" data-last-date="'+val.last_date+'" data-strategy-id="'+val.strategy_id+'" /><label for="choice'+val.strategy_id+'"></label></p></dd>';
				html += '<dt>';
					if(val.strategy_type == 'M'){
					html += '<img src="/img/ico_menual.gif" /> ';
					}else if(val.strategy_type == 'S'){
					html += '<img src="../img/ico_system.gif" /> ';
					}else if(val.strategy_type == 'H'){
					html += '<img src="../img/ico_hybrid.gif" /> ';
					}
					for(var i=0;i<val.items.length;i++){
					html += '<img src="'+val.items[i].icon+'" alt="'+escapedHTML(val.items[i].name)+'" /> ';
					}

					html += '<br/><h6>'+escapedHTML(val.name)+'</h6>';
					
					html += '<dl class="tool">';
						html += '<dt>증권사</dt>';
						html += '<dd>';
						if(val.broker.logo){
							html += '<img src="'+val.broker.logo+'" />';
						}else{
							html += escapedHTML(val.broker.company);
						}
						html += '</dd>';
						html += '<dt>매매툴</dt>';
						html += '<dd>';
						if(val.system_tool.logo){
							html += '<img src="' + val.system_tool.logo +'" />';
						}else{
							html += escapedHTML(val.system_tool.name);
						}
						html += '</dd>';
					html += '</dl>';
				html += '</dt>';
				html += '<dd class="graph">';
					html += '<div id="strategy_graph'+val.strategy_id + '" data-role="strategy_graph" data-graph-data="'+val.str_sm_index+'" style="width:70px;height:70px;"></div>';
				html += '</dd>';
				html += '<dd class="profit">';
					html += '누적 수익률';
					html += '<br />';
					if(val.daily_values[val.daily_values.length - 1].acc_pl_rate > 0){
						html += '<span class="plus">'+ Math.round(val.daily_values[val.daily_values.length - 1].acc_pl_rate*100)/100 + '%</span>';
					}else if(val.daily_values[val.daily_values.length - 1].acc_pl_rate > 0){
						html += '<span class="minus">'+ Math.round(val.daily_values[val.daily_values.length - 1].acc_pl_rate*100)/100 + '%</span>';
					}else{
						html += '<span>' + Math.round(val.daily_values[val.daily_values.length - 1].acc_pl_rate*100)/100 + '%</span>';
					}
				html += '</dd>'; 
				html += '<dd class="profit last">';
					html += '평균 수익률';
					html += '<br />';
					html += '<span>'+ val.daily_values[val.daily_values.length - 1].avg_pl_rate + '%</span>';
				html += '</dd>';
			html += '</dl>';
		});

		$('#search_result').append(html);

		start = start + count;
		isLoading = false;

		if(data.items.length < count) hasMore = false;

		$('div').each(function(){
			if($(this).data('role') == 'strategy_graph' && !$(this).data('loaded')){
				loadGraph($(this).attr('id'));
			}
		});
	});
}

$(function () {
    var seriesOptions = [],
        // create the chart when all data is loaded
        createChart = function () {

            $('#graph').highcharts('StockChart', {

                rangeSelector: {
                    selected: 5,
					inputEnabled : false
                },

                yAxis: {
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
                    }]
                },

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

		<?php 
		$cnt = 0;
		foreach($strategies as $k => $strategy){ ?>
		seriesOptions[<?php echo $k+1 ?>] = {
			name: '<?php echo htmlspecialchars($strategy['name']) ?>',
			data: <?php echo $strategy['str_c_price']; ?>
			<?php $cnt++; ?>
		};
		<?php } ?>

		<?php if($cnt>0) { ?>
		seriesOptions[0] = {
			name: '포트폴리오 기준가',
			data: <?php echo $str_unified_sm_index; ?>,
                        type: 'areaspline',
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
		<?php } ?>

        createChart();

		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>
});

function showResult(){
	// $('#result_data').show();
	var percents_array = [];
	var strategy_ids_array = [];
	var inputs = $('#portfolio_form input');

	if(inputs.length == 0){
		alert('전략을 선택하세요');
		return false;
	}

	for(var i =0;i<inputs.length;i++){
		if($(inputs[i]).attr('name') == 'percents[]'){
			if($(inputs[i]).val().replace(/[^\d-]+/g, '') == '0'){
				alert('비율을 설정하세요');
				return false;
			}
			percents_array.push($(inputs[i]).val().replace(/[^\d-]+/g, ''));
			continue;
		}else if($(inputs[i]).attr('name') == 'strategy_ids[]'){
			strategy_ids_array.push($(inputs[i]).val().replace(/[^\d-]+/g, ''));
			continue;
		}
	}

	var total_percent = 0;
	for(var i=0;i<percents_array.length;i++){
		total_percent += parseInt(percents_array[i]);
	}

	if(total_percent != 100){
		alert('비율의 합이 100%이어야 합니다');
		return false;
	}

	if(parseInt($('#start_date').val().replace(/[^\d-]+/g, '')) < parseInt(checking_start_date)){
		alert('시작일은 ' + checking_start_date + ' 이후로 설정해주세요');
		return false;
	}

	if(parseInt($('#end_date').val().replace(/[^\d-]+/g, '')) > parseInt(checking_end_date)){
		alert('종료일은 ' + checking_end_date + ' 이전으로 설정해주세요');
		return false;
	}

	location.href = '?name='+encodeURIComponent($('#name').val())+'&start_date='+$('#start_date').val()+'&end_date='+$('#end_date').val()+'&amount='+$('#amount').val()+'&percents='+percents_array.join()+'&strategy_ids='+strategy_ids_array.join();
}

function showPopupPortfolio(){
	showLayer2('add_portfolio');
	start = 0;
	loadList();
}
	</script>
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <a href="/portfolios" class="btn_list">목록으로</a>
        <div id="content" class="view">
			<form action="/portfolios/create" method="post" id="portfolio_form">
            <div class="portfolio_info">
                <div id="mod_name">
                    <input id="name" name="name" type="text" title="포트폴리오 명" value="<?php echo htmlspecialchars($name) ?>" required="required"  />
                    <!-- <button onclick="" name="" title="저장"><span class="ir">저장</span></button>  -->
                </div>
                <button type="submit" title="포트폴리오 저장" class="portfolio_save"><span class="ir">포트폴리오 저장</span></button>  
            </div>

            <table border="0" cellspacing="0" cellpadding="0" class="portfolio" id="portfolio_strategies">
                <col width="*" /> <col width="130" /><col width="130" /> <col width="100" /><col width="100" /> <col width="140" /><col width="40" />
                <thead>
                    <tr>
                        <td class="first">전략명</td>
                        <td>증권사/선물사</td>
                        <td>매매툴</td>
						<td>운용기간</td>
                        <td>누적 수익률</td>
                        <td>비율</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
					<?php foreach($strategies as $strategy){ ?>
                    <tr>
                        <td class="first"><a href="/strategies/<?php echo htmlspecialchars($strategy['strategy_id']) ?>" target="_blank"><?php echo htmlspecialchars($strategy['name']) ?></a></td>
                        <td><?php if(!empty($strategy['broker']['logo_s'])){ ?><img src="<?php echo $strategy['broker']['logo_s'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['broker']['company']) ?><?php } ?></td>
                        <td><?php if(!empty($strategy['system_tool']['logo'])){ ?><img src="<?php echo $strategy['system_tool']['logo'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['system_tool']['name']) ?><?php } ?></td>
						<td><?php echo $strategy['daily_values'][0]['basedate']; ?>~
						<br/><?php  echo $strategy['daily_values'][count($strategy['daily_values'])-1]['basedate'] ?></td>
                        <td>
						<?php if($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'] > 0){
						?>
						<span class="plus"><?php echo $strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'] ?>%</span>
						<?php }else if($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'] < 0){ ?>
						<span class="minus"><?php echo $strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'] ?>%</span>
						<?php }else{ ?>
						<span><?php echo $strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'] ?>%</span>
						<?php } ?>
						</td>
                        <td>
                            <p class="ratio">
                                <a onmousedown="continueMinus('percent<?php echo htmlspecialchars($strategy['strategy_id']) ?>')" onmouseup="stopMinus('percent<?php echo htmlspecialchars($strategy['strategy_id']) ?>')" class="subtraction">-</a>
                                <input type="text" id="percent<?php echo htmlspecialchars($strategy['strategy_id']) ?>" name="percents[]" title="비율" value="<?php echo htmlspecialchars($strategy['percents']) ?>%"  />
								<input type="hidden" name="strategy_ids[]" value="<?php echo htmlspecialchars($strategy['strategy_id']) ?>" />
                                <a onmousedown="continuePlus('percent<?php echo htmlspecialchars($strategy['strategy_id']) ?>')" onmouseup="stopPlus('percent<?php echo htmlspecialchars($strategy['strategy_id']) ?>')" class="add">+</a>
                            </p>
                        </td>
                        <td class="delete"><button type="button" onclick="$(this).parents('tr').remove();" title="삭제" class="delete"><span class="ir">삭제</span></button></td>
                    </tr>
					<?php } ?>
                </tbody>
            </table>
            <p class="portfolio_add"><a href="#" onclick="showPopupPortfolio();return false;">+ 추가하기</a></p>
            
            <div class="condition">
            <fieldset>      
                <dl>
                    <dt>기간 설정:</dt>
                    <dd>
                        <input id="start_date" name="start_date" type="text" title="시작" class="datepicker" value="<?php echo getDateString($start_date) ?>" required="required" />  ~
                        <input id="end_date" name="end_date" type="text" title="종료" class="datepicker" value="<?php echo getDateString($end_date) ?>" required="required" /> 
                    </dd>
                    <dt>금액:</dt>
                    <dd>
                        <input id="amount" name="amount" type="text" title="금액" value="<?php echo htmlspecialchars($amount) ?>" style="width:115px;" required="required" onkeyup="inputNumberFormat(this)" /> 
                    </dd>
                    <dd>
                        <button type="button" onclick="showResult();" title="결과보기" class="act"><span class="ir">결과보기</span></button>
                    </dd>
                </dl>
            </fieldset>
            </div>
			</form>
            
            <!-- 결과보기 버튼 클릭 후 나타나는 영역 -->

            <div id="result_data" class="result_data"<?php if(count($strategies) == 0) echo ' style="display:none;"' ?>>
                <div class="graph" id="graph" style="width:892px;height:500px;">
                </div>
                <p class="line"></p>
                <table border="0" cellspacing="1" cellpadding="0">
                <tbody>
                    <tr>
                        <td class="thead">누적수익률</td>
                        <td><b><?php echo $portfolio_total_profit_rate ?>%</b></td>
                        <td class="thead">누적수익금액</td>
                        <td><b><?php echo number_format($portfolio_total_profit) ?></b></td>
                    </tr>
                </tbody>
                </table>

                <table border="0" cellspacing="1" cellpadding="0">
                <col width="*" /><col width="135" /><col width="135" /><col width="135" /><col width="135" /><col width="135" />
                <thead>
                    <tr>
                        <td>상품명</td>
                        <td>비율</td>
                        <td>운용기간</td>
                        <td>누적 수익률</td>
                        <td>평균 손익률</td>
                        <td>승률</td>
                    </tr>
                </thead>
                <tbody>
					<?php foreach($strategies as $strategy){ ?>
                    <tr>
                        <td class="thead first"><?php echo htmlspecialchars($strategy['name']) ?></td>
                        <td><?php echo $strategy['percents'] ?>%</td>
                        <td>
						<?php
						if(count($strategy['daily_values'])){
							$last_time = strtotime($strategy['daily_values'][count($strategy['daily_values'])-1]['basedate']);
							$first_time = strtotime($strategy['daily_values'][0]['basedate']);
							if($last_time - $first_time > 0){
								if(floor(($last_time - $first_time)/(60*60*24*30*12)) > 0) echo floor(($last_time - $first_time)/(60*60*24*30*12)).'년 ';
								if(ceil((($last_time - $first_time)%(60*60*24*30*12))/(60*60*24*30)) > 0) 
									echo ceil((($last_time - $first_time)%(60*60*24*30*12))/(60*60*24*30)).'월';
							}
						}
						?>
						</td>
                        <td><?php if(count($strategy['daily_values'])) echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2); else echo 0 ?>%</td>
                        <td><?php if(count($strategy['daily_values'])) echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate'],2); else echo 0 ?>%</td>
                        <td><?php if(count($strategy['daily_values'])) echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['winning_rate'],2); else echo 0 ?>%</td>
                    </tr>
					<?php } ?>
                </tbody>
                </table>
            </div>            

            <!-- //결과보기 버튼 클릭 후 나타나는 영역 -->
            
            
            <!-- 포트폴리오 추가 레이어 : 브라우저 높이 계산후 그 값에서 -20px 뺀 값을 add_portfolio 높이값으로 지정해 주어야 함.  -->
            <div id="add_portfolio" class="layer" style="display:none;">
                <button type="button" title="닫기" onclick="closeLayer('add_portfolio');" class="close"><span class="ir">닫기</span></button>
                <form action="/strategies" method="get" id="strategy_search_form">
                      <fieldset class="search"> 
                         <b>전략 검색 : &nbsp;&nbsp;</b> 
                          <input id="q" name="q" type="text" title="전략명 입력" value="" required="required" />
                          <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
                      </fieldset>
                </form>

				<form action="/" method="post" id="attach_strategy_form">
                <table border="0" cellspacing="0" cellpadding="0" class="list_head">
                    <thead>
                    <tr>
                        <td style="width:72px;">순위</td>
                        <td style="width:300px;">기본정보</td>
                        <td style="width:112px;">분석</td>
                        <td>수익률</td>
                    </tr>
                    </thead>
                </table>
                <div class="add_list" id="search_result">
                    <!-- 기본 10개 불러오고 스크롤 하면서 10개씩 추가로 불러옴 -->
					<div class="loading">
                        <img src="/img/loading.gif" />
                    </div>
                </div>
                
                <div class="btn_area">
                    <button type="submit" title="포트폴리오에 추가" class="submit"><span class="ir">포트폴리오에 추가</span></button>
                </div>
				</form>
            </div>
            <!-- //포트폴리오 추가 레이어 -->
        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
