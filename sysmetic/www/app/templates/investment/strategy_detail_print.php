<!doctype html>
<html lang="ko">
<head>
    <title><?php echo htmlspecialchars($strategy['name']) ?> | SYSMETIC</title>
    <? require_once $skinDir."common/head.php" ?>
	<meta property="og:image" content="http://sysmetic.co.kr/images/common/sysmetic_logo.png" />
	<meta id="meta_og_title" property="og:title" content="<?=$strategy[name]?>">
	<meta id="meta_og_description" property="og:description" content='<?php echo nl2br(htmlspecialchars($strategy['intro'])) ?>' />
    <script src="/script/calendar.js"></script>
    <script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
    <script src="http://code.highcharts.com/stock/highstock.js"></script>
    <script src="/js/printThis.js"></script>

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

    function ask(name){
        $('#ask_body').val('');
        $('#ask_strategy_name').text(name);
        commonLayerOpen('request_strategy');
    }

    function invest(name){
        $('#invest_strategy_name').text(name);
        $('#s_price, #s_date, #max_loss_per').val('');
        commonLayerOpen('investment');
    }

    $(function(){
        $('#ask_form').submit(function(){
            if (!$('#ask_body').val()) {
                $('#ask_body').focus();
                alert('내용을 입력해주세요.');
                return false;
            }

            $.post($(this).attr('action'), $(this).serialize(), function(data){
                if (data.result) {
                    commonLayerClose('request_strategy');
                    commonLayerOpen('request_strategy_complete');
                } else {
                    alert('처리 중 요류가 발생하였습니다');
                }
            }, 'json');
            return false;
        });

        $('#invest_form').submit(function() {
            if (!$('#mobile').val()) {
                $('#mobile').focus();
                alert('연락처를 입력해주세요.');
                return false;
            } else if (!$('#email').val()) {
                $('#email').focus();
                alert('이메일을 입력해주세요.');
                return false;
            } else if (!$('#s_price').val()) {
                $('#s_price').focus();
                alert('투자가입금액을 입력해주세요.');
                return false;
            } else if (!$('#s_date').val()) {
                $('#s_date').focus();
                alert('투자개시시점을 입력해주세요.');
                return false;
            } else if (!$('#max_loss_per').val()) {
                $('#max_loss_per').focus();
                alert('최대손실한도율설정을 입력해주세요.');
                return false;
            } else if (!$('#check01').is(":checked")) {
                $('#check01').focus();
                alert('개인정보 제3자 정보제공에 동의해주세요.');
                return false;
            }

            $.post($(this).attr('action'), $(this).serialize(), function(data){
                if (data.result) {
                    commonLayerClose('investment');
                    commonLayerOpen('investment_complete');
                } else {
                    alert('처리 중 요류가 발생하였습니다');
                }
            }, 'json');
            return false;
        });

        $('.category select').change('click', function() {
            createGraph($('#1series option:selected').data('label')
                , $('#1series option:selected').data('chart')
                , series_data[$('#1series option:selected').val()]
                , series_min_data[$('#1series option:selected').val()]
                , series_max_data[$('#1series option:selected').val()]
                , $('#2series option:selected').data('label')
                , $('#2series option:selected').data('chart')
                , series_data[$('#2series option:selected').val()]
                , series_min_data[$('#2series option:selected').val()]
                , series_max_data[$('#2series option:selected').val()]);
        });

        createGraph('누적 수익률(%)', 'line', series_data['acc_pl_rate'], series_min_data['acc_pl_rate'], series_max_data['acc_pl_rate'], '원금', 'line', series_data['principal'], series_min_data['principal'], series_max_data['principal']);

        // 최초로딩정보
        $('.details .cont_area #daily').load( "/strategies/<?=$strategy['strategy_id']?>/daily");

        // follow
        var follow_load = false;
        $('.follow').on('click', 'button', function(){
            switch ($(this).data('role')) {
                case 'followForm':
                    if (follow_load == false) {
                        $.ajaxSetup({ async:false });
                        $.get('/strategies/follow/form', function(data){
                            content= data;
                            $('body').append(content);
                            follow_load = true;
                        });
                        $.ajaxSetup({ async:true });
                    }

                    $('.layer_popup .name').text($(this).data('strategy-name'));
                    $('.layer_popup #strategy_id').val($(this).data('strategy-id'));
                    commonLayerOpen('strategy_follow');
                break;

                case 'unfollow':
                    var el = $(this);
                    var callback = function() {
                        el.attr('title', 'Follow').attr('class', 'btn_follow').data('role', 'followForm').html('Follow +');
                        $('#follows_count'+el.data('strategy-id')).text(parseInt($('#follows_count'+el.data('strategy-id')).text()) - 1);
                    };

                    unfollow('strategies', $(this).data('strategy-id'), callback);

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
    });

    function createGraph(name, charttype, data, min, max, name1, charttype1, data1, min1, max1) {
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
        //$('#show_img').attr('src', url);
        //showLayer('preview');


        $('#show_img').attr('src', url);
        commonLayerOpen('preview_img');
    }

    
    <?php if(!empty($flash['regist_complete'])){ ?>
    if (confirm("일간자료 입력 및 상세한 전략정보는\n마이페이지 -> 상품관리에서 입력해주세요.\n이동하시겠습니까?")) {
        location.href='/mypage/strategies/<?=$strategy[strategy_id]?>/basic';
    }
    <?php } ?>
        
	window.onload=function(){
		$('.strategy_detail_wrap').printThis();
	}

    </script>
</head>
<body>
    <!-- wrapper -->

        <!-- header -->
        <?// require_once $skinDir."common/header.php" ?>
        <!-- header -->

        <!-- container -->


            <?// require_once $skinDir."investment/sub_menu.php" ?>
			<div style="margin:10px; color:#ff0000">※ 출력 방향을 가로로 설정하여 출력하시기 바랍니다.</div>

                <div class="strategy_detail_wrap">
                    <div class="side">
                        <div class="follow">
                            <dl class="info">
                                <dt>Followers : </dt>
                                <dd id="follows_count<?=$strategy['strategy_id']?>" ><?php echo number_format($strategy['followers_count']) ?></dd>
                            </dl>

                            <? if ($isLoggedIn()) { ?>
                                <? if ($strategy['is_following']) { ?>
                                <button id="btn_follow<?=$strategy['strategy_id']?>" type="button" class="btn_unfollow" data-role="unfollow" data-strategy-id="<?=$strategy['strategy_id']?>" data-strategy-name="<?=htmlspecialchars($strategy['name'])?>">unFollow -</button>
                                <? } else if ($strategy['is_mine']) { ?>
                                   <button type="button" class="btn_follow" data-role="mine">Follow +</button>
                                <? } else { ?>
                                <button id="btn_follow<?=$strategy['strategy_id']?>" type="button" class="btn_follow" data-role="followForm" data-strategy-id="<?=$strategy['strategy_id']?>" data-strategy-name="<?=htmlspecialchars($strategy['name'])?>">Follow +</button>
                                <? } ?>
                            <? } else { ?>
                            <button type="button" class="btn_follow" data-role="login">Follow +</button>
                            <? } ?>
                        </div>
                        <div class="user_info">
                            <div class="photo">
								<?php
	                            $picture = getProfileImg($strategy['developer']['picture']);
								?>
									<img src="<?=$picture?>" />
							</div>
                            <p class="nickname"><?php if(empty($strategy['developer']['nickname'])) echo htmlspecialchars($strategy['developer_name']); else echo htmlspecialchars($strategy['developer']['nickname']) ?></p>
                        </div>
                        <div class="etc_info">
							<?
							if ($strategy['pb']['name']) { 
								$strategy['pb']['picture'] = getProfileImg($strategy['pb']['picture']);
							?>
                            <dl>
                                <dt>PB</dt>
                                <dd onclick="window.location='/lounge/<?=$strategy['pb']['uid']?>'" style="cursor:pointer"><img src="<?=$strategy['pb']['picture']?>" width=30 alt="" style="border-radius: 50em;" /><?=$strategy['pb']['name']?></dd>
                            </dl>
                            <? }else{ ?>
                            <dl>
                                <dt>PB</dt>
                                <dd>없음</dd>
                            </dl>
                            <? } ?>
                            <dl>
                                <dt>중개사</dt>
                                <dd>
                                    <a href="javascript:;">
                                        <?php if(!empty($strategy['broker']['logo_s'])){ ?><img src="<?php echo $strategy['broker']['logo_s'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['broker']['company']) ?><?php } ?>
                                    </a>
                                </dd>
                            </dl>
                            <dl>
                                <dt>매매툴</dt>
                                <dd>
                                    <a href="javascript:;">
                                        <?php if(!empty($strategy['system_tool']['logo'])){ ?><img src="<?php echo $strategy['system_tool']['logo'] ?>" /><?php }else{ ?><?php echo htmlspecialchars($strategy['system_tool']['name']) ?><?php } ?>
                                    </a>
                                </dd>
                            </dl>
                            <? if ($isLoggedIn()) { ?>
                            <a href="javascript:;" class="btn blue" onclick="ask('<?=htmlspecialchars($strategy['name'])?>');">문의하기</a>
                                <? if ($_SESSION['user']['user_type'] != 'P' && $_SESSION['user']['user_type'] != 'T') { ?>
                                    <? if ($strategy['is_fund']) { ?>
                                    <a href="#" class="btn red" onclick="invest('<?=htmlspecialchars($strategy['name'])?>');">투자요청</a>
                                    <? } else { ?>
                                    <a href="#" class="btn red" onclick="alert('현재는 투자를 받고 있지 않습니다.')">투자불가</a>
                                    <? } ?>
                                <? } else { ?>
                                <a href="#" class="btn red" onclick="alert('현재는 투자를 받고 있지 않습니다.')">투자불가</a>
                                <? } ?>
                            <? } else { ?>
                            <a href="#a" class="btn blue" onclick="login();">문의하기</a>
                                <? if ($strategy['is_fund']) { ?>
                                <a href="#a" class="btn red" onclick="login();">투자요청</a>
                                <? } else { ?>
                                <a href="#a" class="btn red" onclick="alert('현재는 투자를 받고 있지 않습니다.')">투자불가</a>
                                <? } ?>
                            <? } ?>
                        </div>
                        <div class="investment_info">
                            <dl>
                                <dt>최소투자금액</dt>
                                <dd class="n_gothic"><?=$strategy['min_price']?></dd>
                            </dl>
                            <dl>
                                <dt>투자원금(<?php echo $strategy['currency'] ?>)</dt>
                                <dd class="n_gothic">
                                <?php echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['principal']) ?>
                                </dd>
                            </dl>
                            <dl>
                                <dt>펀딩금액(<?php echo $strategy['currency'] ?>)</dt>
                                <dd class="n_gothic"><?php echo number_format($strategy['total_funding']) ?></dd>
                            </dl>
                            <dl>
                                <dt>투자자수</dt>
                                <dd class="n_gothic"><?php echo number_format($strategy['investor_count']) ?></dd>
                            </dl>
                        </div>
                        <div class="investment_info">
                            <dl>
                                <dt>KP Ratio</dt>
                                <dd class="n_gothic"><?php if(count($strategy['daily_values'])) echo $strategy['daily_values'][count($strategy['daily_values'])-1]['kp_ratio']; else echo 0 ?></dd>
                            </dl>
                            <dl>
                                <dt>SM Score</dt>
                                <dd class="n_gothic"><?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'])) echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'],2); else echo '0' ?></dd>
                            </dl>
                        </div>
                        <div class="investment_info">
                            <dl>
                                <dt>최초 손익입력일자</dt>
                                <dd class="n_gothic"><?=str_replace('-', '/', $strategy['first_update'])?></dd>
                            </dl>
                            <dl>
                                <dt>최종 손익입력일자</dt>
                                <dd class="n_gothic"><?=str_replace('-', '/', $strategy['last_update'])?></dd>
                            </dl>
                        </div>
                        <div class="share">
                            <p class="cnt">Share : 264</p>
                            <a href="javascript:;" onclick="goFacebook('<?=makeFacebookShare($strategy['name'],"http://sysmetic.co.kr/strategies/".$strategy['strategy_id'])?>')" class="btn_share"><img src="/images/sub/btn_share_fb.png" alt="페이스북" /></a>
                            <a href="javascript:;" onclick="goTwitter('<?=makeTwitterShare($strategy['name']);?>')" class="btn_share"><img src="/images/sub/btn_share_twitter.png" alt="트위터" /></a>
                            <a href="javascript:;" id="kakao-link-btn" class="btn_share"><img src="/images/sub/btn_share_kakao.png" alt="카카오톡" /></a>
                        </div>
                    </div>
                    <div class="details">
                        <div class="head">
                            <div class="title">
                                <div class="options">
                                    <!--span class="op_s"><?=$strategy['strategy_type']?></span-->
                                    <img src="<?=$strategy['types']['icon']?>" />
									<?
									if(strtoupper($strategy['strategy_term'][0])=="D")echo "<img src='/images/sysm_d.png'>";
									if(strtoupper($strategy['strategy_term'][0])=="P")echo "<img src='/images/sysm_p.png'>";
									?>
                                    <!-- <span class="op_d"><?=strtoupper($strategy['strategy_term'][0])?></span> -->
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
                                <p class="subject"><? if ($strategy['kind']) { ?><span class="category">[<?=$strategy['kind']?>]</span><? } ?><?php echo htmlspecialchars($strategy['name']) ?></p>
                            </div>
                            <? if ($strategy['file']['save_name']) { ?>
                            <a href="/strategies/<?=$strategy['strategy_id']?>/download">제안서 다운로드</a>
                            <? } else { ?>
                            <a href="javascript:;" onclick="alert('제안서가 없습니다');">제안서 다운로드</a>
                            <? } ?>
                        </div>
                        <div class="txt_summary">
                            <p>
                            <?php echo nl2br(htmlspecialchars($strategy['intro'])) ?>
                            </p>
                        </div>
                        <div class="box_summary">
                            <dl class="gray">
                                <dt>누적 수익률</dt>
                                <dd class="n_gothic"><?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2) ?>%</dd>
                            </dl>
                            <dl class="light_gray">
                                <dt>최대 자본인하율</dt>
                                <dd class="blue n_gothic"><?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd_rate'],2) ?>%</dd>
                            </dl>
                            <dl class="gray">
                                <dt>평균 손익율</dt>
                                <dd class="n_gothic"><?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate']) ?>%</dd>
                            </dl>
                            <dl class="light_gray">
                                <dt>Profit Factor</dt>
                                <dd class="n_gothic"><?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['profit_factor'], 2) ?> : 1</dd>
                            </dl>
                            <dl class="gray">
                                <dt>승률</dt>
                                <dd class="n_gothic"><?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['winning_rate']) ?>%</dd>
                            </dl>
                        </div>

                        <?
                        for ($i = 1; $i <= 12; $i++) {
                            $y = date("Y", strtotime("-$i month", time()));
                            $m = date("m", strtotime("-$i month", time()));
                            $pro += percent_format($strategy['monthly_profit_rate'][$y][intval($m)],2);

                            switch ($i) {
                                case 1:
                                    $pro1 = $pro;
                                    break;
                                case 3:
                                    $pro3 = $pro;
                                    break;
                                case 6:
                                    $pro6 = $pro;
                                    break;
                                case 9:
                                    $pro9 = $pro;
                                    break;
                                case 12:
                                    $pro12 = $pro;
                                    break;
                            }
                        }
                        ?>

                        <div class="box_summary">
                            <dl class="light_gray">
                                <dt>1개월</dt>
                                <dd class="n_gothic"><?=$pro1?>%</dd>
                            </dl>
                            <dl class="gray">
                                <dt>3개월</dt>
                                <dd class="n_gothic"><?=$pro3?>%</dd>
                            </dl>
                            <dl class="light_gray">
                                <dt>6개월</dt>
                                <dd class="n_gothic"><?=$pro6?>%</dd>
                            </dl>
                            <dl class="gray">
                                <dt>9개월</dt>
                                <dd class="n_gothic"><?=$pro9?>%</dd>
                            </dl>
                            <dl class="light_gray">
                                <dt>12개월</dt>
                                <dd class="n_gothic"><?=$pro12?>%</dd>
                            </dl>
                        </div>
                        <div class="monthly_ror">
                            <h3>월간 수익률</h3>
                            <table>
                                <colgroup>
                                    <col style="width:52px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:49px;" />
                                    <col style="width:72px;" />
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th scope="row">1월</th>
                                        <th scope="row">2월</th>
                                        <th scope="row">3월</th>
                                        <th scope="row">4월</th>
                                        <th scope="row">5월</th>
                                        <th scope="row">6월</th>
                                        <th scope="row">7월</th>
                                        <th scope="row">8월</th>
                                        <th scope="row">9월</th>
                                        <th scope="row">10월</th>
                                        <th scope="row">11월</th>
                                        <th scope="row">12월</th>
                                        <th scope="row" class="right">합계</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <? foreach ($strategy['monthly_profit_rate'] as $year => $v) { ?>
                                    <tr>
                                        <th scope="col"><?php echo $year ?></th>
                                        <?
                                        foreach($v as $month => $vv){
                                            if ($strategy['monthly_profit_rate'][$year][$month] != 0) {
                                                $class = ($vv < 0) ? 'blue' : '';
                                                    //- $class = ($month%6==0) ? 'blue' : '';
                                            ?>
                                            <td class="<?=$class?>"><?=percent_format($vv,2)?>%</td>
                                            <?
                                            } else {
                                            ?>
                                            <td></td>
                                            <?
                                            }
                                        }
                                        ?>
                                        <td class="right"><?=round($strategy['yearly_profit_rate'][$year],2)?>%</td>
                                    </tr>
                                    <? } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="data_view">
                            <h3>분석</h3>
                            <div class="category">
                                <div class="custom_selectbox">
                                    <label for="">기준가</label>
                                    <select id="1series" >
                                        <option value="sm_index" data-label="기준가" data-chart="line">기준가</option>
                                        <option value="balance" data-label="잔고" data-chart="line">잔고</option>
                                        <option value="principal" data-label="원금" data-chart="line">원금</option>
                                        <option value="acc_flow" data-label="누적 입출 금액" data-chart="line">누적 입출 금액</option>
                                        <option value="flow" data-label="일별 입출 금액" data-chart="column">일별 입출 금액</option>
                                        <option value="daily_pl" data-label="일손익 금액" data-chart="column">일손익 금액</option>
                                        <option value="daily_pl_rate" data-label="일손익률(%)" data-chart="column">일손익률(%)</option>
                                        <option value="acc_pl" data-label="누적 수익 금액" data-chart="line">누적 수익 금액</option>
                                        <option selected="selected" value="acc_pl_rate" data-label="누적 수익률(%)" checked="checked" data-chart="line">누적 수익률(%)</option>
                                        <option value="dd"  data-label="현재 자본인하금액" data-chart="column">현재 자본인하금액</option>
                                        <option value="dd_rate" data-label="현재 자본인하율" data-chart="column">현재 자본인하율(%)</option>
                                        <option value="avg_pl" data-label="평균 손익 금액" data-chart="line">평균 손익 금액</option>
                                        <option value="avg_pl_rate" data-label="평균 손익률(%)" data-chart="line">평균 손익률(%)</option>
                                        <option value="winning_rate" data-label="승률" data-chart="line">승률</option>
                                        <option value="avg_pl_ratio" data-label="Profit Factor" data-chart="line">Profit Factor</option>
                                        <option value="roa"  data-label="ROA" data-chart="line">ROA</option>
                                        <option value="total_profit" data-label="totalProfit" data-chart="line">totalProfit</option>
                                        <option value="total_loss" data-label="totalLoss" data-chart="line">totalLoss</option>
                                    </select>
                                </div>

                                <div class="custom_selectbox">
                                    <label for="">정렬기준</label>
                                    <select id="2series" >
                                        <option value="sm_index" data-label="기준가" data-chart="line">기준가</option>
                                        <option value="balance" data-label="잔고" data-chart="line">잔고</option>
                                        <option selected="selected" value="principal" data-label="원금" data-chart="line">원금</option>
                                        <option value="acc_flow" data-label="누적 입출 금액" data-chart="line">누적 입출 금액</option>
                                        <option value="flow" data-label="일별 입출 금액" data-chart="column">일별 입출 금액</option>
                                        <option value="daily_pl" data-label="일손익 금액" data-chart="column">일손익 금액</option>
                                        <option value="daily_pl_rate" data-label="일손익률(%)" data-chart="column">일손익률(%)</option>
                                        <option value="acc_pl" data-label="누적 수익 금액" data-chart="line">누적 수익 금액</option>
                                        <option value="acc_pl_rate" data-label="누적 수익률(%)" checked="checked" data-chart="line">누적 수익률(%)</option>
                                        <option value="dd"  data-label="현재 자본인하금액" data-chart="column">현재 자본인하금액</option>
                                        <option value="dd_rate" data-label="현재 자본인하율" data-chart="column">현재 자본인하율(%)</option>
                                        <option value="avg_pl" data-label="평균 손익 금액" data-chart="line">평균 손익 금액</option>
                                        <option value="avg_pl_rate" data-label="평균 손익률(%)" data-chart="line">평균 손익률(%)</option>
                                        <option value="winning_rate" data-label="승률" data-chart="line">승률</option>
                                        <option value="avg_pl_ratio" data-label="Profit Factor" data-chart="line">Profit Factor</option>
                                        <option value="roa"  data-label="ROA" data-chart="line">ROA</option>
                                        <option value="total_profit" data-label="totalProfit" data-chart="line">totalProfit</option>
                                        <option value="total_loss" data-label="totalLoss" data-chart="line">totalLoss</option>
                                    </select>
                                </div>
                            </div>
                            <div class="analysis">
                                <!-- 실제 차트 적용 시 아래 bg 클래스 삭제해주세요 size : 716 * 650px -->
                                <div class="chart_area" id="strategy_graph" data-role="strategy_graph">
                                </div>
                            </div>
                        </div>
