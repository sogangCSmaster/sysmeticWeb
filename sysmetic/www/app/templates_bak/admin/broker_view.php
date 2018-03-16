<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders - 브로커 관리</title>	
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
    <link  rel="stylesheet" type="text/css" href="/css/sysmetic.css" />	
    <script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
    <script>
	$(function(){
		$('#broker_form').submit(function(){
			if(!$('#company').val()){
				alert('회사명을 입력해주세요');
				$('#company').focus();
				return false;
			}
			
			return true;
		});
	});
	
	function addSTool(){
		showLayer('add_s_tool');
	}
	
	function editSTool(tool_id, name, logo){
		$('#edit_s_tool_id').val(tool_id);
		$('#system_trading_name1').val(name);
		if(logo){
			$('#system_trading_logo1').attr('src', logo);
			$('#system_trading_logo1').show();
		}else{
			$('#system_trading_logo1').hide();
		}
		showLayer('edit_s_tool');
	}
	
	function deleteSTool(broker_id, tool_id){
		var result = confirm('삭제하시겠습니까?');
		if(result){
			location.href='/admin/brokers/delete_tool?broker_id='+broker_id+'&tool_type=s&tool_id=' + tool_id;
		}else{
			return false;
		}
	}
	
	function addATool(){
		showLayer('add_a_tool');
	}
	
	function editATool(tool_id, name, logo){
		$('#edit_a_tool_id').val(tool_id);
		$('#api_name1').val(name);

		if(logo){
			$('#api_logo1').attr('src', logo);
			$('#api_logo1').show();
		}else{
			$('#api_logo1').hide();
		}
		showLayer('edit_a_tool');
	}
	
	function deleteATool(broker_id, tool_id){
		var result = confirm('삭제하시겠습니까?');
		if(result){
			location.href='/admin/brokers/delete_tool?broker_id='+broker_id+'&tool_type=a&tool_id=' + tool_id;
		}else{
			return false;
		}
	}
	
	<?php if(!empty($flash['error'])){ ?>
	alert('<?php echo htmlspecialchars($flash['error']) ?>');
	<?php } ?>
	</script>
</head>

