<!doctype html>
<html lang="ko">
<head>
    <title>라운지 | SYSMETIC</title>
    <? include_once $skinDir."/common/head.php" ?>
    <script>
    $(function() {
        getContent('thumb', 1);
    });

    function getContent(type, page) {
        $.ajax({
            mthod: 'get',
            data: {type: type, page: page},
            url: '/lounge/<?=$pb['uid']?>/load_contents',
            dataType: 'html',
        }).done(function(html) {
            $('.article_list').append(html);
        });
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
            <section class="area pb_detail">
                <div class="area">
                    <div class="head">
                        <? include $skinDir."/lounge/pb_info.php" ?>
                    </div>
                    <div class="content board">
                        <div class="head">
                            <strong class="cnt">공지·칼럼 <span class="mark"><?=number_format($total_contents)?></span>개</strong>
                            <? if ($mine) { ?>
                            <a href="/lounge/<?=$pb['uid']?>/contents/write" class="btn_portfolio_manage">글쓰기</a>
                            <? } ?>
                        </div>
                        <div class="article_list">
                        </div>
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
