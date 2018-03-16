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
	<link rel="stylesheet" href="/css/reset.css">
	<link rel="stylesheet" href="/css/common2.css?<?=time()?>">
	<link rel="stylesheet" href="/css/sub.css?<?=time()?>">
	<link rel="stylesheet" href="/css/jquery_ui.css">
	<script src="/script/jquery-1.10.1.min.js"></script>
	<script src="/script/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="/script/html5shiv.js"></script>
	<script src="/script/common.js?t-<?=time();?>"></script>
	<script src="/script/imageresize.js"></script>
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