                                <?
                                foreach ($lists as $k => $v) {
                                ?>
								<li>
									<div class="photo">
										<img src="<?=getProfileImg($v['picture'])?>" alt="">
									</div>
									<div class="detail">
										<div class="row">
											<div class="left_a">
												<strong class="name"><?=$v['nickname']?></strong>
												<div class="grade"><img src="/images/sub/img_pb_grade<?=$v['num']?>.gif" alt="5점"></div>
												<span class="date"><?=$v['reg_date']?></span>
											</div>
                                            <? if ($v['uid'] == $_SESSION['user']['uid']) { ?>
											<div class="right_a">
												<a href="javascript:;" onclick="del()" class="btn_delete"><img src="/images/sub/btn_review_delete.gif" alt="delete"></a>
											</div>
                                            <? } ?>
										</div>
										<p class="r_cont"><?=nl2br($v['contents'])?></p>
									</div>
								</li>
                                <?
                                }
                                ?>