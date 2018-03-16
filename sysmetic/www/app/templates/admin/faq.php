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
        $('#update').on('click', function() {
			if($('#edit_cate_form input[type=checkbox]:checked').length == 0){
				alert('선택된 게시물이 없습니다');
				return false;
			}

			if($('[type=radio][name=cate_id]:checked').val() == ''){
				alert('분류를 선택해주세요');
				return false;
			}

		    $('#edit_cate_form').submit();
		});
	});

	function confirmDelete(fidx){
		var result = confirm('해당 FAQ를 삭제하시겠습니까?');
		if(result){
			location.href='/admin/faq/'+fidx+'/delete';
		}
	}
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">FAQ 관리</h3>

            <div class="strategy_view">
                <div class="tab">
                    <a href="/admin/faq" title="종목관리" class="tab_<?=(!$cate) ? 'on' : 'off';?>"><span class="ir">전체</span></a>
                    <? foreach ($cates as $v) { ?>
                    <a href="/admin/faq?cate=<?=$v['cate_id']?>" title="<?=$v['name']?>" class="tab_<?=($cate != $v['cate_id']) ? 'off' : 'on';?>"><span class="ir"><?=$v['name']?></span></a>
                    <? } ?>
                </div>

                <a href="/admin/faq_cate" title="FAQ 등록" class="write btn_admin notice"><span class="ir">카테고리 관리</span></a>
            </div>

			<form action="/admin/faq" method="get">
            <fieldset class="admin_search">
                <input type="hidden" name="cate" value="<?=$cate?>" />
                <input id="" name="" type="text" title="질문" placeholder="질문" />
                <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>

            <a href="/admin/faq/write" title="FAQ 등록" class="write btn_admin notice"><span class="ir">FAQ 등록</span></a>

			<form action="/admin/faq/move" method="post" id="edit_cate_form">
            <input type="hidden" name="cate" value="<?=$cate?>" />
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="60" /><col width="60" /><col width="150" /><col width="*" /><col width="200" /><col width="100" />
                <thead>
                <tr>
                    <td>선택</td>
                    <td>No</td>
                    <td>분류</td>
                    <td>질문</td>
                    <td>작성일</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?
                $now = date('Y-m-d H:i:s');
                foreach($posts as $post){ ?>
                <tr>
                    <td><p><input type="checkbox" name="fidxs[]" id="choice<?php echo $post['fidx'] ?>" value="<?php echo $post['fidx'] ?>" /><label for="choice<?php echo $post['fidx'] ?>"></label></p></td>
                    <td class="num"><?php echo $post['fidx'] ?></td>
                    <td class="first"><?=$cates_name[$post['cate_id']]?></td>
                    <td class="first"><?php echo htmlspecialchars($post['subject']) ?></td>
                    <td class="num"><?=$post['reg_date']?></td>
                    <td>

                        <a href="/admin/faq/<?php echo $post['fidx'] ?>/edit" title="수정" class="sbtn"><span class="ir">수정</span></a>
                        <button type="button" onclick="confirmDelete(<?php echo $post['fidx'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>

                    </td>
                </tr>
				<? } ?>
                </tbody>
            </table>
            
            <fieldset class="admin">
                <span class="fieldset_txt">선택한 항목분류를</span>.
    
                <div class="select open" style="width:150px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="cate_id" id="cate" class="option" type="radio" value="" checked><label for="cate_id">카테고리</label></li>
                        <? foreach ($cates as $k => $v) { ?>
                        <li><input name="cate_id" id="cate<?=$k?>" class="option" type="radio" value="<?=$v['cate_id']?>" /><label for="cate<?=$k?>"><?=$v['name']?></label></li>
                        <? } ?>
                    </ul>
                </div> 
                &nbsp;(으)로 &nbsp;&nbsp;
                <button type="button" title="변경" class="admin1" id="update"><span class="ir">변경</span></button>
            </fieldset>

			</form>

            <!-- 15개 목록 노출 후 페이징 -->
			<?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/faq?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/faq?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/faq?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/faq?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/admin/faq?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>