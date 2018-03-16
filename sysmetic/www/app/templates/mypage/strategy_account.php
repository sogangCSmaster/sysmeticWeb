<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>
	<script src="/script/calendar.js"></script>

	<script>
    $(function () {
        $('#setChg').on('click', function() {
            if (!confirm('설정을 변경하시겠습니까?')) {
                return false;
            }
            
            var is_open = $('#is_open').val();
            var is_fund = $('#is_fund').val();

            $.ajax({
                type: 'post',
                data: {is_open: is_open, is_fund: is_fund},
                url: '/mypage/strategies/<?=$strategy['strategy_id']?>/set',
                dataType: 'json',
            }).done(function(data) {
                if (data.result) {
                    alert('상태가 변경되었습니다');
                    location.reload();
                } else {
                    alert(data.msg);
                    location.reload();
                }
            });
        });

        var total = '<?=$total?>';
        var page = 1;

        var getList = function(page) {
            $.ajax({
                type: 'get',
                data: {page: page},
                url: '/mypage/strategies/<?=$strategy['strategy_id']?>/account/list',
                dataType: 'html',
            }).done(function(html) {
                $('.acc_no_list').append(html);

                cnt = $('.acc_no_list li').length;
                if (total > cnt) {
                    $('.btn_list_more').show();
                }
            });
        }

        $('.btn_list_more').on('click', function() {
            $(this).hide();
            page = page + 1;
            getList(page);
        });


        $('.btn_list_more').hide();
        getList(page);


		$('#accounts_form').submit(function(){
			if($('#accounts_form input[type=checkbox]:checked').length == 0){
				alert('선택된 항목이 없습니다');
				return false;
			}

			var result = confirm('삭제하시겠습니까?');
			if(!result) return false;

			return true;
		});
    
        <?php if(!empty($flash['error'])){ ?>
        //alert('<?php echo htmlspecialchars($flash['error']) ?>');
        $('#errMsg').text('<?php echo htmlspecialchars($flash['error']) ?>');
        commonLayerOpen('upload_error');
        <?php } ?>
	});

	function openImage(url){
		$('#show_img').attr('src', url);
        commonLayerOpen('img_detail_view');
	}
	</script>
</head>
<body>
    <!-- wrapper -->
    <div class="wrapper">
        <!-- header -->
        <? require_once $skinDir."common/header.php" ?>
        <!-- header -->
        <!-- container -->
        <div class="container">

			<section class="area mypage">	
				<div class="cont_a">

                    <? require_once $skinDir."mypage/sub_menu.php" ?>
                    

					<div class="content my_products">
						<div class="pd_regist type04">
							<div class="info">
								<strong class="name"><?=$strategy['name']?></strong>
								<div class="regist_date">
									<dl>
										<dt>등록 : </dt>
										<dd><?=$strategy['reg_at']?></dd>
									</dl>
									<dl class="bottom">
										<dt>최종 업데이트 : </dt>
										<dd><?=$strategy['mod_at']?></dd>
									</dl>
								</div>
							</div>
							<div class="stat_set">
								<strong class="title">상태 설정 :</strong>
								<span class="txt">공개여부</span>
								<div class="custom_selectbox" style="width:150px;">
									<label for="is_open">공개</label>
									<select id="is_open" name="select01">
										<option value="1" <?=($strategy['is_open'] == 1) ? 'selected' : ''?>>공개</option>
										<option value="0" <?=($strategy['is_open'] == 0) ? 'selected' : ''?>>비공개</option>
									</select>
								</div>
								<span class="txt">펀딩여부</span>
								<div class="custom_selectbox" style="width:150px;">
									<label for="is_fund">펀딩가능</label>
									<select id="is_fund" name="is_fund">
										<option value="1" <?=($strategy['is_fund'] == 1) ? 'selected' : ''?>>펀딩가능</option>
										<option value="0" <?=($strategy['is_fund'] == 0) ? 'selected' : ''?>>펀딩불가</option>
									</select>
								</div>
								<a href="javascript:;" id="setChg" class="btn_set">변경</a>
							</div>
							<div class="category">
								<ul>
									<li><a href="/mypage/strategies/<?=$strategy['strategy_id']?>/basic">기본정보</a></li>
									<li><a href="/mypage/strategies/<?=$strategy['strategy_id']?>/analysis">일간분석</a></li>
									<li><a href="/mypage/strategies/<?=$strategy['strategy_id']?>/fund">펀딩금액/투자자수</a></li>
									<li class="curr"><a href="javascript:;">실계좌 정보</a></li>
								</ul>
								<a href="/시스메틱전략등록방법.pdf" class="btn_upload_guide" target="_blank"><img src="/images/sub/ico_qm.gif" alt="?" /> 업로드 가이드</a>
							</div>

				            <form action="/mypage/strategies/<?php echo $strategy['strategy_id'] ?>/account/delete" method="post" id="accounts_form">
							<div class="list_head">
								<div class="delete_a">
									<p class="txt">선택한 이미지를</p>
									<input type="submit" class="btn_delete" value="삭제" />
								</div>
								<a href="javascript:;" class="btn" onclick="commonLayerOpen('acc_no_certification')">실계좌 인증등록</a>
							</div>
							
							<div class="list">
								<ul class="acc_no_list">
								</ul>
							</div>
                            </form>

							<a href="javascript:;" class="btn_list_more">+ 더보기</a>
							<a href="/mypage/strategies" class="btn_full_gray">목록</a>
						</div>
					</div>
                </div>
            </section>
        </div>
        <!-- //container -->

        <!-- footer -->
        <? require_once $skinDir."common/footer.php" ?>
        <!-- //footer -->
    </div>
    <!-- //wrapper -->

