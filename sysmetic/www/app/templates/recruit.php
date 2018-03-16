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
								<li class="curr"><a href="javascript:;">채용정보</a></li>
								<li><a href="/partent">특허</a></li>
								<li><a href="/contact">오시는길</a></li>
							</ul>
						</nav>
					</div>
					<div class="content recruit">
						<div class="sector">
							<h3 class="title n_squere">모집부문</h3>
							<div class="detail">
								<ul>
									<li>
										<p class="subject">웹서비스 개발 및 운영(경력 정규직,3개월 계약직 후)</p>
										<p class="summary">- 경력 3년 이상(PHP, Mysql, ASP.NET, Mssql)</p>
									</li>
									<li>
										<p class="subject">기획 & 마케팅 & 영업(신입 정규직, 3개월 계약직 후)</p>
										<p class="summary">
											- 컨텐츠 관리 및 운영<br />
											- 서비스 기획 및 제휴사 관리
										</p>
									</li>
								</ul>
							</div>
							<img src="../images/sub/img_recruit_sysme.gif" alt="" class="img" />
						</div>
						<div class="normal">
							<h3 class="title n_squere">기업문화</h3>
							<p class="summary">
								아주 자유롭고 편한분위기로 근무 가능합니다.<br />
								출근지역은 여의도이며 기상악화시 재택근무도 가능합니다.
							</p>
						</div>
						<div class="normal">
							<h3 class="title n_squere">복지혜택</h3>
							<p class="summary">
								보험, 의료: 4대 보험 / 근무 형태: 자율 출근 / 연차, 휴가: 자율 휴가제 / 개인 장비: 고사양노트북제공/듀얼모니터
							</p>
						</div>
						<div class="normal">
							<div class="left">
								<h3 class="title n_squere">급여</h3>
								<p class="summary">
									협의 후 결정
								</p>
							</div>
							<div class="left">
								<h3 class="title n_squere">입사 시점</h3>
								<p class="summary">
									수시
								</p>
							</div>
						</div>
						<div class="normal">
							<h3 class="title n_squere">지원 방법</h3>
							<p class="summary">
								이력서와 자기소개서를 이메일 <a href="mailto:ceo@sysmetic.co.kr" class="email">ceo@sysmetic.co.kr</a> 제출(자유양식)
							</p>
						</div>
						<div class="normal last">
							<h3 class="title n_squere">비전</h3>
							<p class="summary">
								2015년 6월 설립한 회사로 빅데이터에 기반한 "투자자와 브로커 매칭플랫폼"이라는 사업모델로 서비스를 시작하여 베타서비스를 운영 중에 있습니다.<br />
								과정에서 금융기관에 컨텐츠공급 계약을 체결하였고, 핀테크업체를 대표하여 코스콤주관 OPEN API TF에도 참여하였습니다. <br />
								최근에는 온/오프라인 교육을 왕성하게 진행하여 금융투자 전문인력 양성에도 기여하고 있습니다.
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
