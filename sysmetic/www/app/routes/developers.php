<?
$app->group('/developers', function() use ($app, $log, $isLoggedIn) {

    // pb, trader 목록
    $app->get('/list', function() use ($app, $log, $isLoggedIn) {

        $type = $app->request->get('type');
        $type2 = $app->request->get('type2');
        $keyword = $app->request->get('keyword');

        if ($type == 'T' || $type == 'P') {
            $where = "user_type = '$type' and is_delete='0' ";
        } else {
            $where = "user_type IN ('T', 'P') and is_delete='0' ";
        }

		if($type2){
			$tmp = $app->db->select('subscribe', '*', array('reg_uid' => $_SESSION['user']['uid']));
			$uids = array();
			foreach ($tmp as $k => $v) {
				$uids[] = $v['uid'];
			}
            $where = "uid IN (".implode(",",$uids).")";
		}

        if (!empty($keyword)) {
            $where_keyword = " AND (name LIKE '%".$app->db->conn->real_escape_string($keyword)."%' OR nickname LIKE '%".$app->db->conn->real_escape_string($keyword)."%' OR company LIKE '%".$app->db->conn->real_escape_string($keyword)."%')";
            $keyword = htmlspecialchars($keyword);
        }

        // 페이징 관련 변수
        $count = 10;
        $page = $app->request->get('page');
        if (empty($page) || !is_numeric($page)) $page = 1;
        $start = ($page - 1) * $count;

        $sql = "
                SELECT *, (b.strategy_cnt + b.portfolio_cnt) sort
                FROM (
                    SELECT
                        *,
                        (SELECT count(*) FROM strategy WHERE (developer_uid = a.uid or pb_uid = a.uid) AND is_delete='0' AND is_operate='1' AND is_open='1') strategy_cnt,
                        (SELECT count(*) from portfolio WHERE uid=a.uid AND is_open = '1') portfolio_cnt,
                        (SELECT logo_s FROM broker WHERE broker_id = a.broker_id) logo_s
                    FROM
                        user a
                    WHERE
                        $where $where_keyword
                    ) b
                ORDER BY sort DESC LIMIT $start, $count";

        $result = $app->db->conn->query($sql);
        $developers = array();

        while ($row = $result->fetch_assoc()) {
            $developers[] = $row;
        }

        $param = array(
            'developers' => $developers,
            'more'      => (count($developers) < $count) ? false : true,
        );

        $app->render('developers/list.php', $param);
    });

});
