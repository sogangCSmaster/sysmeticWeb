                    <?
                    if (count($strategies)) {
                        foreach($strategies as $strategy) {
                            $strategies_link = '/strategies/'.$strategy['strategy_id'];
                            if ($developer['uid']) $strategies_link .= '?developer_uid='.$developer['uid'];

                            $strategy_term = strtoupper($strategy['strategy_term']);
                            //$picture = ($strategy['developer']['picture']) ? htmlspecialchars($strategy['developer']['picture']) : "/img/over_s1.png";
                            $picture = getProfileImg($strategy['developer']['picture']);
                    ?>
                        <li>
                            <a href="javascript:;" style='cursor:default'>
                                <div class="user_info">
                                    <div class="photo"><img src="<?=$picture?>" alt="" /></div>
                                    <p class="nickname"><?php if(empty($strategy['developer']['nickname'])) echo htmlspecialchars($strategy['developer_name']); else echo htmlspecialchars($strategy['developer']['nickname']) ?></p>
                                </div>
                                <div class="strategy">
                                    <p class="subject">
									 <?
										if ($search_type == 'mypage') {
											if($strategy['is_open']){
												echo "<font color='#3300ff'>[공개]</font>";
											}else{
												echo "<font color='#ff0000'>[비공개]</font>";
											}
										}
									 ?>
                                    <? if ($strategy['kind']) { ?><span class="category">[<?=$strategy['kind']?>]</span><? } ?>
                                    <span style="cursor:pointer" onclick="location.href='<?=$strategies_link?>'"><?php echo htmlspecialchars($strategy['name']) ?></span></p>
                                    <div class="options" style="height:50px;">
                                        <!--span class="op_s"><?=$strategy['strategy_type']?></span-->
                                        <img src="<?=$strategy['types']['icon']?>" />
										<?
										if($strategy_term[0]=="D")echo "<img src='/images/ico_day.gif'>";
										if($strategy_term[0]=="P")echo "<img src='/images/ico_position.gif'>";
										?>
                                        <!-- <span class="op_d"><?=$strategy_term[0]?></span> -->
                                        <?
                                        foreach ($strategy['items'] as $k => $v) {
                                            switch ($v['name']) {
                                                case "주식/ETF": $class="op_etf"; break;
                                                case "K200선물": $class="op_k200_sun"; break;
                                                case "K200옵션" : $class="op_k200_op"; break;
                                                case "해외선물" : $class="op_out_sun"; break;
                                                case "해외옵션" : $class="op_out_op"; break;
                                                default : $class="";
                                            }
                                        ?>
                                        <!--span class="<?=$class?>"><?=$v['name']?></span-->
                                        <img src="<?=$v['icon']?>" />
                                        <?
                                        }
                                        ?>
                                    </div>
                                    <div class="etc_info" style="height:50px;">
                                        <?
										if ($strategy['pb']['name']) { 
											$strategy['pb']['picture'] = getProfileImg($strategy['pb']['picture']);
										?>
                                        <dl>
                                            <dt>PB</dt>
											<dd onclick="window.location='/lounge/<?=$strategy['pb']['uid']?>'" style="cursor:pointer"><img src="<?=$strategy['pb']['picture']?>" width=25 alt="" style="border-radius: 50em;" /><?=$strategy['pb']['name']?></dd>
                                        </dl>
                                        <? }else{ ?>
                                        <dl>
                                            <dt>PB</dt>
											<dd>없음</dd>
                                        </dl>
                                        <? } ?>
                                        <dl>
                                            <dt>중개사</dt>
                                            <dd><?php if(!empty($strategy['broker']['logo_s'])){ ?><img src="<?php echo $strategy['broker']['logo_s'] ?>" style="height:25px;" /><?php }else{ ?><?php echo htmlspecialchars($strategy['broker']['company']) ?><?php } ?>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="chart">
                                    <!-- 실제 차트 적용 시 아래 bg 클래스 삭제해주세요 size : 127 * 119px -->
                                    <div class="chart_area" id="strategy_graph<?php echo $strategy['strategy_id'] ?>" data-role="strategy_graph" data-graph-data="<?php echo $strategy['str_c_price'] ?>" >
                                    </div>
                                </div>
                                <div class="cnt_stat">
                                    <dl>
                                        <dt>누적 수익률</dt>
                                        <dd><?php if(count($strategy['daily_values'])) echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['acc_pl_rate'],2);else echo '0' ?> %</dd>
                                    </dl>
                                    <dl>
                                        <dt>최근 1년 수익률</dt>
                                        <dd><?php if(isset($strategy['daily_values'])) echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['one_yr_pl_rate'],2); else echo '0' ?> %</dd>
                                    </dl>
                                </div>
                                <div class="cnt_stat">
                                    <dl>
                                        <dt>SM Score</dt>
                                        <dd><?php if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'])) echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['sm_score'],2); else echo '0' ?></dd>
                                    </dl>
                                    <dl>
                                        <dt>최대 자본인하율</dt>
                                        <dd>
                                        <?//echo $strategy['currency'] ?>
                                        <?php echo percent_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd_rate'],2) ?> %
                                        <?php// if(isset($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd'])) echo number_format($strategy['daily_values'][count($strategy['daily_values'])-1]['mdd']); else echo '0'; ?></dd>
                                    </dl>
                                </div>
                                <div class="follow_info">
                                    <dl>
                                        <dt>Followers</dt>
                                        <dd id="follows_count<?=$strategy['strategy_id']?>"><?php echo number_format($strategy['followers_count']) ?></dd>
                                    </dl>
                                    
                                    <? if ($search_type != 'mypage') { ?>
                                        <? if ($isLoggedIn()) { ?>
                                            <? if ($strategy['is_following']) { ?>
                                            <button id="btn_follow<?=$strategy['strategy_id']?>" type="button" class="btn_unfollow" data-role="unfollow" data-strategy-id="<?=$strategy['strategy_id']?>" data-strategy-name="<?=htmlspecialchars($strategy['name'])?>">unFollow -</button>
                                            <? } else if ($strategy['is_mine']) { ?>
                                               <button type="button" class="btn_follow" data-role="mine">Follow +</button>
                                            <? } else { ?>
                                            <button id="btn_follow<?=$strategy['strategy_id']?>" type="button" class="btn_follow" data-role="followForm" data-strategy-id="<?=$strategy['strategy_id']?>" data-strategy-name="<?=htmlspecialchars($strategy['name'])?>">Follow +</button>
                                            <? } ?>
                                        <? } else { ?>
                                        <button type="button" class="btn_follow" data-role="login">Follow +</button>
                                        <? } ?>
                                    <? } else { ?>
                                    
                                        <div class="btns">
                                            <button type="button" class="btn manage" data-role="manage" data-strategy-id="<?=$strategy['strategy_id']?>">관리</button>
                                            <button type="button" class="btn delete" data-role="delete" data-strategy-id="<?=$strategy['strategy_id']?>">삭제</button>
                                        </div>

                                    <? } ?>
                                </div>
                            </a>
                        </li>
                        <?
                        }   // end foreach
                        if ($more) {
                        ?>
                        <a href="javascript:;" class="btn_list_more">+ 더보기</a>
                    <?
                        }
                    }   // end if
                    else
                    {
                    ?>
                        <li class='endList' style="line-height:30px;text-align:center"><strong>데이터가 없습니다.</strong></li>
                    <?
                    }
                    ?>