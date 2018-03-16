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
								<li><a href="/partent">특허</a></li>
								<li class="curr"><a href="javascript:;">오시는길</a></li>
							</ul>
						</nav>
					</div>
					<div class="content contact_w">
						<div class="map_a" id="map">
							지도 영역 (988px * 498px)
						</div>

						<script type="text/javascript" src="//apis.daum.net/maps/maps3.js?apikey=cd44ac53b2bec0d09a770a5e46f9c94d"></script>
						<script>
						var mapContainer = document.getElementById('map'), // 지도를 표시할 div 
							mapOption = { 
								center: new daum.maps.LatLng(37.5058742, 127.0522021), // 지도의 중심좌표
								level: 3 // 지도의 확대 레벨
							};

						var map = new daum.maps.Map(mapContainer, mapOption); // 지도를 생성합니다

						// 마커가 표시될 위치입니다 
						var markerPosition  = new daum.maps.LatLng(37.5058742, 127.0522021); 

						// 마커를 생성합니다
						var marker = new daum.maps.Marker({
							position: markerPosition
						});

						// 마커가 지도 위에 표시되도록 설정합니다
						marker.setMap(map);

						// 아래 코드는 지도 위의 마커를 제거하는 코드입니다 
						</script>

						<div class="txt_summary">
							<h3 class="title n_squere">주소</h3>
							<p class="address">서울시 강남구 테헤란로 419, 15층 1524호 (삼성동, 파이낸스 플라자)</p>
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
