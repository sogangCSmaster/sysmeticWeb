                        <div class="photo">
                            <img src="<?=getProfileImg($pb['picture'])?>" alt="" style="width:123px;height:123px" />
                            <a href="/lounge/<?=$pb['uid']?>" class="btn_mypage"><img src="/images/sub/btn_go_mypage.gif" alt="" /></a>
                        </div>
                        <div class="info_nav">
                            <div class="info_box">
                                <div class="row top">
                                    <strong class="name"><?=$pb['name']?></strong>
                                    <span class="job pb">PB</span>
                                </div>
                                <div class="row bottom">
                                    <div class="grade"><img src="/images/sub/img_pb_grade<?=$pb['num']?>.gif" alt="<?=$pb['num']?>점" /></div>
                                    <div class="company"><?=$pb['company']?></div>
                                    <div class="address">
                                        <?=$pb['part']?> <?=$pb['sido2']?>
                                        <a href="http://map.daum.net/?q=<?=urlencode($pb['sido2']);?>" target="_blank" class="btn_location"><img src="/images/sub/btn_trader_location.png" alt=""></a>
                                    </div>
                                </div>
                            </div>
                            <nav class="menu">
                                <ul>
                                    <li class="menu01 <?=($menu == 'profile') ? 'curr' : ''?>" <?if($menu == 'profile'){?>style="background:rgba(0,0,0,.4);"<?}?>>
                                        <a href="/lounge/<?=$pb['uid']?>/profile">
                                            프로필
                                            <img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
                                        </a>
                                    </li>
                                    <li class="menu02 <?=($menu == 'strategies') ? 'curr' : ''?>" <?if($menu == 'strategies'){?>style="background:rgba(0,0,0,.4);"<?}?>>
                                        <a href="/lounge/<?=$pb['uid']?>/strategies">
                                            상품
                                            <img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
                                        </a>
                                    </li>
                                    <li class="menu03 <?=($menu == 'portfolios') ? 'curr' : ''?>" <?if($menu == 'portfolios'){?>style="background:rgba(0,0,0,.4);"<?}?>>
                                        <a href="/lounge/<?=$pb['uid']?>/portfolios">
                                            포트폴리오
                                            <img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
                                        </a>
                                    </li>
                                    <li class="menu04 <?=($menu == 'contents') ? 'curr' : ''?>" <?if($menu == 'contents'){?>style="background:rgba(0,0,0,.4);"<?}?>>
                                        <a href="/lounge/<?=$pb['uid']?>/contents">
                                            공지.칼럼
                                            <img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
                                        </a>
                                    </li>
                                    <li class="menu05 <?=($menu == 'appraise') ? 'curr' : ''?>" <?if($menu == 'appraise'){?>style="background:rgba(0,0,0,.4);"<?}?>>
                                        <a href="/lounge/<?=$pb['uid']?>/appraise">
                                            PB 평가
                                            <img src="/images/sub/img_lounge_nav_arrow.png" alt="" class="arrow" />
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                            <? if (!$subscribe_chk) { ?>
                            <a href="javascript:;" id="subscribe" class="btn_read_more">
                                <img src="/images/sub/ico_read_more.gif" alt="" />
                                <strong>구독하기 ( <span class="mark"><?=$subscribe_cnt?></span> )</strong>
                            </a>
                            <? } else { ?>
                            <a href="javascript:;" id="subscribe" class="btn_read_more">
                                <img src="/images/sub/ico_read_more.gif" alt="" />
                                <strong>구독취소 ( <span class="mark"><?=$subscribe_cnt?></span> )</strong>
                            </a>
                            <? } ?>
                        </div>

                        <script>
                        $('#subscribe').on('click', function() {
                            
                            <? if (!$subscribe_chk) { ?>

                                <? if ($isLoggedIn()) { ?>
                                $.ajax({
                                    mthod: 'post',
                                    data: {uid: '<?=$pb['uid']?>'},
                                    url: '/lounge/subscribe/reg',
                                    dataType: 'json',
                                }).done(function(data) {
                                    if (data.result) {
                                        alert("PB가 새글을 등록할 경우\n마이페이지 구독페이지에서 \n확인하실 수 있습니다.");
                                        location.reload();
                                    } else {
                                        alert(data.msg);
                                    }
                                });
                                <? } else { ?>
                                login();
                                <? } ?>
                            
                            <? } else { ?>
                                
                                if (confirm('구독을 취소하시겠습니까?')) {
                                    $.ajax({
                                        mthod: 'post',
                                        data: {uid: '<?=$pb['uid']?>'},
                                        url: '/lounge/subscribe/del',
                                        dataType: 'json',
                                    }).done(function(data) {
                                        if (data.result) {
                                            location.reload();
                                        } else {
                                            alert(data.msg);
                                        }
                                    });
                                }

                            <? } ?>
                        });

                        
                        </script>