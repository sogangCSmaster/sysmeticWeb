<!doctype html>
<html lang="ko">
<head>
    <title>투자하기 | SYSMETIC</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta property="og:type" content='website' />
	<meta property="og:title" content="SYSMETIC">
	<meta property='og:site_name' content='SYSMETIC' />
	<meta property="og:title" content='SYSMETIC' />
	<meta property="og:description" content='SYSMETIC' />
	<link rel="stylesheet" href="/css/common2.css?<?=time()?>">

</head>
<body>
    <!-- wrapper -->
    <div class="wrapper">

        <!-- header -->
<!----- GA ----->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-65985931-1', 'auto');
ga('send', 'pageview');
</script>
<!----- GA ----->

		<!-- header -->
		<header class="header">
			<div class="area">
				<h1 class="header_logo"><a href="/" target="_top"><img src="/images/common/img_header_logo.png" alt="SYSMETIC" /></a></h1>
				<div class="cont_box">
                <!------ //로그인 전 ------->
				    <?php if(!$isLoggedIn()){ ?>
					<div class="member before_login">
					</div>
					<!-- //로그인 전 -->
				    <?php }else{ ?>


					<!-- 로그인 후 -->
					<div class="member after_login">
						<a href="javascript:;bigData();" class="btn_bigdata" target="_top">주식 빅데이터</a>
						<div class="links">
                            <?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin" target="_top">사이트관리</a><?php } ?>

                            <!-- <a href="/admin/strategies">전략관리</a>
                            <a href="/qna">나의 Q&A</a>
                            <a href="/my_answers">나의 Q&A</a> -->

                            <? if($_SESSION['user']['user_type'] == 'P'){ ?>
							<a href="/lounge/<?=$_SESSION['user']['uid']?>" target="_top"><img src="/images/common/ico_header_coffee.png" alt="" /> My Lounge</a>
                            <? } ?>

                            <? if($_SESSION['user']['user_type'] == 'P' || $_SESSION['user']['user_type'] == 'A'){ ?>
							<a href="/pb/bbs" target="_top">PB 게시판</a>
                            <? } ?>

							<a href="/mypage/subscribe" target="_top">마이페이지</a>
							<a href="/logout" target="_top">로그아웃</a>
						</div>
					</div>
					<!-- //로그인 후 -->
				    <?php } ?>

					<!-- gnb -->
					<nav class="gnb">
						<ul>
							<li>
								<a href="/investment" target="_top"><img src="/images/common/txt_gnb01_<?=($topmenu == 'invest') ? 'on' : 'off';?>.png" alt="투자하기" /></a>
								<ul class="sub">
									<li><a href="/investment/strategies" target="_top"><img src="/images/common/txt_gnb0101.png" alt="전략랭킹" /></a></li>
									<li><a href="/investment/developers" target="_top"><img src="/images/common/txt_gnb0102.png" alt="PB/트레이더" /></a></li>
									<li><a href="/investment/search" target="_top"> <img src="/images/common/txt_gnb0103.png" alt="상품검색" /></a></li>
									<li><a href="/investment/portfolios" target="_top"><img src="/images/common/txt_gnb0104.png" alt="전략 포트폴리오" /></a></li>
								</ul>
							</li>
							<li>
								<a href="/fund" target="_top"><img src="/images/common/txt_gnb02_<?=($topmenu == 'fund') ? 'on' : 'off';?>.png" alt="투자받기" /></a>
								<ul class="sub">
									<li><a href="/fund/strategies/write" target="_top"><img src="/images/common/txt_gnb0201.png" alt="전략등록" /></a></li>
								</ul>
							</li>
							<li>
								<a href="/lounge" target="_top"><img src="/images/common/txt_gnb03_<?=($topmenu == 'lounge') ? 'on' : 'off';?>.png" alt="Lounge" /></a>
                            	<? if($_SESSION['user']['user_type'] == 'P'){ ?>
								<ul class="sub">
									<li><a href="/lounge/<?=$_SESSION['user']['uid']?>" target="_top"><img src="/images/common/txt_gnb0301.png" alt="My Lounge" /></a></li>
								</ul>
								<? } ?>
							</li>
						</ul>
					</nav>
					<!-- //gnb -->
				</div>
			</div>
		</header>
		<!-- header -->

        <!-- header -->
	

	<!------<iframe src="/admin_head" width=100% height=120 frameborder=0></iframe>
	 헤더 영역 ------->


	<div id="head" style="margin-top:122px;">
		<div class="sub_menu_n">
            <!------ 관리자 메뉴 // 슈퍼어드민 관리자에게만 노출되는 메뉴 ------->
                <a href="/admin/users" style="font-weight:bold;color:#ffffff">회원</a>
				 &nbsp;|&nbsp;
                <a href="/admin/items" style="font-weight:bold;color:#ffffff">종목</a>
				 &nbsp;|&nbsp;
                <a href="/admin/brokers" style="font-weight:bold;color:#ffffff">증권사</a>
				 &nbsp;|&nbsp;
                <a href="/admin/education" style="font-weight:bold;color:#ffffff">교육</a>
				 &nbsp;|&nbsp;
                <a href="/admin/media" style="font-weight:bold;color:#ffffff">미디어룸</a>
				 &nbsp;|&nbsp;
                <a href="/admin/faq" style="font-weight:bold;color:#ffffff">FAQ</a>
				 &nbsp;|&nbsp;
                <a href="/admin/notice" style="font-weight:bold;color:#ffffff">공지사항</a>
				 &nbsp;|&nbsp;
                <a href="/admin/banner" style="font-weight:bold;color:#ffffff">배너</a>
				 &nbsp;|&nbsp;
                <a href="/admin/mail" style="font-weight:bold;color:#ffffff">메일발송</a>
				 &nbsp;|&nbsp;
                <a href="/admin/strategies" style="font-weight:bold;color:#ffffff">상품</a>
				 &nbsp;|&nbsp;
                <a href="/admin/strategies_qna" style="font-weight:bold;color:#ffffff">문의하기</a>
				 &nbsp;|&nbsp;
                <a href="/admin/strategies_invest" style="font-weight:bold;color:#ffffff">투자하기</a>
				 &nbsp;|&nbsp;
                <a href="/admin/pb_request" style="font-weight:bold;color:#ffffff">상품상담</a>
				 &nbsp;|&nbsp;
                <a href="/admin/customer" style="font-weight:bold;color:#ffffff">고객센터</a>
                <!--a href="/admin/contacts" style="font-weight:bold;color:#ffffff">Contact관리</a-->
        </div>
    </div>
    <!------ //헤더 영역 ------->