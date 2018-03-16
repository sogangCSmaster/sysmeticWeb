<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 전략 등록</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="/js/calendar.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#reg_strategy_form').submit(function(){
			if(!$('#name').val()){
				alert('전략명을 입력해주세요');
				$('#name').focus();
				return false;
			}

			if($('input[data-role=item]:checked').length == 0){
				alert('종목을 선택해주세요');
				return false;
			}

			if(!$('input[name=broker_type]:checked').val()){
				alert('브로커를 선택해주세요');
				return false;
			}

			if(!$('input[name=broker_id]:checked').val()){
				alert('브로커를 선택해주세요');
				return false;
			}

			if(!$('input[name=tool_id]:checked').val()){
				alert('매매툴을 선택해주세요');
				return false;
			}

			/* if($('#investment').val() == ''){
				alert('투자원금을 입력해주세요');
				return false;
			}*/

			if($('#intro').val() == ''){
				alert('전략소개를 입력해주세요');
				return false;
			}

			return true;
		});

		$('#broker_type .iList input[type=radio]').on('click', function(){
			$('#company_type1').hide();
			$('#company_type2').hide();

			if($(this).data('sub-list')) $('#'+$(this).data('sub-list')).css('display', 'inline-block');
		});

		$('#company_type1 .iList input[type=radio]').on('click', function(){
			$('#tools_list .select').hide();
			if($(this).val()){
				if($('#broker'+$(this).val() + '_tools').length){
					$('#broker'+$(this).val() + '_tools').show();
					$('#broker'+$(this).val() + '_tools').find('input:eq(0)').attr('checked','checked');
					$('#broker'+$(this).val() + '_tools').find('.myValue').text($('#broker'+$(this).val() + '_tools').find('label:eq(0)').text());
				}
				// else $('#default_tools').show();
			}else{
				// $('#default_tools').show();
			}
		});

		$('#company_type2 .iList input[type=radio]').on('click', function(){
			$('#tools_list .select').hide();
			if($(this).val()){
				if($('#broker'+$(this).val() + '_tools').length){
					$('#broker'+$(this).val() + '_tools').show();
					$('#broker'+$(this).val() + '_tools').find('input:eq(0)').attr('checked','checked');
					$('#broker'+$(this).val() + '_tools').find('.myValue').text($('#broker'+$(this).val() + '_tools').find('label:eq(0)').text());
				}
				// else $('#default_tools').show();
			}else{
				// $('#default_tools').show();
			}
		});
	});

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
            <h3 class="admin_strategy_write">전략 등록</h3>
