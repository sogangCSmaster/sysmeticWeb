<!doctype html>
<html lang="ko">
<head>
    <title>라운지 | SYSMETIC</title>
    <? include_once $skinDir."/common/head.php" ?>
    <script src="http://code.highcharts.com/stock/highstock.js"></script>
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
		//    foreach($series_data as $k => $v){
		//        if(!in_array($k, array('sm_index','balance', 'principal', 'acc_flow', 'flow', 'daily_pl', 'daily_pl_rate', 'acc_pl', 'acc_pl_rate', 'dd', 'dd_rate', 'avg_pl', 'avg_pl_rate', 'winning_rate', 'avg_pl_rate', 'roa', 'total_profit', 'total_loss'))) continue;
		//        echo '\''.$k.'\' : ';
		//        $v_array = array();
		//        foreach($v as $vv){
		//            foreach($vv as $kkk => $vvv){
		//                $v_array[] = '['.$kkk.','.$vvv.']';
		//            }
		//        }
		//        echo '['.implode(',', $v_array).'],'."\n";
		//    }
	echo '\'tot_pl_rate\' : '. $chart_pl_rate;
    echo '};'."\n";

    echo 'var series_min_data = {'."\n";
		//    foreach($series_data as $k => $v){
		//        if(!in_array($k, array('sm_index','balance', 'principal', 'acc_flow', 'flow', 'daily_pl', 'daily_pl_rate', 'acc_pl', 'acc_pl_rate', 'dd', 'dd_rate', 'avg_pl', 'avg_pl_rate', 'winning_rate', 'avg_pl_rate', 'roa', 'total_profit', 'total_loss'))) continue;
		//        echo '\''.$k.'\' : ';
		//        $min_value = -9999;
		//        foreach($v as $vv){
		//            foreach($vv as $kkk => $vvv){
		//                if ( $min_value == -9999 ) $min_value = $vvv;
		//                if ( $vvv < $min_value ) $min_value = $vvv;
		//            }
		//        }
		//        echo '['.$min_value.'],'."\n";
		//    }
		// echo '\'tot_pl_rate\' : '. $chart_pl_rate;
    echo '};'."\n";

    echo 'var series_max_data = {'."\n";
		//    foreach($series_data as $k => $v){
		//        if(!in_array($k, array('sm_index','balance', 'principal', 'acc_flow', 'flow', 'daily_pl', 'daily_pl_rate', 'acc_pl', 'acc_pl_rate', 'dd', 'dd_rate', 'avg_pl', 'avg_pl_rate', 'winning_rate', 'avg_pl_rate', 'roa', 'total_profit', 'total_loss'))) continue;
		//        echo '\''.$k.'\' : ';
		//        $max_value = 9999;
		//        foreach($v as $vv){
		//            foreach($vv as $kkk => $vvv){
		//                if ( $max_value == 9999 ) $max_value = $vvv;
		//                if ( $vvv > $max_value ) $max_value = $vvv;
		//            }
		//        }
		//        echo '['.$max_value.'],'."\n";
		//    }
		// echo '\'tot_pl_rate\' : '. $chart_pl_rate;
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

        // createGraph('누적 수익률(%)', 'line', series_data['tot_pl_rate'], series_min_data['tot_pl_rate'], series_max_data['tot_pl_rate']);
			//- createGraph('누적 수익률(%)', 'line', series_data['acc_pl_rate'], series_min_data['acc_pl_rate'], series_max_data['acc_pl_rate'], '원금', 'line', series_data['principal'], series_min_data['principal'], series_max_data['principal']);

		createGraphSingle('누적 수익률(%)', 'line', series_data['tot_pl_rate']);

        // 최초로딩정보
        $('.details .cont_area #daily').load( "/investment/strategies/<?=$strategy['strategy_id']?>/daily");

        // 리뷰로드
        $('.review_area').load('/investment/strategies/<?=$strategy['strategy_id']?>/reviews');

        // follow
        var follow_load = false;
        $('.follow').on('click', 'button', function(){
            switch ($(this).data('role')) {
                case 'followForm':
                    if (follow_load == false) {
                        $.ajaxSetup({ async:false });
                        $.get('/investment/strategies/follow/form', function(data){
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
                    var btn_el = $(this);
                    $.get('/investment/strategies/'+$(this).data('strategy-id')+'/unfollow', {type:'json'}, function(data){
                        if (data.result) {
                            btn_el.attr('title', 'Follow').attr('class', 'btn_follow').data('role', 'followForm').html('Follow +');
                            $('#follows_count'+btn_el.data('strategy-id')).text(parseInt($('#follows_count'+btn_el.data('strategy-id')).text()) - 1);
                        } else {
                        }
                    }, 'json');
                break;

                case 'login':
                    login();
                break;
            }

            return false;
        });

    });

    function createGraphSingle(name, charttype, data) {
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
				labels: {
					align: 'right',
					x: -3
				},
				title: {
					text: name
				},
				height: '100%',
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
			}],
			navigator:{
				enabled: true
			},
			credits:{
				enabled: false
			}
		});
	}

    function createGraph(name, charttype, data, min, max, name1, charttype1, data1, min1, max1) {
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
        //$('#show_img').attr('src', url);
        //showLayer('preview');


        $('#show_img').attr('src', url);
        commonLayerOpen('preview_img');
    }
    </script>
