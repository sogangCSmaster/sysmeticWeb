                                <?
                                foreach ($counsels as $counsel) {
                                    $class = ($counsel['req_type'] == 'Offline') ? "offline" : "";
                                ?>
                                <li class="<?=$class?>">
									<a href="/mypage/counsel/<?=$counsel['req_id']?>">
										<div class="row_q">
											<p class="subject"><?=$counsel['subject']?></p>
											<span class="write_time"><?=substr($counsel['reg_date'], 0, 16)?></span>
										</div>
										<div class="row_stat">
                                            <? if ($counsel['req_type'] == 'Online') { ?>
                                                <? if ($counsel['status'] == 0) { ?>
                                                <p class="summary"><?=$counsel['pb_name']?> 님이 답변대기 중입니다.</p>
                                                <? } else { ?>
                                                <p class="summary"><?=$counsel['pb_name']?> 님의 답변이 등록 되었습니다.</p>
                                                <strong class="stat_complete">답변완료</strong>
											    <span class="write_time"><?=substr($counsel['answer_date'], 0, 16)?></span>
                                                <? } ?>
                                            <? } else { ?>
                                                <? if ($counsel['status'] == 0) { ?>
                                                <p class="summary">상담 예약중입니다.</p>
                                                <? } else { ?>
                                                <p class="summary">상담 완료되었습니다.</p>
                                                <strong class="stat_complete">답변완료</strong>
											    <span class="write_time"><?=substr($counsel['answer_date'], 0, 16)?></span>
                                                <? } ?>
											    <strong class="bedge">Offline 상담</strong>
                                            <? } ?>
										</div>
									</a>
								</li>
                                <? } ?>