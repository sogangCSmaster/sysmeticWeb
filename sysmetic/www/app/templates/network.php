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
								<li class="curr"><a href="javascript:;">네트워크</a></li>
								<li><a href="/recruit">채용정보</a></li>
								<li><a href="/partent">특허</a></li>
								<li><a href="/contact">오시는길</a></li>
							</ul>
						</nav>
					</div>
					<div class="content network">
						<!--ul>
							<li class="left"><a href="javascript:;"><img src="../images/sub/img_network_hana.gif" alt="하나대투증권" /></a></li>
							<li><a href="javascript:;"><img src="../images/sub/img_network_shinhan.gif" alt="신한금융투자" /></a></li>
							<li><a href="javascript:;"><img src="../images/sub/img_network_hi.gif" alt="하이투자증권" /></a></li>
							<li class="left"><a href="javascript:;"><img src="../images/sub/img_network_nh.gif" alt="NH투자증권" /></a></li>
							<li><a href="javascript:;"><img src="../images/sub/img_network_kyobo.gif" alt="교보증권" /></a></li>
							<li><a href="javascript:;"><img src="../images/sub/img_network_hankook.gif" alt="한국투자증권" /></a></li>
							<li class="left"><a href="javascript:;"><img src="../images/sub/img_network_meritz.gif" alt="메리츠종금증권" /></a></li>
							<li><a href="javascript:;"><img src="../images/sub/img_network_uanta.gif" alt="유안타증권" /></a></li>
							<li><a href="javascript:;"><img src="../images/sub/img_network_sk.gif" alt="SK증권" /></a></li>
						</ul-->
<div style="text-align:center;padding:50px;">준비중입니다. </div>
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
