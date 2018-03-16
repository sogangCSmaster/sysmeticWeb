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
    <script type="text/javascript" src="/smarteditor2/js/HuskyEZCreator.js" charset="utf-8"></script>
	<script>
	$(function(){
        var editor_object = [];
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: editor_object,
            elPlaceHolder: "contents",
            sSkinURI: "/smarteditor2/SmartEditor2Skin.html",
            htParams : {
                bUseToolbar : true,
                bUseVerticalResizer : true, // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseModeChanger : true, // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
            }
        });

		$('#notice_form').submit(function(){

            
            editor_object.getById["contents"].exec("UPDATE_CONTENTS_FIELD", []);


			if(!$('#subject').val()){
				alert('제목을 입력하세요');
				$('#subject').focus();
				return false;
			}

			if(!$('#contents').val()){
				alert('내용을 입력하세요');
				$('#contents').focus();
				return false;
			}

			return true;
		});
        

        var img_file_no = 1;
        $('#img_file_add').on('click', function(){
            $('#img_file' + ++img_file_no + '_div').show();
        });

        var att_file_add = 1;
        $('#att_file_add').on('click', function(){
            $('#att_file' + ++att_file_add + '_div').show();
        });
	});
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">공지사항 관리</h3>



            <? if ($post['notice_id']) { ?>
			<form action="/admin/notice/<?=$post['notice_id']?>/edit" method="post" id="notice_form" enctype="multipart/form-data">
            <? } else { ?>
			<form action="/admin/notice/write" method="post" id="notice_form" enctype="multipart/form-data">
            <? } ?>
            <div id="strategy_view1" class="strategy_view">
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:96%;">
                <col width="160" /><col width="*" />
                <tbody>
                    <tr>
                        <td class="thead">제목</td>
                        <td>   
                            <input id="subject" name="subject" type="text" title="공지사항 제목" value="<?=$post['subject']?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">공개일시</td>
                        <td>
                            일자 : <input id="open_day" name="open_day" type="text" value="<?=str_replace('-', '.', substr($post['open_date'], 0, 10))?>" style="width:100px"  class="datepicker"/>
                            시간 : 
                                    <div class="select open" style="width:80px;">
                                        <div class="myValue"></div>
                                        <ul class="iList">
                                            <?
                                            for ($i=0; $i<24; $i++) {
                                                $v = sprintf("%02d", $i);
                                            ?>
                                            <li><input name="open_h" id="open_h<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['open_h'] == $v) ? 'checked' : ''?> /><label for="open_h<?=$i?>"><?=$v?></label></li>
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
                                            <li><input name="open_m" id="open_m<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['open_m'] == $v) ? 'checked' : ''?> /><label for="open_m<?=$i?>"><?=$v?></label></li>
                                            <? } ?>
                                        </ul>
                                    </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">내용</td>
                        <td>   
                            <textarea id="contents" name="contents" style="height:400px;"><?=$post['contents']?></textarea>
                        </td>
                    </tr>

                    <!-- <tr>
                        <td class="thead">이미지 첨부</td>
                        <td>
                            <?
                            if (isset($imgs) && is_array($imgs)) {
                                foreach ($imgs as $v) {
                            ?>
                                <img src="/notice/<?=$v['save_name']?>"> <?=$v['file_name']?> (<label><input type="checkbox" name="del_files[]" value="<?=$v['fid']?>" />삭제</label>)<br />
                            <?
                                }
                            }
                            ?>

                            <?
                            for ($i=1; $i<=5; $i++) {
                                $id = "img_file".$i;
                            ?>
                            <div class="add_img" id="<?=$id?>_div" style="display:<?=($i==1) ? '' : 'none'?>">
                                <input id="<?=$id?>" name="<?=$id?>" type="text"  title="이미지" class="file_input_textbox" style="width:500px;" readonly="readonly">
                                <div class="file_input_div">		
                                    <input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
                                    <input name="img[]" type="file" title="이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('<?=$id?>').value = this.value" />
                                </div>
                                <? if ($i == 1) { ?>
							    <input type="button" title="+" value="+" class="file_input_button act" id="img_file_add"></button>
                                <? } ?>
                            </div>
                            <? } ?>
                        </td>
                    </tr> -->
                    <tr class='att_file'>
                        <td class="thead">파일 첨부</td>
                        <td>
                            <?
                            if (isset($atts) && is_array($atts)) {
                                foreach ($atts as $v) {
                            ?>
                                <a href="/notice/<?=$v['save_name']?>" target="_blank"><?=$v['file_name']?></a> (<label><input type="checkbox" name="del_files[]" value="<?=$v['fid']?>" />삭제</label>)<br />
                            <?
                                }
                            }
                            ?>

                            <?
                            for ($i=1; $i<=3; $i++) {
                                $id = "att_file".$i;
                            ?>
                            <div id="<?=$id?>_div" style="display:<?=($i==1) ? '' : 'none'?>">
                                <input id="<?=$id?>" name="<?=$id?>" type="text"  title="이미지" class="file_input_textbox" style="width:500px;" readonly="readonly">
                                <div class="file_input_div">		
                                    <input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
                                    <input name="att[]" type="file" title="이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('<?=$id?>').value = this.value" />
                                </div>
                                <? if ($i == 1) { ?>
                                <input type="button" title="+" value="+" class="file_input_button act" id="att_file_add"></button>
                                <? } ?>
                            </div>
                            <? } ?>
                        </td>
                    </tr>
                    </tbody>
                </table>

            
                <p class="btn_area">
                    <button type="submit" title="등록" class="submit"><span class="ir">등록</span></button>
                    <a href="/admin/notice" title="취소" class="cancel"><span class="ir">취소</span></a>
                </p>
            </div>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>