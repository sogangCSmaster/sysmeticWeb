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
		$('#accounts_form').submit(function(){
			if($('#accounts_form input[type=checkbox]:checked').length == 0){
				alert('선택된 항목이 없습니다');
				return false;
			}

			var result = confirm('삭제하시겠습니까?');
			if(!result) return false;

			return true;
		});
	});

	function openImage(url){
		$('#show_img').attr('src', url);
		showLayer('preview');
	}
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="admin_strategy">전략 상세보기</h3>

            <h4 class="admin"><?php echo htmlspecialchars($strategy['name']) ?></h4>
            <span class="write_date"><b>등록일 :</b> <?php echo date('Y.m.d H:i:s', strtotime($strategy['reg_at'])) ?></span>

            <!-- 실계좌 정보 -->
            <div id="strategy_view2" name="strategy_view" class="strategy_view">

                <div class="tab">
                    <a title="기본정보" class="<?php if($current_tab_menu == 'basic') echo 'tab_on'; else echo 'tab_off' ?>" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>"><span class="ir">기본정보</span></a>
                    <a title="일간분석" class="<?php if($current_tab_menu == 'daily') echo 'tab_on'; else echo 'tab_off' ?>" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/daily"><span class="ir">일간분석</span></a>
                    <a title="펀딩금액/투자자 수" class="<?php if($current_tab_menu == 'funding') echo 'tab_on'; else echo 'tab_off' ?> long" href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/funding"><span class="ir">펀딩금액/투자자 수</span></a>
                    <a title="실계좌 정보" class="<?php if($current_tab_menu == 'accounts') echo 'tab_on'; else echo 'tab_last' ?>"  href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts"><span class="ir">실계좌 정보</span></a>
                </div>
                
                <button type="button" onclick="showLayer('daily_write3');" title="실계좌인증 등록" class="write btn_admin"><span class="ir">실계좌인증 등록</span></button>

				<?php if(count($daily_values) > 0){ ?>
				<form action="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts/delete" method="post" id="accounts_form">
                <ul class="certification">
                	<?php foreach($daily_values as $v){ ?>
                    <li>
                        <dl>
                            <dd>
                                 <p>
                                    <img src="<?php echo $v['image'] ?>" onclick="openImage(this.src)" />
                                    <input type="checkbox" name="account_ids[]" id="choice<?php echo $v['account_id'] ?>" value="<?php echo $v['account_id'] ?>" /><label for="choice<?php echo $v['account_id'] ?>"></label>
                                </p>
                            </dd>
                            <dt><?php echo $v['title'] ?></dt>
                        </dl>
                    </li> 
                    <?php } ?>                                      
                </ul>
            

                <fieldset class="admin">
                    선택한 이미지를 &nbsp;
                    <!-- <button type="button" title="수정" class="admin1" onclick=""><span class="ir">수정</span></button> &nbsp;OR&nbsp; -->
                    <button type="submit" title="삭제" class="admin1"><span class="ir">삭제</span></button>
                </fieldset>
                </form>
				<?php } ?>

                <?php if($total > 0){ ?>
            	<div class="paging">
					<a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=1"<?php if($page == 1) echo ' class="first_no"' ?>>first</a>
                	<?php if($page_start > $page_count){ ?><a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=<?php echo $page_start-1 ?>">prev</a><?php } ?><!-- class="prev_no" -->
					<?php for($i = $page_start;$i<=$page_start + $page_count - 1;$i++){ ?>
					<?php if($i > ceil($total / $count)) break; ?>
                	<a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=<?php echo $i ?>"<?php if($page == $i) echo ' class="this"' ?>><?php echo $i ?></a>
					<?php } ?>
					<?php if($page_start + $page_count <= $total_page){ ?><a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=<?php echo $page_start + $page_count ?>" class="next">next</a><?php } ?>
                	<a href="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts?page=<?php echo $total_page ?>" class="last<?php if($total_page == $page) echo '_no' ?>">last</a>
            	</div>
				<?php } ?>
            </div>

			<!--
            <p class="btn_board">
                <button id="" type="" onclick="location.href='strategy';" title="목록" class="cancel"><span class="ir">목록</span></button>
            </p>
            -->
        </div>        
    </div>
    <!------ //본문 영역 ------->  
        
    <!-- 실계좌 인증 등록 레이어 -->
    <div id="daily_write3" class="layer" style="width:540px;">
        <div class="layer_head">
            <p class="write3">실계좌 인증 등록</p>
            <!-- <p class="edit3">실계좌 인증 등록</p>-->
            <span class="layer_close" onclick="closeLayer('daily_write3');">X</span>
        </div>

		<form action="/admin/strategies/<?php echo $strategy['strategy_id'] ?>/accounts/add" method="post" enctype="multipart/form-data">
        <div class="layer_form">
            <table border="0" cellspacing="1" cellpadding="0" class="admin_table">
                <col width="220" /><col width="*" />
                <thead>
                <tr>
					<!--<td>순번</td>-->
                    <td>제목</td>
                    <td>이미지</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <!--<td><input name="target_date[]" type="text" title="순번" value="" style="width:20px;"  /></td>-->
					<td><input name="title[]" type="text" title="제목" value="" style="width:170px;"  /></td>
                    <td>                
						<input id="img1" name="account_img_fake" type="text"  title="이미지" class="file_input_textbox" style="width:180px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file1" name="account_img[]" type="file" title="이미지" class="file_input_hidden" style="width:180px;" onchange="document.getElementById('img1').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr>
                    <!-- <td><input name="target_date[]" type="text" title="순번" value="" style="width:20px;"  /></td>-->
					<td><input name="title[]" type="text" title="제목" value="" style="width:170px;"  /></td>
                    <td>               
						<input id="img2" name="account_img_fake" type="text"  title="이미지" class="file_input_textbox" style="width:180px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file2" name="account_img[]" type="file" title="이미지" class="file_input_hidden" style="width:180px;" onchange="document.getElementById('img2').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr>
                    <!-- <td><input name="target_date[]" type="text" title="순번" value="" style="width:20px;"  /></td> -->
					<td><input name="title[]" type="text" title="제목" value="" style="width:170px;"  /></td>
                    <td>                 
						<input id="img3" name="account_img_fake" type="text"  title="이미지" class="file_input_textbox" style="width:180px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file3" name="account_img[]" type="file" title="이미지" class="file_input_hidden" style="width:180px;" onchange="document.getElementById('img3').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr>
                    <!-- <td><input name="target_date[]" type="text" title="순번" value="" style="width:20px;"  /></td> -->
					<td><input name="title[]" type="text" title="제목" value="" style="width:170px;"  /></td>
                    <td>            
						<input id="img4" name="account_img_fake" type="text"  title="이미지" class="file_input_textbox" style="width:180px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file4" name="account_img[]" type="file" title="이미지" class="file_input_hidden" style="width:180px;" onchange="document.getElementById('img4').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr>
                    <!-- <td><input name="target_date[]" type="text" title="순번" value="" style="width:20px;"  /></td> -->
					<td><input name="title[]" type="text" title="제목" value="" style="width:170px;"  /></td>
                    <td>                
						<input id="img5" name="account_img_fake" type="text"  title="이미지" class="file_input_textbox" style="width:180px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file5" name="account_img[]" type="file" title="이미지" class="file_input_hidden" style="width:180px;" onchange="document.getElementById('img5').value = this.value" />
						</div>
                    </td>
                </tr>
                </tbody>
            </table>

            <p class="btn_area">
                <button type="submit" title="입력" class="submit"><span class="ir">입력</span></button>
                <button type="reset" title="취소"onclick="closeLayer('daily_write3');" class="cancel"><span class="ir">취소</span></button>
            </p>
        </div>
        </form>
    </div>
    <!-- //펀딩금액/투자자수 수정 레이어 -->

    <!-- 이미지 보기 레이어 -->
    <div id="preview" class="layer" style="width:600px;display:none;">
        <div class="layer_head">
            <span class="layer_close" onclick="closeLayer('preview');">X</span>
        </div>

        <div class="layer_photo">
            <img src="" id="show_img" />
        </div>
    </div>

	<?php require_once('footer.php') ?>

</body>
</html>
