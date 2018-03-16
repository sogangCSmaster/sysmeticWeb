<!doctype html>
<html lang="ko">
<head>
	<title>고객센터 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
    $(function () {
        var totalCnt = <?=$total?>;
        var cate_id = '<?=$cate_id?>';
        var page = 1;
        var getCounsel = function(loadPage) {
            keyword = $('#keyword').val();

            $.ajax({
                mthod: 'get',
                data: {page: loadPage, cate_id: cate_id, keyword: keyword},
                url: '/cs/faq/list',
                dataType: 'html',
            }).done(function(html) {
                $('.faq').append(html);
                if ($('.faq li').length < totalCnt)
                {
                    $('.faq_list .btn_list_more').show().on('click', function() {
                        $(this).hide();
                        page = page + 1;
                        getCounsel(page);
                    });
                }
				/*
                $('.faq_list .question .btn_control').unbind("click").bind("click", function(){
                    if($(this).closest('li').hasClass('show')){
                        $(this).text('답변보기');
                        $(this).closest('li').removeClass('show');
                        $(this).parent('.question').siblings('.answer').slideUp('fast');
                    }else{
                        $(this).text('답변접기');
                        $(this).closest('li').addClass('show');
                        $(this).parent('.question').siblings('.answer').slideDown('fast');
                    }
                });
				*/
            });
        }

        getCounsel(page);
    });

	function faq_view(x){
		//console.log($('#fview'+x).closest('li').hasClass('show'));
		if($('#fview'+x).closest('li').hasClass('show')){
			$('#fview'+x).text('답변보기');
			$('#fview'+x).closest('li').removeClass('show');
			$('#fview'+x).parent('.question').siblings('.answer').slideUp('fast');
		}else{
			$('#fview'+x).text('답변접기');
			$('#fview'+x).closest('li').addClass('show');
			$('#fview'+x).parent('.question').siblings('.answer').slideDown('fast');
		}
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


			<section class="area cs_w">
				<div class="cont_a">
                
                    <? require_once $skinDir."cs/sub_menu.php" ?>

                    <div class="content faq_list">

                        <div class="list_search">
                            <form action="" method="get">
                            <div class="input_box">
                                <input type="text" id="keyword" name="keyword" placeholder="검색어를 입력해주세요." value="<?=$keyword?>" />
                            </div>
                            <button type="submit" class="btn_search">검색하기</button>
                            </form>
                        </div>
                        <div class="category">
                            <ul>
                                <li class="<?=(!$cate_id) ? 'curr' : ''?>"><a href="/cs/faq?keyword=<?=$keyword?>">전체</a></li>
                                <? foreach ($category as $v) { ?>
                                <li class="<?=($cate_id == $v['cate_id']) ? 'curr' : ''?>"><a href="/cs/faq?cate_id=<?=$v['cate_id']?>&keyword=<?=$keyword?>"><?=$v['name']?></a></li>
                                <? } ?>
                            </ul>
                        </div>
                        <ul class="faq">
                        </ul>

						<a href="javascript:;" class="btn_list_more" style="display:none">+ 더보기</a>

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

</body>

<script>

</script>
</html>
