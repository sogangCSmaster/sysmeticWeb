<!doctype html>
<html lang="ko">
<head>
	<title>고객센터 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
    $(function() {
        $('#regFrm').on('submit', function() {
            if (!$.trim($('#subject').val())) {
                alert('제목을 입력해주세요');
                $('#subject').focus();
                return false;
            } else if (!$.trim($('#email').val())) {
                alert('이메일주소를 입력해주세요');
                $('#email').focus();
                return false;
            } else if (!$.trim($('#mobile').val())) {
                alert('휴대전화번호를 입력해주세요');
                $('#mobile').focus();
                return false;
            } else if (!$.trim($('#contents').val())) {
                alert('내용을 입력해주세요');
                $('#contents').focus();
                return false;
            } else {
				if(!$("#zsfCode").val()){
					alert('스팸방지코드를 입력해 주세요.\r\n(스팸광고 방지를 위함)'); 
					$("#zsfCode").focus(); 
					return false;
				}else{
					if (!confirm('저장하시겠습니까?')) {
						return false;
					}
				}
            }
        });
    });
    </script>
	<?if($_GET['error']){?>
	<script type="text/javascript">
	<!--
		alert('보안코드가 잘못되었습니다. 다시 확인하여 주세요');
	//-->
	</script>
	<?}?>
</head>
<body>
    <!-- wrapper -->
    <div class="wrapper">
        <!-- header -->
        <? require_once $skinDir."common/header.php" ?>
        <!-- header -->
        <!-- container -->
        <div class="container">


			<section class="area cs_w">
				<div class="cont_a">
                
                    <? require_once $skinDir."cs/sub_menu.php" ?>

					<div class="content cs_form">
						<form id="regFrm" action="/cs/req" method="post">
							<fieldset>
								<legend class="screen_out">1:1 문의</legend>
								<table class="form_tbl">
									<colgroup>
										<col style="width:164px">
										<col style="width:826px">
									</colgroup>
									<tbody>
										<tr>
											<th>제목</th>
											<td>
												<div class="input_box" style="width:795px;">
													<input type="text" id="subject" name="subject" placeholder="제목을 입력해주세요." style="width:775px;">
												</div>
											</td>
										</tr>
										<tr>
											<th>분류</th>
											<td>
												<div class="choice_area">
													<input type="radio" id="contact_type01" name="target" value="1" checked="checked">
													<label for="contact_type01">사이트 이용 문의</label>
													<input type="radio" id="contact_type02" name="target" value="2">
													<label for="contact_type02">제휴 문의</label>
													<input type="radio" id="contact_type03" name="target" value="3">
													<label for="contact_type03">기타 문의</label>
												</div>
											</td>
										</tr>
										<tr>
											<th>이메일주소</th>
											<td>
												<div class="input_box" style="width:795px;">
													<input type="text" id="email" name="email" placeholder="이메일주소를 입력해주세요." style="width:775px;" value="<?=$_SESSION['user']['email']?>">
												</div>
											</td>
										</tr>
										<tr>
											<th>휴대전화번호</th>
											<td>
												<div class="row">
													<div class="input_box" style="width:264px;">
														<input type="text" id="mobile" name="mobile" placeholder="휴대전화번호를 입력해주세요." value="<?=$_SESSION['user']['mobile']?>" style="width:244px;">
													</div>
													<p class="txt">답변 등록시 SMS가 발송됩니다.</p>
												</div>
											</td>
										</tr>
										<tr class="editor">
											<th>내용</th>
											<td>
												<div class="textarea">
													<textarea id="contents" name="contents" placeholder="상품 문의는 해당 상품 내의 문의하기 또는 PB 상담기능을 이용해주세요."></textarea>
												</div>
											</td>
										</tr>
										<tr>
											<th>보안문자</th>
											<td>
												<div class="row">
													<table>
													<tr>
														<td><img id="zsfImg" src="/zmSpamFree/zmSpamFree.php?zsfimg" alt="여기를 클릭해 주세요." onclick="this.src='/zmSpamFree/zmSpamFree.php?re&amp;zsfimg='+new Date().getTime()" align="absmiddle" /></td>
														<td><input type="text" name="zsfCode" id="zsfCode" value="" style="width:70px;height:20px;" /> 좌측 숫자를 입력하세요	</td>
													</tr>
													</table>
													
												</div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="btn_wrap">
									<a href="/cs/notice" class="btn_common_gray btn_cancel">취소</a>
									<button type="submit" class="btn_common_red">등록</button>
								</div>
							</fieldset>
						</form>
						
					</div>


				</div>
			</section>

        </div>
        <!-- //container -->

        <!-- footer -->
        <? require_once $skinDir."common/footer.php" ?>
        <!-- //footer -->
    </div>
    <!-- //wrapper -->

</body>

<script>

</script>
</html>
