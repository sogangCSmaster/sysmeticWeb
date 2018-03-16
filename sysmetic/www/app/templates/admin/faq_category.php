<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 종목 관리</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#cate_add_form').submit(function(){
			if(!$('#name').val()){
				alert('분류를 입력하세요');
				$('#name').focus();
				return false;
			}

			return true;
		});

		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>
	});

	function confirmDelete(cate_id){
		var result = confirm('삭제하시겠습니까?');
		if(result){
			location.href='/admin/faq_cate/'+cate_id+'/delete';
		}
	}

	function openEdit(cate_id, name, sorting){
		$('#cate_edit_form').show();
		$('#edit_cate_id').val(cate_id);
		$('#edit_name').val(name);
		$('#edit_sorting').val(sorting);
	}
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">FAQ 카테고리 관리</h3>

            <div class="strategy_view">
                <div class="tab">
                    <a href="/admin/faq" title="종목관리" class="tab_<?=(!$cate) ? 'off' : 'on';?>"><span class="ir">전체</span></a>
                    <? foreach ($cates as $v) { ?>
                    <a href="/admin/faq?cate=<?=$v['cate_id']?>" title="<?=$v['name']?>" class="tab_<?=($cate != $v['cate_id']) ? 'off' : 'on';?>"><span class="ir"><?=$v['name']?></span></a>
                    <? } ?>
                </div>

                <a href="/admin/faq_cate" title="FAQ 등록" class="write btn_admin notice"><span class="ir">카테고리 관리</span></a>
            </div>

            <table border="0" cellspacing="1" cellpadding="0" class="admin_table" style="width:500px;">
            <col width="70" /><col width="*" /><col width="150" />
                <thead>
                <tr>
                    <td>순서</td>
                    <td>분류</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($cates as $type){ ?>
                <tr>
                    <td><?php echo $type['sorting'] ?></td>
                    <td><?php echo htmlspecialchars($type['name']) ?></td>
                    <td>
                        <button type="button" onclick="openEdit(<?php echo $type['cate_id'] ?>, '<?php echo htmlspecialchars($type['name']) ?>', <?php echo $type['sorting'] ?>)" title="수정" class="sbtn"><span class="ir">수정</span></button> 
                        <button type="button" onclick="confirmDelete(<?php echo $type['cate_id'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>

			<form action="/admin/faq_cate/add" method="post" id="cate_add_form" enctype="multipart/form-data">
            <fieldset class="admin">
                <legend>분류추가</legend>
                <b>분류추가 : </b>
				<input id="sorting" name="sorting" type="text" title="순서 입력" value="1" style="width:60px;" required="required" />
                <input id="name" name="name" type="text" title="분류명 입력" style="width:160px;" required="required" />
                <button type="submit" title="추가" class="admin1"><span class="ir">추가</span></button>
            </fieldset>
			</form>

            <!-- 종목 수정 -->
			<form action="/admin/faq_cate/edit" method="post" enctype="multipart/form-data" id="cate_edit_form" style="display:none;">
            <fieldset class="admin">
                <legend>분류수정</legend>
                <b>분류수정 : </b>
				<input type="hidden" id="edit_cate_id" name="edit_cate_id" value="">
				<input id="edit_sorting" name="sorting" type="text" title="순서 입력" value="1" class="ready" style="width:60px;" required="required" />    
                <input id="edit_name" name="name" type="text" title="분류명 입력" value="" class="ready" style="width:160px;" required="required" />
                <button type="submit" title="변경" class="admin1"><span class="ir">변경</span></button>
            </fieldset>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>