<?php
$type = (!empty($flash['user_type'])) ? htmlspecialchars($flash['user_type']) : $type;
?>
<!doctype html>
<html lang="ko">
<head>
	<title>실명확인 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
	<script src="/script/jquery.iframe-transport.js"></script>
	<script src="/script/jquery.fileupload.js"></script>
	<script>alert('실명인증이 필요합니다. 실명인증정보를 입력해주세요')</script>
	<script>
	$(function(){
		$('#form_signup').submit(function(){

			if(!$('#name').val()){
                alert('이름을 입력해주세요.');
				$('#name').focus();
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

			return true;
		});

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
					<h2 class="page_title n_squere">실명확인</h2>
					<p class="page_summary">실명인증이 필요합니다.</p>
				</div>
				<div class="content_area step03">

                    <form id="reqCBAForm" name="reqCBAForm" method="post" action = "" onsubmit="return openPCCWindow()">
                        <input type="hidden" name="reqInfo" value = "<?=$enc_reqInfo?>">
                        <input type="hidden" name="retUrl" value = "<?=$retUrl?>">
                    </form>

			        <form action="/member/mem_update" method="post" id="form_signup">
                        <input type="hidden" id="email" name="email" value="<?=$email?>" />
						<fieldset>
							<div class="group"><br><br>
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
														<input type="text" id="name" name="name" placeholder="실명인증 버튼을 눌러주세요" value="" onclick="$('#reqCBAForm').submit();" readonly />
													</div>
													<button type="button" class="btn_default" onclick="$('#reqCBAForm').submit();">실명인증</button>
												</div>
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

							<div class="btn_area">
								<a href="javascript:;history.back();" class="btn_common_gray btn_cancel">취소</a>
								<button type="submit" class="btn_common_red btn_next_step">실명확인완료</button>
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
