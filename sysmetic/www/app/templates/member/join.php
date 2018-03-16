<?php
$type = (!empty($flash['user_type'])) ? htmlspecialchars($flash['user_type']) : $type;
?>
<!doctype html>
<html lang="ko">
<head>
	<title>회원가입 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
	<script src="/script/jquery.iframe-transport.js"></script>
	<script src="/script/jquery.fileupload.js"></script>
	<script>
	$(function(){
		$('#btn_user_id_check').on('click', function(){
			if($('#email1').val() && $('#email2').val()){
				$.post('/member/email_check', {email:$('#email1').val()+'@'+$('#email2').val()}, function(data){
					if(data.result){
                        notice('이미 가입된 이메일 주소 입니다.<br />	다른 이메일 주소로 가입해 주세요.');
						$('#email1').focus();
					}else{
                        notice('사용 가능한 이메일 주소입니다.');
					}
				}, 'json');
			}else{
                alert('이메일을 입력해주세요.');
				$('#email1').focus();
			}
		});

		$('#form_signup').submit(function(){

			if(!$('#name').val()){
                alert('이름을 입력해주세요.');
				$('#name').focus();
				return false;
			}

			if(!$('#email1').val()){
                alert('이메일을 입력해주세요.');
				$('#email1').focus();
				return false;
			}

			if(!$('#email2').val()){
                alert('이메일을 입력해주세요.');
				$('#email2').focus();
				return false;
			}

			if(!$('#password').val()){
                alert('비밀번호를 입력해주세요.');
				$('#password').focus();
				return false;
			}

			if(!$('#password_confirm').val()){
                alert('비밀번호확인을 입력해주세요.');
				$('#password_confirm').focus();
				return false;
			}

			if($('#password').val() != $('#password_confirm').val()){
                alert("비밀번호가 맞지 않습니다\n다시 확인해 주세요.");
				$('#password').focus();
				return false;
			}

			if($('#password').val().length < 6 || $('#password').val().length >= 20){
                alert('비밀번호는 문자, 숫자포함 6자 이상 20자 이하이어야 합니다.');
				$('#password').focus();
				return false;
			}

			if(!/^(?=.*\d)(?=.*[a-zA-Z]).{6,19}$/.test($('#password').val())){
                alert('비밀번호는 문자, 숫자포함 6자 이상 20자 이하이어야 합니다.');
				$('#password').focus();
				return false;
			}

			if(!$('#mobile').val()){
                alert('휴대폰번호를 입력해주세요.');
				$('#mobile').focus();
				return false;
			}

			if(!$('#mobile').val() && !/^[0-9]{10,11}$/.test($('#mobile').val())){
                alert('정확한 휴대폰 번호를 확인해 주세요.');
				$('#mobile').focus();
				return false;
			}
/*
			if(!$('#nickname').val()){
				alert('닉네임은 필수 입력사항입니다.');
				$('#nickname').focus();
				return false;
			}
*/
			if($('#birthday').val() && !/^[0-9]{8}$/.test($('#birthday').val())){
				alert('생년월일을 다시 확인해 주세요.');
				$('#birthday').focus();
				return false;
			}

            if ($('#user_type').val() == 'T' && !$('#check01').is(":checked")) {
                alert('트레이더회원 이용약관에 동의해 주세요');
                $('#check01').focus();
                return false;
            }

            if ($('#user_type').val() == 'P' && !$('#check01').is(":checked")) {
                alert('PB회원 이용약관에 동의해 주세요');
                $('#check01').focus();
                return false;
            }

			return true;
		});

		$('#email_choice').change(function(){
			$('#email2').val($(this).val());
		});


        $('#gugun').focus(function(){
			if(!$('#sido').val()){
                alert('지역을 먼저 선택해 주세요.');
				$('#sido').focus();
				return false;
			}
        });

        // 프로필 이미지 등록
		$('#profile').fileupload({
			forceIframeTransport: true,
			dataType: 'text',
			done: function (e, data) {
				$('#my_pic').attr('src', data.result);
			},
			change: function (e, data) {
				$.each(data.files, function (index, file) {
				});
			},
			fail: function (e, data) {
				alert(data.textStatus);
			}
		});

        // 프로필이미지 삭제
        $('#delProfile').click(function(){
            var img = $('#my_pic').attr('src');
			$('#my_pic').attr('src', '');
			$('#sample_photo').val('');
			
            if (img != "") {
                $.post('/settings/delete_picture', {'type':'profile', 'img':img}, function(data) {
                    if (data == 'success') {
                        $('#my_pic').attr('src', '');
                    } else {
                       // alert('이미지 삭제중 요류가 발생하였습니다');
                    }
                });
            }
        });

        // 명함이미지등록
		$('#namecard').fileupload({
			forceIframeTransport: true,
			dataType: 'text',
			done: function (e, data) {
				$('#my_namecard').attr('src', data.result);
			},
			change: function (e, data) {
				$.each(data.files, function (index, file) {
				});
			},
			fail: function (e, data) {
				alert(data.textStatus);
			}
		});


        // 명함이미지삭제
        $('#delNamecard').click(function(){
            var img = $('#my_namecard').attr('src');
            if (img != "") {
                $.post('/settings/delete_picture', {'type':'namecard', 'img':img}, function(data) {
                    if (data == 'success') {
                        $('#my_namecard').attr('src', '');
                    } else {
                        alert('이미지 삭제중 요류가 발생하였습니다');
                    }
                });
            }
        });


		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>
	});

    var CBA_window;

    function openPCCWindow(){
        var CBA_window = window.open('', 'PCCWindow', 'width=430, height=560, resizable=1, scrollbars=no, status=0, titlebar=0, toolbar=0, left=300, top=200' );

        if(CBA_window == null){
             alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
        }

        document.reqCBAForm.action = 'https://pcc.siren24.com/pcc_V3/jsp/pcc_V3_j10.jsp';
       // document.reqCBAForm.action = 'http://sysmetic.mypro.co.kr/member/auth_result';
        document.reqCBAForm.target = 'PCCWindow';

        return true;
    }

	</script>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once $skinDir."common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container join">
			<section class="area">
				<div class="page_title_area">
					<h2 class="page_title n_squere">회원가입</h2>
					<p class="page_summary">시스메틱 홈페이지를 찾아주셔서 감사합니다. 약관에 동의하셔야 가입이 가능합니다.</p>
				</div>
				<div class="content_area step03">

                    <form id="reqCBAForm" name="reqCBAForm" method="post" action = "" onsubmit="return openPCCWindow()">
                        <input type="hidden" name="reqInfo" value = "<?=$enc_reqInfo?>">
                        <input type="hidden" name="retUrl" value = "<?=$retUrl?>">
                    </form>

			        <form action="/member/signup" method="post" id="form_signup">
                        <input type="hidden" id="user_type" name="user_type" value="<?=$type?>" />
                        <input type="hidden" id="sample_photo" name="sample_photo" value="" />
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
														<input type="text" id="name" name="name" placeholder="실명인증 버튼을 눌러주세요" value="<?php if(!empty($flash['name'])) echo htmlspecialchars($flash['name']) ?>" readonly maxlength="20" onclick="$('#reqCBAForm').submit();" />
													</div>
													<button type="button" class="btn_default" onclick="$('#reqCBAForm').submit();">실명인증</button>
												</div>
											</td>
										</tr>
										<tr class="high">
											<th>이메일</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="width:208px;">
														<input type="text" id="email1" name="email1" value="<?php if(!empty($flash['email1'])) echo htmlspecialchars($flash['email1']) ?>" />
													</div>
													<span class="inner_txt">@</span>
													<div class="input_box" style="width:208px;">
														<input type="text" id="email2" name="email2" value="<?php if(!empty($flash['email2'])) echo htmlspecialchars($flash['email2']) ?>" />
													</div>
													<div class="custom_selectbox" style="margin-left:10px; width:208px;">
														<label for="email_choice">이메일을 선택해주세요.</label>
														<select id="email_choice">
															<option value="" selected="selected">이메일을 선택해주세요.</option>
                                                            <?
                                                            foreach (getEmailList() as $v) {
                                                            ?>
															<option value="<?=$v?>" <?=($flash['email2'] == $v) ? 'selected' : '' ?>><?=$v?></option>
                                                            <?
                                                            }
                                                            ?>
														</select>
													</div>
													<button type="button" id="btn_user_id_check" class="btn_default">중복확인</button>
												</div>
												<p class="txt_summary">* 사이트 이용 시 아이디로 사용됩니다.</p>
											</td>
										</tr>
										<tr>
											<th>비밀번호</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="margin-right:24px; width:298px;">
														<input type="password" id="password" name="password" placeholder="비밀번호를 입력해주세요." />
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
														<input type="password" id="password_confirm" name="password_confirm" placeholder="비밀번호를 다시 입력해주세요." />
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>휴대폰번호</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="margin-right:24px; width:298px;">
														<input type="text" id="mobile" name="mobile" placeholder="휴대폰번호를 입력해주세요." onkeyup="inputOnlyNumber(this)" value="<?php if(!empty($flash['mobile'])) echo htmlspecialchars($flash['mobile']) ?>" maxlength="12" />
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

                                        <?
                                        if ($type == 'T' || $type == 'P') {
                                        ?>
                                        <tr class="photo">
                                            <th>프로필이미지</th>
                                            <td>
                                                <div class="wrapping photo">
                                                    <div class="photo_box"><img id="my_pic" style='width:120px;height:121px' src="" /></div>
                                                    <input type="file" id="profile" name="profile" data-url="/settings/upload_picture" />
													<button type="button" class="btn_default" onclick="commonLayerOpen('choice_sample_img')">샘플선택</button>
                                                    <label for="profile" class="btn_default">사진첨부</label>
                                                    <button id="delProfile" type="button" class="btn_default">삭제하기</button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?
                                        }
                                        ?>
                                        <?
                                        if ($type != 'P') {
                                        ?>
                                        <tr>
                                            <th>닉네임</th>
                                            <td>
                                                <div class="wrapping">
                                                    <div class="input_box" style="margin-right:10px; width:298px;">
                                                        <input type="text" id="nickname" name="nickname" placeholder="닉네임을 입력해주세요." value="<?php if(!empty($flash['nickname'])) echo htmlspecialchars($flash['nickname']) ?>" maxlength="20" />
                                                    </div>
                                                    <p class="txt_summary">* 사이트 이용 시 사용할 이름을 입력해 주세요.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        <?
                                        }
                                        ?>

										<tr>
											<th>생년월일</th>
											<td>
												<div class="wrapping">
													<div class="input_box" style="margin-right:10px; width:298px;">
														<input type="text" id="birthday" name="birthday" placeholder="생년월일을 입력해주세요." onkeyup="inputOnlyNumber(this)" value="<?php if(!empty($flash['birthday'])) echo htmlspecialchars($flash['birthday']) ?>" maxlength="8" />
													</div>
													<p class="txt_summary">* 숫자만 입력해 주세요. ex)19801022</p>
												</div>
											</td>
										</tr>
										<tr>
											<th>성별</th>
											<td>
												<div class="choice_area">
													<input type="radio" id="male" name="gender" value="M" <?php if(empty($flash['gender']) || $flash['gender'] == 'M') echo ' checked="checked"' ?> />
													<label for="male">남성</label>
													<input type="radio" id="female" name="gender" value="F" <?php if(!empty($flash['gender']) && $flash['gender'] == 'F') echo ' checked="checked"' ?> />
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
														<select id="sido" name="sido">
															<option value="" selected="selected">지역 선택</option>
                                                            <?
                                                            $areas = getAreaList();
                                                            foreach ($areas as $v) {
                                                            ?>
															<option value="<?=$v?>" <?=($flash['sido'] == $v) ? 'selected' : '' ?>><?=$v?></option>
                                                            <?
                                                            }
                                                            ?>
														</select>
													</div>
													<div class="input_box" style="width:188px;">
														<input type="text" id="gugun" name="gugun" placeholder="시/구/군 입력" value="<?php if(!empty($flash['gugun'])) echo htmlspecialchars($flash['gugun']) ?>" />
													</div>
												</div>
											</td>
										</tr>

                                        <?
                                        if ($type == 'P') {
                                        ?>
										<tr class="more_high">
											<th>지점</th>
											<td style="height:200px">
												<div class="wrapping">
													<div class="custom_selectbox" style="margin-right:10px; width:188px;">
														<label for="brand_choice">증권사 선택</label>
														<select id="brand_choice" name="broker_id">
															<option value="" selected="selected">증권사 선택</option>
                                                            <? foreach ($brokers as $k => $v) { ?>
                                                            <option value="<?=$v['broker_id']?>" <?=($flash['broker_id'] == $v['broker_id']) ? 'selected' : '' ?>><?=$v['company'];?></option>
                                                            <? } ?>
														</select>
													</div>
													<!--div class="custom_selectbox" style="margin-right:10px; width:188px;">
														<label for="sido2">지역 선택</label>
														<select id="sido2" name="sido2">
															<option value="" selected="selected">지역 선택</option>
                                                            <? foreach ($areas as $v) { ?>
															<option value="<?=$v?>" <?=($flash['sido2'] == $v) ? 'selected' : '' ?>><?=$v?></option>
                                                            <? } ?>
														</select>
													</div-->
													<!--div class="input_box" style="margin-right:10px; width:188px;">
														<input type="text" id="gugun2" name="gugun2" placeholder="시/구/군 입력" value="<?php if(!empty($flash['gugun2'])) echo htmlspecialchars($flash['gubun2']) ?>"
													</div> /-->
													<div class="input_box" style="width:188px;">
														<input type="text" id="part" name="part" placeholder="지점 입력" value="<?php if(!empty($flash['part'])) echo htmlspecialchars($flash['part']) ?>" />
													</div>
												</div>
												<div class="input_box" style="margin-top:10px; width:388px;">
													<input type="text" id="sido2" name="sido2" placeholder="주소입력" value="<?php if(!empty($myInfo['sido2'])) echo htmlspecialchars($myInfo['sido2']) ?>" onclick="sample4_execDaumPostcode()" readonly />
												</div>
												<div class="input_box" style="margin-top:10px; width:788px;">
													<input type="text" id="addr" name="addr" placeholder="구/군 이하 상세주소 입력" style="padding:0 1.5%; width:97%;" value="<?php if(!empty($flash['addr'])) echo htmlspecialchars($flash['addr']) ?>" />
												</div>
											</td>
										</tr>
										<tr class="photo">
											<th>명함이미지</th>
											<td>
												<div class="wrapping photo namecard">
													<div class="photo_box"><img id="my_namecard" style='width:220px;height:121px' src="" /></div>
													<input type="file" id="namecard" name="namecard" data-url="/settings/upload_namecard" />
													<label for="namecard" class="btn_default">사진첨부</label>
													<button type="button" id="delNamecard" class="btn_default">삭제하기</button>
												</div>
											</td>
										</tr>
                                        <?
                                        }
                                        ?>

									</tbody>
								</table>
							</div>

							<div class="group">
								<h3>정보수신동의</h3>
								<div class="agree_box">
									<div class="agree01">
										<input type="checkbox" name="alarm_feeds" id="agree01" />
										<label for="agree01">관심 전략과 포트폴리오 관련 정보를 수신 동의합니다.</label>
									</div>
									<div class="agree02">
										<input type="checkbox" name="alarm_all" id="agree02"  /> <!-- <?//php if(!empty($flash['alarm_all'])) echo ' checked="checked"' ?> -->
										<label for="agree02">마케팅 및 정보성 알림에 수신 동의합니다.</label>
									</div>
								</div>
							</div>

                            <?
                            if ($type == 'T') {
                            ?>
							<div class="group">
								<div class="check_area">
									<input type="checkbox" id="check01" name="check01">
									<label for="check01">트레이더회원 이용약관에 동의합니다.</label>
								</div>
								<div class="cont_area">
									<p class="cont">
                                    <? include "agree_rules.php"; ?>
									</p>
								</div>
							</div>
                            <?
                            }
                            ?>

                            <?
                            if ($type == 'P') {
                            ?>
							<div class="group">
								<div class="check_area">
									<input type="checkbox" id="check01" name="check01">
									<label for="check01">PB회원 이용약관에 동의합니다.</label>
								</div>
								<div class="cont_area">
									<p class="cont">
                                    <? include "agree_rules.php"; ?>
									</p>
								</div>
							</div>
                            <?
                            }
                            ?>

							<div class="btn_area">
								<a href="javascript:;history.back();" class="btn_common_gray btn_cancel">취소</a>
								<button type="submit" class="btn_common_red btn_next_step">가입완료</button>
							</div>
						</fieldset>
					</form>
				</div>
			</section>
		</div>
		<!-- //container -->

        <!-- footer -->
		<? require_once $skinDir."common/footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->

    <!-- 레이어얼럿 -->
    <article class="layer_popup common_notice overlap_email">
        <div class="dim" onclick="commonLayerClose('common_notice')"></div>
        <div class="contents">
            <div class="layer_header">
                <h2>안내</h2>
                <button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('common_notice')"></button>
            </div>
            <div class="cont">
                <p class="txt_info">
                    <strong class="mark"></strong>
                </p>
                <button type="button" class="btn_common_gray" onclick="commonLayerClose('common_notice')">확인</button>
            </div>
        </div>
    </article>
    <!-- //레이어팝업 : -->

