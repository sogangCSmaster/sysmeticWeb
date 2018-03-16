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
		$('#type_add_form').submit(function(){
			if(!$('#name').val()){
				alert('유형을 입력하세요');
				$('#name').focus();
				return false;
			}

			return true;
		});

		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>
	});

	function confirmDelete(type_id){
		var result = confirm('삭제하시겠습니까?');
		if(result){
			location.href='/admin/types/'+type_id+'/delete';
		}
	}

	function openEdit(type_id, name, image_url, sorting){
		$('#type_edit_form').show();
		$('#edit_type_id').val(type_id);
		$('#edit_name').val(name);
		$('#edit_sorting').val(sorting);
		if(image_url){
			$('#current_type_image').show();
			$('#current_type_image').attr('src', image_url);
		}else{
			$('#current_type_image').hide();
			// $('#current_type_image').attr('src', image_url);
		}
	}
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">유형 관리</h3>

            <div class="strategy_view">
                <div class="tab">
                    <a href="/admin/items" title="종목관리" class="tab_of"><span class="ir">종목관리</span></a>
                    <a href="/admin/kinds" title="종류관리" class="tab_off"><span class="ir">종류관리</span></a>
                    <a href="/admin/types" title="유형관리" class="tab_on"><span class="ir">유형관리</span></a>
                </div>
            </div>

            <table border="0" cellspacing="1" cellpadding="0" class="admin_table" style="width:500px;">
            <col width="70" /><col width="*" /><col width="150" /><col width="150" />
                <thead>
                <tr>
                    <td>순서</td>
                    <td>상품유형</td>
                    <td>아이콘</td>
                    <td>관리</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($types as $type){ ?>
                <tr>
                    <td><?php echo $type['sorting'] ?></td>
                    <td><?php echo htmlspecialchars($type['name']) ?></td>
                    <td><?php if(!empty($type['icon'])){ ?><img src="<?php echo htmlspecialchars($type['icon']) ?>" /><?php } ?></td>
					<!-- <td><?php echo $type['sorting'] ?></td> -->
                    <td>
                        <button type="button" onclick="openEdit(<?php echo $type['type_id'] ?>, '<?php echo htmlspecialchars($type['name']) ?>', '<?php if(!empty($type['icon'])) echo htmlspecialchars($type['icon']) ?>', <?php echo $type['sorting'] ?>)" title="수정" class="sbtn"><span class="ir">수정</span></button> 
                        <button type="button" onclick="confirmDelete(<?php echo $type['type_id'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>

			<form action="/admin/types/add" method="post" id="type_add_form" enctype="multipart/form-data">
            <fieldset class="admin">
                <legend>유형추가</legend>
                <b>유형추가 : </b>
				<input id="sorting" name="sorting" type="text" title="순서 입력" value="1" style="width:60px;" required="required" />
                <input id="name" name="name" type="text" title="유형명 입력" style="width:60px;" required="required" />
				<div style="display:inline-block; margin:5px 0 -10px 0;">

					<input id="img_logo" name="img_logo" type="text" title="유형 이미지" class="file_input_textbox" style="width:190px;" readonly="readonly">

					<div class="file_input_div">		
						<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
						<input id="file_logo" name="type_image" type="file" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_logo').value = this.value" />
					</div>
				</div>
                <button type="submit" title="추가" class="admin1"><span class="ir">추가</span></button>
            </fieldset>
			</form>

            <!-- 종목 수정 -->
			<form action="/admin/types/edit" method="post" enctype="multipart/form-data" id="type_edit_form" style="display:none;">
            <fieldset class="admin">
                <legend>유형수정</legend>
                <b>유형수정 : </b>
				<input type="hidden" id="edit_type_id" name="edit_type_id" value="">
				<input id="edit_sorting" name="sorting" type="text" title="순서 입력" value="1" class="ready" style="width:60px;" required="required" />    
                <input id="edit_name" name="name" type="text" title="유형명 입력" value="" class="ready" style="width:60px;" required="required" />    
                <img id="current_type_image" src="../img/ico_item01.gif" />

				<div style="display:inline-block; margin:5px 0 -10px 0;">

					<input id="img_logo1" name="img_logo" type="text" title="유형 이미지" class="file_input_textbox" style="width:190px;" readonly="readonly">

					<div class="file_input_div">		
						<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
						<input id="file_logo1" name="type_image" type="file" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_logo1').value = this.value" />
					</div>
				</div>

                <button type="submit" title="변경" class="admin1"><span class="ir">변경</span></button>
            </fieldset>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>