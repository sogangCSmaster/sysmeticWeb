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
	<script>
	$(function(){
		$('#education_form').submit(function(){
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

        
        $('#search1, #search2').on('click', function() {
            if ($(this).val() == 'ON') {
                $('.ea_date').hide();
                $('.att_file').hide();
                $('#img_file_add').hide();
            } else {
                $('.ea_date').show();
                $('.att_file').show();
                $('#img_file_add').show();
            }
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

	<?php if(!empty($flash['error'])){ ?>
	alert('<?php echo htmlspecialchars($flash['error']) ?>');
	<?php } ?>
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">교육 등록</h3>

            <? if ($post['eidx']) { ?>
			<form action="/admin/education/<?=$post['eidx']?>/edit" method="post" id="education_form" enctype="multipart/form-data">
            <? } else { ?>
			<form action="/admin/education/write" method="post" id="education_form" enctype="multipart/form-data">
            <? } ?>
            <div id="strategy_view1" class="strategy_view">
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:96%;">
                <col width="160" /><col width="*" />
                <tbody>
                    <tr >
                        <td class="thead">카테고리</td>
                        <td>
                        <div class="select open" style="width:110px;">
                            <div class="myValue"></div>
                            <ul class="iList">
                                <li><input name="type" id="search1" class="option" type="radio" value="ON" <?=(!$post['type'] || $post['type'] == 'ON') ? 'checked' : ''?> /><label for="search1">온라인</label></li>
                                <li><input name="type" id="search2" class="option" type="radio" value="OFF" <?=($post['type'] == 'OFF') ? 'checked' : ''?> /><label for="search2">오프라인</label></li>
                            </ul>
                        </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="thead">교육명</td>
                        <td>   
                            <input id="subject" name="subject" type="text" title="공지사항 제목" value="<?=$post['subject']?>" required="required" />
                        </td>
                    </tr>
                    <tr class='ea_date' style="display:<?=($post['type'] == 'OFF') ? '' : 'none'?>">
                        <td class="thead">교육시간</td>
                        <td>   
                            시작일 : <input id="e_start_day" name="e_start_day" type="text" value="<?=str_replace('-', '.', substr($post['e_start_date'], 0, 10))?>" style="width:100px"  class="datepicker" />
                            시간 : 
                                    <div class="select open" style="width:80px;">
                                        <div class="myValue"></div>
                                        <ul class="iList">
                                            <?
                                            for ($i=0; $i<24; $i++) {
                                                $v = sprintf("%02d", $i);
                                            ?>
                                            <li><input name="e_start_h" id="e_start_h<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['e_start_h'] == $v) ? 'checked' : ''?> /><label for="e_start_h<?=$i?>"><?=$v?></label></li>
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
                                            <li><input name="e_start_m" id="e_start_m<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['e_start_m'] == $v) ? 'checked' : ''?> /><label for="e_start_m<?=$i?>"><?=$v?></label></li>
                                            <? } ?>
                                        </ul>
                                    </div>
                            <br />
                            종료일 : <input id="e_end_day" name="e_end_day" type="text" value="<?=str_replace('-', '.', substr($post['e_end_date'], 0, 10))?>" style="width:100px"  class="datepicker" />
                            시간 : 
                                    <div class="select open" style="width:80px;">
                                        <div class="myValue"></div>
                                        <ul class="iList">
                                            <?
                                            for ($i=0; $i<24; $i++) {
                                                $v = sprintf("%02d", $i);
                                            ?>
                                            <li><input name="e_end_h" id="e_end_h<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['e_end_h'] == $v) ? 'checked' : ''?> /><label for="e_end_h<?=$i?>"><?=$v?></label></li>
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
                                            <li><input name="e_end_m" id="e_end_m<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['e_end_m'] == $v) ? 'checked' : ''?> /><label for="e_end_m<?=$i?>"><?=$v?></label></li>
                                            <? } ?>
                                        </ul>
                                    </div>
                        </td>
                    </tr>
                    <tr class='ea_date' style="display:<?=($post['type'] == 'OFF') ? '' : 'none'?>">
                        <td class="thead">신청기간</td>
                        <td>   
                            시작일 : <input id="a_start_day" name="a_start_day" type="text" value="<?=str_replace('-', '.', substr($post['a_start_date'], 0, 10))?>" style="width:100px"  class="datepicker"/>
                            시간 : 
                                    <div class="select open" style="width:80px;">
                                        <div class="myValue"></div>
                                        <ul class="iList">
                                            <?
                                            for ($i=0; $i<24; $i++) {
                                                $v = sprintf("%02d", $i);
                                            ?>
                                            <li><input name="a_start_h" id="a_start_h<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['a_start_h'] == $v) ? 'checked' : ''?> /><label for="a_start_h<?=$i?>"><?=$v?></label></li>
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
                                            <li><input name="a_start_m" id="a_start_m<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['a_start_m'] == $v) ? 'checked' : ''?> /><label for="a_start_m<?=$i?>"><?=$v?></label></li>
                                            <? } ?>
                                        </ul>
                                    </div>
                            <br />
                            종료일 : <input id="a_end_day" name="a_end_day" type="text" value="<?=str_replace('-', '.', substr($post['a_end_date'], 0, 10))?>" style="width:100px"  class="datepicker"/>
                            시간 : 
                                    <div class="select open" style="width:80px;">
                                        <div class="myValue"></div>
                                        <ul class="iList">
                                            <?
                                            for ($i=0; $i<24; $i++) {
                                                $v = sprintf("%02d", $i);
                                            ?>
                                            <li><input name="a_end_h" id="a_end_h<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['a_end_h'] == $v) ? 'checked' : ''?> /><label for="a_end_h<?=$i?>"><?=$v?></label></li>
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
                                            <li><input name="a_end_m" id="a_end_m<?=$i?>" class="option" type="radio" value="<?=$v?>" <?=($post['a_end_m'] == $v) ? 'checked' : ''?> /><label for="a_end_m<?=$i?>"><?=$v?></label></li>
                                            <? } ?>
                                        </ul>
                                    </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">내용</td>
                        <td>   
                            <textarea id="contents_body" name="contents_body" style="height:400px;" required="required"><?=$post['contents']?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">이미지 첨부</td>
                        <td>
                            <?
                            if (isset($imgs) && is_array($imgs)) {
                                foreach ($imgs as $v) {
                            ?>
                                <img src="/education/<?=$v['save_name']?>"> <?=$v['file_name']?> (<label><input type="checkbox" name="del_files[]" value="<?=$v['fid']?>" />삭제</label>)<br />
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
							    <input type="button" title="+" value="+" class="file_input_button act" id="img_file_add" style="display:<?=($post['type'] == 'OFF') ? '' : 'none'?>"></button>
                                <? } ?>
                            </div>
                            <? } ?>
                        </td>
                    </tr>
                    <tr class='att_file' style="display:<?=($post['type'] == 'OFF') ? '' : 'none'?>">
                        <td class="thead">파일 첨부</td>
                        <td>
                            <?
                            if (isset($atts) && is_array($atts)) {
                                foreach ($atts as $v) {
                            ?>
                                <a href="/education/<?=$v['save_name']?>" target="_blank"><?=$v['file_name']?></a> (<label><input type="checkbox" name="del_files[]" value="<?=$v['fid']?>" />삭제</label>)<br />
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
                    <a href="/admin/education" title="취소" class="cancel"><span class="ir">취소</span></a>
                </p>
            </div>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>