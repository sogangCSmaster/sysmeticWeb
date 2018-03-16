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
		$('#broker_form').submit(function(){
			if(!$('#company').val()){
				alert('회사명을 입력해주세요');
				$('#company').focus();
				return false;
			}
			
			// 반드시 1개 이상의 매매툴이 입력되어야함
			if(!$('#system_trading_name').val()){
				alert('시스템트레이딩을 입력해주세요');
				$('#system_trading_name').focus();
				return false;
			}

			return true;
		});

        var trading_add = 1;
        $('#trading_add').on('click', function(){
            $('#trading' + trading_add++).show();
        });

        var api_add = 1;
        $('#api_add').on('click', function(){
            $('#api' + api_add++).show();
        });
	});
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">증권사 관리</h3>
            
			<form action="/admin/brokers/write" method="post" enctype="multipart/form-data" id="broker_form">
            <table border="0" cellspacing="0" cellpadding="0" class="admin_write">
            <col width="160" /> <col width="*" /><col width="160" /> <col width="180" />
            <tbody>
                <tr>
                    <td class="thead">회사명</td>
                    <td colspan="3">
                        <input id="company" name="company" type="text" title="회사명" required="required" />
                    </td>
                </tr>
                <tr>
                    <td class="thead">로고이미지</td>
                    <td colspan="3">					
						<input id="img_logo" name="img_logo" type="text"  title="로고이미지" class="file_input_textbox" style="width:400px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_logo" name="logo" type="file" title="로고이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_logo').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr>
                    <td class="thead">로고이미지_S</td>
                    <td colspan="3">				
						<input id="img_logo_s" name="img_logo_s" type="text"  title="로고이미지" class="file_input_textbox" style="width:400px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_logo_s" name="logo_s" type="file" title="로고이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_logo_s').value = this.value" />
						</div>
                        <!-- <img src="../img/sample1.jpg" class="broker_logo" />
                        <input id="img_logo_s" name="img_logo_s" type="text" title="로고이미지_s" value="" style="width:400px;" onclick="document.getElementById('file_logo_s').click();" readonly="readonly"  />
                        <button type="button" title="찾아보기" class="act" onclick="document.getElementById('file_logo_s').click();" value="찾아보기"><span class="ir">찾아보기</span></button>

                        <input id="file_logo_s" name="logo_s" type="file" title="로고이미지_s" value="" style="display:none;" onchange=" document.getElementById('img_logo_s').value = this.value"  /> -->
                    </td>
                </tr>
                <tr><td colspan="4" class="line"></td></tr>
                <tr>
                    <td class="thead">종류</td>
                    <td colspan="3">
                        <p>
                            <input id="type1" name="company_type" type="radio" title="증권사" value="증권사" checked="checked"  /> <label for="type1">증권사</label>&nbsp;&nbsp;
                            <input id="type2" name="company_type" type="radio" title="선물사" value="선물사"  /> <label for="type2">선물사</label>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="thead">노출여부</td>
                    <td colspan="3">
                        <p>
                            <input id="open1" name="is_open" type="radio" title="노출" value="1" checked="checked"  /> <label for="open1">노출</label>&nbsp;&nbsp;
                            <input id="open2" name="is_open" type="radio" title="비노출" value="0" /> <label for="open2">비노출</label>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="thead">메인배너 링크연결</td>
                    <td colspan="3">
                        <p>
                            <input id="main1" name="is_main" type="radio" title="노출" value="1"  /> <label for="main1">노출</label>&nbsp;&nbsp;
                            <input id="main2" name="is_main" type="radio" title="비노출" value="0" checked="checked" /> <label for="main2">비노출</label>
                        </p>
                    </td>
                </tr>
                <tr><td colspan="4" class="line"></td></tr>
                <tr>
                    <td class="thead">홈페이지 URL</td>
                    <td colspan="3">
                        <input id="url" name="url" type="text" title="홈페이지 URL" />
                    </td>
                </tr>
                <tr>
                    <td class="thead">계좌개설 URL</td>
                    <td colspan="3">
                        <input id="url2" name="url2" type="text" title="계좌개설 URL" />
                    </td>
                </tr>
                <tr>
                    <td class="thead">국내상품</td>
                    <td colspan="3">
                        <input id="domestic" name="domestic" type="text" title="국내상품"  />
                    </td>
                </tr>
                <tr>
                    <td class="thead">해외상품</td>
                    <td colspan="3">
                        <input id="overseas" name="overseas" type="text" title="해외상품"  />
                    </td>
                </tr>
                <!--
                <tr>
                    <td class="thead">F/X</td>
                    <td>
                        <input id="fx" name="fx" type="text" title="F/X" />
                    </td>
                    <td class="thead">DMA</td>
                    <td>
                        <input id="dma" name="dma" type="text" title="DMA" />
                    </td>
                </tr>
                -->
                <tr><td colspan="4" class="line"></td></tr>
                <tr>
                    <td class="thead">매매툴</td>
                    <td>
                        <input id="system_trading_name" name="system_trading_name" type="text" title="시스템 트레이딩" style="width:150px;" required="required"  />
                    </td>
                    <td colspan="2">
						<input id="img_trading" name="img_trading" type="text"  title="시스템 트레이딩 이미지" class="file_input_textbox" style="width:170px;" readonly="readonly">
						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_trading" name="system_trading_image" type="file" title="시스템 트레이딩 이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_trading').value = this.value" />
						</div>
							<input type="button" title="찾아보기" value="+" class="file_input_button act" id="trading_add"></button>
                    </td>
                </tr>
				<tr id="trading1" style="display:none">
                    <td class="thead">매매툴</td>
                    <td>
                        <input id="system_trading_name1" name="system_trading_name1" type="text" title="시스템 트레이딩" style="width:150px;"  />
                    </td>
                    <td colspan="2">
						<input id="img_trading1" name="img_trading1" type="text"  title="시스템 트레이딩 이미지" class="file_input_textbox" style="width:200px;" readonly="readonly">
						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_trading1" name="system_trading_image1" type="file" title="시스템 트레이딩 이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_trading1').value = this.value" />
						</div>
                    </td>
                </tr>
				<tr id="trading2" style="display:none">
                    <td class="thead">매매툴</td>
                    <td>
                        <input id="system_trading_name2" name="system_trading_name2" type="text" title="시스템 트레이딩" style="width:150px;"  />
                    </td>
                    <td colspan="2">
						<input id="img_trading2" name="img_trading2" type="text"  title="시스템 트레이딩 이미지" class="file_input_textbox" style="width:200px;" readonly="readonly">
						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_trading2" name="system_trading_image2" type="file" title="시스템 트레이딩 이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_trading2').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr>
                    <td class="thead">API</td>
                    <td>
                        <input id="api_name" name="api_name" type="text" title="API이미지" style="width:150px;"  />
                    </td>
                    <td colspan="2">
						<input id="img_api" name="img_api" type="text"  title="API이미지" class="file_input_textbox" style="width:170px;" readonly="readonly">
						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_api" name="api_image" type="file" title="API이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_api').value = this.value" />
						</div>
							<input type="button" title="찾아보기" value="+" class="file_input_button act" id="api_add"></button>
                    </td>
                </tr>
                <tr id="api1" style="display:none">
                    <td class="thead">API</td>
                    <td>
                        <input id="api_name1" name="api_name1" type="text" title="API이미지" style="width:150px;"  />
						
                    </td>
                    <td colspan="2">
						<input id="img_api1" name="img_api1" type="text"  title="API이미지" class="file_input_textbox" style="width:200px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_api1" name="api_image1" type="file" title="API이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_api1').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr id="api2" style="display:none">
                    <td class="thead">API</td>
                    <td>
                        <input id="api_name2" name="api_name2" type="text" title="API이미지" style="width:150px;"  />
						
                    </td>
                    <td colspan="2">
						<input id="img_api2" name="img_api2" type="text"  title="API이미지" class="file_input_textbox" style="width:200px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_api2" name="api_image2" type="file" title="API이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_api2').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr><td colspan="4" class="line"></td></tr>
            </tbody>
            </table>

            <p class="btn_area">
                <button type="submit" title="수정" class="submit"><span class="ir">등록</span></button>
                <a href="/admin/brokers" title="취소" class="cancel"><span class="ir">취소</span></a>
            </p>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>