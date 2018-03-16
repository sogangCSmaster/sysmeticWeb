                    <?
                    $picture = getProfileImg($myInfo['picture']);
                    $user_type = array(
                        'T' => '트레이더 회원',
                        'P' => 'PB 회원',
                        'N' => '일반 회원',
                        'A' => '관리자'
                    );
                    ?>					
                    <div class="head">
						<div class="photo">
							<img src="<?=$picture?>" alt="" />
						</div>
						<div class="info_nav">
							<div class="info_box">
								<div class="info">
									<strong class="name"><?=$myInfo['nickname']?></strong>
									<dl>
										<dt>가입일 : </dt>
										<dd> <?=substr($myInfo['reg_at'], 0, 10)?></dd>
										<dt class="type">회원타입 : </dt>
										<dd> <?=$user_type[$myInfo['user_type']]?></dd>
									</dl>
								</div>
								<div class="btns">
									<a href="/mypage/modify" class="btn modify">개인정보수정</a>
									<a href="/mypage/withdraw" class="btn member_leave">회원탈퇴하기</a>
								</div>
							</div> 
							<nav class="menu">
								<ul>
									<li class="menu01 <?=($submenu=='subscribe') ? 'curr' : ''?>">
										<a href="/mypage/subscribe">
											구독
											<img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
										</a>
									</li>
									<li class="menu02 <?=($submenu=='favorite') ? 'curr' : ''?>">
										<a href="/mypage/favorite">
											나의 관심상품
											<img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
										</a>
									</li>
                                    <? if ($_SESSION['user']['user_type'] == 'P' || $_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'A') { ?>
									<li class="menu03 <?=($submenu=='strategy') ? 'curr' : ''?>">
										<a href="/mypage/strategies">
											상품관리
											<img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
										</a>
									</li>
                                    <? } ?>
									<li class="menu04 <?=($submenu=='portfolio') ? 'curr' : ''?>">
										<a href="/mypage/portfolios">
											나의 포트폴리오
											<img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
										</a>
									</li>
                                    <? if ($_SESSION['user']['user_type'] == 'P' || $_SESSION['user']['user_type'] == 'N' || $_SESSION['user']['user_type'] == 'T') { ?>
									<li class="menu05 <?=($submenu=='counsel') ? 'curr' : ''?>">
										<a href="/mypage/counsel">
											나의 상담
											<img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
										</a>
									</li>
                                    <? } ?>
									<li class="menu06 <?=($submenu=='request') ? 'curr' : ''?>">
										<a href="/mypage/request">
											나의 문의
											<img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
										</a>
									</li>
									<li class="menu07 <?=($submenu=='invest') ? 'curr' : ''?>">
										<a href="/mypage/invest">
											나의 투자
											<img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
										</a>
									</li>
									<li class="menu08 <?=($submenu=='customer') ? 'curr' : ''?>">
										<a href="/mypage/customer">
											고객센터 상담내역
											<img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
										</a>
									</li>
								</ul>
							</nav>
						</div>
					</div>
