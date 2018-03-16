<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
var isLoading = false;
var hasMore = true;
var start = <?php echo $start + $count ?>;
var count = <?php echo $count ?>;

function loadList(){
	$.getJSON('/portfolios', {format:'json', start:start, count:count}, function(data){
		var html = '';
		/*
		if(data.items.length > 0){
			$('.no_result').remove();
		}
		*/

		$.each(data.items, function(key, val){
			html += '<tr>';
				html += '<td><b>' + escapedHTML(val.name) + '</b><br /><i>생성일</i> <u>'+ val.str_create_at +'</u></td>';
				html += '<td class="data">' + comma(val.result_amount) + '</td>';
				html += '<td class="data">';
				if(val.total_profit_rate > 0){
				html += '<span class="plus">' + val.total_profit_rate + '%</span>';
				}else if(val.total_profit_rate < 0){
				html += '<span class="minus">' + val.total_profit_rate + '%</span>';
				}else{
				html += va.total_profit_rate + '%';
				}
				html += '</td>';
				html += '<td class="date">'+val.str_start_date+' ~<br />'+val.str_end_date+'</td>';
				html += '<td class="btn"><button type="button" title="상세보기" class="btn_view" onclick="location.href=\'/portfolios/' + val.portfolio_id + '\'"><span class="ir">상세보기</span></button></td>';
				html += '<td class="btn"><a href="/portfolios/' + val.portfolio_id + '/delete'" title="삭제" class="btn_view" onclick="return confirm('삭제하시겠습니까?');"><span class="ir">삭제</span></a></td>';

			html += '</tr>';
		});

		$('#portfolio_list tbody').append(html);

		start = start + count;
		isLoading = false;

		if(data.items.length < count) hasMore = false;
	});
}

	$(window).scroll(function(){
		if(isLoading) return;
		if(!hasMore) return;

		if(($(document).height() - $(window).height() * 3) <= ($(window).height() + $(window).scrollTop())){
			isLoading = true;
			loadList();
		}
	});

	$(function(){
		<?php if(!empty($flash['error'])){ ?>
		alert('<?php echo htmlspecialchars($flash['error']) ?>');
		<?php } ?>
	});
	</script>
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content">
			<?php if(count($portfolios) == 0){ ?>
            <div class="no_data">
                <p>포트폴리오가 없습니다.</p><br />
                여러가지 전략을 원하는 비율로 선택하여<br />나만의 포트폴리오를 만들 수 있습니다. <br />
                <button type="button" title="포트폴리오 만들기" class="portfolio_make" onclick="location.href='/portfolios/create'"><span class="ir">포트폴리오 만들기</span></button>
            </div>
			<?php }else{ ?>
            <table border="0" cellspacing="0" cellpadding="0" class="list_head">
                <thead>
                <tr>
                    <td style="width:280px;">이름</td>
                    <td style="width:200px;">금액</td>
                    <td style="width:158px;">누적 수익률</td>
                    <td style="width:120px;">기간</td>
                    <td style="width:80px;"></td>
                    <td></td>
                </tr>
                </thead>
            </table>

            <div class="portfolio_list" id="portfolio_list">
               <table border="0" cellspacing="0" cellpadding="0">
               <col width="280" /><col width="200" /><col width="158" /><col width="120" /><col width="80" /><col width="*" />
               <tbody>
					<?php foreach($portfolios as $portfolio){ ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($portfolio['name']) ?></b><br /><i>생성일</i> <u><?php echo date('Y/m/d', strtotime($portfolio['reg_at'])) ?></u></td>
                        <td class="data"><span class="<?php echo getSignClass($portfolio['result_amount'], 'false') ?>"><?php echo number_format($portfolio['result_amount']) ?></span></td>
                        <td class="data"><span class="<?php echo getSignClass($portfolio['total_profit_rate'], 'false') ?>"><?php echo number_format($portfolio['total_profit_rate'],2,'.','') ?>%</span></td>
                        <td class="date"><?php echo substr($portfolio['start_date'], 0, 4).'.'.substr($portfolio['start_date'], 4, 2).'.'.substr($portfolio['start_date'], 6, 2) ?> ~<br /><?php echo substr($portfolio['end_date'], 0, 4).'.'.substr($portfolio['end_date'], 4, 2).'.'.substr($portfolio['end_date'], 6, 2) ?></td>
                        <td class="btn"><a title="상세보기" class="btn_view" href="/portfolios/<?php echo $portfolio['portfolio_id'] ?>"><span class="ir">상세보기</span></a></td>
			<td class="btn"><a href="/portfolios/<?php echo $portfolio['portfolio_id'] ?>/delete" title="삭제" class="btn_view" onclick="return confirm('삭제하시겠습니까?');"><span class="ir">삭제</span></a></td>
                    </tr>
					<?php } ?>
               </tbody>
               </table>
            </div>

            <div class="btn_portfolio">
                <button type="button" title="포트폴리오 추가" class="portfolio_make" onclick="location.href='/portfolios/create'"><span class="ir">포트폴리오 추가</span></button>
            </div>
			<?php } ?>

            <!-- 스크롤 하면 더보기 불러옴 -->

	    </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>
