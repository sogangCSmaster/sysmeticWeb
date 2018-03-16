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

		<div class="sub_menu">
            <!------ 관리자 메뉴 // 슈퍼어드민 관리자에게만 노출되는 메뉴 ------->
            <p class="sub_menu3" style="display:block;">
                <?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/users" id="submenu3_1" class="<?php if(isset($current_menu) && $current_menu == 'admin_users') echo 'on';else echo 'off' ?>">회원관리</a><?php } ?>
                <?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/items" id="submenu3_2" class="<?php if(isset($current_menu) && $current_menu == 'admin_items') echo 'on';else echo 'off' ?>">종목관리</a><?php } ?>
                <?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/brokers" id="submenu3_3" class="<?php if(isset($current_menu) && $current_menu == 'admin_brokers') echo 'on';else echo 'off' ?>">브로커관리</a><?php } ?>
                <?php if($_SESSION['user']['user_type'] == 'A' || $_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'B'){ ?><a href="/admin/strategies" id="submenu3_4" class="<?php if(isset($current_menu) && $current_menu == 'admin_strategies') echo 'on';else echo 'off' ?>">전략관리</a><?php } ?>
                <?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/contacts" id="submenu3_5" class="<?php if(isset($current_menu) && $current_menu == 'admin_contacts') echo 'on';else echo 'off' ?>">Contact관리</a><?php } ?>
                <?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/notice" id="submenu1_5" class="<?php if(isset($current_menu) && $current_menu == 'admin_notice') echo 'on';else echo 'off' ?>">공지사항</a><?php } ?>
                <?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/mail" id="submenu3_6" class="<?php if(isset($current_menu) && $current_menu == 'admin_mail') echo 'on';else echo 'off' ?>">메일발송</a><?php } ?>
            </p>
        </div>

        <!------ 스크롤 메뉴 ------->
        <div id="menu_layer">
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
                
				<div class="sub_menu">
                    <!------ 관리자 메뉴 // 슈퍼어드민 관리자에게만 노출되는 메뉴 ------->
                    <p class="sub_menu3" style="display:block;">
						<?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/users" id="submenu3_1" class="<?php if(isset($current_menu) && $current_menu == 'admin_users') echo 'on';else echo 'off' ?>">회원관리</a><?php } ?>
						<?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/items" id="submenu3_2" class="<?php if(isset($current_menu) && $current_menu == 'admin_items') echo 'on';else echo 'off' ?>">종목관리</a><?php } ?>
						<?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/brokers" id="submenu3_3" class="<?php if(isset($current_menu) && $current_menu == 'admin_brokers') echo 'on';else echo 'off' ?>">브로커관리</a><?php } ?>
						<?php if($_SESSION['user']['user_type'] == 'A' || $_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'B'){ ?><a href="/admin/strategies" id="submenu3_4" class="<?php if(isset($current_menu) && $current_menu == 'admin_strategies') echo 'on';else echo 'off' ?>">전략관리</a><?php } ?>
						<?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/contacts" id="submenu3_5" class="<?php if(isset($current_menu) && $current_menu == 'admin_contacts') echo 'on';else echo 'off' ?>">Contact관리</a><?php } ?>
						<?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/notice" id="submenu1_5" class="<?php if(isset($current_menu) && $current_menu == 'admin_notice') echo 'on';else echo 'off' ?>">공지사항</a><?php } ?>
						<?php if($_SESSION['user']['user_type'] == 'A'){ ?><a href="/admin/mail" id="submenu3_6" class="<?php if(isset($current_menu) && $current_menu == 'admin_mail') echo 'on';else echo 'off' ?>">메일발송</a><?php } ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!------ //헤더 영역 ------->