<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
	<script src="/script/jquery.iframe-transport.js"></script>
	<script src="/script/jquery.fileupload.js"></script>
	<script>
	$(function(){
		$('#form_modify').submit(function(){

            if(!$('#pre_password').val()){
                alert("비밀번호를 입력해주세요.");
                $('#pre_password').focus();
                return false;
            }

            if ($('#password').val() || $('#password_confirm').val())
            {
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

			if($('#birthday').val() && !/^[0-9]{8}$/.test($('#birthday').val())){
				alert('생년월일을 다시 확인해 주세요.');
				$('#birthday').focus();
				return false;
			}

            if (!confirm('수정하시겠습니까?')) {
                return false;
            }

			return true;
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
            if (img != "") {
                $.post('/settings/delete_picture', {'type':'profile', 'img':img}, function(data) {
                    if (data == 'success') {
                        $('#my_pic').attr('src', '');
                        $('#pre_profile').val('');
                        $('#pre_profile_s').val('');
                    } else {
                        alert('이미지 삭제중 요류가 발생하였습니다');
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
                        $('#pre_namecard').val('');
                        $('#pre_namecard_s').val('');
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

	</script>

</head>
<body>
    <!-- wrapper -->
    <div class="wrapper">
        <!-- header -->
        <? require_once $skinDir."common/header.php" ?>
        <!-- header -->
        <!-- container -->
        <div class="container">

			<section class="area mypage">	
				<div class="cont_a">

                <? require_once $skinDir."mypage/sub_menu.php" ?>
<?
//__v($myInfo);
?>
					<div class="content info_modify">
			            <form action="/mypage/modify" method="post" id="form_modify">
                        <input type="hidden" id="user_type" name="user_type" value="<?=$myInfo['user_type']?>" />
							<fieldset>
								<legend class="screen_out">개인정보 수정</legend>
								<h2 class="page_title n_squere">개인정보 수정</h2>
								<div class="group">
									<h3>필수정보입력</h3>
									<table class="form_tbl">
										<colgroup>
											<col style="width:164px">
											<col style="width:826px">
										</colgroup>
										<tbody>
											<tr>
												<th>이름</th>
												<td>
													<p class="fix_info"><?=$myInfo['name']?></p>
												</td>
											</tr>
											<tr class="high">
												<th>이메일</th>
												<td>
													<p class="fix_info"><?=$myInfo['email']?></p>
													<p class="txt_summary">* 사이트 이용 시 아이디로 사용됩니다.</p>
												</td>
											</tr>
											<tr>
												<th>비밀번호</th>
												<td>
													<div class="wrapping">
														<div class="input_box" style="margin-right:24px; width:298px;">
															<input type="password" id="pre_password" name="pre_password" placeholder="비밀번호를 입력해주세요.">
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>신규 비밀번호</th>
												<td>
													<div class="wrapping">
														<div class="input_box" style="margin-right:24px; width:298px;">
															<input type="password" id="password" name="password" placeholder="신규비밀번호를 입력해주세요.">
														</div>
														<p class="txt_summary">* 비밀번호는 문자, 숫자포함 6~20자로 되어야 합니다.</p>
													</div>
												</td>
											</tr>
											<tr>
												<th>신규 비밀번호 확인</th>
												<td>
													<div class="wrapping">
														<div class="input_box" style="width:298px;">
															<input type="password" id="password_confirm" name="password_confirm" placeholder="신규비밀번호를 다시 입력해주세요.">
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>휴대폰번호</th>
												<td>
													<div class="wrapping">
														<div class="input_box" style="margin-right:24px; width:298px;">
															<input type="text" id="mobile" name="mobile" placeholder="휴대폰번호를 입력해주세요." onkeyup="inputOnlyNumber(this)" maxlength="12" value="<?=$myInfo['mobile']?>">
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
												<col style="width:164px">
												<col style="width:826px">
											</colgroup>
											<tbody>
                                                
                                                <input type="hidden" id="pre_profile" name="pre_profile" value="<?=$myInfo['picture']?>" />
                                                <input type="hidden" id="pre_profile_s" name="pre_profile_s" value="<?=$myInfo['picture_s']?>" />
                                                <?
                                                if ($myInfo['user_type'] == 'T' || $myInfo['user_type'] == 'P') {
                                                ?>
												<tr class="photo">
													<th>프로필이미지</th>
													<td>
                                                        <div class="wrapping photo">
                                                            <div class="photo_box"><img id="my_pic" style='width:120px;height:121px' src="<?=$myInfo['picture']?>" /></div>
                                                            <input type="file" id="profile" name="profile" data-url="/settings/upload_picture" />
                                                            <label for="profile" class="btn_default">사진첨부</label>
                                                            <button id="delProfile" type="button" class="btn_default">삭제하기</button>
                                                        </div>
													</td>
												</tr>
                                                <?
                                                }
                                                ?>
                                                <?
                                                if ($myInfo['user_type'] != 'P') {
                                                ?>
												<tr>
													<th>닉네임</th>
													<td>
														<p class="fix_info"><?=$myInfo['nickname']?></p>
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
																<input type="text" id="birthday" name="birthday" onkeyup="inputOnlyNumber(this)" maxlength="8" placeholder="생년월일을 입력해주세요." value="<?=$myInfo['birthday']?>" >
															</div>
															<p class="txt_summary">* 숫자만 입력해 주세요. ex)19801022</p>
														</div>
													</td>
												</tr>
												<tr>
													<th>성별</th>
													<td>
														<div class="choice_area">
															<input type="radio" id="male" name="gender" value="M" <?=($myInfo['gender'] == 'M') ? 'checked' : ''?>>
															<label for="male">남성</label>
															<input type="radio" id="female" name="gender" value="F" <?=($myInfo['gender'] == 'F') ? 'checked' : ''?>>
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
                                                                    <option value="<?=$v?>" <?=($myInfo['sido'] == $v) ? 'selected' : '' ?>><?=$v?></option>
                                                                    <?
                                                                    }
                                                                    ?>
                                                                </select>
															</div>
															<div class="input_box" style="width:188px;">
																<input type="text" id="gugun" name="gugun" placeholder="시/구/군 입력" value="<?=$myInfo['gugun']?>" >
															</div>
														</div>
													</td>
												</tr>

                                                
                                                <input type="hidden" id="pre_namecard" name="pre_namecard" value="<?=$myInfo['namecard']?>" />
                                                <input type="hidden" id="pre_namecard_s" name="pre_namecard_s" value="<?=$myInfo['namecard_s']?>" />
                                                <?
                                                if ($myInfo['user_type'] == 'P') {
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
                                                                    <option value="<?=$v['broker_id']?>" <?=($myInfo['broker_id'] == $v['broker_id']) ? 'selected' : '' ?>><?=$v['company'];?></option>
                                                                    <? } ?>
                                                                </select>
                                                            </div>
                                                            <!--div class="custom_selectbox" style="margin-right:10px; width:188px;">
                                                                <label for="sido2">지역 선택</label>
                                                                <select id="sido2" name="sido2">
                                                                    <option value="" selected="selected">지역 선택</option>
                                                                    <? foreach ($areas as $v) { ?>
                                                                    <option value="<?=$v?>" <?=($myInfo['sido2'] == $v) ? 'selected' : '' ?>><?=$v?></option>
                                                                    <? } ?>
                                                                </select>
                                                            </div-->
                                                            <!--div class="input_box" style="margin-right:10px; width:188px;">
                                                                <input type="text" id="gugun2" name="gugun2" placeholder="시/구/군 입력" value="<?php if(!empty($myInfo['gugun2'])) echo htmlspecialchars($myInfo['gugun2']) ?>" onClick="execDaumPostCode();" readonly />
                                                            </div-->
                                                            <div class="input_box" style="width:188px;">
                                                                <input type="text" id="part" name="part" placeholder="지점 입력" value="<?php if(!empty($myInfo['part'])) echo htmlspecialchars($myInfo['part']) ?>" />
                                                            </div>
                                                        </div>

														<div class="input_box" style="margin-top:10px; width:388px;">
															<input type="text" id="sido2" name="sido2" placeholder="주소입력" value="<?php if(!empty($myInfo['sido2'])) echo htmlspecialchars($myInfo['sido2']) ?>" onclick="sample4_execDaumPostcode()" readonly />
														</div>
                                                        <div class="input_box" style="margin-top:10px; width:788px;">
                                                            <input type="text" id="addr" name="addr" placeholder="상세주소 입력" style="padding:0 1.5%; width:97%;" value="<?php if(!empty($myInfo['addr'])) echo htmlspecialchars($myInfo['addr']) ?>" />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="photo">
                                                    <th>명함이미지</th>
                                                    <td>
                                                        <div class="wrapping photo namecard">
                                                            <div class="photo_box"><img id="my_namecard" style='width:220px;height:121px' src="<?=$myInfo['namecard']?>" /></div>
                                                            <input type="file" id="namecard" name="namecard" data-url="/settings/upload_namecard" />
                                                            <label for="namecard" class="btn_default">사진첨부</label>
                                                            <button type="button" id="delNamecard" class="btn_default">삭제하기</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <? } ?>
											</tbody>
										</table>
									</div>
								<div class="group">
									<h3>정보수신동의</h3>
									<div class="agree_box">
										<div class="agree01">
											<input type="checkbox" name="alarm_feeds" id="agree01" <?=($myInfo['alarm_feeds'] == 1) ? 'checked' : ''?>>
											<label for="agree01">관심 전략과 관심 포트폴리오 관련 정보를 수신 동의합니다.</label>
										</div>
										<div class="agree02">
											<input type="checkbox" name="alarm_all" id="agree02" <?=($myInfo['alarm_all'] == 1) ? 'checked' : ''?>>
											<label for="agree02">전략 및 정보성 알림에 수신 동의합니다.</label>
										</div>
									</div>
								</div>
								<div class="btn_area">
									<a href="/mypage/subscribe" class="btn_common_gray btn_cancel">취소</a>
									<button type="submit" class="btn_common_red btn_complete">수정완료</button>
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


<div id="daum_layer" style="display:none;position:fixed;overflow:hidden;z-index:9999;-webkit-overflow-scrolling:touch;">
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
