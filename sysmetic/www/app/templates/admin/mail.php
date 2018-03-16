<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 메일 발송</title>	
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
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">메일발송</h3>
            <a href="/admin/mail/write" title="메일발송예약" class="write btn_admin notice"><span class="ir">메일발송 등록</span></a>

            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="*" /><col width="100" /><col width="180" /><col width="100" /><col width="100" />
                <thead>
                <tr>
                    <td>제목</td>
                    <td>발송타입</td>
                    <td>발송 날짜 / 시간</td>
                    <td>상태</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($mails as $mail){ ?>
                <tr>
                    <td class="first"><?php echo htmlspecialchars($mail['subject']) ?></td>
                    <td><?php if($mail['mail_type'] == 'normal') echo '공지메일'; else echo '홍보메일'; ?></td>
                    <td class="num"><?php echo date('<b>Y.m.d</b> H:i:s', $mail['reserve_at']) ?></td>
                    <td class="btn">
					<?php if($mail['status'] == 'queued'){ ?>
					<button type="button" title="발송대기" class="waiting"><span class="ir">발송대기</span></button>
					<?php }else if($mail['status'] == 'sent'){ ?>
					<button type="button" title="발송완료" class="complete"><span class="ir">발송완료</span></button>
					<?php } ?>
					<!--
					<button type="button" title="예약취소" class="complete line"><span class="ir">예약취소</span></button>
					-->
					</td>
                    <td>
                        <a href="/admin/mail/<?php echo $mail['mail_id'] ?>?page=<?php echo $page ?>" title="상세보기" class="btn_view"><span class="ir">상세보기</span></a>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>

            <!-- 15개 목록 노출 후 페이징 -->
			<?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/mail?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/mail?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/mail?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/mail?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/admin/mail?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>