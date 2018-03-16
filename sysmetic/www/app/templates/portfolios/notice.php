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
            <table border="0" cellspacing="0" cellpadding="0" class="board notice">
            <col width="*" /><col width="200" />
                <thead>
                <tr>
                    <td class="left">제목</td>
                    <td>날짜</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($posts as $post){ ?>
                <tr>
                    <td class="name"><a href="/bbs/notice/<?php echo $post['notice_id'] ?>?page=<?php echo $page ?>"><?php echo htmlspecialchars($post['subject']) ?></a></td>
                    <td class="date"><?php echo date('<b>Y.m.d</b> H:i:s', strtotime($post['reg_at'])) ?></td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <!-- 15개 목록 노출 후 페이징 -->
			<?php if($total > 0){ ?>
            <div class="paging">
				<a href="/bbs/notice?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/bbs/notice?page=<?php echo $page_start ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/bbs/notice?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/bbs/notice?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/bbs/notice?page=<?php echo $total_page ?>" class="last">last</a>
            </div>
			<?php } ?>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>