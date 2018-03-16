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
		$('#item_add_form').submit(function(){
			if(!$('#name').val()){
				alert('종목을 입력하세요');
				$('#name').focus();
				return false;
			}

			return true;
		});

		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>
	});

	function confirmDelete(item_id){
		var result = confirm('삭제하시겠습니까?');
		if(result){
			location.href='/admin/items/'+item_id+'/delete';
		}
	}

	function openEdit(item_id, name, image_url, sorting){
		$('#item_edit_form').show();
		$('#edit_item_id').val(item_id);
		$('#edit_name').val(name);
		$('#edit_sorting').val(sorting);
		if(image_url){
			$('#current_item_image').show();
			$('#current_item_image').attr('src', image_url);
		}else{
			$('#current_item_image').hide();
			// $('#current_item_image').attr('src', image_url);
		}
	}
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">종목관리</h3>

            <div class="strategy_view">
                <div class="tab">
                    <a href="/admin/items" title="종목관리" class="tab_on"><span class="ir">종목관리</span></a>
                    <a href="/admin/kinds" title="종류관리" class="tab_off"><span class="ir">종류관리</span></a>
                    <a href="/admin/types" title="유형관리" class="tab_off"><span class="ir">유형관리</span></a>
                </div>
            </div>

            <table border="0" cellspacing="1" cellpadding="0" class="admin_table" style="width:500px;">
            <col width="70" /><col width="*" /><col width="150" /><col width="150" />
                <thead>
                <tr>
                    <td>순서</td>
                    <td>종목명</td>
                    <td>아이콘</td>
                    <td>관리</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($items as $item){ ?>
                <tr>
                    <td><?php echo $item['sorting'] ?></td>
                    <td><?php echo htmlspecialchars($item['name']) ?></td>
                    <td><?php if(!empty($item['icon'])){ ?><img src="<?php echo htmlspecialchars($item['icon']) ?>" /><?php } ?></td>
					<!-- <td><?php echo $item['sorting'] ?></td> -->
                    <td>
                        <button type="button" onclick="openEdit(<?php echo $item['item_id'] ?>, '<?php echo htmlspecialchars($item['name']) ?>', '<?php if(!empty($item['icon'])) echo htmlspecialchars($item['icon']) ?>', <?php echo $item['sorting'] ?>)" title="수정" class="sbtn"><span class="ir">수정</span></button> 
                        <button type="button" onclick="confirmDelete(<?php echo $item['item_id'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>

			<form action="/admin/items/add" method="post" id="item_add_form" enctype="multipart/form-data">
            <fieldset class="admin">
                <legend>종목추가</legend>
                <b>종목추가 : </b>
				<input id="sorting" name="sorting" type="text" title="순서 입력" value="1" style="width:60px;" required="required" />                
                <input id="name" name="name" type="text" title="종목명 입력" style="width:60px;" required="required" />                
                <!--
				<input id="img_logo" name="img_logo" type="text" title="종목 이미지" value="" style="width:190px;" onclick="document.getElementById('file_logo').click();" readonly="readonly"  />
                
				<button type="button" title="찾아보기" class="act" onclick="document.getElementById('file_logo').click();" value="찾아보기"><span class="ir">찾아보기</span></button>

                <input id="file_logo" name="item_image" type="file" title="종목이미지" value="" style="display:none;" onchange=" document.getElementById('img_logo').value = this.value"  />
				-->
				<div style="display:inline-block; margin:5px 0 -10px 0;">

					<input id="img_logo" name="img_logo" type="text" title="종목 이미지" class="file_input_textbox" style="width:190px;" readonly="readonly">

					<div class="file_input_div">		
						<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
						<input id="file_logo" name="item_image" type="file" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_logo').value = this.value" />
					</div>
				</div>
                <button type="submit" title="추가" class="admin1"><span class="ir">추가</span></button>
            </fieldset>
			</form>

            <!-- 종목 수정 -->
			<form action="/admin/items/edit" method="post" enctype="multipart/form-data" id="item_edit_form" style="display:none;">
            <fieldset class="admin">
                <legend>종목수정</legend>
                <b>종목수정 : </b>
				<input type="hidden" id="edit_item_id" name="edit_item_id" value="">
				<input id="edit_sorting" name="sorting" type="text" title="순서 입력" value="1" class="ready" style="width:60px;" required="required" />    
                <input id="edit_name" name="name" type="text" title="종목명 입력" value="선물" class="ready" style="width:60px;" required="required" />    
                <img id="current_item_image" src="../img/ico_item01.gif" />            

				<div style="display:inline-block; margin:5px 0 -10px 0;">

					<input id="img_logo1" name="img_logo" type="text" title="종목 이미지" class="file_input_textbox" style="width:190px;" readonly="readonly">

					<div class="file_input_div">		
						<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
						<input id="file_logo1" name="item_image" type="file" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_logo1').value = this.value" />
					</div>
				</div>

				<!--
                <input id="img_logo1" name="img_logo" type="text" title="종목 이미지" value="" style="width:190px;" onclick="document.getElementById('file_logo1').click();" readonly="readonly"  />
                <button type="button" title="찾아보기" class="act" onclick="document.getElementById('file_logo1').click();" value="찾아보기"><span class="ir">찾아보기</span></button>

                <input id="file_logo1" name="item_image" type="file" title="종목이미지" value="" style="display:none;" onchange=" document.getElementById('img_logo1').value = this.value"  />

				-->




                <button type="submit" title="변경" class="admin1"><span class="ir">변경</span></button>
            </fieldset>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>