`
			<form action="/admin/strategies/write" method="post" id="reg_strategy_form">
            <div id="strategy_view1" class="strategy_view">
		<button id="" type="button" onclick="window.open('http://sysmetic.co.kr/img/%EC%8B%9C%EC%8A%A4%EB%A9%94%ED%8B%B1%EC%A0%84%EB%9E%B5%EB%93%B1%EB%A1%9D%EB%B0%A9%EB%B2%95.pdf','_blank');" title="전략등록설명보기" class="write btn_admin"><span class="ir">전략등록 설명보기</span></button>
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:96%;">
                <col width="160" /> <col width="240" /><col width="160" /> <col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">전략명</td>
                        <td colspan="3">   
                            <input id="name" name="name" type="text" title="전략명" required="required" />
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">종목</td>
                        <td colspan="3">
                            <p class="admin">
                                <!-- 종목관리에 등록된 종목 불러옴 -->
								<?php foreach($items as $item){ ?>
								<input name="item_ids[]" id="item<?php echo $item['item_id'] ?>" class="option" type="checkbox" value="<?php echo $item['item_id'] ?>" data-role="item" /><label for="item<?php echo $item['item_id'] ?>"><?php echo htmlspecialchars($item['name']) ?></label>
								<?php } ?>               
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">브로커 선택</td>
                        <td colspan="3"> 
                            <div id="broker_type" class="select open" style="width:90px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="broker_type" id="type0" class="option" type="radio" value="" checked="checked" /><label for="type0">선택</label></li>
                                    <li><input name="broker_type" id="type1" class="option" type="radio" value="증권사" data-sub-list="company_type1" /><label for="type1">증권사</label></li>
                                    <li><input name="broker_type" id="type2" class="option" type="radio" value="선물사" data-sub-list="company_type2" /><label for="type2">선물사</label></li>
                                </ul>
                            </div> 
                            <div id="company_type1" class="select open" style="width:140px;display:none;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="broker_id" id="broker_type1_0" class="option" type="radio" value="" checked="checked" /><label for="broker_type1_0">브로커 선택</label></li>
									<?php foreach($company_type1 as $v){ ?>
                                    <li><input name="broker_id" id="broker<?php echo $v['id'] ?>" class="option" type="radio" value="<?php echo $v['id'] ?>" /><label for="broker<?php echo $v['id'] ?>"><?php echo htmlspecialchars($v['name']) ?></label></li>
									<?php } ?>
                                </ul>
                            </div>
							<div id="company_type2" class="select open" style="width:140px;display:none;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="broker_id" id="broker_type2_0" class="option" type="radio" value="" checked="checked" /><label for="broker_type2_0">브로커 선택</label></li>
                                    <?php foreach($company_type2 as $v){ ?>
                                    <li><input name="broker_id" id="broker<?php echo $v['id'] ?>" class="option" type="radio" value="<?php echo $v['id'] ?>" /><label for="broker<?php echo $v['id'] ?>"><?php echo htmlspecialchars($v['name']) ?></label></li>
									<?php } ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr id="tools_list">
                        <td class="thead">매매툴</td>
                        <td>
                        	<!--
							<div id="default_tools" class="select open" style="width:140px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="tool_id" id="tool" class="option" type="radio" value="" checked="checked" /><label for="tool">매매툴 선택</label></li>
                                </ul>
                            </div> 
                            -->

							<?php foreach($tools as $k => $v){ ?>
                            <div id="<?php echo $k ?>_tools" class="select open" style="width:140px;display:none;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="tool_id" id="tool" class="option" type="radio" value="" checked="checked" /><label for="tool">매매툴 선택</label></li>
									<?php foreach($v as $tool){ ?>
                                    <li><input name="tool_id" id="tool<?php echo $tool['tool_id'] ?>" class="option" type="radio" value="<?php echo $tool['tool_id'] ?>" /><label for="tool<?php echo $tool['tool_id'] ?>"><?php echo htmlspecialchars($tool['name']) ?></label></li>
									<?php } ?>									
                                </ul>
                            </div> 
							<?php } ?>
                            <!-- Broker에 등록된 시스템 트레이딩 불러옴 -->
                        </td>
                        <td class="thead">통화</td>
                        <td>   
                            <div class="select open" style="width:90px;">
                                <div class="myValue"></div>
                                <ul class="iList">
									<!--
                                    <li><input name="money" id="money0" class="option" type="radio" checked="checked" /><label for="money0">선택</label></li>
									-->
                                    <li><input name="currency" id="money1" class="option" type="radio" value="KRW" checked="checked" /><label for="money1">KRW</label></li>
                                    <li><input name="currency" id="money2" class="option" type="radio" value="USD" /><label for="money2">USD</label></li>
                                    <li><input name="currency" id="money3" class="option" type="radio" value="JPY" /><label for="money3">JPY</label></li>
                                    <li><input name="currency" id="money4" class="option" type="radio" value="EUR" /><label for="money4">EUR</label></li>
                                    <li><input name="currency" id="money5" class="option" type="radio" value="CNY" /><label for="money5">CNY</label></li>
                                </ul>
                            </div> 

                            <!--  <input id="investment" name="investment" type="text" title="투자원금" value="0" style="width:130px;" required="required" onkeyup="inputNumberFormat(this)" /> -->
                        </td>
                    </tr>
					<!--
                    <tr>
                        <td class="thead">시작일자</td>
                        <td>   
                            <input id="start_date" name="start_date" type="text" title="시작일자" value="" class="datepicker"  />
                        </td>
                        <td class="thead">최종일자</td>
                        <td>
                            <input id="end_date" name="end_date" type="text" title="최종일자" value="" class="datepicker"  />
                        </td>
                    </tr>
					-->
                    <tr>
                        <td class="thead">주기</td>
                        <td colspan="3"> 
                            <div class="select open" style="width:90px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="term" id="term_type1" class="option" type="radio" value="day" checked="checked" /><label for="term_type1">데이</label></li>
                                    <li><input name="term" id="term_type2" class="option" type="radio" value="position" /><label for="term_type2">포지션</label></li>
                                </ul>
                            </div> 
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">전략소개</td>
                        <td colspan="3">   
                            <textarea name="intro" id="intro" required="required"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <p class="admin_msg">
                    * 전략 등록 후 관리자의 승인을 거쳐 최종 공개가 됩니다.
                </p>
            
                <p class="btn_area">
                    <button type="submit" title="등록" class="submit"><span class="ir">등록</span></button>
                    <button type="reset" title="취소" class="cancel"><span class="ir">취소</span></button>
                </p>
            </div>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
