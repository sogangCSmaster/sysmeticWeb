<!doctype html>
<html lang="ko">
<head>
	<title>로그인 | SYSMETIC</title>
	<? require_once "common/head.php" ?>
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
	    <? require_once "common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container login">
			<div class="login_box">
				<form action="/signin" method="post" id="form_login">
				<input type="hidden" name="redirect_url" value="<?=$redirect_url?>" />
					<fieldset>
						<legend class="screen_out">로그인</legend>
						<div class="top_area">
							<div class="logo"><img src="../images/common/img_footer_logo.png" alt="SYSMETIC" /></div>
							<p class="login_summary">회원님이 가입하셨던 아이디/비밀번호를 입력해주세요.</p>
							<div class="input_box">
								<input type="text" id="email" name="email" value="<?=$remember_id?>" placeholder="이메일주소를 입력해주세요." />
							</div>
							<div class="input_box">
								<input type="password" id="password" name="password" placeholder="비밀번호를 입력해주세요." />
							</div>
							<button type="submit" class="btn_login">로그인</button>
							<div class="fnc">
								<div class="remember">
									<input type="checkbox" id="remember_id" name="remember_id" value='1' <?=($remember_id) ? 'checked' : ''?> />
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
	    <? require_once "common/footer.php" ?>
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
			<form name="searchEmailAuth" id="searchEmailAuth" action="/member/search_email_auth">
				<fieldset>
					<legend class="screen_out">이메일 찾기</legend>
					<div class="form_area">
						<p class="txt_info">
							가입된 이메일을 찾기 위해 <br />휴대폰 번호 인증이 필요합니다.
						</p>
						<p class="txt_guide">회원가입 시 입력한 휴대폰 번호로 인증해 주세요</p>
						<div class="certify_area">
							<div class="input_box">
								<input type="text" id="mobile" name="mobile" onkeyup="inputOnlyNumber(this);" required="required" placeholder="휴대폰 번호를 입력해주세요.">
							</div>
							<button type="submit" class="btn_cetify">인증번호발송</button>
						</div>
						<button type="button" class="btn_common_gray" onclick="commonLayerClose('find_email')">닫기</button>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</article>

<script>
$(function(){
	$('#searchEmailAuth').submit(function(){
		if(!$('#mobile').val()){
			alert('휴대폰 번호를 입력해주세요.');
			$('#mobile').focus();
			return false;
		}

		$.post($('#searchEmailAuth').attr('action'), {type:'json', mobile:$('#mobile').val()}, function(data){
			if (!data.result) {
				alert('해당 휴대폰 번호로 가입된 이메일이 없습니다.');
			} else {
	            commonLayerClose('find_email');
	            commonLayerOpen('find_email02');
			}
		}, 'json');
		$('#mobile').val('');

		return false;
	});
});
</script>
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
			<form id="checkEmailAuth" action="/member/check_email_auth">
				<fieldset>
					<legend class="screen_out">인증번호 입력</legend>
					<div class="form_area">
						<p class="txt_info">
							전송된 인증번호를 입력하세요.
						</p>
						<div class="certify_area">
							<div class="input_box">
								<input type="text" id="authNum" name="authNum" required="required" maxlength="6" placeholder="인증번호를 입력해주세요." >
							</div>
							<div class="btn_area">
								<button type="submit" class="btn_check">확인</button>
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
<script>

$(function(){
	$('#checkEmailAuth').submit(function(){
		if(!$('#authNum').val()){
			alert('인증번호를 입력해주세요.');
			$('#authNum').focus();
			return false;
		}

		$.post($('#checkEmailAuth').attr('action'), {type:'json', authNum:$('#authNum').val()}, function(data){
			if (!data.result) {
				alert('인증번호가 일치하지 않습니다.');
	            //commonLayerClose('find_email02');
	            //commonLayerOpen('find_email_false');
			} else {
				$('.find_email_success .mark').text(data.result);
	            commonLayerClose('find_email02');
	            commonLayerOpen('find_email_success');
			}

			$('#authNum').val('');
		}, 'json');

		return false;
	});

	$('#checkEmailAuth .btn_resend').click(function() {
		$('#authNum').val('');
		$.post('/member/search_email_reauth', function(data) {
			if (!data.result) {
				alert('인증번호 발송 중 오류가 발생하였습니다.');
			} else {
				alert('인증번호가 재발송 되었습니다');
			}
		}, 'json');
	});
});
</script>
<!-- //레이어팝업 : 이메일 찾기2 -->

