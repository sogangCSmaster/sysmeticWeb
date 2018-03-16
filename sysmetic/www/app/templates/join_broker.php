<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 브로커 등록</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>

		$('form').submit(function(){
			/*
			if(!$('input[name=broker_id]:checked').val()){
				alert('근무처를 선택해주세요');
				return false;
			}
			*/

			return true;

		});
	});


	</script>
</head>

<body>

	<?php require_once('header.php') ?>
    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="admin_broker_write">브로커 등록</h3>
            
            <p class="sub_title default">기본정보</p>
            <div class="user_info view">
                <dl>
                    <dt>이메일</dt>
                    <dd>
                        <?php echo htmlspecialchars($_SESSION['user']['email']) ?>
                    </dd>
                    <dt>이름</dt>
                    <dd>
					<?php if(!empty($_SESSION['user']['name'])){ ?>
					<?php echo htmlspecialchars($_SESSION['user']['name']) ?>
					<?php }else{ ?>
					<span class="no">등록된 이름이 없습니다.</span>
					<?php } ?>
					</dd>
                    <dt>휴대폰</dt>
                    <dd><?php echo htmlspecialchars($_SESSION['user']['mobile']) ?></dd>
                </dl>
            </div>

            <p class="sub_title add">추가정보</p>
			<form action="/join_broker" method="post">
            <div class="user_info broker_add">
                <dl>
                    <dt>근무처</dt>
                    <dd>                          
                        <div class="select_area">
                            <div class="select open" style="width:140px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="company" id="broker0" class="option" type="radio" value="" checked="checked" /><label for="broker0">근무처 선택</label></li>
									<?php foreach($brokers as $broker){ ?>
                                    <li><input name="company" id="broker1" class="option" type="radio" value="<?php echo htmlspecialchars($broker['company']) ?>" /><label for="broker1"><?php echo htmlspecialchars($broker['company']) ?></label></li>   
									<?php } ?>
                                </ul>
                            </div>       
                        </div>
                        <input name="location" type="text" title="근무지점" value="근무지점" onclick="this.value='';" />
                        <input name="work_year" type="text" title="근무년수" value="근무년수" onclick="this.value='';" />
                        <input name="position" type="text" title="직책" value="직책" onclick="this.value='';" />
                    </dd>
                    <dt>주력분야</dt>
                    <dd>
                        <p><input type="checkbox" name="major[]" id="item1" value="채권/펀드" /><label for="item1">채권/펀드</label></p>
                        <p><input type="checkbox" name="major[]" id="item2" value="주식/ETF" /><label for="item2">주식/ETF</label></p>
                        <p><input type="checkbox" name="major[]" id="item3" value="해외주식" /><label for="item3">해외주식</label></p>
                        <p><input type="checkbox" name="major[]" id="item4" value="국내파생" /><label for="item4">국내파생</label></p>
                        <p><input type="checkbox" name="major[]" id="item5" value="해외파생" /><label for="item5">해외파생</label></p>
                        <p><input type="checkbox" name="major[]" id="item6" value="기타" /><label for="item6">기타</label></p>
                        <input name="major_etc" type="text" title="기타" value="기타 입력" onclick="this.value='';" />
                    </dd>
                    <dt>전략상품</dt>
                    <dd>
                        보유전략 : <input name="my_strategy_count" type="text" title="보유전략" value="0" style="width:30px;"  /> &nbsp;&nbsp;
                        공개 가능 전략 : <input name="open_strategy_count" type="text" title="공개 가능 전략" value="0" style="width:30px;" />
                    </dd>
                    <dt>요청사항</dt>
                    <dd>
                        <textarea id="memo" name="memo" ></textarea>
                    </dd>
                </dl>
            </div>

            <p class="btn_board">
                <button type="submit" title="브로커 등록" class="submit"><span class="ir">브로커 등록</span></button>
                <button type="cancel" title="취소" class="cancel"><span class="ir">취소</span></button>
            </p>
			</form>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>