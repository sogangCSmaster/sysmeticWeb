<!doctype html>
<html lang="ko">
<head>
    <title>투자받기 | SYSMETIC</title>
    <? require_once $skinDir."common/head.php" ?>
    <script>
    $(function(){
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
            
            if(!$('#pb_uid').val()){
                alert('PB를 선택해주세요');
                $('#pb_uid').focus();
                return false;
            }

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

            /* if($('#investment').val() == ''){
                alert('투자원금을 입력해주세요');
                return false;
            }*/

            if($('#intro').val() == ''){
                alert('전략소개를 입력해주세요');
                return false;
            }

            if (!confirm('등록하시겠습니까?')) {
                return false;
            }
            return true;
        });


        $('#broker_id').on('change', function() {
            $.ajax({
                method: "POST",
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
                method: "POST",
                dataType: 'json',
                url: "/fund/strategies/pb_search",
                data: { broker_id: $(this).val() }
            }).done(function(data) {
                if (data == null) {
                    data = 0;
                } else {
                    $("#pb_uid option").not(".no").remove();
                    for (var i=0; i<data.length; i++) {
                        $('#pb_uid').append("<option value='"+data[i]['uid']+"'>"+data[i]['name']+"</option>");
                    }
                    var text = $('#pb_uid option:eq(0)').text();
                    $('#pb_uid').eq(0).siblings('label').text(text);
                }
            });
        });

        // 트레이더 검색
        $('#search_btn').on('click', function(){
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
            <section class="area fund">
                <div class="page_title_area no_bg">
                    <? if ($_SESSION['user']['user_type'] == 'T') { ?>
                    <p class="page_title n_squere">상품 등록 - 트레이더</p>
                    <p class="page_summary">트레이더 회원 상품등록을 하실 수 있습니다.</p>
                    <? } else if ($_SESSION['user']['user_type'] == 'P') { ?>
                    <p class="page_title n_squere">상품 등록 - PB</p>
                    <p class="page_summary">PB 회원 상품등록을 하실 수 있습니다.</p>
                    <? } else { ?>
                    <p class="page_title n_squere">상품 등록</p>
                    <p class="page_summary">상품등록을 하실 수 있습니다.</p>
                    <? } ?>
                </div>
                <div class="content_area regist">
                    <form action="/fund/strategies/write" method="post" id="reg_strategy_form" enctype="multipart/form-data">
                        <fieldset>
                            <legend class="screen_out">상품정보 입력</legend>
                            <table class="form_tbl">
                                <colgroup>
                                    <col style="width:18%" />
                                    <col style="width:32%" />
                                    <col style="width:18%" />
                                    <col style="width:32%" />
                                </colgroup>
                                <tbody>
                                    <tr>
                                        <th>상품명</th>
                                        <td colspan="3">
                                            <div class="input_box" style="width:795px;">
                                                <input type="text" id="name" name="name" placeholder="상품명을 입력해주세요.">
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

                                                    <label for="broker_id">중개사를 선택해주세요.</label>

                                                    <select id="broker_id" name="broker_id">
                                                        <option value="" selected="selected">중개사를 선택해주세요.</option>

                                                        <?php foreach($brokers as $broker){ ?>
                                                        <option value="<?php echo htmlspecialchars($broker['broker_id']) ?>"><?php echo htmlspecialchars($broker['company']) ?></option>
                                                        <? } ?>

                                                    </select>

                                                <? } ?>
                                            </div>
                                        </td>
                                        <th>매매툴</th>
                                        <td>
                                            <div class="custom_selectbox" style="width:298px;">
                                                <label for="tool_id">매매툴을 선택해주세요.</label>
                                                <select name="tool_id" id="tool_id">
                                                    <option value="" selected="selected">매매툴을 선택해주세요.</option>
                                                    <? foreach ($tools_id as $tool) { ?>
                                                    <option value="<?=$tool['tool_id'] ?>"><?=$tool['name'] ?></option>
                                                    <? } ?>
                                                </select>
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
                                                    <option value="" class='no' selected="selected">PB를 선택해주세요.</option>
                                                    <option value="0" class='no' >없음</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <? } else { ?>
                                    <input type="hidden" id="pb_uid" name="pb_uid" value="<?=$_SESSION['user']['uid']?>" />
                                    <? } ?>

                                    <? if ($_SESSION['user']['user_type'] == 'P' || $_SESSION['user']['user_type'] == 'A') { ?>
                                    <tr>
                                        <th>트레이더</th>
                                        <td colspan="3">
                                            <div class="wrapping">
                                                <div class="input_box in_btn" style="width:298px;">
                                                    <input type="text" id="nickname" name="nickname" placeholder="트레이더 검색" />
                                                    <button type="button" id="search_btn" class="btn_search_trader" title="검색"></button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="search_result hide">
                                        <th>검색결과</th>
                                        <td colspan="3">
                                            <ul class="trader_list">
                                            </ul>
                                        </td>
                                    </tr>
                                    <? } else { ?>
                                    <input type="hidden" id="trader_uid" name="trader_uid" value="<?=$_SESSION['user']['uid']?>" />
                                    <? } ?>

                                    <tr>
                                        <th>종류</th>
                                        <td>
                                            <div class="custom_selectbox" style="width:298px;">
                                                <label for="strategy_type">종류를 선택해주세요.</label>
                                                <select id="strategy_type" name="strategy_type">
                                                    <option value="" selected="selected">종류를 선택해주세요.</option>
                                                    <? foreach ($types as $type) { ?>
                                                    <option value="<?=$type['type_id']?>"><?=$type['name']?></option>
                                                    <? } ?>
                                                    <!--option value="S">System Trading</option-->
                                                </select>
                                            </div>
                                        </td>
                                        <th>주기</th>
                                        <td>
                                            <div class="custom_selectbox" style="width:298px;">
                                                <label for="term">주기를 선택해주세요.</label>
                                                <select id="term" name="term">
                                                    <option value="" selected="selected">주기를 선택해주세요.</option>
                                                    <option value="day" <?=($search['q_term'] == 'day') ? 'selected' : ''?>>데이</option>
                                                    <option value="position" <?=($search['q_term'] == 'position') ? 'selected' : ''?>>포지션</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>종목</th>
                                        <td colspan="3">
                                            <div class="check_category">
                                                <? foreach ($items as $item) { ?>
                                                <input name="item_ids[]" id="item<?php echo $item['item_id'] ?>" class="option" type="checkbox" value="<?php echo $item['item_id'] ?>" data-role="item" /><label for="item<?php echo $item['item_id'] ?>"><?php echo htmlspecialchars($item['name']) ?></label>
                                                <? } ?>
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
                                                    <option value="<?=$kind['kind_id']?>"><?=$kind['name']?></option>
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
                                                    <option value="KRW">KRW</option>
                                                    <option value="USD">USD</option>
                                                    <option value="JPY">JPY</option>
                                                    <option value="EUR">EUR</option>
                                                    <option value="CNY">CNY</option>
                                                    <option value="HKD">HKD</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="multi_line">최소위탁<br />가능금액</th>
                                        <td colspan="3">
                                            <div class="custom_selectbox" style="width:298px;">
                                                <label for="min_price">위탁금액 범위선택</label>
                                                <select id="min_price" name="min_price">
                                                    <option value="" selected="selected">위탁금액 범위선택</option>
                                                    <? foreach ($fund_price as $k => $v) { ?>
                                                    <option value="<?=$k?>"><?=$v?></option>
                                                    <? } ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="high">
                                        <th>상품소개</th>
                                        <td colspan="3">
                                            <div class="textarea_box">
                                                <textarea  name="intro" id="intro" placeholder="상품소개 내용을 입력해주세요."></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>제안서</th>
                                        <td colspan="3">
                                            <div class="file_add">
                                                <div class="input_box" style="width:298px;">
                                                    <input type="text" id="" name="" placeholder="파일을 첨부해주세요.">
                                                </div>
                                                <input type="file" id="file01" name="attach_file" />
                                                <label for="file01" class="btn_add_file">찾아보기</label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="btn_area">
                                <a href="/investment/strategies" class="btn_common_gray btn_cancel">취소</a>
                                <button class="btn_common_red btn_regist">등록</button>
                            </div>
                        </fieldset>
                    </form>
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
