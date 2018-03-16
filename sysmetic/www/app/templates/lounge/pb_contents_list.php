                        <ul class="tn_type">
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
                                <a href="/lounge/<?=$v['uid']?>/contents/<?=$v['cidx']?>" class="subject" style="<?=$bg?>">
                                    <p>
										<?=$v['subject']?>
										<?
										if($v['filecnt'])echo " <img src='/images/sub/ico_folder.png'>";
										?>
									</p>
                                </a>
                            </li>
                            <? } ?>
                        </ul>
                        <? if ($next) { ?>
                        <a href="javascript:;" class="btn_list_more" onclick="$(this).hide();getContent('thumb', <?=$next?>);">+ 더보기</a>
                        <? } ?>
