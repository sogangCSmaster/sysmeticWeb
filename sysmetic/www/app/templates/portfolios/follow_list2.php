<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 관심전략</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script>
var isLoading = false;
var hasMore = true;
var start = <?php echo $start + $count ?>;
var count = <?php echo $count ?>;

function loadList(){
	if($('.loading').length == 0) $('#container_lanking_list').append('<div class="loading"><img src="/img/loading.gif" /></div>');

	$.getJSON('/followings/<?php echo date('Ymd') ?>', {format:'json', item:$('input[name=item]:checked').val(), term:$('input[name=term]:checked').val(), sort:'<?php echo $sort ?>', start:start, count:count, sort_by:'<?php echo $sort_by ?>'}, function(data){
		var html = '';
		/*
		if(data.items.length > 0){
			$('.no_result').remove();
		}
		*/
		$('.loading').remove();

		$.each(data.items, function(key, val){
            html += '<div class="lanking_list">';
                html += '<dl>';
                    html += '<dt>';
                        if(val.strategy_type == 'M'){
						html += '<img src="/img/ico_menual.gif" /> ';
						}else if(val.strategy_type == 'S'){
						html += '<img src="../img/ico_system.gif" /> ';
						}

						if(val.strategy_term == 'day'){
						html += '<img src="/img/ico_day.gif" /> ';
						}else if(val.strategy_term == 'position'){
						html += '<img src="../img/ico_position.gif" /> ';
						}

						for(var i=0;i<val.items.length;i++){
                        html += '<img src="'+val.items[i].icon+'" alt="'+escapedHTML(val.items[i].name)+'" /> ';
						}

                        html += '<h6><a href="/strategies/'+val.strategy_id +'">'+escapedHTML(val.name)+'</a></h6>';
                        
                        html += '<dl class="tool">';
                            html += '<dt>트레이더</dt>';
                            html += '<dd>';
							if(val.developer.picture){
								html += '<img src="/img/over_s1.png" class="over" />';
								html += '<img src="'+val.developer.picture+'" class="trader" /> ';
							}
							if(!val.developer.nickname){
								html += escapedHTML(val.developer_name);
							}else{
								html += escapedHTML(val.developer.nickname);
							}
							html += '</dd>';
                            html += '<dt>증권사</dt>';
                            html += '<dd>';
							if(val.broker.logo_s){
								html += '<img src="'+val.broker.logo_s+'" />';
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
						html += '<div id="strategy_graph'+val.strategy_id + '" data-role="strategy_graph" data-graph-data="'+val.str_c_price+'" style="width:127px;height:120px;"></div>';
                    html += '</dd>';
                    html += '<dd class="profit">';
                        html += '<dl class="tool">';
                            html += '<dt>누적 수익률</dt>';
                            html += '<dd>';
							if(val.daily_values[val.daily_values.length - 1]){
								html += comma(val.daily_values[val.daily_values.length - 1].total_profit_rate);
							}else{
								html += 0;
							}
							html += '%</dd>';
                            html += '<dt>연간 수익률</dt>';
                            html += '<dd>';
							if(val.yearly_profit_rate['<?php echo date('Y') ?>']){
								html += val.yearly_profit_rate['<?php echo date('Y') ?>'] + '%';
							}else{
								html += '0%';
							}
							html += '</dd>';
                            html += '<dt><b>원금</b>('+val.currency + ')</dt>';
                            html += '<dd>';
							if(val.daily_values[val.daily_values.length - 1]){
								html += comma(val.daily_values[val.daily_values.length - 1].principal);
							}else{
								html += 0;
							}
							html += '</dd>';
                        html += '</dl>';
                    html += '</dd>';
                    html += '<dd class="mdd">';
					html += '<i>' + val.currency +'</i><br />';
						if(val.daily_values[val.daily_values.length - 1] && val.daily_values[val.daily_values.length - 1].mdd) html += val.daily_values[val.daily_values.length - 1].mdd;
						else html += '0';
						html += '<br />';					
					html += '</dd>';
                    html += '<dd class="self">';
						if(val.daily_values[val.daily_values.length - 1] && val.daily_values[val.daily_values.length - 1].sharp_ratio) html += val.daily_values[val.daily_values.length - 1].sharp_ratio;
						else html += '0';
					html += '</dd>';

					html += '<dd class="followers">Followers<br /><b id="follows_count'+val.strategy_id+'">' + comma(val.followers_count) +'</b></dd>';
                html += '</dl>';
				
				<?php if($isLoggedIn()){ ?>
				if(val.is_following){
				html += '<button type="button" title="UnFollow" class="unfollow" data-role="unfollow" data-strategy-id="' +val.strategy_id +'"><span class="ir">Unfollow</span></button>';
				}else{
				html += '<button type="button" title="Follow" class="follow" data-role="follow" data-strategy-id="'+val.strategy_id+'"><span class="ir">Follow</span></button>';
				}
				<?php }else{ ?>
				html += '<button type="button" title="Follow" class="follow" data-role="follow" data-strategy-id="'+val.strategy_id+'" onclick="showLayer(\'login_layer\');"><span class="ir">Follow</span></button>';
				<?php } ?>
            html += '</div>';
		});

		$('#container_lanking_list').append(html);

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
	$('div').each(function(){
		if($(this).data('role') == 'strategy_graph' && !$(this).data('loaded')){
			loadGraph($(this).attr('id'));
		}
	});

	$(window).scroll(function(){
		if(isLoading) return;
		if(!hasMore) return;

		if(($(document).height() - $(window).height() * 3) <= ($(window).height() + $(window).scrollTop())){
			isLoading = true;
			loadList();
		}
	});

	$('body').on('click', 'button', function(){
		if($(this).data('role') == 'follow'){
			var btn_el = $(this);
			$.get('/strategies/'+$(this).data('strategy-id')+'/follow', {type:'json'}, function(data){
				if(data.result){
					btn_el.attr('title', 'UnFollow').attr('class', 'unfollow').data('role', 'unfollow').html('<span class="ir">Unfollow</span>');
					$('#follows_count'+btn_el.data('strategy-id')).text(parseInt($('#follows_count'+btn_el.data('strategy-id')).text()) + 1);
				}else{
				}
			}, 'json');
		}else if($(this).data('role') == 'unfollow'){
			var btn_el = $(this);
			$.get('/strategies/'+$(this).data('strategy-id')+'/unfollow', {type:'json'}, function(data){
				if(data.result){
					btn_el.attr('title', 'Follow').attr('class', 'follow').data('role', 'follow').html('<span class="ir">Follow</span>');
					$('#follows_count'+btn_el.data('strategy-id')).text(parseInt($('#follows_count'+btn_el.data('strategy-id')).text()) - 1);
				}else{
				}
			}, 'json');
		}

		return false;
	});
});
	</script>
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content">
                <?php if(count($strategies) == 0){ ?>
            <div class="no_data">
                <p>관심전략이 없습니다.</p><br />
                전략을 Follow 하면 관심 전략에서 확인할 수 있습니다.
            </div>
            <?php }else{ ?>

            <!-- 데이터형 목록 -->
            <div id="type_data" class="my_list" style="display:block;">
                <div class="select_area">
                    <div class="select open" style="width:130px;">
                        <div class="myValue"></div>
                        <ul class="iList">
                            <li><input name="view" id="view0" class="option" type="radio" value="목록형 보기"  onfocus="location.href='/followings/<?php echo date("Ymd")?>';" /><label for="view0">목록형 보기</label></li>
                            <li><input name="view" id="view1" class="option" type="radio" value="데이터형 보기" checked="checked" /><label for="view1">데이터형 보기</label></li>
                        </ul>
                    </div>
                </div>


                <div class="navi">
                    <a href="/followings2/<?php echo date("Ymd", strtotime($basedate . " - 7 day")) ?>"  class="prev on">◀</a>
                    <b class="term">
                        <?php
                                $week = ceil((date("Ymd", strtotime($basedate))- date("Ym01", strtotime($basedate)))/7);
                                if($week == 0) $week = 1;

                                $week_str = date("o년 n월 ", strtotime($basedate));
                                $week_str .= $week."주";
                                echo $week_str;
                        ?>
                    </b>
                    <?php
                        if(date('Ymd') >= $basedate){
                                echo '<a href="/followings2/'.date("Ymd", strtotime($basedate . " + 7 day")).'"  class="next on">▶</a>';
                        }
                        else {
                                echo '<a href="#" class="next off">▶</a>';
                        }
                    ?>
                </div>

                <table border="0" cellspacing="1" cellpadding="0">
                <col width="90" /><col width="90" /><col width="90" /><col width="90" /><col width="90" /><col width="90" /><col width="90" />
                <col width="100" /><col width="100" />
                    <thead>
                    <tr>
                        <td colspan="7">일간손익</td>
                        <td rowspan="2">주간손익</td>
                        <td rowspan="2">누적손익</td>
                    </tr>
                    <tr class="date">
                        <td><?php $d_6=date("n/j", strtotime($basedate . " - 6 day")); echo $d_6 ?></td>
                        <td><?php $d_5=date("n/j", strtotime($basedate . " - 5 day")); echo $d_5 ?></td>
                        <td><?php $d_4=date("n/j", strtotime($basedate . " - 4 day")); echo $d_4 ?></td>
                        <td><?php $d_3=date("n/j", strtotime($basedate . " - 3 day")); echo $d_3 ?></td>
                        <td><?php $d_2=date("n/j", strtotime($basedate . " - 2 day")); echo $d_2 ?></td>
                        <td><?php $d_1=date("n/j", strtotime($basedate . " - 1 day")); echo $d_1 ?></td>
                        <td><?php $d=date("n/j", strtotime($basedate)); echo $d ?></td>
                    </tr>
		    </thead>
                    <tbody>

                    <?php foreach($strategies as $strategy){ ?>
                    <tr>
                        <td colspan="9" class="name">
                            <?php if($strategy['strategy_type'] == 'M'){ ?>
                            <img src="/img/ico_menual.gif" />
                            <?php }else if($strategy['strategy_type'] == 'S'){ ?>
                            <img src="/img/ico_system.gif" />
                            <?php } ?>

                            <?php if($strategy['strategy_term'] == 'day'){ ?>
                            <img src="/img/ico_day.gif" />
                            <?php }else if($strategy['strategy_term'] == 'position'){ ?>
                            <img src="../img/ico_position.gif" />
                            <?php } ?>

                            <?php foreach($strategy['items'] as $v){ ?>
                            <img src="<?php echo $v['icon'] ?>" alt="<?php echo htmlspecialchars($v['name']) ?>" />
                            <?php } ?>

                            &nbsp; <a href="/strategies/<?php echo $strategy['strategy_id'] ?>"><?php echo htmlspecialchars($strategy['name']) ?></a>

                            <button id="" type="button" title="UnFollow" class="unfollow2" onclick="location.href='/followings2/<?php echo $strategy['strategy_id'] ?>/unfollow';"><span class="ir">Unfollow</span></button>

                        </td>
                    </tr>
		    <tr>
			<td><span class="<?php echo getSignClass($strategy['weekly_profit_values'][$d_6],'true') ?>"><?php echo number_format($strategy['weekly_profit_values'][$d_6]) ?></span></td>
			<td><span class="<?php echo getSignClass($strategy['weekly_profit_values'][$d_5],'true') ?>"><?php echo number_format($strategy['weekly_profit_values'][$d_5]) ?></span></td>
			<td><span class="<?php echo getSignClass($strategy['weekly_profit_values'][$d_4],'true') ?>"><?php echo number_format($strategy['weekly_profit_values'][$d_4]) ?></span></td>
			<td><span class="<?php echo getSignClass($strategy['weekly_profit_values'][$d_3],'true') ?>"><?php echo number_format($strategy['weekly_profit_values'][$d_3]) ?></span></td>
			<td><span class="<?php echo getSignClass($strategy['weekly_profit_values'][$d_2],'true') ?>"><?php echo number_format($strategy['weekly_profit_values'][$d_2]) ?></span></td>
			<td><span class="<?php echo getSignClass($strategy['weekly_profit_values'][$d_1],'true') ?>"><?php echo number_format($strategy['weekly_profit_values'][$d_1]) ?></span></td>
			<td><span class="<?php echo getSignClass($strategy['weekly_profit_values'][$d],'true') ?>"><?php echo number_format($strategy['weekly_profit_values'][$d]) ?></span></td>
			<?php
				$weeklySum = $strategy['weekly_profit_values'][$d_6]; 
				$weeklySum += $strategy['weekly_profit_values'][$d_5]; 
				$weeklySum += $strategy['weekly_profit_values'][$d_4]; 
				$weeklySum += $strategy['weekly_profit_values'][$d_3]; 
				$weeklySum += $strategy['weekly_profit_values'][$d_2]; 
				$weeklySum += $strategy['weekly_profit_values'][$d_1]; 
				$weeklySum += $strategy['weekly_profit_values'][$d]; 
			?>
			<td><span class="<?php echo getSignClass($weeklySum,'true') ?>"><?php echo number_format($weeklySum) ?></span></td>
			<td><span class="<?php echo getSignClass($strategy['daily_values'][count($strategy['daily_values'])-1]['total_profit'],'true') ?>"><?php echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['total_profit']) ?></span></td>
			
		    </tr>
                    <?php } ?>
                </table>

            </div>
            <?php } ?>
            <!-- //데이터형 목록 -->

            </div>
    </div>

	<?php require_once('footer.php') ?>

</body>
</html>
