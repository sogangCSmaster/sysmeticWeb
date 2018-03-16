<!doctype html>
<html lang="ko">
<head>
    <title>PB게시판</title>
    <? include_once $skinDir."/common/head.php" ?>
	<script src="/script/jquery.iframe-transport.js"></script>
	<script src="/script/jquery.fileupload.js"></script>
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
                bUseVerticalResizer : false, // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseModeChanger : true, // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
            }
        });

        //전송버튼 클릭이벤트
        $('#regFrm').on('submit', function() {
            
            editor_object.getById["contents"].exec("UPDATE_CONTENTS_FIELD", []);

            if (!$.trim($('#subject').val())) {
                $('#subject').focus();
                alert('제목을 입력해주세요');
                return false;
            } else if (!$.trim($('#contents').val())) {
                $('#contents').focus();
                alert('내용을 입력해주세요');
                return false;
            } else {
                if (!confirm('저장하시겠습니까?')) {
                    return false;
                }
            }


            return;
        });

        $('.file .btn_default').on('click', function() {
            var maxFile = 5;
            var len = $(this).parent().siblings('ul').children('li').length;
            if (len >= maxFile) {
                alert('파일 업로드는 최대 '+ maxFile +'개 까지 가능합니다');
                return false;
            }
        });

        // 이미지 등록
		$('#images').fileupload({
			forceIframeTransport: true,
			dataType: 'json',
			done: function (e, data) {
                var result = data.result;
                if (result.success) {
                    var html = '<li>';
                    html +='<input type="hidden" name="attach_images_filename[]" value="' + result.filename + '" />';
                    html +='<input type="hidden" name="attach_images_savename[]" value="' + result.savename + '" />';
                    html +='<p class="filename">' + result.filename + '</p>';
                    html += '<span class="info">' + result.filesize + ' b</span>';
                    html += '<a href="javascript:;" class="btn_delete" onclick="fileDelete(this);" data-savename="' + result.savename + '"><img src="/images/sub/btn_review_delete.gif" alt="파일삭제" /></a>';
                    html += '</li>';
                    $('#images_list').append(html);
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

        // 파일등록
		$('#files').fileupload({
			forceIframeTransport: true,
			dataType: 'json',
			done: function (e, data) {
                var result = data.result;
                if (result.success) {
                    var html = '<li>';
                    html +='<input type="hidden" name="attach_files_filename[]" value="' + result.filename + '" />';
                    html +='<input type="hidden" name="attach_files_savename[]" value="' + result.savename + '" />';
                    html +='<p class="filename">' + result.filename + '</p>';
                    html += '<span class="info">' + result.filesize + ' b</span>';
                    html += '<a href="javascript:;" class="btn_delete" onclick="fileDelete(this);" data-savename="' + result.savename + '"><img src="/images/sub/btn_review_delete.gif" alt="파일삭제" /></a>';
                    html += '</li>';
                    $('#files_list').append(html);
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

    function fileDelete(e) {
        var savename = $(e).data('savename');
        if (savename != "") {
            $.post('/pb/bbs/delete_file', {'savename':savename}, function(data) {
                if (data == 'success') {
                    $(e).parent().remove();
                } else {
                    alert('이미지 삭제중 요류가 발생하였습니다');
                }
            });
        }
    }
    </script>
</head>
<body>
    <!-- wrapper -->
    <div class="wrapper">

        <!-- header -->
        <? require_once $skinDir."/common/header.php" ?>
        <!-- header -->

        <!-- container -->
		<div class="container">
			<section class="area pb_only write">
				<div class="content board_detail">	
					<div class="page_title_area no_bg">
						<p class="page_title n_squere">PB게시판 - 글쓰기 </p>
						<p class="page_summary">공지·칼럼을 등록 하실 수 있습니다.</p>
					</div>
					<div class="form_box">

                            <? if (!$info['bid']) { ?>
							<form id="regFrm" action="/pb/bbs/write" method="post" enctype="multipart/form-data">
                            <? } else { ?>
							<form id="regFrm" action="/pb/bbs/<?=$info['bid']?>/modify" method="post" enctype="multipart/form-data">
                            <? } ?>
								<fieldset>
									<legend class="screen_out">공지,칼럼 글쓰기</legend>
									<table class="form_tbl">
										<colgroup>
											<col style="width:164px">
											<col style="width:826px">
										</colgroup>
										<tbody>
											<tr>
												<th>제목</th>
												<td>
													<div class="input_box" style="width:795px;">
														<input type="text" id="subject" name="subject" placeholder="제목을 입력해주세요." value="<?=$info['subject']?>" style="width:775px;">
													</div>
												</td>
											</tr>
											<tr class="editor">
												<th>내용</th>
												<td>
													<div class="editor_a">
                                                    <textarea name="contents" id="contents" rows="10" cols="100" style="width:795px; height:230px"><?=$info['contents']?></textarea>
													</div>
												</td>
											</tr>
											<!-- <tr class="file pd">
												<th>이미지첨부</th>
												<td>
													<div class="wrapping">
														<div class="input_box" style="width:298px;">
															<input type="text" id="images_name" name="images_name" placeholder="이미지를 선택해주세요" style="width:278px;" />
															<input type="file" id="images" name="images" data-url="/pb/bbs/upload_images" />
														</div>
														<label for="images" class="btn_default">찾아보기</label>
													</div>
													<p class="txt_caution">* 이미지 첨부는 5개까지 가능합니다.</p>
													<ul class="files" id="images_list">
                                                    <? foreach ($info['images'] as $k => $v) { ?>
                                                        <li>
                                                            <input type="hidden" name="attach_images_filename[]" value="<?=$v['file_name']?>" />
                                                            <input type="hidden" name="attach_images_savename[]" value="<?=$v['save_name']?>" />
                                                            <p class="filename"><?=$v['file_name']?></p>
                                                            <span class="info"><?=$v['file_size']?></span>
                                                            <a href="javascript:;" class="btn_delete" onclick="fileDelete(this);" data-savename="<?=$v['save_name']?>"><img src="/images/sub/btn_review_delete.gif" alt="파일삭제" /></a>
                                                        </li>
                                                    <? } ?>
													</ul>
												</td>
											</tr> -->
											<tr class="file pd">
												<th>파일첨부</th>
												<td>
													<div class="wrapping">
														<div class="input_box" style="width:298px;">
															<input type="text" id="files_name" name="files_name" placeholder="파일을 선택해주세요" style="width:278px;" />
															<input type="file" id="files" name="files" data-url="/pb/bbs/upload_files" />
														</div>
														<label for="files" class="btn_default">찾아보기</label>
													</div>
													<p class="txt_caution">* 파일 첨부는 5개까지 가능합니다.</p>
													<ul class="files" id="files_list">
                                                    <? foreach ($info['files'] as $k => $v) { ?>
                                                        <li>
                                                            <input type="hidden" name="attach_files_filename[]" value="<?=$v['file_name']?>" />
                                                            <input type="hidden" name="attach_files_savename[]" value="<?=$v['save_name']?>" />
                                                            <p class="filename"><?=$v['file_name']?></p>
                                                            <span class="info"><?=$v['file_size']?></span>
                                                            <a href="javascript:;" class="btn_delete" onclick="fileDelete(this);" data-savename="<?=$v['save_name']?>"><img src="/images/sub/btn_review_delete.gif" alt="파일삭제" /></a>
                                                        </li>
                                                    <? } ?>
													</ul>
												</td>
											</tr>
										</tbody>
									</table>
									<div class="btn_area">
										<a href="/pb/bbs" class="btn_common_gray btn_cancel">취소</a>
										<button type="submit" class="btn_common_red btn_next_step">등록</button>
									</div>
								</fieldset>
							</form>
						</div>
					</div>


                </div>
            </section>
        </div>
        <!-- //container -->

        <!-- footer -->
        <? require_once $skinDir."/common/footer.php" ?>
        <!-- // footer -->

    </div>
    <!-- //wrapper -->

</body>
</html>
