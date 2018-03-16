<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 개인정보 수정</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script type="text/javascript" src="/js/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="/js/jquery.iframe-transport.js"></script>
	<script type="text/javascript" src="/js/jquery.fileupload.js"></script>
	<script>
	$(function(){
		$('#iframe_upload').load(function () {
			iframeContents = this.contentWindow.document.body.innerHTML;
			var n = iframeContents.indexOf('http');
			if(n != -1){
				$('#my_pic').attr('src', iframeContents);
			}else{
				alert(iframeContents);
			}
		});

		$('#file1').fileupload({
			forceIframeTransport: true,
			dataType: 'text',
			done: function (e, data) {
				$('#my_pic').attr('src', data.result);
				/*
				$.each(data.result.files, function (index, file) {
					alert(file.name);
					// $('<p/>').text(file.name).appendTo(document.body);
				});
				*/
			},
			change: function (e, data) {
				$.each(data.files, function (index, file) {
					// alert('Selected file: ' + file.name);
				});
			},
			fail: function (e, data) {
				/*
				var log = Function.prototype.bind.call(console.log, console);
				log.apply(console, data);
				console.log(data);
				alert(data.errorThrown);
				*/
				alert(data.textStatus);
			}
		});

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

		$('#settings_form').submit(function(){
			<?php if(empty($_SESSION['user']['email'])){ ?>
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
			<?php } ?>

			<?php if(empty($_SESSION['skip_current_password']) || !$_SESSION['skip_current_password']) { ?>
			if(!$('#current_password').val()){
				alert('사용중인 비밀번호를 입력해주세요');
				$('#current_password').focus();
				return false;
			}
			<?php } ?>

			if(!$('#nickname').val()){
				alert('닉네임은 필수 입력사항입니다.');
				$('#nickname').focus();
				return false;
			}

			if($('#password').val() && $('#password_confirm').val()){
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
			}

			if($('#mobile').val() && !/^[0-9]{10,11}$/.test($('#mobile').val())){
				alert('정확한 휴대폰 번호를 확인해 주세요.');
				$('#mobile').focus();
				return false;
			}

			if($('#birthday').val() && !/^[0-9]{8}$/.test($('#birthday').val())){
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
            <h3 class="info_edit">개인정보 수정</h3>
            
			<form action="/settings/edit" method="post" enctype="multipart/form-data" id="settings_form">
            <p class="sub_title default">기본정보</p>
            <div class="user_info">
                <dl>
                    <dt>회원타입</dt>
                    <?php if($_SESSION['user']['user_type'] == 'T'){ ?>
                    <dd class="txt">Trader</dd>
                    <?php }else if($_SESSION['user']['user_type'] == 'A'){ ?>
                    <dd class="txt">Admin</dd>
                    <?php }else if($_SESSION['user']['user_type'] == 'B'){ ?>
                    <dd class="txt">Broker</dd>
                    <?php }else{ ?>
                    <dd class="check">
                        <!-- 회원타입 변경 개발 필요 -->
                        <p>
                            <input name="user_type" id="user_type2" class="option" type="radio" value="N"<?php if($_SESSION['user']['user_type'] == 'N') echo ' checked="checked"' ?> /><label for="user_type2">일반</label>
                            <input name="user_type" id="user_type1" class="option" type="radio" value="T"<?php if($_SESSION['user']['user_type'] == 'T') echo ' checked="checked"' ?> /><label for="user_type1">Trader</label> &nbsp;&nbsp;
                        </p>     
                        <span>* 회원 타입 변경 시 관리자의 승인이 필요합니다.</span>
					</dd>   
                    <?php } ?>
                    <dt>이메일</dt>
					<?php if(empty($_SESSION['user']['email'])){ ?>
                    <dd class="mail">
                        <input id="email1" name="email1" type="text" title="이메일" required="required" /> <i>@</i>
                        <input id="email2" name="email2" type="text" title="이메일" required="required" />
                        
                        <div class="select open" style="width:140px;">
                            <div class="myValue"></div>
                            <ul class="iList">
                                <li><input name="mail" id="mail0" class="option" type="radio" checked="checked" value="" /><label for="mail0">이메일 선택</label></li>
                                <li><input name="mail" id="mail1" class="option" type="radio" value="naver.com"  /><label for="mail1">naver.com</label></li>
                                <li><input name="mail" id="mail2" class="option" type="radio" value="chol.com"  /><label for="mail2">chol.com</label></li>
                                <li><input name="mail" id="mail3" class="option" type="radio" value="empal.com"  /><label for="mail3">empal.com</label></li>
                                <li><input name="mail" id="mail4" class="option" type="radio" value="freechal.com"  /><label for="mail4">freechal.com</label></li>
                                <li><input name="mail" id="mail5" class="option" type="radio" value="gmail.com"  /><label for="mail5">gmail.com</label></li>
                                <li><input name="mail" id="mail6" class="option" type="radio" value="hanmail.net"  /><label for="mail6">hanmail.net</label></li>
                                <li><input name="mail" id="mail7" class="option" type="radio" value="hanmir.com"  /><label for="mail7">hanmir.com</label></li>
                                <li><input name="mail" id="mail8" class="option" type="radio" value="hitel.com"  /><label for="mail8">hitel.com</label></li>
                                <li><input name="mail" id="mail9" class="option" type="radio" value="hotmail.com"  /><label for="mail9">hotmail.com</label></li>
                                <li><input name="mail" id="mail10" class="option" type="radio" value="korea.com"  /><label for="mail10">korea.com</label></li>
                                <li><input name="mail" id="mail11" class="option" type="radio" value="lycos.com"  /><label for="mail11">lycos.com</label></li>
                                <li><input name="mail" id="mail12" class="option" type="radio" value="nate.com"  /><label for="mail12">nate.com</label></li>
                                <li><input name="mail" id="mail13" class="option" type="radio" value="netian.com"  /><label for="mail13">netian.com</label></li>
                                <li><input name="mail" id="mail14" class="option" type="radio" value="paran.com"  /><label for="mail14">paran.com</label></li>
                                <li><input name="mail" id="mail15" class="option" type="radio" value="yahoo.com"  /><label for="mail15">yahoo.com</label></li>
                            </ul>
                        </div> 
                        <button type="button" title="중복확인" class="act" id="btn_user_id_check" value="중복확인"><span class="ir">중복확인</span></button>
                        <span>* 사이트 이용 시 아이디로 사용됩니다. </span>
                    </dd>
					<?php }else{ ?>
                    <dd class="txt"><?php echo htmlspecialchars($_SESSION['user']['email']) ?></dd>
					<?php } ?>
					<dt>닉네임</dt>
                    <dd>
                        <input id="nickname" name="nickname" type="text" title="닉네임" value="<?php echo htmlspecialchars($_SESSION['user']['nickname']) ?>" required="required" />
                        <span>* 사이트 이용 시 사용할 닉네임을 입력해 주세요.</span>
                    </dd>
					<?php if(empty($_SESSION['user']['platform'])){ ?>
					<?php if(empty($_SESSION['skip_current_password']) || !$_SESSION['skip_current_password']) { ?>
					<dt>현재 비밀번호</dt>
                    <dd>
                        <input id="current_password" name="current_password" type="password" title="현재 비밀번호" value="" required="required" />
                    </dd>
                    <?php } ?>
                    <dt>비밀번호</dt>
                    <dd>
                        <input id="password" name="password" type="password" title="비밀번호" value="" />
                        <span>* 비밀번호는 문자, 숫자포함 6~20자로 되어야 합니다.</span>
                    </dd>
                    <dt>비밀번호 확인</dt>
                    <dd><input id="password_confirm" name="password_confirm" type="password" title="비밀번호 확인" value="" /></dd>
					<?php } ?>
                </dl>
            </div>

            <p class="sub_title add">추가정보</p>
            <div class="user_info more_info">
                <dl>
                    <?php if($_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'B' || $_SESSION['user']['user_type'] == 'A'){ ?>
                    <dt class="photo">프로필<br />이미지</dt>
                    <dd class="photo">
                        
                    </dd>
                    <?php } ?>
                    <dt>이름</dt>
                    <dd>
                        <input id="name" name="name" type="text" title="이름" value="<?php echo htmlspecialchars($_SESSION['user']['name']) ?>" />   
			<span>* 반드시 실명을 입력해 주시기 바랍니다.</span>
                    </dd>
                    <dt>휴대폰</dt>
                    <dd class="eng">
                        <input id="mobile" name="mobile" type="text" title="휴대폰" value="<?php echo htmlspecialchars($_SESSION['user']['mobile']) ?>" />
                        <span>- &nbsp;없이 입력해 주세요.</span>
                    </dd>
                    <dt>생년월일</dt>
                    <dd class="eng">
                        <input id="birthday" name="birthday" type="text" title="생년월일" value="<?php echo htmlspecialchars($_SESSION['user']['birthday']) ?>" maxlength="8" />
                        <span class="num">ex) 19800922</span>
                    </dd>
                    <dt>지역</dt>
                    <dd id="region">                        
                        <div class="select open" style="width:140px;" id="sido">
                            <div class="myValue"></div>
                            <ul class="iList">
                                <li><input name="sido" id="location0" class="option" type="radio" <?php if(empty($_SESSION['user']['sido'])) echo ' checked="checked"' ?> value="" /><label for="location0">지역선택1</label></li>
                                <li><input name="sido" id="location1" class="option" type="radio" value="서울특별시" data-gugun="gugun1"<?php if($_SESSION['user']['sido'] == '서울특별시') echo ' checked="checked"' ?> /><label for="location1">서울특별시</label></li>
                                <li><input name="sido" id="location2" class="option" type="radio" value="부산광역시" data-gugun="gugun2"<?php if($_SESSION['user']['sido'] == '부산광역시') echo ' checked="checked"' ?> /><label for="location2">부산광역시</label></li>
                                <li><input name="sido" id="location3" class="option" type="radio" value="인천광역시" data-gugun="gugun3"<?php if($_SESSION['user']['sido'] == '인천광역시') echo ' checked="checked"' ?> /><label for="location3">인천광역시</label></li>
                                <li><input name="sido" id="location4" class="option" type="radio" value="대구광역시" data-gugun="gugun4"<?php if($_SESSION['user']['sido'] == '대구광역시') echo ' checked="checked"' ?> /><label for="location4">대구광역시</label></li>
                                <li><input name="sido" id="location5" class="option" type="radio" value="광주광역시" data-gugun="gugun5"<?php if($_SESSION['user']['sido'] == '광주광역시') echo ' checked="checked"' ?> /><label for="location5">광주광역시</label></li>
                                <li><input name="sido" id="location6" class="option" type="radio" value="대전광역시" data-gugun="gugun6"<?php if($_SESSION['user']['sido'] == '대전광역시') echo ' checked="checked"' ?> /><label for="location6">대전광역시</label></li>
                                <li><input name="sido" id="location7" class="option" type="radio" value="울산광역시" data-gugun="gugun7"<?php if($_SESSION['user']['sido'] == '울산광역시') echo ' checked="checked"' ?> /><label for="location7">울산광역시</label></li>
                                <li><input name="sido" id="location8" class="option" type="radio" value="세종특별자치시" data-gugun="gugun8"<?php if($_SESSION['user']['sido'] == '세종특별자치시') echo ' checked="checked"' ?> /><label for="location8">세종특별자치시</label></li>
                                <li><input name="sido" id="location9" class="option" type="radio" value="경기도" data-gugun="gugun9"<?php if($_SESSION['user']['sido'] == '경기도') echo ' checked="checked"' ?> /><label for="location9">경기도</label></li>
                                <li><input name="sido" id="location10" class="option" type="radio" value="강원도" data-gugun="gugun10"<?php if($_SESSION['user']['sido'] == '강원도') echo ' checked="checked"' ?> /><label for="location10">강원도</label></li>
                                <li><input name="sido" id="location11" class="option" type="radio" value="충청남도" data-gugun="gugun11"<?php if($_SESSION['user']['sido'] == '충청남도') echo ' checked="checked"' ?> /><label for="location11">충청남도</label></li>
                                <li><input name="sido" id="location12" class="option" type="radio" value="충청북도" data-gugun="gugun12"<?php if($_SESSION['user']['sido'] == '충청북도') echo ' checked="checked"' ?> /><label for="location12">충청북도</label></li>
                                <li><input name="sido" id="location13" class="option" type="radio" value="경상북도" data-gugun="gugun13"<?php if($_SESSION['user']['sido'] == '경상북도') echo ' checked="checked"' ?> /><label for="location13">경상북도</label></li>
                                <li><input name="sido" id="location14" class="option" type="radio" value="경상남도" data-gugun="gugun14"<?php if($_SESSION['user']['sido'] == '경상남도') echo ' checked="checked"' ?> /><label for="location14">경상남도</label></li>
                                <li><input name="sido" id="location15" class="option" type="radio" value="전라북도" data-gugun="gugun15"<?php if($_SESSION['user']['sido'] == '전라북도') echo ' checked="checked"' ?> /><label for="location15">전라북도</label></li>
                                <li><input name="sido" id="location16" class="option" type="radio" value="전라남도" data-gugun="gugun16"<?php if($_SESSION['user']['sido'] == '전라남도') echo ' checked="checked"' ?> /><label for="location16">전라남도</label></li>
                                <li><input name="sido" id="location17" class="option" type="radio" value="제주도" data-gugun="gugun17"<?php if($_SESSION['user']['sido'] == '제주도') echo ' checked="checked"' ?> /><label for="location17">제주도</label></li>
                            </ul>
                        </div> 
						<input id="gugun" name="gugun" type="text" title="구군" value="<?php echo htmlspecialchars($_SESSION['user']['gugun']) ?>" />
                    </dd>
                    <dt>성별</dt>
                    <dd class="check">
                        <p>
                            <input name="gender" id="sex1" class="option" type="radio" value="M"<?php if($_SESSION['user']['gender'] == 'M') echo ' checked="checked"' ?> /><label for="sex1"><b>남</b></label> &nbsp;&nbsp;
                            <input name="gender" id="sex2" class="option" type="radio" value="F"<?php if($_SESSION['user']['gender'] != 'M') echo ' checked="checked"' ?> /><label for="sex2">여</label>
                        </p>                    
                    </dd>
                </dl>
            </div>
            
            <p class="sub_title agree">정보수신동의</p>
            <div class="agree_mail">
                <ul>
                    <li class="agree">
                        <p><input name="alarm_feeds" id="agree1" class="option" type="checkbox"<?php if($_SESSION['user']['alarm_feeds']) echo ' checked="checked"' ?> /><label for="agree1">관심 전략과 관심 포트폴리오 관련 정보를 수신 동의합니다.</label> </p>
                    </li>
                    <li> 
                        <p><input name="alarm_all" id="agree2" class="option" type="checkbox"<?php if($_SESSION['user']['alarm_all']) echo ' checked="checked"' ?> /><label for="agree2">전략 및 정보성 알림에 수신 동의합니다. </label></p>
                    </li>
                </ul>
            </div>

            <p class="btn_board">
                <button type="submit" title="완료" class="submit"><span class="ir">완료</span></button>
                <button type="reset" title="취소" class="cancel"><span class="ir">취소</span></button>
            </p>
			</form>
      
			<?php if($_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'B' || $_SESSION['user']['user_type'] == 'A'){ ?>      
            <!------ 트레이더/브로커/어드민만 프로필 이미지 등록 가능 ------->
			<form id="picture_upload_form" action="/settings/upload_picture" method="post" enctype="multipart/form-data" target="iframe_upload">
            <div class="photo_edit">    
                <img src="/img/profile_over.png" class="over" />
                <img src="<?php echo $_SESSION['user']['picture_s'] ?>" alt="<?php echo htmlspecialchars($_SESSION['user']['name']) ?>" id="my_pic" />

				<input id="img1" name="profile_fake" type="text" class="file_input_textbox" readonly="readonly">

				<div class="file_input_div">		
					<input type="button" value="" class="file_input_button edit" />
					<input id="file1" name="profile" type="file" data-url="/settings/upload_picture"  class="file_input_hidden" />
				</div>

            </div>
            </form>
			<!--
			<iframe id="iframe_upload" name="iframe_upload" src="#" style="width:0;height:0;border:0;"></iframe>
			-->
			<?php } ?>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