<!-- 레이어팝업 : 이메일 찾기 완료 -->
<article class="layer_popup find_email_result find_email_success">
	<div class="dim" onclick="commonLayerClose('find_email_success')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>이메일 찾기</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('find_email_success')"></button>
		</div>
		<div class="cont">
			<!-- 회원정보가 있을 경우 -->
			<p class="txt_info">
				해당 휴대폰 번호로 가입된 이메일은<br />
				<strong class="mark"></strong> 입니다.
			</p>
			<div class="btn_area half">
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('find_email_success')">로그인 하기</a>
				<a href="javascript:;" class="btn_common_red" onclick="commonLayerClose('find_email_success'); commonLayerOpen('find_password');">비밀번호 찾기</a>
			</div>
		</div>
	</div>
</article>

<article class="layer_popup find_email_result find_email_false">
	<div class="dim" onclick="commonLayerClose('find_email_false')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>이메일 찾기</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('find_email_false')"></button>
		</div>
		<div class="cont">

			<p class="txt_info">
				<strong class="mark">해당 휴대폰 번호로 가입된 이메일이 없습니다.</strong><br />
				다시한번 확인해주세요.
			</p>
			<div class="btn_area half">
				<button type="button" class="btn_common_gray" onclick="commonLayerClose('find_email_false');"
				>닫기</button>
				<a href="javascript:;" class="btn_common_red" onclick="commonLayerClose('find_email_false'); commonLayerOpen('find_email');">이메일 찾기</a>
			</div>
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
		    <form action="/forget_password" method="post" id="forget_password_form">
				<fieldset>
					<legend class="screen_out">이메일 주소 입력</legend>
					<div class="form_area">
						<p class="txt_info">
							<strong class="mark">회원으로 가입된 이메일 주소를 입력해 주세요.</strong>
						</p>
						<div class="input_box">
							<input type="email" id="forget_email" name="email" placeholder="이메일 주소를 입력해주세요." required="required" />
						</div>
						<div class="btn_area half">
							<button type="button" class="btn_common_gray" onclick="commonLayerClose('find_password')">닫기</button>
							<button type="submit" class="btn_common_red">비밀번호 재설정</button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 비밀번호 재설정 -->

<script>
$(function(){
	$('#forget_password_form').submit(function(){
		if(!$('#forget_email').val()){
			alert('이메일 주소를 입력해주세요.');
			$('#forget_email').focus();
			return false;
		}

		$.post($('#forget_password_form').attr('action'), {type:'json', email:$('#forget_email').val()}, function(data){
			if(data.result){
                commonLayerClose('find_password');
				html = '<strong class="mark">임시 비밀번호가 발송되었습니다.</strong><br />메일을 확인해 주세요.';
			}else{
                html = '<strong class="mark">가입된 이메일이 아닙니다.</strong><br />이메일 주소를 확인해 주세요.';
			}
            $('.find_password_result .txt_info').html(html);
            commonLayerOpen('find_password_result');
		}, 'json');

		$('#forget_email').val('');
		return false;
	});
});
</script>

<!-- 레이어팝업 : 비밀번호 재설정 완료 -->
<article class="layer_popup find_password_result">
	<div class="dim" onclick="commonLayerClose('find_password_result')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>비밀번호 재설정</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('find_password_result')"></button>
		</div>
		<div class="cont">
			<p class="txt_info">Loading..</p>
			<button type="button" class="btn_common_gray" onclick="commonLayerClose('find_password_result')">닫기</button>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 비밀번호 재설정 완료 -->

</body>
</html>
