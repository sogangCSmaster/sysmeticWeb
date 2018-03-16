<!doctype html>
<html lang="ko">
<head>
	<title>마이페이지 | SYSMETIC</title>
	<? require_once $skinDir."common/head.php" ?>

    <script>
    $(function(){
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

        $('#reg_strategy_form').submit(function(){
            if(!$('#name').val()){
                alert('전략명을 입력해주세요');
                $('#name').focus();
                return false;
            }

            if(!$('#broker_id').val()){
                alert('중개사를 선택해주세요');
                $('#broker_id').focus();
                return false;
            }

            if(!$('#tool_id').val()){
                alert('매매툴을 선택해주세요');
                $('#tool_id').focus();
                return false;
            }
/*
            if(!$('#pb_uid').val()){
                alert('PB를 선택해주세요');
                $('#pb_uid').focus();
                return false;
            }
*/
            if(!$('#strategy_type').val()){
                alert('종류를 선택해주세요');
                $('#strategy_type').focus();
                return false;
            }

            if(!$('#term').val()){
                alert('주기를 선택해주세요');
                $('#term').focus();
                return false;
            }

            if($('input[data-role=item]:checked').length == 0){
                alert('종목을 선택해주세요');
                return false;
            }

            if(!$('#strategy_kind').val()){
                alert('상품종류를 선택해주세요');
                $('#strategy_kind').focus();
                return false;
            }

            if(!$('#strategy_currency').val()){
                alert('통화를 선택해주세요');
                $('#strategy_currency').focus();
                return false;
            }

            if(!$('#min_price').val()){
                alert('최소위탁가능금액을 선택해주세요');
                $('#min_price').focus();
                return false;
            }

            if (!confirm('수정하시겠습니까?')) {
                return false;
            }
            return true;
        });


        $('#broker_id').on('change', function() {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "/fund/strategies/tools",
                data: { broker_id: $(this).val() }
            }).done(function(data) {
                if (data == null) {
                    data = 0;
                } else {
                    $("#tool_id option").not("[value='']").remove();
                    for (var i=0; i<data.length; i++) {
                        $('#tool_id').append("<option value='"+data[i]['tool_id']+"'>"+data[i]['name']+"</option>");
                    }
                    var text = $('#tool_id option:eq(0)').text();
                    $('#tool_id').eq(0).siblings('label').text(text);
                }
            });

            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "/fund/strategies/pb_search",
                data: { broker_id: $(this).val() }
            }).done(function(data) {
                if (data == null) {
                    data = 0;
                } else {
                    $("#pb_uid option").not('.no').remove();
                    for (var i=0; i<data.length; i++) {
                        $('#pb_uid').append("<option value='"+data[i]['uid']+"'>"+data[i]['name']+"</option>");
                    }
                    var text = $('#pb_uid option:eq(0)').text();
                    $('#pb_uid').eq(0).siblings('label').text(text);
                }
            });
        });

        // 트레이더 검색
        $('.btn_search').on('click', function(){
            if(!$('#nickname').val()) return;

            $('.search_result .trader_list').html('');

            $.ajax({
                method: "POST",
                dataType: 'json',
                url: "/fund/strategies/trader_search",
                data: { nickname: $('#nickname').val() }
            }).done(function(data) {
                $('.search_result').show();

                if (data.items.length) {
                    $.each(data.items, function(key, val){
                        var html = '';
                        html += '<li>';
                        html += '<img src="'+val.picture+'" alt="" />';
                        html += '<label><strong class="name">'+escapedHTML(val.nickname)+'</strong> ';
                        html += '<input type="radio" name="trader_uid" id="trader_uid" class="option" type="radio" value="'+val.uid+'" /></label>';
                        html += '</li>';
                        $('.trader_list').append(html);
                    });
                } else {
                    $('.trader_list').html('<li><strong class="name">검색된 트레이더가 없습니다</li>');
                }
            });

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
						<div class="pd_regist type01">
							<div class="info">
								<strong class="name"><?=$strategy['name']?></strong>
								<div class="regist_date">
									<dl>
										<dt>등록 : </dt>
										<dd><?=$strategy['reg_at']?></dd>
									</dl>
									<dl class="bottom">
										<dt>최종 업데이트 : </dt>
										<dd><?=($strategy['mod_at']) ? $strategy['mod_at'] : $strategy['reg_at']?></dd>
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
									<li class="curr"><a href="javascript:;">기본정보</a></li>
									<li><a href="/mypage/strategies/<?=$strategy['strategy_id']?>/analysis">일간분석</a></li>
									<li><a href="/mypage/strategies/<?=$strategy['strategy_id']?>/fund">펀딩금액/투자자수</a></li>
									<li><a href="/mypage/strategies/<?=$strategy['strategy_id']?>/account">실계좌 정보</a></li>
								</ul>
								<a href="/시스메틱전략등록방법.pdf" class="btn_upload_guide" target="_blank"><img src="/images/sub/ico_qm.gif" alt="?" /> 업로드 가이드</a>
							</div>
							<form id="reg_strategy_form" action="/mypage/strategies/<?=$strategy['strategy_id']?>/update" method="post" enctype="multipart/form-data">
								<fieldset>
									<legend class="screen_out"></legend>
										<div class="group">
											<table class="form_tbl">
												<colgroup>
													<col style="width:164px">
													<col style="width:331px">
													<col style="width:164px">
													<col style="width:331px">
												</colgroup>
                                                <?
                                                //__v($info);
                                                ?>
												<tbody>
													<tr>
														<th>상품명</th>
														<td colspan="3">
															<div class="input_box" style="width:795px;">
																<input type="text" id="name" name="name" value="<?=$strategy['name']?>" placeholder="상품명을 입력해주세요." style="width:775px;" />
															</div>
														</td>
													</tr>
													<tr>
														<th>중개사</th>
														<td>

															<div class="custom_selectbox" style="width:298px;">
                                                            <? if ($_SESSION['user']['user_type'] == 'P') { ?>

                                                                <label for="broker_id"><?=$brokers['company']?></label>
                                                                <input type="hidden" id="broker_id" name="broker_id" value="<?=$brokers['broker_id']?>" />

                                                            <? } else { ?>
																<label for="select03">중개사를 선택해주세요.</label>
                                                                <select id="broker_id" name="broker_id">
                                                                    <option value="" selected="selected">중개사를 선택해주세요.</option>
                                                                    <?php foreach($brokers as $broker){ ?>
                                                                    <option value="<?php echo htmlspecialchars($broker['broker_id']) ?>" <?=($strategy['broker_id'] == $broker['broker_id']) ? 'selected' : ''?>><?php echo htmlspecialchars($broker['company']) ?></option>
                                                                    <? } ?>
                                                                </select>
                                                            <? } ?>
                                                            </div>

															<!-- <span class="op_name">한화투자증권</span> -->
														</td>
														<th>매매툴</th>
														<td>
															<div class="wrapping">
																<div class="custom_selectbox" style="width:298px;">
																	<label for="tool_id">매매툴을 선택해주세요.</label>
                                                                    <select name="tool_id" id="tool_id">
                                                                        <option value="" selected="selected">매매툴을 선택해주세요.</option>
                                                                        <? foreach ($tools_id as $tool) { ?>
                                                                        <option value="<?=$tool['tool_id'] ?>" <?=($strategy['tool_id'] == $tool['tool_id']) ? 'selected' : ''?>><?=$tool['name'] ?></option>
                                                                        <? } ?>
                                                                    </select>
																</div>
															</div>
														</td>
													</tr>

                                                    <? if ($_SESSION['user']['user_type'] == 'T' || $_SESSION['user']['user_type'] == 'A') { ?>
                                                    <tr>
                                                        <th>PB</th>
                                                        <td colspan="3">
                                                            <div class="custom_selectbox" style="width:298px;">
                                                                <label for="pb_uid">PB를 선택해주세요.</label>
                                                                <select id="pb_uid" name="pb_uid">
                                                                    <option value="" selected="selected">PB를 선택해주세요.</option>
                                                                    <option value="0" class='no' <?=($strategy['pb_uid'] == 0) ? 'selected' : ''?>>없음</option>
                                                                    <? foreach ($pb as $v) { ?>
                                                                    <option value="<?=$v['uid']?>" <?=($strategy['pb_uid'] == $v['uid']) ? 'selected' : ''?>><?=$v['name']?></option>
                                                                    <? } ?>
                                                                </select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <? } else { ?>
                                                    <input type="hidden" id="pb_uid" name="pb_uid" value="<?=$strategy['pb_uid']?>" />
                                                    <? } ?>
                                                    
                                                    <? if ($_SESSION['user']['user_type'] == 'P' || $_SESSION['user']['user_type'] == 'A') { ?>
													<tr>
														<th>트레이더</th>
														<td colspan="3">
															<div class="input_box in_btn" style="width:300px;">
																<input type="text" id="nickname" name="nickname" placeholder="트레이더 검색" style="width:228px;" />
																<button type="button" class="btn_search" id="search_btn" title="검색"></button>
															</div>
																<div class="btn_search"><a href="javascript:;" class="btn">검색</a></div>
														</td>
													</tr>

                                                    <!-- 트레이더 검색결과 : 아래 tr에 show클래스 삭제하면 화면에서 숨김 -->
                                                    <tr class="search_result hide">
                                                        <th>검색결과</th>
                                                        <td colspan="3">
                                                            <ul class="trader_list">
                                                                <li>
                                                                    <img src="<?=$trader['picture']?>" alt="" />
                                                                    <strong class="name"><?=$trader['nickname']?></strong>
                                                                    <label><input type="radio" name="trader_uid" id="trader_uid" class="option" type="radio" value="<?=$trader['uid']?>" checked /></label>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                    <? } else { ?>
                                                    <input type="hidden" id="trader_uid" name="trader_uid" value="<?=$strategy['trader_uid']?>" />
                                                    <? } ?>

													<tr>
														<th>종류</th>
														<td>
															<div class="wrapping">
																<div class="custom_selectbox" style="width:298px;">
																	<label for="strategy_type">종류를 선택해주세요.</label>
                                                                    <select id="strategy_type" name="strategy_type">
                                                                        <option value="M" selected="selected">종류를 선택해주세요.</option>
                                                                        <? foreach ($types as $type) { ?>
                                                                        <option value="<?=$type['type_id']?>" <?=($strategy['strategy_type'] == $type['type_id']) ? 'selected' : ''?>><?=$type['name']?></option>
                                                                        <? } ?>
                                                                        <!--option value="S">System Trading</option-->
                                                                    </select>

                                                                    <!--select id="strategy_type" name="strategy_type">
                                                                        <option value="M">종류를 선택해주세요.</option>
                                                                        <option value="M" <?=($strategy['strategy_type'] == 'M') ? 'selected' : ''?>>Manual Trading</option>
                                                                        <option value="S" <?=($strategy['strategy_type'] == 'S') ? 'selected' : ''?>>System Trading</option>
																	</selec-->
																</div>
															</div>
														</td>
														<th>주기</th>
														<td>
															<div class="wrapping">
																<div class="custom_selectbox" style="width:298px;">
																	<label for="term">주기를 선택해주세요.</label>
                                                                    <select id="term" name="term">
                                                                        <option value="" selected="selected">주기를 선택해주세요.</option>
                                                                        <option value="day" <?=($strategy['strategy_term'] == 'day') ? 'selected' : ''?>>데이</option>
                                                                        <option value="position" <?=($strategy['strategy_term'] == 'position') ? 'selected' : ''?>>포지션</option>
                                                                    </select>
																</div>
															</div>
														</td>
													</tr>
													<tr>
														<th>종목</th>
														<td colspan="3">
															<div class="wrapping choice_type">

                                                                <!-- 종목관리에 등록된 종목 불러옴 -->
																<table>
																<tr>
																<?
																$a=0;
																foreach ($items as $item) { 
																?>
																<td style="height:40px;border-bottom: 0px solid #bcbcbc;"><input name="item_ids[]" id="item<?php echo $item['item_id'] ?>" class="option" type="checkbox" value="<?php echo $item['item_id'] ?>" data-role="item" <?php if(in_array($item['item_id'], $strategy['items'])) echo ' checked="checked"' ?> /><label for="item<?php echo $item['item_id'] ?>"><?php echo htmlspecialchars($item['name']) ?></label></td>
																<?
																	if($a==4){
																		echo "</tr><tr>";
																		$a=0;
																	}else{
																		$a++;
																	}
																}
																?>
																</tr>
																</table>


															</div>
														</td>
													</tr>


                                                    <tr>
                                                        <th>상품종류</th>
														<td>
                                                            <div class="custom_selectbox" style="width:298px;">
                                                                <label for="strategy_kind">종류를 선택해주세요.</label>
                                                                <select id="strategy_kind" name="strategy_kind">
                                                                    <option value="" selected="selected">상품종류를 선택해주세요.</option>
                                                                    <? foreach ($kinds as $kind) { ?>
                                                                    <option value="<?=$kind['kind_id']?>" <?=($strategy['strategy_kind'] == $kind['kind_id']) ? 'selected' : ''?>><?=$kind['name']?></option>
                                                                    <? } ?>
                                                                </select>
                                                            </div>
                                                        </td>

                                                        <th>통화</th>
                                                        <td>
                                                            <div class="custom_selectbox" style="width:298px;">
                                                                <label for="strategy_currency">통화를 선택해주세요.</label>
                                                                <select id="strategy_currency" name="currency">
                                                                    <option value="" selected="selected">통화를 선택해주세요.</option>
                                                                    <option value="KRW" <?=($strategy['currency'] == 'KRW') ? 'selected' : ''?>>KRW</option>
                                                                    <option value="USD" <?=($strategy['currency'] == 'USD') ? 'selected' : ''?>>USD</option>
                                                                    <option value="JPY" <?=($strategy['currency'] == 'JPY') ? 'selected' : ''?>>JPY</option>
                                                                    <option value="EUR" <?=($strategy['currency'] == 'EUR') ? 'selected' : ''?>>EUR</option>
                                                                    <option value="CNY" <?=($strategy['currency'] == 'CNY') ? 'selected' : ''?>>CNY</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                    </tr>


													<tr>
														<th class="multi_line">최소위탁<br>가능금액</th>
														<td colspan="3">
															<div class="custom_selectbox" style="width:298px;">
																<label for="min_price">위탁금액 범위선택</label>
                                                                <select id="min_price" name="min_price">
                                                                    <option value="" selected="selected">위탁금액 범위선택</option>
                                                                    <? foreach ($fund_price as $k => $v) { ?>
                                                                    <option value="<?=$k?>" <?=($strategy['min_price'] == $k) ? 'selected' : ''?>><?=$v?></option>
                                                                    <? } ?>
                                                                </select>
															</div>
														</td>
													</tr>
													<tr class="high">
														<th>상품소개</th>
														<td colspan="3">
															<div class="textarea_box">
																<textarea name="intro" id="intro" placeholder="상품소개 내용을 입력해주세요."><?=$strategy['intro']?></textarea>
															</div>
														</td>
													</tr>
													<tr>
														<th>제안서</th>
														<td colspan="2">
															<div class="file_add">
																<div class="input_box" style="width:298px;">
																	<input type="text" id="file_name" name="file_name" placeholder="파일을 첨부해주세요." style="width:278px;" readonly />
																</div>
																<input type="file" id="file01" name="attach_file" onchange="$('#file_name').val(this.value);" />
																<label for="file01" class="btn_add_file">찾아보기</label>
															</div>
                                                        </td>
                                                        <td>
															<div class="wrapping choice_type">
                                                            <? if($strategy['file']) { ?>
                                                                <input type="hidden" name="save_name" value="<?=$strategy['file']['save_name']?>" />
                                                                <input name="attached_file_del" id="attached_file_del" class="option" type="checkbox" value="<?=$strategy['file']['save_name']?>" /><label for="attached_file_del">삭제 (<?=$strategy['file']['file_name']?>)</label>
                                                            <? } ?>
															</div>
														</td>
                                                    </tr>
												</tbody>
											</table>
										</div>
										<div class="btn_area">
											<a href="/mypage/strategies" class="btn_common_gray btn_list">목록</a>
											<button type="submit" class="btn_common_red btn_modify">수정</button>
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
        <? require_once $skinDir."common/footer.php" ?>
        <!-- //footer -->
    </div>
    <!-- //wrapper -->

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
