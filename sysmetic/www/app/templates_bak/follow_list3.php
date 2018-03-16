<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 전략랭킹</title>	
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


	var params = {format:'json', item:$('input[name=item]:checked').val(), term:$('input[name=term]:checked').val(), sort:'<?php echo $sort ?>', start:start, count:count, sort_by:'<?php echo $sort_by ?>'};

	if(is_extend_search){
		params = 'format=json&start='+start+'&count='+count +'&sort='+'<?php echo $sort ?>' + '&sort_by=' + '<?php echo $sort_by ?>' +'&' + $('#extend_search_form').serialize();
	}

	$.getJSON('/strategies', params, function(data){
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
						}else if(val.strategy_type == 'H'){
						html += '<img src="../img/ico_hybrid.gif" /> ';
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
								html += Math.round(val.daily_values[val.daily_values.length - 1].acc_pl_rate);
							}else{
								html += 0;
							}
							html += '%</dd>';
                            html += '<dt>연간 수익률</dt>';
                            html += '<dd>';
							if(val.yearly_profit_rate){
								html += val.yearly_profit_rate + '%';
							}else{
								html += '0%';
							}
							html += '</dd>';
                            html += '<dt><b>원금</b>('+val.currency + ')</dt>';
                            html += '<dd>';
							if(val.daily_values[val.daily_values.length - 1]){
								html += comma(Math.round(val.daily_values[val.daily_values.length - 1].principal));
							}else{
								html += 0;
							}
							html += '</dd>';
							if(val.total_funding>0) {
                            html += '<dt class="funding">펀딩금액</dt>';
                            html += '<dd class="funding">' + comma(val.total_funding);
							html += '</dd>';
							}
                        html += '</dl>';
                    html += '</dd>';
                    html += '<dd class="mdd">';
					html += '<i>' + val.currency +'</i><br />';
						if(val.daily_values[val.daily_values.length - 1] && val.daily_values[val.daily_values.length - 1].mdd) html += comma(Math.round(val.daily_values[val.daily_values.length - 1].mdd));
						else html += '0';
						html += '<br />';					
					html += '</dd>';
                    html += '<dd class="self">';
						if(val.daily_values[val.daily_values.length - 1] && val.daily_values[val.daily_values.length - 1].sm_score) html += Math.round(val.daily_values[val.daily_values.length - 1].sm_score*100)/100;
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
				html += '<button type="button" title="Follow" class="follow" onclick="showLayer(\'login_layer\');"><span class="ir">Follow</span></button>';
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
	$('input[name=item]').on('click', function(){
		/*
		$('#container_lanking_list').html('');
		start = 0;
		loadList();
		*/
		location.href = '/strategies?item=' + $('input[name=item]:checked').val() + '&term=' + $('input[name=item]:checked').val();
	});

	$('input[name=term]').on('click', function(){
		/*
		$('#container_lanking_list').html('');
		start = 0;
		loadList();
		*/
		location.href = '/strategies?item=' + $('input[name=item]:checked').val() + '&term=' + $('input[name=term]:checked').val();
	});

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

	$('#container_lanking_list').on('click', 'button', function(){
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

            <div id="type_list" style="">
                <div class="select_area">
                    <div class="select open" style="width:130px;">
                        <div class="myValue"></div>
                        <ul class="iList">
                            <li><input name="view" id="view0" class="option" type="radio" value="목록형 보기" checked="checked" /><label for="view0">목록형 보기</label></li>
                            <li><input name="view" id="view1" class="option" type="radio" value="데이터형 보기" onfocus="location.href='/followings2/<?php echo date("Ymd")?>';" /><label for="view1">데이터형 보기</label></li>
                        </ul>
                    </div>
                </div>



            <table border="0" cellspacing="0" cellpadding="0" class="list_head">
                <thead>
                <tr>
                    <td style="width:80px;">순위</td>
                    <td style="width:150px;">전략</td>
                    <td style="width:107px;">분석</td>
                    <td style="width:243px;"><a href="?sort=total_profit_rate" class="sorting">수익률</a></td>
                    <td style="width:139px;"><a href="?sort=mdd&amp;sort_by=<?php if($sort_by == 'asc') echo 'desc'; else 
					echo 'asc'; ?>" class="sorting">MDD</a></td>
                    <td style="width:79px;"><a href="?sort=sharp_ratio" class="sorting">SM Score</a></td>
                    <td><a href="?sort=followers_count" class="sorting">팔로워</a></td>
                </tr>
                </thead>
            </table>

			<div id="container_lanking_list">
			<?php if(count($strategies)){ ?>
			<?php foreach($strategies as $strategy){ ?>
            <div class="lanking_list">
                <dl>
                    <dt>
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

                        <h6><a href="/strategies/<?php echo $strategy['strategy_id'] ?>"><?php echo htmlspecialchars($strategy['name']) ?></a></h6>
                        
                        <dl class="tool">
                            <dt>트레이더</dt>
                            <dd><?php if(!empty($strategy['developer']['picture'])){ ?><img src="/img/over_s1.png" class="over" /><img src="<?php echo htmlspecialchars($strategy['developer']['picture']) ?>" class="trader" /> <?php } ?><?php if(empty($strategy['developer']['nickname'])) echo htmlspecialchars($strategy['developer_name']); else echo htmlspecialchars($strategy['developer']['nickname']) ?></dd>
                            <dt>증권사</dt>
                            <dd>
							<?php if(!empty($strategy['broker']['logo_s'])){ ?><img src="<?php echo $strategy['broker']['logo_s'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['broker']['company']) ?><?php } ?>
							</dd>
                            <dt>매매툴</dt>
                            <dd><?php if(!empty($strategy['system_tool']['logo'])){ ?><img src="<?php echo $strategy['system_tool']['logo'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['system_tool']['name']) ?><?php } ?></dd>
                        </dl>
                    </dt>
                    <dd class="graph">
						<div id="strategy_graph<?php echo $strategy['strategy_id'] ?>" data-role="strategy_graph" data-graph-data="<?php echo $strategy['str_c_price'] ?>" style="width:127px;height:120px;"></div>
                    </dd>
                    <dd class="profit">
                        <dl class="tool">
                            <dt>누적 수익률</dt>
                            <dd><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate']);else echo '0' ?>%</dd>
                            <dt>연간 수익률</dt>
                            <dd>
							<?php if(isset($strategy['yearly_profit_rate'])) echo round($strategy['yearly_profit_rate'],2); else echo '0' ?>%
							<!-- <span class="minus">30.10% -->
							<!-- <span class="plus"> -->
							</dd>
                            <dt><b>원금</b>(<?php echo htmlspecialchars($strategy['currency']) ?>)</dt><!------ 화폐단위 불러와서 표시해 줄 것 ------->
                            <dd><?php if(count($strategy['daily_values'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['principal']);else echo '0' ?></dd>
                            <?php if($strategy['total_funding']>0){ ?>
			    	<dt class="funding">펀딩금액</dt>
                            	<dd class="funding"><?php echo number_format($strategy['total_funding']) ?></dd>
			    <?php } ?>
                        </dl>
                    </dd>
                    <dd class="mdd">
					<i><?php echo $strategy['currency'] ?></i><br />
					<?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd']); else echo '0'; ?><br />
					</dd>
					<!------ 화폐단위 불러와서 표시해 줄 것 ------->
                    <dd class="self"><?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'])) echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'],2); else echo '0' ?></dd>
                    <dd class="followers">Followers<br /><b id="follows_count<?php echo $strategy['strategy_id'] ?>"><?php echo number_format($strategy['followers_count']) ?></b></dd>
                </dl>
				<?php if($isLoggedIn()){ ?>
				<?php if($strategy['is_following']){ ?>
				<button type="button" title="UnFollow" class="unfollow" data-role="unfollow" data-strategy-id="<?php echo $strategy['strategy_id'] ?>"><span class="ir">Unfollow</span></button>
				<?php }else{ ?>
				<button type="button" title="Follow" class="follow" data-role="follow" data-strategy-id="<?php echo $strategy['strategy_id'] ?>"><span class="ir">Follow</span></button>
				<?php } ?>
				<?php }else{ ?>
				<button type="button" title="Follow" class="follow" onclick="showLayer('login_layer');"><span class="ir">Follow</span></button>
				<?php } ?>
            </div>
            <?php } ?>
			<?php }else{ ?>
			<!-- 검색결과 없음 -->
			<?php } ?>
			</div>

            <!-- 스크롤 하면 더보기 불러옴 -->

			<?php } ?>
	    </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
