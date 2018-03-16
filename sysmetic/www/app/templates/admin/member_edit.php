<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 브로커 신청 정보</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />
	<script src="/script/jquery-1.10.1.min.js"></script>
	<script src="/script/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="/script/html5shiv.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script src="/script/common.js"></script>
	<script src="/script/jquery.iframe-transport.js"></script>
	<script src="/script/jquery.fileupload.js"></script>
	<script>
	$(function(){
		$('#form_modify').submit(function(){

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

	<?php require_once('header.php') ?>
    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            
            <h5 style="margin:0 0 30px 0;">회원정보</h5> 
            
			<form action="/admin/users/modify" method="post" id="form_modify">
            <input type="hidden" name="uid" value="<?=$uInfo['uid']?>" />
            <p class="sub_title default">필수정보입력</p>
            <div class="user_info view">
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:100%;">
                    <colgroup>
                        <col style="width:20%">
                        <col style="width:*">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td class="thead">회원구분</td>
                            <td>
                                <p class="fix_info">
                                <?
                                switch ($uInfo['user_type']) {
                                    case 'T': echo '트레이더'; break;
                                    case 'P': echo 'PB'; break;
                                    case 'N': echo '일반회원'; break;
                                    case 'A': echo '관리자'; break;
                                }
                                ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="thead">이름</td>
                            <td>
                                <p class="fix_info"><?=$uInfo['name']?></p>
                            </td>
                        </tr>
                        <tr class="high">
                            <td class="thead">이메일</td>
                            <td>
                                <p class="fix_info"><?=$uInfo['email']?></p>
                                <p class="txt_summary">* 사이트 이용 시 아이디로 사용됩니다.</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="thead">비밀번호</td>
                            <td>
                                <div class="wrapping">
                                    <div class="input_box" style="margin-right:24px; width:298px;">
                                        <input type="password" id="password" name="password" placeholder="비밀번호를 입력해주세요.">
                                    </div>
                                    <p class="txt_summary">* 비밀번호는 문자, 숫자포함 6~20자로 되어야 합니다.</p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="thead">비밀번호 확인</td>
                            <td>
                                <div class="wrapping">
                                    <div class="input_box" style="width:298px;">
                                        <input type="password" id="password_confirm" name="password_confirm" placeholder="비밀번호를 다시 입력해주세요.">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="thead">휴대폰번호</td>
                            <td>
                                <div class="wrapping">
                                    <div class="input_box" style="margin-right:24px; width:298px;">
                                        <input type="text" id="mobile" name="mobile" placeholder="휴대폰번호를 입력해주세요." onkeyup="inputOnlyNumber(this)" maxlength="12" value="<?=$uInfo['mobile']?>">
                                    </div>
                                    <p class="txt_summary">* - 없이 숫자만 입력해 주세요.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="sub_title add">추가정보</p>
            <div class="user_info view">

                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:100%;">
                    <colgroup>
                        <col style="width:20%">
                        <col style="width:*">
                    </colgroup>
                    <tbody>
                        
                        <?
                        if ($uInfo['user_type'] == 'T' || $uInfo['user_type'] == 'P') {
                        ?>
                        <tr class="photo">
                            <td class="thead">프로필이미지</td>
                            <td>
                                <div class="wrapping photo">
                                    <input type="hidden" id="pre_profile" name="pre_profile" value="<?=$uInfo['picture']?>" />
                                    <input type="hidden" id="pre_profile_s" name="pre_profile_s" value="<?=$uInfo['picture_s']?>" />
                                    <div class="photo_box"><img id="my_pic" style='width:120px;height:121px' src="<?=$uInfo['picture']?>" /></div>
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
                        if ($uInfo['user_type'] != 'P') {
                        ?>
                        <tr>
                            <td class="thead">닉네임</td>
                            <td>
                                <div class="input_box" style="margin-right:10px; width:298px;">
                                    <input type="text" id="nickname" name="nickname" maxlength="8" placeholder="닉네임" value="<?=$uInfo['nickname']?>" >
                                </div>
                            </td>
                        </tr>
                        <?
                        }
                        ?>

                        <tr>
                            <td class="thead">생년월일</td>
                            <td>
                                <div class="wrapping">
                                    <div class="input_box" style="margin-right:10px; width:298px;">
                                        <input type="text" id="birthday" name="birthday" onkeyup="inputOnlyNumber(this)" maxlength="8" placeholder="생년월일을 입력해주세요." value="<?=$uInfo['birthday']?>" >
                                    </div>
                                    <p class="txt_summary">* 숫자만 입력해 주세요. ex)19801022</p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="thead">성별</td>
                            <td>
                                <div class="choice_area">
                                    <input type="radio" id="male" name="gender" value="M" <?=($uInfo['gender'] == 'M') ? 'checked' : ''?>>
                                    <label for="male">남성</label>
                                    <input type="radio" id="female" name="gender" value="F" <?=($uInfo['gender'] == 'F') ? 'checked' : ''?>>
                                    <label for="female">여성</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="thead">지역</td>
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
                                            <option value="<?=$v?>" <?=($uInfo['sido'] == $v) ? 'selected' : '' ?>><?=$v?></option>
                                            <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input_box" style="width:188px;">
                                        <input type="text" id="gugun" name="gugun" placeholder="시/구/군 입력" value="<?=$uInfo['gugun']?>" >
                                    </div>
                                </div>
                            </td>
                        </tr>

                        
                        <?
                        if ($uInfo['user_type'] == 'P') {
                        ?>
                        <tr class="more_high">
                            <td class="thead">지점 (pb)</td>
                            <td>
                                <div class="wrapping">
                                    <div class="custom_selectbox" style="margin-right:10px;">
                                        <label for="brand_choice">증권사 선택</label>
                                        <select id="brand_choice" name="broker_id">
                                            <option value="" selected="selected">증권사 선택</option>
                                            <? foreach ($brokers as $k => $v) { ?>
                                            <option value="<?=$v['broker_id']?>" <?=($uInfo['broker_id'] == $v['broker_id']) ? 'selected' : '' ?>><?=$v['company'];?></option>
                                            <? } ?>
                                        </select>
                                    </div>
                                    <!-- <div class="custom_selectbox" style="margin-right:10px;">
                                        <label for="sido2">지역 선택</label>
                                        <select id="sido2" name="sido2">
                                            <option value="" selected="selected">지역 선택</option>
                                            <? foreach ($areas as $v) { ?>
                                            <option value="<?=$v?>" <?=($uInfo['sido2'] == $v) ? 'selected' : '' ?>><?=$v?></option>
                                            <? } ?>
                                        </select>
                                    </div> -->
                                    <div class="input_box" style="width:188px;">
                                        <input type="text" id="part" name="part" placeholder="지점 입력" value="<?php if(!empty($uInfo['part'])) echo htmlspecialchars($uInfo['part']) ?>" />
                                    </div>
                                    <!-- <div class="input_box" style="margin-right:10px;width:188px;">
                                        <input type="text" id="gugun2" name="gugun2" placeholder="시/구/군 입력" value="<?php if(!empty($uInfo['gugun2'])) echo htmlspecialchars($uInfo['gugun2']) ?>" />
                                    </div> -->
                                </div>

								<div class="input_box" style="margin-top:10px; width:388px;">
									<input type="text" id="sido2" name="sido2" placeholder="주소입력" value="<?php if(!empty($uInfo['sido2'])) echo htmlspecialchars($uInfo['sido2']) ?>" onClick="execDaumPostCode();" readonly />
								</div>
								<div class="input_box" style="margin-top:10px; width:788px;">
									<input type="text" id="addr" name="addr" placeholder="상세주소 입력" value="<?php if(!empty($uInfo['addr'])) echo htmlspecialchars($uInfo['addr']) ?>" />
								</div>

                                <!-- <div class="input_box" style="margin-top:10px; width:388px;">
                                    <input type="text" id="addr" name="addr" placeholder="구/군 이하 상세주소 입력"  value="<?php if(!empty($uInfo['addr'])) echo htmlspecialchars($uInfo['addr']) ?>" />
                                </div> -->
                            </td>
                        </tr>
                        <tr class="photo">
                            <td class="thead">명함이미지 (pb)</td>
                            <td>
                                <div class="wrapping photo namecard">
                                    <input type="hidden" id="pre_namecard" name="pre_namecard" value="<?=$uInfo['namecard']?>" />
                                    <input type="hidden" id="pre_namecard_s" name="pre_namecard_s" value="<?=$uInfo['namecard_s']?>" />
                                    <div class="photo_box"><img id="my_namecard" style='width:220px;height:121px' src="<?=$uInfo['namecard']?>" /></div>
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

            <p class="btn_board">
                <button type="submit" title="수정" class="submit"><span class="ir">수정</span></button>
                <button type="button" title="취소" class="cancel" onclick="location.href='/admin/users'"><span class="ir">취소</span></button>
            </p>
            
            </form>

        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>


    <!-- 우편번호 찾기 -->
	<div id="daum_layer" style="display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch;">
	<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
	</div>

    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
    <script>
    // 우편번호 찾기 화면을 넣을 element
    var element_layer = document.getElementById('daum_layer');

    function closeDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_layer.style.display = 'none';
    }

    function execDaumPostCode() {
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
            },
            width : '100%',
            height : '100%'
        }).embed(element_layer);

        // iframe을 넣은 element를 보이게 한다.
        element_layer.style.display = 'block';

        // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
        initLayerPosition();
    }

    // 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
    // resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
    // 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
    function initLayerPosition(){
        var width = 300; //우편번호 서비스가 들어갈 element의 width
        var height = 460; //우편번호 서비스가 들어갈 element의 height
        var borderWidth = 5; //샘플에서 사용하는 border의 두께

        // 위에서 선언한 값들을 실제 element에 넣는다.
        element_layer.style.width = width + 'px';
        element_layer.style.height = height + 'px';
        element_layer.style.border = borderWidth + 'px solid';
        // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
        element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
        element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
    }
    </script>

