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

		$('#faq_form').submit(function(){

            editor_object.getById["contents"].exec("UPDATE_CONTENTS_FIELD", []);

			if(!$('#subject').val()){
				alert('제목을 입력하세요');
				$('#subject').focus();
				return false;
			}

			if(!$('#contents').val()){
				alert('답변을 입력하세요');
				$('#contents').focus();
				return false;
			}

			return true;
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
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">FAQ 등록</h3>

            <? if ($post['fidx']) { ?>
			<form action="/admin/faq/<?=$post['fidx']?>/edit" method="post" id="faq_form" enctype="multipart/form-data">
            <? } else { ?>
			<form action="/admin/faq/write" method="post" id="faq_form" enctype="multipart/form-data">
            <? } ?>
            <div id="strategy_view1" class="strategy_view">
                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:96%;">
                <col width="160" /><col width="*" />
                <tbody>

                    <tr>
                        <td class="thead">카테고리</td>
                        <td>
                        <div class="select open" style="width:150px;">
                            <div class="myValue"></div>
                            <ul class="iList">
                                <li><input name="cate_id" id="search" class="option" type="radio" value="0" <?=(!$post['cate_id']) ? 'checked' : ''?> /><label for="search1">선택</label></li>
                                <? foreach ($cates as $k => $v) { ?>
                                <li><input name="cate_id" id="search<?=$k?>" class="option" type="radio" value="<?=$v['cate_id']?>" <?=($post['cate_id'] == $v['cate_id']) ? 'checked' : ''?> /><label for="search<?=$k?>"><?=$v['name']?></label></li>
                                <? } ?>
                            </ul>
                        </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="thead">제목</td>
                        <td>   
                            <input id="subject" name="subject" type="text" title="제목" value="<?=$post['subject']?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">답변</td>
                        <td>   
                            <textarea id="contents" name="contents" style="height:400px;"><?=$post['contents']?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <p class="btn_area">
                    <button type="submit" title="등록" class="submit"><span class="ir">등록</span></button>
                    <a href="/admin/faq" title="취소" class="cancel"><span class="ir">취소</span></a>
                </p>
            </div>
			</form>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>