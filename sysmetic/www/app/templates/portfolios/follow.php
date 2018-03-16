
<!-- 레이어팝업 : Follow 하기 -->
<article class="layer_popup strategy_follow">
    <div class="dim" onclick="commonLayerClose('strategy_follow')"></div>
    <div class="contents">
        <div class="layer_header">
            <h2>Follow 하기</h2>
            <button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('strategy_follow')"></button>
        </div>
        <div class="cont">
            <p class="name"></p>
            <div class="form_box">
                <div class="row">
                    <strong class="title">그룹선택</strong>
                    <div class="custom_selectbox">
                        <label for="">그룹선택</label>
                        <select id="follow_group">
                            <option selected="selected" value="">그룹선택</option>
                            <? foreach ($groups as $k => $v) { ?>
                            <option value="<?=$v['group_id']?>"><?=$v['group_name']?></option>
                            <? } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <strong class="title">그룹만들기</strong>
                    <div class="input_box in_btn">
                        <input type="hidden" id="portfolio_id" value="" />
                        <input type="text" id="group_name" name="group_name" require="required" placeholder="그룹명을 입력" />
                        <button type="button" id="save_group" name="save_group" class="btn_save">저장</button>
                    </div>
                </div>
            </div>
            <div class="btn_area half">
                <button type="button" id="btn_follow" class="btn_common_red">Follow</button>
                <a href="javascript:;" class="btn_common_gray" onclick="commonLayerClose('strategy_follow')">취소</a>
            </div>
        </div>
    </div>
</article>
<!-- //레이어팝업 : Follow 하기 -->

<!-- 레이어팝업 : Follow 완료 -->
<article class="layer_popup strategy_follow_complete">
    <div class="dim" onclick="commonLayerClose('strategy_follow_complete')"></div>
    <div class="contents">
        <div class="layer_header">
            <h2>Follow 하기</h2>
            <button type="button" title="레이어 팝업 닫기" class="btn_close_layer" onclick="commonLayerClose('strategy_follow_complete')"></button>
        </div>
        <div class="cont">
            <div class="summary">
                <p class="complete_msg">전략을 Follow 합니다.</p>
                <p class="info_msg">Follow 한 전략은 나의 관심전략 페이지에서<br />확인 가능합니다.</p>
            </div>
            <div class="btn_area">
                <button type="button" class="btn_common_gray" onclick="commonLayerClose('strategy_follow_complete')">닫기</button>
            </div>
        </div>
    </div>
</article>
<!-- //레이어팝업 : Follow 완료 -->

<script>
$(function(){
    $('#save_group').click(function(){
        var group_name = $.trim($('#group_name').val());
        if (!group_name) {
            alert('그룹명을 입력하세요.');
        } else {
            $.post('/strategies/follow/group', {'group_name' : group_name}, function(data){
                if (data.result) {
                    $('#follow_group').append('<option value="'+data.result+'">'+group_name+'</option>')
                    $('#group_name').val('');
                    alert('그룹이 등록되었습니다');
                } else {
                    alert('처리중 오류가 발생하였습니다');
                }
            }, 'json');
        }
    });

    $('#btn_follow').click(function(){
        var btn_el = $(this);
        var group_id = $('#follow_group option:selected').val();
        var portfolio_id = $('.strategy_follow #portfolio_id').val();
        if (!portfolio_id) {
            alert('처리중 오류가 발생하였습니다');
            commonLayerClose('strategy_follow');
        } else if (!group_id) {
            alert('그룹을 선택하세요');
        } else {
            $.post('/portfolios/'+portfolio_id+'/follow', {type:'json', group_id: group_id}, function(data){
                if (data.result) {
                    $('#btn_follow'+portfolio_id).attr('title', 'UnFollow').attr('class', 'btn_unfollow').data('role', 'unfollow').text('Unfollow -');
                    $('#follows_count'+portfolio_id).text(parseInt($('#follows_count'+portfolio_id).text()) + 1);

                    commonLayerClose('strategy_follow');
                    commonLayerOpen('strategy_follow_complete');
                } else {
                    alert('처리중 오류가 발생하였습니다.');
                }
            }, 'json');
        }
    });
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
