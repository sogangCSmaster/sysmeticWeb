								<?
                                foreach ($qnas as $qna) {
                                ?>
                                <li>
									<a href="/mypage/customer/<?=$qna['cus_id']?>">
										<div class="row_q">
											<p class="subject"><strong class="category">[<?=$qna['target_value_text']?>]</strong><?=$qna['subject']?></p>
											<span class="write_time"><?=substr($qna['reg_at'], 0, 16)?></span>
										</div>
										<div class="row_stat">
                                            <? if (!$qna['answer']) { ?>
											<p class="summary">답변대기 중입니다.</p>
                                            <? } else { ?>
											<p class="summary">답변이 완료되었습니다.</p>
											<strong class="stat_complete">답변완료</strong>
                                            <? } ?>
										</div>
									</a>
								</li>
                                <?
                                }
                                ?>