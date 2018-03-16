<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 전략 상세보기 - <?php echo htmlspecialchars($strategy['name']) ?></title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="/js/calendar.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#edit_strategy_form').submit(function(){
			if(!$('#name').val()){
				alert('전략명을 입력해주세요');
				$('#name').focus();
				return false;
			}

			if($('input[data-role=item]:checked').length == 0){
				alert('종목을 선택해주세요');
				return false;
			}

            /*
			if(!$('input[name=broker_type]:checked').val()){
				alert('브로커를 선택해주세요');
				return false;
			}
            */

			if(!$('input[name=broker_id]:checked').val()){
				alert('중개사를 선택해주세요');
				return false;
			}

			if(!$('input[name=tool_id]:checked').val()){
				alert('매매툴을 선택해주세요');
				return false;
			}

			/*if($('#investment').val() == ''){
				alert('투자원금을 입력해주세요');
				return false;
			}*/

			if($('#intro').val() == ''){
				alert('전략소개를 입력해주세요');
				return false;
			}

			return true;
		});

		$('#search_btn').on('click', function(){
			if(!$('#nickname').val()) return;

			$('#trader_result').html('');
			$.getJSON('/admin/trader_search?nickname=' + $('#nickname').val(),{"nickname":$('#nickname').val()}, function(data){

				console.log(data);

				$.each(data.items, function(key, val){
					var html = '';
					html += '<li>';
                    html += '<p><input name="developer_uid" id="developer_uid'+val.uid+'" class="option" type="radio" value="'+val.uid+'" /><label for="developer_uid'+val.uid+'">'+escapedHTML(val.nickname)+'</label></p>';
                    html += '</li>';
					$('#trader_result').append(html);
				});
			});
		});

		$('#broker_type .iList input[type=radio]').on('click', function(){
			$('#company_type1').hide();
			$('#company_type2').hide();

			if($(this).data('sub-list')){
				$('#'+$(this).data('sub-list')).css('display', 'inline-block');
				$('#'+$(this).data('sub-list')).find('input:eq(0)').attr('checked','checked');
				$('#'+$(this).data('sub-list')).find('.myValue').text($('#'+$(this).data('sub-list')).find('label:eq(0)').text());
				$('#tools_list .select').hide();
			}
		});

		$('#company_type1 .iList input[type=radio]').on('click', function(){
			$('#tools_list .select').hide();
			$('#pb_list .select').hide();
			if($(this).val()){
				if($('#broker'+$(this).val() + '_tools').length){
					$('#broker'+$(this).val() + '_tools').show();
					$('#broker'+$(this).val() + '_tools').find('input:eq(0)').attr('checked','checked');
					$('#broker'+$(this).val() + '_tools').find('.myValue').text($('#broker'+$(this).val() + '_tools').find('label:eq(0)').text());
				}
				if($('#broker'+$(this).val() + '_pb').length){
					$('#broker'+$(this).val() + '_pb').show();
					$('#broker'+$(this).val() + '_pb').find('input:eq(0)').attr('checked','checked');
					$('#broker'+$(this).val() + '_pb').find('.myValue').text($('#broker'+$(this).val() + '_pb').find('label:eq(0)').text());
				}
				// else $('#default_tools').show();
			}else{
				// $('#default_tools').show();
			}
		});

		$('#company_type2 .iList input[type=radio]').on('click', function(){
			$('#tools_list .select').hide();
			if($(this).val()){
				if($('#broker'+$(this).val() + '_tools').length){
					$('#broker'+$(this).val() + '_tools').show();
					$('#broker'+$(this).val() + '_tools').find('input:eq(0)').attr('checked','checked');
					$('#broker'+$(this).val() + '_tools').find('.myValue').text($('#broker'+$(this).val() + '_tools').find('label:eq(0)').text());
				}
				// else $('#default_tools').show();
			}else{
				// $('#default_tools').show();
			}
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
            <h3 style="font-size:1.875em;font-weight:bold;font-style: normal;">상품 상세보기</h3>

            <h4 class="admin"><?php echo htmlspecialchars($strategy['name']) ?></h4>
            <span class="write_date"><b>등록일 :</b> <?php echo date('Y.m.d H:i:s', strtotime($strategy['reg_at'])) ?></span>

            <!-- 기본정보 -->
			<form action="/admin/strategies/<?php echo $strategy['strategy_id'] ?>" method="post" enctype="multipart/form-data" id="edit_strategy_form">
            <div id="strategy_view0" name="strategy_view" class="strategy_view" style="display:block;">

                <div class="tab">
                    <a title="기본정보" class="<?php if($current_tab_menu == 'basic') echo 'tab_on'; else echo 'tab_off' ?>" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>"><span class="ir">기본정보</span></a>
                    <a title="일간분석" class="<?php if($current_tab_menu == 'daily') echo 'tab_on'; else echo 'tab_off' ?>" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/daily"><span class="ir">일간분석</span></a>
                    <a title="펀딩금액/투자자 수" class="<?php if($current_tab_menu == 'funding') echo 'tab_on'; else echo 'tab_off' ?> long" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding"><span class="ir">펀딩금액/투자자 수</span></a>
                    <a title="실계좌 정보" class="<?php if($current_tab_menu == 'accounts') echo 'tab_on'; else echo 'tab_last' ?>" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts"><span class="ir">실계좌 정보</span></a>
                </div>

                <table border="0" cellspacing="0" cellpadding="0" class="admin_write" style="width:96%;">
                <col width="160" /> <col width="240" /><col width="160" /> <col width="*" />
                <tbody>

                    <tr>
                        <td class="thead">펀딩여부</td>
                        <td colspan="3">   
                            <div class="select open" style="width:110px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <!-- <li><input name="status" id="status0" class="option" type="radio" /><label for="status0">상태 선택</label></li> -->
                                    <li><input name="is_fund" id="is_fund1" class="option" type="radio" value="1"<?php if($strategy['is_fund']) echo ' checked="checked"' ?> /><label for="is_fund1">펀딩가능</label></li>
                                    <li><input name="is_fund" id="is_fund2" class="option" type="radio" value="0"<?php if(!$strategy['is_fund']) echo ' checked="checked"' ?> /><label for="is_fund2">펀딩불가</label></li>
                                </ul>
                            </div> 
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">상태</td>
                        <td>   
                            <div class="select open" style="width:110px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <!-- <li><input name="status" id="status0" class="option" type="radio" /><label for="status0">상태 선택</label></li> -->
                                    <li><input name="is_operate" id="status1" class="option" type="radio" value="1"<?php if($strategy['is_operate']) echo ' checked="checked"' ?> /><label for="status1">운용 중</label></li>
                                    <li><input name="is_operate" id="status2" class="option" type="radio" value="0"<?php if(!$strategy['is_operate']) echo ' checked="checked"' ?> /><label for="status2">운용 중지</label></li>
                                </ul>
                            </div> 
                        </td>
                        <td class="thead">공개여부</td>
                        <td>
                            <div class="select open" style="width:110px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <!-- <li><input name="open" id="open0" class="option" type="radio" /><label for="open0">공개여부</label></li> -->
                                    <li><input name="is_open" id="open1" class="option" type="radio" value="1"<?php if($strategy['is_open']) echo ' checked="checked"' ?> /><label for="open1">공개</label></li>
                                    <li><input name="is_open" id="open2" class="option" type="radio" value="0"<?php if(!$strategy['is_open']) echo ' checked="checked"' ?> /><label for="open2">비공개</label></li>
                                </ul>
                            </div> 
			                <br /><br />* 일간 분석 정보를 3일 이상 등록해야 공개 가능
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">상품명</td>
                        <td colspan="3">   
                            <input id="name" name="name" type="text" title="상품명" style="width:365px;" value="<?php echo htmlspecialchars($strategy['name']) ?>" required="required" />
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">중개사</td>
                        <td>   
                            <div id="company_type1" class="select open" style="width:140px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="broker_id" id="broker_type1_0" class="option" type="radio" value="" /><label for="broker_type1_0">브로커 선택</label></li>
									<?php foreach($company as $v){ ?>
                                    <li><input name="broker_id" id="broker<?php echo $v['id'] ?>" class="option" type="radio" value="<?php echo $v['id'] ?>"<?php if($strategy['broker_id'] == $v['id']) echo ' checked="checked"' ?> /><label for="broker<?php echo $v['id'] ?>"><?php echo htmlspecialchars($v['name']) ?></label></li>
									<?php } ?>
                                </ul>
                            </div>
                        </td>
                        <td class="thead">매매툴</td>
                        <td id="tools_list">
							<div id="default_tools" class="select open" style="width:140px;<?php if(!empty($strategy['tool_id'])) echo 'display:none;' ?>">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="tool_id" id="tool" class="option" type="radio" value="" /><label for="tool">매매툴 선택</label></li>
                                </ul>
                            </div> 

                            <?php foreach($tools as $k => $v){ ?>
                            <div id="<?php echo $k ?>_tools" class="select open" style="width:140px;<?php if($k != 'broker'.$strategy['broker_id']) echo 'display:none;' ?>">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="tool_id" id="tool" class="option" type="radio" value="" /><label for="tool">매매툴 선택</label></li>
									<?php foreach($v as $tool){ ?>
                                    <li><input name="tool_id" id="tool<?php echo $tool['tool_id'] ?>" class="option" type="radio" value="<?php echo $tool['tool_id'] ?>"<?php if($strategy['tool_id'] == $tool['tool_id']) echo ' checked="checked"' ?> /><label for="tool<?php echo $tool['tool_id'] ?>"><?php echo htmlspecialchars($tool['name']) ?></label></li>
									<?php } ?>									
                                </ul>
                            </div> 
							<?php } ?>
                            <!-- Broker에 등록된 시스템 트레이딩 불러옴 -->
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">PB</td>
                        <td id="pb_list" colspan="3">
                            <?php foreach($pb_list as $k => $v){ ?>
                            <div id="<?php echo $k ?>_pb" class="select open" style="width:140px;<?php if($k != 'broker'.$strategy['broker_id']) echo 'display:none;' ?>">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="pb_uid" id="pb_00" class="option" type="radio" value="" <?php if(!$strategy['pb_uid']) echo ' checked="checked"'?> /><label for="pb_00">PB 없음</label></li>
									<?php foreach($v as $pb){ ?>
                                    <li><input name="pb_uid" id="pb_<?php echo $pb['uid'] ?>" class="option" type="radio" value="<?php echo $pb['uid'] ?>"<?php if($strategy['pb_uid'] == $pb['uid']) echo ' checked="checked"' ?> /><label for="pb_<?php echo $pb['uid'] ?>"><?php echo htmlspecialchars($pb['name']) ?></label></li>
									<?php } ?>
                                </ul>
                            </div>
                            <? } ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">트레이더</td>
                        <td colspan="3">
							<?php if(!empty($strategy['developer']['nickname'])) echo htmlspecialchars($strategy['developer']['nickname']) ?>
                            <input id="nickname" name="nickname" type="text" title="닉네임" style="width:150px;" value="" />
                            <button id="search_btn" type="button" title="검색"><span class="ir">검색</span></button>
                            <!-- 검색 결과 -->
                            <div class="trader_search" id="trader_result">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">종류</td>
                        <td>
                            <!-- Super Admin만 표기 -->  
                            <div class="select open" style="width:150px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="strategy_type" id="trading0" class="option" type="radio" value="" <?php if(!$strategy['strategy_type']) echo ' checked="checked"' ?> /><label for="trading0">타입 선택</label></li>
                                    <? foreach ($types as $k=>$v) { ?>
                                    <li><input name="strategy_type" id="trading0<?=$k?>" class="option" type="radio" value="<?=$v['type_id']?>"<?php if($strategy['strategy_type'] == $v['type_id']) echo ' checked="checked"' ?> /><label for="trading0<?=$k?>"><?=$v['name']?></label></li>
                                    <? } ?>
                                </ul>
                            </div> 
                        </td>
                        <td class="thead">주기</td>
                        <td> 
                            <div class="select open" style="width:90px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="term" id="term_type1" class="option" type="radio" value="day"<?php if($strategy['strategy_term'] == 'day') echo ' checked="checked"' ?> /><label for="term_type1">데이</label></li>
                                    <li><input name="term" id="term_type2" class="option" type="radio" value="position"<?php if($strategy['strategy_term'] == 'position') echo ' checked="checked"' ?> /><label for="term_type2">포지션</label></li>
                                </ul>
                            </div> 
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">종목</td>
                        <td colspan="3">
                            <p class="admin">
                                <!-- 종목관리에 등록된 종목 불러옴 -->
								<?php foreach($items as $item){ ?>
								<input name="item_ids[]" id="item<?php echo $item['item_id'] ?>" class="option" type="checkbox" value="<?php echo $item['item_id'] ?>" data-role="item"<?php if(in_array($item['item_id'], $strategy['items'])) echo ' checked="checked"' ?> /><label for="item<?php echo $item['item_id'] ?>"><?php echo htmlspecialchars($item['name']) ?></label>
								<?php } ?>                     
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">상품종류</td>
                        <td>   
                            <div class="select open" style="width:150px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="strategy_kind" id="kind0" class="option" type="radio" value="" <?php if(!$strategy['strategy_kind']) echo ' checked="checked"' ?> /><label for="kind0">상품종류 선택</label></li>
                                    <? foreach ($kinds as $k=>$v) { ?>
                                    <li><input name="strategy_kind" id="kind0<?=$k?>" class="option" type="radio" value="<?=$v['kind_id']?>"<?php if($strategy['strategy_kind'] == $v['kind_id']) echo ' checked="checked"' ?> /><label for="kind0<?=$k?>"><?=$v['name']?></label></li>
                                    <? } ?>
                                </ul>
                            </div> 
                        </td>
                        <td class="thead">통화</td>
                        <td>   
                            <div class="select open" style="width:90px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <!-- <li><input name="money" id="money0" class="option" type="radio" /><label for="money0">선택</label></li> -->
                                    <li><input name="currency" id="money1" class="option" type="radio" value="KRW"<?php if($strategy['currency'] == 'KRW') echo ' checked="checked"' ?> /><label for="money1">KRW</label></li>
                                    <li><input name="currency" id="money2" class="option" type="radio" value="USD"<?php if($strategy['currency'] == 'USD') echo ' checked="checked"' ?> /><label for="money2">USD</label></li>
                                    <li><input name="currency" id="money3" class="option" type="radio" value="JPY"<?php if($strategy['currency'] == 'JPY') echo ' checked="checked"' ?> /><label for="money3">JPY</label></li>
                                    <li><input name="currency" id="money4" class="option" type="radio" value="EUR"<?php if($strategy['currency'] == 'EUR') echo ' checked="checked"' ?> /><label for="money4">EUR</label></li>
                                    <li><input name="currency" id="money5" class="option" type="radio" value="CNY"<?php if($strategy['currency'] == 'CNY') echo ' checked="checked"' ?> /><label for="money5">CNY</label></li>
                                </ul>
                            </div> 
                        </td>
                    </tr>
					<!--
                    <tr>
                        <td class="thead">시작일자</td>
                        <td>   
                            <input id="" name="" type="text" title="시작일자" class="datepicker" value="2012.03.12"  />
                        </td>
                        <td class="thead">최종일자</td>
                        <td>
                            <input id="" name="" type="text" title="최종일자" class="datepicker" value="2015.03.15"  />
                        </td>
                    </tr>
					-->
					<tr>
                        <td class="thead">최소위탁 가능금액</td>
                        <td colspan="3"> 
                            <div class="select open" style="width:250px;">
                                <div class="myValue"></div>
                                <ul class="iList">
                                    <li><input name="min_price" id="price0" class="option" type="radio" value="" <?php if(!$strategy['min_price']) echo ' checked="checked"' ?> /><label for="price0">금액 선택</label></li>
                                    <? foreach ($fund_price as $k=>$v) { ?>
                                    <li><input name="min_price" id="price0<?=$k?>" class="option" type="radio" value="<?=$k?>"<?php if($strategy['min_price'] == $k) echo ' checked="checked"' ?> /><label for="price0<?=$k?>"><?=$v?></label></li>
                                    <? } ?>
                                </ul>
                            </div> 
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">전략소개</td>
                        <td colspan="3">
                            <textarea name="intro"><?php echo htmlspecialchars($strategy['intro']) ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="thead">제안서</td>
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
            
                <p class="btn_area">
                    <button type="submit" title="수정" class="submit"><span class="ir">수정</span></button>
                    <button type="reset" title="취소" class="cancel"><span class="ir">취소</span></button>
                </p>
            </div>
			</form>
            
            <p class="btn_board">
                <button type="button" onclick="location.href='/admin/strategies?page=<?php echo $page ?>';" title="목록" class="cancel"><span class="ir">목록</span></button>
            </p>
        </div>        
    </div>
    <!------ //본문 영역 ------->  

	<?php require_once('footer.php') ?>

</body>
</html>
