<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 회원가입</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#btn_user_id_check').on('click', function(){
			if($('#email1').val() && $('#email2').val()){
				$.post('/email_check', {email:$('#email1').val()+'@'+$('#email2').val()}, function(data){
					if(data.result){
						alert('이미 가입된 이메일 입니다. 다른 이메일로 가입해주세요.');
						$('#email1').focus();
					}else{
						alert('사용 가능한 이메일 입니다');
					}
				}, 'json');
			}else{
				alert('이메일과 비밀번호는 필수 입력사항입니다.');
				$('#email1').focus();
			}
		});

		$('#form_signup').submit(function(){
			if(!$('#email1').val()){
				alert('이메일과 비밀번호는 필수 입력사항입니다.');
				$('#email1').focus();
				return false;
			}

			if(!$('#email2').val()){
				alert('이메일과 비밀번호는 필수 입력사항입니다.');
				$('#email2').focus();
				return false;
			}

			if(!$('#password').val()){
				alert('이메일과 비밀번호는 필수 입력사항입니다.');
				$('#password').focus();
				return false;
			}

			if(!$('#password_confirm').val()){
				alert('이메일과 비밀번호는 필수 입력사항입니다.');
				$('#password_confirm').focus();
				return false;
			}

			if($('#password').val() != $('#password_confirm').val()){
				alert('비밀번호가 일치하지 않습니다.');
				$('#password').focus();
				return false;
			}

			if($('#password').val().length < 6 || $('#password').val().length >= 20){
				alert('비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
				$('#password').focus();
				return false;
			}

			if(!/^(?=.*\d)(?=.*[a-zA-Z]).{6,19}$/.test($('#password').val())){
				alert('비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
				$('#password').focus();
				return false;
			}

			if(!$('#nickname').val()){
				alert('닉네임은 필수 입력사항입니다.');
				$('#nickname').focus();
				return false;
			}

			if($('#mobile').val() && !/^[0-9]{10,11}$/.test($('#mobile').val())){
				alert('정확한 휴대폰 번호를 확인해 주세요.');
				$('#mobile').focus();
				return false;
			}

			if($('#mobile').val() && !/^[0-9]{8}$/.test($('#birthday').val())){
				alert('생년월일이 올바르지 않습니다.');
				$('#birthday').focus();
				return false;
			}
			
			return true;
		});

		$('.mail .iList input[type=radio]').on('click', function(){
			if($(this).attr('id') != 'mail0'){
				$('#email2').val($(this).val());
			}else{
				$('#email2').val('');
			}
		});

		/*
		$('#sido .iList input[type=radio]').on('click', function(){
			for(var i = 1;i<=17;i++){
				$('#gugun' + i).hide();
			}
			$('#'+$(this).data('gugun')).css('display', 'inline-block');
		});
		*/

		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>
	});
	</script>
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="join">회원가입</h3>
            
			<form action="/signup" method="post" id="form_signup">
            <p class="sub_title default">기본정보</p>
            <div class="user_info">
                <dl>
                    <dt>회원타입</dt>
                    <dd class="check">
                        <p>
                            <input name="user_type" id="type1" value="N" class="option" type="radio"<?php if(empty($flash['user_type']) || $flash['user_type'] == 'N') echo ' checked="checked"' ?> /><label for="type1"><b>일반</b></label> &nbsp;&nbsp;
                            <input name="user_type" id="type2" value="T" class="option" type="radio"<?php if(!empty($flash['user_type']) || $flash['user_type'] == 'T') echo ' checked="checked"' ?> /><label for="type2">Trader</label>
                        </p>                    
                    </dd>
                    <dt>이메일</dt>
                    <dd class="mail">
                        <input id="email1" name="email1" type="text" title="이메일" value="<?php if(!empty($flash['email1'])) echo htmlspecialchars($flash['email1']) ?>" required="required" /> <i>@</i>
                        <input id="email2" name="email2" type="text" title="이메일" value="<?php if(!empty($flash['email2'])) echo htmlspecialchars($flash['email2']) ?>" required="required" />
                        
                        <div class="select open" style="width:140px;">
                            <div class="myValue"></div>
                            <ul class="iList">
                                <li><input name="mail" id="mail0" class="option" type="radio" value=""<?php if(empty($flash['email2'])) echo ' checked="checked"' ?> /><label for="mail0">이메일 선택</label></li>
                                <li><input name="mail" id="mail1" class="option" type="radio" value="naver.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'naver.com') echo ' checked="checked"' ?> /><label for="mail1">naver.com</label></li>
                                <li><input name="mail" id="mail2" class="option" type="radio" value="chol.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'chol.com') echo ' checked="checked"' ?> /><label for="mail2">chol.com</label></li>
                                <li><input name="mail" id="mail3" class="option" type="radio" value="empal.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'empal.com') echo ' checked="checked"' ?> /><label for="mail3">empal.com</label></li>
                                <li><input name="mail" id="mail4" class="option" type="radio" value="freechal.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'freechal.com') echo ' checked="checked"' ?> /><label for="mail4">freechal.com</label></li>
                                <li><input name="mail" id="mail5" class="option" type="radio" value="gmail.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'gmail.com') echo ' checked="checked"' ?> /><label for="mail5">gmail.com</label></li>
                                <li><input name="mail" id="mail6" class="option" type="radio" value="hanmail.net"<?php if(!empty($flash['email2']) && $flash['email2'] == 'hanmail.net') echo ' checked="checked"' ?> /><label for="mail6">hanmail.net</label></li>
                                <li><input name="mail" id="mail7" class="option" type="radio" value="hanmir.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'hanmir.com') echo ' checked="checked"' ?> /><label for="mail7">hanmir.com</label></li>
                                <li><input name="mail" id="mail8" class="option" type="radio" value="hitel.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'hitel.com') echo ' checked="checked"' ?> /><label for="mail8">hitel.com</label></li>
                                <li><input name="mail" id="mail9" class="option" type="radio" value="hotmail.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'hotmail.com') echo ' checked="checked"' ?> /><label for="mail9">hotmail.com</label></li>
                                <li><input name="mail" id="mail10" class="option" type="radio" value="korea.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'korea.com') echo ' checked="checked"' ?> /><label for="mail10">korea.com</label></li>
                                <li><input name="mail" id="mail11" class="option" type="radio" value="lycos.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'lycos.com') echo ' checked="checked"' ?> /><label for="mail11">lycos.com</label></li>
                                <li><input name="mail" id="mail12" class="option" type="radio" value="nate.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'nate.com') echo ' checked="checked"' ?> /><label for="mail12">nate.com</label></li>
                                <li><input name="mail" id="mail13" class="option" type="radio" value="netian.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'netian.com') echo ' checked="checked"' ?> /><label for="mail13">netian.com</label></li>
                                <li><input name="mail" id="mail14" class="option" type="radio" value="paran.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'paran.com') echo ' checked="checked"' ?> /><label for="mail14">paran.com</label></li>
                                <li><input name="mail" id="mail15" class="option" type="radio" value="yahoo.com"<?php if(!empty($flash['email2']) && $flash['email2'] == 'yahoo.com') echo ' checked="checked"' ?> /><label for="mail15">yahoo.com</label></li>
                            </ul>
                        </div> 

                        <button type="button" title="중복확인" class="act" value="중복확인" id="btn_user_id_check"><span class="ir">중복확인</span></button>
                        <span>* 사이트 이용 시 아이디로 사용됩니다. </span>
                    </dd>
                    <dt>비밀번호</dt>
                    <dd>
                        <input id="password" name="password" type="password" title="비밀번호" value="" required="required" />
                        <span>* 비밀번호는 문자, 숫자포함 6~20자로 되어야 합니다.</span>
                    </dd>
                    <dt>비밀번호 확인</dt>
                    <dd><input id="password_confirm" name="password_confirm" type="password" title="비밀번호 확인" value="" required="required" /></dd>
					<dt>닉네임</dt>
                    <dd>
                        <input id="nickname" name="nickname" type="text" title="닉네임" value="<?php if(!empty($flash['nickname'])) echo htmlspecialchars($flash['nickname']) ?>" required="required" />
			<span>*사이트 이용시 사용할 이름을 입력해 주세요. </span>
                    </dd>
                </dl>
            </div>

            <p class="sub_title add">추가정보</p>
            <div class="user_info">
                <dl>
                    <dt>이름</dt>
                    <dd>
                        <input id="name" name="name" type="text" title="이름" value="<?php if(!empty($flash['name'])) echo htmlspecialchars($flash['name']) ?>" />
                        <span>* 반드시 실명을 입력해 주시기 바랍니다.</span>
                    </dd>
                    <dt>휴대폰</dt>
                    <dd>
                        <input id="mobile" name="mobile" type="text" title="휴대폰" value="<?php if(!empty($flash['mobile'])) echo htmlspecialchars($flash['mobile']) ?>" />
                        <span>- &nbsp;없이 입력해 주세요.</span>
                    </dd>
                    <dt>생년월일</dt>
                    <dd>
                        <input id="birthday" name="birthday" type="text" title="생년월일" value="<?php if(!empty($flash['birthday'])) echo htmlspecialchars($flash['birthday']) ?>" maxlength="8" />
                        <span class="num">ex) 19800922</span>
                    </dd>
                    <dt>지역</dt>
                    <dd id="region">                        
                        <div class="select open" style="width:140px;" id="sido">
                            <div class="myValue"></div>
                            <ul class="iList">
                                <li><input name="sido" id="location0" class="option" type="radio" value=""<?php if(empty($flash['sido'])) echo ' checked="checked"' ?> /><label for="location0">지역선택1</label></li>
                                <li><input name="sido" id="location1" class="option" type="radio" value="서울특별시" data-gugun="gugun1"<?php if(!empty($flash['sido']) && $flash['sido'] == '서울특별시') echo ' checked="checked"' ?> /><label for="location1">서울특별시</label></li>
                                <li><input name="sido" id="location2" class="option" type="radio" value="부산광역시" data-gugun="gugun2"<?php if(!empty($flash['sido']) && $flash['sido'] == '부산광역시') echo ' checked="checked"' ?> /><label for="location2">부산광역시</label></li>
                                <li><input name="sido" id="location3" class="option" type="radio" value="인천광역시" data-gugun="gugun3"<?php if(!empty($flash['sido']) && $flash['sido'] == '인천광역시') echo ' checked="checked"' ?> /><label for="location3">인천광역시</label></li>
                                <li><input name="sido" id="location4" class="option" type="radio" value="대구광역시" data-gugun="gugun4"<?php if(!empty($flash['sido']) && $flash['sido'] == '대구광역시') echo ' checked="checked"' ?> /><label for="location4">대구광역시</label></li>
                                <li><input name="sido" id="location5" class="option" type="radio" value="광주광역시" data-gugun="gugun5"<?php if(!empty($flash['sido']) && $flash['sido'] == '광주광역시') echo ' checked="checked"' ?> /><label for="location5">광주광역시</label></li>
                                <li><input name="sido" id="location6" class="option" type="radio" value="대전광역시" data-gugun="gugun6"<?php if(!empty($flash['sido']) && $flash['sido'] == '대전광역시') echo ' checked="checked"' ?> /><label for="location6">대전광역시</label></li>
                                <li><input name="sido" id="location7" class="option" type="radio" value="울산광역시" data-gugun="gugun7"<?php if(!empty($flash['sido']) && $flash['sido'] == '울산광역시') echo ' checked="checked"' ?> /><label for="location7">울산광역시</label></li>
                                <li><input name="sido" id="location8" class="option" type="radio" value="세종특별자치시" data-gugun="gugun8"<?php if(!empty($flash['sido']) && $flash['sido'] == '세종특별자치시') echo ' checked="checked"' ?> /><label for="location8">세종특별자치시</label></li>
                                <li><input name="sido" id="location9" class="option" type="radio" value="경기도" data-gugun="gugun9"<?php if(!empty($flash['sido']) && $flash['sido'] == '경기도') echo ' checked="checked"' ?> /><label for="location9">경기도</label></li>
                                <li><input name="sido" id="location10" class="option" type="radio" value="강원도" data-gugun="gugun10"<?php if(!empty($flash['sido']) && $flash['sido'] == '강원도') echo ' checked="checked"' ?> /><label for="location10">강원도</label></li>
                                <li><input name="sido" id="location11" class="option" type="radio" value="충청남도" data-gugun="gugun11"<?php if(!empty($flash['sido']) && $flash['sido'] == '충청남도') echo ' checked="checked"' ?> /><label for="location11">충청남도</label></li>
                                <li><input name="sido" id="location12" class="option" type="radio" value="충청북도" data-gugun="gugun12"<?php if(!empty($flash['sido']) && $flash['sido'] == '충청북도') echo ' checked="checked"' ?> /><label for="location12">충청북도</label></li>
                                <li><input name="sido" id="location13" class="option" type="radio" value="경상북도" data-gugun="gugun13"<?php if(!empty($flash['sido']) && $flash['sido'] == '경상북도') echo ' checked="checked"' ?> /><label for="location13">경상북도</label></li>
                                <li><input name="sido" id="location14" class="option" type="radio" value="경상남도" data-gugun="gugun14"<?php if(!empty($flash['sido']) && $flash['sido'] == '경상남도') echo ' checked="checked"' ?> /><label for="location14">경상남도</label></li>
                                <li><input name="sido" id="location15" class="option" type="radio" value="전라북도" data-gugun="gugun15"<?php if(!empty($flash['sido']) && $flash['sido'] == '전라북도') echo ' checked="checked"' ?> /><label for="location15">전라북도</label></li>
                                <li><input name="sido" id="location16" class="option" type="radio" value="전라남도" data-gugun="gugun16"<?php if(!empty($flash['sido']) && $flash['sido'] == '전라남도') echo ' checked="checked"' ?> /><label for="location16">전라남도</label></li>
                                <li><input name="sido" id="location17" class="option" type="radio" value="제주도" data-gugun="gugun17"<?php if(!empty($flash['sido']) && $flash['sido'] == '제주도') echo ' checked="checked"' ?> /><label for="location17">제주도</label></li>
                            </ul>
                        </div>
						<input id="gugun" name="gugun" type="text" title="구군" value="<?php if(!empty($flash['gugun'])) echo htmlspecialchars($flash['gugun']) ?>" maxlength="8" />
                    </dd>
                    <dt>성별</dt>
                    <dd class="check">
                        <p>
                            <input name="gender" id="sex1" class="option" type="radio" value="M"<?php if(empty($flash['gender']) || $flash['gender'] == 'M') echo ' checked="checked"' ?> /><label for="sex1"><b>남</b></label> &nbsp;&nbsp;
                            <input name="gender" id="sex2" class="option" type="radio" value="F"<?php if(!empty($flash['gender']) && $flash['gender'] == 'F') echo ' checked="checked"' ?>  /><label for="sex2">여</label>
                        </p>                    
                    </dd>
                </dl>
            </div>
            
            <p class="sub_title agree">정보수신동의</p>
            <div class="agree_mail">
                <ul>
                    <li class="agree">
                        <p><input name="alarm_feeds" id="agree1" class="option" type="checkbox"<?php // if(!empty($flash['alarm_feeds'])) echo ' checked="checked"' ?> checked="checked" /><label for="agree1">관심 전략과 관심 포트폴리오 관련 정보를 수신 동의합니다.</label> </p>
                    </li>
                    <li> 
                        <p><input name="alarm_all" id="agree2" class="option" type="checkbox"<?php if(!empty($flash['alarm_all'])) echo ' checked="checked"' ?> /><label for="agree2">전략 및 정보성 알림에 수신 동의합니다. </label></p>
                    </li>
                </ul>
            </div>

            <p class="btn_board">
                <button type="submit" title="회원가입" class="submit"><span class="ir">회원가입</span></button>
                <button type="reset" title="취소" class="cancel"><span class="ir">취소</span></button>
            </p>
			</form>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
