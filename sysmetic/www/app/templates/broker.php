<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 브로커</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
	<script>
	$(function(){
		$('#ask_form').submit(function(){
			if(!$('#ask_body').val()){
				alert('내용을 입력해주세요.');
				return false;
			}

			$.post($(this).attr('action'), $(this).serialize(), function(data){
				if(data.result){
					$('#ask_body').val('');
					alert('문의내용이 접수되었습니다');
					closeLayer('ask');
				}
			}, 'json');
			return false;
		});
	});

	function showAskBroker(broker_id, name){
		$('#broker_name').html(name);
		$('#broker_id').val(broker_id);
		showLayer('ask');
	}
	</script>
</head>

<body>
    
	<?php require_once('header.php') ?>

    <!------ //헤더 영역 ------->
    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content">
            <ul class="broker_list">
				<?php foreach($brokers as $broker){ ?>                
                <li id="broker<?php echo htmlspecialchars($broker['broker_id']) ?>">
                    <a name="broker<?php echo htmlspecialchars($broker['broker_id']-1) ?>"></a>
                    <table border="0" cellspacing="1" cellpadding="0">
                    <col width="*" /><col width="160" /><col width="180" /><col width="89" /><col width="180" />
                        <tbody>
                        <tr>
                            <td rowspan="5" class="logo">
								<?php if(!empty($broker['logo'])){ ?>
                                <img src="<?php echo htmlspecialchars($broker['logo']) ?>" alt="<?php echo htmlspecialchars($broker['company']) ?>"  />
								<?php }else{ ?>
								<?php echo htmlspecialchars($broker['company']) ?>
								<?php } ?>

                                <div class="button">
								<?php if($isLoggedIn()){ ?>
								<?php if($_SESSION['user']['user_type'] != 'N'){ ?>
								<?php }else{ ?>
                                <button type="button" title="브로커에게 문의하기" onclick="showAskBroker(<?php echo htmlspecialchars($broker['broker_id']) ?>, '<?php echo htmlspecialchars($broker['company']) ?>')"><span class="ir">브로커에게 문의하기</span></button>
                               <?php } ?>
								<?php }else{ ?>
								<button type="button" title="브로커에게 문의하기" onclick="showLayer('login_layer');return false;"><span class="ir">브로커에게 문의하기</span></button>
								<?php } ?>
                                </div>
                            </td>
                            <td class="thead">URL</td>
                            <td colspan="3"><?php if(!empty($broker['url'])){ ?><a href="<?php echo htmlspecialchars($broker['url']) ?>" target="_blank"><?php echo htmlspecialchars($broker['url']) ?></a><?php } ?></td>
                        </tr>
                        <tr>
                            <td class="thead">국내상품</td>
                            <td colspan="3"><?php if(empty($broker['domestic'])) echo '-'; else echo htmlspecialchars($broker['domestic']) ?></td>
                        </tr>
                        <tr>
                            <td class="thead">해외상품</td>
                            <td colspan="3"><?php if(empty($broker['overseas'])) echo '-'; else echo htmlspecialchars($broker['overseas']) ?></td>
                        </tr>
                        <tr>
                            <td class="thead">F/X</td>
                            <td><?php if(empty($broker['fx'])) echo '-'; else echo htmlspecialchars($broker['fx']) ?></td>
                            <td class="thead">DMA</td>
                            <td><?php if(empty($broker['dma'])) echo 'O'; else echo htmlspecialchars($broker['dma']) ?></td>
                        </tr>
                        <tr>
                            <td class="thead">시스템 트레이딩</td>
                            <td>
							<?php
							$v_array = array();
							foreach($broker['system_trading_tools'] as $k => $v){
								if(!empty($v['logo'])) echo '<div><img src="'.htmlspecialchars($v['logo']).'" alt="'.htmlspecialchars($v['name']).'" /></div>';
								else $v_array[] = htmlspecialchars($v['name']);
							}
							if(count($v_array)) echo '<div>'.implode(', ', $v_array).'</div>';
							?>
							</td>
                            <td class="thead">API</td>
                            <td>
							<?php
							$v_array = array();
							foreach($broker['api_tools'] as $k => $v){
								if(!empty($v['logo'])) echo '<img src="'.htmlspecialchars($v['logo']).'" alt="'.htmlspecialchars($v['name']).'" />';
								else $v_array[] = htmlspecialchars($v['name']);
							}
							if(count($v_array)) echo implode(', ', $v_array);
							?>
							</td>
                        </tr>
                        </tbody>
                    </table>
                </li>
				<?php } ?>
            </ul>
        </div>
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

<div id="ask" class="layer" style="top:1000px; left:20px; display:none;">
    <div class="layer_head">
        <p class="ask">문의하기</p>
        <span class="layer_close" onclick="closeLayer('ask');">X</span>
    </div>
    
    <div class="ask_form">  
        <p>브로커 : <span id="broker_name"></span></p>
		<form action="/brokers/ask" method="post" id="ask_form">
        <fieldset>
            <legend>문의하기</legend>
            <textarea name="ask_body" id="ask_body" required="required"></textarea>
            <p class="btn_layer">
				<input type="hidden" name="broker_id" id="broker_id" value="">
                <button type="submit" title="문의하기" class="submit"><span class="ir">문의하기</span></button>
                <button type="reset" title="닫기" class="cancel" onclick="closeLayer('ask');"><span class="ir">닫기</span></button>
            </p>
        </fieldset>
		</form>
    </div>
</div>

</body>
</html>