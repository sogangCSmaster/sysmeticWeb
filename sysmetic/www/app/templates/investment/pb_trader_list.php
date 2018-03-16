<!doctype html>
<html lang="ko">
<head>
    <title>PB/트레이더 | SYSMETIC</title>
    <? require_once $skinDir."common/head.php" ?>

    <script>
    $(function() {
        var getContent = function(page) {
            $('#page').val(page);
            $.ajax({
                mthod: 'get',
                data: $('#searchFrm').serialize(),
                url: '/developers/list',
                dataType: 'html',
            }).done(function(html) {
                $('.list_body ul').append(html);
                if (html == "") {
                    $('.btn_list_more').hide();
                }
            });
        }

        getContent(1);

        $('.btn_list_more').on('click', function() {
            page = parseInt($('#page').val()) + 1;
            getContent(page);
        });
    });

    var chk = true;
    function movePage(page) {
        chk = false;
        $('#page').val(page);
        $('#searchFrm').submit();
    }

    function searchType(type) {
        $('#type').val(type);
        $('#searchFrm').submit();
    }

    function search() {
        if (chk == true) {
            $('#page').val(1);
        }
        chk = true;
        return;
    }
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

            <? require_once $skinDir."investment/sub_menu.php" ?>

            <section class="area in_snb pb_trader">
                <div class="page_header">
                <form id="searchFrm" action="/investment/developers" method="get" onsubmit="return search();">
                <input type="hidden" id="page" name="page" value="<?=$page?>" />
                <input type="hidden" id="type" name="type" value="<?=$type?>" />
                <input type="hidden" id="type2" name="type2" value="<?=$type2?>" />
                    <nav class="category">
                        <ul>
                            <li class="<?=(!$type) ? 'curr' : '';?>"><a href="javascript:;searchType('');">전체</a></li>
                            <li class="<?=($type=='P') ? 'curr' : '';?>""><a href="javascript:;searchType('P');">PB</a></li>
                            <li class="<?=($type=='T') ? 'curr' : '';?>""><a href="javascript:;searchType('T');">트레이더</a></li>
                        </ul>
                    </nav>
                    <div class="search_box">
                        <input type="text" id="keyword" name="keyword" value="<?=$keyword?>" placeholder="검색어를 입력해주세요." />
                        <button type="submit" class="btn_search" title="검색"></button>
                    </div>
                </form>
                </div>
                <div class="list_wrap">
                    <? if ($keyword) { ?>
                    <div class="list_info result">
                        <p><strong class="keyword">"<?=$keyword?>"</strong> 이름의 검색결과<strong class="cnt"><?=number_format($total['P'])?></strong><span>명의 PB</span>와 <strong class="cnt"><?=number_format($total['T'])?></strong><span>명의 트레이더</span>가 있습니다.</p>
                    </div>
                    <? } else { ?>
                    <div class="list_info total">
                        <p><strong class="cnt"><?=number_format($total['P'])?></strong><span>명의 PB</span>와 <strong class="cnt"><?=number_format($total['T'])?></strong><span>명의 트레이더</span>가 함께 하고 있습니다.</p>
                    </div>
                    <? } ?>

                    <div class="list_body">
                        <ul>
                        </ul>
                    </div>
                    <a href="javascript:;" class="btn_list_more">+ 더보기</a>
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
