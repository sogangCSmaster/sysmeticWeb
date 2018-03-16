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
	<script>
	function confirmDelete(bidx){
		var result = confirm('해당 배너를 삭제하시겠습니까?');
		if(result){
			location.href='/admin/banner/'+bidx+'/delete';
		}
	}
	</script>
</head>


<body>
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">배너 관리</h3>

			<form action="/admin/banner" method="get">
            <fieldset class="admin_search">
                <input id="" name="" type="text" title="검색어 입력" placeholder="제목" />
                <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>

            <a href="/admin/banner/write" title="배너 등록" class="write btn_admin notice"><span class="ir">배너 등록</span></a>

			<form action="/admin/banner/edit" method="post" id="edit_notice_form">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="60" /><col width="*" /><col width="200" /><col width="150" /><col width="100" />
                <thead>
                <tr>
                    <td>No</td>
                    <td>제목</td>
                    <td>노출기간</td>
                    <td>상태</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($posts as $post){ ?>
                <tr>
                    <td class="num"><?php echo $post['bidx'] ?></td>
                    <td class="first"><?php echo htmlspecialchars($post['subject']) ?></td>
                    <td class="num">
                    시작일 : <?=substr($post['start_date'], 0, 16) ?><br />
                    종료일 : <?=substr($post['end_date'], 0, 16)?></td>
                    <td class="num">
                    <?
                    $today = date('Y-m-d H:i:s');
                    if ($today > $post['end_date']) {
                        $status = '<button type="button" title="종료" class="complete"><span class="ir">종료</span></button>';
                    } else if ($today > $post['start_date']) {
                        $status = '<button type="button" title="노출중" class="complete2"><span class="ir">노출중</span></button>';
                    } else {
                        $status = '<button type="button" title="대기중" class="waiting"><span class="ir">대기중</span></button>';
                    }

                    echo $status;
                    ?>
                    </td>
                    <td>
                        <a href="/admin/banner/<?php echo $post['bidx'] ?>/edit" title="수정" class="sbtn"><span class="ir">수정</span></a>
                        <button type="button" onclick="confirmDelete(<?php echo $post['bidx'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="5"><img src="/data/banner/<?=$post['banner_image']?>" style="max-width:100%" /></td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
			</form>

			<?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/banner?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/banner?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/banner?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/banner?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/admin/banner?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>