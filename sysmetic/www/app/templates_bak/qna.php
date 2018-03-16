<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 나의 Q&A</title>	
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
            <h3 class="qna">나의 Q&A</h3>
			<?php if($_SESSION['user']['user_type'] == 'T'){ ?>
            <!-- 트레이더 이상에게만 노출 -->
            <div class="strategy_view qna">
                <div class="tab">
                    <a href="/qna" title="내가 한 질문" class="tab_on"><span class="ir">내가 한 질문</span></a>
                    <a href="/my_answers" title="내가 받은 질문" class="tab_last"><span class="ir">내가 받은 질문</span></a>
                </div>
            </div>
			<?php } ?>
            <!-- // -->
			<?php if(count($qs)){ ?>
            <table border="0" cellspacing="0" cellpadding="0" class="board">
            <col width="140" /><col width="120" /><col width="*" /><col width="160" /><col width="130" />
                <thead>
                <tr>
                    <td class="left">날짜</td>
                    <td>구분</td>
                    <td>문의 제목</td>
                    <td class="left">트레이더 / 브로커</td>
                    <td>상태</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($qs as $q){ ?>
                <tr>
                    <td class="date"><?php echo date('<b>Y.m.d</b>', strtotime($q['reg_at'])) ?><br /> <?php echo date('H:i:s', strtotime($q['reg_at'])) ?></td>
                    <td><?php if($q['target'] == 'broker') echo '브로커 문의'; else echo '전략 문의'; ?></td>
                    <td class="name"><a href="/qna/<?php echo $q['qna_id'] ?>"><?php if($q['target'] == 'broker') echo '브로커에게 한 문의입니다'; else echo htmlspecialchars($q['strategy_name']); ?></a></td>
                    <td>전략명 : <?php echo htmlspecialchars($q['target_value_text']) ?></td>
                    <td class="btn">
					<?php if(!empty($q['answer'])){ ?>
					<button type="button" title="상세보기" class="complete"><span class="ir">답변완료</span></button>
					<?php }else{ ?>
					<button type="button" title="답변대기" class="waiting"><span class="ir">답변대기</span></button>
					<?php } ?>
					</td>
                </tr>
				<?php } ?>
                </tbody>
            </table>
			<?php }else{ ?>
            <div class="no_data">
                <p>문의 내용이 없습니다.</p><br />
            </div>
			<?php } ?>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>