<br><br><br>
                        <div class="etc_info">
                            <nav class="category">
                                <ul>
                                    <li><a href="javascript:;" data-type="info">통계</a></li>
                                    <li class="curr"><a href="javascript:;" data-type="daily">일간분석</a></li>
                                    <li><a href="javascript:;" data-type="monthly">월간분석</a></li>
                                    <li><a href="javascript:;" data-type="accounts">실계좌 정보</a></li>
                                </ul>
                                <a href="/cs/faq" class="btn_faq">상품통계 FAQ</a>
                            </nav>
                            <div class="cont_area">
                                <div class="cont" id="info">
                                    <!-- 통계 -->
                                </div>
                                <div class="cont show" id="daily">
                                    <!-- 일간분석 -->
                                </div>
                                <div class="cont" id="monthly">
                                    <!-- 월간분석 -->
                                </div>
                                <div class="cont" id="accounts">
                                    <!-- 실계좌 정보 -->
                                </div>
                            </div>
                        </div>


                    </div>
                </div>



<script>
//탭 별 콘텐츠 보기
$('.details .category li a').on('click', function(){
    var idx = $('.details .category li a').index(this);
    var type = $('.details .category li a').eq(idx).data('type');
    $('.details .category li').removeClass('curr').eq(idx).addClass('curr');
    $('.details .cont_area .cont').removeClass('show').eq(idx).addClass('show');
    $('.details .cont_area .cont').eq(idx).load( "/strategies/<?=$strategy['strategy_id']?>/"+type);
});

//3자 제공동의 툴팁 hide
$('.investment .check_area .btn_close').on('click', function(){
    $(this).closest('.must_agree').removeClass('show');
});

//custom selectbox
var select = $('select');
for(var i = 0; i < select.length; i++){
    var idxData = select.eq(i).children('option:selected').text();
    select.eq(i).siblings('label').text(idxData);
}
select.change(function(){
    var select_name = $(this).children("option:selected").text();
    $(this).siblings("label").text(select_name);
});

</script>
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type='text/javascript'>
//<![CDATA[
// // 사용할 앱의 JavaScript 키를 설정해 주세요.
Kakao.init('b41e79273a98cbc168b495ab60a6ae51');
// // 카카오링크 버튼을 생성합니다. 처음 한번만 호출하면 됩니다.
Kakao.Link.createTalkLinkButton({
  container: '#kakao-link-btn',
  label: '<?=$strategy['name']?>',
  image: {
    src: 'http://sysmetic.co.kr/images/common/sysmetic_logo.png',
    width: '500',
    height: '300'
  },
  webButton: {
    text: '<?=$strategy['name']?>',
    url: 'http://<?=$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]?>'
  }
});
//]]>
</script>
</body>
</html>
