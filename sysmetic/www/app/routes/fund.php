<?php
/**
 * 투자받기
 */

// 투자받기 메인
$app->get('/fund', $authenticateForRole('T,P,A,N'), function() use ($app, $log, $isLoggedIn) {
    $topmenu = 'fund';

    switch ($_SESSION['user']['user_type']) {
        case 'T':
        case 'P':
        case 'A':
            break;
        case 'N':
        default:
            alert('사용이 불가능한 메뉴입니다.');
            $app->stop();
            break;
    }

    $app->render('fund/main.php', array('topmenu'=>$topmenu));
});

// 상품등록 strategies
$app->group('/fund/strategies', $authenticateForRole('T,P,A,N'), function () use ($app, $log, $isLoggedIn) {


    $app->post('/tools', function() use ($app, $log) {

        $broker_id = $app->request->post('broker_id');
        $tools = $app->db->select('system_trading_tool', 'tool_id, name', array('broker_id'=>$broker_id));

        echo json_encode($tools);
    });

    // 트레이더 검색
    $app->post('/trader_search', function () use ($app, $log) {
        $nickname = $app->request->post('nickname');

        $response_traders = array();

        if(empty($nickname)){
            echo json_encode($response_traders);
            $app->stop();
        }

        $result = $app->db->conn->query('SELECT * FROM user WHERE is_delete=\'0\' AND user_type = \'T\' AND is_request_trader=\'0\' AND (nickname LIKE \'%'.$app->db->conn->real_escape_string($nickname).'%\')');

        while($row = $result->fetch_array()){
            $response_traders[] = array('uid'=>$row['uid'], 'name'=>$row['name'], 'nickname'=>$row['nickname'], 'picture_s'=>$row['picture_s'], 'picture'=>$row['picture']);
        }

        echo json_encode(array('items'=>$response_traders));
    });

    // pb 검색
    $app->post('/pb_search', function() use ($app, $log) {
        $broker_id = $app->request->post('broker_id');

        $developers = $app->db->select('user', 'uid, name', array('user_type'=>'P', 'is_request_pb'=>'0', 'is_delete'=>'0', 'broker_id'=>$broker_id));

        echo json_encode($developers);
    });

    $app->get('/write', function () use ($app, $log, $isLoggedIn) {

        $topmenu = 'fund';

        switch ($_SESSION['user']['user_type']) {
            case 'T':
                $brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
                $tools_id = array();

                break;
            case 'P';
                $brokers = $app->db->selectOne('broker', '*', array('broker_id'=>$_SESSION['user']['broker_id']));
// __v($brokers);
                $tools_id = $app->db->select('system_trading_tool', 'tool_id, name', array('broker_id'=>$_SESSION['user']['broker_id']));
// __v($item_id);
                break;
            case 'A';
                $brokers = $app->db->select('broker', '*', array(), array('sorting'=>'asc'));
                $tools_id = array();

                break;
            default: 
                alert('트레이더, PB 회원만 등록이 가능합니다.');
                $app->stop();
        }

        $items = $app->db->select('item', '*', array(), array('sorting'=>'asc'));
        $kinds = $app->db->select('kind', '*', array(), array('sorting'=>'asc'));
        $types = $app->db->select('type', '*', array(), array('sorting'=>'asc'));

        $fund_price = $app->config('strategy.min_price');

        $app->render('fund/strategy_write.php', array('topmenu'=>$topmenu, 'items'=>$items, 'kinds'=>$kinds, 'types'=>$types, 'brokers'=>$brokers, 'tools_id'=>$tools_id, 'fund_price'=>$fund_price));
    });

    $app->post('/write', function () use ($app, $log) {

        $name = $app->request->post('name');
        $broker_id = $app->request->post('broker_id');
        $input_item_ids = $app->request->post('item_ids');
        $tool_id = $app->request->post('tool_id');
        $currency = $app->request->post('currency');
        $term = $app->request->post('term');
        $intro = $app->request->post('intro');
        $strategy_type = $app->request->post('strategy_type');
        $strategy_kind = $app->request->post('strategy_kind');
        $min_price = $app->request->post('min_price');
        $pb_uid = $app->request->post('pb_uid');
        $trader_uid = $app->request->post('trader_uid');

        if ($_SESSION['user']['user_type'] == 'T') {
            $trader_uid = $_SESSION['user']['uid'];
        }
        
        if ($_SESSION['user']['user_type'] == 'P') {
            // pb
            if (!$trader_uid) {
                $trader_uid = $_SESSION['user']['uid'];
            }
            $pb_uid = $_SESSION['user']['uid'];
        }

        if ($_SESSION['user']['user_type'] == 'A') {
            
            if (!$trader_uid) {
                $trader_uid = $_SESSION['user']['uid'];
            }
        }

        $investment = $app->request->post('investment');

        if (empty($strategy_type)) {
            $strategy_type = 'M';
        }

        $brokers = $app->db->selectOne('broker', 'company_type', array('broker_id'=> $broker_id));
        $broker_type = $brokers['company_type'];

        if(empty($currency)){
            $currency = 'KRW';
        }

        if(empty($investment)){
            $investment = 0;
        }else{
            $investment = intval(str_replace(',', '', $investment));
        }

        if(empty($term)){
            $term = 'day';
        }

        if(empty($intro)){
            $intro = '';
        }

        if(empty($min_price)){
            $min_price = 0;
        }

        // 업로드 된 파일이 있는지 확인
        if ($_FILES['attach_file']['name'] && is_uploaded_file($_FILES['attach_file']['tmp_name'])) {
            $savePath = $app->config('strategy.path');

            $filename = $_FILES['attach_file']['name'];
            $filesize = $_FILES['attach_file']['size'];
            $filetmpname = $_FILES['attach_file']['tmp_name'];
            $filetype = $_FILES['attach_file']['type'];
            $tmpfileext = explode('.', $filename);
            $fileext = $tmpfileext[count($tmpfileext)-1];

            // filename modify
            $saveFilename = md5(uniqid(rand(), true)).'.'.$fileext;

            // filename same check
            while(file_exists($savePath.'/'.$saveFilename)){
                $saveFilename = md5(uniqid(rand(), true));
            }

            $finalFilename = $savePath.'/'.$saveFilename;

            if(!move_uploaded_file($filetmpname, $finalFilename)) {
                alert('업로드에 실패하였습니다');
                $app->stop();
            }

            $isUpload = true;
        }

        $param = array (
            'name'=>$name,
            'broker_type'=>$broker_type,
            'broker_id'=>$broker_id,
            'developer_uid'=>$trader_uid,
            //'developer_uid'=>$_SESSION['user']['uid'],
            'developer_name'=> '',
            'tool_id'=>$tool_id,
            'currency'=>$currency,
            'investment'=>$investment,
            'strategy_type'=>$strategy_type,
            'strategy_term'=>$term,
            'strategy_kind'=>$strategy_kind,
            'intro'=>$intro,
            'is_open'=>'0',
            'is_operate'=>'0',
            'min_price'=>$min_price,
            'pb_uid'=> ($pb_uid) ? $pb_uid : 0,
            'trader_uid'=> $trader_uid
        );

        $new_strategy_id = $app->db->insert('strategy', $param);

        foreach ($input_item_ids as $v) {
            if(empty($v)) continue;
            $app->db->insert('strategy_item', array(
                'strategy_id'=>$new_strategy_id,
                'item_id'=>$v
            ));
        }

        if ($isUpload) {
            $app->db->insert('strategy_file', array(
                'strategy_id'=>$new_strategy_id,
                'file_name'=>$filename,
                'save_name'=>$saveFilename)
            );
        }

        $app->flash('regist_complete', 'true');
        $app->redirect('/strategies/'.$new_strategy_id);

    });
});
