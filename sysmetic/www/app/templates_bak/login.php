<!doctype html>
<html lang="ko">
<head>
	<title>로그인 | SYSMETIC</title>
	<? require_once "common_head.php" ?>
	<script>
	$(function(){
		$('#form_login').submit(function(){
			if(!$('#email').val()){
				alert('이메일과 비밀번호는 필수 입력사항입니다.');
				$('#email').focus();
				return false;
			}

			if(!$('#password').val()){
				alert('이메일과 비밀번호는 필수 입력사항입니다.');
				$('#password').focus();
				return false;
			}

			return true;
		});

		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>

		<?php if(!empty($flash['success'])){ ?>
		alert('<?php echo htmlspecialchars($flash['success']) ?>');
		<?php } ?>
	});
	</script>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
	    <? require_once "common_header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container login">
			<div class="login_box">
				<form action="/signin" method="post" id="form_login">
					<fieldset>
						<legend class="screen_out">로그인</legend>
						<div class="top_area">
							<div class="logo"><img src="../images/common/img_footer_logo.png" alt="SYSMETIC" /></div>
							<p class="login_summary">회원님이 가입하셨던 아이디/비밀번호를 입력해주세요.</p>
							<div class="input_box">
								<input type="text" id="email" name="email"  placeholder="이메일주소를 입력해주세요." />
							</div>
							<div class="input_box">
								<input type="password" id="password" name="password" placeholder="비밀번호를 입력해주세요." />
							</div>
							<button type="submit" class="btn_login">로그인</button>
							<div class="fnc">
								<div class="remember">
									<input type="checkbox" name="remember_me" id="remember_me" />
									<label for="remember_id">아이디 기억하기</label>
								</div>
								
								<div class="find_area">
									<a href="javascript:;" class="btn_find" onclick="commonLayerOpen('find_email')">이메일찾기</a> /
									<a href="javascript:;" class="btn_find" onclick="commonLayerOpen('find_password')">비밀번호 찾기</a>
								</div>
							</div>
						</div>
						<div class="bottom_area">
							<p class="join_summary">
								아직 시스메틱 회원이 아니신가요? <a href="/join_select">회원가입</a>을 해 주세요. 
							</p>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
		<!-- //container -->

		<!-- footer -->
	    <? require_once "common_footer.php" ?>
		<!-- //footer -->

	</div>
	<!-- //wrapper -->

<!-- 레이어팝업 : 이메일 찾기 -->
<article class="layer_popup find_email">
	<div class="dim" onclick="commonLayerClose('find_email')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>이메일 찾기</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('find_email')"></button>
		</div>
		<div class="cont">
			<form action="">
				<fieldset>
					<legend class="screen_out">이메일 찾기</legend>
					<div class="form_area">
						<p class="txt_info">
							가입된 이메일을 찾기 위해 <br />휴대폰 번호 인증이 필요합니다.
						</p>
						<p class="txt_guide">회원가입 시 입력한 휴대폰 번호로 인증해 주세요</p>
						<div class="certify_area">
							<div class="input_box">
								<input type="text" id="" name="" placeholder="휴대폰 번호를 입력해주세요.">
							</div>
							<button type="button" class="btn_cetify" onclick="commonLayerClose('find_email'); commonLayerOpen('find_email02')">인증번호발송</button>
						</div>
						<button type="submit" class="btn_common_gray" onclick="commonLayerClose('find_email')">닫기</button>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 이메일 찾기 -->

