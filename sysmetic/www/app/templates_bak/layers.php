<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders</title>	
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
</head>

<body>

<!-- 추가정보 입력 -->
<div id="profile_layer" class="layer" style="top:20px; left:490px; display:block;">
    <div class="layer_head">
        <p class="add_profile">추가정보 입력</p>
        <span class="layer_close" onclick="closeLayer('daily_write1');">X</span>
    </div>
    
    <div class="login_form">        
        <fieldset class="profile">
            <legend>추가정보 입력</legend>
            <dl>
                <dt>이름</dt>
                    <dd>
                    <input id="" name="" type="text" title="이름" onclick="this.class=''; this.value='';" class="ready" value="사이트 이용 시 사용할 이름 입력" />
                </dd>
                <dt>이메일</dt>
                <dd>
                    <input id="" name="" type="text" title="이메일" onclick="this.class=''; this.value='';" class="ready" value="관련 정보 수신을 위한 이메일 주소 입력" />
                </dd>    
                <dt>휴대폰</dt>
                <dd>
                    <input id="" name="" type="tel" title="휴대폰" onclick="this.class=''; this.value='';" class="ready" value="상담 시 필요한 휴대폰 정보를 '-' 없이 입력" maxlength="11" />
                </dd>    
            </dl>

            <div class="agree_mail">
                <ul>
                    <li class="agree">
                        <p><input name="agree" id="agree1" class="option" type="checkbox" checked="checked" /><label for="agree1">관심 전략과 관심 포트폴리오 관련 정보를 수신 동의합니다.</label> </p>
                    </li>
                    <li> 
                        <p><input name="agree" id="agree2" class="option" type="checkbox" /><label for="agree2">전략 및 정보성 알림에 수신 동의합니다. </label></p>
                    </li>
                </ul>
            </div>

            <p class="btn_layer">
                <button id="" type="" title="추가정보 입력" class="submit"><span class="ir">추가정보 입력</span></button>
                <button id="" type="" title="건너뛰기" class="cancel"><span class="ir">건너뛰기</span></button>
            </p>
        </fieldset>
    </div>
</div>

</body>
</html>