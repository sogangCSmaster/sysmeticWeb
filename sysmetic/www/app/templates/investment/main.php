<!doctype html>
<html lang="ko">
<head>
    <title>투자하기 | SYSMETIC</title>
    <? require_once $skinDir."common/head.php" ?>
</head>
<body>
    <!-- wrapper -->
    <div class="wrapper">

        <!-- header -->
        <? require_once $skinDir."common/header.php" ?>
        <!-- header -->

        <!-- container -->
        <div class="container">

            <? require_once $skinDir."investment/sub_menu.php" ?>

            <section class="area in_snb investment_main">
                <div class="box left">
                    <p class="title n_squere">인기있는 상품을 골라 투자하거나,</p>
                    <p class="summary"><img src="/images/sub/txt_investment_main01.gif" alt="PB 또는 트레이더를 통해 원하는 상품에 투자해보세요." /></p>
                    <a href="/investment/strategies" class="btn top">상품랭킹 보기</a>
                    <a href="/investment/developers" class="btn">PB/트레이더 보기</a>
                </div>
                <div class="box center">
                    <p class="title n_squere">상세 검색 기능을 이용하여</p>
                    <p class="summary"><img src="/images/sub/txt_investment_main02.gif" alt="원하는 전략을 직접 찾아본 뒤
                    투자할 수 있습니다." /></p>
                    <a href="/investment/search" class="btn_search">상품 검색</a>
                </div>
                <div class="box right">
                    <p class="title n_squere">시스메틱에 등록된 다양한 전략으로</p>
                    <p class="summary"><img src="/images/sub/txt_investment_main03.gif" alt="작성된 포트폴리오를 확인해보세요. 직접 포트폴리오를 만들 수도 있습니다." /></p>
                    <a href="/investment/portfolios" class="btn top">포트폴리오 랭킹 보기</a>
                    <a href="/investment/portfolios/write" class="btn">나의 포트폴리오 만들기</a>
                </div>
            </section>
        </div>
        <!-- //container -->

        <!-- footer -->
        <? require_once $skinDir."common/footer.php" ?>
        <!-- // footer -->

    </div>
    <!-- //wrapper -->

</body>
</html>
