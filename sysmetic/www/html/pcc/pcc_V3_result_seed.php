<?
	/************************************************************************************/
	/* - sample ���������� ��û �� ��Ű�� ������ Reqnum���� �����ͼ� IV���� ����   	    */
	/* - ��Ű ����ð� ��� �� ���ó�� ����										    */
 	/************************************************************************************/
	//01. ��Ű�� Ȯ��
	$iv = "";
	if (isset($_COOKIE["REQNUM"])) {
		$iv = $_COOKIE["REQNUM"]; 
		//��Ű ����
		setcookie("REQNUM", "", time()-600);
	} else {
?>
		<script language=javascript>
			alert("������ ����Ǿ����ϴ�.!!");			
		</script>
<?
		return;
	}

	// �Ķ���ͷ� ���� ��û���
	$enc_retInfo = $_REQUEST["retInfo"];

	//02. ��û��� ��ȣȭ
	//2014.02.07 KISA �ǰ����
	//�� ���� ��, �ҹ� �õ� ������ ���Ͽ� �Ʒ� ���Ͽ� �ش��ϴ� ���ڿ��� ���
	if(preg_match('~[^0-9a-zA-Z+/=^]~', $iv, $matches)||preg_match('~[^0-9a-zA-Z+/=^]~', $enc_retInfo, $matches)){
		echo "�Է� �� Ȯ���� �ʿ��մϴ�.(res-1)"; exit;
	}
	$dec_retInfo = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX SEED 2 0 $iv $enc_retInfo ");//��ȣȭ��� ��ġ�� ������ SciSecuX ������ �ִ� ������ ��θ� �������ּ���.

?>

    [����Ȯ�� ��� ���� Sample-PHP] <br> <br>

	[��ȣȭ �ϱ��� ���Ű�] <br><br>
	retInfo : <? echo $enc_retInfo ?> <br>


<?
	//������ ���� : "����Ȯ��1����ȣȭ��/������������/�Ϻ�ȭȮ�庯��"
	$totInfo = split("\\^", $dec_retInfo);

	$encPara  = $totInfo[0];		//����Ȯ��1����ȣȭ��
	$encMsg   = $totInfo[1];		//��ȣȭ�� ���� �Ķ������ ������������

	//03. ������������ ����
	//2014.02.07 KISA �ǰ����
	//�� ���� ��, �ҹ� �õ� ������ ���Ͽ� �Ʒ� ���Ͽ� �ش��ϴ� ���ڿ��� ���
	if(preg_match('~[^0-9a-zA-Z+/=^]~', $encPara, $matches)){
		echo "�Է� �� Ȯ���� �ʿ��մϴ�.(res-2)"; exit;
	}
	$hmac_str = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX HMAC 1 0 $encPara ");

	if($hmac_str != $encMsg){
?>
		<script language=javascript>
			alert("���������� �����Դϴ�.!!");
		</script>
		<a href="http://.../pcc_V3_input_seed.php">[Back]</a>
<?
		exit;
	}

	//04. ����Ȯ��1����ȣȭ�� ��ȣȭ
	//2014.02.07 KISA �ǰ����
	//�� ���� ��, �ҹ� �õ� ������ ���Ͽ� �Ʒ� ���Ͽ� �ش��ϴ� ���ڿ��� ���
	if(preg_match('~[^0-9a-zA-Z+/=^]~', $iv, $matches)||preg_match('~[^0-9a-zA-Z+/=^]~', $encPara, $matches)){
		echo "�Է� �� Ȯ���� �ʿ��մϴ�.(res-3)"; exit;
	}
	$decPara = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX SEED 2 0 $iv $encPara ");

	//05. �Ķ���� �и�
	$split_dec_retInfo = split("\\^", $decPara);

	$name		= $split_dec_retInfo[0];		//����
	$birYMD		= $split_dec_retInfo[1];		//�������
	$sex		= $split_dec_retInfo[2];		//����
	$fgnGbn		= $split_dec_retInfo[3];		//���ܱ��� ���а�
	$di			= $split_dec_retInfo[4];		//DI
	$ci1		= $split_dec_retInfo[5];		//CI1
	$ci2		= $split_dec_retInfo[6];		//CI2	
	$civersion	= $split_dec_retInfo[7];		//CI Version
	$reqNum		= $split_dec_retInfo[8];		//��û��ȣ
	$result		= $split_dec_retInfo[9];		//����Ȯ�� ��� (Y/N)
	$certGb		= $split_dec_retInfo[10];		//��������
	$cellNo		= $split_dec_retInfo[11];		//�ڵ��� ��ȣ
	$cellCorp	= $split_dec_retInfo[12];		//�̵���Ż�
	$certDate	= $split_dec_retInfo[13];		//�����ð�
	$addVar		= $split_dec_retInfo[14];		//�߰� �Ķ����

	//���� �ʵ�
	$ext1		= $split_dec_retInfo[15];
	$ext2		= $split_dec_retInfo[16];
	$ext3		= $split_dec_retInfo[17];
	$ext4		= $split_dec_retInfo[18];
	$ext5		= $split_dec_retInfo[19];


