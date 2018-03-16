
                                        <?php foreach($daily_values as $v){ ?>
                                        <tr>
                                            <td><?php echo substr($v['target_date'], 0, 4).'.'.substr($v['target_date'], 4, 2).'.'.substr($v['target_date'], 6, 2) ?></td>
                                            <td><?php echo number_format($v['money']) ?></td>
                                            <td><?php echo number_format($v['investor']) ?></td>
                                            <td>
                                                <a href="javascript:;" class="btn modify" onclick="editData('<?php echo substr($v['target_date'], 0, 4).'.'.substr($v['target_date'], 4, 2).'.'.substr($v['target_date'], 6, 2) ?>', '<?php echo number_format($v['money']) ?>', '<?php echo number_format($v['investor']) ?>')"">수정</a>
                                                <a href="javascript:;" class="btn delete"  onclick="deleteData('<?php echo $v['target_date'] ?>')">삭제</a>
                                            </td>
                                        </tr>
                                        <?php } ?>