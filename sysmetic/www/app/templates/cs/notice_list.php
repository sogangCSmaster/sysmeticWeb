                                    <? foreach ($lists as $v) { ?>
									<tr>
										<td><?=$v['notice_id']?></td>
										<td class="left">
											<a href="/cs/notice/<?=$v['notice_id']?>"><p class="subject"><?=htmlspecialchars($v['subject'])?>
											<?
											if($v['filecnt'])echo " <img src='/images/sub/ico_folder.png'>";
											?>
											</p></a>
										</td>
										<td class="small"><?=$v['reg_at']?></td>
									</tr>
                                    <? } ?>
