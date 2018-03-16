                                <?
                                $now = date('Y-m-d H:i:s');
                                foreach ($lists as $v) {
                                ?>
								<tr>
									<td class="left">
										<a href="/cs/education/<?=$v['eidx']?>">
											<p class="subject normal">
												<?=$v['subject']?>
                                                <?
                                                if ($v['e_end_date'] < $now) { // 교육기간 종료 : 종료
                                                    $status = '<span class="status end">교육종료</span>';
                                                } else if ($v['a_start_date'] < $now && $v['a_end_date'] > $now) { // 접수기간 중 : 접수중
                                                    $status = '<span class="status ing">신청중</span>';
                                                } else if ($v['a_start_date'] > $now) { // 접수전 : 대기중
                                                    $status = '<span class="status deadline">신청대기</span>';
                                                } else if ($v['a_end_date'] < $now && $v['e_start_date'] > $now) {
                                                    $status = '<span class="status end">신청마감</span>';
                                                } else {
                                                    $status = '<span class="status ing">교육중</span>';
                                                }
                                                echo $status;
                                                ?>
											</p>
										</a>
									</td>
									<td class="small two">
										<?=substr($v['e_start_date'], 0, 16)?> ~ <br />
										<?=substr($v['e_end_date'], 0, 16)?>
									</td>
									<td class="small two gray">
										<?=substr($v['a_start_date'], 0, 16)?> ~ <br />
										<?=substr($v['a_end_date'], 0, 16)?>
									</td>
								</tr>
                                <? } ?>