<style>
.layer_popup.img_detail_view .detail_pic {padding:10px 0; text-align:center;}
.layer_popup.img_detail_view .detail_pic img {max-width:calc(100% - 2px); border:1px solid #bfbfbf;}

</style>

<!-- 레이어팝업 : 실계좌 인증 등록 입력 -->
<article class="layer_popup img_detail_view">
	<div class="dim" onclick="commonLayerClose('img_detail_view')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>이미지 상세보기</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('img_detail_view')"></button>
		</div>
		<div class="cont">
			<div class="detail_pic">
				<img id="show_img" src="" alt="" />
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 실계좌 인증 등록 입력 -->

<!-- 레이어팝업 : 실계좌 인증 등록 입력 -->
<article class="layer_popup input_data acc_no_certification">
	<div class="dim" onclick="commonLayerClose('acc_no_certification')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>실계좌 인증 등록</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('acc_no_certification')"></button>
		</div>
		<form action="/mypage/strategies/<?php echo $strategy['strategy_id'] ?>/account/add" method="post" enctype="multipart/form-data">
		<div class="cont">
			<div class="form_box">
				<table class="form_tbl">
					<colgroup>
						<col style="width:50%;" />
						<col style="width:50%;" />
					</colgroup>
					<thead>
						<tr>
							<th>제목</th>
							<th>이미지</th>
						</tr>
					</thead>
					<tbody>
                        <? for ($i=1; $i<=5; $i++) { ?>
						<tr>
							<td>
								<input type="text" name="title[]" placeholder="제목" style="width:182px;" />
							</td>
							<td>
								<div class="file_upload">
									<div class="input_box">
										<input type="text" id="img<?=$i?>" name="account_img_fake" placeholder="파일을 첨부해주세요." readonly="readonly" />
										<input type="file" id="file_upload<?=$i?>" name="account_img[]" onchange="document.getElementById('img<?=$i?>').value = this.value" />
									</div>
									<label for="file_upload<?=$i?>" class="btn_upload">찾아보기</label>
								</div>
							</td>
						</tr>
                        <? } ?>
					</tbody>
				</table>
			</div>
			<div class="btn_area half">
				<button type="submit" class="btn_common_red">등록</button>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('acc_no_certification')">취소</a>
			</div>
		</div>
        </form>
	</div>
</article>
<!-- //레이어팝업 : 실계좌 인증 등록 입력 -->

<!-- 레이어팝업 : 안내 > 업로드 오류 commonLayerOpen('upload_error') -->
<article class="layer_popup common_info upload_error">
	<div class="dim" onclick="commonLayerClose('upload_error')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('upload_error')"></button>
		</div>
		<div class="cont">
			<p class="txt_caution" id="errMsg"></p>
			<div class="btn_area">
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('upload_error')">닫기</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 안내 > 폴더명 중복 -->

<script>
	//file add
	$('.acc_no_certification .file_upload input[type="file"]').change(function(){
		var filePath = $(this).val();
		$(this).siblings('input').val(filePath);
	});
	//custom selectbox
    var select = $('select');
    for(var i = 0; i < select.length; i++){
        var idxData = select.eq(i).children('option:selected').text();
        select.eq(i).siblings('label').text(idxData);
    }
    select.change(function(){
        var select_name = $(this).children("option:selected").text();
        $(this).siblings("label").text(select_name);
    });
</script>
</body>
</html>
