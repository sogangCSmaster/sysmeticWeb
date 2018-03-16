
    <div id="footer">사업자 등록번호 : 711-86-00050 / 통신판매업신고 : 제2017-서울강남-03204호 / 특허출원번호 : 10-2016-00262203<br />
상호명 : (주)시스메틱 /
주소지 : 서울시 강남구 테헤란로 419, 15층 1524호 (삼성동, 파이낸스 플라자) / tel : 02-6338-1880 / 메일주소 : <a href="mailto:ceo@sysmetic.co.kr">ceo@sysmetic.co.kr</a> <br /><br />

<a href="http://sysmetic.co.kr/privacy">개인정보취급방침</a> | COPYRIGHT ⓒ <b>SYSMETIC TRADERS</b> ALL RIGHT RESERVED.<br /><br />

시스메틱의 모든 사이트의 내용은 정보를 제공하기 위함이며, 투자권유와 주식 및 파생상품 매매를 목적으로 하고 있지 않습니다. <br />따라서 본 사이트의 수익률과 관련정보에 대해서는 (주)시스메틱은 어떠한 책임도 없습니다.<br />
또한 본 사이트를 통해 제공받게 되는 운용성과의 결과에 대해서도 (주)시스메틱은 어떠한 책임이 없으며 모든 책임과 활용되는 모든 정보는 투자자 본인의 책임입니다.</div>

<div id="mask" style="display:none;"></div>

<?php if(!$isLoggedIn()){ ?>
<div id="login_layer" class="layer" style="top:20px; left:20px; display:<?php if(isset($show_signin) && $show_signin) echo 'block'; else echo 'none'; ?>;">
    <div class="layer_head">
        <p class="login">로그인</p>
        <span class="layer_close" onclick="closeLayer('login_layer');">X</span>
    </div>
    
    <div class="login_form">
        <p class="others">
            <button type="button" title="네이버 아이디로 로그인" class="login_naver" onclick="location.href='/signin/naver'"><span class="ir">네이버 아이디로 로그인</span></button>
            <button type="button" title="페이스북 아이디로 로그인" class="login_facebook" onclick="location.href='/signin/facebook'"><span class="ir">페이스북 아이디로 로그인</span></button>
        </p>
        
		<form action="/signin" method="post" id="popup_form_signin">
        <fieldset class="login">
            <legend>로그인</legend>
            <input id="login_email" name="email" type="text" title="이메일" onclick="this.value='';" value="이메일" required="required" />
            <input id="login_password" name="password" type="password" title="비밀번호" onclick="this.className='password';" value="" required="required" />
            <button type="submit" title="로그인" class="submit"><span class="ir">로그인</span></button>
            <p>
                <input name="remember_me" id="login_remember_me" class="option" type="checkbox" /><label for="login_remember_me">로그인 유지</label>
                
                <span class="link">
                    <a href="/forget_password" onclick="closeLayer('login_layer');showLayer('forget_password');return false;">비밀번호 재설정</a> <i>&nbsp;l&nbsp;</i>
                    <a href="/signup">회원가입</a>
                </span>
            </p>                    
        </fieldset>
		</form>
    </div>
</div>

<div id="wrong_id" class="layer" style="top:440px; left:20px; display:none;">
    <div class="layer_head">
        <p class="info">안내</p>
        <span class="layer_close" onclick="closeLayer('wrong_id');">X</span>
    </div>
    
    <div class="infomation">    
        <p class="msg line">
            <b>등록된 회원이 아닙니다.</b><br />
            회원가입 하시겠습니까?
        </p>
        <p class="btn_layer">
            <button type="button" title="회원가입" class="submit" onclick="location.href='/signup'"><span class="ir">회원가입</span></button>
            <button type="button" title="닫기" class="cancel" onclick="closeLayer('wrong_id');"><span class="ir">닫기</span></button>
        </p>
    </div>
</div>

<div id="wrong_password" class="layer" style="top:440px; left:380px; display:none;">
    <div class="layer_head">
        <p class="info">안내</p>
        <span class="layer_close" onclick="closeLayer('wrong_password');">X</span>
    </div>
    
    <div class="infomation">    
        <p class="msg">
            <b>비밀번호가 틀렸습니다.</b><br />
            비밀번호 찾기를 하시겠습니까? 
        </p>
        <p class="btn_layer">
            <button type="button" title="비밀번호 찾기" class="submit" onclick="closeLayer('wrong_password');showLayer('forget_password');"><span class="ir">비밀번호 찾기</span></button>
            <button type="button" title="닫기" class="cancel" onclick="closeLayer('wrong_password');"><span class="ir">닫기</span></button>
        </p>
    </div>
</div>

<script>
$(function(){
	$('#popup_form_signin').submit(function(){
		if(!$('#login_email').val()){
			$('#login_email').focus();
			alert('이메일을 입력하세요');
			return false;
		}

		if(!$('#login_password').val()){
			$('#login_password').focus();
			alert('비밀번호를 입력하세요');
			return false;
		}

		$.post('/signin/json', $('#popup_form_signin').serialize(), function(data){
			if(data.result){
				location.href = '/';
			}else{
				if(data.error_type == 'wrong_id') showLayer('wrong_id');
				else showLayer('wrong_password');
			}
		}, 'json');

		return false;
	});
});
</script>

<div id="forget_password" class="layer" style="top:440px; left:740px; display:<?php if(isset($show_forget_password) && $show_forget_password) echo 'block'; else echo 'none'; ?>;">
    <div class="layer_head">
        <p class="password">비밀번호 재설정</p>
        <span class="layer_close" onclick="closeLayer('forget_password');">X</span>
    </div>
    
    <div class="infomation"> 
		<form action="/forget_password" method="post" id="forget_password_form">
        <p class="msg insert">
            <b>회원 가입한 이메일 주소를 입력해 주세요.</b><br />
            <input id="forget_email" name="email" type="text" title="이메일" required="required" />
        </p>
        <p class="btn_layer">
            <button type="submit" title="비밀번호 재설정" class="submit"><span class="ir">비밀번호 재설정</span></button>
            <button type="button" title="닫기" class="cancel" onclick="closeLayer('forget_password');"><span class="ir">닫기</span></button>
        </p>
		</form>
    </div>
</div>

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
				showLayer('reset_password');
			}else{
				showLayer('wrong_mail');
			}
		}, 'json');
		return false;
	});
});
</script>

