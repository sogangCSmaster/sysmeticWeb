<!doctype html>
<html lang="ko">
<head>
	<title>회원가입 | SYSMETIC</title>
	<? require_once "common_head.php" ?>
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
										<tr class="photo">
											<th>프로필이미지</th>
											<td>
												<div class="wrapping photo">
													<div class="photo_box"></div>
													<input type="file" id="photo_upload" name="photo_upload" />
													<label for="photo_upload" class="btn_default">사진첨부</label>
													<button type="button" class="btn_default">삭제하기</button>
												</div>
											</td>
										</tr>
										<tr>
											<th>닉네임</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="margin-right:10px; width:298px;">
														<input type="text" id="" name="" placeholder="닉네임을 입력해주세요." />
													</div>
													<p class="txt_summary">* 사이트 이용 시 사용할 이름을 입력해 주세요.</p>
												</div>
											</td>
										</tr>
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
							
							<div class="group">
								<div class="check_area">
									<input type="checkbox" id="check01" name="check01">
									<label for="check01">트레이더회원 이용약관에 동의합니다.</label>
								</div>
								<div class="cont_area">
									<p class="cont">
										제 1 조 (목적)<br />
										이 약관은 시스메틱 트레이더 서비스(이하 "서비스"라 합니다)와 관련하여, 서비스와 이용고객(또는 회원)간에 서비스의 이용조건 및 절차, 서비스와 회원간의 권리, 의무 및 기타 필요한 사항을 규정함을 목적으로 합니다.<br /><br />

										제 2 조 (용어의 정의)<br />
										1) "회원" 이라 함은 서비스에 개인정보를 제공하여 회원등록을 한 자로서, 사이트에 전자우편주소를 등록하고 사이트의 정보를 지속적으로 제공받으며, 사이트가 제공하는 서비스를 계속적으로 이용할 수 있는 자를 말합니다.<br />
										2) "비회원" 이라 함은 회원으로 가입하지 않고 제공하는 서비스를 이용하는 자를 말합니다.<br />
										3) "비밀번호(Password)"라 함은 회원의 동일성 확인과 회원의 권익 및 비밀보호를 위하여 회원 스스로가 설정하여 사이트에 등록한	영문과 숫자의 조합을 말합니다.<br /><br />
									</p>
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