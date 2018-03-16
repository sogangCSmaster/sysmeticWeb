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

			<form action="/admin/notice/<?php echo $post['notice_id'] ?>/edit" method="post" id="notice_form">
            <div id="strategy_view1" class="strategy_view">
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:96%;">
                <col width="160" /><col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">제목</td>
                        <td>   
                            <input id="subject" name="subject" type="text" title="공지사항 제목" value="<?php echo htmlspecialchars($post['subject']) ?>" required="required" />
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
                                    <li><input name="is_open" id="open1" class="option" type="radio" value="1"<?php if($post['is_open']) echo ' checked="checked"' ?> /><label for="open1">공개</label></li>
                                    <li><input name="is_open" id="open2" class="option" type="radio" value="0"<?php if(!$post['is_open']) echo ' checked="checked"' ?> /><label for="open2">비공개</label></li>
                                </ul>
                            </div> 
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">내용</td>
                        <td>   
                            <textarea id="contents_body" name="contents_body" style="height:400px;" required="required"><?php echo htmlspecialchars($post['contents']) ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">이미지 첨부</td>
                        <td>
                            첨부된 파일 : <a href="#" target="_blank">asdff.jpg</a> <br />
                            <input id="img_file" name="img_file" type="text" title="첨부이미지" value="" style="width:500px;" onclick="document.getElementById('file_file').click();" readonly="readonly"  />
                            <button type="button" title="찾아보기" class="act" onclick="document.getElementById('file_file').click();" value="찾아보기"><span class="ir">찾아보기</span></button>

                            <input id="file_img" name="file" type="file" title="첨부이미지" value="" style="display:none;" onchange=" document.getElementById('img_file').value = this.value"  />
                        </td>
                    </tr>
                    </tbody>
                </table>

            
                <p class="btn_area">
                    <button type="submit" title="등록" class="submit"><span class="ir">수정</span></button>
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