<!-- 레이어팝업 : 샘플이미지 선택 -->
<article class="layer_popup choice_sample_img">
	<div class="dim" onclick="commonLayerClose('choice_sample_img')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>샘플이미지 선택</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('choice_sample_img')"></button>
		</div>
		<div class="cont">
			<ul class="sample_list">
				<li>
					<div class="pic">
						<img src="../images/sub/img_profile_sample01.jpg" alt="" />
					</div>
					<button type="button" class="btn_choice" onclick="$('#my_pic').attr('src', '/images/sub/img_profile_sample01.jpg');$('#sample_photo').val('/images/sub/img_profile_sample01.jpg');commonLayerClose('choice_sample_img');">선택</button>
				</li>
				<li>
					<div class="pic">
						<img src="../images/sub/img_profile_sample02.jpg" alt="" />
					</div>
					<button type="button" class="btn_choice" onclick="$('#my_pic').attr('src', '/images/sub/img_profile_sample02.jpg');$('#sample_photo').val('/images/sub/img_profile_sample02.jpg');commonLayerClose('choice_sample_img');">선택</button>
				</li>
				<li>
					<div class="pic">
						<img src="../images/sub/img_profile_sample03.jpg" alt="" />
					</div>
					<button type="button" class="btn_choice" onclick="$('#my_pic').attr('src', '/images/sub/img_profile_sample03.jpg');$('#sample_photo').val('/images/sub/img_profile_sample03.jpg');commonLayerClose('choice_sample_img');">선택</button>
				</li>
			</ul>
			<button type="button" class="btn_common_gray" onclick="commonLayerClose('choice_sample_img')">닫기</button>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 샘플이미지 선택 -->

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


<div id="daum_layer" style="display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch;">
<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>

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

    <!-- 우편번호 찾기 -->
    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
    <script>
    function sample4_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = data.address; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 기본 주소가 도로명 타입일때 조합한다.
                if(data.addressType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    //fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }
                
                var tmpAddr = fullAddr.split(' ');

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('sido2').value = fullAddr;

                /*
                document.getElementById('sido2').value = tmpAddr[0];
                document.getElementById('gugun2').value = tmpAddr[1];
                document.getElementById('addr').value = tmpAddr.join(' ');
                */

                document.getElementById('addr').focus();
                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';
            }
        }).open();
    }
    </script>

</body>
</html>
