<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - <?php echo htmlspecialchars($strategy['name']) ?> - 리뷰</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#form_review').submit(function(){
			<?php if(!$isLoggedIn()){ ?>
			alert('로그인해주세요');
			return false;
			<?php } ?>

			if($('input[name=star]:checked').val() == '0'){
				alert('별점을 선택해주세요');
				return false;
			}

			if(!$('#review_body').val()){
				$('#review_body').focus();
				alert('내용을 입력해주세요');
				return false;
			}

			return true;
		});
	});
	</script>
</head>

<body>
<!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content">
            <div class="frame_page">

<!-- 리뷰 -->
<div id="strategy_view0" name="strategy_view" class="strategy_view" style="display:block;">
    <div class="tab">
        <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/info" title="통계" class="tab_off"><span class="ir">통계</span></a>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/daily" title="일간분석" class="tab_off"><span class="ir">일간분석</span></a>
        <a href="/strategies/<?php echo $strategy['strategy_id'] ?>/monthly" title="월간분석" class="tab_off"><span class="ir">월간분석</span></a>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/accounts" title="실계좌 정보" class="tab_off"><span class="ir">실계좌 정보 </span></a>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews" title="리뷰" class="tab_on"><span class="ir">리뷰</span></a>
    </div>

	<form action="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews/add" method="post" id="form_review">
    <fieldset class="review">
        <legend>리뷰등록</legend>
        <div class="select open" style="width:140px;">
            <div id="starValue" class="myValue"></div>
            <ul class="iList">
                <li><input name="star" id="star0" class="option" value="0" onclick="chg_select_star ('starValue', 'myValue star0');" type="radio" checked="checked" /><label for="item0" style="display:block;">별점선택</label></li>
                <li><input name="star" id="star1" class="option" value="1" onclick="chg_select_star ('starValue', 'myValue star1');"  type="radio"  /><label for="star1" class="star1">★</label></li>
                <li><input name="star" id="star2" class="option" value="2" onclick="chg_select_star ('starValue', 'myValue star2');" type="radio" /><label for="star2" class="star2">★★</label></li>
                <li><input name="star" id="star3" class="option" value="3" onclick="chg_select_star ('starValue', 'myValue star3');" type="radio" /><label for="star3" class="star3">★★★</label></li>
				<li><input name="star" id="star4" class="option" value="4" onclick="chg_select_star ('starValue', 'myValue star4');" type="radio" /><label for="star4" class="star4">★★★★</label></li>
				<li><input name="star" id="star5" class="option" value="5" onclick="chg_select_star ('starValue', 'myValue star5');" type="radio" /><label for="star5" class="star5">★★★★★</label></li>
            </ul>
        </div>
		<!--
        <input id="" name="" type="text" class="" title="이름" onclick="this.className='ready';" value="이름" /> 
		-->
        <!-- 로그인 사용자의 경우
        <input id="" name="" type="text" class="ready" title="이름" value="안젤리나졸리나" /> 
        -->
        <p>
            <textarea id="review_body" name="review_body" onclick="this.className='ready';" required="required"></textarea>
            <button type="submit" title="리뷰 등록" class="review"><span class="ir">리뷰 등록</span></button>
         </p>
    </fieldset>
	</form>

	<?php if(count($reviews)){ ?>
    <ul class="review_list">
		<?php foreach($reviews as $review){ ?>
        <li>
			<?php if(!empty($_SESSION['user'])){ ?>
			<?php if($_SESSION['user']['user_type'] == 'A' || $_SESSION['user']['uid'] == $review['writer_uid']){ ?>
            <a title="삭제" class="delete" href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews/<?php echo $review['review_id'] ?>/delete" onclick="return confirm('삭제하시겠습니까?')"><span class="ir">삭제</span></a>
            <!-- 삭제 버튼은 작성자 와 관리자에게만 보임 -->
			<?php } ?>
			<?php } ?>
            <dl>
                <dt>
                    <span class="star<?php echo $review['rating'] ?>">
					<?php for($i=1;$i<=$review['rating'];$i++){ ?>
					★
					<?php } ?>
					</span><br />
                    <b><?php if(!empty($review['writer']['nickname'])) echo $review['writer']['nickname']; else echo $review['writer_name'] ?></b> &nbsp;&nbsp;l&nbsp;&nbsp; <?php echo date('Y.m.d H:i', strtotime($review['reg_at'])) ?>
                </dt>
                <dd>
                    <?php echo nl2br(htmlspecialchars($review['contents'])) ?>
                </dd>
            </dl>
        </li>
		<?php } ?>
    </ul>
	<?php } ?>

    <?php if($total > 0){ ?>
	<div class="paging">
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
		<?php if($page_start > $page_count){ ?><a href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
		<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
		<?php if($i > ceil($total / $count)) break; ?>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
		<?php } ?>
		<?php if($page_start + $page_count <= $total_page){ ?><a href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
		<a href="/strategies/<?php echo $strategy['strategy_id'] ?>/reviews?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
	</div>
	<?php } ?>
 </div>


      </div>
    </div>
  </div>
</body>
</html>