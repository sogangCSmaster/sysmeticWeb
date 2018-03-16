<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 로그인</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
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
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
           <!-- 로그인 -->
            <div class="page">
                <div class="layer_head">
                    <p class="login">로그인</p>
                </div>
                
                <div class="login_form">
                    <p class="others">
                        <button type="button" title="네이버 아이디로 로그인" class="login_naver" onclick="location.href='/signin/naver'"><span class="ir">네이버 아이디로 로그인</span></button>
                        <button type="button" title="페이스북 아이디로 로그인" class="login_facebook" onclick="location.href='/signin/facebook'"><span class="ir">페이스북 아이디로 로그인</span></button>
                    </p>
                    
					<form action="/signin" method="post" id="form_login">
                    <fieldset class="login">
                        <legend>로그인</legend>
                        <input id="email" name="email" type="text" title="이메일" onclick="this.value='';" value="이메일" required="required" />
                        <input id="password" name="password" type="password" title="비밀번호" onclick="this.className='password';" value="" required="required" />
                        <button type="submit" title="로그인" class="submit"><span class="ir">로그인</span></button>
                        <p>
                            <input name="remember_me" id="remember_me" class="option" type="checkbox" /><label for="remember_me">로그인 유지</label>
                            
                            <span class="link">
                                <a href="/forget_password" onclick="showLayer('forget_password');return false;">비밀번호 재설정</a> <i>&nbsp;l&nbsp;</i>
                                <a href="/signup">회원가입</a>
                            </span>
                        </p>                    
                    </fieldset>
					</form>
                </div>
            </div>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>