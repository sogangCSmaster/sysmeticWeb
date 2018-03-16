<!doctype html>
<html lang="ko">
<head>
	<title>파트너</title>
	<? require_once "common/head.php" ?>
</head>
<body>
	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<? require_once "common/header.php" ?>
		<!-- header -->

		<!-- container -->
		<div class="container">

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
		<!-- //container -->

        <!-- footer -->
		<? require_once "common/footer.php" ?>
        <!-- // footer -->

	</div>
	<!-- //wrapper -->

</body>
</html> 