<body>

	<?php require_once('header.php') ?>

    <!------ 본문 영역 ------->
    <div id="wrap">
        <div id="content" class="view">
            <h3 class="admin_broker">브로커 관리</h3>
            
            <form action="/admin/brokers/<?php echo $broker['broker_id'] ?>/edit" method="post" enctype="multipart/form-data" id="broker_form">
            <table border="0" cellspacing="0" cellpadding="0" class="admin_write">
            <col width="160" /> <col width="*" /><col width="160" /> <col width="180" />
            <tbody>
                <tr>
                    <td class="thead">회사명</td>
                    <td colspan="3">
                        <input id="company" name="company" type="text" title="회사명" required="required" value="<?php echo htmlspecialchars($broker['company']) ?>"  />
                    </td>
                </tr>
                <tr>
                    <td class="thead">로고이미지</td>
                    <td colspan="3">
                        <?php if(!empty($broker['logo'])){ ?><img src="<?php echo $broker['logo'] ?>" class="broker_logo" /><?php } ?><br />
						<input id="img_logo" name="img_logo" type="text"  title="로고이미지" class="file_input_textbox" style="width:400px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_logo" name="logo" type="file" title="로고이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_logo').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr>
                    <td class="thead">로고이미지_S</td>
                    <td colspan="3">
                        <?php if(!empty($broker['logo_s'])){ ?><img src="<?php echo $broker['logo_s'] ?>" class="broker_logo2" /><?php } ?><br />
						<input id="img_logo_s" name="img_logo_s" type="text"  title="로고이미지" class="file_input_textbox" style="width:400px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_logo_s" name="logo_s" type="file" title="로고이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_logo_s').value = this.value" />
						</div>
                    </td>
                </tr>
                <tr><td colspan="4" class="line"></td></tr>
                <tr>
                    <td class="thead">종류</td>
                    <td colspan="3">
                        <p>
                            <input id="type1" name="company_type" type="radio" title="증권사" value="증권사"<?php if($broker['company_type'] == '증권사') echo ' checked="checked"' ?> /> <label for="type1">증권사</label>&nbsp;&nbsp;
                            <input id="type2" name="company_type" type="radio" title="선물사" value="선물사"<?php if($broker['company_type'] == '선물사') echo ' checked="checked"' ?> /> <label for="type2">선물사</label>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="thead">노출여부</td>
                    <td colspan="3">
                        <p>
                            <input id="open1" name="is_open" type="radio" title="노출" value="1"<?php if($broker['is_open']) echo ' checked="checked"' ?> /> <label for="open1">노출</label>&nbsp;&nbsp;
                            <input id="open2" name="is_open" type="radio" title="비노출" value="0"<?php if(!$broker['is_open']) echo ' checked="checked"' ?> /> <label for="open2">비노출</label>
                        </p>
                    </td>
                </tr>
                <tr><td colspan="4" class="line"></td></tr>
                <tr>
                    <td class="thead">URL</td>
                    <td colspan="3">
                        <input id="url" name="url" type="text" title="URL" value="<?php echo htmlspecialchars($broker['url']) ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="thead">국내상품</td>
                    <td colspan="3">
                        <input id="domestic" name="domestic" type="text" title="국내상품" value="<?php echo htmlspecialchars($broker['domestic']) ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="thead">해외상품</td>
                    <td colspan="3">
                        <input id="overseas" name="overseas" type="text" title="해외상품" value="<?php echo htmlspecialchars($broker['overseas']) ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="thead">F/X</td>
                    <td>
                        <input id="fx" name="fx" type="text" title="F/X" value="<?php echo htmlspecialchars($broker['fx']) ?>" />
                    </td>
                    <td class="thead">DMA</td>
                    <td>
                        <input id="dma" name="dma" type="text" title="DMA" value="<?php echo htmlspecialchars($broker['dma']) ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="thead">시스템 트레이딩</td>
                    <td colspan="3" style="line-height:30px;">
                    	<?php foreach($broker['system_trading_tools'] as $v){ ?>
                    	<?php if(!empty($v['logo'])){ ?><img src="<?php echo $v['logo'] ?>" class="trading_logo" /><?php } ?>
                        <?php if(!empty($v['name'])) echo htmlspecialchars($v['name']) ?>
                        &nbsp;&nbsp; <button type="button" onclick="editSTool(<?php echo $v['tool_id'] ?>, '<?php if(!empty($v['name'])) echo htmlspecialchars($v['name']) ?>', '<?php if(!empty($v['logo'])) echo htmlspecialchars($v['logo']) ?>')" title="수정" class="sbtn"><span class="ir">수정</span></button> 
                        <button type="button" onclick="deleteSTool(<?php echo $broker['broker_id'] ?>, <?php echo $v['tool_id'] ?>)" title="삭제" class="sbtn delete"><span class="ir">삭제</span></button> <br />
                    	<?php } ?>
						<?php if(count($broker['system_trading_tools']) < 2){ ?>
                          &nbsp;&nbsp; <button type="button" onclick="addSTool();" title="추가" class="sbtn"><span class="ir">추가</span></button>
						  <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class="thead">API</td>
                    <td colspan="3" style="line-height:30px;">
                    	<?php foreach($broker['api_tools'] as $v){ ?>
                        <?php if(!empty($v['logo'])){ ?><img src="<?php echo $v['logo'] ?>" class="trading_logo" /><?php } ?>
                        <?php if(!empty($v['name'])) echo htmlspecialchars($v['name']) ?>
                        &nbsp;&nbsp; <button type="button" onclick="editATool(<?php echo $v['tool_id'] ?>, '<?php if(!empty($v['name'])) echo htmlspecialchars($v['name']) ?>', '<?php if(!empty($v['logo'])) echo htmlspecialchars($v['logo']) ?>');" title="수정" class="sbtn"><span class="ir">수정</span></button>
                        <button type="button" onclick="deleteATool(<?php echo $broker['broker_id'] ?>, <?php echo $v['tool_id'] ?>)" title="삭제" class="sbtn delete"><span class="ir">삭제</span></button> <br />
						<?php } ?>
						<?php if(count($broker['api_tools']) < 2){ ?>
                          &nbsp;&nbsp; <button type="button" onclick="addATool();" title="추가" class="sbtn"><span class="ir">추가</span></button>
						  <?php } ?>
                    </td>
                </tr>
                <!--
                <tr>
                    <td class="thead">시스템 트레이딩</td>
                    <td colspan="3">                          
                        <input id="system_trading_name" name="system_trading_name" type="text" title="시스템 트레이딩" style="width:140px; margin-bottom:5px;" value="<?php if(!empty($broker['system_trading_tools'][0]['name'])) echo htmlspecialchars($broker['system_trading_tools'][0]['name']) ?>"  />
                        <?php if(!empty($broker['system_trading_tools'][0]['logo'])){ ?><img src="<?php echo $broker['system_trading_tools'][0]['logo'] ?>" class="trading_logo" /><?php } ?><br />
                        <input id="img_trading" name="img_trading" type="text" title="시스템 트레이딩 이미지" value="" style="width:400px;" onclick="document.getElementById('file_trading').click();" readonly="readonly"  />
                        <button type="button" title="찾아보기" class="act" onclick="document.getElementById('file_trading').click();" value="찾아보기"><span class="ir">찾아보기</span></button>

                        <input id="file_trading" name="system_trading_image" type="file" title="시스템 트레이딩 이미지" value="" style="display:none;" onchange=" document.getElementById('img_trading').value = this.value"  />
                       
                        <p class="btn_s">
                            <button onclick="" name="" title="수정" class="submit"><span class="ir">수정</span></button>                        
                            <button onclick="" name="" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                        </p>
                    </td>
                </tr>
				<tr>
                    <td class="thead">시스템 트레이딩</td>
                    <td colspan="3">                          
                        <input id="system_trading_name1" name="system_trading_name1" type="text" title="시스템 트레이딩" style="width:140px; margin-bottom:5px;" value="<?php if(!empty($broker['system_trading_tools'][1]['name'])) echo htmlspecialchars($broker['system_trading_tools'][1]['name']) ?>"  />
                        <?php if(!empty($broker['system_trading_tools'][1]['logo'])){ ?><img src="<?php echo $broker['system_trading_tools'][1]['logo'] ?>" class="trading_logo" /><?php } ?><br />
                        <input id="img_trading1" name="img_trading1" type="text" title="시스템 트레이딩 이미지" value="" style="width:400px;" onclick="document.getElementById('file_trading1').click();" readonly="readonly"  />
                        <button type="button" title="찾아보기" class="act" onclick="document.getElementById('file_trading1').click();" value="찾아보기"><span class="ir">찾아보기</span></button>

                        <input id="file_trading1" name="system_trading_image1" type="file" title="시스템 트레이딩 이미지" value="" style="display:none;" onchange=" document.getElementById('img_trading1').value = this.value"  />

                        <p class="btn_s">
                            <button onclick="" name="" title="수정" class="submit"><span class="ir">수정</span></button>                        
                            <button onclick="" name="" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="thead">API</td>
                    <td colspan="3">       
                        <input id="api_name" name="api_name" type="text" title="API이미지" style="width:140px; margin-bottom:5px;" value="<?php if(!empty($broker['api_tools'][0]['name'])) echo htmlspecialchars($broker['api_tools'][0]['name']) ?>"  />
                        <?php if(!empty($broker['api_tools'][0]['logo'])){ ?><img src="<?php echo $broker['api_tools'][0]['logo'] ?>" class="trading_logo" /><?php } ?><br />
                        <input id="img_api" name="img_api" type="text" title="API이미지" value="" style="width:400px;" onclick="document.getElementById('file_api').click();" readonly="readonly"  />
                        <button type="button" title="찾아보기" class="act" onclick="document.getElementById('file_api').click();" value="찾아보기"><span class="ir">찾아보기</span></button>

                        <input id="file_api" name="api_image" type="file" title="API이미지" value="" style="display:none;" onchange=" document.getElementById('img_api').value = this.value"  />
                        
                        <p class="btn_s">
                            <button onclick="" name="" title="수정" class="submit"><span class="ir">수정</span></button>                        
                            <button onclick="" name="" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                        </p>
                    </td>
                </tr>
				<tr>
                    <td class="thead">API</td>
                    <td colspan="3">       
                        <input id="api_name1" name="api_name1" type="text" title="API이미지" style="width:140px; margin-bottom:5px;" value="<?php if(!empty($broker['api_tools'][1]['name'])) echo htmlspecialchars($broker['api_tools'][1]['name']) ?>"  />
                        <?php if(!empty($broker['api_tools'][1]['logo'])){ ?><img src="<?php echo $broker['api_tools'][1]['logo'] ?>" class="trading_logo" /><?php } ?><br />
                        <input id="img_api1" name="img_api1" type="text" title="API이미지" value="" style="width:400px;" onclick="document.getElementById('file_api1').click();" readonly="readonly"  />
                        <button type="button" title="찾아보기" class="act" onclick="document.getElementById('file_api1').click();" value="찾아보기"><span class="ir">찾아보기</span></button>

                        <input id="file_api1" name="api_image1" type="file" title="API이미지" value="" style="display:none;" onchange=" document.getElementById('img_api1').value = this.value"  />
                        
                        <p class="btn_s">
                            <button onclick="" name="" title="수정" class="submit"><span class="ir">수정</span></button>                        
                            <button onclick="" name="" title="삭제" class="sbtn"><span class="ir">삭제</span></button>
                        </p>
                    </td>
                </tr>
                <tr><td colspan="4" class="line"></td></tr>
                -->
				<!--
                <tr>
                    <td class="thead">브로커 이메일</td>
                    <td colspan="3">
                        <input id="email" name="email" type="text" title="브로커 이메일"  />
                    </td>
                </tr>
				-->
            </tbody>
            </table>
			
            <p class="btn_area">
                <button type="submit" title="수정" class="submit"><span class="ir">수정</span></button>
                <button type="reset" title="취소" class="cancel"><span class="ir">취소</span></button>
            </p>
            </form>
            
            <!-- 시스템트레이딩 추가 -->
            <div id="add_s_tool" class="layer" style="width:480px; display:none;">
                <div class="layer_head">
                    <p class="text">시스템 트레이딩 추가</p>                    
                    <span class="layer_close" onclick="closeLayer('add_s_tool');">X</span>
                </div>

				<form action="/admin/brokers/<?php echo $broker['broker_id'] ?>/add_tool" method="post" enctype="multipart/form-data">
                <div class="layer_form">         
                    <input id="system_trading_name" name="system_trading_name" type="text" title="시스템 트레이딩" style="width:140px; margin-bottom:5px;" value=""  />
                    <br />
									
					<input id="img_trading" name="img_trading" type="text"  title="시스템 트레이딩 이미지" class="file_input_textbox" style="width:360px;" readonly="readonly">

					<div class="file_input_div">		
						<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
						<input id="file_trading" name="system_trading_image" type="file" title="시스템 트레이딩 이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_trading').value = this.value" />
					</div>
                     
                    <br />
                    <p class="btn_area">
                        <button type="submit" title="추가" class="submit"><span class="ir">추가</span></button>
                        <button type="button" title="취소" onclick="closeLayer('add_s_tool');" class="cancel"><span class="ir">취소</span></button>
                    </p>
                </div>
                </form>
            </div>
            <!-- 시스템트레이딩 수정 -->
            <div id="edit_s_tool" class="layer" style="width:480px; display:none;">
                <div class="layer_head">
                    <p class="text">시스템 트레이딩 수정</p>                    
                    <span class="layer_close" onclick="closeLayer('edit_s_tool');">X</span>
                </div>

				<form action="/admin/brokers/<?php echo $broker['broker_id'] ?>/edit_tool" method="post" enctype="multipart/form-data">
                <div class="layer_form">
                     <input id="system_trading_name1" name="system_trading_name" type="text" title="시스템 트레이딩" style="width:140px; margin-bottom:5px;" value=""  />
                      <img src="" class="trading_logo" id="system_trading_logo1" style="display:none;" /><br />
									
						<input id="img_trading1" name="img_trading1" type="text"  title="시스템 트레이딩 이미지" class="file_input_textbox" style="width:360px;" readonly="readonly">

						<div class="file_input_div">		
							<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
							<input id="file_trading1" name="system_trading_image" type="file" title="시스템 트레이딩 이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_trading1').value = this.value" />
						</div>
                     
                    <br />
                    <p class="btn_area">
						<input type="hidden" name="tool_type" value="s">
                    	<input type="hidden" name="tool_id" id="edit_s_tool_id" value="">
                        <button type="submit" title="수정" class="submit"><span class="ir">수정</span></button>
                        <button type="button" title="취소" onclick="closeLayer('edit_s_tool');" class="cancel"><span class="ir">취소</span></button>
                    </p>
                </div>
                </form>
            </div>
            
            <!-- API 추가 -->
            <div id="add_a_tool" class="layer" style="width:480px; display:none;">
                <div class="layer_head">
                    <p class="text">API 추가</p>                    
                    <span class="layer_close" onclick="closeLayer('add_a_tool');">X</span>
                </div>

				<form action="/admin/brokers/<?php echo $broker['broker_id'] ?>/add_tool" method="post" enctype="multipart/form-data">
                <div class="layer_form">
                                         
                    <input id="api_name" name="api_name" type="text" title="API이미지" style="width:140px; margin-bottom:5px;" value=""  />
                    <br />
					
					<input id="img_api" name="img_api" type="text"  title="API이미지" class="file_input_textbox" style="width:360px;" readonly="readonly">

					<div class="file_input_div">		
						<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
						<input id="file_api" name="api_image" type="file" title="API이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_api').value = this.value" />
					</div>

                    <br />
                    <p class="btn_area">
                        <button type="submit" title="추가" class="submit"><span class="ir">추가</span></button>
                        <button type="button" title="취소" onclick="closeLayer('add_a_tool');" class="cancel"><span class="ir">취소</span></button>
                    </p>
                </div>
                </form>
            </div>
            <!-- API 수정 -->
            <div id="edit_a_tool" class="layer" style="width:480px; display:none;">
                <div class="layer_head">
                    <p class="text">API 수정</p>                    
                    <span class="layer_close" onclick="closeLayer('edit_a_tool');">X</span>
                </div>

				<form action="/admin/brokers/<?php echo $broker['broker_id'] ?>/edit_tool" method="post" enctype="multipart/form-data">
                <div class="layer_form">
                                         
                    <input id="api_name1" name="api_name" type="text" title="API이미지" style="width:140px; margin-bottom:5px;" value=""  />
                    <img src="" class="trading_logo" id="api_logo1" style="display:none;" /><br />
					
					<input id="img_api1" name="img_api1" type="text"  title="API이미지" class="file_input_textbox" style="width:360px;" readonly="readonly">

					<div class="file_input_div">		
						<input type="button" title="찾아보기" value="찾아보기" class="file_input_button act"></button>
						<input id="file_api1" name="api_image" type="file" title="API이미지" class="file_input_hidden" style="width:190px;" onchange="document.getElementById('img_api1').value = this.value" />
					</div>

                    <br />
                    <p class="btn_area">
						<input type="hidden" name="tool_type" value="a">
                    	<input type="hidden" name="tool_id" id="edit_a_tool_id" value="">
                        <button type="submit" title="수정" class="submit"><span class="ir">수정</span></button>
                        <button type="button" title="취소" onclick="closeLayer('edit_a_tool');" class="cancel"><span class="ir">취소</span></button>
                    </p>
                </div>
                </form>
            </div>

        </div>        
    </div>
    <!------ //본문 영역 ------->

	<?php require_once('footer.php') ?>

</body>
</html>