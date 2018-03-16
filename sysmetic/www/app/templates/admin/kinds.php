<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 종류 관리</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#kind_add_form').submit(function(){
			if(!$('#name').val()){
				alert('종류를 입력하세요');
				$('#name').focus();
				return false;
			}

			return true;
		});

		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>
	});

	function confirmDelete(kind_id){
		var result = confirm('삭제하시겠습니까?');
		if(result){
			location.href='/admin/kinds/'+kind_id+'/delete';
		}
	}

	function openEdit(kind_id, name, image_url, sorting){
		$('#kind_edit_form').show();
		$('#edit_kind_id').val(kind_id);
		$('#edit_name').val(name);
		$('#edit_sorting').val(sorting);
		if(image_url){
			$('#current_kind_image').show();
			$('#current_kind_image').attr('src', image_url);
		}else{
			$('#current_kind_image').hide();
		}
	}
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">종류 관리</h3>

            <div class="strategy_view">
                <div class="tab">
                    <a href="/admin/items" title="종목관리" class="tab_off"><span class="ir">종목관리</span></a>
                    <a href="/admin/kinds" title="종류관리" class="tab_on"><span class="ir">종류관리</span></a>
                    <a href="/admin/types" title="유형관리" class="tab_off"><span class="ir">유형관리</span></a>
                </div>
            </div>

            <table border="0" cellspacing="1" cellpadding="0" class="admin_table" style="width:500px;">
            <col width="70" /><col width="*" /><col width="150" />
                <thead>
                <tr>
                    <td>순서</td>
                    <td>종류명</td>
                    <td>관리</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($kinds as $kind){ ?>
                <tr>
                    <td><?php echo $kind['sorting'] ?></td>
                    <td><?php echo htmlspecialchars($kind['name']) ?></td>
                    <td>
                        <button type="button" onclick="openEdit(<?php echo $kind['kind_id'] ?>, '<?php echo htmlspecialchars($kind['name']) ?>', '<?php if(!empty($kind['icon'])) echo htmlspecialchars($kind['icon']) ?>', <?php echo $kind['sorting'] ?>)" title="수정" class="sbtn"><span class="ir">수정</span></button> 
                        <button type="button" onclick="confirmDelete(<?php echo $kind['kind_id'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>

			<form action="/admin/kinds/add" method="post" id="kind_add_form" enctype="multipart/form-data">
            <fieldset class="admin">
                <legend>종류추가</legend>
                <b>종류추가 : </b>
				<input id="sorting" name="sorting" type="text" title="순서 입력" value="1" style="width:60px;" required="required" />
                <input id="name" name="name" type="text" title="종류명 입력" style="width:60px;" required="required" />

                <button type="submit" title="추가" class="admin1"><span class="ir">추가</span></button>
            </fieldset>
			</form>

            <!-- 종목 수정 -->
			<form action="/admin/kinds/edit" method="post" enctype="multipart/form-data" id="kind_edit_form" style="display:none;">
            <fieldset class="admin">
                <legend>종류수정</legend>
                <b>종류수정 : </b>
				<input type="hidden" id="edit_kind_id" name="edit_kind_id" value="">
				<input id="edit_sorting" name="sorting" type="text" title="순서 입력" value="1" class="ready" style="width:60px;" required="required" />    
                <input id="edit_name" name="name" type="text" title="종류명 입력" value="선물" class="ready" style="width:60px;" required="required" />    

                <button type="submit" title="변경" class="admin1"><span class="ir">변경</span></button>
            </fieldset>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>