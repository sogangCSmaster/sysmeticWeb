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
                                    <p><?=$v['subject']?></p>
                                </a>
                                <div class="info_box">
                                    <div class="photo"><img src="<?=getProfileImg($v['picture'])?>" alt="" /></div>
                                    <div class="info">
                                        <div class="name">
                                            <strong><?=$v['name']?></strong>
                                            <a href="/lounge/<?=$v['uid']?>"><img src="../images/sub/btn_lounge_coffee.gif" alt="" /></a>
                                        </div>
                                        <p class="time"><?=substr($v['reg_date'], 0, 16)?></p>
                                    </div>
                                </div>
                            </li>
                            <? } ?>
                        </ul>
                        <? if ($next) { ?>
                        <a href="javascript:;" class="btn_list_more" onclick="$(this).hide();getContent('thumb', <?=$next?>);">+ 더보기</a>
                        <? } ?>
