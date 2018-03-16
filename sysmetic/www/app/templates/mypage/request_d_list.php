                                <?
                                foreach ($qnas as $qna) {
                                ?>
                                <li class="ing" onclick='location.href="/mypage/request/<?=$qna['qna_id']?>"' style='cursor:pointer'>
									<div class="row_q">
										<p class="subject"><strong class="category">[<?=$qna['strategy_name']?>]</strong><?=$qna['subject']?></p>
										<span class="write_time">&nbsp;&nbsp;|&nbsp;&nbsp;<?=substr($qna['reg_at'], 0, 16)?></span>
										<span class="name"><?=$qna['name']?></span>
									</div>
									<div class="row_stat">
                                        <? if (!$qna['answer']) { ?>
										<a href="/mypage/request/<?=$qna['qna_id']?>/answer" class="btn_reply_write">답변하기</a>
                                        <? } else { ?>
                                        <strong class="stat_complete">답변완료</strong>
										<span class="write_time"><?=date('Y-m-d H:i', $qna['answer_at'])?></span>
                                        <? } ?>
									</div>
								</li>
                                <? } ?>