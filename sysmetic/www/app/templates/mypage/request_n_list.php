                                <?
                                foreach ($qnas as $qna) {
                                ?>
								<li>
									<a href="/mypage/request/<?=$qna['qna_id']?>">
										<div class="row_q">
											<p class="subject"><strong class="category">[<?=$qna['strategy_name']?>]</strong><?=$qna['subject']?></p>
											<span class="write_time"><?=substr($qna['reg_at'], 0, 16)?></span>
										</div>
										<div class="row_stat">
                                            <? if (!$qna['answer']) { ?>
											<p class="summary">답변 대기 중입니다.</p>
                                            <? } else { ?>
											<p class="summary">답변이 완료 되었습니다.</p>
											<strong class="stat_complete">답변완료</strong>
											<span class="write_time"><?=date('Y-m-d H:i', $qna['answer_at'])?></span>
                                            <? } ?>
										</div>
									</a>
								</li>
                                <? } ?>