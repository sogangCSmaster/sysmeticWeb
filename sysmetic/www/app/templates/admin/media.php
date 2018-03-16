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
	$(function(){
		$('#edit_notice_form').submit(function(){
			if($('#edit_notice_form input[type=checkbox]:checked').length == 0){
				alert('선택된 게시물이 없습니다');
				return false;
			}

			if($('#exec').val() == 'edit'){
				if($('[type=radio][name=is_open_flag]:checked').val() == ''){
					alert('상태를 선택해주세요');
					return false;
				}
			}
		});
	});

	function confirmDelete(midx){
		var result = confirm('해당 기사를 삭제하시겠습니까?');
		if(result){
			location.href='/admin/media/'+midx+'/delete';
		}
	}

	function setExec(value){
		$('#exec').val(value);
	}
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">미디어룸 관리</h3>

			<form action="/admin/media" method="get">
            <fieldset class="admin_search">      
                <div class="select open" style="width:95px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="search" id="search0" class="option" type="radio" checked="checked" /><label for="search0">선택</label></li>
                        <li><input name="search" id="search1" class="option" type="radio" /><label for="search1">제목</label></li>
                        <li><input name="search" id="search2" class="option" type="radio" /><label for="search2">내용</label></li>
                    </ul>
                </div> 
                <input id="" name="" type="text" title="검색어 입력" />
                <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>

            <a href="/admin/media/write" title="교육 등록" class="write btn_admin notice"><span class="ir">기사 등록</span></a>

			<form action="/admin/media/edit" method="post" id="edit_notice_form">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="60" /><col width="*" /><col width="200" /><col width="100" />
                <thead>
                <tr>
                    <td>No</td>
                    <td>제목</td>
                    <td>작성일</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?
                $now = date('Y-m-d H:i:s');
                foreach($posts as $post){ ?>
                <tr>
                    <td class="num"><?php echo $post['midx'] ?></td>
                    <td class="first"><?php echo htmlspecialchars($post['subject']) ?></td>
                    <td class="num"><?=$post['reg_date']?></td>
                    <td>

                        <a href="/admin/media/<?php echo $post['midx'] ?>/edit" title="수정" class="sbtn"><span class="ir">수정</span></a>
                        <button type="button" onclick="confirmDelete(<?php echo $post['midx'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>

                    </td>
                </tr>
				<? } ?>
                </tbody>
            </table>
            
			</form>

            <!-- 15개 목록 노출 후 페이징 -->
			<?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/media?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/media?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/media?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/media?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/admin/media?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>