<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script src="http://code.highcharts.com/highcharts.js"></script>

    <script>
    $(function () {
        var getStrategies = function(page) {
            $('#page').val(page);
            $.ajax({
                mthod: 'get',
                data: $('#searchFrm').serialize(),
                url: '/strategies/list',
                dataType: 'html',
            }).done(function(html) {
                $('.product .list_body').append(html);

                $('.product .list_body div').each(function(){
                    if ($(this).data('role') == 'strategy_graph' && !$(this).data('loaded')) {
                        loadGraph($(this).attr('id'));
                    }
                });

                $('.product .btn_list_more').on('click', function() {
                    $(this).remove();
                    page = page + 1;
                    getStrategies(page);
                });
            });
        }

        getStrategies(1);

        var getPortfolios = function(page) {
            $('#page2').val(page);
            $.ajax({
                mthod: 'get',
                data: $('#searchFrm2').serialize(),
                url: '/portfolios/list',
                dataType: 'html',
            }).done(function(html) {
                $('.portfolio .list_body').append(html);

                $('div').each(function(){
                    if ($(this).data('role') == 'portfolio_graph' && !$(this).data('loaded')) {
                        loadGraph($(this).attr('id'));
                    }
                });

                $('.portfolio .btn_list_more').on('click', function() {
                    $(this).remove();
                    page = page + 1;
                    getPortfolios(page);
                });
            });
        }

        getPortfolios(1);


        $('.product .list_body').on('click', 'button', function(){
            switch ($(this).data('role')) {
                case 'unfollow':
                    var el = $(this);
                    var callback = function() {
                        $(el).closest('li').remove();
				        $('#strategies_cnt').text($('#strategies_cnt').text() -1);
                    };

                    unfollow('strategies', $(this).data('strategy-id'), callback);
                break;
            }

            return false;
        });

        $('.portfolio .list_body').on('click', 'button', function(){
            switch ($(this).data('role')) {
                case 'unfollow':
                    var el = $(this);
                    var callback = function() {
                        $(el).closest('li').remove();
				        $('#portfolios_cnt').text($('#portfolios_cnt').text() -1);
                    };

                    unfollow('portfolios', $(this).data('portfolio-id'), callback);
                break;
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
        <? require_once $skinDir."common/header.php" ?>
        <!-- header -->
        <!-- container -->
        <div class="container">

			<section class="area mypage">	
				<div class="cont_a">

                    <? require_once $skinDir."mypage/sub_menu.php" ?>
                    
					<div class="content my_favorite">
                        <form id="searchFrm">
                        <input type="hidden" id="page" name="page" value="" />
                        <input type="hidden" name="search_type" value="favorite" />
                        <input type="hidden" name="count" value="5" />
                        <input type="hidden" name="strategies" value="<?=$strategies?>" />
                        </form>
						<div class="folder_a">
							<ul class="folder">
                                <?
                                foreach ($groups as $k => $v) {
                                    if ($group_id == $v['group_id']) $group_name = $v['group_name'];
                                ?>
								<li class="<?=($group_id == $v['group_id']) ? 'curr' : ''?>"><a href="?group_id=<?=$v['group_id']?>"><?=$v['group_name']?></a></li>
                                <? } ?>
							</ul>
							<a href="javascript:;" class="btn_make_folder" onclick="commonLayerOpen('make_folder')">+ 폴더 만들기</a>
						</div>
						<div class="group product">
							<div class="list_info">
								<p class="info"><strong class="cnt" id="strategies_cnt"><?=number_format($strategy_cnt)?></strong>개의 관심상품<p>
								<div class="btns">
                                    <? if ($group_name != '기본그룹') { ?>
									<a href="javascript:;" class="btn modify" onclick="commonLayerOpen('modify_folder')">폴더명 변경</a>
									<a href="javascript:;" class="btn delete" onclick="commonLayerOpen('folder_delete')">폴더삭제</a>
                                    <? } ?>
								</div>
							</div>
							<div class="list_wrap">
								<div class="list_header">
									<strong class="row_tit" style="width:480px;">상품</strong>
									<strong class="row_tit" style="width:126px;">그래프</strong>
									<strong class="row_tit" style="width:129px;">수익률</strong>
									<strong class="row_tit" style="width:152px;">SM Score / MDD</strong>
									<strong class="row_tit" style="width:97px;">Follow</strong>
								</div>
								<ul class="list_body">
                                </ul>
                            </div>
                        </div>
                        <form id="searchFrm2">
                        <input type="hidden" id="page2" name="page" value="" />
                        <input type="hidden" name="search_type" value="favorite" />
                        <input type="hidden" name="count" value="2" />
                        <input type="hidden" name="portfolios" value="<?=$portfolios?>" />
                        </form>
						<div class="group portfolio">
							<div class="list_info">
								<p class="info"><strong class="cnt" id="portfolios_cnt"><?=number_format($portfolio_cnt)?></strong>개의 관심 포트폴리오<p>
							</div>
							<div class="list_wrap">
								<div class="list_header">
									<strong class="row_tit" style="width:540px;">포트폴리오 / 정보</strong>
									<strong class="row_tit" style="width:143px;">그래프</strong>
									<strong class="row_tit" style="width:205px;">수익률 / 기간 / MDD</strong>
									<strong class="row_tit" style="width:97px;">Follow</strong>
								</div>
								<ul class="list_body">
                                </ul>
                            </div>
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


<!-- 레이어팝업 : 폴더 삭제 -->
<article class="layer_popup folder_delete">
	<div class="dim" onclick="commonLayerClose('folder_delete')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('folder_delete')"></button>
		</div>
		<div class="cont">
			<div class="summary">
				<p class="q_msg">
					폴더를 삭제하면 <br />
					해당 관심상품도 모두 삭제됩니다.<br />
					<span class="mark">삭제하시겠습니까?</span>
				</p>
			</div>
			<div class="btn_area half">
				<a href="javascript:;" class="btn_common_red" id="del_group">예</a>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('folder_delete')">아니오</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 폴더 삭제 -->

<!-- 레이어팝업 : 폴더 만들기 -->
<article class="layer_popup make_folder">
	<div class="dim" onclick="commonLayerClose('make_folder')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>폴더 만들기</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('make_folder')"></button>
		</div>
		<div class="cont">
			<div class="input_box">
				<input type="text" id="group_name" name="group_name" placeholder="폴더명을 입력해주세요." />
			</div>
			<div class="btn_area half">
				<button type="button" class="btn_common_red" id="reg_group">만들기</button>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('make_folder')">취소</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 폴더 만들기 -->

<!-- 레이어팝업 : 폴더명 변경 -->
<article class="layer_popup modify_folder">
	<div class="dim" onclick="commonLayerClose('modify_folder')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>폴더명 변경</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('modify_folder')"></button>
		</div>
		<div class="cont">
			<div class="input_box">
                <input type="hidden" id="group_id" value="<?=$group_id?>" />
				<input type="text" id="group_name2" name="group_name2" value="<?=$group_name?>" />
			</div>
			<div class="btn_area half">
				<button type="button" class="btn_common_red" id="mod_group">변경</button>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('modify_folder')">취소</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 폴더명 변경 -->

<!-- 레이어팝업 : 안내 > 폴더명 중복 commonLayerOpen('same_name') -->
<article class="layer_popup common_info same_name">
	<div class="dim" onclick="commonLayerClose('same_name')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('same_name')"></button>
		</div>
		<div class="cont">
			<p class="txt_caution">이미 사용중인 폴더명입니다. </p>
			<div class="btn_area">
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('same_name')">닫기</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 안내 > 폴더명 중복 -->


<script>
$(function() {

    $('#reg_group').click(function(){
        var group_name = $.trim($('#group_name').val());
        if (!group_name) {
            alert('폴더명을 입력하세요.');
        } else {
            $.post('/strategies/follow/group', {'group_name' : group_name}, function(data){
                if (data.result) {
                    alert('폴더가 생성되었습니다');
                    location.reload();
                } else {
                    $('#group_name').val('');
                    alert(data.msg);
                }
            }, 'json');
        }
    });

    $('#mod_group').click(function(){
        var group_id = $('#group_id').val();
        var group_name = $.trim($('#group_name2').val());
        if (!group_name) {
            alert('폴더명을 입력하세요.');
        } else {
            $.post('/mypage/follow/group/edit', {'group_id': group_id, 'group_name' : group_name}, function(data){
                if (data.result) {
                    alert('폴더명이 변경되었습니다');
                    location.reload();
                } else {
                    alert(data.msg);
                }
            }, 'json');
        }
    });

    $('#del_group').click(function(){
        var group_id = $('#group_id').val();
        $.post('/mypage/follow/group/delete', {'group_id': group_id}, function(data){
            if (data.result) {
                alert('폴더가 삭제되었습니다');
                location.reload();
            } else {
                alert('처리중 오류가 발생하였습니다');
            }
        }, 'json');
    });
});
</script>
</body>
</html>
