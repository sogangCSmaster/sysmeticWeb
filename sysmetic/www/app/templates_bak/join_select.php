<!doctype html>
<html lang="ko">
<head>
	<title>회원가입 | SYSMETIC</title>
	<? require_once "common_head.php" ?>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once "common_header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container join">
			<section class="area">
				<div class="page_title_area">
					<h2 class="page_title n_squere">회원가입</h2>
					<p class="page_summary">시스메틱 홈페이지를 찾아주셔서 감사합니다. 약관에 동의하셔야 가입이 가능합니다.</p>
				</div>
				<div class="content_area step01">
					<form id="joinSelectFrm" action="/agree" method="get">
                    <input type="hidden" id="platform" name="platform" value="<?php echo htmlspecialchars($platform) ?>" />
						<fieldset>
							<legend class="screen_out">회원타입 선택</legend>
							<div class="step_view">01.회원타입 선택</div>
							<h3 class="cont_title n_squere">회원유형 선택</h3>
							<p class="cont_summary">회원가입하시고자하는 회원유형을 선택해주세요.</p>
							<div class="choice_type">
								<input type="radio" id="member_type01" name="type" value="N" <?=($type=='N') ? 'checked': '';?> />
								<label for="member_type01"><span>01.</span>일반 회원 (투자가)</label>
								<input type="radio" id="member_type02" name="type" value="T" <?=($type=='T') ? 'checked': '';?> />
								<label for="member_type02" class="center"><span>02.</span>트레이더 회원</label>
								<input type="radio" id="member_type03" name="type" value="P" <?=($type=='P') ? 'checked': '';?> />
								<label for="member_type03"><span>03.</span>PB 회원 (증권사 직원)</label>
							</div>
							<div class="member_type_cont">
								<div class="type type_N">
									<h3 class="cont_title n_squere">일반회원으로 가입 시,</h3>
									<ul>
										<li>PB 및 트레이더에게 상품에 대해 문의할 수 있습니다.</li>
										<li>관심 전략 레코드를 메일로 받아볼 수 있습니다.</li>
									</ul>
								</div>
								<div class="type type_T">
									<h3 class="cont_title n_squere">트레이더회원으로 가입 시,</h3>
									<ul>
										<li>시스메틱에 운용중인 전략을 등록할 수 있습니다.</li>
										<li>PB를 통해 전략을 금융상품화 할 수 있습니다.</li>
									</ul>
								</div>
								<div class="type type_P">
									<h3 class="cont_title n_squere">PB회원으로 가입 시,</h3>
									<ul>
										<li>개인 라운지를 통해 전략과 상품을 홍보할 수 있습니다.</li>
										<li>상담기능을 이용해 새로운 고객을 만날 수 있습니다.</li>
									</ul>
								</div>
							</div>
							
							<div class="btn_area one">
								<button type="button" class="btn_common_red btn_join" onclick="goNext();">일반회원으로 가입하기</button>
							</div>
						</fieldset>
					</form>					
				</div>
			</section>
		</div>
		<!-- //container -->
        
        <!-- footer -->
		<? require_once "common_footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->

<script>
	//회원 유형 선택
	$('.choice_type input[name="type"]').change(function(){
		var idx = $('.choice_type input[name="type"]').index(this);
		$('.member_type_cont .type').removeClass('show').eq(idx).addClass('show');
		if(idx === 0){
			$('.btn_join').text('일반회원으로 가입하기');
		}else if(idx === 1){
			$('.btn_join').text('트레이더회원으로 가입하기');
		}else{
			$('.btn_join').text('PB회원으로 가입하기');
		}
	});

    function goNext() {
        $('#joinSelectFrm').submit();
    }

    $(document).ready(function(){
        var type = '<?=$type?>';
		$('.member_type_cont .type_'+type).addClass('show');

		if(type === 'N'){
			$('.btn_join').text('일반회원으로 가입하기');
		}else if(type === 'T'){
			$('.btn_join').text('트레이더회원으로 가입하기');
		}else{
			$('.btn_join').text('PB회원으로 가입하기');
		}
    });
</script>
</body>
</html>