<!doctype html>
<html lang="ko">
<head>
	<title>라운지 | SYSMETIC</title>
	<? include_once $skinDir."/common/head.php" ?>
    <script>
    $(function() {
        var page = 1;

        var loadContents = function(page) {
            
            $.ajax({
                type: 'get',
                data: {page: page},
                url: '/lounge/<?=$pb['uid']?>/appraise/list',
                dataType: 'html',
            }).done(function(html) {
                $('.list_body').append(html);

                var total = $('.cnt').text();
                var cnt = $('.list_body li').length;
                if (total > cnt) {
                    $('.btn_list_more').show();
                } else {
                    $('.btn_list_more').hide();
                }
            });
        }

        $('.btn_list_more').on('click', function() {
            page = page + 1;
            loadContents(page);
        });

        loadContents(page);


        $('#regFrm').on('submit', function() {
            
            <? if ($isLoggedIn()) { ?>

            if (!$('#num').val()) {
                alert('별점을 선택해주세요');
                $('#num').focus();
                return false;
            } else if (!$.trim($('#contents').val())) {
                alert('평가내용을 입력해주세요');
                $('#contents').focus();
                return false;
            } else {
                if (!confirm('등록하시겠습니까?')) {
                    return false;
                } else {

                    $.ajax({
                        type: 'post',
                        data: $('#regFrm').serialize(),
                        url: '/lounge/<?=$pb['uid']?>/appraise',
                        dataType: 'json',
                    }).done(function(data) {
                        if (data.result) {
                            location.href="/lounge/<?=$pb['uid']?>/appraise";
                        } else {
                            alert(data.msg);
                        }
                    });

                    return false;
                }
            }

            <? } else { ?>
            login();
            return false;
            <? } ?>
        });

    });

    function del() {
        if (!confirm('삭제하시겠습니까?')) {
            return false;
        }

        $.ajax({
            type: 'get',
            url: '/lounge/<?=$pb['uid']?>/appraise/delete',
            dataType: 'json',
        }).done(function(data) {
            if (data.result) {
                location.href="/lounge/<?=$pb['uid']?>/appraise";
            } else {
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
		<? require_once $skinDir."/common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container">
			<section class="area pb_detail">
				<div class="area">
					<div class="head">
                        <? include $skinDir."/lounge/pb_info.php" ?>
					</div>

					<div class="content appraise">	
						<div class="form_box">
                            <form id="regFrm" method="post">
							<div class="custom_selectbox" style="width:150px;">
								<label for="num">별점선택</label>
								<select id="num" name="num">
									<option value="" selected="selected">별점선택</option>
									<option value="5">★★★★★</option>
									<option value="4">★★★★☆</option>
									<option value="3">★★★☆☆</option>
									<option value="2">★★☆☆☆</option>
									<option value="1">★☆☆☆☆</option>
								</select>
							</div>
							<div class="write_a">
								<div class="textarea">
									<textarea id="contents" name="contents" placeholder="평가내용을 입력해 주세요. "></textarea>
								</div>
								<button type="submit" class="btn_write">등록</button>
							</div>
                            </form>
						</div>
						<div class="appraise_list">
							<div class="title_a">
								<strong class="title">평가 <span class="cnt"><?=number_format($total)?></span>개</strong>
							</div>
							<ul class="list_body">
							</ul>
							<a href="javascript:;" class="btn_list_more" style="display:none">+ 더보기</a>
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
