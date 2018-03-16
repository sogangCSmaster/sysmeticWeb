<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - Blocker Contacts 관리</title>	
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
            <h3 class="admin_contact">Blocker Contacts 관리</h3>
            <div class="board_view">
                <div class="qna_broker">
                    <dl>
                        <dt>브로커</dt>
                        <dd><?php echo htmlspecialchars($contact['target_value_text']); ?></dd>
                        <dt>질문자</dt>
                        <dd/><?php echo htmlspecialchars($contact['name']); ?></dd>
                    </dl>
                </div>
                <p class="headline broker">
                    
                    <span><?php echo date('<b>Y.m.d</b> H:i:s', strtotime($contact['reg_at'])) ?></span>
                </p>
                <p class="text">
                    <?php echo nl2br(htmlspecialchars($contact['question'])) ?>
                </p>
            </div>
			<form action="/admin/contacts/<?php echo $contact['qna_id'] ?>/answer" method="post" id="answer_form">
            <div class="board_reply">
                <p class="sub_title reply">답변</p>
				<?php if(!empty($contact['answer'])){ ?>
                <p class="text">
                    <?php echo nl2br(htmlspecialchars($contact['answer'])) ?>
                </p>
				<?php }else{ ?>
                <p class="text no">
                    답변 대기 중입니다.
                </p>
				
                <p class="text write">
					<input type="hidden" name="qna_id" value="<?php echo $contact['qna_id'] ?>">
                    <textarea name="answer" id="answer" required="required"></textarea>
                </p>
				<?php } ?>
            </div>

            <p class="btn_board">
                <!------ 트레이더와 브로커만 노출 ------->
				<?php if(empty($contact['answer'])){ ?>
                <button type="submit" title="답변하기" class="submit"><span class="ir">답변하기</span></button>
				<?php } ?>
				<!--
                <button type="button" title="완료" class="submit"><span class="ir">완료</span></button> --><!------ //답변 작성 시 노출 ------->
                <!------ //트레이더와 브로커만 노출 ------->
                <button type="reset" title="목록" class="cancel" onclick="location.href='/admin/contacts?page=<?php echo $page ?>'"><span class="ir">목록</span></button><!------ //답변 완료 시 목록 버튼만 노출됨 ------->
            </p>
			</form>
        </div> 
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>