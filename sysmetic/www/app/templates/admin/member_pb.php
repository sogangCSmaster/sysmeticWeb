<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 브로커 회원</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
    <script>
    $(function(){
        $('#approval').on('click', function() {
            if($('#edit_user_form input[type=checkbox]:checked').length == 0){
                alert('선택된 사용자가 없습니다');
                return false;
            } else {
                if (!confirm('승인하시겠습니까?')) {
                    return false;
                }
                $('#edit_user_form').attr('action', '/admin/request/approve').submit();
            }
        });

        $('#del').on('click', function() {
            if($('#edit_user_form input[type=checkbox]:checked').length == 0){
                alert('선택된 사용자가 없습니다');
                return false;
            } else {
                if (!confirm("선택한 회원을 강제탈퇴 시키겠습니까?\n탈퇴 처리시 취소가 불가능합니다")) {
                    return false;
                }
                $('#edit_user_form').attr('action', '/admin/request/delete').submit();
            }
        });


    });

    
	function openImage(url){
		$('#show_img').attr('src', url);
		showLayer('preview');
	}
    </script>

</head>

<body>

	<?php require_once('header.php') ?>
    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">회원관리</h3>
            <div class="strategy_view">
                <div class="tab">
                    <a href="/admin/users" title="회원전체" class="tab_off"><span class="ir">회원전체</span></a>
                    <a href="/admin/request_normal" title="일반 회원" class="tab_off"><span class="ir">일반 회원</span></a>
                    <a href="/admin/request_trader" title="트레이더 회원" class="tab_off"><span class="ir">트레이더 회원</span></a>
                    <a href="/admin/request_pb" title="PB 회원" class="tab_on"><span class="ir">PB 회원</span></a>
                </div>
            </div>


			<form action="/admin/request_pb" method="get">
            <fieldset class="admin_search">      
                <div class="select open" style="width:115px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="q_type" id="search1" class="option" type="radio" value="email"<?php if($q_type == 'email') echo ' checked="checked"' ?> /><label for="search1">이메일</label></li>
                        <li><input name="q_type" id="search2" class="option" type="radio" value="name"<?php if($q_type == 'name') echo ' checked="checked"' ?> /><label for="search2">이름</label></li>
                        <li><input name="q_type" id="search3" class="option" type="radio" value="mobile"<?php if($q_type == 'mobile') echo ' checked="checked"' ?> /><label for="search3">핸드폰번호</label></li>
                        <li><input name="q_type" id="search4" class="option" type="radio" value="birthday"<?php if($q_type == 'birthday') echo ' checked="checked"' ?> /><label for="search4">생년월일</label></li>
                    </ul>
                </div> 
                <input id="q" name="q" type="text" title="검색어 입력" value="<?php echo htmlspecialchars($q) ?>" />
                <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
            </form>

			<form action="/admin/request/approve" method="post" id="edit_user_form">
            <input type="hidden" name="user_type" value="P" />
            <input type="hidden" name="redirect_url" value="/admin/request_pb" />
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
                <col width="40" /><col width="60" /><col width="180" /><col width="100" />
                <col width="100" /><col width="*" /><col width="80" /><col width="90" /><col width="80" /><col width="50" />
                <thead>
                <tr>
                    <td>선택</td>
                    <td>번호</td>
                    <td>이메일</td>
                    <td>이름</td>
                    <td>핸드폰번호</td>
                    <td>근무처</td>
                    <td>명함이미지</td>
                    <td>가입일</td>
                    <td>상태</td>
                    <td>관리</td>
                </tr>
                </thead>
                <tbody>
				<?
                foreach($users as $user) {
                    if ($user['namecard']) {
                        $namecard = "<button type='button' class='waiting' style='cursor:pointer' onclick='openImage(\"".$user['namecard']."\")' ><span class='ir'>명함보기</span></button>";
                    } else {
                        $namecard = "<button type='button' class='complete' ><span class='ir'>명함없음</span></button>";
                    }
                ?>
                <tr>
                    <td><p><input type="checkbox" name="uids[]" id="choice<?php echo $user['uid'] ?>" value="<?php echo $user['uid'] ?>" /><label for="choice<?php echo $user['uid'] ?>"></label></p></td>
                    <td class="num"><?php echo htmlspecialchars($user['uid']) ?></td>
                    <td class="mail"><?php echo htmlspecialchars($user['email']) ?></td>
                    <td><?php echo htmlspecialchars($user['name']) ?> 
                    <? if ($user['is_request_pb'] == '0') { ?>
                    <a href="/lounge/<?=$user['uid']?>" target="_blank">
                    <img src="/images/sub/btn_lounge_coffee.gif" /></a>
                    <? } ?>
                    </td>
                    <td class="num"><?php echo htmlspecialchars($user['mobile']) ?></td>
                    <td><?php echo htmlspecialchars($user['company'].' '.$user['sido2'])?><br /><?php echo htmlspecialchars($user['gugun2'].' '.$user['part']) ?></td>
                    <td><?=$namecard?></td>
                    <td><?php echo substr($user['reg_at'], 0, 10) ?></td>
                    <td>
                    <? if ($user['is_request_pb'] == '1') { ?>
					<button type="button" title="대기중" class="waiting"><span class="ir">대기중</span></button>
                    <? } else { ?>
					<button type="button" title="승인" class="complete"><span class="ir">승인</span></button>
                    <? } ?>
                    </td>
                    <td><a href="/admin/users/<?php echo $user['uid'] ?>/modify" title="수정" class="sbtn"><span class="ir">수정</span></a></td>
                </tr>
				<?php } ?>
                </tbody>
            </table>

            <fieldset class="admin">
                <span class="fieldset_txt">선택한 회원의 등급을</span> <strong>PB</strong>
                &nbsp;로 &nbsp;&nbsp;
                <button type="button" title="승인" class="admin1" id="approval"><span class="ir">승인</span></button>
            </fieldset>
            <fieldset class="admin">
                <span class="fieldset_txt">선택한 회원을</span>
                <button type="button" title="승인" class="admin1" id="del"><span class="ir">강제탈퇴</span></button>
            </fieldset>
			</form>

            <?php if($total > 0){ ?>
            <div class="paging">
				<a href="?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>
        </div>        
    </div>
    <!------ //본문 영역 ------->

    <!-- 이미지 보기 레이어 -->
    <div id="preview" class="layer" style="width:600px;display:none;">
        <div class="layer_head">
            <span class="layer_close" onclick="closeLayer('preview');">X</span>
        </div>

        <div class="layer_photo">
            <img src="" id="show_img" />
        </div>
    </div>

	<?php require_once('footer.php') ?>

</body>
</html>
