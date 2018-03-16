                                    <? foreach ($lists as $v) { ?>
									<tr>
										<td><?=$v['midx']?></td>
										<td class="left">
											<a href="/cs/media/<?=$v['midx']?>"><p class="subject"><?=htmlspecialchars($v['subject'])?>
											<?
											if($v['filecnt'])echo " <img src='/images/sub/ico_folder.png'>";
											?>
											</p></a>
										</td>
										<td class="small"><?=$v['reg_date']?></td>
									</tr>
                                    <? } ?>
