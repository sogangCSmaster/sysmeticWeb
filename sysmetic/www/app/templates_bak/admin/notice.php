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
            <h3 class="notice">공지사항</h3>
			<!--
			<form action="/admin/notice" method="get">
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
			-->
            <a href="/admin/notice/write" title="공지사항 등록" class="write btn_admin notice"><span class="ir">공지사항 등록</span></a>

			<form action="/admin/notice/edit" method="post" id="edit_notice_form">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="60" /><col width="60" /><col width="*" /><col width="180" /><col width="100" /><col width="100" />
                <thead>
                <tr>
                    <td>선택</td>
                    <td>No</td>
                    <td>제목</td>
                    <td>날짜</td>
                    <td>상태</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($posts as $post){ ?>
                <tr>
                    <td><p><input type="checkbox" name="notice_ids[]" id="choice<?php echo $post['notice_id'] ?>" value="<?php echo $post['notice_id'] ?>" /><label for="choice<?php echo $post['notice_id'] ?>"></label></p></td>
                    <td class="num"><?php echo $post['notice_id'] ?></td>
                    <td class="first"><a href="/admin/notice/<?php echo $post['notice_id'] ?>"><?php echo htmlspecialchars($post['subject']) ?></a></td>
                    <td class="num"><?php echo date('<b>Y.m.d</b> H:i:s', strtotime($post['reg_at'])) ?></td>
                    <td class="btn">
					<?php if($post['is_open']){ ?>
					<button type="button" title="공개중" class="complete"><span class="ir">공개중</span></button>
					<?php }else{ ?>
					<button type="button" title="비공개" class="waiting"><span class="ir">비공개</span></button>
					<?php } ?>
					</td>
                    <td>
                        <a href="/admin/notice/<?php echo $post['notice_id'] ?>?page=<?php echo $page ?>" title="상세보기" class="btn_view"><span class="ir">상세보기</span></a>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <fieldset class="admin">
                <span class="fieldset_txt">선택한 공지사항을</span>  
                <button type="submit" title="삭제" class="admin1" onclick="setExec('delete');"><span class="ir">삭제</span></button> &nbsp;OR &nbsp;
                
                <div class="select open" style="width:100px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="is_open_flag" id="member0" class="option" type="radio" value="" checked="checked" /><label for="member0">선택</label></li>
                        <li><input name="is_open_flag" id="member1" class="option" type="radio" value="1" /><label for="member1">공개중</label></li>
                        <li><input name="is_open_flag" id="member2" class="option" type="radio" value="0" /><label for="member2">비공개</label></li>
                    </ul>
                </div> 
                &nbsp;(으)로 &nbsp;
				<input type="hidden" name="exec" id="exec" value="delete">
                <button type="button" title="변경" class="admin1" onclick="setExec('edit');$('#edit_notice_form').submit();"><span class="ir">변경</span></button>
            </fieldset>
			</form>

            <!-- 15개 목록 노출 후 페이징 -->
			<?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/notice?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/notice?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/notice?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/notice?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/admin/notice?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>