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
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="admin_broker">브로커 관리</h3>

			<!--
			<form action="" method="get">
            <fieldset class="admin_search">
            <input id="q" name="q" type="text" title="검색어 입력" />
            <button id="search_btn" type="submit" title="검색"><span class="ir">검색</span></button>
            </fieldset>
			</form>
			-->

            <a href="/admin/brokers/write" title="브로커 등록" class="write btn_admin notice"><span class="ir">브로커 등록</span></a>

			<form action="/admin/brokers/edit" method="post">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
            <col width="80" /><col width="100" /><col width="*" /><col width="120" />
            <col width="120" /><col width="100" /><col width="80" />
                <thead>
                <tr>
                    <td>순서</td>
                    <td><!-- <span class="sorting"> -->종류<!-- </span> --></td>
                    <td>회사명</td>
                    <td>시스템 트래이딩</td>
                    <td>API</td>
                    <td>노출여부</td>
                    <td>-</td>
                </tr>
                </thead>
                <tbody>
				<?php foreach($brokers as $broker){ ?>
                <tr>
                    <td><input name="broker_<?php echo htmlspecialchars($broker['broker_id']) ?>" type="text" style="width:20px;" title="노출순서" value="<?php echo $broker['sorting'] ?>" required="required"  /></td>
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
                        <a href="/admin/brokers/<?php echo $broker['broker_id'] ?>" title="상세보기" class="btn_view"><span class="ir">상세보기</span></a>
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