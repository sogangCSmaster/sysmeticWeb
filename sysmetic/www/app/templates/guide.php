<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 이용안내</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>   
    var displayTab = function(number) {
        //$('div > div').css('display', 'none');
        $('#guide' + number).css('display', 'block');
    };

    $(document).ready(function() {
        var address = unescape(location.href);
        var param = "";
        if(address.indexOf("tab", 0) != -1) {
            param = address.substring(address.indexOf("tab", 0) + 4);
        } else {
            param = "0";
        }
        displayTab(param);
    });
    </script>
</head>

<body>
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="guide">이용 안내</h3>
            <div class="text guide">
                <p class="banner"><img src="/img/guide_banner.jpg" /></p>
                         
                <div id="guide0" name="guide" class="guide_view" style="display:none;">
                    <div class="tab">
                        <button id="" type="button" title="투자가" class="tab_on" onclick="chg_tab2 (0, 0);"><span class="ir">투자가</span></button>
                        <button id="" type="button" title="브로커" class="tab_off" onclick="chg_tab2 (0, 1);"><span class="ir">브로커</span></button>
                        <button id="" type="button" title="개발자/트레이더" class="tab_off" onclick="chg_tab2 (0, 2);"><span class="ir">개발자/트레이더</span></button>
                        <button id="" type="button" title="수수료(예정)" class="tab_off tab_last" onclick="chg_tab2 (0, 3);"><span class="ir">수수료(예정)</span></button>
                    </div>

                    전략을 사용하려는 투자가들은 시스메틱 트레이더의 투자가로 등록하여 원하는 전략을 브로커와 연계하여 위탁 운용이 가능합니다. <br />
                    다음 프로세스를 통해 공개되는 전략의 투자가 가능합니다.<br />
                    먼저 원하시는 <strong>"1) 전략 선택  -> 2) 브로커 문의하기  -> 3) 해당 전략이 운용되고 있는 증권/선물사의 브로커와 연결 -> 4)계좌 개설 및 HTS 주문대리인 체결 -> 5) 운용개시"</strong>를 통해 공개되는 전략의 투자가 가능합니다.<br /><br />
                    
                    <p><img src="/img/guide_img.jpg" /></p>
                    <strong>전략에 소개되어 있는 최소 금액 이상만 위탁 운용 가능</strong>하며 대표 전략 성과와 위탁 운용성과가 주문 시차나 운용 사정상 조금씩<br />손익의 차이가 발생할 수 있습니다. 전략 운용성과에 대한 성과보수 배분과 조건들은 각 대표전략을 운용하는 개발자와 협의하여<br />결정하게 됩니다. <br /><br />
                    참고로 전략을 펀딩하게 되면 "투자가의 수"와 "펀딩금액"이 대표전략에 표시되며 향후 운용을 위탁한 계좌도 필명으로 공개 가능합니다.<br /><br />                    
                    
                    <p class="btn_board">
                        <a href="/strategies?open_search=1" title="투자 시작하기" class="submit"><span class="ir">투자 시작하기</span></a>
                    </p>
                </div>
                
                <div id="guide1" name="guide" class="guide_view" style="display:none;">
                    <div class="tab">
                        <button id="" type="button" title="투자가" class="tab_off" onclick="chg_tab2 (1, 0);"><span class="ir">투자가</span></button>
                        <button id="" type="button" title="브로커" class="tab_on" onclick="chg_tab2 (1, 1);"><span class="ir">브로커</span></button>
                        <button id="" type="button" title="개발자/트레이더" class="tab_off" onclick="chg_tab2 (1, 2);"><span class="ir">개발자/트레이더</span></button>
                        <button id="" type="button" title="수수료(예정)" class="tab_off tab_last" onclick="chg_tab2 (1, 3);"><span class="ir">수수료(예정)</span></button>
                    </div>
                    증권사/선물사의 PB나 영업 브로커가 보유한 자문사의 운용 레코드나 전문 투자브띠끄의 운용레코드, 개인 전업투자가들의<br />운용성과를 발굴하여 시스메틱 트레이더를 통해 공개가 가능하며 <strong>외부 투자가들과 위탁 운용을 중개, 연결</strong>할 수 있습니다.<br /><br />
                    훌륭한 개발자와 트레이더를 보유한 증권/선물사의 PB나 브로커는 시스메틱 트레이더 라는 새로운 영업채널을 확보하고 <br />이를 연계하여 영업망을 확충해나갈 수 있으며 타 증권사와 선물사의 개발자와 트레이더를 보유한 다른 PB와 브로커들과 협력하여 고객의 금융상품 포트폴리오를 다양화하여 궁극적으로 수익률 극대화를 가져나갈 수 있습니다.<br /><br />
                    또한 다른 증권/선물사의 PB와 브로커들과 협력하여 본인 회사에서 보유하지 않은 금융상품 POOL이나 TOOL들을 공유하여 경쟁이 아닌 상생의 형태로 협력적인 네크워크를 구축해나갈 수 있습니다.<br /><br />                    
                    
                    <p class="btn_board">
						<?php if($isLoggedIn() && ($_SESSION['user']['user_type'] == 'N' || $_SESSION['user']['user_type'] == 'T')){ ?>
                        <a href="/join_broker" title="브로커 회원 등록하기" class="submit"><span class="ir">브로커 회원 등록하기</span></a>
						<?php }else { ?>
						<a href="#" title="브로커 회원 등록하기" class="submit" onclick="alert('일반회원과 트레이더 회원만 등록 가능합니다.');return false;"><span class="ir">브로커 회원 등록하기</span></a>
						<?php } ?>
                   </p>
                </div>
                
                <div id="guide2" name="guide" class="guide_view" style="display:none;">
                    <div class="tab">
                        <button id="" type="button" title="투자가" class="tab_off" onclick="chg_tab2 (2, 0);"><span class="ir">투자가</span></button>
                        <button id="" type="button" title="브로커" class="tab_off" onclick="chg_tab2 (2, 1);"><span class="ir">브로커</span></button>
                        <button id="" type="button" title="개발자/트레이더" class="tab_on" onclick="chg_tab2 (2, 2);"><span class="ir">개발자/트레이더</span></button>
                        <button id="" type="button" title="수수료(예정)" class="tab_off tab_last" onclick="chg_tab2 (2, 3);"><span class="ir">수수료(예정)</span></button>
                    </div>
                    시스템 개발자들이나 트레이더들은 크게 System Trading 부문과 Manual Trading 부문 2개의 부문으로 본인의 전략성과와 <br />운용레코드들을 시스메틱 트레이더에 직접 등록 할 수 있습니다. <br /><br />
                    <p>
                        <img src="/img/img_guide.jpg" style="width:600px;" /><br />
                        <span>[전략등록 페이지]</span>
                    </p>

                    System Trading부문은 예스트레이더나 MC, 트레이드 스테이션이나 API 와 같은 TOOL을 통해 100% 자동화하여 운용되는 형태를 의미하며 Manual Trading부문은 옵션 합성전략이나 자동화가 불가능하지만 일정한 원칙과 전략을 통해 일관성 있게 운용되는 방법을 의미합니다. <br />
                    또한 전략의 공개, 비공개 여부를 선택하여 등록, 관리할 수 있습니다. <br />만약 당신이 자문사, 시스템 개발사나 투자 브띠끄를 운영하고 있다면 당신이 운영, 관리하고 있는 여러개의 계좌 레코드 실적을 <br />시스메틱 트레이더를 통해 비공개로 관리할 수도 있습니다.<br /><br />
                    그리고 다른 투자가들의 자금을 위탁 운용하여 맡고자 한다면 계좌번호를 지운 HTS의 기간별 손익 화면에 대한 이미지 캡춰파일<br /> 등록을 통해 계좌손익의 실 인증을 받드시 받아야 합니다.  실 인증은 운용되고 있는 증권사나 선물사의 계좌를 관리하고 있는 PB나 브로커가 대신하여 등록할 수 도 있으며 본인인 직접 등록 가능합니다. 다만 다른 투자가들의 자금을 위탁운용해야 한다면<br /> 반드시 <strong>시스메틱 트레이더와 제휴되어 있는 증권사의 PB나 브로커에게 관리계좌를 이관</strong>하시고 6개월 이상의 실전 운용 레코드<br />실적을 갖추어야 할 것입니다.<br /><br />                   
                    
                    <p class="btn_board">
				        <?php if($isLoggedIn() && $_SESSION['user']['user_type'] == 'N'){ ?>
                        <a href="/settings/edit" title="트레이더 가입하기" class="submit"><span class="ir">트레이더 가입하기</span></a> 
                        <?php }else{ ?>
                        <a href="#" title="트레이더 가입하기" class="submit" onclick="alert('일반회원만 회원타입 변경이 가능합니다.');return false;"><span class="ir">트레이더 가입하기</span></a>
						<?php } ?>
                    </p>
                </div>

                <div id="guide3" name="guide" class="guide_view" style="display:none;">
                    <div class="tab">
                        <button id="" type="button" title="투자가" class="tab_off" onclick="chg_tab2 (3, 0);"><span class="ir">투자가</span></button>
                        <button id="" type="button" title="브로커" class="tab_off" onclick="chg_tab2 (3, 1);"><span class="ir">브로커</span></button>
                        <button id="" type="button" title="개발자/트레이더" class="tab_off" onclick="chg_tab2 (3, 2);"><span class="ir">개발자/트레이더</span></button>
                        <button id="" type="button" title="수수료(예정)" class="tab_on" onclick="chg_tab2 (3, 3);"><span class="ir">수수료(예정)</span></button>
                    </div>
                    <div>
                        시스메틱 트레이더를 통해 유치된 자금을 통해 수익이 발생하여 운용수익을 배분하게 되는 경우 개발자나 트레이더는 일정 수수료(현재 10%)를 배분수익에서 시스메틱 트레이더에 지급합니다.<br /> 단,  <strong>이 일정 수수료는 전체 운용개시 자금의 1%를 초과할 수 없습니다</strong>. <br /><br />
                        예를 들어 1억의 자금으로 1천만원 수익배분발생시 (투자가) 7:3 (운용자) 배분인 경우 3백만원의 수익이 운용자의 배분수익이 됩니다. 여기서 10%인 30만원의 후취 수수료가 발생하고 이 금액은 1억의 1%인 100만원을 초과하지 못합니다.<br />
                        하지만 투자가가 지급하게 되는 수수료나 선취는 전혀 발생하지 않습니다.
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>