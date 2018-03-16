<?
if (count($developers)) {
    $idx = 0;
    foreach ($developers as $developer) {
        $idx++;
        $li_class = ($idx%2===0) ? 'right' : '';
        $v = array_map('htmlspecialchars', $developer);
        $picture = getProfileImg($v['picture']);
    ?>
    <li class="<?=$li_class?>">
        <? if ($v['user_type'] == 'P') { ?>
        <div class="photo"><a href="/lounge/<?=$v['uid']?>"><img src="<?=$picture?>" alt="<?=$v['nickname']?>" /></a></div>
        <? } else { ?>
        <div class="photo"><img src="<?=$picture?>" alt="<?=$v['nickname']?>" /></div>
        <? } ?>
        <div class="info">
            <div class="user_info">
                <? if ($v['user_type'] == 'P') { ?>
                <strong class="name"><a href="/lounge/<?=$v['uid']?>"><?=($v['user_type'] == 'P') ? $v['name'] : $v['nickname']?></a></strong>
                <a href="/lounge/<?=$v['uid']?>" class="btn_coffee"><img src="/images/sub/ico_list_coffee.gif" alt="" /></a>
                <? } else { ?>
                <strong class="name"><?=($v['nickname'])?></strong>
                <? } ?>
                <span class="job <?=($v['user_type'] == 'T') ? 'trader' : 'pb'; ?>"><?=($v['user_type'] == 'T') ? '트레이더' : 'PB'; ?></span>
            </div>
            <div class="company_info">
                <p>
                    <? if ($v['user_type'] == 'T') { ?>
                    <span class="address"><?=($v['sido'])?> <?=($v['gugun'])?></span>
                    <?
					} else {
						$sido2 = explode(' ', $v['sido2']);
					?>
                    <a href="/investment/developers?keyword=<?=($v['company'])?>"><?php if(!empty($v['logo_s'])){ ?><!--img src="<?php echo $v['logo_s'] ?>" /--><?php } ?>
                    <strong class="company"><?=($v['company'])?></strong></a>
                    <span class="address"><?=($v['part'])?> <?=$sido2[0]?> <?=$sido2[1]?></span>
                    <? } ?>
                </p>
            </div>
            <div class="job_info">
                <p>
                    <a href="/investment/strategies?developer_uid=<?=$v['uid']?>">상품 <span class="cnt"><?=number_format($v['strategy_cnt'])?></span> 개</a>, <a href="/investment/portfolios?developer_uid=<?=$v['uid']?>">포트폴리오 <span class="cnt"><?=number_format($v['portfolio_cnt'])?></span> 개</a>
                </p>
                <a href="/investment/strategies?developer_uid=<?=$v['uid']?>" class="btn_list_view"><img src="../images/sub/btn_strategy_list_view.gif" alt="전략목록보기" /></a>
            </div>
        </div>
    </li>
<?
    }
}  
?>