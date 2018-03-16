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
            <h3 class="admin_strategy">전략 관리</h3>

			<form action="/admin/strategies" method="get">
            <fieldset class="admin_search">
            <input id="q" name="q" type="text" title="검색어 입력" value="<?php if(!empty($q)) echo htmlspecialchars($q) ?>" required="required" />
            <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>

            <a href="/admin/strategies/write" title="전략 등록" class="write btn_admin"><span class="ir">전략등록</span></a>

            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="50" /><col width="60" /><col width="*" /><col width="180" />
            <col width="80" /><col width="85" /><col width="60" /><col width="90" />
                <thead>
                <tr>
                    <td class="num">No</td>
                    <td><!-- <span class="sorting"> -->종류<!-- </span> --></td>
                    <td>전략명</td>
                    <td>개발자</td>
                    <td>상태</td>
                    <td>공개여부</td>
                    <td colspan="2">-</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($strategies as $strategy){ ?>
                <tr>
                    <td class="num"><?php echo htmlspecialchars($strategy['strategy_id']) ?></td>
                    <td>
					<?php if($strategy['strategy_type'] == 'S'){ ?>
					<img src="../img/ico_system.gif" />
					<?php }else if($strategy['strategy_type'] == 'M'){ ?>
					<img src="../img/ico_menual.gif" />
					<?php }else if($strategy['strategy_type'] == 'H'){ ?>
					<img src="../img/ico_hybrid.gif" />
					<?php }else{ ?>
					대기
					<?php } ?>
					</td>
                    <td><a href="/strategies/<?php echo $strategy['strategy_id'] ?>"><?php echo htmlspecialchars($strategy['name']) ?></a></td>
                    <td><?php if(!empty($strategy['developer']['name'])) echo htmlspecialchars($strategy['developer']['name']); else echo htmlspecialchars($strategy['developer_name']) ?></td>
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
                    <td>
                        <a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/delete" title="삭제" class="sbtn" onclick="return confirm('삭제하시겠습니까?');"><span class="ir">삭제</span></a>
                    </td>
                    <td class="btn">
                        <a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>?page=<?php echo $page ?>" title="상세보기" class="btn_view"><span class="ir">상세보기</span></a>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/strategies?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/strategies?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/strategies?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/strategies?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/admin/strategies?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
