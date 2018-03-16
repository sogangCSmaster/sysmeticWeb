<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 개인정보</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="info">개인정보</h3>
            
            <a href="/agreement" title="이용약관 보기" class="btn_view rules"><span class="ir">이용약관 보기</span></a>
            <p class="sub_title default">기본정보</p>
            <div class="user_info view">
                <dl>
                    <!------ 트레이더/브로커/어드민만 프로필 이미지 등록 가능 ------->
					<?php if($_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'B' || $_SESSION['user']['user_type'] == 'A'){ ?>
                    <dt class="photo">프로필<br />이미지</dt>
                    <dd class="photo">
                        <img src="/img/profile_over.png" class="over" />
						<img src="<?php echo $_SESSION['user']['picture_s'] ?>" alt="" />
                    </dd>
					<?php } ?>
                    <dt>이메일</dt>
                    <dd class="eng">
						<?php if(empty($_SESSION['user']['email'])){ ?>
						<span class="no">이메일/비밀번호  등록 후 아이디로 사용할 수 있습니다.</span>
						<?php }else{ ?>
                        <?php echo htmlspecialchars($_SESSION['user']['email']) ?>
						<?php } ?>
						<?php if($_SESSION['user']['user_type'] == 'T'){ ?>
						<img src="/img/ico_trader.gif" alt="Trader" />
                        <?php }else if($_SESSION['user']['user_type'] == 'A'){ ?>
						<img src="/img/ico_admin.gif" alt="Admin" />
                        <?php }else if($_SESSION['user']['user_type'] == 'B'){ ?><img src="/img/ico_broker.gif" alt="Broker" />
						<?php } ?>                        
                    </dd>
					<dt>닉네임</dt>
                    <dd><?php if(!empty($_SESSION['user']['nickname'])) echo htmlspecialchars($_SESSION['user']['nickname']); else echo '<span class="no">등록된 닉네임이 없습니다.</span>' ?></dd>
                    <dt>이름</dt>
                    <dd><?php if(!empty($_SESSION['user']['name'])) echo htmlspecialchars($_SESSION['user']['name']); else echo '<span class="no">등록된 이름이 없습니다.</span>' ?></dd>
                    <dt>휴대폰</dt>
                    <dd class="eng"><?php if(!empty($_SESSION['user']['mobile'])) echo htmlspecialchars($_SESSION['user']['mobile']); else echo '<span class="no">휴대폰번호가 없습니다.</span>' ?></dd>
                    <dt>생년월일</dt>
                    <dd class="eng"><?php if(!empty($_SESSION['user']['birthday']) && strlen($_SESSION['user']['birthday']) == 8) echo htmlspecialchars(substr($_SESSION['user']['birthday'], 0, 4).'.'.substr($_SESSION['user']['birthday'], 4, 2).'.'.substr($_SESSION['user']['birthday'], 6, 2)); else echo '<span class="no">생년월일 정보가 없습니다.</span>' ?></dd>
                    <dt>지역</dt>
                    <dd><?php if(!empty($_SESSION['user']['sido']) || !empty($_SESSION['user']['gugun'])) echo htmlspecialchars($_SESSION['user']['sido'].' '.$_SESSION['user']['gugun']); else echo '<span class="no">지역 정보가 없습니다.</span>' ?></dd>
                    <dt>성별</dt>
                    <dd><?php if($_SESSION['user']['gender'] == 'M') echo '남'; else echo '여' ?><!------ <span class="no">성별 정보가 없습니다.</span> -------></dd>
                </dl>
            </div>
            
            <p class="sub_title agree2">정보수신동의</p>
            <div class="agree_mail">
                <ul class="view">
                    <li<?php if($_SESSION['user']['alarm_feeds']) echo ' class="agree"' ?>>관심 전략과 관심 포트폴리오 관련 정보를 수신 동의합니다. </li>
                    <li<?php if($_SESSION['user']['alarm_all']) echo ' class="agree"' ?>> 전략 및 정보성 알림에 수신 동의합니다. </li>
                </ul>
            </div>

            <p class="btn_board">
                <a title="수정하기" class="submit" href="/settings/edit"><span class="ir">수정하기</span></a>
            </p>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
