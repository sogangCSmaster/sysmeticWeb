
                                    <? foreach ($lists as $list) { 
                                        $picture = getProfileImg($list['picture']);
                                    ?>
									<li class="lst">
										<div class="photo">
											<img src="<?=$picture?>" alt="" />
										</div>
										<div class="detail">
											<div class="row">
												<div class="left_a">
													<strong class="name"><?=$list['nickname']?></strong>
                                                    <? if ($list['user_type'] == 'P') { ?>
													<a href="/lounge/<?=$list['uid']?>" class="btn_lounge"><img src="/images/sub/ico_list_coffee.gif" alt="lounge" /></a>
                                                    <? } ?>
													<span class="date"><?=$list['reg_date']?></span>
												</div>
												<div class="right_a">
													<a href="javascript:;" class="btn rep">댓글달기</a>
                                                    <? if ($list['uid'] == $_SESSION['user']['uid']) { ?>
													<a href="javascript:;" class="btn mod">수정하기</a>
													<a href="javascript:;deleteFrm(<?=$list['cid']?>);" class="btn_delete"><img src="/images/sub/btn_review_delete.gif" alt="delete" /></a>
                                                    <? } ?>
												</div>
											</div>
											<p class="r_cont">
                                            <? if ($list['secret']) { ?>
                                                <? if ($list['uid'] == $_SESSION['user']['uid'] || $info['uid'] == $_SESSION['user']['uid']) { ?>
                                                    <span class="secret">[비공개]</span> <?=nl2br($list['contents'])?>
                                                <? } else { ?>
                                                    <span class="secret">[비공개 댓글 입니다.]</span>
                                                <? } ?>
                                            <? } else { ?>
                                                <?=nl2br($list['contents'])?>
                                            <? } ?>
                                            </p>
                                            
                                            <form id="f<?=$list['cid']?>" method="post" onsubmit="modifyFrm(this);return false;">
                                            <div class="modify_form">
                                                <div class="textarea_box">
                                                    <input type="hidden" name="cid" value="<?=$list['cid']?>" />
                                                    <textarea name="contents"><?=$list['contents']?></textarea>
                                                </div>
                                                <button type="submit" class="btn_modify_confirm">수정</button>
                                            </div>
                                            </form>

										</div>
									</li>
                                    <? } ?>

<script>
// reply modify
$('.btn.mod').on('click', function(){
	if($(this).closest('.detail').hasClass('modify')){
		$(this).closest('.detail').removeClass('modify');
		$(this).text('수정하기');
	}else{
		$(this).closest('.detail').addClass('modify');
		$(this).text('수정취소');
	}
});
</script>
