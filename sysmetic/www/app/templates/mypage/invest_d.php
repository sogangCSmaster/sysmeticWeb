<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
    $(function () {
        var getCounsel = function(strategy_id) {
            var loadPage = $('#'+strategy_id).data('page');
            $.ajax({
                method: 'get',
                data: {strategy_id: strategy_id, page: loadPage},
                url: '/mypage/invest/list',
                dataType: 'html',
            }).done(function(html) {
                $('#'+strategy_id + ' .list_tbl tbody').append(html);

                if ($('#'+strategy_id + ' .list_tbl tbody tr').length < $('#'+strategy_id + ' .cnt').text())
                {
                    $('#'+strategy_id + ' .btn_list_more').show().on('click', function() {
                        $(this).hide();
                        page = $('#'+strategy_id).data('page') + 1;
                        $('#'+strategy_id).data({page: page});
                        getCounsel(strategy_id);
                    });
                }

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
            });
        }

        var page = 1;
        $('.my_counsel .list_wrap').each(function() {
            var strategy_id = $(this).attr('id');
            getCounsel(strategy_id);
        });
    });

    function chgStatus(invest_id, status) {
        $.ajax({
            method: 'post',
            data: {invest_id: invest_id, status: status},
            url: '/mypage/invest/chg',
            dataType: 'json',
        }).done(function(data) {
            if (data.result) {
                alert('상태가 변경되었습니다');
            } else {
                alert('처리 중 오류가 발생하였습니다');
            }
        })
    }

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

					<div class="content my_counsel">
						<div class="list_info">
							<p class="info">총 <strong class="cnt"><?=count($strategies)?></strong>개의 상품에 <strong class="cnt"><?=number_format($total)?></strong>명이 투자하였습니다.
                            
                            <div class="custom_selectbox" style="width:190px;">
								<label for="strategy_id">전체</label>
								<select id="strategy_id" name="strategy_id" onchange="location.href='?strategy_id='+this.value;">
									<option value="" selected="selected">전체</option>
                                    <? foreach ($strategies as $strategy) { ?>
									<option value="<?=$strategy['strategy_id']?>" <?=$strategy['strategy_id'] == $strategy_id ? 'selected' : ''?>><?=$strategy['name']?></option>
                                    <? } ?>
								</select>
							</div>

						</div>

                        <?
                        foreach ($strategies as $strategy) {
                            if (!$strategy_id && !$strategy['stg_cnt']) continue;
                            if ($strategy_id && ($strategy_id != $strategy['strategy_id'])) continue;
                        ?>
						<div class="list_wrap" id="<?=$strategy['strategy_id']?>" data-page="1">
                        	<div class="product_name">
								<dl>
									<dt>상품명 :</dt>
									<dd><?=$strategy['name']?></dd>
								</dl>
								<p><strong class="cnt"><?=number_format($strategy['stg_cnt'])?></strong>명 투자</p>
							</div>
							<table class="list_tbl">
								<colgroup>
									<col style="width:52px;" />
									<col style="width:106px;" />
									<col style="width:114px;" />
									<col style="width:160px;" />
									<col style="width:120px;" />
									<col style="width:119px;" />
									<col style="width:132px;" />
									<col style="width:179px;" />
								</colgroup>
								<thead>
									<tr>
										<th style="border-right:none;">&nbsp;</th>
										<th style="border-left:none;">이름</th>
										<th>연락처</th>
										<th>이메일</th>
										<th>투자가입금액</th>
										<th>투자개시시점</th>
										<th>최대손실 한도율</th>
										<th>상태</th>
									</tr>
								</thead>

								<tbody>
								</tbody>
							</table>
							<a href="javascript:;" class="btn_list_more" style="display:none">+ 더보기</a>
						</div>
                        <? } ?>
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

<script>

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

</body>
</html>
