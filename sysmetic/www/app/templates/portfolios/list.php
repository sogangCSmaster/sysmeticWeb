

                        <?
                        if (count($portfolios)) {
                            $idx = 0;
                            foreach($portfolios as $portfolio) {
                                $idx++;

                                $picture = getProfileImg($portfolio['user']['picture']);
                        ?>
                        <li>
                            <a>
                                <div class="user_info">
                                    <div class="photo"><img src="<?=$picture?>" alt=""></div>
                                    <p class="nickname"><?=$portfolio['user']['nickname']?></p>
                                    <? if (!$search_type && !$uid) { ?>
                                    <span class="rank no<?=$portfolio['idx'] + 1?>"><?=sprintf("%02d", $portfolio['idx'] + 1)?></span>
                                    <? } ?>
                                </div>
                                <div class="portfolio_info">
                                    <p class="subject"><span style="cursor:pointer" onclick="location.href='/investment/portfolios/<?=$portfolio['portfolio_id']?>';"><?php echo htmlspecialchars($portfolio['name']) ?></span></p>
                                    <div class="etc_info">
                                        <dl class="left">
                                            <dt>투자원금</dt>
                                            <dd><?=number_format($portfolio['amount'])?></dd>
                                        </dl>
                                        <dl class="center">
                                            <dt>투자수익</dt>
                                            <dd><?=number_format(($portfolio['amount'] * $portfolio['total_pl_rate']) / 100)?></dd>
												<!-- <dd><?=number_format($portfolio['result_amount'])?></dd> -->
                                        </dl>
                                        <dl class="right">
                                            <dt>평가금액</dt>
                                            <dd><?=number_format($portfolio['amount'] + (($portfolio['amount'] * $portfolio['total_pl_rate']) / 100))?></dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="chart">
                                    <!-- 실제 차트 적용 시 아래 bg 클래스 삭제해주세요 size : 127 * 119px -->
                                    <div class="chart_area" id="portfolio_graph<?php echo $portfolio['portfolio_id'] ?>" data-role="portfolio_graph" data-graph-data="<?php echo $portfolio['str_unified_sm_index'] ?>">
                                    </div>
                                </div>
                                <div class="cnt_stat">
                                    <div class="row top">
                                        <dl>
                                            <dt>누적 수익률</dt>
											<dd><?php echo number_format($portfolio['total_pl_rate'],2,'.','') ?> %</dd>
                                            <!-- <dd><?php echo number_format($portfolio['total_profit_rate'],2,'.','') ?> %</dd> -->
                                        </dl>
                                        <dl>
                                            <dt>MDD</dt>
                                            <dd><?php echo number_format($portfolio['mdd_rate'],2,'.','') ?> %</dd>
                                        </dl>
                                    </div>
                                    <div class="row bottom">
                                        <dl>
                                            <dt>기간</dt>
                                            <dd><?php echo substr($portfolio['start_date'], 0, 4).'.'.substr($portfolio['start_date'], 4, 2).'.'.substr($portfolio['start_date'], 6, 2) ?> ~ <?php echo substr($portfolio['end_date'], 0, 4).'.'.substr($portfolio['end_date'], 4, 2).'.'.substr($portfolio['end_date'], 6, 2) ?></dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="follow_info">
                                    <dl>
                                        <dt>Followers</dt>
                                        <dd id="follows_count<?=$portfolio['portfolio_id']?>"><?php echo number_format($portfolio['followers_count']) ?></dd>
                                    </dl>

                                    <? if ($search_type != 'mypage') { ?>
                                        <? if ($isLoggedIn()) { ?>
                                            <? if ($portfolio['is_following']) { ?>
                                            <button id="btn_follow<?=$portfolio['portfolio_id']?>" type="button" class="btn_unfollow" data-role="unfollow" data-portfolio-id="<?=$portfolio['portfolio_id']?>" data-portfolio-name="<?=htmlspecialchars($portfolio['name'])?>">unFollow -</button>
                                            <? } else if ($portfolio['uid'] == $_SESSION['user']['uid']) { ?>
                                            <button type="button" class="btn_follow" data-role="mine">Follow +</button>
                                            <? } else { ?>
                                            <button id="btn_follow<?=$portfolio['portfolio_id']?>" type="button" class="btn_follow" data-role="followForm" data-portfolio-id="<?=$portfolio['portfolio_id']?>" data-portfolio-name="<?=htmlspecialchars($portfolio['name'])?>">Follow +</button>
                                            <? } ?>
                                        <? } else { ?>
                                        <button type="button" class="btn_follow" data-role="login">Follow +</button>
                                        <? } ?>
                                    <? } else { ?>
                                    
                                        <div class="btns">
                                            <button type="button" class="btn manage" data-role="manage" data-portfolio-id="<?=$portfolio['portfolio_id']?>">관리</button>
                                            <button type="button" class="btn delete" data-role="delete" data-portfolio-id="<?=$portfolio['portfolio_id']?>">삭제</button>
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