</head>
<body>
    <!-- wrapper -->
    <div class="wrapper">

        <!-- header -->
        <? require_once $skinDir."/common/header.php" ?>
        <!-- header -->

        <!-- container -->
        <div class="container">
            <section class="area pb_detail">
                <div class="area">
                    <div class="head">
                        <? include $skinDir."/lounge/pb_info.php" ?>
                    </div>
                    <div class="content main">
                        <div class="summary_data" <?if($total==0){?>style="display:none;"<?}?>>
                            <dl>
                                <dt>누적 수익률</dt>
                                <dd><?php echo percent_format($total_pl_rate,2) ?>%</dd>
									<!-- <dd><?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2) ?>%</dd> -->
                            </dl>
                            <dl class="bg">
                                <dt>최대 자본인하율</dt>
                                <dd class="blue"><?php echo percent_format($total_mdd_rate,2) ?>%</dd>
                            </dl>
                            <dl>
                                <dt>평균 손익률</dt>
                                <dd><?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['avg_pl_rate']) ?>%</dd>
                            </dl>
                            <dl class="bg">
                                <dt>Profit Factor</dt>
                                <dd><?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['profit_factor'], 2) ?> : 1</dd>
                            </dl>
                            <dl>
                                <dt>승률</dt>
                                <dd><?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['winning_rate']) ?>%</dd>
                            </dl>
                        </div>
                        <div class="chart_area" <?if($total==0){?>style="display:none;"<?}?>>
                            <div class="chart sample" id="strategy_graph" data-role="strategy_graph">chart_area</div>
                        </div>
                        <div class="lately_cont">
                            <div class="head">
                                <h2 class="title n_gothic">최근게시물 <span class="cnt"><?=number_format($total_contents);?></span>개</h2>
                                <a href="/lounge/<?=$pb['uid']?>/contents" class="btn_more n_gothic">+ 더보기</a>
                            </div>
                            <ul>
                                <?
                                $cnt = 0;
                                foreach ($contents as $v) {
                                    $cnt++;
									$content = $v['contents'];
									$img_array = str_img($content);
									$bgImg = $img_array[0];

									if (!$bgImg) {
										$bgImg = '/images/img_lounge_sample'.rand(0, 2).'.jpg';
									}

                                    $bg = "background:url('$bgImg') no-repeat center; background-size:cover;";
                                ?>
                                <li class="<?=($cnt==1) ? 'left' : ''?>">
                                    <a href="/lounge/<?=$v['uid']?>/contents/<?=$v['cidx']?>" class="subject" style="<?=$bg?>">
                                        <p><?=$v['subject']?></p>
                                    </a>
                                </li>
                                <? } ?>
                                    <!-- 게시물 이미지를 가져와 inline background로 사용 -->
                                <!-- <li>
                                    <a href="javascript:;" class="subject" style="background:url('../images/img_lounge_sample.jpg') no-repeat center; background-size:cover;">
                                        <p>** 주식 분석사례</p>
                                    </a>
                                </li> -->
                            </ul>
                        </div>
                        <div class="counsel_info">
                            <div class="info">
                                <div class="head">
                                    <h2 class="title">상담하기</h2>
                                    <img src="/images/sub/ico_pencel.png" alt="" />
                                </div>
                                <p class="summary">언제든 전략 및 투자에 대한 <br />상담을 하실 수 있습니다.</p>
                            </div>
                            <div class="type online">
                                <h2 class="title">Online 상담하기</h2>
                                <p class="summary">상담내용을 등록해 주시면 확인 후 답변 드립니다.</p>
                                <a href="/lounge/<?=$pb['uid']?>/counsel/Online" class="btn">바로가기</a>
                            </div>
                            <div class="type offline">
                                <h2 class="title">Offline 상담하기</h2>
                                <p class="summary">상담 가능 시간과 상담 내용을 남겨 주시면 PB가 직접 연락 드립니다.</p>
                                <a href="/lounge/<?=$pb['uid']?>/counsel/Offline" class="btn">바로가기</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- //container -->

        <!-- footer -->
        <? require_once $skinDir."/common/footer.php" ?>
        <!-- // footer -->

    </div>
    <!-- //wrapper -->
</body>
</html>
