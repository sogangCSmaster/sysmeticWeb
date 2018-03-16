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
								<li class="curr"><a href="javascript:;">회사연혁</a></li>
								<li><a href="/business_area">사업영역</a></li>
								<li><a href="/network">네트워크</a></li>
								<li><a href="/recruit">채용정보</a></li>
								<li><a href="/partent">특허</a></li>
								<li><a href="/contact">오시는길</a></li>
							</ul>
						</nav>
					</div>
					<div class="content history_w">
						<ul>
							<li class="y2017">
								<div class="box">
									<div class="step">
										<dl>
											<dt class="n_squere">STEP4</dt>
											<dd class="n_squere">서비스 고도화</dd>
										</dl>
									</div>
									<div class="detail">
										<dl>
											<dt class="n_squere">2017</dt>
											<dd>
												<ul>
													<li>
														<strong class="month">04</strong>
														<p>시스메틱 PB &amp; 투자자 매칭 플랫폼 개발 완료</p>
													</li>
													<li>
														<strong class="month">03</strong>
														<p>종목뉴스 빅데이터 분석 서비스 오픈</p>
													</li>
												</ul>
											</dd>
										</dl>
									</div>
								</div>
							</li>
							<li class="y2016">
								<div class="box">
									<div class="step">
										<dl>
											<dt class="n_squere">STEP3</dt>
											<dd class="n_squere">시장경쟁력 강화</dd>
										</dl>
									</div>
									<div class="detail">
										<dl>
											<dt class="n_squere">2016</dt>
											<dd>
												<ul>
													<li>
														<strong class="month">08</strong>
														<p>퍼넥스 데이터 시각화 종목 빅데이터분석 업무협약</p>
													</li>
													<li>
														<strong class="month">08</strong>
														<p>위버풀 뉴스검색 API제공 서비스 업무협약</p>
													</li>
													<li>
														<strong class="month">06</strong>
														<p>현대선물 시스메틱 계좌 통계분석 웹 서비스 제작</p>
													</li>
													<li>
														<strong class="month">04</strong>
														<p>유진선물금융 컨텐츠 관한 포괄적 업무협약</p>
													</li>
												</ul>
											</dd>
										</dl>
									</div>
								</div>
							</li>
							<li class="y2015_2">
								<div class="box">
									<div class="step">
										<dl>
											<dt class="n_squere">STEP2</dt>
											<dd class="n_squere">서비스개발</dd>
										</dl>
									</div>
									<div class="detail">
										<dl>
											<dt class="n_squere">2015</dt>
											<dd>
												<ul>
													<li>
														<strong class="month">12</strong>
														<p>서비스오픈 / 특허출원</p>
													</li>
													<li>
														<strong class="month">12</strong>
														<p>코스콤 자본시장 증권 공동망 API구축 Testbed T/F팀 참여</p>
													</li>
													<li>
														<strong class="month">11</strong>
														<p>온라인 동영상 지식플랫폼 airKlass 온라인교육부분 공동제휴 </p>
													</li>
												</ul>
											</dd>
										</dl>
									</div>
								</div>
							</li>
							<li class="y2015">
								<div class="box">
									<div class="step">
										<dl>
											<dt class="n_squere">STEP1</dt>
											<dd class="n_squere">회사발전기틀 마련 </dd>
										</dl>
									</div>
									<div class="detail">
										<dl>
											<dt class="n_squere">2015</dt>
											<dd>
												<ul>
													<li>
														<strong class="month">10</strong>
														<p>현대선물 금융 컨텐츠 관한 포괄적 업무제휴</p>
													</li>
													<li>
														<strong class="month">09</strong>
														<p>한국영상대학교 온라인 증권 교육 컨텐츠 제작에 관한 산학 협약</p>
													</li>
													<li>
														<strong class="month">06</strong>
														<p>시스메틱 설립</p>
													</li>
												</ul>
											</dd>
										</dl>
									</div>
								</div>
							</li>
						</ul>
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