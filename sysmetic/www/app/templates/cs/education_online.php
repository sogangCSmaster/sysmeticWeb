                                <?
                                $i = 0;
                                foreach ($lists as $v) {
                                    $i++;
                                    $class = ($i%4 == 1) ? 'left': '';

                                    
                                    if ($v['save_name']) {
                                        $bgImg = '/education/'.$v['save_name'];
                                    } else {
                                        $bgImg = '/images/img_edu_list_sample0'.rand(1, 3).'.jpg';
                                    }

                                ?>
								<li class="<?=$class?>">
									<a href="/cs/education/<?=$v['eidx']?>">
										<div class="pic">
											<!-- 게시물 이미지 -->
											<img src="<?=$bgImg?>" alt="" class="data" />
											<!-- //게시물 이미지 -->
											<img src="/images/sub/img_logo_trans.png" alt="" class="logo" />
										</div>
										<p class="subject"><?=$v['subject']?></p>
										<p class="datetime"><?=$v['reg_date']?></p>
									</a>
								</li>
                                <? } ?>