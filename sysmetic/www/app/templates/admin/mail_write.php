<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 메일발송</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="/js/calendar.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#mail_form').submit(function(){
			if(!$('#subject').val()){
				alert('제목을 입력하세요');
				$('#subject').focus();
				return false;
			}

			if(!$('#contents_body').val()){
				alert('내용을 입력하세요');
				$('#contents_body').focus();
				return false;
			}

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
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">메일즉시발송</h3>
            
            <!------ //  발송완료나 예약 취소 상태에서 내용보기로 들어온 경우 모든 폼은 readonly 상태 여야 함.------->
			<form action="/admin/mail/write" method="post" id="mail_form">
            <div id="strategy_view1" class="strategy_view">
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:96%;">
                <col width="160" /><col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">메일제목</td>
                        <td>   
                            <input id="subject" name="subject" type="text" title="메일제목" value="" required="required" />
                        </td>
                    </tr>
                    <!-- <tr>
                        <td class="thead">발송날짜/시간</td>
                        <td>   
                            발송날짜 : <input id="send_date" name="send_date" type="text" title="발송날짜" value="<?php echo date('Y.m.d', strtotime('+1 day')) ?>" class="datepicker" style="width:100px;" /> &nbsp;&nbsp;
                            발송시간 :                             
                            <div class="select open" style="width:90px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="send_time" id="time0" class="option" type="radio" checked="checked" /><label for="time0">선택</label></li> 
                                    <li><input name="send_time" id="time1" class="option" type="radio" value="0" checked="checked" /><label for="time1">00시</label></li>
                                    <li><input name="send_time" id="time2" class="option" type="radio" value="1" /><label for="time2">01시</label></li>
                                    <li><input name="send_time" id="time3" class="option" type="radio" value="2" /><label for="time3">02시</label></li>
                                    <li><input name="send_time" id="time4" class="option" type="radio" value="3" /><label for="time4">03시</label></li>
                                    <li><input name="send_time" id="time5" class="option" type="radio" value="4" /><label for="time5">04시</label></li>
                                    <li><input name="send_time" id="time6" class="option" type="radio" value="5" /><label for="time6">05시</label></li>
                                    <li><input name="send_time" id="time7" class="option" type="radio" value="6" /><label for="time7">06시</label></li>
                                    <li><input name="send_time" id="time8" class="option" type="radio" value="7" /><label for="time8">07시</label></li>
                                    <li><input name="send_time" id="time9" class="option" type="radio" value="8" /><label for="time9">08시</label></li>
                                    <li><input name="send_time" id="time10" class="option" type="radio" value="9" /><label for="time10">09시</label></li>
                                    <li><input name="send_time" id="time11" class="option" type="radio" value="10" /><label for="time11">10시</label></li>
                                    <li><input name="send_time" id="time12" class="option" type="radio" value="11" /><label for="time12">11시</label></li>
                                    <li><input name="send_time" id="time13" class="option" type="radio" value="12" /><label for="time13">12시</label></li>
                                    <li><input name="send_time" id="time14" class="option" type="radio" value="13" /><label for="time14">13시</label></li>
                                    <li><input name="send_time" id="time15" class="option" type="radio" value="14" /><label for="time15">14시</label></li>
                                    <li><input name="send_time" id="time16" class="option" type="radio" value="15" /><label for="time16">15시</label></li>
                                    <li><input name="send_time" id="time17" class="option" type="radio" value="16" /><label for="time17">16시</label></li>
                                    <li><input name="send_time" id="time18" class="option" type="radio" value="17" /><label for="time18">17시</label></li>
                                    <li><input name="send_time" id="time19" class="option" type="radio" value="18" /><label for="time19">18시</label></li>
                                    <li><input name="send_time" id="time20" class="option" type="radio" value="19" /><label for="time20">19시</label></li>
                                    <li><input name="send_time" id="time21" class="option" type="radio" value="20" /><label for="time21">20시</label></li>
                                    <li><input name="send_time" id="time22" class="option" type="radio" value="21" /><label for="time22">21시</label></li>
                                    <li><input name="send_time" id="time23" class="option" type="radio" value="22" /><label for="time23">22시</label></li>
                                    <li><input name="send_time" id="time24" class="option" type="radio" value="23" /><label for="time24">23시</label></li>
                                </ul>
                            </div> 
                        </td>
                    </tr>-->
                    <tr>
                        <td class="thead">타입선택</td>
                        <td>
                            <p>
                                <input name="mail_type" id="type1" class="option" type="radio" value="normal" checked="checked" /><label for="type1">공지메일</label>
                                <input name="mail_type" id="type2" class="option" type="radio" value="promotion" /><label for="type2">홍보메일</label>                      
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">타겟선택</td>
                        <td>
                            <p>
                                <input name="target_type1" id="target_type1" class="option"  type="checkbox" value="N" checked /><label for="target_type1">일반 회원</label>
                                <input name="target_type2" id="target_type2" class="option"  type="checkbox" value="P" checked /><label for="target_type2">PB 회원</label>                      
                                <input name="target_type3" id="target_type3" class="option"  type="checkbox" value="T" checked /><label for="target_type3">트레이더 회원</label><br>
                                <label for="target_type4">직접입력</label>
								<input id="target_type4_txt" name="target_type4_txt" type="text" title="" value="" style="width:200px;" />
								※ 직접입력시 입력한 메일주소로 발송됩니다.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">내용</td>
                        <td>   
                            <textarea style="height:400px;" name="contents_body" id="contents_body" required="required"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            
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