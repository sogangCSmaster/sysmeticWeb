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

	<!------ 헤더 영역 ------->
	<div id="head" style="display:block;">
        <div class="top">        
            <h1><a href="/">SYSMETIC TRADERS</a></h1>
            <div class="top_menu">                
                <!------ //로그인 전 ------->
				<?php if(!$isLoggedIn()){ ?>
                <a href="/signup" class="btn_admin">회원가입<a>
                <a href="/signin" class="btn_login" onclick="showLayer('login_layer');return false;">로그인</a>
				<?php }else{ ?>

                <?php if($_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'B'){ ?><a href="/admin/strategies" class="top_admin">전략관리</a><?php } ?>
                <?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin" class="top_admin">사이트관리</a><?php } ?>

                <p class="personal" onclick="$('#topbox_layer').toggle();">
				<?php echo htmlspecialchars($_SESSION['user']['email']); ?>
                </p>
                <div id="topbox_layer" class="topbox" style="display:none;">                    
                    <img src="/img/ico_topbox_close.gif" />
                    <ul style="width:110px;">
                        <li><a href="/settings">개인정보</a></li>
                        <?php if($_SESSION['user']['user_type'] == 'N'){ ?>
						<li><a href="/qna">나의 Q&A</a></li>
						<?php }else if($_SESSION['user']['user_type'] == 'T'){ ?>
						<li><a href="/my_answers">나의 Q&A</a></li>
						<?php } ?>
                        <li><a href="/logout">로그아웃</a></li>
                    </ul>
                </div>                
				<?php } ?>
            </div>
            <div class="menu">
                <a href="/intro" id="menu1" class="<?php if(isset($current_menu) && in_array($current_menu, array('intro', 'guide', 'training', 'service', 'notice'))) echo 'on';else echo 'off' ?>">시스메틱 안내</a>
                <a href="/brokers" id="menu2" class="<?php if(isset($current_menu) && $current_menu == 'brokers') echo 'on';else echo 'off' ?>">브로커</a>
                <a href="/strategies" id="menu3" class="<?php if(isset($current_menu) && in_array($current_menu, array('strategies', 'followings'))) echo 'on';else echo 'off' ?>">전략랭킹</a>
                <a href="/portfolios" id="menu4" class="<?php if(isset($current_menu) && $current_menu == 'portfolios') echo 'on';else echo 'off' ?>">포트폴리오</a>
            </div>
        </div>

		<?php if(isset($current_menu) && in_array($current_menu, array('intro', 'guide', 'training', 'service', 'notice', 'escrow'))){ ?>
        <div class="sub_menu">
            <!------ 시스메틱안내 서브메뉴 ------->
            <p class="sub_menu1">
                <a href="/intro" id="submenu1_1" class="<?php if(isset($current_menu) && $current_menu == 'intro') echo 'on';else echo 'off' ?>">시스메틱 소개</a>
                <a href="/guide" id="submenu1_2" class="<?php if(isset($current_menu) && $current_menu == 'guide') echo 'on';else echo 'off' ?>">이용안내</a>
				<!--
                <a href="/training" id="submenu1_3" class="<?php if(isset($current_menu) && $current_menu == 'training') echo 'on';else echo 'off' ?>">교육안내</a>
				-->
				<!-- <a href="/escrow" id="submenu1_3" class="<?php if(isset($current_menu) && $current_menu == 'escrow') echo 'on';else echo 'off' ?>">담보투자</a>-->
                <a href="/service" id="submenu1_4" class="<?php if(isset($current_menu) && $current_menu == 'service') echo 'on';else echo 'off' ?>">제휴 서비스</a>
                <a href="/bbs/notice" id="submenu1_5" class="<?php if(isset($current_menu) && $current_menu == 'notice') echo 'on';else echo 'off' ?>">공지사항</a>
            </p>
		</div>
		<?php } ?>
		<?php if(isset($current_menu) && in_array($current_menu, array('strategies', 'followings'))){ ?>
		<div class="sub_menu">
            <!------ 전략랭킹 서브메뉴 ------->
            <p class="sub_menu2">
                <a href="/strategies" id="submenu2_1" class="<?php if(isset($current_menu) && $current_menu == 'strategies') echo 'on';else echo 'off' ?>">전략랭킹</a>
                <a href="/followings/<?php echo date("Ymd") ?>" id="submenu2_2" class="<?php if(isset($current_menu) && $current_menu == 'followings') echo 'on';else echo 'off' ?>">나의 관심전략</a>
            </p>
        </div>
		<?php } ?>

        <!------ 스크롤 메뉴 ------->
        <div id="menu_layer" style="">
            <div class="layer_wrap">
                <h1 class="scroll"><a href="/">SYSMETIC TRADERS</a></h1>
                 <div class="menu">
                    <a href="/intro" id="menu1" class="<?php if(isset($current_menu) && in_array($current_menu, array('intro', 'guide', 'training', 'service', 'notice'))) echo 'on';else echo 'off' ?>">시스메틱 안내</a>
                    <a href="/brokers" id="menu2" class="<?php if(isset($current_menu) && $current_menu == 'brokers') echo 'on';else echo 'off' ?>">브로커</a>
                    <a href="/strategies" id="menu3" class="<?php if(isset($current_menu) && in_array($current_menu, array('strategies', 'followings'))) echo 'on';else echo 'off' ?>">전략랭킹</a>
                    <a href="/portfolios" id="menu4" class="<?php if(isset($current_menu) && $current_menu == 'portfolios') echo 'on';else echo 'off' ?>">포트폴리오</a>
                    <?php if($isLoggedIn()){ ?><a href="#topbox_layer2" id="menu5" onclick="$('#topbox_layer2').toggle();return false;">개인메뉴</a><?php } ?>
                </div>
				<?php if($isLoggedIn()){ ?>
                <div id="topbox_layer2" class="topbox" style="display:none;">                    
                    <ul style="width:110px;">
                        <?php if($_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'B'){ ?><li><a href="/admin/strategies">전략관리</a></li><?php } ?>
                        <?php if($_SESSION['user']['user_type'] == 'A'){ ?><li><a href="/admin">사이트관리</a></li><?php } ?>
                        <li><a href="/settings">개인정보</a></li>
                        <?php if($_SESSION['user']['user_type'] == 'N'){ ?>
						<li><a href="/qna">나의 Q&A</a></li>
						<?php }else if($_SESSION['user']['user_type'] == 'T'){ ?>
						<li><a href="/my_answers">나의 Q&A</a></li>
						<?php } ?>
                        <li><a href="/logout">로그아웃</a></li>
                    </ul>
                </div>   
				<?php } ?>
                
				<?php if(isset($current_menu) && in_array($current_menu, array('intro', 'guide', 'training', 'service', 'notice', 'escrow'))){ ?>
                <div class="sub_menu">
                    <!------ 시스메틱안내 서브메뉴 ------->
                    <p class="sub_menu1">
                      <a href="/intro" id="submenu1_1" class="<?php if(isset($current_menu) && $current_menu == 'intro') echo 'on';else echo 'off' ?>">시스메틱 소개</a>
                      <a href="/guide" id="submenu1_2" class="<?php if(isset($current_menu) && $current_menu == 'guide') echo 'on';else echo 'off' ?>">이용안내</a>
					  <!--
                      <a href="/training" id="submenu1_3" class="<?php if(isset($current_menu) && $current_menu == 'training') echo 'on';else echo 'off' ?>">교육안내</a>
					  -->
					  <!--<a href="/escrow" id="submenu1_3" class="<?php if(isset($current_menu) && $current_menu == 'escrow') echo 'on';else echo 'off' ?>">담보투자</a>-->
                      <a href="/service" id="submenu1_4" class="<?php if(isset($current_menu) && $current_menu == 'service') echo 'on';else echo 'off' ?>">제휴 서비스</a>
                      <a href="/bbs/notice" id="submenu1_5" class="<?php if(isset($current_menu) && $current_menu == 'notice') echo 'on';else echo 'off' ?>">공지사항</a>
                    </p>
                </div>
				<?php } ?>

				<?php if(isset($current_menu) && in_array($current_menu, array('strategies', 'followings'))){ ?>
                <div class="sub_menu">
                    <!------ 전략랭킹 서브메뉴 ------->
                    <p class="sub_menu2">
                      <a href="/strategies" id="submenu2_1" class="<?php if(isset($current_menu) && $current_menu == 'strategies') echo 'on';else echo 'off' ?>">전략랭킹</a>
                      <a href="/followings/<?php echo date("Ymd") ?>" id="submenu2_2" class="<?php if(isset($current_menu) && $current_menu == 'followings') echo 'on';else echo 'off' ?>">나의 관심전략</a>
                    </p>
                </div>
				<?php } ?>
            </div>
        </div>
    </div>
    <!------ //헤더 영역 ------->
