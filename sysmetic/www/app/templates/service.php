<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 제휴 서비스</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#ask_form').submit(function(){
			if(!$('#ask_body').val()){
				alert('내용을 입력해주세요.');
				return false;
			}

			$.post($(this).attr('action'), $(this).serialize(), function(data){
				if(data.result){
					alert('문의내용이 접수되었습니다');
					closeLayer('ask');
				}
			}, 'json');
			return false;
		});
	});
	</script>
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="service">제휴 서비스</h3>
            <div class="text">
                
                <!-- 전략가동대행 서비스 -->
                <div id="service0" name="service_view"  style="display:block;">
                    <div class="tab">
                        <button id="service_list_0" type="button" title="전략가동대행 서비스" class="tab_on" onclick="chg_tab(0, 0);"><span class="ir">전략가동대행 서비스</span></button>
                        <button id="service_list_1" type="button" title="전략개발대행 서비스" class="tab_off" onclick="chg_tab (0, 1);"><span class="ir">전략개발대행 서비스</span></button>
                        <button id="service_list_2" type="button" title="교육 서비스" class="tab_off tab_last" onclick="chg_tab (0, 2);"><span class="ir">교육 서비스</span></button>
                    </div>
                    <b>01. 서비스 대상 고객</b><br/>
                      -  &nbsp;하이투자증권 예스트레이더 고객으로서 시스템시장의 판매로직 구매자 <br/><br/>

                    <b>02. 모니터링 서비스</b><br/>
                      -  &nbsp;매매 PC의 HTS 접속 (전략 설정 및 자동 매매설정은 고객이 직접 실행.)<br/>
                      -  &nbsp;모니터링 직원은 직원은  24시간 매매 PC의 동작 상황을 모니터링 절차에 따라 간단한 조회만 실행. <br/>
                        &nbsp;&nbsp;&nbsp;인터넷 접속상황, HTS 접속상황, 시장 데이타 수신상황, 전략 실행 상황, 자동주문 체결 상황 등을 모니터링.<br/>
                      -  &nbsp;고객별 비상 상황에 대한 매뉴얼을 만들어, 비상 조치 및 비상 연락 등의 방법으로 위험을 최소화.<br/>
                      -  &nbsp;백업 시스템 제공 (비상시 메인 PC에서 백업 PC로 전환) <br/><br/>

                     <span class="txt">
                     &nbsp;&nbsp;&nbsp;*  모니터링 직원은 신규 주문, 정정주문, 취소주문 등의 주문행위를 할 수 없음.<br/>
                     &nbsp;&nbsp;&nbsp;*  주문 대리인 지정 불필요 (주문에 대한 실행의 경우 별도의 주문대리인 지정 및 계약을 통해서 이루어질 수 있음)</span><br/><br/>

                    <b>03. 모니터링 대상 매매 PC</b><br/>
                    -  &nbsp;본인 소유의 컴퓨터 (원격 모니터링),  본사에서 제공하는 호스팅 컴퓨터  둘 중 선택가능<br/><br/>

                    <b>04. 서비스 효과</b><br/>
                    -  &nbsp;안정적인 운용 지원 (시스템트레이딩 운용경험을 바탕으로 운영리스크 최소화 가능)<br/>
                    -  &nbsp;고객의 개인정보 보호 및 로직 보안에 우려 없음<br/><br/>
                    
                    <b>05. 서비스 비용</b><br/>
                    -  &nbsp;개별 상담 후 협의 결정<br/>

                    <br/><br/>

                    <p class="btn_board">
                        <a href="mailto:help@sysmetic.co.kr" title="서비스 문의하기" class="submit"><span class="ir">서비스 문의 하기</span></a>
                        <!--
                        <button type="button" title="서비스 문의하기" class="submit" onclick="<?php if($isLoggedIn()){ ?>showLayer('ask');<?php }else{ ?>location.href='/signin'<?php } ?>"><span class="ir">서비스 문의 하기</span></button>
                        -->
                    </p>
                </div>

                <!-- 전략개발대행 서비스 -->
                <div id="service1" name="service_view"  style="display:none;">
                    <div class="tab">
                        <button id="" type="button" title="전략가동대행 서비스" class="tab_off" onclick="chg_tab(1, 0);"><span class="ir">전략가동대행 서비스</span></button>
                        <button id="" type="button" title="전략개발대행 서비스" class="tab_on" onclick="chg_tab (1, 1);"><span class="ir">전략개발대행 서비스</span></button>
                        <button id="" type="button" title="교육 서비스" class="tab_off tab_last" onclick="chg_tab (1, 2);"><span class="ir">교육 서비스</span></button>
                    </div>
                    <b>01. 서비스 대상 고객</b><br/>
                      -  &nbsp;시스메틱 트레이더 회원으로서 매매아이디어가 있지만 예스랭귀지 작성이 어려우신 분 <br/><br/>

                    <b>02. 서비스 내용</b><br/>
                      -  &nbsp;개발언어 : 예스트레이더 랭귀지<br/>
                      -  &nbsp;개발 종목 : KOSPI200 지수, 선물·옵션, 주식, ETF 등 하이투자증권 예스트레이더로 매매 가능한 종목<br/>
                      -  &nbsp;특장점 : 단순한 문답형식이 아닌 1:1 맞춤서비스 제공<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;아이디어의 단순 코드 구현이 아닌 매매와 관련된 컨설팅 서비스 제공<br/>
                      -  &nbsp;개발진 : 시스템트레이딩 개발 및 실제 운용 경력 10년 이상 (증권,투자자문사 등)<br/><br/>

                    <b>03. 서비스 결과물 (예시)</b><br/>
                    -  &nbsp;예스랭귀지 코드(Signal, 필요 시 indicator 코드 포함)<br/>
                      -  &nbsp;화면틀(전략실행차트용, 시뮬레이션차트용)<br/>
                      -  &nbsp;기타 전략에 관련된 문서(코드의 설명 요약 및 전략 적용 시 주의점)<br/><br/>

                    <b>04. 서비스 비용</b><br/>
                    -  &nbsp;개별 상담 후 협의 결정<br/>
                    <br/><br/>

                    <p class="btn_board">
                        <button type="button" title="서비스 문의하기" class="submit" onclick="<?php if($isLoggedIn()){ ?>showLayer('ask');<?php }else{ ?>location.href='/signin'<?php } ?>"><span class="ir">서비스 문의 하기</span></button>
                    </p>
                </div>
                

                <!-- 담보투자(에스크로 펀딩) -->
                <div id="service2" name="service_view"  style="display:none;">
                    <div class="tab">
                        <button id="" type="button" title="전략가동대행 서비스" class="tab_off" onclick="chg_tab(2, 0);"><span class="ir">전략가동대행 서비스</span></button>
                        <button id="" type="button" title="전략개발대행 서비스" class="tab_off" onclick="chg_tab (2, 1);"><span class="ir">전략개발대행 서비스</span></button>
                        <button id="" type="button" title="교육 서비스" class="tab_on" onclick="chg_tab (2, 2);"><span class="ir">교육 서비스</span></button>
                    </div>
                    <div class="text">
                    <a href="https://www.airklass.com/pr/promotion?id=22" target="blank"><img src="/img/post03.png" ></a> 
			</div>
                </div>

            </div>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

<?php if($isLoggedIn()){ ?>
<div id="ask" class="layer" style="top:1000px; left:20px; display:none;">
    <div class="layer_head">
        <p class="ask">문의하기</p>
        <span class="layer_close" onclick="closeLayer('ask');">X</span>
    </div>
    
    <div class="ask_form">  
		<form action="/service/ask" method="post" id="ask_form">
        <fieldset>
            <legend>문의하기</legend>
            <textarea name="ask_body" id="ask_body" required="required"></textarea>
            <p class="btn_layer">
                <button type="submit" title="문의하기" class="submit"><span class="ir">문의하기</span></button>
                <button type="reset" title="닫기" class="cancel" onclick="closeLayer('ask');"><span class="ir">닫기</span></button>
            </p>
        </fieldset>
		</form>
    </div>
</div>
<?php } ?>

</body>
</html>