?>
<html>
    <head>
        <title>SCI������ ����Ȯ�μ���  �׽�Ʈ</title>
        <meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
        <style>
            <!--
            body,p,ol,ul,td
            {
                font-family: ����;
                font-size: 12px;
            }

            a:link { size:9px;color:#000000;text-decoration: none; line-height: 12px}
            a:visited { size:9px;color:#555555;text-decoration: none; line-height: 12px}
            a:hover { color:#ff9900;text-decoration: none; line-height: 12px}

            .style1 {
                color: #6b902a;
                font-weight: bold;
            }
            .style2 {
                color: #666666
            }
            .style3 {
                color: #3b5d00;
                font-weight: bold;
            }
            -->
        </style>
    </head>
	<body>
            [��ȣȭ �� ���Ű�] <br>
            <br>
            <table cellpadding="1" cellspacing="1" border="1">
				<tr>
					<td align="center" colspan="2">14���̻� �� �ſ������� ���</td>
				</tr>
				<tr>
                    <td align="left">����</td>
                    <td align="left"><? echo $name ?></td>
                </tr>
				<tr>
                    <td align="left">����</td>
                    <td align="left"><? echo $sex ?></td>
                </tr>
				<tr>
                    <td align="left">�������</td>
                    <td align="left"><? echo $birYMD ?></td>
                </tr>
				<tr>
                    <td align="left">���ܱ��� ���а�(1:������, 2:�ܱ���)</td>
                    <td align="left"><? echo $fgnGbn ?></td>
                </tr>				
				<tr>
                    <td align="left">�ߺ�����������</td>
                    <td align="left"><? echo $di ?></td>
                </tr>
				<tr>
                    <td align="left">��������1</td>
                    <td align="left"><? echo $ci1 ?></td>
                </tr>
				<tr>
                    <td align="left">��������2</td>
                    <td align="left"><? echo $ci2 ?></td>
                </tr>
				<tr>
                    <td align="left">������������</td>
                    <td align="left"><? echo $civersion ?></td>
                </tr>
                <tr>
                    <td align="left">��û��ȣ</td>
                    <td align="left"><? echo $reqNum ?></td>
                </tr>
				<tr>
                    <td align="left">������������</td>
                    <td align="left"><? echo $result ?></td>
                </tr>
				<tr>
                    <td align="left">��������</td>
                    <td align="left"><? echo $certGb ?></td>
                </tr>
				<tr>
                    <td align="left">�ڵ�����ȣ</td>
                    <td align="left"><? echo $cellNo ?></td>
                </tr>
				<tr>
                    <td align="left">�̵���Ż�</td>
                    <td align="left"><? echo $cellCorp ?></td>
                </tr>
                <tr>
                    <td align="left">��û�ð�</td>
                    <td align="left"><? echo $certDate ?></td>
                </tr>				
				<tr>
                    <td align="left">�߰��Ķ����</td>
                    <td align="left"><? echo $addVar ?>&nbsp;</td>
                </tr>
				
            </table>              
            <br>
            <br>
            <a href="http://.../pcc_V3_input_seed.php">[Back]</a>
</body>
</html>


---------------------------
�� ������ �޽���
---------------------------
�������� ���� ��û�� �ƴմϴ�.

[������ȣ] PJ100811
---------------------------
Ȯ��   
---------------------------
