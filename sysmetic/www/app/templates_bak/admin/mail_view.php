<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 메일 발송</title>	
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
            <h3 class="admin_mail">메일 발송</h3>
            
            <div id="strategy_view1" class="strategy_view">
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write mail_view" style="width:96%;">
                <col width="160" /><col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">메일제목</td>
                        <td><b><?php echo htmlspecialchars($mail['subject']) ?></b></td>
                    </tr>
                    <tr>
                        <td class="thead">발송날짜/시간</td>
                        <td>   
                            발송날짜 : <?php echo date('Y.m.d', $mail['reserve_at']) ?> &nbsp;&nbsp;
                            발송시간 : <?php echo date('H:i:s', $mail['reserve_at']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">타입선택</td>
                        <td><?php if($mail['mail_type'] == 'normal') echo '공지메일'; else echo '홍보메일'; ?></td>
                    </tr>
                    <tr>
                        <td class="thead">내용</td>
                        <td class="mail_txt">   
						<?php echo nl2br(htmlspecialchars($mail['subject'])) ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            
				<!--
                <p class="btn_area">
                    <button id="" type="" title="목록" class="cancel"><span class="ir">목록</span></button>
                </p>
				-->

                <p class="btn_area">
					<!--
                    <button id="" type="submit" title="변경" class="submit"><span class="ir">변경</span></button>
					-->
					<!------ //내용보기로 들어왔을 경우 ------->
					<a title="삭제" class="submit" href="/admin/mail/<?php echo $mail['mail_id'] ?>/delete?page=<?php echo $page ?>" onclick="return confirm('삭제하시겠습니까?');"><span class="ir">삭제</span></a>
                    <a title="목록" class="cancel" href="/admin/mail?page=<?php echo $page ?>"><span class="ir">목록</span></a><!------ // 발송완료나 예약취소 상태로 내용보기로 들어왔을 경우 목록버튼만 노출됨 ------->
                </p>

				<!------ //발송대기 상태에서 내용보기로 들어왔을 경우에만 노출 ------->
				<!--
				<p class="btn_area">
					<button id="" type="submit" title="지금 발송" class="submit"><span class="ir">지금 발송</span></button>
					<button id="" type="" title="예약 취소" class="cancel"><span class="ir">예약 취소</span></button>
				</p>
				-->
				<!------ //발송대기 상태에서 내용보기로 들어왔을 경우에만 노출 ------->
            </div>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>