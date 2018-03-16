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
				<h1 class="header_logo"><a href="/"><img src="images/common/img_header_logo.png" alt="SYSMETIC" /></a></h1>
				<div class="cont_box">  
                <!------ //로그인 전 ------->
				    <?php if(!$isLoggedIn()){ ?>
					<div class="member before_login">
						<div class="links">
							<a href="javascript:;">로그인</a>
							<span class="bar">/</span>
							<a href="/join_select">회원가입</a>
							<span class="bar">/</span>
							<a href="/guide">이용안내</a>
							<span class="bar">/</span>
							<a href="javascript:;">고객센터</a>
						</div>
						<a href="javascript:;" class="btn_bigdata">주식 빅데이터</a>
					</div>
					<!-- //로그인 전 -->
				    <?php }else{ ?>


					<!-- 로그인 후 -->
					<div class="member after_login">
						<div class="links">
                            <?php if($_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'B'){ ?><a href="/admin/strategies">전략관리</a><?php } ?>
                            <?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin">사이트관리</a><?php } ?>

							<a href="javascript:;"><img src="images/common/ico_header_coffee.png" alt="" /> My Lounge</a>
							<a href="javascript:;">PB 게시판</a>

                            <?php if($_SESSION['user']['user_type'] == 'N'){ ?>
                            <li><a href="/qna">나의 Q&A</a></li>
                            <?php }else if($_SESSION['user']['user_type'] == 'T'){ ?>
                            <li><a href="/my_answers">나의 Q&A</a></li>
                            <?php } ?>

							<a href="/settings">마이페이지</a>
							<a href="/logout">로그아웃</a>
						</div>
						<a href="#" class="btn_bigdata">주식 빅데이터</a>
					</div>
					<!-- //로그인 후 -->
				    <?php } ?>

					<!-- gnb -->
					<nav class="gnb">
						<ul>
							<li>
								<a href="javascript:;"><img src="images/common/txt_gnb01_off.png" alt="투자하기" /></a>
								<ul class="sub">
									<li><a href="javascript:;"><img src="images/common/txt_gnb0101.png" alt="전략랭킹" /></a></li>
									<li><a href="javascript:;"><img src="images/common/txt_gnb0102.png" alt="PB/트레이더" /></a></li>
									<li><a href="javascript:;"><img src="images/common/txt_gnb0103.png" alt="상품검색" /></a></li>
									<li><a href="javascript:;"><img src="images/common/txt_gnb0104.png" alt="전략 포트폴리오" /></a></li>
								</ul>
							</li>
							<li>
								<a href="javascript:;"><img src="images/common/txt_gnb02_off.png" alt="투자받기" /></a>
								<ul class="sub">
									<li><a href="javascript:;"><img src="images/common/txt_gnb0201.png" alt="전략등록" /></a></li>
								</ul>
							</li>
							<li>
								<a href="javascript:;"><img src="images/common/txt_gnb03_off.png" alt="Lounge" /></a>
								<ul class="sub">
									<li><a href="javascript:;"><img src="images/common/txt_gnb0301.png" alt="My Lounge" /></a></li>
								</ul>
							</li>
						</ul>
					</nav>
					<!-- //gnb -->
				</div>
			</div>
		</header>
		<!-- header -->