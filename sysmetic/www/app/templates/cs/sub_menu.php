
					<div class="head">
						<div class="top_a">
							<h2 class="tit n_squere">고객센터</h2>
							<div class="infos">
								<dl class="info01 email">
									<dt>제휴 문의</dt>
									<dd>contact@sysmetic.co.kr</dd>
								</dl>
								<dl class="info02 email">
									<dt>일반 문의</dt>
									<dd>help@sysmetic.co.kr</dd>
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
							<!--div class="sns">
								<a href="javascript:;" class="facebook"><img src="../images/sub/ico_cs_facebook.png" alt="facebook" /></a>
								<a href="javascript:;" class="blog"><img src="../images/sub/ico_cs_blog.png" alt="blog" /></a>
							</div-->
						</div>
						<nav class="cs_nav">
							<ul>
								<li class="<?=($submenu == 'notice') ? 'curr' : '';?>"><a href="/cs/notice">공지사항</a></li>
								<li class="<?=($submenu == 'faq') ? 'curr' : '';?>"><a href="/cs/faq">FAQ</a></li>
                                <? if ($isLoggedIn()) { ?>
								<li class="<?=($submenu == 'req') ? 'curr' : '';?>"><a href="/cs/req">1:1 문의</a></li>
                                <? } else { ?>
								<li class="<?=($submenu == 'req') ? 'curr' : '';?>"><a href="#a" onclick="login();">1:1 문의</a></li>
                                <? } ?>
								<li class="<?=($submenu == 'guide') ? 'curr' : '';?>"><a href="/cs/guide">이용안내</a></li>
								<li class="<?=($submenu == 'media') ? 'curr' : '';?>"><a href="/cs/media">Media Room</a></li>
								<li class="<?=($submenu == 'education') ? 'curr' : '';?>"><a href="/cs/education">교육안내</a></li>
								<li class="<?=($submenu == 'partners') ? 'curr' : '';?>"><a href="/cs/partners">Partners</a></li>
							</ul>
						</nav>
					</div>