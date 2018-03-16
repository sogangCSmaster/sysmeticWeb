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
                    <a href="/admin/portfolios" title="종류관리" class="tab_on"><span class="ir">포트폴리오</span></a>
                    <a href="/admin/strategies_op" title="종목관리" class="tab_off" style='width:160px'><span style='width:160px'>상품승인요청 <strong style='color:#ff0000'>(<?=number_format($op_cnt)?>)</strong></span></a>
                </div>
            </div>

			<form action="/admin/portfolios" method="get">
            <fieldset class="admin_search">
                <div class="select open" style="width:115px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="q_type" id="search1" class="option" type="radio" value="name"<?php if($q_type == 'name') echo ' checked="checked"' ?> /><label for="search1">포트폴리오</label></li>
                        <li><input name="q_type" id="search2" class="option" type="radio" value="uid"<?php if($q_type == 'uid') echo ' checked="checked"' ?> /><label for="search2">개발자</label></li>
                    </ul>
                </div> 
            <input id="q" name="q" type="text" title="검색어 입력" value="<?php if(!empty($q)) echo htmlspecialchars($q) ?>" />
            <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>

            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="50" /><col width="*" /><col width="90" /><col width="90" /><col width="170" />
            <col width="90" /><col width="90" />
                <thead>
                <tr>
                    <td class="num">No</td>
                    <td>포트폴리오</td>
                    <td>개발자</td>
                    <td>누적수익률</td>
                    <td>기간</td>
                    <td>상세보기</td>
                    <td>삭제</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($portfolios as $portfolio){ ?>
                <tr>
                    <td class="num"><?php echo htmlspecialchars($portfolio['portfolio_id']) ?></td>
                    <td><a href="/investment/portfolios/<?php echo $portfolio['portfolio_id'] ?>" target="_blank"><?php echo htmlspecialchars($portfolio['name']) ?></a></td>
                    <td><?=$portfolio['nickname']?></td>
                    <td><?=$portfolio['total_profit_rate']?></td>
                    <td><?=$portfolio['start_date']?> ~ <?=$portfolio['end_date']?></td>
                    <td class="btn">
                        <a href="/investment/portfolios/<?php echo $portfolio['portfolio_id'] ?>" title="상세보기" class="btn_view" target="_blank"><span class="ir">상세보기</span></a>
                    </td>
                    <td class="btn">
                        <a href="/investment/portfolios/<?php echo $portfolio['portfolio_id'] ?>/delete" title="삭제" class="sbtn" onclick="return confirm('삭제하시겠습니까?');"><span class="ir">삭제</span></a>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/portfolios?page=1<?=$q_str?>"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/portfolios?page=<?php echo $page_start-1 ?><?=$q_str?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/portfolios?page=<?php echo $i ?><?=$q_str?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/portfolios?page=<?php echo $page_start + $page_count ?><?=$q_str?>" class="next">next</a><?php } ?>
                <a href="/admin/portfolios?page=<?php echo $total_page ?><?=$q_str?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
