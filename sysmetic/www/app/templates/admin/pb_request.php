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
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">상품상담 관리</h3>

            <div class="strategy_view">
                <div class="tab">
                    <a href="?" title="전체" class="tab_<?=(!$req_type) ? 'on' : 'off';?>"><span class="ir">전체</span></a>
                    <a href="?req_type=Online" title="Online상담" class="tab_<?=($req_type == 'Online') ? 'on' : 'off';?>"><span class="ir">Online상담</span></a>
                    <a href="?req_type=Offline" title="Offline상담" class="tab_<?=($req_type == 'Offline') ? 'on' : 'off';?>"><span style='ir'>Offline상담</span></a>
                </div>
            </div>

			<form action="/admin/pb_request" method="get">
            <input type="hidden" name="req_type" value="<?=$req_type?>" />
            <fieldset class="admin_search">
                <div class="select open" style="width:115px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="q_type" id="search1" class="option" type="radio" value="subject"<?php if($q_type == 'subject') echo ' checked="checked"' ?> /><label for="search1">상담제목</label></li>
                        <li><input name="q_type" id="search2" class="option" type="radio" value="pb"<?php if($q_type == 'pb') echo ' checked="checked"' ?> /><label for="search2">PB</label></li>
                        <li><input name="q_type" id="search3" class="option" type="radio" value="user"<?php if($q_type == 'user') echo ' checked="checked"' ?> /><label for="search3">상담자</label></li>
                    </ul>
                </div> 
            <input id="q" name="q" type="text" title="검색어 입력" value="<?php if(!empty($q)) echo htmlspecialchars($q) ?>" />
            <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>


            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="60" /><col width="60" /><col width="*" /><col width="80" /><col width="160" />
            <col width="80" /><col width="70" /><col width="80" /><col width="130" />
                <thead>
                <tr>
                    <td>No</td>
                    <td>타입</td>
                    <td>상담제목</td>
                    <td>상담신청</td>
                    <td>연락처</td>
                    <td>PB명</td>
                    <td>등록일</td>
                    <td>상태</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($contacts as $contact){ ?>
                <tr>
                    <td class="num"><?php echo $contact['req_id'] ?></td>
                    <td class="num"><?php echo $contact['req_type'] ?></td>
                    <td class="first"><?php echo htmlspecialchars($contact['subject']) ?></td>
                    <td><?php echo htmlspecialchars($contact['user_name']); ?></td>
                    <td><?=$contact['b_email']?><br><?=$contact['b_mobile']?></td>
                    <td><?php echo htmlspecialchars($contact['pb_name']); ?></td>
                    <td><?=$contact['reg_date']?></td>
                    <td>
                    <?
                    if ($contact['req_type'] == 'Online') {
                        if ($contact['status'] == 0) {
                            $status = '<button type="button" title="답변대기" class="complete2"><span class="ir">답변대기</span></button>';
                        } else {
                            $status = '<button type="button" title="답변완료" class="complete"><span class="ir">답변완료</span></button>';
                        }
                    } else {
                        if ($contact['status'] == 0) {
                            $status = '<button type="button" title="상담예약" class="waiting"><span class="ir">상담예약</span></button>';
                        } else {
                            $status = '<button type="button" title="상담완료" class="complete"><span class="ir">상담완료</span></button>';
                        }
                    }
                    ?>

                    <?=$status?>
					</td>
                    <td>
                        <a href="/admin/pb_request/<?php echo $contact['req_id'] ?>" title="상세보기" class="btn_view"><span class="ir">상세보기</span></a>
                        <a href="javascript:;" onclick="qnaDel('<?php echo $contact['req_id'] ?>')" title="상세보기" class="btn_view"><span class="ir">삭제</span></a>
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
			window.location="/admin/pb_request/del/"+uid;
		}else{
			return false;
		}
	}
//-->
</script>