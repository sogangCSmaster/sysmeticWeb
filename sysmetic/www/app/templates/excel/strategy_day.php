<html>
<head>
<title></title>
<style>table {border-callapse:collapse;} td {border:1px solid black;}</style>
</head>
<body>
<table>
<thead>
<tr>
<th>일자</th>
<th>원금</th>
<th>입출금</th>
<th>일손익</th>
<th>일손익(%)</th>
<th>누적손익</th>
<th>누적손익(%)</th>
</tr>
</thead>
<tbody>
    <? foreach ($daily_values as $v) { ?>
    <tr>
    <td><?=$v['basedate']?></td>
    <td><?=$v['principal']?></td>
    <td><?=$v['flow']?></td>
    <td><?=$v['daily_pl']?></td>
    <td><?=$v['daily_pl_rate']?></td>
    <td><?=$v['acc_pl']?></td>
    <td><?=$v['acc_pl_rate']?></td>
    </tr>
    <? } ?>
</tbody>
</table>
</body>
</html>
