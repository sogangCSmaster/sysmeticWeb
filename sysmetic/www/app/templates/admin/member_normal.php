<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 회원관리</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
        $('#update').on('click', function() {
			if($('#edit_user_form input[type=checkbox]:checked').length == 0){
				alert('선택된 회원이 없습니다');
				return false;
			}

			if($('[type=radio][name=user_type]:checked').val() == ''){
				alert('상태를 선택해주세요');
				return false;
			}

            $('#edit_user_form').attr('action', '/admin/users/edit').submit();

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
                    <a href="/admin/request_normal" title="일반 회원" class="tab_on"><span class="ir">일반 회원</span></a>
                    <a href="/admin/request_trader" title="트레이더 회원" class="tab_off"><span class="ir">트레이더 회원</span></a>
                    <a href="/admin/request_pb" title="PB 회원" class="tab_off"><span class="ir">PB 회원</span></a>
                    <!--a href="/admin/request_broker" title="브로커 회원" class="tab_off  tab_last"><span class="ir">브로커 회원</span></a-->
                </div>
            </div>

			<form action="/admin/request_normal" method="get">
            <fieldset class="admin_search">      
                <div class="select open" style="width:115px;">
                    <div class="myValue"></div>
                    <ul class="iList">
						<!--
                        <li><input name="search" id="search0" class="option" type="radio" checked="checked" /><label for="search0">선택</label></li>
						-->
                        <li><input name="q_type" id="search1" class="option" type="radio" value="email"<?php if($q_type == 'email') echo ' checked="checked"' ?> /><label for="search1">이메일</label></li>
                        <li><input name="q_type" id="search2" class="option" type="radio" value="name"<?php if($q_type == 'name') echo ' checked="checked"' ?> /><label for="search2">이름</label></li>
                        <li><input name="q_type" id="search5" class="option" type="radio" value="nickname"<?php if($q_type == 'nickname') echo ' checked="checked"' ?> /><label for="search5">닉네임</label></li>
                        <li><input name="q_type" id="search3" class="option" type="radio" value="mobile"<?php if($q_type == 'mobile') echo ' checked="checked"' ?> /><label for="search3">핸드폰번호</label></li>
                        <li><input name="q_type" id="search4" class="option" type="radio" value="birthday"<?php if($q_type == 'birthday') echo ' checked="checked"' ?> /><label for="search4">생년월일</label></li>
                    </ul>
                </div> 
                <input id="q" name="q" type="text" title="검색어 입력" value="<?php echo htmlspecialchars($q) ?>" />
                <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>

			<form action="/admin/users/edit" method="post" id="edit_user_form">
            <input type="hidden" name="redirect_url" value="/admin/request_normal" />
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
                <col width="40" /><col width="45" /><col width="60" /><col width="180" /><col width="100" />
                <col width="100" /><col width="80" /><col width="*" /><col width="40" /><col width="90" /><col width="50" />
                <thead>
                <tr>
                    <td>선택</td>
                    <td>번호</td>
                    <td>회원등급</td>
                    <td>이메일</td>
                    <td>이름/닉네임</td>
                    <td>핸드폰번호</td>
                    <td>생년월일</td>
                    <td>지역</td>
                    <td>성별</td>
                    <td>가입일</td>
                    <td>관리</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($users as $user){ ?>
                <tr>
                    <td><p><input type="checkbox" name="uids[]" id="choice<?php echo $user['uid'] ?>" value="<?php echo $user['uid'] ?>" /><label for="choice<?php echo $user['uid'] ?>"></label></p></td>
                    <td class="num"><?php echo htmlspecialchars($user['uid']) ?></td>
                    <td>
					<?php
					if($user['user_type'] == 'T') echo '트레이더';
                    else if($user['user_type'] == 'B') echo '브로커';
                    else if($user['user_type'] == 'A') echo '관리자';
                    else if($user['user_type'] == 'P') echo 'PB';
                    else echo '일반';
                    ?>
					</td>
                    <td class="mail"><?php echo htmlspecialchars($user['email']) ?></td>
                    <td><?php echo htmlspecialchars($user['name']) ?><br /><?php echo htmlspecialchars($user['nickname']) ?></td>
                    <td class="num"><?php echo htmlspecialchars($user['mobile']) ?></td>
                    <td class="num"><?php echo htmlspecialchars($user['birthday']) ?></td>
                    <td><?php echo htmlspecialchars($user['sido'].' '.$user['gugun']) ?></td>
                    <td><?php if($user['gender'] == 'M') echo '남'; else echo '여' ?></td>
                    <td><?=substr($user['reg_at'], 0, 10)?></td>
                    <td><a href="/admin/users/<?php echo $user['uid'] ?>/modify" title="수정" class="sbtn"><span class="ir">수정</span></a></td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <fieldset class="admin">
                <span class="fieldset_txt">선택한 회원등급을</span>   
    
                <div class="select open" style="width:100px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="user_type" id="member0" class="option" type="radio" value="" checked="checked" /><label for="member0">선택</label></li>
                        <li><input name="user_type" id="member1" class="option" type="radio" value="N" /><label for="member1">회원</label></li>
                        <li><input name="user_type" id="member2" class="option" type="radio" value="T" /><label for="member2">트레이더</label></li>
                        <li><input name="user_type" id="member3" class="option" type="radio" value="P" /><label for="member3">PB</label></li>
                        <li><input name="user_type" id="member4" class="option" type="radio" value="A" /><label for="member4">관리자</label></li>
                    </ul>
                </div> 
                &nbsp;(으)로 &nbsp;&nbsp;
                <button type="button" title="변경" class="admin1" id="update"><span class="ir">변경</span></button>
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

	<?php require_once('footer.php') ?>

</body>
</html>