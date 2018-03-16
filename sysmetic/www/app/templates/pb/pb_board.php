<!doctype html>
<html lang="ko">
<head>
    <title>PB게시판</title>
    <? include_once $skinDir."/common/head.php" ?>
    <script>
    function more(page) {
        var field = $('#field').val();
        var keyword = $('#keyword').val();
        if ($.trim(field)) {
            field = '&field='+field;
        }
        if ($.trim(keyword)) {
            keyword = '&keyword='+keyword;
        }

        location.href='/pb/bbs?page='+page+field+keyword;
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
			<section class="area pb_only">	
				<div class="pb_board_head">
					<h2 class="tit n_squere">PB 게시판</h2>
					<p class="summary">
						PB 전용 게시판에서는 각 소속 증권사의 컴플라이언스 규정을 반드시 준수해야하며<br />
						내부자 정보나 주식종목에 대한 민감한 사안(허위정보 유포 등)을 다루는 것을 금지하고 있습니다.		
					</p>
					<a href="/pb/bbs/write" class="btn_write">글쓰기</a>
				</div>
				<div class="content">
					<div class="list_search">
                        <form method="get" action="/pb/bbs">
						<div class="custom_selectbox" style="width:188px;">
							<label for="field">제목</label>
							<select id="field" name="field">
								<option value="subject" <?=($field == 'subject') ? 'selected' : ''?>>제목</option>
								<option value="name" <?=($field == 'name') ? 'selected' : ''?>>작성자</option>
								<option value="company" <?=($field == 'company') ? 'selected' : ''?>>증권사</option>
								<option value="contents" <?=($field == 'contents') ? 'selected' : ''?>>내용</option>
							</select>
						</div>
						<div class="input_box">
							<input type="text" id="keyword" name="keyword" value="<?=$keyword?>" placeholder="검색어를 입력해주세요.">
						</div>
						<button type="submit" class="btn_search">검색하기</button>
                        </form>
					</div>
					<div class="list_wrap">
						<table class="list_tbl">
							<colgroup>
								<col style="width:48px;">
								<col style="width:563px;">
								<col style="width:263px;">
								<col style="width:118px;">
							</colgroup>
							<thead>
								<tr>
									<th style="border-right:none;">&nbsp;</th>
									<th style="border-left:none;">제목</th>
									<th>작성자</th>
									<th>작성일</th>
								</tr>
							</thead>
							<tbody>
                            <?
                            $idx = 0;
                            foreach ($lists as $v) {
                                $company = ($v['user_type'] == 'A') ? '관리자' : $v['company'];
                            ?>
                            <tr>
                                <td><?=$v['bid']?></td>
                                <td class="left">
                                    <a href="/pb/bbs/<?=$v['bid']?>">
                                        <p class="subject">
											<?=$v['subject']?> <? if ($v['reply_cnt']) {?><strong class="red reply_cnt">[<?=$v['reply_cnt']?>]</strong><? } ?>
											<?
											if($v['filecnt'])echo " <img src='/images/sub/ico_folder.png'>";
											?>
										</p>
                                    </a>
                                </td>
                                <td class="small">
                                    <img src="<?=getProfileImg($v['picture'])?>" alt="" class="img_photo" />
                                    <span class="name">[<?=$company?>] <?=$v['name']?></span>
                                    <? if ($v['user_type'] == 'P') {?>
                                    <a href="/lounge/<?=$v['uid']?>" class="btn_lounge"><img src="/images/sub/btn_lounge_coffee.gif" alt="라운지" /></a>
                                    <? } ?>
                                </td>
                                <td class="small"><?=substr($v['reg_date'], 0, 10)?></td>
                            </tr>
                            <? } ?>
							</tbody>
						</table>

                        <?=$paging?>
						<!--nav class="page_nate">
							<a href="javascript:;" class="btn_page first">처음으로</a>
							<a href="javascript:;" class="btn_page prev">이전</a>
							<a href="javascript:;" class="direct curr">1</a>
							<a href="javascript:;" class="direct">2</a>
							<a href="javascript:;" class="direct">3</a>
							<a href="javascript:;" class="direct">4</a>
							<a href="javascript:;" class="direct">5</a>
							<a href="javascript:;" class="direct">6</a>
							<a href="javascript:;" class="btn_page next">다음</a>
							<a href="javascript:;" class="btn_page last">마지막</a>
						</nav-->

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

<script>

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
