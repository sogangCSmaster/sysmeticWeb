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

		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>
	});
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">상담하기 관리</h3>
            <div class="board_view">
                <div class="qna_broker">
                    <dl>
                        <dt>질문자</dt>
                        <dd/><?php echo htmlspecialchars($contact['name']); ?></dd>
                        <dt>날짜</dt>
                        <dd><?php echo date('<b>Y.m.d</b> H:i:s', strtotime($contact['reg_at'])) ?></dd>
                    </dl>
                </div>
                <p class="headline broker">
                    <b>Q. [<?=$contact['target_value_text']?>] <?php echo htmlspecialchars($contact['subject']) ?></b>
                </p>
                <p class="text">
                    <?php echo nl2br(htmlspecialchars($contact['question'])) ?>
                </p>
            </div>
			<form action="/admin/customer/<?php echo $contact['cus_id'] ?>/answer" method="post" id="answer_form">
            <div class="board_reply">
                <p class="sub_title reply">답변</p>
                <p class="text write">
					<input type="hidden" name="cus_id" value="<?php echo $contact['cus_id'] ?>">
					<input type="hidden" name="mobile" value="<?php echo $contact['mobile'] ?>">
                    <textarea name="answer" id="answer" required="required"><?=$contact['answer']?></textarea>
                </p>
            </div>

            <p class="btn_board">
                <!------ 트레이더와 브로커만 노출 ------->
                <button type="submit" title="답변하기" class="submit"><span class="ir">답변하기</span></button>
                <button type="reset" title="목록" class="cancel" onclick="location.href='/admin/customer?page=<?php echo $page ?>&answer=<?=$answer?>'"><span class="ir">목록</span></button>
            </p>
			</form>
        </div> 
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>