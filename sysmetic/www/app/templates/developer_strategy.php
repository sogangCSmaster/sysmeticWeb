<!doctype html>
<html lang="ko">
<head>
	<title>title</title>
	<? require_once "common/head.php" ?>
	<link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script>
	var isLoading = false;
	var hasMore = true;
	var start = <?php echo $start + $count ?>;
	var count = <?php echo $count ?>;
	var is_extend_search = <?php if($is_extend_search) echo 'true'; else echo 'false'; ?>;


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
	                            html += '<dt>최근 1년 수익률</dt>';
	                            html += '<dd>';
								if(val.daily_values[val.daily_values.length - 1]){
									html += Math.round(val.daily_values[val.daily_values.length - 1].one_yr_pl_rate) +'%';
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
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once "common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container">
			<section class="area">
				<div class="page_title_area">
					<h2 class="page_title n_squere"></h2>
					<p class="page_summary"></p>
				</div>
				<div class="content_area">

		            <div class="select_area">
		                <div class="select open" style="width:110px;">
		                    <div class="myValue"></div>
		                    <ul class="iList">
		                        <li><input name="item" id="item0" class="option" type="radio" value=""<?php if(empty($q_item)) echo ' checked="checked"'; ?> /><label for="item0">종목선택</label></li>
		                        <?php foreach($items as $item){ ?>
		                        <li><input name="item" id="item<?php echo $item['item_id'] ?>" class="option" type="radio" value="<?php echo $item['item_id'] ?>"<?php if(!empty($q_item) && $q_item == $item['item_id']) echo ' checked="checked"'; ?> /><label for="item<?php echo $item['item_id'] ?>"><?php echo htmlspecialchars($item['name']) ?></label></li>
		                        <?php } ?>
		                    </ul>
		                </div>
		                <div class="select open" style="width:105px;">
		                    <div class="myValue"></div>
		                    <ul class="iList">
		                        <li><input name="term" id="term0" class="option" type="radio" value=""<?php if(empty($q_term)) echo ' checked="checked"'; ?> /><label for="term0">주기 선택</label></li>
		                        <li><input name="term" id="term1" class="option" type="radio" value="day"<?php if(!empty($q_term) && $q_term == 'day') echo ' checked="checked"'; ?> /><label for="term1">데이</label></li>
		                        <li><input name="term" id="term2" class="option" type="radio" value="position"<?php if(!empty($q_term) && $q_term == 'position') echo ' checked="checked"'; ?> /><label for="term2">포지션</label></li>
		                    </ul>
		                </div>
		            </div>

		            <button onclick="showSearch('search_detail');" class="btn_search" title="전략 상세검색"><span class="ir">전략 상세검색</span></button>
		            <!-- 상세검색 -->
		            <div id="search_detail" class="search_detail" style="<?php if(!$is_open_search) echo 'display:none;' ?>">
		            <button onclick="closeLayer('search_detail');" class="btn_search" title="전략 상세검색"><span class="ir">전략 상세검색</span></button>
					<form action="/strategies" method="get" id="extend_search_form">
		            <fieldset>
		                <legend>전략 상세검색</legend>
		                <table border="0" cellspacing="0" cellpadding="0" class="list_search">
		                    <col width="140" /><col width="*" />
		                    <tr>
		                        <td class="thead">구분</td>
		                        <td>
		                            <p><input type="checkbox" name="search_strategy_type[]" id="search_strategy_type1" value="M"<?php if(isset($search_params['search_strategy_type']) && is_array($search_params['search_strategy_type']) && in_array('M', $search_params['search_strategy_type'])) echo ' checked="checked"' ?> /><label for="search_strategy_type1">Manual Trading</label></p>
		                            <p><input type="checkbox" name="search_strategy_type[]" id="search_strategy_type2" value="S"<?php if(isset($search_params['search_strategy_type']) && is_array($search_params['search_strategy_type']) && in_array('S', $search_params['search_strategy_type'])) echo ' checked="checked"' ?> /><label for="search_strategy_type2">System Trading</label></p>
		                            <p><input type="checkbox" name="search_strategy_type[]" id="search_strategy_type2" value="S"<?php if(isset($search_params['search_strategy_type']) && is_array($search_params['search_strategy_type']) && in_array('H', $search_params['search_strategy_type'])) echo ' checked="checked"' ?> /><label for="search_strategy_type2">Hybrid Trading(System + Manual)</label></p>
		                        </td>
		                    </tr>
		                    <tr>
		                        <td class="thead">주기</td>
		                        <td>
		                            <p><input type="checkbox" name="search_term[]" id="search_term1" value="day"<?php if(isset($search_params['search_term']) && is_array($search_params['search_term']) && in_array('day', $search_params['search_term'])) echo ' checked="checked"' ?> /><label for="search_term1">데이</label></p>
		                            <p><input type="checkbox" name="search_term[]" id="search_term2" value="position"<?php if(isset($search_params['search_term']) && is_array($search_params['search_term']) && in_array('position', $search_params['search_term'])) echo ' checked="checked"' ?> /><label for="search_term2">포지션</label></p>
		                        </td>
		                    </tr>
		                    <tr>
		                        <td class="thead">종목</td>
		                        <td>
		                            <?php foreach($items as $item){ ?>
		                            <p><input type="checkbox" name="search_item[]" id="search_item<?php echo $item['item_id'] ?>" value="<?php echo $item['item_id'] ?>"<?php if(isset($search_params['search_item']) && is_array($search_params['search_item']) && in_array($item['item_id'], $search_params['search_item'])) echo ' checked="checked"' ?> /><label for="search_item<?php echo $item['item_id'] ?>"><?php echo htmlspecialchars($item['name']) ?></label></p>
		                            <?php } ?>
		                        </td>
		                    </tr>
		                    <tr>
		                        <td class="thead">증권사</td>
		                        <td>
									<?php foreach($brokers as $broker){ ?>
		                            <p><input type="checkbox" name="search_broker[]" id="search_broker<?php echo htmlspecialchars($broker['broker_id']) ?>" value="<?php echo htmlspecialchars($broker['broker_id']) ?>"<?php if(isset($search_params['search_broker']) && is_array($search_params['search_broker']) && in_array($broker['broker_id'], $search_params['search_broker'])) echo ' checked="checked"' ?> /><label for="search_broker<?php echo htmlspecialchars($broker['broker_id']) ?>"><?php echo htmlspecialchars($broker['company']) ?></label></p>
									<?php } ?>
		                        </td>
		                    </tr>
							<!--
		                    <tr>
		                        <td class="thead">매매툴</td>
		                        <td>
		                            <p><input type="checkbox" name="system_tool" id="system_tool1" value="예스트레이더" /><label for="system_tool1">예스트레이더</label></p>
		                            <p><input type="checkbox" name="system_tool" id="system_tool2" value="MC Chart" /><label for="system_tool2">MC Chart</label></p>
		                        </td>
		                    </tr>
							-->
		                </table>

		                <table border="0" cellspacing="0" cellpadding="0" class="list_search">
		                    <col width="140" /><col width="*" />
		                    <tr>
		                        <td class="thead">누적수익률</td>
		                        <td>
		                            <input name="search_total_profit_rate_min" type="text" value="<?php if(isset($search_params['search_total_profit_rate_min'])) echo htmlspecialchars($search_params['search_total_profit_rate_min']) ?>" /> ~ <input name="search_total_profit_rate_max" type="text" value="<?php if(isset($search_params['search_total_profit_rate_max'])) echo htmlspecialchars($search_params['search_total_profit_rate_max']) ?>" /> %
		                        </td>
		                    </tr>
		                    <tr>
		                        <td class="thead">최근 1년 수익률</td>
		                        <td>
		                            <input name="search_yearly_profit_rate_min" type="text" value="<?php if(isset($search_params['search_yearly_profit_rate_min'])) echo htmlspecialchars($search_params['search_yearly_profit_rate_min']) ?>" /> ~ <input name="search_yearly_profit_rate_max" type="text" value="<?php if(isset($search_params['search_yearly_profit_rate_max'])) echo htmlspecialchars($search_params['search_yearly_profit_rate_max']) ?>" /> %
		                        </td>
		                    </tr>
		                    <tr>
		                        <td class="thead">원금</td>
		                        <td>
		                            <input name="search_principal_min" type="text" value="<?php if(isset($search_params['search_principal_min'])) echo htmlspecialchars($search_params['search_principal_min']) ?>" /> ~ <input name="search_principal_max" type="text" value="<?php if(isset($search_params['search_principal_max'])) echo htmlspecialchars($search_params['search_principal_max']) ?>" />
		                        </td>
		                    </tr>
		                    <tr>
		                        <td class="thead">MDD</td>
		                        <td>
		                            <input name="search_mdd_min" type="text" value="<?php if(isset($search_params['search_mdd_min'])) echo htmlspecialchars($search_params['search_mdd_min']) ?>" /> ~ <input name="search_mdd_max" type="text" value="<?php if(isset($search_params['search_mdd_max'])) echo htmlspecialchars($search_params['search_mdd_max']) ?>" />
		                        </td>
		                    </tr>
		                    <tr>
		                        <td class="thead">SM Score</td>
		                        <td>
		                            <input name="search_sharp_ratio_min" type="text" value="<?php if(isset($search_params['search_sharp_ratio_min'])) echo htmlspecialchars($search_params['search_sharp_ratio_min']) ?>" /> ~ <input name="search_sharp_ratio_max" type="text" value="<?php if(isset($search_params['search_sharp_ratio_max'])) echo htmlspecialchars($search_params['search_sharp_ratio_max']) ?>" />
		                        </td>
		                    </tr>
		                </table>

		                <div class="btn_area">
							<input type="hidden" name="extend_search" value="1">
		                    <button type="submit" title="검색" class="submit"><span class="ir">검색</span></button>
		                </div>
		            </fieldset>
		            </form>
		            </div>
		            <!-- //상세검색 -->

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
		                            <dt>최근 1년 수익률</dt>
		                            <dd>
									<?php if(isset($strategy['daily_values'])) echo round($strategy['daily_values'][count($strategy['daily_values'])-1]['one_yr_pl_rate'],2); else echo '0' ?>%
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


                </div>
            </section>
		</div>
		<!-- //container -->

        <!-- footer -->
		<? require_once "common/footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->

</body>
</html>
