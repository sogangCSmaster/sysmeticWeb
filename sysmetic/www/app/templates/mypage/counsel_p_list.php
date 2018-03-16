                                <?
                                foreach ($counsels as $counsel) {
                                    $class = ($counsel['req_type'] == 'Offline') ? "offline" : "";
                                    $status = $counsel['status'] ? '' : 'ing';
                                ?>
                                <li class="<?=$class?> <?=$status?>">
                                    
                                    <? if ($counsel['status'] == 0) { ?>
                                        <div class="row_q">
                                            <a href="/mypage/counsel/<?=$counsel['req_id']?>">
                                            <p class="subject"><?=$counsel['subject']?></p>
                                            <span class="write_time">&nbsp;&nbsp;|&nbsp;&nbsp;<?=substr($counsel['reg_date'], 0, 16)?></span>
                                            <span class="name"><?=$counsel['req_name']?></span>
                                            </a>
                                        </div>
                                        <div class="row_stat">
                                            <? if ($class == 'offline') { ?>
                                                <form id="answerFrm" action="/mypage/counsel/<?=$counsel['req_id']?>/answer" method="post">
                                                <button type="submit" class="btn_reply_write">상담완료 표시하기</button>
                                                </form>
                                                <strong class="bedge">Offline 상담</strong>
                                            <? } else { ?>
                                                <a href="/mypage/counsel/<?=$counsel['req_id']?>/answer" class="btn_reply_write">답변하기</a>
                                            <? } ?>
                                        </div>
                                    <? } else { ?>
                                        <a href="/mypage/counsel/<?=$counsel['req_id']?>">
                                            <div class="row_q">
                                                <p class="subject"><?=$counsel['subject']?></p>
                                                <span class="write_time">&nbsp;&nbsp;|&nbsp;&nbsp;<?=substr($counsel['answer_date'], 0, 16)?></span>
                                                <span class="name"><?=$counsel['req_name']?></span>
                                            </div>
                                            <div class="row_stat">
                                                <? if ($class == 'offline') { ?>
                                                    <strong class="stat_complete">상담완료</strong>
                                                    <span class="write_time"><?=substr($counsel['answer_date'], 0, 16)?></span>
                                                    <strong class="bedge">Offline 상담</strong>
                                                <? } else { ?>
                                                    <strong class="stat_complete">답변완료</strong>
                                                    <span class="write_time"><?=substr($counsel['answer_date'], 0, 16)?></span>
                                                <? } ?>
                                            </div>
                                        </a>
                                    <? } ?>
								</li>
                                <?
                                }
                                ?>