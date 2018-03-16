<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>

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

		$('#excel').on('change',function(){
			jQuery('#excel_text').val($('#excel').val());
		});

        var total = '<?=$total?>';
        var page = 1;

        var getList = function(page) {
            $.ajax({
                type: 'get',
                data: {page: page},
                url: '/mypage/strategies/<?=$strategy['strategy_id']?>/analysis/list',
                dataType: 'html',
            }).done(function(html) {
                $('.list_body').append(html);

                cnt = $('.list_body tr').length;
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
    });

    function editData(basedate, balance, flow, PL){
    	$('#edit_basedate').val(basedate);
    		//- $('#edit_balance').val(balance);
    	$('#edit_flow').val(flow);
    	$('#edit_PL').val(PL);
		jQuery('.day_data_input').show();
    		//- showLayer('daily_modify1');
    }
	
	function deleteData(basedate){
		var result = confirm('삭제하시겠습니까?');
		if(result) location.href='/mypage/strategies/<?php echo $strategy['strategy_id'] ?>/analysis/delete?basedate=' +basedate;
	}

	function deleteAllData(){
		var result = confirm('삭제하시겠습니까?');
		if(result) location.href='/mypage/strategies/<?php echo $strategy['strategy_id'] ?>/analysis/deleteall';
	}

	$(function(){
		$('#upload_form').submit(function(){
			$('#upload_form button[type=submit]').attr('disabled', 'disabled');
		});
	});

	<?php if(!empty($flash['error'])){ ?>
	alert('<?php echo htmlspecialchars($flash['error']) ?>');
	<?php } ?>
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
						<div class="pd_regist type02">
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
									<li class="curr"><a href="javascript:;">일간분석</a></li>
									<li><a href="/mypage/strategies/<?=$strategy['strategy_id']?>/fund">펀딩금액/투자자수</a></li>
									<li><a href="/mypage/strategies/<?=$strategy['strategy_id']?>/account">실계좌 정보</a></li>
								</ul>
								<a href="/시스메틱전략등록방법.pdf" class="btn_upload_guide" target="_blank"><img src="/images/sub/ico_qm.gif" alt="?" /> 업로드 가이드</a>
							</div>
							<div class="btn_a">
								<a href="javascript:;" class="btn" onclick="commonLayerOpen('excel_upload')">엑셀업로드</a>
								<a href="javascript:;" class="btn" onclick="commonLayerOpen('week_data_input')">데이터입력</a>
								<a href="javascript:;" class="btn all_delete" onclick="commonLayerOpen('all_data_delete')">전체삭제</a>
							</div>
							
							<div class="list">
								<table class="list_tbl">
									<colgroup>
										<col style="width:111px;" />
										<col style="width:114px;" />
										<col style="width:115px;" />
										<col style="width:115px;" />
										<col style="width:115px;" />
										<col style="width:115px;" />
										<col style="width:125px;" />
										<col style="width:139px;" />
										<col style="width:147px;" />
									</colgroup>
									<thead>
										<tr>
											<th>날짜</th>
											<th>원금</th>
											<th>입출금</th>
											<th>일 손익</th>
											<th>일 손익률</th>
											<th>누적손익</th>
											<th>누적수익률</th>
											<th>관리</th>
										</tr>
									</thead>
									<tbody class="list_body">
									</tbody>
								</table>
							</div>

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

<!-- 레이어팝업 : 일간분석 데이터 입력 -->
<article class="layer_popup input_data day_data_input">
	<div class="dim" onclick="commonLayerClose('day_data_input')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>일간분석 데이터 입력</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('day_data_input')"></button>
		</div>
		<div class="cont">
			<form action="/mypage/strategies/<?=$strategy['strategy_id']?>/analysis/edit" method="post">
				<div class="form_box">
					<table class="form_tbl">
						<colgroup>
							<col style="width:33.33%;" />
							<col style="width:33.33%;" />
							<col style="width:33.33%;" />
						</colgroup>
						<thead>
							<tr>
								<th>날짜</th>
								<th>입출금</th>
								<th>일손익</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><input id="edit_basedate" name="basedate" type="text" title="날짜" style="border: 0px;" value="" required="required" readonly=""></td>
								<td><input id="edit_flow" name="flow" type="text" title="입출금" value="" required="required" class="__input_number__ __input_number_only__" onkeyup="inputNumberFormat(this)"></td>
								<td><input id="edit_PL" name="PL" type="text" title="일손익" value="" required="required" class="__input_number__ __input_number_only__" onkeyup="inputNumberFormat(this)"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_area half">
					<button type="submit" class="btn_common_red">수정</button>
					<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('day_data_input')">취소</a>
				</div>
			</form>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 일간분석 데이터 입력 -->

<!-- 레이어팝업 : 일간분석 데이터 수정 -->
<article class="layer_popup input_data day_data_modify">
	<div class="dim" onclick="commonLayerClose('day_data_input')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>일간분석 데이터 수정</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('day_data_modify')"></button>
		</div>
		<div class="cont">
			<div class="form_box">
				<table class="form_tbl">
					<colgroup>
						<col style="width:33.33%;" />
						<col style="width:33.33%;" />
						<col style="width:33.33%;" />
					</colgroup>
					<thead>
						<tr>
							<th>날짜</th>
							<th>입출금</th>
							<th>일손익</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<input type="text" id="" name="" value="2015-10-21" />
							</td>
							<td>
								<input type="text" id="" name="" value="100,000,000" />
							</td>
							<td>
								<input type="text" id="" name="" value="332,201" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="btn_area half">
				<button type="button" class="btn_common_red">수정</button>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('day_data_modify')">취소</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 일간분석 데이터 수정 -->

<!-- 레이어팝업 : 주간분석 데이터 입력 -->
<article class="layer_popup input_data week_data_input">
	<div class="dim" onclick="commonLayerClose('week_data_input')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>주간분석 데이터 입력</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('week_data_input')"></button>
		</div>
		<div class="cont">
			<form action="/mypage/strategies/<?=$strategy['strategy_id']?>/analysis/add" method="post">
				<div class="form_box">
					<table class="form_tbl">
						<colgroup>
							<col style="width:33.33%;" />
							<col style="width:33.33%;" />
							<col style="width:33.33%;" />
						</colgroup>
						<thead>
							<tr>
								<th>날짜</th>
								<th>입출금</th>
								<th>일손익</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<input type="text" id="" name="basedate[]" class="datepicker" placeholder="날짜입력" />
								</td>
								<td>
									<input type="text" id="" name="flow[]" class="__input_number__ __input_number_only__" placeholder="입출금입력" onkeyup="inputNumberFormat(this)" />
								</td>
								<td>
									<input type="text" id="" name="PL[]" class="__input_number__ __input_number_only__" placeholder="일손익입력" onkeyup="inputNumberFormat(this)" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="" name="basedate[]" class="datepicker" placeholder="날짜입력" />
								</td>
								<td>
									<input type="text" id="" name="flow[]" class="__input_number__ __input_number_only__" placeholder="입출금입력" onkeyup="inputNumberFormat(this)" />
								</td>
								<td>
									<input type="text" id="" name="PL[]" class="__input_number__ __input_number_only__" placeholder="일손익입력" onkeyup="inputNumberFormat(this)" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="" name="basedate[]" class="datepicker" placeholder="날짜입력" />
								</td>
								<td>
									<input type="text" id="" name="flow[]" class="__input_number__ __input_number_only__" placeholder="입출금입력" onkeyup="inputNumberFormat(this)" />
								</td>
								<td>
									<input type="text" id="" name="PL[]" class="__input_number__ __input_number_only__" placeholder="일손익입력" onkeyup="inputNumberFormat(this)" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="" name="basedate[]" class="datepicker" placeholder="날짜입력" />
								</td>
								<td>
									<input type="text" id="" name="flow[]" class="__input_number__ __input_number_only__" placeholder="입출금입력" onkeyup="inputNumberFormat(this)" />
								</td>
								<td>
									<input type="text" id="" name="PL[]" class="__input_number__ __input_number_only__" placeholder="일손익입력" onkeyup="inputNumberFormat(this)" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="" name="basedate[]" class="datepicker" placeholder="날짜입력" />
								</td>
								<td>
									<input type="text" id="" name="flow[]" class="__input_number__ __input_number_only__" placeholder="입출금입력" onkeyup="inputNumberFormat(this)" />
								</td>
								<td>
									<input type="text" id="" name="PL[]" class="__input_number__ __input_number_only__" placeholder="일손익입력" onkeyup="inputNumberFormat(this)" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="" name="basedate[]" class="datepicker" placeholder="날짜입력" />
								</td>
								<td>
									<input type="text" id="" name="flow[]" class="__input_number__ __input_number_only__" placeholder="입출금입력" onkeyup="inputNumberFormat(this)" />
								</td>
								<td>
									<input type="text" id="" name="PL[]" class="__input_number__ __input_number_only__" placeholder="일손익입력" onkeyup="inputNumberFormat(this)" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="" name="basedate[]" class="datepicker" placeholder="날짜입력" />
								</td>
								<td>
									<input type="text" id="" name="flow[]" class="__input_number__ __input_number_only__" placeholder="입출금입력" onkeyup="inputNumberFormat(this)" />
								</td>
								<td>
									<input type="text" id="" name="PL[]" class="__input_number__ __input_number_only__" placeholder="일손익입력" onkeyup="inputNumberFormat(this)" />
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_area half">
					<button type="submit" class="btn_common_red submit">입력</button>
					<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('week_data_input')">취소</a>
				</div>
			</form>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 일간분석 데이터 입력 -->

<!-- 레이어팝업 : 주간분석 데이터 수정 -->
<article class="layer_popup input_data week_data_modify">
	<div class="dim" onclick="commonLayerClose('week_data_modify')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>주간분석 데이터 수정</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('week_data_modify')"></button>
		</div>
		<div class="cont">
			<div class="form_box">
				<table class="form_tbl">
					<colgroup>
						<col style="width:33.33%;" />
						<col style="width:33.33%;" />
						<col style="width:33.33%;" />
					</colgroup>
					<thead>
						<tr>
							<th>날짜</th>
							<th>입출금</th>
							<th>일손익</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<input type="text" id="" name="" value="2015-10-21" />
							</td>
							<td>
								<input type="text" id="" name="" value="100,000,000" />
							</td>
							<td>
								<input type="text" id="" name="" value="332,201" />
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" id="" name="" value="2015-10-21" />
							</td>
							<td>
								<input type="text" id="" name="" value="100,000,000" />
							</td>
							<td>
								<input type="text" id="" name="" value="332,201" />
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" id="" name="" value="2015-10-21" />
							</td>
							<td>
								<input type="text" id="" name="" value="100,000,000" />
							</td>
							<td>
								<input type="text" id="" name="" value="332,201" />
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" id="" name="" value="2015-10-21" />
							</td>
							<td>
								<input type="text" id="" name="" value="100,000,000" />
							</td>
							<td>
								<input type="text" id="" name="" value="332,201" />
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" id="" name="" value="2015-10-21" />
							</td>
							<td>
								<input type="text" id="" name="" value="100,000,000" />
							</td>
							<td>
								<input type="text" id="" name="" value="332,201" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="btn_area half">
				<button type="button" class="btn_common_red">수정</button>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('week_data_modify')">취소</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 주간분석 데이터 수정 -->

<!-- 레이어팝업 : 안내 > 전체 데이터 삭제 -->
<article class="layer_popup common_confirm all_data_delete">
	<div class="dim" onclick="commonLayerClose('all_data_delete')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('all_data_delete')"></button>
		</div>
		<div class="cont">
			<div class="summary">
				<p class="q_msg">
					모든 일간분석 데이터를<br />
					<span class="mark">삭제하시겠습니까?</span>
				</p>
			</div>
			<div class="btn_area half">
				<a href="javascript:;" class="btn_common_red" onclick="deleteAllData();">예</a>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('all_data_delete')">아니오</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 안내 > 전체 데이터 삭제 -->

<!-- 레이어팝업 : 안내 > 해당일자 데이터 삭제 -->
<article class="layer_popup common_confirm day_data_delete">
	<div class="dim" onclick="commonLayerClose('day_data_delete')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('day_data_delete')"></button>
		</div>
		<div class="cont">
			<div class="summary">
				<p class="q_msg">
					해당 날짜의 데이터를<br />
					<span class="mark">삭제하시겠습니까?</span>
				</p>
			</div>
			<div class="btn_area half">
				<a href="javascript:;" class="btn_common_red" onclick="commonLayerClose('day_data_delete')">예</a>
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('day_data_delete')">아니오</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 안내 > 해당일자 데이터 삭제 -->

<!-- 레이어팝업 : 엑셀 업로드 -->
<article class="layer_popup excel_upload">
	<div class="dim" onclick="commonLayerClose('excel_upload')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>엑셀 업로드</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('excel_upload')"></button>
		</div>
		<div class="cont">

			<form action="/mypage/strategies/<?=$strategy['strategy_id']?>/analysis/upload" method="post" enctype="multipart/form-data" id="upload_form">
				<div class="file_upload">
					<div class="input_box">
						<input type="text" id="excel_text" name="" placeholder="파일을 첨부해주세요." />
						<input type="file" id="excel" name="excel" />					
					</div>
					<label for="excel" class="btn_upload">찾아보기</label>
				</div>
				<p class="info">
					- 엑셀 업로드에 문제가 있는 경우, <strong class="email">help@sysmetic.co.kr</strong> 로<br />
					해당파일을 첨부하여 문의해 주세요.
				</p>
				<div class="btn_area half">
					<button type="submit" class="btn_common_red">업로드</button>
					<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('excel_upload')">취소</a>
				</div>
			</form>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 엑셀 업로드 -->

<!-- 레이어팝업 : 안내 > 업로드 오류 commonLayerOpen('upload_error') -->
<article class="layer_popup common_info upload_error">
	<div class="dim" onclick="commonLayerClose('upload_error')"></div>
	<div class="contents">
		<div class="layer_header">
			<h2>안내</h2>
			<button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('upload_error')"></button>
		</div>
		<div class="cont">
			<p class="txt_caution">엑셀파일만 업로드 가능합니다.</p> 
			<div class="btn_area">
				<a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('upload_error')">닫기</a>
			</div>
		</div>
	</div>
</article>
<!-- //레이어팝업 : 안내 > 폴더명 중복 -->
<script>
    //file add
    $('.regist input[type="file"]').change(function(){
        var filePath = $(this).val();
        $(this).siblings('.input_box').children('input').val(filePath);
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
