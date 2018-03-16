<div class="real_info">
<? if (count($monthly_values) > 0) { ?>
<ul>
    <?
    $cnt = 0;
    foreach ($monthly_values as $v) {
    ?>
    <li <?=($cnt%5==0) ? 'class="left"' : '';?>>
        <a href="javascript:;">
            <div class="photo">
                <img src="<?=$v['image'] ?>" alt="" onclick="openImage(this.src);"  />
            </div>
            <p class="summary">
                <?=$v['title'] ?>
            </p>
        </a>
    </li>
    <?
        $cnt++;
     } ?>
</ul>
<? } ?>

<? if ($total > 0) { ?>
<nav class="page_nate">

    <a href="javascript:;movePage(1);" class="btn_page first">first</a>
    <? if ($page_start > $page_count) { ?>
    <a href="javascript:;movePage(<?=$page_start-1 ?>);" class="btn_page prev">prev</a>
    <? } ?>
    <? for ($i = $page_start;$i<=$page_start + $page_count - 1;$i++) { ?>
        <? if ($i > ceil($total / $count)) break; ?>
        <a href="javascript:;movePage(<?=$i?>);" class="direct <?=($page == $i) ? "curr" : ""?>"><?=$i?></a>
    <? } ?>
    <? if ($page_start + $page_count <= $total_page) { ?>
        <a href="javascript:;movePage(<?=($page_start + $page_count)?>);" class="btn_page next">next</a>
    <? } ?>
    <a href="javascript:;movePage(<?=($total_page)?>);" class="btn_page last">last</a>

</nav>
<? } ?>
</div>

<script>
function movePage(page) {
    $('.details .cont_area #accounts').load('/strategies/<?=$strategy['strategy_id']?>/accounts?page='+page);
}
</script>

