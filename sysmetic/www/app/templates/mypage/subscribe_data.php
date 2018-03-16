<ul class="list_body">
						<? if (count($lists)) { ?>
                            <?
							$idx = 0;
							foreach ($lists as $v) {

								$content = $v['contents'];
								$img_array = str_img($content);
								$bgImg = $img_array[0];

                                if (!$bgImg) {
                                    $bgImg = '/images/img_lounge_sample'.rand(0, 2).'.jpg';
                                }


								$bg = "background:url('$bgImg') no-repeat center; background-size:cover;";
                            ?>

							<li class="<?=($idx++ % 4 == 0) ? 'left' : ''?>">
                                <a href="/lounge/<?=$v['uid']?>" class="subject" style="<?=$bg?>">
                                    <p>
										<?=$v['subject']?>
										<?
										if($v['filecnt'])echo " <img src='/images/sub/ico_folder.png'>";
										?>
									</p>
                                </a>
                            </li>

                            <!-- <li>
                                <div class="row">
                                    <div class="photo">
                                        <img src="<?=getProfileImg($v['picture'])?>" alt="" />
                                    </div>
                                    <div class="info">
                                        <strong class="name"><?=$v['nickname']?></strong>
                                        <a href="/lounge/<?=$v['uid']?>" class="btn_lounge"><img src="/images/sub/ico_list_coffee.gif" alt="my lounge" /></a>
                                        <span class="date"><?=$v['reg_date']?></span>
                                    </div>
                                    <a href="javascript:;" class="btn_cancel" onclick="delChk(<?=$v['uid']?>);">구독취소</a>
                                </div>
                                <div class="row <?=($pic) ? 'pic' : ''?>">
                                    <? if ($pic) { ?>
									<div class="picture">
										<img src="/data/contents/<?=$pic?>" alt="" />
									</div>
                                    <? } ?>

                                    <div class="txt">
                                        <a href="/lounge/<?=$v['uid']?>/contents/<?=$v['cidx']?>" class="subject"><p><?=$v['subject']?><span class="reply_cnt"></span></p></a>
                                        <p class="summary" style="height:50px;overflow: hidden">
                                            <?=substr($v['contents'], 0, 500);?>
                                        </p>
                                    </div>
                                </div>
                            </li> -->
                            <?
                            }   // end foreach
							?>
</ul>
							<?
                            if ($more) {
                            ?>
								 <a href="javascript:;" class="btn_list_more">+ 더보기</a>
							<?
							}
							?>
						<?
						}else {
                        ?>
                            <li style="line-height:30px;text-align:center"><strong>데이터가 없습니다.</strong></li>
                        <?
                        }
                        ?>
