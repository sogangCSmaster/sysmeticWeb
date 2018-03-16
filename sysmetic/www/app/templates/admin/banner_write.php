<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 공지사항 등록</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
    <script type="text/javascript" src="/js/calendar.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script type="text/javascript" src="/js/jquery.iframe-transport.js"></script>
	<script type="text/javascript" src="/js/jquery.fileupload.js"></script>
	<script>
	$(function(){
		$('#banner_form').submit(function(){

			if(!$('#subject').val()){
				alert('제목을 입력하세요');
				$('#subject').focus();
				return false;
			}

			return true;
		});
        
        
        // 이미지 등록
		$('#images').fileupload({
			forceIframeTransport: true,
			dataType: 'json',
			done: function (e, data) {
                var result = data.result;
                if (result.success) {
                    $('#banner_image').val(result.savename);
                    $('#img_prev').attr('src', '/data/banner/'+result.savename);
                } else {
                    alert(result.msg);
                }
			},
			change: function (e, data) {
				$.each(data.files, function (index, file) {
				});
			},
			fail: function (e, data) {
				alert(data.textStatus);
			}
		});

	});
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">배너 관리</h3>

            <? if ($post['bidx']) { ?>
			<form action="/admin/banner/<?=$post['bidx']?>/edit" method="post" id="banner_form" enctype="multipart/form-data">
            <? } else { ?>
			<form action="/admin/banner/write" method="post" id="banner_form" enctype="multipart/form-data">
            <? } ?>
            <div id="strategy_view1" class="strategy_view">
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:96%;">
                <col width="160" /><col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">제목</td>
                        <td>   
                            <input id="subject" name="subject" type="text" title="제목" value="<?=$post['subject']?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">노출기간</td>
                        <td>
                            시작일 : <input id="start_day" name="start_day" type="text" value="<?=str_replace('-', '.', substr($post['start_date'], 0, 10))?>" style="width:100px"  class="datepicker"/>
                            시간 : 
                                    <div class="select open" style="width:80px;">
                                        <div class="myValue"></div>
                                        <ul class="iList">
                                            <?
                                            for ($i=0; $i<24; $i++) {
                                                $v = sprintf("%02d", $i);
                                            ?>
                                            <li><input name="start_h" id="start_h<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['start_h'] == $v) ? 'checked' : ''?> /><label for="start_h<?=$i?>"><?=$v?></label></li>
                                            <? } ?>
                                        </ul>
                                    </div>
                                    <div class="select open" style="width:80px;">
                                        <div class="myValue"></div>
                                        <ul class="iList">
                                            <?
                                            for ($i=0; $i<60; $i = $i + 10) {
                                                $v = sprintf("%02d", $i);
                                            ?>
                                            <li><input name="start_m" id="start_m<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['start_m'] == $v) ? 'checked' : ''?> /><label for="start_m<?=$i?>"><?=$v?></label></li>
                                            <? } ?>
                                        </ul>
                                    </div>
                            <br />
                            종료일 : <input id="end_day" name="end_day" type="text" value="<?=str_replace('-', '.', substr($post['end_date'], 0, 10))?>" style="width:100px"  class="datepicker" />
                            시간 : 
                                    <div class="select open" style="width:80px;">
                                        <div class="myValue"></div>
                                        <ul class="iList">
                                            <?
                                            for ($i=0; $i<24; $i++) {
                                                $v = sprintf("%02d", $i);
                                            ?>
                                            <li><input name="end_h" id="end_h<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['end_h'] == $v) ? 'checked' : ''?> /><label for="end_h<?=$i?>"><?=$v?></label></li>
                                            <? } ?>
                                        </ul>
                                    </div>
                                    <div class="select open" style="width:80px;">
                                        <div class="myValue"></div>
                                        <ul class="iList">
                                            <?
                                            for ($i=0; $i<60; $i = $i + 10) {
                                                $v = sprintf("%02d", $i);
                                            ?>
                                            <li><input name="end_m" id="end_m<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['end_m'] == $v) ? 'checked' : ''?> /><label for="end_m<?=$i?>"><?=$v?></label></li>
                                            <? } ?>
                                        </ul>
                                    </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">연결링크</td>
                        <td>   
                            <input id="url" name="url" type="text" title="연결링크" value="<?=$post['url']?>" />
                        </td>
                    </tr>

                    <tr>
                        <td class="thead">
						이미지 첨부 <br>※가로:990/세로:136
						</td>
                        <td>
                            <div>
                                <div class="file_input_div">		
                                    <input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
                                    <input type="hidden" id="banner_image" name="banner_image" value="<?=$post['banner_image']?>" />
                                    <input id="images" name="images" type="file" title="이미지" class="file_input_hidden" style="width:190px;" data-url="/admin/banner/upload_images" />
                                </div>
                            </div>

                            <div style="clear:both"><img id="img_prev" src="<?=($post['banner_image']) ? '/data/banner/'.$post['banner_image'] : ''?>"  style="max-width:100%" /></div>
                        </td>
                    </tr>
                    </tbody>
                </table>

            
                <p class="btn_area">
                    <button type="submit" title="등록" class="submit"><span class="ir">등록</span></button>
                    <a href="/admin/banner" title="취소" class="cancel"><span class="ir">취소</span></a>
                </p>
            </div>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>