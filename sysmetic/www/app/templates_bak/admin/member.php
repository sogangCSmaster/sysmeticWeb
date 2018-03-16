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
		$('#edit_user_form').submit(function(){
			if($('#edit_user_form input[type=checkbox]:checked').length == 0){
				alert('선택된 게시물이 없습니다');
				return false;
			}

			if($('[type=radio][name=user_type]:checked').val() == ''){
				alert('상태를 선택해주세요');
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
            <h3 class="admin_member">회원관리</h3>
            <div class="strategy_view">
                <div class="tab">
                    <a href="/admin/users" title="회원전체" class="tab_on"><span class="ir">회원전체</span></a>
                    <a href="/admin/request_trader" title="트레이더 회원" class="tab_off"><span class="ir">트레이더 회원</span></a>
                    <a href="/admin/request_broker" title="브로커 회원" class="tab_off  tab_last"><span class="ir">브로커 회원</span></a>
                </div>
            </div>

			<form action="/admin/users" method="get">
            <fieldset class="admin_search">      
                <div class="select open" style="width:115px;">
                    <div class="myValue"></div>
                    <ul class="iList">
						<!--
                        <li><input name="search" id="search0" class="option" type="radio" checked="checked" /><label for="search0">선택</label></li>
						-->
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

			<form action="/admin/users/edit" method="post" id="edit_user_form">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="40" /><col width="45" /><col width="75" /><col width="75" /><col width="" />
            <col width="100" /><col width="110" /><col width="80" /><col width="90" /><col width="45" />
                <thead>
                <tr>
                    <td>선택</td>
                    <td>번호</td>
                    <td>회원종류<!-- <span class="sorting">회원종류</span> --></td>
                    <td>회원등급<!-- <span class="sorting">회원등급</span> --></td>
                    <td>이메일</td>
                    <td>이름</td>
                    <td>핸드폰번호</td>
                    <td>생년월일</td>
                    <td>지역</td>
                    <td>성별<!-- <span class="sorting">성별</span> --></td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($users as $user){ ?>
                <tr>
                    <td><p><input type="checkbox" name="uids[]" id="choice<?php echo $user['uid'] ?>" value="<?php echo $user['uid'] ?>" /><label for="choice<?php echo $user['uid'] ?>"></label></p></td>
                    <td class="num"><?php echo htmlspecialchars($user['uid']) ?></td>
                    <td><?php if($user['platform'] == 'naver') echo 'Naver'; else if($user['platform'] == 'facebook') echo 'Facebook';else echo '회원'; ?></td>
                    <td>
					<?php if($user['is_request_trader']){ ?>
					<span class="waiting">대기</span>
					<?php }else{ ?>
					<?php
					if($user['user_type'] == 'T') echo '트레이더'; else if($user['user_type'] == 'B') echo '브로커'; else if($user['user_type'] == 'A') echo '관리자'; else echo '일반';
					?>
					<?php } ?>
					</td>
                    <td class="mail"><?php echo htmlspecialchars($user['email']) ?></td>
                    <td><?php echo htmlspecialchars($user['name']) ?></td>
                    <td class="num"><?php echo htmlspecialchars($user['mobile']) ?></td>
                    <td class="num"><?php echo htmlspecialchars($user['birthday']) ?></td>
                    <td><?php echo htmlspecialchars($user['sido'].' '.$user['gugun']) ?></td>
                    <td><?php if($user['gender'] == 'M') echo '남'; else echo '여' ?></td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
            
            <fieldset class="admin">
                <span class="fieldset_txt">선택한 회원을</span>   
    
                <div class="select open" style="width:100px;">
                    <div class="myValue"></div>
                    <ul class="iList">
                        <li><input name="user_type" id="member0" class="option" type="radio" value="" checked="checked" /><label for="member0">선택</label></li>
                        <li><input name="user_type" id="member1" class="option" type="radio" value="N" /><label for="member1">회원</label></li>
                        <li><input name="user_type" id="member2" class="option" type="radio" value="T" /><label for="member2">트레이더</label></li>
                        <li><input name="user_type" id="member3" class="option" type="radio" value="B" /><label for="member3">브로커</label></li>
                        <li><input name="user_type" id="member4" class="option" type="radio" value="A" /><label for="member4">관리자</label></li>
                    </ul>
                </div> 
                &nbsp;(으)로 &nbsp;&nbsp;
                <button type="submit" title="변경" class="admin1"><span class="ir">변경</span></button>
            </fieldset>
			</form>

            <?php if($total > 0){ ?>
            <div class="paging">
				<a href="/admin/users?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                <?php if($page_start > $page_count){ ?><a href="/admin/users?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
				<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
				<?php if($i > ceil($total / $count)) break; ?>
                <a href="/admin/users?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
				<?php } ?>
				<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/users?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                <a href="/admin/users?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            </div>
			<?php } ?>
        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>