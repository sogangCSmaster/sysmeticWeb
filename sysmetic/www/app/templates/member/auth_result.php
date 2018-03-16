<!doctype html>
<html lang="ko">
<head>
    <title>실명인증</title>
    <script>
    <? if ($result == 'Y') { ?>
    opener.document.getElementById('name').value = '<?=$name?>';
    opener.document.getElementById('mobile').value = '<?=$mobile?>';
    <? } else { ?>
    alert("실명인증에 실패하였습니다.");
    <? } ?>
    self.close();
    </script>
</head>
</html>
