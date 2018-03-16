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

	function confirmDelete(eidx){
		var result = confirm('해당 교육을 삭제하시겠습니까?');
		if(result){
			location.href='/admin/education/'+eidx+'/delete';
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
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">교육 관리</h3>

			<form action="/admin/education" method="get">
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

            <a href="/admin/education/write" title="교육 등록" class="write btn_admin notice"><span class="ir">교육 등록</span></a>

			<form action="/admin/education/edit" method="post" id="edit_notice_form">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="60" /><col width="70" /><col width="*" /><col width="120" /><col width="100" /><col width="100" /><col width="100" />
                <thead>
                <tr>
                    <td>No</td>
                    <td>카테고리</td>
                    <td>교육명</td>
                    <td>등록일</td>
                    <td>상태</td>
                    <td>신청</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?
                $now = date('Y-m-d H:i:s');
                foreach($posts as $post){ ?>
                <tr>
                    <td class="num"><?php echo $post['eidx'] ?></td>
                    <td class="num"><?php echo $post['type'] ?></td>
                    <td class="first"><?php echo htmlspecialchars($post['subject']) ?></td>
                    <td class="num"><?=substr($post['reg_date'], 0, 10) ?></td>
                    <td class="btn">
					<? if ($post['type'] == 'ON') { ?>
					<button type="button" title="접수중" class="complete"><span class="ir">접수중</span></button>
					<?
                    } else {
                        if ($post['e_end_date'] < $now) { // 교육기간 종료 : 종료
                            $status = '<button type="button" title="종료" class="complete2"><span class="ir">종료</span></button>';
                        } else if ($post['a_start_date'] < $now && $post['a_end_date'] > $now) { // 접수기간 중 : 접수중
                            $status = '<button type="button" title="접수중" class="complete"><span class="ir">접수중</span></button>';
                        } else if ($post['a_start_date'] > $now) { // 접수전 : 대기중
                            $status = '<button type="button" title="대기중" class="waiting"><span class="ir">대기중</span></button>';
                        } else {
                            $status = '<button type="button" title="진행중" class="complete2"><span class="ir">진행중</span></button>';
                        }
                        echo $status;
                    ?>
					<? } ?>
					</td>
                    <td><?=number_format($post['apply'])?></td>
                    <td>

                        <a href="/admin/education/<?php echo $post['eidx'] ?>/edit" title="수정" class="sbtn"><span class="ir">수정</span></a>
                        <button type="button" onclick="confirmDelete(<?php echo $post['eidx'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>

                    </td>
                </tr>
				<? } ?>
                </tbody>
            </table>
            
			</form>

            <!-- 15개 목록 노출 후 페이징 -->
			<?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/education?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/education?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/education?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/education?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/admin/education?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>