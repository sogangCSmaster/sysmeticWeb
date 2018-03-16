<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 공지사항</title>	
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
					<?php foreach($post['attachments'] as $attachment){ ?>
					<p><img src="<?php echo $attachment['url'] ?>" onload="fitImageSize(this, '<?php echo $attachment['url'] ?>', 894, 894);" style="display:none"></p>
					<?php } ?>
					<?php echo nl2br(htmlspecialchars($post['contents'])) ?>
                </p>
                <p class="img">
                </p>
            </div>
            <p class="btn_board">
                <a title="목록" class="cancel" href="/bbs/notice?page=<?php echo $page ?>"><span class="ir">목록</span></a>
            </p>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>