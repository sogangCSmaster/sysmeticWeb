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
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">공지사항 관리</h3>
            <div class="board_view">
                <p class="headline">
                    <b><?php echo htmlspecialchars($post['subject']) ?></b>
                    <span><?php echo date('<b>Y.m.d</b> H:i:s', strtotime($post['reg_at'])) ?></span>
                </p>
                <p class="text">
                    <?php echo $post['contents']; ?>
                </p>

				<?
				//Array ( [0] => Array ( [fid] => 12 [notice_id] => 25 [file_type] => FILE [save_name] => 4d4fe85c95587f1fd8d1568dd4d3fe0a.pdf [file_name] => 대기업소주지분구조도.pdf ) )
				if (isset($atts) && is_array($atts)) {
					foreach ($atts as $v) {

				?>
					<div align=right>첨부파일 : <a href="/notice/<?=$v['save_name']?>" target="_blank"><?=$v['file_name']?></a></div>
				<?
					}
				}
				?>

            </div>

            <!--
			<form action="/admin/notice/<?php echo $post['notice_id'] ?>/edit" method="post">
            <div class="change">
                <div class="select open" style="width:100px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="is_open" id="member1" class="option" type="radio" value="1"<?php if(!$post['is_open']) echo ' checked="checked"' ?> /><label for="member1">공개중</label></li>
                        <li><input name="is_open" id="member2" class="option" type="radio" value="0"<?php if($post['is_open']) echo ' checked="checked"' ?> /><label for="member2">비공개</label></li>
                    </ul>
                </div> 
                &nbsp;(으)로 &nbsp;
				<input type="hidden" name="page" value="<?php echo $page ?>">
                <button type="submit" title="변경" class="admin1"><span class="ir">변경</span></button>
            </div>
			</form>
            -->

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