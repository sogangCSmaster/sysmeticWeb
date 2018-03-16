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
            <h3 class="notice">공지사항</h3>
            <div class="board_view">
                <p class="headline">
                    <b><?php echo htmlspecialchars($post['subject']) ?></b>
                    <span><?php echo date('<b>Y.m.d</b> H:i:s', strtotime($post['reg_at'])) ?></span>
                </p>
                <p class="text">
                    <?php echo nl2br(htmlspecialchars($post['contents'])); ?>
                </p>
				<?php foreach($post['attachments'] as $attachment){ ?>
				<p class="img"><img src="<?php echo $attachment['url'] ?>" onload="fitImageSize(this, '<?php echo $attachment['url'] ?>', 894, 894);" style="display:none"></p>
				<?php } ?>
            </div>

			<form action="/admin/notice/<?php echo $post['notice_id'] ?>/edit" method="post">
            <div class="change">
                <div class="select open" style="width:100px;">
                    <div class="myValue"></div>
                    <ul class="iList">
						<!--
                        <li><input name="member" id="member0" class="option" type="radio" /><label for="member0">선택</label></li>
						-->
                        <li><input name="is_open" id="member1" class="option" type="radio" value="1"<?php if(!$post['is_open']) echo ' checked="checked"' ?> /><label for="member1">공개중</label></li>
                        <li><input name="is_open" id="member2" class="option" type="radio" value="0"<?php if($post['is_open']) echo ' checked="checked"' ?> /><label for="member2">비공개</label></li>
                    </ul>
                </div> 
                &nbsp;(으)로 &nbsp;
				<input type="hidden" name="page" value="<?php echo $page ?>">
                <button type="submit" title="변경" class="admin1"><span class="ir">변경</span></button>
            </div>
			</form>

            <p class="btn_board">
                <a title="삭제" class="submit" href="/admin/notice/<?php echo $post['notice_id'] ?>/delete?page=<?php echo $page ?>" onclick="return confirm('삭제하시겠습니까?');"><span class="ir">삭제</span></a>
                <a title="목록" class="cancel" href="/admin/notice?page=<?php echo $page ?>"><span class="ir">목록</span></a>
            </p>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>