<!-- 레이어팝업 : 이메일 찾기2 -->
<article class="layer_popup find_email02">
	<div class="dim" onclick="commonLayerClose('find_email02')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>이메일 찾기</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('find_email02')"></button>
		</div>
		<div class="cont">
			<form action="">
				<fieldset>
					<legend class="screen_out">인증번호 입력</legend>
					<div class="form_area">
						<p class="txt_info">
							전송된 인증번호를 입력하세요.
						</p>
						<div class="certify_area">
							<div class="input_box">
								<input type="text" id="" name="" placeholder="휴대폰 번호를 입력해주세요.">
							</div>
							<div class="btn_area">
								<button type="button" class="btn_check" onclick="commonLayerClose('find_email02'); commonLayerOpen('find_email_result')">확인</button>
								<button type="button" class="btn_resend">재전송</button>
							</div>
						</div>
						<button type="submit" class="btn_common_gray" onclick="commonLayerClose('find_email02')">닫기</button>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 이메일 찾기2 -->

<!-- 레이어팝업 : 이메일 찾기 완료 -->
<article class="layer_popup find_email_result">
	<div class="dim" onclick="commonLayerClose('find_email_result')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>이메일 찾기</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('find_email_result')"></button>
		</div>
		<div class="cont">
			<!-- 회원정보가 있을 경우 -->
			<p class="txt_info">
				해당 휴대폰 번호로 가입된 이메일은<br />
				<strong class="mark">Dhfjsdfgs**@naver.com</strong> 입니다.
			</p>
			<div class="btn_area half">
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('find_email_result')">로그인 하기</a>
				<a href="javascript:;" class="btn_common_red" onclick="commonLayerClose('find_email_result'); commonLayerOpen('find_password');">비밀번호 찾기</a>
			</div>
			<!-- //회원정보가 있을 경우 -->

			<!-- 회원정보가 없을 경우 
			<p class="txt_info">
				<strong class="mark">해당 휴대폰 번호로 가입된 이메일이 없습니다.</strong><br />
				다시한번 확인해주세요.
			</p>
			<div class="btn_area half">
				<button type="button" class="btn_common_gray" onclick="commonLayerClose('find_email_result');"
				>닫기</button>
				<a href="javascript:;" class="btn_common_red" onclick="commonLayerClose('find_email_result'); commonLayerOpen('find_email');">이메일 찾기</a>
			</div>
			<!-- //회원정보가 없을 경우 -->
		</div>
	</div>
</article>
<!-- //레이어팝업 : 이메일 찾기 완료 -->

<!-- 레이어팝업 : 비밀번호 재설정 -->
<article class="layer_popup find_password">
	<div class="dim" onclick="commonLayerClose('find_password')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>비밀번호 재설정</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('find_password')"></button>
		</div>
		<div class="cont">
			<form action="">
				<fieldset>
					<legend class="screen_out">이메일 주소 입력</legend>
					<div class="form_area">
						<p class="txt_info">
							<strong class="mark">회원으로 가입된 이메일 주소를 입력해 주세요.</strong>
						</p>
						<div class="input_box">
							<input type="text" id="" name="" placeholder="이메일 주소를 입력해주세요." />
						</div>
						<div class="btn_area half">
							<button type="button" class="btn_common_gray" onclick="commonLayerClose('find_password')">닫기</button>
							<a href="javascript:;" class="btn_common_red">비밀번호 재설정</a>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 비밀번호 재설정 -->

<!-- 레이어팝업 : 비밀번호 재설정 완료 -->
<article class="layer_popup find_password_result">
	<div class="dim" onclick="commonLayerClose('find_password_result')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>비밀번호 재설정</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('find_password_result')"></button>
		</div>
		<div class="cont">
			<!-- 회원정보 맞을 시 -->
			<p class="txt_info">
				<strong class="mark">임시 비밀번호가 발송되었습니다.</strong><br />
				메일을 확인해 주세요.
			</p>
			<!-- 회원정보 맞을 시 -->
			<!-- 회원정보 틀릴 시 
			<p class="txt_info">
				<strong class="mark">가입된 이메일이 아닙니다.</strong><br />
				이메일 주소를 확인해 주세요.
			</p>
			<!-- 회원정보 틀릴 시 -->
			<button type="button" class="btn_common_gray" onclick="commonLayerClose('find_password_result')">닫기</button>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 비밀번호 재설정 완료 -->
<script>

</script>
</body>
</html>