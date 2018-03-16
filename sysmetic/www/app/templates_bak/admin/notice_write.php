<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 공지사항 등록</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#notice_form').submit(function(){
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
            <h3 class="admin_notice_write">공지사항 등록</h3>

			<form action="/admin/notice/write" method="post" id="notice_form" enctype="multipart/form-data">
            <div id="strategy_view1" class="strategy_view">
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:96%;">
                <col width="160" /><col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">제목</td>
                        <td>   
                            <input id="subject" name="subject" type="text" title="공지사항 제목" value="" required="required" />
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">공개여부</td>
                        <td>
                            <div class="select open" style="width:90px;">
                                <div class="myValue"></div>
                                <ul class="iList">
									<!--
                                    <li><input name="open" id="open0" class="option" type="radio" value="" checked="checked" /><label for="open0">선택</label></li>
									-->
                                    <li><input name="is_open" id="open1" class="option" type="radio" value="1" checked="checked" /><label for="open1">공개</label></li>
                                    <li><input name="is_open" id="open2" class="option" type="radio" value="0" /><label for="open2">비공개</label></li>
                                </ul>
                            </div> 
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">내용</td>
                        <td>   
                            <textarea id="contents_body" name="contents_body" style="height:400px;" required="required"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">이미지 첨부</td>
                        <td>
							<input id="img_file" name="img_file" type="text"  title="이미지" class="file_input_textbox" style="width:500px;" readonly="readonly">

							<div class="file_input_div">		
								<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
								<input id="file_img" name="file" type="file" title="이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_file').value = this.value" />
							</div>
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