<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 교육안내</title>	
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
					$('#ask_body').val('');
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
            <h3 class="training">교육안내</h3>
            <div class="text">
                <b>01. SYSTEM TRADING 교육</b><br/>
                  -  &nbsp;매달 6회 순환교육 실시 중(06년 1월 ~ 현재) <br/>
                  -  &nbsp;매월 30~40名의 수강생 참가(현재까지 약 4,500명 수강, 국내최대)<br/>
                  -  &nbsp;월1회 특강실시(강사 : 시스템 개발회사 대표, 일임자문사 대표 등) 및 분기별 명사특강<br/>
                  -  &nbsp;장소 : 하이투자증권 교대역 지점 교육장(PC 3O대 보유)<br/><br/>

                <b>02. 교육의 목적</b><br/>
                  -  &nbsp;시스템 트레이딩 시장 활성화<br/>
                  -  &nbsp;지속적인 교육을 통한 고객 DB확보<br/>
                  -  &nbsp;우수한 교육생들의 시스템 개발능력 지원<br/><br/>

                <b>03. 교육 커리큘럼</b><br/>
                <table border="0" cellspacing="1" cellpadding="0" class="statistics">
                <col width="127" /> <col width="*" /><col width="127" />
                <thead>
                    <tr>
                        <td></td>
                        <td>교육과정</td>
                        <td>정원</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="thead">1일차</td>
                        <td>예스트레이더 사용방법 (1인 1PC 교육)</td>
                        <td>30명</td>
                    </tr>
                    <tr>
                        <td class="thead">2일차</td>
                        <td>차트분석 / 기술적 문석 기법 & 옵션 시뮬레이터</td>
                        <td>30명</td>
                    </tr>
                    <tr>
                        <td class="thead">3일차</td>
                        <td>시스템 트레이딩 기법 & 자금 관리 전략 / 터틀 트레이딩</td>
                        <td>30명</td>
                    </tr>
                    <tr>
                        <td class="thead">4일차</td>
                        <td>예스트레이더 랭귀지 실습</td>
                        <td>30명</td>
                    </tr>
                    <tr>
                        <td class="thead">5일차</td>
                        <td>시스템 트레이딩 실전 전략</td>
                        <td>30명</td>
                    </tr>
                    <tr>
                        <td class="thead">6일차</td>
                        <td>옵션 시스템 & 전략기법</td>
                        <td>30명</td>
                    </tr>
                    <tr>
                        <td rowspan="2" class="thead">특강</td>
                        <td>실전 매매전략 소개</td>
                        <td>20명(별도통보)</td>
                    </tr>
                    <tr>
                        <td>예스트레이더 마스터 과정</td>
                        <td>14명(별도통보)</td>
                    </tr>
                </tbody>
                </table>
                <span class="txt"><b>* 특강 참가자격</b> :  하이투자증권 교대역 지점 거래 고객으로 모든 교육과정을 이수한 사람에 한해 참석 가능</span><br/><br/>

                <b>04. 교육이수 후 매매고객 혜택</b><br/>
                -  &nbsp;옵션 시뮬레이터 무료사용 (옵션 과거 월믈 8년간 가격자료 제공)<br/>
                -  &nbsp;시스템 트레이딩 전략Kit 제공 (전략/시스템 매매 학술논문 게재 CD포함) – 매매고객 한함<br/>
                - &nbsp;네이버 시스메틱 동호회 정회원 자격 부여 (<a href="http://cafe.naver.com/sysmetic/" target="_blank">http://cafe.naver.com/sysmetic</a>)
                
                <br/><br/><br/>

                <p class="btn_board">
                        <a href="https://www.yesstock.com/board_new/view.asp?Ext=0&db=board100075&num=57&startpage=1" target="_blank" title="교육일정 보기" class="submit"><span class="ir">교육일정 보기</span></a>
                </p>
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
		<form action="/training/ask" method="post" id="ask_form">
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