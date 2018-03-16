<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 전략 상세보기 - <?php echo htmlspecialchars($strategy['name']) ?></title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="/js/calendar.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
    function editData(target_date, money, investor){
    	$('#edit_target_date').val(target_date);
    	$('#edit_money').val(money);
    	$('#edit_investor').val(investor);
    	showLayer('daily_modify2');
    }

	function deleteData(target_date){
		var result = confirm('삭제하시겠습니까?');

		if(result) location.href='/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding/delete?target_date=' +target_date;
	}
    </script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">상품 상세보기</h3>

            <h4 class="admin"><?php echo htmlspecialchars($strategy['name']) ?></h4>
            <span class="write_date"><b>등록일 :</b> <?php echo date('Y.m.d H:i:s', strtotime($strategy['reg_at'])) ?></span>
            
            <!-- 일간 분석 -->
            <div id="strategy_view1" name="strategy_view" class="strategy_view">

               <div class="tab">
                    <a title="기본정보" class="<?php if($current_tab_menu == 'basic') echo 'tab_on'; else echo 'tab_off' ?>" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>"><span class="ir">기본정보</span></a>
                    <a title="일간분석" class="<?php if($current_tab_menu == 'daily') echo 'tab_on'; else echo 'tab_off' ?>" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/daily"><span class="ir">일간분석</span></a>
                    <a title="펀딩금액/투자자 수" class="<?php if($current_tab_menu == 'funding') echo 'tab_on'; else echo 'tab_off' ?> long" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding"><span class="ir">펀딩금액/투자자 수</span></a>
                    <a title="실계좌 정보" class="<?php if($current_tab_menu == 'accounts') echo 'tab_on'; else echo 'tab_last' ?>" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts"><span class="ir">실계좌 정보</span></a>
                </div>
                                
                <!-- Super Admin만 노출 -->
                <h5 class="admin">펀딩금액/투자자 수</h5>
                <button id="" type="button" onclick="showLayer('excel_upload');" title="엑셀 업로드" class="write btn_admin btn3"><span class="ir">엑셀 업로드</span></button> &nbsp;
                <button type="button" onclick="showLayer('daily_write2');" title="펀딩금액/투자자 수 입력" class="write btn_admin"><span class="ir">펀딩금액/투자자 수 입력</span></button>

				<?php
				/*
                <fieldset class="admin" style="float:right; margin-top:-8px;">
                    <span class="fieldset_txt">펀딩금액/투자자 수를</span>                     
                    <div class="select open" style="width:110px;">
                        <div class="myValue"></div>
                        <ul class="iList">
							<!--
                            <li><input name="open_funding" id="open_funding0" class="option" type="radio" /><label for="open_funding0">공개여부</label></li>
							-->
                            <li><input name="open_funding" id="open_funding1" class="option" type="radio" checked="checked" /><label for="open_funding1">공개</label></li>
                            <li><input name="open_funding" id="open_funding2" class="option" type="radio" /><label for="open_funding2">비공개</label></li>
                        </ul>
                    </div> 
                    로 &nbsp;
                    <button type="button" title="변경" class="admin1" onclick=""><span class="ir">변경</span></button>
                </fieldset>
				*/
				?>

                <p class="data_info">화폐단위 : <?php echo htmlspecialchars($strategy['currency']) ?></p><!------ 화폐단위 불러와서 표시해 줄 것 ------->
                <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
                    <thead>
                    <tr>
                        <td>날짜</td>
                        <td>펀딩금액</td>
                        <td>투자자수</td>
                        <td>관리</td>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach($daily_values as $v){ ?>
                    <tr>
                        <td class="date"><?php echo substr($v['target_date'], 0, 4).'.'.substr($v['target_date'], 4, 2).'.'.substr($v['target_date'], 6, 2) ?></td>
                        <td class="data"><?php echo number_format($v['money']) ?></td>
                        <td class="data"><?php echo number_format($v['investor']) ?></td>
                        <td>
                            <button type="button" onclick="editData('<?php echo substr($v['target_date'], 0, 4).'.'.substr($v['target_date'], 4, 2).'.'.substr($v['target_date'], 6, 2) ?>', '<?php echo number_format($v['money']) ?>', '<?php echo number_format($v['investor']) ?>')" title="수정" class="sbtn"><span class="ir">수정</span></button>                        
                            <button type="button" onclick="deleteData('<?php echo $v['target_date'] ?>')" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                        </td>
                    </tr>
					<?php } ?>
                    </tbody>
                </table>
                
                <?php if($total > 0){ ?>
				<div class="paging">
					<a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
					<?php if($page_start > $page_count){ ?><a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
					<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
					<?php if($i > ceil($total / $count)) break; ?>
					<a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
					<?php } ?>
					<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
					<a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
				</div>
				<?php } ?>
                <!-- //Super Admin만 노출 -->
            </div>

			<!--
            <p class="btn_board">
                <button id="" type="" onclick="location.href='strategy';" title="목록" class="cancel"><span class="ir">목록</span></button>
            </p>
			-->
        </div>        
    </div>
    <!------ //본문 영역 ------->  

    <!-- 펀딩금액/투자자수 입력 레이어 -->
    <div id="daily_write2" class="layer" style="width:400px;">
        <div class="layer_head">
            <p class="write2">펀딩금액/투자자수 입력</p>
            <span class="layer_close" onclick="closeLayer('daily_write2');">X</span>
        </div>

		<form action="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding/add" method="post">
        <div class="layer_form">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
                <thead>
                <tr>
                    <td>날짜</td>
                    <td>펀딩금액</td>
                    <td>투자자수</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><input name="target_date" type="text" title="날짜" class="datepicker" value="<?php echo date('Y.m.d') ?>" required="required" /></td>
                    <td><input name="money" type="text" title="펀딩금액" value="0" required="required" onkeyup="inputNumberFormat(this)" /></td>
                    <td><input name="investor" type="text" title="투자자수" value="0" required="required" onkeyup="inputNumberFormat(this)" /></td>
                </tr>
                </tbody>
            </table>

            <p class="btn_area">
                <button type="submit" title="입력" class="submit"><span class="ir">입력</span></button>
                <button type="reset" title="취소"onclick="closeLayer('daily_write2');" class="cancel"><span class="ir">취소</span></button>
            </p>
        </div>
		</form>

    </div>
    <!-- //펀딩금액/투자자수 입력 레이어 -->

    <!-- 펀딩금액/투자자수 수정 레이어 -->
    <div id="daily_modify2" class="layer" style="width:400px;">
        <div class="layer_head">
            <p class="edit2">펀딩금액/투자자수 수정</p>
            <span class="layer_close" onclick="closeLayer('daily_modify2');">X</span>
        </div>

		<form action="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding/edit" method="post">
        <div class="layer_form">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
                <thead>
                <tr>
                    <td>날짜</td>
                    <td>펀딩금액</td>
                    <td>투자자수</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><input name="target_date" id="edit_target_date" type="text" title="날짜" class="datepicker" value="" required="required" /></td>
                    <td><input name="money" id="edit_money" type="text" title="펀딩금액" value="" required="required" onkeyup="inputNumberFormat(this)" /></td>
                    <td><input name="investor" id="edit_investor" type="text" title="투자자수" value="" required="required" onkeyup="inputNumberFormat(this)" /></td>
                </tr>
                </tbody>
            </table>

            <p class="btn_area">
                <button type="submit" title="수정" class="submit"><span class="ir">수정</span></button>
                <button type="reset" title="취소"onclick="closeLayer('daily_modify2');" class="cancel"><span class="ir">취소</span></button>
            </p>
        </div>
		</form>
    </div>
    <!-- //펀딩금액/투자자수 수정 레이어 -->

    <!-- 엑셀 업로드 레이어 -->
    <div id="excel_upload" class="layer" style="width:400px;">
        <div class="layer_head">
            <p class="excel">엑셀 업로드</p>
            <span class="layer_close" onclick="closeLayer('excel_upload');">X</span>
        </div>

		<form action="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding/upload" method="post" enctype="multipart/form-data">
        <div class="layer_form">
            <div class="upload">             
				<input id="upload" name="excel_fake" type="text"  title="엑셀파일" class="file_input_textbox" style="width:255px;" readonly="readonly">

				<div class="file_input_div">		
					<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
					<input id="excel" name="excel" type="file" title="엑셀파일" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('upload').value = this.value" />
				</div>
            </div>

            <p class="btn_area">
                <button type="submit" title="업로드" class="submit"><span class="ir">업로드</span></button>
                <button type="reset" title="취소" onclick="closeLayer('excel_upload');" class="cancel"><span class="ir">취소</span></button>
            </p>
        </div>
        </form>
    </div>

	<?php require_once('footer.php') ?>

</body>
</html>