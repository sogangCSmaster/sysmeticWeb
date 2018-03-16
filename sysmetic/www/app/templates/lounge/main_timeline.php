
                        <ul class="tl_type">
                            <? foreach ($lists as $v) {

								$content = $v['contents'];
								$img_array = str_img($content);
								$bgImg = $img_array[0];

                                if (!$bgImg) {
                                    $bgImg = '/images/img_lounge_sample'.rand(0, 2).'.jpg';
                                }

                                $bg = "background:url('$bgImg') no-repeat center; background-size:cover;";
                            ?>
                            <li>
                                <div class="info">
                                    <div class="photo">
                                        <img src="<?=getProfileImg($v['picture'])?>" alt="" />
                                    </div>
                                    <div class="name">
                                        <strong><?=$v['name']?></strong>
                                        <a href="/lounge/<?=$v['uid']?>"><img src="/images/sub/btn_lounge_coffee.gif" alt="" /></a>
                                    </div>
                                    <p class="time"><?=substr($v['reg_date'], 0, 16)?></p>
                                </div>
                                <div class="cont" style="cursor: pointer;<?=$bg?>" onclick="window.location='/lounge/<?=$v['uid']?>/contents/<?=$v['cidx']?>';">
                                    <p class="subject"><?=$v['subject']?></p>
                                    <p class="summary" style="height:40px;overflow: hidden"><?=substr(strip_tags($v['contents']), 0, 400);?></p>
                                    <div class="btn_area">
                                        <a href="/lounge/<?=$v['uid']?>/contents/<?=$v['cidx']?>" class="btn_view_all">전체 글 읽기</a>
                                    </div>
                                    <img src="/images/sub/img_lounge_left_arrow.png" alt="" class="ico_arrow" />
                                </div>
                            </li>
                            <? } ?>
                        </ul>
                        <? if ($next) { ?>
                        <a href="javascript:;" class="btn_list_more" onclick="$(this).hide();getContent('timeline', <?=$next?>);">+ 더보기</a>
                        <? } ?>
