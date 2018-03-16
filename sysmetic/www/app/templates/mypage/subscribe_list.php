<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>

    <script>
    $(function() {
        var getContent = function(page) {
            $.ajax({
                method: 'get',
                data: {page: page},
                url: '/mypage/subscribe/list',
                dataType: 'html',
                async: false
            }).done(function(html) {
                $('.lyk').append(html);

                $('.btn_list_more').on('click', function() {
                    $(this).remove();
                    page = page + 1;
                    getContent(page);
                });
            });

            $(".lyk").viewimageresize();
        }

        $('.lyk').on('click', 'button', function(){
        });

        getContent(1);
    });

    function delChk(uid) {
        $('#delUid').val(uid);
        commonLayerOpen('subscribe_cancel');
    }

    function delSubscribe() {
        $.ajax({
            mthod: 'post',
            data: {uid: $('#delUid').val()},
            url: '/lounge/subscribe/del',
            dataType: 'json',
        }).done(function(data) {
            if (data.result) {
                commonLayerClose('subscribe_cancel');
                location.reload();
            } else {
                commonLayerClose('subscribe_cancel');
                alert(data.msg);
            }
        });
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

                    <div class="content subscribe_list">
                        <? if ($pbCnt) { ?>
						<a href="/investment/developers?page=1&type=P&type2=1" class="only_btn" style="color:#ffffff;text-decoration:none;border:0px;">구독중인 PB</a></p>
                        <p class="list_info">구독중인 <strong class="cnt"><?=number_format($pbCnt)?></strong>명의 PB의 <strong class="cnt"><?=number_format($newCnt)?></strong>개의 새 글이 있습니다.</p>
						
                        <!-- 구독중인 PB가 있는 경우 -->
						<div class="lyk">
                        </div>
                        
                        <? } else { ?>

                        <!-- 구독중인 PB가 없는 경우 -->
                        <div class="none">
                            <p class="txt_info"><img src="/images/sub/txt_subscribe_none_list.png" alt="구독중인 PB가 없습니다." /></p>
                            <p class="summary">
                                관심 있는 <strong class="bold">PB의 Lounge를 방문해 <span class="mark">"구독하기"</span></strong>를 하시면,<br />
                                해당 PB의 새글을 구독하실 수 있습니다.
                            </p>
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

<!-- 레이어팝업 : 구독 취소 -->
<article class="layer_popup subscribe_cancel">
	<div class="dim" onclick="commonLayerClose('subscribe_cancel')"></div>
    <input type="hidden" id="delUid" value="" />
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('subscribe_cancel')"></button>
		</div>
		<div class="cont">
			<div class="summary">
				<p class="q_msg">해당 PB의 새 글을<br /><span class="mark">구독 취소</span> 하시겠습니까?</p>
			</div>
			<div class="btn_area half">
				<a href="javascript:;" class="btn_common_red" onclick="delSubscribe();commonLayerClose('subscribe_cancel')">예</a>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('subscribe_cancel')">아니오</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 구독 취소 -->


</body>
</html>
