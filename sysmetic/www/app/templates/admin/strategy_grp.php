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
		$('#grp_add_form').submit(function(){
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

	function confirmDelete(grp_id){
		var result = confirm('삭제하시겠습니까?');
		if(result){
			location.href='/admin/strategies_grp/'+grp_id+'/delete';
		}
	}

	function openEdit(grp_id, name){
		$('#grp_edit_form').show();
		$('#edit_grp_id').val(grp_id);
		$('#edit_name').val(name);
	}
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">상품 관리</h3>

            <div class="strategy_view">
                <div class="tab">
                    <a href="/admin/strategies" title="종목관리" class="tab_off"><span class="ir">상품</span></a>
                    <a href="/admin/portfolios" title="종류관리" class="tab_off"><span class="ir">포트폴리오</span></a>
                    <a href="/admin/strategies_op" title="종목관리" class="tab_off" style='width:160px'><span style='width:160px'>상품승인요청 <strong style='color:#ff0000'>(<?=number_format($op_cnt)?>)</strong></span></a>
                </div>
            </div>


            <table border="0" cellspacing="1" cellpadding="0" class="admin_table" style="width:500px;">
            <col width="70" /><col width="*" /><col width="150" />
                <thead>
                <tr>
                    <td>NO</td>
                    <td>이름</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?
                $idx = 0;
                foreach($grp as $type){
                    $idx++;
                ?>
                <tr>
                    <td><?php echo $idx ?></td>
                    <td><?php echo htmlspecialchars($type['name']) ?></td>
                    <td>
                        <button type="button" onclick="openEdit(<?php echo $type['grp_id'] ?>, '<?php echo htmlspecialchars($type['name']) ?>')" title="수정" class="sbtn"><span class="ir">수정</span></button> 
                        <button type="button" onclick="confirmDelete(<?php echo $type['grp_id'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>

			<form action="/admin/strategies_grp/add" method="post" id="grp_add_form" enctype="multipart/form-data">
            <fieldset class="admin">
                <legend>그룹추가</legend>
                <b>그룹추가 : </b>
                <input id="name" name="name" type="text" title="분류명 입력" style="width:160px;" required="required" />
                <button type="submit" title="추가" class="admin1"><span class="ir">추가</span></button>
            </fieldset>
			</form>

            <!-- 종목 수정 -->
			<form action="/admin/strategies_grp/edit" method="post" enctype="multipart/form-data" id="grp_edit_form" style="display:none;">
            <fieldset class="admin">
                <legend>그룹수정</legend>
                <b>그룹수정 : </b>
				<input type="hidden" id="edit_grp_id" name="edit_grp_id" value="">
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