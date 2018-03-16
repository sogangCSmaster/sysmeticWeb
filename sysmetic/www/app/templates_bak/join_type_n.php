<!doctype html>
<html lang="ko">
<head>
	<title>회원가입 | SYSMETIC</title>
	<? require_once "common_head.php" ?>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once "common_header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container join">
			<section class="area">
				<div class="page_title_area">
					<h2 class="page_title n_squere">회원가입</h2>
					<p class="page_summary">시스메틱 홈페이지를 찾아주셔서 감사합니다. 약관에 동의하셔야 가입이 가능합니다.</p>
				</div>
				<div class="content_area step03">
					<form action="">
						<fieldset>
							<legend class="screen_out">정보 입력</legend>
							<div class="step_view">03.정보 입력</div>
							<div class="group">
								<h3>필수정보입력</h3>
								<table class="form_tbl">
									<colgroup>
										<col style="width:164px" />
										<col style="width:826px" />
									</colgroup>
									<tbody>
										<tr>
											<th>이름</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="width:298px;">
														<input type="text" id="" name="" placeholder="이름을 입력해주세요." />
													</div>
													<button type="button" class="btn_default">실명인증</button>
												</div>
											</td>
										</tr>
										<tr class="high">
											<th>이메일</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="width:208px;">
														<input type="text" id="" name="" />
													</div>
													<span class="inner_txt">@</span>
													<div class="input_box" style="width:208px;">
														<input type="text" id="" name="" />
													</div>
													<div class="custom_selectbox" style="margin-left:10px; width:208px;">
														<label for="email_choice">이메일을 선택해주세요.</label>
														<select id="email_choice" name="roomName">
															<option value="" selected="selected">이메일을 선택해주세요.</option>
															<option value="">naver.com</option>
															<option value="">lycos.com</option>
														</select>
													</div>
													<button type="button" class="btn_default" onclick="commonLayerOpen('overlap_email')">중복확인</button>
												</div>
												<p class="txt_summary">* 사이트 이용 시 아이디로 사용됩니다.</p>
											</td>
										</tr>
										<tr>
											<th>비밀번호</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="margin-right:24px; width:298px;">
														<input type="password" id="" name="" placeholder="비밀번호를 입력해주세요." />
													</div>
													<p class="txt_summary">* 비밀번호는 문자, 숫자포함 6~20자로 되어야 합니다.</p>
												</div>
											</td>
										</tr>
										<tr>
											<th>비밀번호 확인</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="width:298px;">
														<input type="password" id="" name="" placeholder="비밀번호를 다시 입력해주세요." />
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>휴대폰번호</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="margin-right:24px; width:298px;">
														<input type="text" id="" name="" placeholder="휴대폰번호를 입력해주세요." />
													</div>
													<p class="txt_summary">* - 없이 숫자만 입력해 주세요.</p>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							
							<div class="group">
								<h3>추가정보</h3>
								<table class="form_tbl">
									<colgroup>
										<col style="width:164px" />
										<col style="width:826px" />
									</colgroup>
									<tbody>
										<tr>
											<th>생년월일</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="margin-right:10px; width:298px;">
														<input type="text" id="" name="" placeholder="생년월일을 다시 입력해주세요." />
													</div>
													<p class="txt_summary">* 숫자만 입력해 주세요. ex)19801022</p>
												</div>
											</td>
										</tr>
										<tr>
											<th>성별</th>
											<td>
												<div class="choice_area">
													<input type="radio" id="male" name="gender" checked="checked" />
													<label for="male">남성</label>
													<input type="radio" id="female" name="gender" />
													<label for="female">여성</label>
												</div>
											</td>
										</tr>
										<tr>
											<th>지역</th>
											<td>
												<div class="wrapping">
													<div class="custom_selectbox" style="margin-right:10px; width:188px;">
														<label for="area_choice">지역 선택</label>
														<select id="area_choice" name="roomName">
															<option value="" selected="selected">지역 선택</option>
															<option value="">서울</option>
															<option value="">경기</option>
														</select>
													</div>
													<div class="input_box" style="width:188px;">
														<input type="text" id="" name="" placeholder="시/구/군 입력" />
													</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="group">
								<h3>정보수신동의</h3>
								<div class="agree_box">
									<div class="agree01">
										<input type="checkbox" id="agree01" name="agree01" />
										<label for="agree01">관심 전략과 포트폴리오 관련 정보를 수신 동의합니다.</label>
									</div>
									<div class="agree02">
										<input type="checkbox" id="agree02" name="agree02" />
										<label for="agree02">전략 및 정보성 알림에 수신 동의합니다.</label>
									</div>
								</div>
							</div>

							<div class="btn_area">
								<a href="javascript:;" class="btn_common_gray btn_cancel">취소</a>
								<button type="button" class="btn_common_red btn_next_step">가입완료</button>
							</div>
						</fieldset>
					</form>					
				</div>
			</section>
		</div>
		<!-- //container -->

        <!-- footer -->
		<? require_once "common_footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->

<!-- 레이어팝업 : 이메일 중복 -->
<article class="layer_popup overlap_email">
	<div class="dim" onclick="commonLayerClose('overlap_email')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('overlap_email')"></button>
		</div>
		<div class="cont">
			<!-- 회원정보 맞을 시 -->
			<p class="txt_info">
				<strong class="mark">이미 가입된 이메일 주소 입니다.</strong><br />
				다른 이메일 주소로 가입해 주세요.
			</p>
			<!-- 회원정보 맞을 시 -->
			<button type="button" class="btn_common_gray" onclick="commonLayerClose('overlap_email')">확인</button>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 이메일 중복 -->

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