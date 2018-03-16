<?php
/**
 * cs
 */

$app->group('/cs', function() use ($app, $log) {

    $app->get('/notice', function() use ($app, $log) {

        $submenu = 'notice';

        $sql = "SELECT COUNT(*) FROM notice WHERE open_date < now()";
        $result = $app->db->conn->query($sql);
        $row = $result->fetch_array();
        $total = $row[0];

        $param = array(
            'submenu'   => $submenu,
            'total'     => $total,
        );

        $app->render('cs/notice.php', $param);

    });


    $app->get('/notice/list', function() use ($app, $log) {

        $count = (!$app->request->get('count')) ? 10 : $app->request->get('count');
        $page = $app->request->get('page');
        if (empty($page) || !is_numeric($page)) $page = 1;
        $start = ($page - 1) * $count;

        $sql = "SELECT *, (select count(*) from notice_file where notice_id=notice.notice_id) as filecnt  FROM notice WHERE open_date < now() ORDER BY reg_at DESC LIMIT $start, $count";
        
        $lists = array();
      // echo $sql;
        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $lists[] = $row;
        }

        $param = array(
            'lists'    => $lists
        );

        $app->render('cs/notice_list.php', $param);
    });
    

    $app->get('/notice/:id', function($id) use ($app, $log) {

        $submenu = 'notice';

        $info = $app->db->selectOne("notice", "*", array('notice_id'=>$id));
        $imgs = $app->db->select("notice_file", "*", array('notice_id'=>$id, 'file_type'=>'IMG'));
        $files = $app->db->select("notice_file", "*", array('notice_id'=>$id, 'file_type'=>'FILE'));

        $param = array(
            'submenu'   => $submenu,
            'info'     => $info,
            'imgs'      => $imgs,
            'files'     => $files,
        );

        $app->render('cs/notice_view.php', $param);
    });
    


    $app->get('/faq', function() use ($app, $log) {

        $submenu = 'faq';
        
        $cate_id = $app->request->get('cate_id');
        $keyword = $app->request->get('keyword');

        $sql = "SELECT COUNT(*) FROM faq";
        if ($cate_id) {
            $sql .= ' WHERE cate_id = '.$cate_id;
        }

        $result = $app->db->conn->query($sql);
        $row = $result->fetch_array();
        $total = $row[0];
        

        $category = $app->db->select('faq_category', '*', array(), array('sorting'=>'asc'));

        $param = array(
            'submenu'   => $submenu,
            'category'  => $category,
            'total'     => $total,
            'cate_id'   => $cate_id,
            'keyword'   => $keyword,
        );

        $app->render('cs/faq.php', $param);

    });


    $app->get('/faq/list', function() use ($app, $log) {

        $count = (!$app->request->get('count')) ? 10 : $app->request->get('count');
        $page = $app->request->get('page');
        if (empty($page) || !is_numeric($page)) $page = 1;
        $start = ($page - 1) * $count;

        $keyword = $app->request->get('keyword');
        $cate_id = $app->request->get('cate_id');
        if ($cate_id) {
            $where = ' WHERE a.cate_id = '.$cate_id;
        }

        if ($keyword) {
            $where .= ($where) ? ' AND ' : ' WHERE ';
            $where .= 'subject like "%'.$app->db->conn->real_escape_string($keyword).'%"';
        }

        $sql = "SELECT a.*, b.name FROM faq a INNER JOIN faq_category b ON a.cate_id=b.cate_id $where ORDER BY a.reg_date DESC LIMIT $start, $count";
        $lists = array();
      // echo $sql;
        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $lists[] = $row;
        }

        $param = array(
            'lists'    => $lists
        );

        $app->render('cs/faq_list.php', $param);
    });
    

    
    $app->get('/media', function() use ($app, $log) {

        $submenu = 'media';

        $total = $app->db->selectCount('media');

        $param = array(
            'submenu'   => $submenu,
            'total'     => $total,
        );

        $app->render('cs/media.php', $param);

    });


    $app->get('/media/list', function() use ($app, $log) {

        $count = (!$app->request->get('count')) ? 10 : $app->request->get('count');
        $page = $app->request->get('page');
        if (empty($page) || !is_numeric($page)) $page = 1;
        $start = ($page - 1) * $count;
        
        $lists = $app->db->select('media', '*,(select count(*) from media_file where midx=media.midx) as filecnt ', array(), array('reg_date'=>'desc'), $start, $count);

        $param = array(
            'lists'    => $lists
        );

        $app->render('cs/media_list.php', $param);
    });
    

    $app->get('/media/:id', function($id) use ($app, $log) {

        $submenu = 'media';

        $info = $app->db->selectOne("media", "*", array('midx'=>$id));
        $imgs = $app->db->select("media_file", "*", array('midx'=>$id, 'file_type'=>'IMG'));
        $files = $app->db->select("media_file", "*", array('midx'=>$id, 'file_type'=>'FILE'));

        $param = array(
            'submenu'   => $submenu,
            'info'     => $info,
            'imgs'      => $imgs,
            'files'     => $files,
        );

        $app->render('cs/media_view.php', $param);
    });



    
    $app->get('/education', function() use ($app, $log) {

        $submenu = 'education';
        
        $type = $app->request->get('type');
        $type = ($type) ? $type : 'ON';

        $total = $app->db->selectCount('education', array('type'=>$type));

        $param = array(
            'submenu'   => $submenu,
            'type'      => $type,
            'total'     => $total,
        );

        $app->render('cs/education.php', $param);

    });


    $app->get('/education/list', function() use ($app, $log) {

        $count = (!$app->request->get('count')) ? 8 : $app->request->get('count');
        $page = $app->request->get('page');
        $type = $app->request->get('type');

        if (empty($page) || !is_numeric($page)) $page = 1;
        $start = ($page - 1) * $count;
        
        $sql = "SELECT a.*, b.save_name FROM education a LEFT JOIN education_file b ON a.eidx=b.eidx WHERE a.type='$type' ORDER BY a.reg_date DESC LIMIT $start, $count";
        $lists = array();
      // echo $sql;
        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $lists[] = $row;
        }

        //$lists = $app->db->select('education', '*', array('type'=>$type), array('reg_date'=>'desc'), $start, $count);

        $param = array(
            'lists'    => $lists
        );

        $file = ($type == 'ON') ? 'education_online.php' : 'education_offline.php';
        $app->render('cs/'.$file, $param);
    });
    

    $app->get('/education/:id', function($id) use ($app, $log) {

        $submenu = 'education';

        $info = $app->db->selectOne("education", "*", array('eidx'=>$id));
        $imgs = $app->db->select("education_file", "*", array('eidx'=>$id, 'file_type'=>'IMG'));
        $files = $app->db->select("education_file", "*", array('eidx'=>$id, 'file_type'=>'FILE'));

        $param = array(
            'submenu'   => $submenu,
            'info'     => $info,
            'imgs'      => $imgs,
            'files'     => $files,
        );

        $app->render('cs/education_view.php', $param);
    });


    $app->get('/guide', function() use ($app, $log) {

        $submenu = 'guide';

        $param = array(
            'submenu'   => $submenu,
        );

        $app->render('cs/guide.php', $param);
    });


    $app->get('/req', function() use ($app, $log) {

        $submenu = 'req';

        $param = array(
            'submenu'   => $submenu,
        );

        $app->render('cs/req.php', $param);
    });

    $app->post('/req', function() use ($app, $log) {

        $subject = $app->request->post('subject');
        $email = $app->request->post('email');
        $mobile = $app->request->post('mobile');
        $contents = $app->request->post('contents');
        $target = $app->request->post('target');
        $zsfCode = $app->request->post('zsfCode');
		$zsfCode = stripslashes(trim($zsfCode));
		include './zmSpamFree/zmSpamFree.php';
		/*
			zsfCheck 함수는 두 개의 인수를 사용할 수 있다.
			$_POST['zsfCode'] : 사용자가 입력한 스팸방지코드 값
			'DemoPage' : 관리자가 로그파일에 남겨놓고 싶은 메모, 예를 들어 bulletin 게시판의 comment 쓰기시 스팸방지코드를 입력했다 한다면
							'bulletin|comment'라고 써 놓으면, 어떤 게시판의 어떤 상황에서 스팸차단코드가 맞거나 틀렸는지 알 수 있을 것이다.
							이외에 '제목의 일부'나 '글 내용의 일부'를 같이 넣으면, 어떤 스팸광고글이 차단되었는지도 확인할 수 있다.
							참고로 이 인수는 생략 가능하다.
		*/
		$r = zsfCheck ( $zsfCode,'DemoPage' );	# $_POST['zsfCode']는 입력된 스팸방지코드 값이고, 'DemoPage'는 기타 기록하고픈
		$zsfCode_state = $r ? 'Y' : 'N';

		if($zsfCode_state=="N"){
			alert("보안코드가 잘못되었습니다. 다시 확인하여 주세요");
	        $app->redirect('/cs/req?error=1');
		}else{
			switch ($target) {
				case '1': $target_value_text = '사이트이용'; break;
				case '2': $target_value_text = '제휴'; break;
				case '3': $target_value_text = '기타'; break;
			}

			$param = array(
				'uid'       => $_SESSION['user']['uid'],
				'name'      => $_SESSION['user']['name'],
				'subject'   => $subject,
				'email'     => $email,
				'mobile'    => $mobile,
				'question'  => $contents,
				'target'    => $target,
				'target_value_text' => $target_value_text,
			);

			$app->db->insert('customer', $param);
			$app->redirect('/cs/req/complete');
		}


    });

    $app->get('/req/complete', function() use ($app, $log) {
        
        $submenu = 'req';

        $param = array(
            'submenu'   => $submenu,
        );

        $app->render('cs/req_complete.php', $param);
    });


    $app->get('/partners', function() use ($app, $log) {

        $submenu = 'partners';

		$brokers = $app->db->select('broker', '*', array('is_open'=>'1'), array('sorting'=>'asc'));
		foreach($brokers as $k => $broker){
			$s_tools = $app->db->select('system_trading_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
			$a_tools = $app->db->select('api_tool', '*', array('broker_id'=>$broker['broker_id']), array('tool_id'=>'asc'));
			$brokers[$k]['system_trading_tools'] = $s_tools;
			$brokers[$k]['api_tools'] = $a_tools;
		}

        $param = array(
            'submenu'   => $submenu,
            'brokers'   => $brokers,
        );

        $app->render('cs/partners.php', $param);
    });


    $app->get('/download/:table/:id', function($table, $id) use ($app, $log) {

        if ($table == 'notice') {
            $info = $app->db->selectOne("notice_file", "*", array('fid'=>$id));
            $path = $app->config('notice.path');
        }
        if ($table == 'media') {
            $info = $app->db->selectOne("media_file", "*", array('fid'=>$id));
            $path = $app->config('media.path');
        }
        if ($table == 'education') {
            $info = $app->db->selectOne("education_file", "*", array('fid'=>$id));
            $path = $app->config('education.path');
        }

        $filepath = $path.'/'.$info['save_name'];

        $filesize = filesize($filepath);
        $filename = urlencode($info['file_name']);

        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: $filesize");

        readfile($filepath);
    });
});