<div id="reset_password" class="layer" style="top:440px; left:740px; display:none;">
    <div class="layer_head">
        <p class="info">안내</p>
        <span class="layer_close" onclick="closeLayer('reset_password');">X</span>
    </div>
    
    <div class="infomation">    
        <p class="msg">
            <b>비밀번호 재설정 링크가 발급되었습니다. </b><br />
            메일을 확인해 주세요.
        </p>
        <p class="btn_layer">
            <button type="button" title="닫기" class="cancel" onclick="closeLayer('reset_password');closeLayer('forget_password');"><span class="ir">닫기</span></button>
        </p>
    </div>
</div>

<!-- 비밀번호찾기 입력한 이메일이 회원이 아닐 경우 -->
<div id="wrong_mail" class="layer" style="top:440px; left:740px; display:none;">
    <div class="layer_head">
        <p class="info">안내</p>
        <span class="layer_close" onclick="closeLayer('wrong_mail');">X</span>
    </div>
    
    <div class="infomation">    
        <p class="msg line">
            <b>가입된 이메일이 없습니다.</b><br />
            이메일 주소를 확인해 주세요.
        </p>
        <p class="btn_layer">
            <button type="button" title="닫기" class="cancel" onclick="closeLayer('wrong_mail');"><span class="ir">닫기</span></button>
        </p>
    </div>
</div>

<?php } ?>

<?php
if(!$isLoggedIn()){
	if(isset($show_signin) && $show_signin){
?>
	<script>showLayer('login_layer');</script>
<?php
	} else if(isset($show_forget_password) && $show_forget_password){
?>

	<script>showLayer('forget_password');</script>
<?php
	}
}
?>
