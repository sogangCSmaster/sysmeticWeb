<?
	/************************************************************************************/
	/* - ����� ��ȣȭ�� ���� IV ���� Random�ϰ� ������.(�ݵ�� �ʿ���!!)			    */
	/* - input�ڽ� reqNum�� value����  echo $CurTime.$RandNo  ���·� ����				*/
 	/************************************************************************************/
    $CurTime = date(YmdHis);  //���� �ð� ���ϱ�

	//6�ڸ� ������ ����
	$RandNo = rand(100000, 999999);
	
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
<body onload="document.reqPCCForm.id.focus();" bgcolor="#FFFFFF" topmargin=0 leftmargin=0 marginheight=0 marginwidth=0>
        <center>
            <br><br><br>
            <span class="style1">SCI������ ����Ȯ�μ��� �׽�Ʈ</span><br>

            <form name="reqPCCForm" method="post" action="http://sysmetic.mypro.co.kr/pcc/pcc_V3_sample_seed.php">
                <table cellpadding=1 cellspacing=1>
                    <tr>
                        <td align=center>ȸ������̵�</td>
                        <td align=left><input type="text" name="id" size='41' maxlength ='8' value = ""></td>
                    </tr>
                    <tr>
                        <td align=center>���񽺹�ȣ</td>
                        <td align=left><input type="text" name="srvNo" size='41' maxlength ='6' value=""></td>
                    </tr>
                    <tr>
                        <td align=center>��û��ȣ</td>
                        <td align=left><input type="text" name="reqNum" size='41' maxlength ='40' value='<? echo $CurTime.$RandNo ?>'></td>
                    </tr>
					<tr>
                        <td align=center>��������</td>
                        <td align=left>
                            <select name="certGb" style="width:300">                                
                                <option value="H">�޴���</option>
                            </select>
                        </td>
                    </tr>
					<tr>
                        <td align=center>��û�ð�</td>
                        <td align=left><input type="text" name="certDate" size='41' maxlength ='40' value='<? echo $CurTime ?>'></td>
                    </tr>
					<tr>
                        <td align=center>�߰��Ķ����</td>
                        <td align=left><input type="text" name="addVar"  size="41" maxlength ='320' value=""></td>
                    </tr>
                    <tr>
                        <td align=center>�������URL</td>
                        <td align=left><input type="text" name="retUrl" size="41" value="32http://sysmetic.mypro.co.kr/pcc/pcc_V3_popup_seed.php"></td>
                    </tr>
                </table>
                <br><br>
                <input type="submit" value="����Ȯ�μ��� �׽�Ʈ">
            </form>
            <br>
            <br>
            �� Sampleȭ���� ����ſ��� ����Ȯ�μ��� �׽�Ʈ�� ���� �����ϰ� �ִ� ȭ���Դϴ�.<br>
            <br>
        </center>
    </body>
</html>