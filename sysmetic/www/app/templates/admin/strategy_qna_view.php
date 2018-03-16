<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - Blocker posts 관리</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#answer_form').submit(function(){
			if(!$('#answer').val()){
				alert('내용을 입력하세요');
				$('#answer').focus();
				return false;
			}

			return true;
		});
	});
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="admin_post">Blocker posts 관리</h3>
            <div class="board_view">
                <div class="qna_broker">
                    <dl>
                        <dt>브로커</dt>
                        <dd><?php echo htmlspecialchars($post['target_value_text']); ?>&nbsp;</dd>
                        <dt>질문자</dt>
                        <dd/><?php echo htmlspecialchars($post['name']); ?></dd>
                    </dl>
                </div>
                <p class="headline broker">
                    
                    <span><?php echo date('<b>Y.m.d</b> H:i:s', strtotime($post['reg_at'])) ?></span>
                </p>
                <p class="text">
                    <?php echo nl2br(htmlspecialchars($post['question'])) ?>
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
                <button type="reset" title="목록" class="cancel" onclick="history.back();"><span class="ir">목록</span></button>
            </p>
			</form>
        </div> 
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>