            <nav class="snb">
                <div class="snb_area">
                    <h2 class="page_title n_squere">투자하기</h2>
                    <ul>
                        <li class="strategy_ranking <?=($submenu=='strategy') ? 'curr' : ''?>"><a href="/investment/strategies"><img src="/images/sub/txt_snb0101_<?=($submenu=='strategy') ? 'on' : 'off'?>.png" alt="전략랭킹" /></a></li>
                        <li class="pb_trader <?=($submenu=='developers') ? 'curr' : ''?>"><a href="/investment/developers"><img src="/images/sub/txt_snb0102_<?=($submenu=='developers') ? 'on' : 'off'?>.png" alt="PB/트레이더" /></a></li>
                        <li class="product_search <?=($submenu=='search') ? 'curr' : ''?>"><a href="/investment/search"><img src="/images/sub/txt_snb0103_off.png" alt="상품검색" /></a></li>
                        <li class="strategy_portfolio <?=($submenu=='portfolio') ? 'curr' : ''?>"><a href="/investment/portfolios"><img src="/images/sub/txt_snb0104_off.png" alt="전략 포트폴리오" /></a></li>
                    </ul>
                </div>
            </nav>
