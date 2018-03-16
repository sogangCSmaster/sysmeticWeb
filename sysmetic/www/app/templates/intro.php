<!doctype html>
<html lang="ko">
<head>
	<title>회사소개</title>
	<? require_once $skinDir."common/head.php" ?>
    <script>
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
								<li class="curr"><a href="javascript:;">시스메틱은?</a></li>
								<li><a href="/history">회사연혁</a></li>
								<li><a href="/business_area">사업영역</a></li>
								<li><a href="/network">네트워크</a></li>
								<li><a href="/recruit">채용정보</a></li>
								<li><a href="/partent">특허</a></li>
								<li><a href="/contact">오시는길</a></li>
							</ul>
						</nav>
					</div>
					<div class="content about">
						<p class="summary">
							<img src="../images/sub/img_about_title.jpg" alt="당신만의 투자전문가를 고용할수 있습니다. | 또한 당신의 모든 자산은 실시간으로 국내 최고의 전문가들에게 관리 받게 될것입니다." />
							2015년 6월 설립한 회사로 빅데이터에 기반한 "투자자와 브로커 매칭플랫폼"이라는 사업모델로 서비스를 시작하였습니다. 
							시스메틱은 금융상품 투자자와 상품운용자, 투자상담자(PB) 들의 3자간 매칭 플랫폼을 통해 투자자들에게는 적합한 금융상품을, 운용자들에게는 최적의 상품개발을,  중개자들에게는 훌륭한 영업 솔루션을 제공합니다.<br /><br />
							시스메틱은 이 3자간 매칭을 통해 금융기관과 개인투자자간의 정보 비대칭을 해소하고 전문 투자도구가 부재한 개인투자가들에게 새로운 TOOL을 제공하고,
							중개자들에게는 새로운 영업 환경과 기회를 제공함으로서 모두가 윈윈 하는 플랫폼을 완성하는데 그 목표를 두고 있습니다.<br /><br />
							시스메틱에 제공되는 모든 운용성과는 PB들의 검증을통한 리얼!! 이며 PB들에게 새로운 투자상품을 추천받고 통합 자산관리 서비스를 받게 됩니다.또한 온/오프라인 교육을 왕성하게 진행하여 금융투자 전문인력 양성에도 기여하고 있습니다.
						</p>
						<table class="info_tbl">
							<colgroup>
								<col style="width:11%;" />
								<col style="width:39%;" />
								<col style="width:11%;" />
								<col style="width:39%;" />
							</colgroup>
							<tbody>
								<tr>
									<th>회사명</th>
									<td>(주) 시스메틱</td>
									<th>사업자번호</th>
									<td>711-86-00050</td>
								</tr>
								<tr>
									<th>대표이사</th>
									<td>박혜정, 고성엽</td>
									<th>설립일</th>
									<td>2015년 6월 11일</td>
								</tr>
								<tr>
									<th>자본금</th>
									<td>1.36억</td>
									<th>직원수</th>
									<td>5명</td>
								</tr>
								<tr>
									<th>업종</th>
									<td colspan="3">소프트웨어 및 컨텐츠 개발 공급 ,On/Off 교육 서비스 , 정보서비스업</td>
								</tr>
								<tr>
									<th>주소</th>
									<td colspan="3">서울시 강남구 테헤란로 419, 15층 1524호 (삼성동, 파이낸스 플라자)</td>
								</tr>
								<tr>
									<th>연락처</th>
									<td colspan="3">Tel : 02-6338-1880 / Fax : 02-6348-1880 / E-mail : contact@sysmetic.co.kr</td>
								</tr>
							</tbody>
						</table>
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