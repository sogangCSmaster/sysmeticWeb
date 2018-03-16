<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 공지사항 - <?php echo htmlspecialchars($post['subject']) ?></title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">상품상담 관리</h3>

            <div class="board_view">
                <div class="qna_broker">
                    <dl>
                        <dt>상담신청</dt>
                        <dd><?php echo htmlspecialchars($post['user_name']); ?></dd>
                        <dt>PB</dt>
                        <dd/><?php echo htmlspecialchars($post['pb_name']); ?></dd>
                    </dl>
                    <dl>
                        <dt>관심있는 전략</dt>
                        <dd><?php echo htmlspecialchars($post['strategy']); ?></dd>
                        <dt>등록일시</dt>
                        <dd/><?php echo $post['reg_date'] ?></dd>
                    </dl>
                    <? if ($post['req_type'] == 'Offline') { ?>
                    <dl>
                        <dt>상담희망시간</dt>
                        <dd><?php echo htmlspecialchars($post['hope_date']); ?></dd>
                        <dt>연락처</dt>
                        <dd/><?php echo htmlspecialchars($post['mobile']); ?></dd>
                    </dl>
                    <? } ?>
                    <dl>
                        <dt>투자예상금액</dt>
                        <dd><?php echo htmlspecialchars($post['s_price']); ?></dd>
                        <dt>투자개시시점</dt>
                        <dd/><?php echo htmlspecialchars($post['s_date']); ?></dd>
                    </dl>
                </div>
                <p class="headline broker">
                    <b><?php echo htmlspecialchars($post['subject']); ?></b>
                </p>
                <p class="text">
                    <?php echo nl2br(htmlspecialchars($post['contents'])) ?>
                </p>
            </div>
            <div class="board_reply">
                <p class="sub_title reply">답변</p>
				<?php if(!empty($post['answer'])){ ?>
                <p class="text">
                    <?php echo nl2br(htmlspecialchars($post['answer'])) ?>
                </p>
				<?php }else{ ?>
                <p class="text no">
                    답변 대기 중입니다.
                </p>
				<?php } ?>
            </div>

            <p class="btn_board">
                <a title="목록" class="cancel" href="javascript:;" onclick="history.back();"><span class="ir">목록</span></a>
            </p>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>