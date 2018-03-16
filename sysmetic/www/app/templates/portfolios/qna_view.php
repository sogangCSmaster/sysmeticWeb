<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 나의 Q&A</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="qna">나의 Q&A</h3>
            <div class="board_view">
                <p class="headline">
                    <b><?php if($q_item['target'] == 'broker') echo '브로커에게 한 문의입니다'; else echo htmlspecialchars($q_item['strategy_name']); ?></b>
                    <span><?php echo date('<b>Y.m.d</b> H:i:s', strtotime($q_item['reg_at'])) ?></span>
                </p>
                <p class="text">
                    <?php echo nl2br(htmlspecialchars($q_item['question'])) ?>
                </p>
            </div>
            <div class="board_reply">
                <p class="sub_title reply">답변</p>
				<?php if(!empty($q_item['answer'])){ ?>
                <p class="text">
                    <?php echo nl2br(htmlspecialchars($q_item['answer'])) ?>
                </p>
				<?php }else{ ?>
                <p class="text no">
                    답변 대기 중입니다.
                </p>
				<!--
                <p class="text write">
                    <textarea>
                    </textarea>
                </p>
				-->
				<?php } ?>
            </div>

            <p class="btn_board">
                <!------ 트레이더와 브로커만 노출 ------->
                <!-- <button id="" type="" title="답변하기" class="submit"><span class="ir">답변하기</span></button> -->
                <!-- <button id="" type="" title="완료" class="submit"><span class="ir">완료</span></button> --><!------ //답변 작성 시 노출 ------->
                <!------ //트레이더와 브로커만 노출 ------->
                <a title="목록" class="cancel" href="/qna"><span class="ir">목록</span></a><!------ //답변 완료 시 목록 버튼만 노출됨 ------->
            </p>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>