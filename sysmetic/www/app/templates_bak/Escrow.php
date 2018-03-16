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
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="Escrow">담보투자</h3>
            <div class="text Escrow">
                <div id="invest0" name="invest" style="display:block;">
                    <div class="tab">
                        <button id="" type="button" title="에스크로 투자" class="tab_on" onclick="chg_tab3 (0, 0);"><span class="ir">에스크로 투자</span></button>
                        <button id="" type="button" title="크라우딩 투자" class="tab_off  tab_last" onclick="chg_tab3 (0, 1);"><span class="ir">크라우딩 투자</span></button>
                    </div>

                    <ul>
                        <li>
                            <strong>에스크로 투자</strong>라고하는 것은 일정 담보금액을 에스크로(담보)해놓고 담보금의 5배~10배 규모의 계좌를 운용하는 것이며, <br />
                            일종의 레버리지투자라 할 수 있습니다.</li>
                        <li>
                            <strong>에스크로 투자</strong>는 고정수익을 추구하는 계좌주와 레버리지효과를 추구하는 운용자의 2자(者)사이의<br />
                            "공동투자계약" 개념으로 진행됩니다.<br />
                        </li>
                        <li>
                            <strong>에스크로 투자</strong>는 기존의 대여계좌와 엄밀히 다르며, RMS(위험관리 시스템) 전문업체의 관리를 통하여 계좌주는 원금보전과 <br />
                            고정수익을 확보하게 되고 또한 운영자는 일정한 위험관리하에 수익의 대부분을 향유할 수 있는 장점이 있습니다.
                        </li>
                    </ul>

                    <p class="more_info">
                    * &nbsp;공동투자에 따른 계좌주에 대한 수익은 일정한 월 확정이율로 결정되며, <br />
                    &nbsp;&nbsp;&nbsp;이 확정이율을 초과하는 운용수익은 전부 운영자의 몫이 됩니다.  이는 매월 말일을 기준으로 정산합니다. <br />
                    * &nbsp;에스크로 투자는 본 시스메틱과는 전혀 관련이 없으며 RMS 업체에서 모든 계약을 진행합니다.
                    </p>

                   
                    <p class="description">매매가능 증권/선물회사 : 하이투자, 하나대투, 한국투자, 이베스트, NH투자, 현대선물, NH선물</p>
                    <table border="0" cellspacing="1" cellpadding="0" class="intro">
                    <col width="*" /><col width="150" /><col width="120" /><col width="120" /><col width="120" /><col width="120" />
                        <thead>
                        <tr>
                            <td rowspan="2">에스크로 가능상품</td>
                            <td rowspan="2">담보율 (최저)</td>
                            <td colspan="4">예시</td>
                        </tr>
                        <tr>
                            <td>계좌주</td>
                            <td>투자가</td>
                            <td>총투자금액</td>
                            <td>로스컷</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="thead">선물 데이</td>
                            <td>10%</td>
                            <td>1억</td>
                            <td>0.1억</td>
                            <td>1.1억</td>
                            <td>1억3백만</td>
                        </tr>
                        <tr>
                            <td class="thead">옵션 매수/매도 데이</td>
                            <td>15%</td>
                            <td>1억</td>
                            <td>0.15억</td>
                            <td>1.15억</td>
                            <td>1억450만</td>
                        </tr>
                        <tr>
                            <td class="thead">선물/옵션 매수/매도 데이</td>
                            <td>15%</td>
                            <td>1억</td>
                            <td>0.15억</td>
                            <td>1.15억</td>
                            <td>1억450만</td>
                        </tr>
                        <tr>
                            <td class="thead">선물 포지션</td>
                            <td>20%</td>
                            <td>1억</td>
                            <td>0.2억</td>
                            <td>1.2억</td>
                            <td>1억6백만</td>
                        </tr>
                        <tr>
                            <td class="thead">선물/옵션 포지션</td>
                            <td>20%</td>
                            <td>1억</td>
                            <td>0.2억</td>
                            <td>1.2억</td>
                            <td>1억6백만</td>
                        </tr>
                        <tr>
                            <td class="thead">해외선물 포지션</td>
                            <td>20%</td>
                            <td>1억</td>
                            <td>0.2억</td>
                            <td>1.2억</td>
                            <td>1억6백만</td>
                        </tr>
                        <tr>
                            <td class="thead">주식 데이</td>
                            <td>10%</td>
                            <td>1억</td>
                            <td>0.1억</td>
                            <td>1.1억</td>
                            <td>1억3백만</td>
                        </tr>
                        <tr>
                            <td class="thead">주식 포지션</td>
                            <td>30%</td>
                            <td>1억</td>
                            <td>0.3억</td>
                            <td>1.3억</td>
                            <td>1억9백만</td>
                        </tr>
                        </tbody>
                    </table>

                    - 예시표 이외의 기타조건은 메일로 문의<br />
                    - 문의사항 : <a href="mailto:Escrow@sysmetic.co.kr">Escrow@sysmetic.co.kr</a> (에스크로 가능상품, 요청금액을 연락처와 함께 기재바람)<br /><br /><br />
                </div>

                <div id="invest1" name="invest" style="display:none;">
                    <div class="tab">
                        <button id="" type="button" title="에스크로 투자" class="tab_off" onclick="chg_tab3 (1, 0);"><span class="ir">에스크로 투자</span></button>
                        <button id="" type="button" title="크라우딩 투자" class="tab_on" onclick="chg_tab3 (0, 1);"><span class="ir">크라우딩 투자</span></button>
                    </div>

                    <p style="padding-bottom:150px;">
                        에스크로 공동 투자자금 형태로 확정수익을 바라는 확정투자가형 투자가와<br />
                        고위험을 안고 시스템 전략에 투자하려는 고위험 고객의 투자자금을 공동투자방식으로 모집하는 방식을 의미합니다.<br />
                        절차와 방법은 추후 안내해드리겠습니다.
                    </p>
                </div>

            </div>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>