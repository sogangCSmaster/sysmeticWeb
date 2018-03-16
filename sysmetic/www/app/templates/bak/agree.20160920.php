<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 약관동의</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="join">회원가입</h3>
            
			<form action="/agree_ok" method="post">
            <p class="sub_title rules1">이용약관 동의</p>
            <div id="box" class="agree_box">
                <div class="view_text">
                    <?php require_once('rules_text.php') ?>
                </div>
                <p>
                    <input type="checkbox" name="agree1" id="agree1" /><label for="agree1">위 이용약관에 동의합니다.</label>
                </p>
                
            </div>

            <p class="sub_title rules2">개인정보취급방침 동의</p>
            <div id="box" class="agree_box">
                <div class="view_text">
                    <?php require_once('terms_text.php') ?>
                </div>
                <p>
                    <input type="checkbox" name="agree2" id="agree2" /><label for="agree2">위 개인정보취급방침에 동의합니다.</label>
                </p>
                
            </div>


            <p class="btn_board">
				<input type="hidden" name="platform" value="<?php echo htmlspecialchars($platform) ?>" />
                <button type="submit" title="회원가입" class="submit"><span class="ir">회원가입</span></button>
                <button type="cancel" title="취소" class="cancel"><span class="ir">취소</span></button>
            </p>
			</form>

        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>
</body>
</html>