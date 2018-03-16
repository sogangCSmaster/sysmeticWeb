<!doctype html>
<html lang="ko">
<head>
    <title>PB게시판</title>
    <? include_once $skinDir."/common/head.php" ?>
    <script>
    $(function() {
        // 코멘트
        $('.reply_area').load("/pb/bbs/<?=$info['bid']?>/reply", function() {
            getComment(1);
        });

        $(".board_detail").viewimageresize();
    });

    var page = 1;
    function getComment(loadPage) {
        $.ajax({
            mthod: 'get',
            data: {page: loadPage},
            async: false,
            url: "/pb/bbs/<?=$info['bid']?>/reply_list",
            dataType: 'html',
        }).done(function(html) {
            $('.reply_list').append(html);

            if ($('#total_cnt').data('cnt') > $('.lst').length)
            {
                page++;
                $('.btn_list_more').show();
                $('.btn_list_more').unbind('click').click(function() {
                    getComment(page);
                });
            } else {
                $('.btn_list_more').hide();                
            }
        });
    }

    function moveReview(page) {
        $('.review_area').load('/strategies/<?=$strategy_id?>/reviews?page='+page);
    }

    function deleteReview(id) {
        if (!confirm('삭제하시겠습니까?')) {
            return;
        }

        $.post('/strategies/<?=$strategy_id?>/reviews/' + id + '/delete', $(this).serialize(), function(data){
            if (data.result) {
                moveReview(1);
            } else {
                alert('처리 중 요류가 발생하였습니다');
            }
        }, 'json');
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
			<section class="area pb_only detail">
				<div class="content board_detail">	
					<div class="head">
						<div class="info">
							<p class="subject"><?=$info['subject']?></p>
							<span class="time"><?=$info['reg_date']?></span>
						</div>
					</div>
					<div class="cont no_pd_t">
						<div class="writer_info">
							<img src="<?=getProfileImg($info['user']['picture'])?>" alt="" class="img_photo" />
							<span class="name">[<?=$company = ($info['user']['user_type'] == 'A') ? '관리자' : $info['user']['company'];?>] <?=$info['user']['name']?></span>
                            <? if ($info['user']['user_type'] == 'P') {?>
							<a href="/lounge/<?=$info['uid']?>" class="btn_lounge"><img src="/images/sub/btn_lounge_coffee.gif" alt="라운지" /></a>
                            <? } ?>
						</div>

                            <div class="picture">
                                <? foreach ($info['images'] as $v) { ?>
                                <img src="/data/contents/<?=$v['save_name']?>" />
                                <? } ?>
                            </div>

                            <p>
                                <?=$info['contents']?>
                            </p>
                            <? if (count($info['files'])) { ?>
                            <div class="file">
                                <dl>
                                    <dt>첨부파일</dt>
                                    <dd>
                                        <ul>
                                            <? foreach ($info['files'] as $v) { ?>
                                            <li>
                                                <?=$v['file_name']?>
                                                <a href="/pb/bbs/download/<?=$v['fid']?>"><img src="/images/sub/btn_file_download.gif" alt="다운로드" /></a>
                                            </li>
                                            <? } ?>
                                        </ul>
                                    </dd>
                                </dl>
                            </div>
                            <? } ?>
                        </div>
                        <div class="btn_area">
                            <? if ($mine or $_SESSION['user']['user_type']=='A') { ?>
                            <a href="/pb/bbs/<?=$info['bid']?>/modify" class="btn modify">수정</a>
                            <a href="/pb/bbs/<?=$info['bid']?>/delete" onclick="if(!confirm('삭제하시겠습니까?')) return false;" class="btn delete">삭제</a>
                            <? } ?>
                            <a href="/pb/bbs" class="btn list">목록</a>
                        </div>


						<div class="reply_area">

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
