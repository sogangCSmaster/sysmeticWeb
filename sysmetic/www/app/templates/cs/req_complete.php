<!doctype html>
<html lang="ko">
<head>
	<title>고객센터 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
    $(function() {
        $('#regFrm').on('submit', function() {
            if (!$.trim($('#subject').val())) {
                alert('제목을 입력해주세요');
                $('#subject').focus();
                return false;
            } else if (!$.trim($('#email').val())) {
                alert('이메일주소를 입력해주세요');
                $('#email').focus();
                return false;
            } else if (!$.trim($('#mobile').val())) {
                alert('휴대전화번호를 입력해주세요');
                $('#mobile').focus();
                return false;
            } else if (!$.trim($('#contents').val())) {
                alert('내용을 입력해주세요');
                $('#contents').focus();
                return false;
            } else {
                if (!confirm('저장하시겠습니까?')) {
                    return false;
                }
            }
        });
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


			<section class="area cs_w">
				<div class="cont_a">
                
                    <? require_once $skinDir."cs/sub_menu.php" ?>

					<div class="content cs_complete">
						<p class="txt_title">
							<img src="/images/sub/txt_counsel_complete.gif" alt="상담내용이 등록완료 되었습니다.">
						</p>
						<p class="txt_info">
							담당자가 내용 확인 후 답변을 등록하며, 상담내용 및 답변은 <br />
							<strong class="bold">마이페이지 &gt; <span class="mark">고객센터</span></strong> 상담내역에서 확인 가능합니다.
						</p>
						<div class="btn_area">
							<a href="/mypage/customer" class="btn_common_gray btn_mypage">마이페이지 가기</a>
						</div>
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

</body>

<script>

</script>
</html>
