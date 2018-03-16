
                        <?
                        if (count($strategies)) {
                            $idx = 0;
                            foreach($strategies as $strategy) {
                                $idx++;
                        ?>
								<li class="<?=$i/2 ? 'left' : ''?>">
									<div class="chart_area" id="strategy_graph<?php echo $strategy['strategy_id'] ?>" data-role="strategy_graph" data-graph-data="<?php echo $strategy['str_c_price'] ?>" >
									</div>
									<p class="subject">
										<?php echo htmlspecialchars($strategy['name']) ?>
									</p>
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

                                        if ($v['item_id'] == 9 || $v['item_id'] == 10) {
                                            $over = 1;
                                        } else {
                                            $over = 0;
                                        }
                                    ?>
                                    <?
                                    }
                                    ?>
                                    <span class="category"><img src="<?=$strategy['items'][0]['icon']?>" /><?//=$strategy['items'][0]['name']?></span>
									<input type="checkbox" id="check<?=$strategy['strategy_id']?>" name="strategy_ids[]" value="<?=$strategy['strategy_id']?>" />
									<label for="check<?=$strategy['strategy_id']?>" onclick="chkV('<?=$strategy['strategy_id']?>', '<?=$over?>');"></label>
                                    
									<input type="hidden" id="exchange_strategy_<?=$strategy['strategy_id']?>" name="exchange_strategy_<?=$strategy['strategy_id']?>" value="" />
								</li>
                        <?
                            }
                        }
                        ?>