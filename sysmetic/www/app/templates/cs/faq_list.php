                            <? foreach ($lists as $v) { ?>
							<li>
								<a href="javascript:;"  class="question" onclick="faq_view(<?=$v['fidx']?>)">
									<p class="subject">
										<strong class="t_category">[<?=$v['name']?>]</strong> <?=$v['subject']?>
									</p>
									<button type="button" class="btn_control" id="fview<?=$v['fidx']?>">답변보기</button>
								</a>
								<div class="answer">
									<?=$v['contents']?>
								</div>
							</li>
                            <? } ?>
