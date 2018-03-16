<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - Broker Contacts 관리</title>	
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
            <h3 class="admin_contact">Broker Contacts 관리</h3>

			<!--
			<form action="/admin/contacts" method="get">
            <fieldset class="admin_search">
            <input id="q" name="q" type="text" title="검색어 입력" value="<?php if(!empty($q)) echo htmlspecialchars($q) ?>" required="required" />
            <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>
			-->

            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <!-- <col width="40" /> --><col width="50" /><col width="*" />
            <col width="140" /><col width="100" /><col width="80" /><col width="80" />
                <thead>
                <tr>
                    <!-- <td>선택</td> -->
                    <td>No</td>
                    <td>브로커</td>
                    <td>질문자</td>
                    <td>문의일시</td>
                    <td>상태</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($contacts as $contact){ ?>
                <tr>
                    <!-- <td><p><input type="checkbox" id="choice1" /><label for="choice1"></label></p></td> -->
                    <td class="num"><?php echo $contact['qna_id'] ?></td>
                    <td><?php echo htmlspecialchars($contact['target_value_text']) ?></td>
                    <td><?php echo htmlspecialchars($contact['name']); ?></td>
                    <td class="num"><?php echo date('Y.m.d', strtotime($contact['reg_at'])) ?></td>
                    <td>
					<?php if(empty($contact['answer'])){ ?>
					<button type="button" title="대기중" class="waiting"><span class="ir">대기중</span></button>
					<?php }else{ ?>
					<button type="button" title="답변완료" class="complete"><span class="ir">답변완료</span></button>
					<?php } ?>
					<!--
					<button type="button" title="전달완료" class="complete"><span class="ir">전달완료</span></button>
					-->
					</td>
                    <td>
                        <a href="/admin/contacts/<?php echo $contact['qna_id'] ?>?page=<?php echo $page ?>" title="상세보기" class="btn_view"><span class="ir">상세보기</span></a>
                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/contacts?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/contacts?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/contacts?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/contacts?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/admin/contacts?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>