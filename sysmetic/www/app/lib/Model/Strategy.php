<?php
namespace Model;

class Strategy
{
    public $db;
    public $listCaching = false;
    public $mcache = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function __destruct()
    {

    }

    // 전략상세
    public function getInfo($strategy_id, $isLoggedIn)
    {

        $strategy = $this->db->selectOne('strategy', '*', array('strategy_id'=>$strategy_id));

        if (empty($strategy)) {
            return false;
            //$app->halt(404, 'not found');
        }

        // 종목
        $strategy_items = $this->db->select('strategy_item', '*', array('strategy_id'=>$strategy['strategy_id']));

        $item_id_array = array();
        $strategy_items_value = array();
        foreach ($strategy_items as $kk=>$vv) {
            $item_id_array[] = $vv['item_id'];
        }

        if (count($item_id_array)) {
            $result = $this->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
            while ($row = $result->fetch_array()) {
                $strategy_items_value[] = $row;
            }
        }

        $strategy['items'] = $strategy_items_value;

        // 브로커
        $strategy['broker'] = $this->db->selectOne('broker', '*', array('broker_id'=>$strategy['broker_id']));

        // 매매툴
        $strategy['system_tool'] = $this->db->selectOne('system_trading_tool', '*', array('tool_id'=>$strategy['tool_id']));

        // 트레이더
        $developer = $this->db->selectOne('user', '*', array('uid'=>$strategy['developer_uid']));
        $strategy['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

        // 팔로워
        $followers_count = $this->db->selectCount('following_strategy', array('strategy_id'=>$strategy['strategy_id']));
        $strategy['followers_count'] = $followers_count;

        // 팔로잉 여부
        $is_following = false;

        if($isLoggedIn()){
            $is_following = $this->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'], 'strategy_id'=>$strategy['strategy_id'])) > 0 ? true : false;
            $strategy['is_mine'] = ($_SESSION['user']['uid'] == $strategy['developer_uid']) ? true : false;
        }

        $strategy['is_following'] = $is_following;

        // 투자자 수
        $strategy['investor_count'] = 0;
        $result = $this->db->conn->query('SELECT SUM(investor) FROM strategy_funding WHERE strategy_id = '.$strategy['strategy_id']);
        while ($row = $result->fetch_array()) {
            $strategy['investor_count'] = $row[0];
        }

        // 펀딩금액
        $strategy['total_funding'] = 0;
        $result = $this->db->conn->query('SELECT SUM(money) FROM strategy_funding WHERE strategy_id = '.$strategy['strategy_id']);
        while ($row = $result->fetch_array()) {
            $strategy['total_funding'] = empty($row[0]) ? 0 : $row[0];
        }

        // 산식
        $strategy['daily_values'] = $this->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$strategy['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));;

        foreach ($strategy['daily_values'] as $k => $v) {
            $strategy['daily_values'][$k]['m_timestamp'] = strtotime($strategy['daily_values'][$k]['basedate']) * 1000;
        }

        // 월간수익률
        $monthly_values = $this->db->select('strategy_monthly_analysis', '*', array('strategy_id'=>$strategy['strategy_id']), array('baseyear'=>'asc','basemonth'=>'asc'));
        $strategy['monthly_profit_rate'] = calMonthlyPLRate($monthly_values);

        // 년간수익률
        $yearly_values = $this->db->select('strategy_yearly_analysis', '*', array('strategy_id'=>$strategy['strategy_id']), array('baseyear'=>'asc'));
        $strategy['yearly_profit_rate'] = calYearlyPLRate($yearly_values);

        // 저장된 파일
        $files = $this->db->selectOne('strategy_file', '*', array('strategy_id'=>$strategy['strategy_id']));
        $strategy['file'] = $files;

        return $strategy;
    }

    public function setListCaching($cache, $mcache=null)
    {
        $this->listCaching = $cache;
        $this->mcache = $macahe;
    }

    // 전략리스트
    public function getList($search=array(), $sort=array(), $start=0, $limit=0)
    {

        $limit = ($limit === 0) ? 10 : $limit;

        $dbo = $this->db->conn;

        $escape = function($param) use ($dbo) {
            if (is_array($param) == false) {
                return $dbo->real_escape_string($param);
            } else {
                return $param;
            }
        };

        $search = array_map($escape, $search);

        // 일반 검색조건 설정
        $where = array();
        if (isset($search['is_open']) && $search['is_open'] != '') $where[] = "is_open='".$search['is_open']."'";
        if (isset($search['is_operate'])) $where[] = "is_operate = '".$search['is_operate']."'";
        if (isset($search['is_delete'])) $where[] = "is_delete = '".$search['is_delete']."'";

        if (isset($search['search_keyword'])) $where[] = "name like '%".$search['search_keyword']."%'";

        if ($search['search_type'] == 'mypage' or $search['search_type'] == 'mypage2') {
            $where[] = "(developer_uid = '$search[developer_uid]' OR trader_uid = '$search[developer_uid]' OR pb_uid = '$search[developer_uid]')";
        } else {
            //if (!empty($search['developer_uid'])) $where[] = "developer_uid='".$search['developer_uid']."'";
			if($search[developer_uid]){
				 $where[] = "(developer_uid = '$search[developer_uid]' OR trader_uid = '$search[developer_uid]' OR pb_uid = '$search[developer_uid]')";
			}
        }

        if (isset($search['strategy_ids']) && is_array($search['strategy_ids'])) {
            $where[] = "strategy_id IN ('".implode("','", $search['strategy_ids'])."')";
        }

        if (!empty($search['q_item'])) {
           $q_item_strategy_ids = array();
            $rows = $this->db->select('strategy_item', '*', array('item_id'=>$search['q_item']));
            foreach ($rows as $row) {
                $q_item_strategy_ids[] = $row['strategy_id'];
            }

            if (count($q_item_strategy_ids)) {
                $where[] = 'strategy_id IN ('.implode(',', $q_item_strategy_ids).')';
            }
        }

        if ($search['title']) {
            $where[] = 'name like "%'.$search['title'].'%"';
        }

        if (!empty($search['q_term'])) $where[] = "strategy_term = '".$search['q_term']."'";
        if (!empty($search['q_kind'])) $where[] = "strategy_kind = '".$search['q_kind']."'";
        //if (!empty($search['q'])) $where[] = "name LIKE '%'".$search['q']."%'";

        // 상세검색
        if (!empty($search['search_type'])) {
            if ($search['search_type'] == 'item') { // 항목별 검색
                if (isset($search['search_item']) && is_array($search['search_item']) && count($search['search_item'])) {
                    $safe_search_item = array();
                    foreach ($search['search_item'] as $v) {
                        $safe_search_item[] = $this->db->conn->real_escape_string($v);
                    }

                    $q_item_strategy_ids = array();
                    $result = $this->db->conn->query('SELECT * FROM strategy_item WHERE item_id IN (\''.implode('\',\'', $safe_search_item).'\')');
                    while ($row = $result->fetch_array()) {
                        $q_item_strategy_ids[] = $row['strategy_id'];
                    }
                    if (count($q_item_strategy_ids)) $where[] = 'strategy_id IN ('.implode(',', $q_item_strategy_ids).')';
                    else $where[] = 'strategy_id = 0'; // 매칭되는게 없으면 검색결과가 없다는 의미임
                }

                if (isset($search['search_strategy_type']) && is_array($search['search_strategy_type']) && count($search['search_strategy_type'])) {
                    $safe_search_strategy_type = array();
                    foreach ($search['search_strategy_type'] as $v) {
                        $safe_search_strategy_type[] = $this->db->conn->real_escape_string($v);
                    }
                    $where[] = 'strategy_type IN (\''.implode('\',\'', $safe_search_strategy_type).'\')';
                }

                if (isset($search['search_kind']) && is_array($search['search_kind']) && count($search['search_kind'])) {
                    $safe_search_kind = array();
                    foreach ($search['search_kind'] as $v) {
                        $safe_search_kind[] = $this->db->conn->real_escape_string($v);
                    }
                    $where[] = 'strategy_kind IN (\''.implode('\',\'', $safe_search_kind).'\')';
                }

                if (isset($search['search_term']) && is_array($search['search_term']) && count($search['search_term'])) {
                    $safe_search_term = array();
                    foreach ($search['search_term'] as $v) {
                        $safe_search_term[] = $this->db->conn->real_escape_string($v);
                    }
                    $where[] = 'strategy_term IN (\''.implode('\',\'', $safe_search_term).'\')';
                }

                if (isset($search['search_broker']) && is_array($search['search_broker']) && count($search['search_broker'])) {
                    $safe_search_broker = array();
                    foreach ($search['search_broker'] as $v) {
                        $safe_search_broker[] = $this->db->conn->real_escape_string($v);
                    }
                    $where[] = 'broker_id IN ('.implode(',', $safe_search_broker).')';
                }

                if (isset($search['search_profit_rate']) && is_array($search['search_profit_rate']) && count($search['search_profit_rate'])) {

                    $search_profit = array();
                    foreach ($search['search_profit_rate'] as $v) {
                        switch($v) {
                            case "10":
                                $search_profit[] = $search['search_profit_type']." <= 10";
                            break;
                            case "30";
                                $search_profit[] = "(".$search['search_profit_type']." > 10 AND ".$search['search_profit_type']." <= 30) ";
                            break;
                            case "50";
                                $search_profit[] = "(".$search['search_profit_type']." > 30 AND ".$search['search_profit_type']." <= 50) ";
                            break;
                            case "100";
                                $search_profit[] = "(".$search['search_profit_type']." > 50 AND ".$search['search_profit_type']." <= 100) ";
                            break;
                            default;
                                $search_profit[] = $search['search_profit_type']." >= 100";
                            break;
                        }
                    }
                    $where[] = '('.implode(' OR ', $search_profit).')';
                }

                if (isset($search['search_principal_min']) && is_numeric($search['search_principal_min'])) {
                    $where[] = '  principal >= '.$search['search_principal_min']*100000000;
                }

                if (isset($search['search_principal_max']) && is_numeric($search['search_principal_max'])) {
                    $where[] = '  principal <= '.$search['search_principal_max']*100000000;
                }

                if (isset($search['search_mdd_min']) && is_numeric($search['search_mdd_min'])) {
                    $where[] = '  mdd_rate <= '.$search['search_mdd_min'];
                }

                if (isset($search['search_mdd_max']) && is_numeric($search['search_mdd_max'])) {
                    $where[] = '  mdd_rate >= '.$search['search_mdd_max'];
                }

                if (isset($search['search_sharp_ratio_min']) && is_numeric($search['search_sharp_ratio_min'])) {
                    $where[] = '  sharp_ratio >= '.$search['search_sharp_ratio_min'];
                }

                if (isset($search['search_sharp_ratio_max']) && is_numeric($search['search_sharp_ratio_max'])) {
                    $where[] = '  sharp_ratio <= '.$search['search_sharp_ratio_max'];
                }
            } else if ($search['search_type'] == 'algorithm') { // 알고리즘
                if ($search['algorithm'] == 1) {
                    $sort['field'] = ' ( total_profit_rate/mdd )';
                } else if ($search['algorithm'] == 2) {
                    $sort['field'] = ' ( total_profit/(1 - ifnull(if(winning_rate = 0, 2, winning_rate),2)) ) ';
                } else if ($search['algorithm'] == 3) {
                    $sort['field'] = ' ( (mdd_rank + sm_score_rank + winning_rank) / 3 ) ';
                }
            }

        }   // 상세검색 끝

        if (count($where)) {
            $where_str = " WHERE ".implode(" AND ", $where);
        }

        $order_field = ($sort['field']) ? $sort['field'] : 'strategy_id';
        $order_by = ($sort['order_by']) ? $sort['order_by'] : 'desc';

        $sql = "SELECT COUNT(*) cnt FROM strategy {$where_str} ORDER BY {$order_field} {$order_by}";
       // echo $sql;
        $result = $this->db->conn->query($sql);
        $row = $result->fetch_assoc();
        $total_cnt = $row['cnt'];

        $sql = "SELECT * FROM strategy {$where_str} ORDER BY {$order_field} {$order_by}";
        $sql .= ' LIMIT '.$start.','.$limit;

        // 상품ㅁ종류
		$_kinds = $this->db->select('kind', '*', array(), array('sorting'=>'asc'));
        foreach ($_kinds as $v) {
            $kinds[$v['kind_id']] = $v['name'];
        }
        unset($_kinds);

        $strategies = array();
        $result = $this->db->conn->query($sql);
        while ($row = $result->fetch_array()) {
            $strategies[] = $row;
        }

        foreach ($strategies as $k => $v){
            // 종목
            $strategy_items = $this->db->select('strategy_item', 'item_id', array('strategy_id'=>$v['strategy_id']));

            $item_id_array = array();
            $strategy_items_value = array();
            foreach ($strategy_items as $kk=>$vv) {
                $item_id_array[] = $vv['item_id'];
            }

            if (count($item_id_array)) {
                $result = $this->db->conn->query('SELECT * FROM item WHERE item_id IN ('.implode(',', $item_id_array).')');
                while ($row = $result->fetch_array()) {
                    $strategy_items_value[] = $row;
                }
            }
            
            $strategies[$k]['kind'] = $kinds[$strategies[$k]['strategy_kind']];
            $strategies[$k]['items'] = $strategy_items_value;
            $strategies[$k]['broker'] = $this->db->selectOne('broker', '*', array('broker_id'=>$v['broker_id']));
            $strategies[$k]['types'] = $this->db->selectOne('type', '*', array('type_id'=>$v['strategy_type']));
            if ($strategies[$k]['trader_uid']) {
                $strategies[$k]['trader'] = $this->db->selectOne('user', '*', array('uid'=>$v['trader_uid']));
            }
            if ($strategies[$k]['pb_uid']) {
                $strategies[$k]['pb'] = $this->db->selectOne('user', '*', array('uid'=>$v['pb_uid']));
            }
            $strategies[$k]['system_tool'] = $this->db->selectOne('system_trading_tool', '*', array('tool_id'=>$v['tool_id']));
            $developer = $this->db->selectOne('user', 'nickname, picture, picture_s', array('uid'=>$v['developer_uid']));
            $strategies[$k]['developer'] = array('nickname'=>$developer['nickname'],'picture'=>$developer['picture'],'picture_s'=>$developer['picture_s']);

            // 팔로워
            /*
            $followers_count = $this->db->selectCount('following_strategy', array('strategy_id'=>$v['strategy_id']));
            $strategies[$k]['followers_count'] = $followers_count;
            */

            // 팔로잉 여부
            $is_following = false;

            if ($search['isLoggedIn']) {
                $is_following = $this->db->selectCount('following_strategy', array('uid'=>$_SESSION['user']['uid'], 'strategy_id'=>$v['strategy_id'])) > 0 ? true : false;

                $strategies[$k]['is_mine'] = ($_SESSION['user']['uid'] == $strategies[$k]['developer_uid']) ? true : false;
            }

            $strategies[$k]['is_following'] = $is_following;

            // 산식
            $daily_values = $this->db->select('strategy_daily_analysis', '*', array('strategy_id'=>$v['strategy_id'], 'holiday_flag'=>'0'), array('basedate'=>'asc'));

            $strategies[$k]['daily_values_cnt'] = count($daily_values);
            $strategies[$k]['daily_values'] = $daily_values;
            $sm_index_array = array();
            $c_price_array = array();
            foreach ($daily_values as $k1=>$v1) {
                    $m_timestamp = strtotime($v1['basedate'])*1000;
                    $sm_index_array[] = '['.$m_timestamp.','.$v1['sm_index'].']';
                    $c_price_array[] = $v1['sm_index'];			//- 
            }

            // 표를 그리기 위한 데이터
            $strategies[$k]['str_c_price'] = '['.implode(',', $sm_index_array ).']';

            // 포트폴리오 전략에서 사용
            $strategies[$k]['str_sm_index'] = '['.implode(',', $c_price_array).']';			//-
            $strategies[$k]['first_date'] = $daily_values[0]['basedate'];
            $strategies[$k]['last_date'] = $daily_values[count($daily_values)-1]['basedate'];

            // 펀딩금액
            if ($this->mcache !== null) {
                $total_funding = $this->mcache->get('strategy_total_funding:'.$v['strategy_id']);
            }

            if ($total_funding === false) {
                $result = $this->db->conn->query('SELECT SUM(money) FROM strategy_funding WHERE strategy_id = '.$v['strategy_id']);
                $row = $result->fetch_array();
                $total_funding = empty($row[0]) ? 0 : $row[0];
                if ($this->listCaching === true) {
                    $this->mcache->set('strategy_total_funding:'.$v['strategy_id'], $total_funding);
                }
            }
            $strategies[$k]['total_funding'] = $total_funding;

            // 캐시저장
            if ($this->listCaching === true) {
                $this->mcache->set('strategy:'.$v['strategy_id'], $strategies[$k]);
            }
        }

        return array($total_cnt, $strategies);
    }

    // 회원 대표 전략 조회
    public function getBasicStg($uid, $field = 'strategy_id') {
        $result = $this->db->conn->query('SELECT '.$field.' FROM strategy WHERE developer_uid = '.$uid.' ORDER BY total_profit_rate DESC LIMIT 1');
        $row = $result->fetch_array();
        return $row[0];
    }
}
