<h3>리뷰</h3>
<form id="form_review" action="/portfolios/<?=$portfolio_id?>/reviews/add" method="post">
<div class="regist_review">
    <div class="textarea_box">
        <textarea id="review_body" name="review_body" placeholder="리뷰를 입력해주세요."></textarea>
    </div>
    <button type="submit" class="btn_regist">리뷰<br />등록</button>
</div>
</form>
<div class="review_list">
    <ul>
        <? foreach ($reviews as $review) { ?>
        <li>
            <div class="info">
                <strong class="name"><? if(!empty($review['writer']['nickname'])) echo $review['writer']['nickname']; else echo $review['writer_name'] ?></strong>
                <span class="regist_time"><?=date('Y.m.d H:i', strtotime($review['reg_at']))?></span>
            </div>
            <p class="review_contents">
                <?=nl2br(htmlspecialchars($review['contents']))?>
            </p>
            <!-- 내가 작성한 리뷰일 경우 아래 div 노출 -->
            <? if ($_SESSION['user']['user_type'] == 'A' || $_SESSION['user']['uid'] == $review['writer_uid']) { ?>
            <div class="my_review">
                <button type="button" onclick="deleteReview(<?=$review['review_id']?>)" class="btn_delete" title="리뷰 삭제하기"></button>
            </div>
            <? } ?>
        </li>
        <? } ?>
    </ul>

    <?=$paging?>

</div>

<script>
$(function(){
    $('#form_review').submit(function(){
        <? if (!$isLoggedIn()) { ?>
        if (confirm("로그인이 필요합니다\n로그인 하시겠습니까?")) {
            login();
            return false;
        } else {
            return false;
        }
        <? } ?>

        if (!$('#review_body').val()) {
            $('#review_body').focus();
            alert('내용을 입력해주세요');
            return false;
        }

        /*
        if($('input[name=star]:checked').val() == '0'){
            alert('별점을 선택해주세요');
            return false;
        }
        */
        $.post($(this).attr('action'), $(this).serialize(), function(data){
            if (data.result) {
                moveReview(1);
            } else {
                alert('처리 중 요류가 발생하였습니다');
            }
        }, 'json');
        return false;
    });
});

function moveReview(page) {
    $('.review_area').load('/portfolios/<?=$portfolio_id?>/reviews?page='+page);
}

function deleteReview(id) {
    if (!confirm('삭제하시겠습니까?')) {
        return;
    }

    $.post('/portfolios/<?=$portfolio_id?>/reviews/' + id + '/delete', $(this).serialize(), function(data){
        if (data.result) {
            moveReview(1);
        } else {
            alert('처리 중 요류가 발생하였습니다');
        }
    }, 'json');
}
</script>
