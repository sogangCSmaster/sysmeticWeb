
                                        <?php foreach($daily_values as $v){ ?>
                                        <tr>
                                            <td><?php echo $v['basedate'] ?></td>
                                            <td class="right"><?php echo number_format($v['principal']) ?></td>
                                            <td class="right"><?php echo number_format($v['flow']) ?></td>
                                            <td class="right"><?php echo number_format($v['daily_pl']) ?></td>
                                            <td class="right"><?php echo number_format($v['daily_pl_rate'],2,'.','') ?>%</td>
                                            <td class="right"><?php echo number_format($v['acc_pl']) ?></td>
                                            <td class="right"><?php echo number_format($v['acc_pl_rate'],2,'.','') ?>%</td>
                                            <td>
                                                <button type="button" class="btn modify" onclick="editData('<?php echo $v['basedate'] ?>', '<?php echo number_format($v['balance']) ?>', '<?php echo number_format($v['flow']) ?>', '<?php echo number_format($v['daily_pl']) ?>')" title="수정"><span class="ir">수정</span></button>
                                                <button type="button" class="btn delete" onclick="deleteData('<?php echo $v['basedate'] ?>')" title="삭제"><span class="ir">삭제</span></button>
                                            </td>
                                        </tr>
                                        <?php } ?>
										<!-- <tr>
                                            <td>2015-03-12</td>
                                            <td class="right">100,000,000</td>
                                            <td class="right">0</td>
                                            <td class="right">332,410</td>
                                            <td class="right">0.33%</td>
                                            <td class="right">302,280</td>
                                            <td class="right">0.30%</td>
                                            <td>
                                                <a href="javascript:;" class="btn modify" onclick="commonLayerOpen('day_data_modify')">수정</a>
                                                <a href="javascript:;" class="btn delete" onclick="commonLayerOpen('day_data_delete')">삭제</a>
                                            </td>
                                        </tr> -->