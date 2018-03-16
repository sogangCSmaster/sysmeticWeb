<!doctype html>
<html lang="ko">
<head>
    <title>라운지 | SYSMETIC</title>
    <? include_once $skinDir."/common/head.php" ?>
	<meta id="meta_og_title" property="og:title" content="<?=$info['subject']?>">
	<meta id="meta_og_description" property="og:description" content='<?=strip_tags($info['contents'])?>' />
	<meta property="og:image" content="http://sysmetic-live.mypro.co.kr/images/common/sysmetic_logo.png" />
    <script>
    $(function() {
        // 코멘트
        $('.reply_area').load("/lounge/<?=$pb['uid']?>/contents/<?=$info['cidx']?>/reply", function() {
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
            url: "/lounge/<?=$pb['uid']?>/contents/<?=$info['cidx']?>/reply_list",
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
            <section class="area pb_detail">
                <div class="area">
                    <div class="head">
                        <? include $skinDir."/lounge/pb_info.php" ?>
                    </div>
                    <div class="content board_detail">
						<div class="head">
							<div class="info">
								<p class="subject"><?=$info['subject']?></p>
								<span class="time"><?=$info['reg_date']?></span>
							</div>
							<div class="sns">
								<a href="javascript:;" onclick="goFacebook('<?=makeFacebookShare($info['subject'],"http://sysmetic.co.kr/lounge/".$vuid."/contents/".$vcidx)?>')"><img src="/images/sub/btn_share_fb_s.png" alt="facebook 공유" /></a>
								<a href="javascript:;" onclick="goTwitter('<?=makeTwitterShare($info['subject']);?>')"><img src="/images/sub/btn_share_twitter_s.png" alt="twitter 공유" /></a>
								<a href="javascript:;" id="kakao-link-btn" class="btn_share"><img src="/images/sub/btn_share_kakao_s.png" alt="kakaotalk 공유" /></a>
							</div>
						</div>
                        <div class="cont">
                            <p>
                                <? foreach ($info['images'] as $v) { ?>
                                <div><img src="/data/contents/<?=$v['save_name']?>" /></div>
                                <? } ?>
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
                                                <a href="/lounge/<?=$pb['uid']?>/contents/download/<?=$v['fid']?>"><img src="/images/sub/btn_file_download.gif" alt="다운로드" /></a>
                                            </li>
                                            <? } ?>
                                        </ul>
                                    </dd>
                                </dl>
                            </div>
                            <? } ?>
                        </div>
                        <div class="btn_area">
                            <? if ($mine) { ?>
                            <a href="/lounge/<?=$info['uid']?>/contents/<?=$info['cidx']?>/modify" class="btn modify">수정</a>
                            <a href="/lounge/<?=$info['uid']?>/contents/<?=$info['cidx']?>/delete" onclick="if(!confirm('삭제하시겠습니까?')) return false;" class="btn delete">삭제</a>
                            <? } ?>
                            <a href="/lounge/<?=$info['uid']?>/contents" class="btn list">목록</a>
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
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type='text/javascript'>
//<![CDATA[
// // 사용할 앱의 JavaScript 키를 설정해 주세요.
Kakao.init('b41e79273a98cbc168b495ab60a6ae51');
// // 카카오링크 버튼을 생성합니다. 처음 한번만 호출하면 됩니다.
Kakao.Link.createTalkLinkButton({
  container: '#kakao-link-btn',
  label: '<?=$info['subject']?>',
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
