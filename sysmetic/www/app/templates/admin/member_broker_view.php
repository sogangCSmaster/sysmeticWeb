<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 브로커 신청 정보</title>	
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
            
            <h5 style="margin:0 0 30px 0;">브로커 신청 정보</h5> 
            
            <p class="sub_title default">기본정보</p>
            <div class="user_info view">
                <dl>
                    <dt>이메일</dt>
                    <dd>
                        <?php echo htmlspecialchars($request_broker_info['user']['email']) ?>
                    </dd>
                    <dt>이름</dt>
                    <dd>
					<?php if(!empty($request_broker_info['user']['name'])){ ?>
					<?php echo htmlspecialchars($request_broker_info['user']['name']) ?>
					<?php }else{ ?>
					<span class="no">등록된 이름이 없습니다.</span>
					<?php } ?>
					</dd>
                    <dt>휴대폰</dt>
                    <dd><?php echo htmlspecialchars($request_broker_info['user']['mobile']) ?></dd>
                </dl>
            </div>

            <p class="sub_title add">추가정보</p>
            <div class="user_info view">
                <dl>
                    <dt>근무처</dt>
                    <dd>                          
                        근무처 : <?php echo htmlspecialchars($request_broker_info['company']) ?> &nbsp;&nbsp;
                        근무지점 : <?php echo htmlspecialchars($request_broker_info['location']) ?> &nbsp;&nbsp;
                        근무년수 : <?php echo htmlspecialchars($request_broker_info['work_year']) ?>년 &nbsp;&nbsp;
                        직책 : <?php echo htmlspecialchars($request_broker_info['position']) ?> &nbsp;&nbsp;
                    </dd>
                    <dt>주력분야</dt>
                    <dd>
						<?php
						$major_array = explode('|', $request_broker_info['major']);
						$major_array_text = array();
						foreach($major_array as $k => $v){
							if($v == '기타'){
								$major_etc = empty($major_array[$k+1]) ? '' : $major_array[$k+1];
								$major_array_text[] = '기타 - '.$major_etc;
								break;
							}else $major_array_text[] = $v;
						}
						echo htmlspecialchars(implode(', ', $major_array_text));
						?>
                    </dd>
                    <dt>전략상품</dt>
                    <dd>
                        보유전략 : <?php echo htmlspecialchars($request_broker_info['my_strategy_count']) ?> &nbsp;&nbsp;
                        공개 가능 전략 : <?php echo htmlspecialchars($request_broker_info['open_strategy_count']) ?>
                    </dd>
                    <dt>요청사항</dt>
                    <dd>
                        <?php echo htmlspecialchars($request_broker_info['memo']) ?>
                    </dd>
                </dl>
            </div>

            <p class="btn_board">
                <button type="submit" title="브로커 승인" class="submit" onclick="location.href='/admin/request_broker/<?php echo $request_broker_info['request_broker_id'] ?>/approve'"><span class="ir">브로커 승인</span></button>
                <button type="cancel" title="취소" class="cancel" onclick="location.href='/admin/request_broker/<?php echo $request_broker_info['request_broker_id'] ?>/cancel'"><span class="ir">취소</span></button>
            </p>
        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>