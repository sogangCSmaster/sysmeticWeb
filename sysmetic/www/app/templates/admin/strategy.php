<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 전략 관리</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css?<?=time()?>" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js?<?=time()?>"></script>

    <script>

    <?php if(!empty($flash['error'])){ ?>
    alert('<?php echo htmlspecialchars($flash['error']) ?>');
    <?php } ?>

    </script>
</head>

<body>

	  <?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">상품 관리</h3>

            <div class="strategy_view">
                <div class="tab">
                    <a href="/admin/strategies" title="종목관리" class="tab_on"><span class="ir">상품</span></a>
                    <a href="/admin/portfolios" title="종류관리" class="tab_off"><span class="ir">포트폴리오</span></a>
                    <a href="/admin/strategies_op" title="종목관리" class="tab_off" style='width:160px'><span style='width:160px'>상품승인요청 <strong style='color:#ff0000'>(<?=number_format($op_cnt)?>)</strong></span></a>
                </div>
            </div>

			<form action="/admin/strategies" method="get">
            <fieldset class="admin_search">
            <div class="select open" style="width:115px;">
                <div class="myValue"></div>
                <ul class="iList">
                    <li><input name="q_type" id="search1" class="option" type="radio" value="name"<?php if($q_type == 'name') echo ' checked="checked"' ?> /><label for="search1">상품명</label></li>
                    <li><input name="q_type" id="search2" class="option" type="radio" value="trader"<?php if($q_type == 'trader') echo ' checked="checked"' ?> /><label for="search2">트레이더</label></li>
                    <li><input name="q_type" id="search3" class="option" type="radio" value="pb"<?php if($q_type == 'pb') echo ' checked="checked"' ?> /><label for="search3">PB</label></li>
                    <li><input name="q_type" id="search4" class="option" type="radio" value="broker"<?php if($q_type == 'broker') echo ' checked="checked"' ?> /><label for="search4">중개사</label></li>
                </ul>
            </div> 

            <input id="q" name="q" type="text" title="검색어 입력" value="<?php if(!empty($q)) echo htmlspecialchars($q) ?>" />
            <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            
            <div style="clear:both;margin-top:10px"></div>

            <div class="select open" style="width:115px;">
                <div class="myValue"></div>
                <ul class="iList">
                    <li><input name="q_stats" id="search10" class="option" type="radio" value="" <?=($q_stats == '') ? 'checked' : '' ?> /><label for="search10">상태선택</label></li>
                    <li><input name="q_stats" id="search11" class="option" type="radio" value="is_open_1" <?=($q_stats == 'is_open_1') ? 'checked' : '' ?> /><label for="search11">공개중</label></li>
                    <li><input name="q_stats" id="search12" class="option" type="radio" value="is_open_0" <?=($q_stats == 'is_open_0') ? 'checked' : '' ?> /><label for="search12">비공개</label></li>
                    <li><input name="q_stats" id="search13" class="option" type="radio" value="is_operate_1" <?=($q_stats == 'is_operate_1') ? 'checked' : '' ?> /><label for="search13">운용중</label></li>
                    <li><input name="q_stats" id="search14" class="option" type="radio" value="is_operate_0" <?=($q_stats == 'is_operate_0') ? 'checked' : '' ?> /><label for="search14">운용중지</label></li>
                </ul>
            </div>

            <div class="select open" style="width:115px;">
                <div class="myValue"></div>
                <ul class="iList">
                    <li><input name="q_types" id="search20" class="option" type="radio" value="" <?=($q_types == '') ? 'checked' : '' ?> /><label for="search20">종류선택</label></li>
                    <? foreach ($types as $k => $v) { ?>
                    <li><input name="q_types" id="search2<?=$k+1?>" class="option" type="radio" value="<?=$v['type_id']?>" <?=($q_types == $v['type_id']) ? 'checked' : '' ?> /><label for="search2<?=$k+1?>"><?=$v['name']?></label></li>
                    <? } ?>
                </ul>
            </div>

            <div class="select open" style="width:115px;">
                <div class="myValue"></div>
                <ul class="iList">
                    <li><input name="q_term" id="search30" class="option" type="radio" value="" <?=($q_term == '') ? 'checked' : '' ?> /><label for="search30">주기선택</label></li>
                    <li><input name="q_term" id="search31" class="option" type="radio" value="day" <?=($q_term == 'day') ? 'checked' : '' ?> /><label for="search31">데이</label></li>
                    <li><input name="q_term" id="search32" class="option" type="radio" value="position" <?=($q_term == 'position') ? 'checked' : '' ?>/><label for="search32">포지션</label></li>
                </ul>
            </div>

            <div class="select open" style="width:115px;">
                <div class="myValue"></div>
                <ul class="iList">
                    <li><input name="q_grp" id="search40" class="option" type="radio" value="" <?=($q_grp == '') ? 'checked' : '' ?> /><label for="search40">관심그룹</label></li>
                    <? foreach ($grps as $k => $v) { ?>
                    <li><input name="q_grp" id="search4<?=$k+1?>" class="option" type="radio" value="<?=$v['grp_id']?>" <?=($q_grp == $v['grp_id']) ? 'checked' : '' ?> /><label for="search4<?=$k+1?>"><?=$v['name']?></label></li>
                    <? } ?>
                </ul>
            </div>

            <button id="search_btn" type="submit" title="검색"><span class="ir">선택목록보기</span></button>
            </fieldset>
			</form>

            <!--a href="/admin/strategies/write" title="전략 등록" class="write btn_admin"><span class="ir">전략등록</span></a-->
            <!-- <button type="button" title="관심그룹관리" class="admin" onclick="location.href='';"><span class="ir">관심그룹관리</span></button> -->
            <a href="/admin/strategies_grp" title="관심그룹관리" class="write btn_admin2" target="_blank"><span class="ir">관심그룹관리</span></a>
            <a href="/fund/strategies/write" title="전략 등록" class="write btn_admin" target="_blank"><span class="ir">상품등록</span></a>


			<form action="/admin/strategies_grp/set" method="post">
            <input type="hidden" name="q_type" value="<?=$q_type?>" />
            <input type="hidden" name="q" value="<?=$q?>" />
            <input type="hidden" name="q_stats" value="<?=$q_stats?>" />
            <input type="hidden" name="q_types" value="<?=$q_types?>" />
            <input type="hidden" name="q_term" value="<?=$q_term?>" />
            <input type="hidden" name="q_grp" value="<?=$q_grp?>" />

            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="40" /><col width="40" /><col width="*" /><col width="80" /><col width="80" /><col width="90" />
            <col width="60" /><col width="80" /><col width="70" /><col width="70" /><col width="80" />
            <col width="100" />
                <thead>
                <tr>
                    <td>선택</td>
                    <td class="num">No</td>
                    <!--td>종류</td-->
                    <td>상품명</td>
                    <td>트레이더</td>
                    <td>PB</td>
                    <td>중계사</td>
                    <td>상태</td>
                    <td>공개여부</td>
                    <td>등록일</td>
                    <td>업데이트</td>
                    <td>상세보기</td>
                    <td>관리</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($strategies as $strategy){ ?>
                <tr>
                    <td><p><input type="checkbox" name="strategy_ids[]" id="choice<?php echo $strategy['strategy_id'] ?>" value="<?php echo $strategy['strategy_id'] ?>" /><label for="choice<?php echo $strategy['strategy_id'] ?>"></label></p></td>
                    <td class="num"><?php echo htmlspecialchars($strategy['strategy_id']) ?></td>
                    <!--td>
					<?php if($strategy['strategy_type'] == 'S'){ ?>
					<img src="../img/ico_system.gif" />
					<?php }else if($strategy['strategy_type'] == 'M'){ ?>
					<img src="../img/ico_menual.gif" />
					<?php }else if($strategy['strategy_type'] == 'H'){ ?>
					<img src="../img/ico_hybrid.gif" />
					<?php }else{ ?>
					대기
					<?php } ?>
					</td-->
                    <td><a href="/investment/strategies/<?php echo $strategy['strategy_id'] ?>" target="_blank"><?php echo htmlspecialchars($strategy['name']) ?></a></td>
                    <td><?php if(!empty($strategy['developer']['nickname'])) echo htmlspecialchars($strategy['developer']['nickname']); else echo htmlspecialchars($strategy['developer_name']) ?></td>
                    <td><?=$strategy['pb']['name']?></td>
                    <td><?=$strategy['broker_name']?></td>
                    <td>
					<?php if($strategy['is_operate']){ ?>
					운용중
					<?php }else{ ?>
					운용중지
					<?php } ?>
					</td>
                    <td>
					<?php if($strategy['is_open']){ ?>
					<button type="button" title="공개" class="complete"><span class="ir">공개</span></button>
					<?php }else{ ?>
					<button type="button" title="비공개" class="waiting"><span class="ir">비공개</span></button>
					<?php } ?>
					</td>
                    <td><?=substr($strategy['reg_at'], 2, 8)?></td>
                    <td><?=substr($strategy['mod_at'], 2, 8)?></td>
                    <td class="btn">
                        <a href="/strategies/<?php echo $strategy['strategy_id'] ?>" title="상세보기" class="btn_view" target="_blank"><span class="ir">상세보기</span></a>
                        <!--a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>?page=<?php echo $page ?>" title="상세보기" class="btn_view"><span class="ir">상세보기</span></a-->
                    </td>
                    <td>
                        <a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>?page=<?php echo $page ?>" title="삭제" class="sbtn"><span class="ir">수정</span></a>
                        <a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/delete" title="삭제" class="sbtn" onclick="return confirm('삭제하시겠습니까?');"><span class="ir">삭제</span></a>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <fieldset class="admin">
                <span class="fieldset_txt">선택한 상품을 </span>
                <div class="select open" style="width:100px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="grp_id" id="grp0" class="option" type="radio" value="" checked="checked" /><label for="member0">관심그룹</label></li>
                        <? foreach ($grps as $k => $v) { ?>
                        <li><input name="grp_id" id="grp<?=$k+1?>" class="option" type="radio" value="<?=$v['grp_id']?>" /><label for="grp<?=$k+1?>"><?=$v['name']?></label></li>
                        <? } ?>
                    </ul>
                </div> 
                &nbsp;으로 &nbsp;&nbsp;
                <button type="submit" title="추가" class="admin1" id="update"><span class="ir">추가</span></button>
            </fieldset>
			</form>


            <?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/strategies?page=1<?=$q_str?>"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/strategies?page=<?php echo $page_start-1 ?><?=$q_str?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/strategies?page=<?php echo $i ?><?=$q_str?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/strategies?page=<?php echo $page_start + $page_count ?><?=$q_str?>" class="next">next</a><?php } ?>
                <a href="/admin/strategies?page=<?php echo $total_page ?><?=$q_str?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>


        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
