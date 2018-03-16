                                    <? foreach ($invests as $invest) { ?>
									<tr>
										<td><?=$invest['invest_id']?></td>
										<td class="left">
											<p class="subject"><?=$invest['strategy_name']?></p>
										</td>
										<td class="right price"><?=number_format($invest['s_price'])?></td>
										<td class="small"><?=$invest['s_date']?></td>
										<td class="small"><?=$invest['max_loss_per']?>%</td>
										<td>
                                            <? if ($invest['status'] == 0) { ?>
											<strong class="stat counseling">진행중</strong>
                                            <? } else if ($invest['status'] == 1) { ?>
											<strong class="stat counseling">상담완료</strong>
                                            <? } else { ?>
											<strong class="stat investmenting">투자실행</strong>
                                            <? } ?>
										</td>
									</tr>
                                    <? } ?>