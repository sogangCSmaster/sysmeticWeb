<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 전략 관리</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#edit_stg_form').submit(function(){
			if($('#edit_stg_form input[type=checkbox]:checked').length == 0){
				alert('선택된 상품이 없습니다');
				return false;
			}

			if(!confirm('승인하시겠습니까?')){
				return false;
			}
		});
	});
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
                    <a href="/admin/strategies" title="종목관리" class="tab_off"><span class="ir">상품</span></a>
                    <a href="/admin/portfolios" title="종류관리" class="tab_off"><span class="ir">포트폴리오</span></a>
                    <a href="/admin/strategies_op" title="종목관리" class="tab_on" style='width:160px'><span style='width:160px'>상품승인요청 <strong style='color:#ff0000'>(<?=number_format($op_cnt)?>)</strong></span></a>
                </div>
            </div>

			<form action="/admin/strategies_op" method="get">
            <fieldset class="admin_search">
                <div class="select open" style="width:115px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="q_type" id="search1" class="option" type="radio" value="name"<?php if($q_type == 'name') echo ' checked="checked"' ?> /><label for="search1">상품명</label></li>
                        <li><input name="q_type" id="search2" class="option" type="radio" value="trader"<?php if($q_type == 'trader') echo ' checked="checked"' ?> /><label for="search2">트레이더</label></li>
                        <li><input name="q_type" id="search3" class="option" type="radio" value="pb"<?php if($q_type == 'pb') echo ' checked="checked"' ?> /><label for="search3">PB</label></li>
                    </ul>
                </div> 
            <input id="q" name="q" type="text" title="검색어 입력" value="<?php if(!empty($q)) echo htmlspecialchars($q) ?>" />
            <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>

			<form action="/admin/strategies/operate" method="post" id="edit_stg_form">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="50" /><col width="50" /><col width="*" /><col width="90" /><col width="90" />
            <col width="110" /><col width="60" /><col width="80" /><col width="100" />
                <thead>
                <tr>
                    <td class="num">선택</td>
                    <td class="num">No</td>
                    <td>상품명</td>
                    <td>트레이더</td>
                    <td>PB</td>
                    <td>운용사</td>
                    <td>상태</td>
                    <td>등록일</td>
                    <td>상세보기</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($strategies as $strategy){ ?>
                <tr>
                    <td class="num"><input type="checkbox" name="ids[]" value="<?=$strategy['strategy_id']?>" /></td>
                    <td class="num"><?php echo htmlspecialchars($strategy['strategy_id']) ?></td>
                    <td><a href="/investment/strategies/<?php echo $strategy['strategy_id'] ?>" target="_blank"><?php echo htmlspecialchars($strategy['name']) ?></a></td>
                    <td><?php if(!empty($strategy['developer']['name'])) echo htmlspecialchars($strategy['developer']['name']); else echo htmlspecialchars($strategy['developer_name']) ?></td>
                    <td><?=$strategy['pb_name']?></td>
                    <td><?=$strategy['broker_name']?></td>
                    <td>
					<?php if($strategy['is_operate']){ ?>
					운용중
					<?php }else{ ?>
					운용중지
					<?php } ?>
					</td>
                    <td><?=substr($strategy['reg_at'], 2, 8)?></td>
                    <td class="btn">
                        <a href="/investment/strategies/<?php echo $strategy['strategy_id'] ?>" title="상세보기" class="btn_view" target="_blank"><span class="ir">상세보기</span></a>
                        <!--a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>?page=<?php echo $page ?>" title="상세보기" class="btn_view"><span class="ir">상세보기</span></a-->
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <fieldset class="admin">
                <span class="fieldset_txt">선택한 상품을</span>
                <button type="submit" title="승인" class="admin1"><span class="ir">승인</span></button>
            </fieldset>
			</form>

            <?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/strategies_op?page=1<?=$q_str?>"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/strategies_op?page=<?php echo $page_start-1 ?><?=$q_str?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/strategies_op?page=<?php echo $i ?><?=$q_str?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/strategies_op?page=<?php echo $page_start + $page_count ?><?=$q_str?>" class="next">next</a><?php } ?>
                <a href="/admin/strategies_op?page=<?php echo $total_page ?><?=$q_str?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>

        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
