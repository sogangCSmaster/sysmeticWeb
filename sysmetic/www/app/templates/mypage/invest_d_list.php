                                    <? foreach ($invests as $invest) { ?>
                                    <tr>
										<td><?=$invest['invest_id']?></td>
										<td><?=$invest['user_name']?></td>
										<td class="small"><?=$invest['mobile']?></td>
										<td class="small"><?=$invest['email']?></td>
										<td class="right price"><?=number_format($invest['s_price'])?></td>
										<td class="small"><?=$invest['s_date']?></td>
										<td class="small"><?=$invest['max_loss_per']?>%</td>
										<td>
											<div class="custom_selectbox" style="width:97px;">
												<label for="">전체</label>
												<select id="" name="" onChange="chgStatus(<?=$invest['invest_id']?>, this.value);">
													<option value="0" <?=($invest['status'] == 0) ? 'selected' : ''?>>잰행중</option>
													<option value="1" <?=($invest['status'] == 1) ? 'selected' : ''?>>상담완료</option>
													<option value="2" <?=($invest['status'] == 2) ? 'selected' : ''?>>투자실행</option>
												</select>
											</div>
											<button type="button" class="btn">변경</button>
										</td>
									</tr>
                                    <? } ?>