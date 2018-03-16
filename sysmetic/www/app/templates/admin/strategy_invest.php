<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - Broker Contacts 관리</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css?aa" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">투자하기 관리</h3>

            <div class="strategy_view">
                <div class="tab">
                    <a href="?" title="전체" class="tab_<?=($status=='') ? 'on' : 'off';?>"><span class="ir">전체</span></a>
                    <a href="?status=0" title="진행중" class="tab_<?=($status=='0') ? 'on' : 'off';?>"><span class="ir">진행중</span></a>
                    <a href="?status=1" title="상담완료" class="tab_<?=($status=='1') ? 'on' : 'off';?>"><span class="ir">상담완료</span></a>
                    <a href="?status=2" title="투자실행" class="tab_<?=($status=='2') ? 'on' : 'off';?>"><span style='ir'>투자실행</span></a>
                </div>
            </div>

			<form action="/admin/strategies_invest" method="get">
            <input type="hidden" name="status" value="<?=$status?>" />
            <fieldset class="admin_search">
                <div class="select open" style="width:115px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="q_type" id="search1" class="option" type="radio" value="name"<?php if($q_type == 'name') echo ' checked="checked"' ?> /><label for="search1">상품명</label></li>
                        <li><input name="q_type" id="search3" class="option" type="radio" value="user"<?php if($q_type == 'user') echo ' checked="checked"' ?> /><label for="search3">투자자</label></li>
                    </ul>
                </div> 
            <input id="q" name="q" type="text" title="검색어 입력" value="<?php if(!empty($q)) echo htmlspecialchars($q) ?>" />
            <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>


            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="50" /><col width="60" /><col width="130" /><col width="*" />
            <col width="90" /><col width="90" /><col width="70" /><col width="90" /><col width="90" /><col width="70" />
                <thead>
                <tr>
                    <td>No</td>
                    <td>투자자</td>
                    <td>연락처</td>
                    <td>상품명</td>
                    <td>투자가입금액</td>
                    <td>투자개시시점</td>
                    <td>최대손실한도율</td>
                    <td>등록일</td>
                    <td>상태</td>
					<td>-</td>
                </tr>
                </thead>
                <tbody>
				<?php
				foreach($invests as $invest){ 
				?>
                <tr>
                    <td class="num"><?php echo $invest['invest_id'] ?></td>
                    <td><?php echo htmlspecialchars($invest['name']) ?></td>
                    <td><?=$invest['email']?><br><?=$invest['mobile']?></td>
                    <td><?php echo htmlspecialchars($invest['strategy_name']); ?></td>
                    <td><strong style="color:#ff0000"><?=number_format($invest['s_price'])?></strong></td>
                    <td><?=$invest['s_date']?></td>
                    <td><?=$invest['max_loss_per']?>%</td>
                    <td><?=$invest['insdt']?></td>
                    <td>
                    <?
                    switch ($invest['status']) {
                        case '0':
					        echo '<button type="button" title="진행중" class="complete"><span class="ir">진행중</span></button>';
                            break;
                        case '1':
					        echo '<button type="button" title="상담완료" class="complete2"><span class="ir">상담완료</span></button>';
                            break;
                        case '2':
					        echo '<button type="button" title="투자실행" class="waiting"><span class="ir">투자실행</span></button>';
                            break;
                    }
                    ?>
                    </td>
                    <td>
                        <a href="javascript:;" onclick="qnaDel('<?php echo $invest['invest_id'] ?>')" title="삭제" class="btn_view"><span class="ir">삭제</span></a>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <?php if($total > 0){ ?>
            <div class="paging">
				<a href="?page=1<?=$q_str?>"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="?page=<?php echo $page_start-1 ?><?=$q_str?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="?page=<?php echo $i ?><?=$q_str?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="?page=<?php echo $page_start + $page_count ?><?=$q_str?>" class="next">next</a><?php } ?>
                <a href="?page=<?php echo $total_page ?><?=$q_str?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>

<script type="text/javascript">
<!--
	function qnaDel(uid){
		var check = confirm("정말 삭제 하시겠습니까?");
		if(check){
			window.location="/admin/strategies_invest/del/"+uid;
		}else{
			return false;
		}
	}
//-->
</script>