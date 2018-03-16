<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 브로커 관리</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
    <script>
	$(function(){
		$('#broker_edit_form').submit(function(){

            var result = [];
            $('.sorting').each(function() {
                var element = $(this).val();
                if ($.inArray(element, result) == -1) {  // result 에서 값을 찾는다.  //값이 없을경우(-1)
                    result.push(element);                // result 배열에 값을 넣는다.
                } else {
                    result = -1;
                    return false;
                }
            });
            
            if (result == -1) {
                alert('중복된 값이 있습니다');
                return false;
            } else {
                if (!confirm('순서를 저장하시겠습니까?')) {
                    return false;
                }
            }
		});
	});

	function confirmDelete(broker_id){
		var result = confirm('삭제하시겠습니까?');
		if(result){
			location.href='/admin/brokers/'+broker_id+'/delete';
		}
	}


    <?php if(!empty($flash['error'])){ ?>
    alert('<?php echo htmlspecialchars($flash['error']) ?>');
    <?php } ?>
    </script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">증권사 관리</h3>

			<form action="" method="get">
            <fieldset class="admin_search">
            <input id="q" name="q" type="text" title="회사명 입력" />
            <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>

            <a href="/admin/brokers/write" title="증권사 등록" class="write btn_admin notice"><span class="ir">증권사 등록</span></a>

			<form id="broker_edit_form" action="/admin/brokers/edit" method="post">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="70" /><col width="70" /><col width="*" /><col width="140" />
            <col width="120" /><col width="80" /><col width="80" /><col width="100" />
                <thead>
                <tr>
                    <td>순서</td>
                    <td>종류</td>
                    <td>회사명</td>
                    <td>매매툴</td>
                    <td>API</td>
                    <td>노출여부</td>
                    <td>메인연결</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($brokers as $broker){ ?>
                <tr>
                    <td><input class="sorting" name="broker_<?php echo htmlspecialchars($broker['broker_id']) ?>" type="text" style="width:20px;" title="노출순서" value="<?php echo $broker['sorting'] ?>" required="required"  /></td>
                    <td><?php echo htmlspecialchars($broker['company_type']) ?></td>
                    <td><?php echo htmlspecialchars($broker['company']) ?></td>
                    <td class="num">
					<?php
					$v_array = array();
					foreach($broker['system_trading_tools'] as $k => $v){
						$v_array[] = htmlspecialchars($v['name']);
					}
					echo implode(',', $v_array);
					?>
					</td>
                    <td class="num">
					<?php
					$v_array = array();
					foreach($broker['api_tools'] as $k => $v){
						$v_array[] = htmlspecialchars($v['name']);
					}
					echo implode(',', $v_array);
					?>
					</td>

                    <td>
					<?php if($broker['is_open']){ ?>
					<button type="button" title="노출중" class="complete"><span class="ir">노출중</span></button>
					<?php }else{ ?>
					<button type="button" title="비노출" class="waiting"><span class="ir">비노출</span></button>
					<?php } ?>
					</td>

                    <td>
					<?php if($broker['is_main']){ ?>
					<strong style='color:#ff0000'>연결중</strong>
					<?php }else{ ?>
					-
					<?php } ?>
					</td>

                    <td>

                        
                        <a href="/admin/brokers/<?php echo $broker['broker_id'] ?>" title="상세보기" class="sbtn"><span class="ir">수정</span></a>
                        <button type="button" onclick="confirmDelete(<?php echo $broker['broker_id'] ?>)" title="삭제" class="sbtn"><span class="ir">삭제</span></button>

                    </td>
                </tr>
				<?php } ?>
                </tbody>
            </table>

            <fieldset class="admin">
                노출 순서 
                <button type="submit" title="저장" class="admin1"><span class="ir">저장</span></button>
            </fieldset>
			</form>

        </div>       
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>