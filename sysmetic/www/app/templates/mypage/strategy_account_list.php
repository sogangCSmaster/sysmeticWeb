
                                    <?
                                    $idx = 0;
                                    foreach($daily_values as $v){
                                    ?>
									<li class="<?=($idx%5) == 0 ? 'left' : ''?>">
										<div class="picture">
											<img src="<?php echo $v['image'] ?>" alt=""  onclick="openImage(this.src)" />
										</div>
										<p class="txt">
											<strong><?php echo $v['title'] ?></strong>
										</p>
										<input type="checkbox"  name="account_ids[]" id="choice<?php echo $v['account_id'] ?>" value="<?php echo $v['account_id'] ?>" />
										<label for="choice<?php echo $v['account_id'] ?>" title="선택"></label>
									</li>
                                    <?
                                        $idx++;
                                    }
                                    ?>