<html>
<head>
<title></title>
<style>table {border-callapse:collapse;} td {border:1px solid black;}</style>
</head>
<body>
<table>
<thead>
<tr>
<th>월</th>
<th>평균원금</th>
<th>입출금</th>
<th>월손익</th>
<th>월손익(%)</th>
<th>누적손익</th>
<th>누적수익(%)</th>
</tr>
</thead>
<tbody>
    <? foreach ($monthly_values as $v) { ?>
    <tr>
        <td><?=$v['baseyear'].'.'.$v['basemonth']?></td>
        <td><?=$v['avg_principal']?></td>
        <td><?=$v['flow']?></td>
        <td><?=$v['monthly_pl']?></td>
        <td><?=$v['monthly_pl_rate']?></td>
        <td><?=$v['acc_pl']?></td>
        <td><?=$v['acc_pl_rate']?></td>
    </tr>
    <? } ?>
</tbody>
</table>
</body>
</html>
