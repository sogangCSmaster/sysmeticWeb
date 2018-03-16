<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />  
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta content='IE=edge' http-equiv='X-UA-Compatible'>
    <title>Sysmetic Traders</title>
</head>

<body>

<!-- 관리자 발송 메일 // 공지 -->
<table width="710" border="0" cellspacing="0" cellpadding="0" align="center">
    <thead>
    <tr><td colspan="2" height="20"></td></tr>
    <tr>
        <td width="513" height="60" bgcolor="#262a33" style="padding-left:20px;"><a href="<?php echo htmlspecialchars($url) ?>" target="_blank"><img src="<?php echo htmlspecialchars($url) ?>/img/logo_mail.gif" border="0" /></a></td>
        <td align="left" bgcolor="#262a33"><a href="<?php echo htmlspecialchars($url) ?>" target="_blank"><img src="<?php echo htmlspecialchars($url) ?>/img/link_mail.gif" border="0" /></a></td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan="2" bgcolor="#f0f0f0">
            <table width="580" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr><td height="60"></td></tr>
                <tr>
                    <td height="34" valign="top" style="font-family:돋움, Dotum; font-size:14px; color:#828894;"><b>SYSMETIC TRADERS에서 알려드립니다.</b></td>
                </tr>
                <tr><td height="2" bgcolor="#828894"></td></tr>
                <tr><td height="25"></td></tr>
                <tr>
                    <td style="font-family:돋움, Dotum; font-size:14px; color:#828894; line-height:25px;">
                        <?php echo nl2br(htmlspecialchars($notice_content)) ?>
                    </td>
                </tr>
                <tr><td height="50"></td></tr>
            </table>
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr><td colspan="2" height="8"></td></tr>
    <tr>
        <td colspan="2" style="font-family:돋움, Dotum; font-size:11px; color:#a4a9b3; line-height:21px;">
            이 메일은 SYMETIC TRADERS 에서 사이트 이용 관련 알림을 위해 발송되는 메일로 메일주소는 발신전용입니다.
        </td>
    </tr>
    <tr><td colspan="2" height="40"></td></tr>
    </tfoot>
</table>

</body>
</html>