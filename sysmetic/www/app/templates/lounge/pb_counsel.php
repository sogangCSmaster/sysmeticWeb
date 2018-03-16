<!doctype html>
<html lang="ko">
<head>
	<title>라운지 | SYSMETIC</title>
	<? include_once $skinDir."/common/head.php" ?>
	<script src="/script/calendar.js"></script>
    <script>
    $(function() {

        $('#regFrm').on('submit', function() {
            if (!$.trim($('#subject').val())) {
                $('#subject').focus();
                alert('상담제목을 입력해주세요');
                return false;
            } else if ($('#hope_date').length && !$.trim($('#hope_date').val())) {
                $('#hope_date').focus();
                alert("상담희망시간을 선택하세요.");
                return false;
            } else if ($('#mobile').length && !$.trim($('#mobile').val())) {
                $('#mobile').focus();
                alert("연락처를 입력하세요.");
                return false;
            } else if (!$.trim($('#strategy').val())) {
                $('#strategy').focus();
                alert('관심있는 전략을 입력해주세요');
                return false;
            } else if (!$.trim($('#s_price').val())) {
                $('#s_price').focus();
                alert('투자예상금액을 입력해주세요');
                return false;
            } else if (!$.trim($('#contents').val())) {
                alert('내용을 입력해주세요');
                return false;
            } else if ($('#agree01').length && !$('#agree01').is(':checked')) {
                alert("개인정보 제3자 제공동의를 하셔야만 상담신청이 가능합니다.");
                return false;
            } else {
                if (!confirm('신청하시겠습니까?')) {
                    return false;
                }
            }
        });
    });
    
    
    </script>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once $skinDir."/common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container">
			<section class="area pb_detail">
				<div class="area">
					<div class="head">
                        <? include $skinDir."/lounge/pb_info.php" ?>
					</div>
					<div class="content counsel">	
						<div class="page_title_area no_bg">
							<p class="page_title n_squere">상담하기– <?=$req_type?> 상담</p>
							<p class="page_summary">내용을 입력해주시면 확인후 답변을 드립니다.</p>
						</div>
						<div class="form_box">
							<form id="regFrm" action="/lounge/<?=$pb['uid']?>/counsel/<?=$req_type?>" method="post">
								<fieldset>
									<legend class="screen_out">상담하기– <?=$req_type?> 상담</legend>
									<table class="form_tbl">
										<colgroup>
											<col style="width:164px">
											<col style="width:826px">
										</colgroup>
										<tbody>
											<tr>
												<th>상담제목</th>
												<td>
													<div class="input_box" style="width:795px;">
														<input type="text" id="subject" name="subject" placeholder="제목을 입력해주세요." style="width:775px;">
													</div>
												</td>
											</tr>
                                            <? if ($req_type == 'Offline') { ?>
											<tr>
												<th>상담희망시간</th>
												<td>
													<div class="wrapping">
														<div class="input_box" style="width:338px;">
															<input type="text" id="hope_date" class="datepicker" name="hope_date" placeholder="날짜를 입력해주세요" style="width:318px;" readonly>
														</div>
														<div class="custom_selectbox" style="margin-left:10px; width:108px;">
															<label for="hour">7시</label>
															<select id="hour" name="hour">
                                                                <?
                                                                for ($i = 0; $i <= 23; $i++) {
                                                                    $val = sprintf("%02d", $i);
                                                                ?>
																<option value="<?=$val?>"><?=$val?>시</option>
                                                                <? } ?>
															</select>
														</div>
														<div class="custom_selectbox" style="margin-left:10px; width:108px;">
															<label for="min">00분</label>
															<select id="min" name="min">
                                                                <? for ($i = 0; $i <= 50; $i = $i + 10) {
                                                                    $val = sprintf("%02d", $i);
                                                                ?>
																<option value="<?=$val?>"><?=$val?>분</option>
                                                                <? } ?>
															</select>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>연락처</th>
												<td>
													<div class="input_box" style="width:795px;">
														<input type="text" id="mobile" name="mobile" placeholder="연락처를 입력해주세요." style="width:775px;" value="<?=$_SESSION['user']['mobile']?>">
													</div>
												</td>
											</tr>
                                            <? } ?>
											<tr>
												<th>관심있는 전략</th>
												<td>
													<div class="input_box" style="width:795px;">
														<input type="text" id="strategy" name="strategy" placeholder="관심있는 전략을 입력해주세요." style="width:775px;">
													</div>
												</td>
											</tr>
											<tr>
												<th>투자예상금액</th>
												<td>
													<div class="input_box" style="width:795px;">
														<input type="text" id="s_price" name="s_price" placeholder="투자예상금액을 입력해주세요." style="width:775px;">
													</div>
												</td>
											</tr>
											<tr>
												<th>투자개시시점</th>
												<td>
													<div class="input_box" style="width:795px;">
														<input type="text" id="s_date" name="s_date" placeholder="투자개시시점을 입력해주세요." style="width:775px;">
													</div>
												</td>
											</tr>
											<tr class="editor">
												<th>내용</th>
												<td>
													<div class="textarea">
														<textarea id="contents" name="contents" placeholder="내용을 입력해 주세요."></textarea>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
                                    
                                    <? if ($req_type == 'Offline') { ?>
									<div class="check_area">
										<input type="checkbox" id="agree01" name="agree01">
										<label for="agree01">개인정보 제3자 제공 동의</label>
									</div>
									<div class="agree_list">
										<p>
											개인정보를 제공 받는 자 :  선택한 PB<br />
											개인정보 제공 목적 : 투자 관련 상담<br />
											제공하는 개인정보 항목 : 이름 / 연락처
										</p>
									</div>
                                    <? } ?>

									<div class="btn_area">
										<a href="/lounge/<?=$pb['uid']?>" class="btn_common_gray btn_cancel">취소</a>
										<button type="submit" class="btn_common_red btn_next_step">등록</button>
									</div>
								</fieldset>
							</form>
						</div>
					</div>
				</div>
			</section>
		</div>
		<!-- //container -->

        <!-- footer -->
		<? require_once $skinDir."/common/footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->

<script>
	//custom selectbox
    var select = $('select');
    for(var i = 0; i < select.length; i++){
        var idxData = select.eq(i).children('option:selected').text();
        select.eq(i).siblings('label').text(idxData);
    }
    select.change(function(){
        var select_name = $(this).children("option:selected").text();
        $(this).siblings("label").text(select_name);
    });
</script>
</body>
</html>
