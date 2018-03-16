<!doctype html>
<html lang="ko">
<head>
	<title>회사소개</title>
	<? require_once $skinDir."common/head.php" ?>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">
		<!-- header -->
        <? require_once $skinDir."common/header.php" ?>
		<!-- header -->
		<!-- container -->
		<div class="container">
			<section class="area intro_w">	
				<div class="cont_a">
					<div class="head">
						<div class="top_a">
							<h2 class="tit n_squere">회사소개</h2>
							<div class="infos">
								<dl class="info01 email">
									<dt>제휴 문의</dt>
									<dd><a href="mailto:contact@sysmetic.co.kr">contact@sysmetic.co.kr</a></dd>
								</dl>
								<dl class="info02 email">
									<dt>일반 문의</dt>
									<dd><a href="mailto:help@sysmetic.co.kr">help@sysmetic.co.kr</a></dd>
								</dl>
								<dl class="info03">
									<dt>전화</dt>
									<dd>02-6338-1880</dd>
								</dl>
								<dl class="info04">
									<dt>팩스</dt>
									<dd>02-6348-1880</dd>
								</dl>
							</div>
						</div>
						<nav class="cs_nav">
							<ul>
								<li><a href="/intro">시스메틱은?</a></li>
								<li><a href="/history">회사연혁</a></li>
								<li><a href="/business_area">사업영역</a></li>
								<li><a href="/network">네트워크</a></li>
								<li><a href="/recruit">채용정보</a></li>
								<li class="curr"><a href="javascript:;">특허</a></li>
								<li><a href="/contact">오시는길</a></li>
							</ul>
						</nav>
					</div>
					<div class="content partent_w">
						<div class="pic"><img src="../images/sub/img_partent.jpg" alt="특허증" /></div>
						<div class="detail">
							<p class="subject n_squere">특허 제 10-1679236 호 취득</p>
							<p class="summary_top">
								금융투자를 위한 매칭 플랫폼 제공 시스템<br />
								총 12개의 청구항 항목에 대한 특허 취득
							</p>
							<p class="summary_bottom">
								온라인(웹,모바일) 기반에서의 금융상품 투자 매칭에 대한 포괄적이며 세부적인 <br />
								특허 취득을 통해  라이선스 확보는 물론, 향후 경쟁사 제재, 네트워크와의 <br />
								협업관계에서 우위를 점하게 되었습니다.
							</p>
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
