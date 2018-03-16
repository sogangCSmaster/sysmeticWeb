<!doctype html>
<html lang="ko">
<head>
    <title>라운지 | SYSMETIC</title>
    <? include_once $skinDir."/common/head.php" ?>
    <script>
    $(function() {

        $('#regFrm').on('submit', function() {

            if(!confirm('저장하시겠습니까?')) {
                return false;
            }
        });
    });
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
            <section class="area pb_detail">
                <div class="area">
                    <div class="head">
                        <? include $skinDir."/lounge/pb_info.php" ?>
                    </div>
                    <div class="content profile manage">
                        <form id="regFrm" action="/lounge/<?=$pb['uid']?>/profile/write" method="post">
                        <input type="hidden" name="mode" value="<?=$mode?>" />
                            <fieldset>
                                <legend class="screen_out"></legend>
                                <dl class="first">
                                    <dt>경력</dt>
                                    <dd>
                                        <div class="textarea_box">
                                            <textarea id="career" name="career" placeholder="경력을 입력해주세요."><?=$profile['career']?></textarea>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>자격증</dt>
                                    <dd>
                                        <div class="textarea_box">
                                            <textarea id="license" name="license" placeholder="보유하신 자격증을 입력해주세요."><?=$profile['license']?></textarea>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>자기소개</dt>
                                    <dd>
                                        <div class="textarea_box">
                                            <textarea id="introduce" name="introduce" placeholder="자기소개를 입력해주세요."><?=$profile['introduce']?></textarea>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>기타</dt>
                                    <dd>
                                        <div class="textarea_box">
                                            <textarea id="etc" name="etc" placeholder="기타사항을 입력해주세요."><?=$profile['etc']?></textarea>
                                        </div>
                                    </dd>
                                </dl>
                                <div class="btn_area">
                                    <a href="/lounge/<?=$pb['uid']?>/profile" class="btn_common_gray btn_cancel">취소</a>
                                    <button type="submit" class="btn_common_red btn_next_step">완료</button>
                                </div>
                            </fieldset>
                        </form>
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

</body>
</html>
