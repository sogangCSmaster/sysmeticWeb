<?php
/**
 * PB 회원 전용 메뉴
 */

// 라운지
$app->group('/pb', function() use ($app, $log, $isLoggedIn, $authenticateForRole) {


    // 게시판
    $app->get('/bbs', function() use ($app, $log, $isLoggedIn) {

        $field = $app->request->get('field');
        $keyword = $app->request->get('keyword');
        $page = $app->request->get('page');
        $page = ($page) ? $page : 1;
        $limit = 10;
        $start = ($page - 1) * $limit;
        $page_count = 10;
        $page_start = (ceil($page / $page_count) - 1) * $page_count + 1;

        if ($field && $keyword) {
			if($field=="name" or $field=="company"){
	            $where = " WHERE b.{$field} LIKE '%".$app->db->conn->real_escape_string($keyword)."%'";
			}else{
	            $where = " WHERE a.{$field} LIKE '%".$app->db->conn->real_escape_string($keyword)."%'";
			}
        }

        // 컨텐츠 개수
        $sql = "SELECT count(*)
                FROM 
                    pb_board a INNER JOIN user b ON a.uid=b.uid
                $where";
        $result = $app->db->conn->query($sql);
        $row = $result->fetch_array();
        $total = $row[0];

        $lists = array();
        $sql = "SELECT a.*, b.*, (select count(*) from pb_board_file where bid=a.bid) as filecnt
                FROM 
                    pb_board a INNER JOIN user b ON a.uid=b.uid
                $where
                ORDER BY a.bid DESC limit $start, $limit";

        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_array()) {
            $lists[] = $row;
        }

        $paging = getPaging($page, $total, $page_start, $limit, $page_count, 'more');

        $param = array(
            'total' => $total,
            'lists' => $lists,
            'field' => $field,
            'keyword' => $keyword,
            'page'  => $page,
            'paging'=> $paging,
        );

        $app->render('pb/pb_board.php', $param);
    });


    // 게시판 쓰기
    $app->get('/bbs/write', function() use ($app, $log, $isLoggedIn) {

        $info['images'] = array();
        $info['files'] = array();
        
        $param = array(
            'info'  => $info,
        );

        $app->render('pb/pb_board_write.php', $param);
    });

    // 게시판 쓰기
    $app->post('/bbs/write', function() use ($app, $log, $isLoggedIn) {

        $subject = $app->request->post('subject');
        $contents = $app->request->post('contents');
        $attach_images_filename = $app->request->post('attach_images_filename');
        $attach_images_savename = $app->request->post('attach_images_savename');
        $attach_files_filename = $app->request->post('attach_files_filename');
        $attach_files_savename = $app->request->post('attach_files_savename');


        $param = array(
            'uid'  => $_SESSION['user']['uid'],
            'subject' => $subject,
            'contents'=> $contents,
        );
        
        $bid = $app->db->insert('pb_board', $param);

        if (is_array($attach_images_filename)) {
            foreach ($attach_images_filename as $k => $v) {
                $param = array(
                    'bid'  => $bid,
                    'file_type' => 'IMG',
                    'save_name' => $attach_images_savename[$k],
                    'file_name' => $attach_images_filename[$k],
                );

                $app->db->insert('pb_board_file', $param);
            }
        }
        
        if (is_array($attach_files_filename)) {
            foreach ($attach_files_filename as $k => $v) {

                $param = array(
                    'bid'  => $bid,
                    'file_type' => 'FILE',
                    'save_name' => $attach_files_savename[$k],
                    'file_name' => $attach_files_filename[$k],
                );

                $app->db->insert('pb_board_file', $param);
            }
        }

		//PB게시판 글 등록시 PB회원모두에게 SMS발송
        $sql = "SELECT mobile, (select name from where uid=".$_SESSION['user']['uid']." limit 1) as t_name FROM user WHERE user_type = 'P' ORDER BY uid desc";
        $result = $app->db->conn->query($sql);
        while ($row = $result->fetch_array()) {
			//문자발송
			if($row[mobile]){
				$SMSINFO['smsMsg']="시스메틱 PB게시판에 ".$row[t_name]." PB의 새글이 게시되었습니다.";
				$SMSINFO['smsHp']=$row[mobile];
				sendSMS($SMSINFO);
			}
        }


        $app->redirect('/pb/bbs');
    });



    // 게시판 수정
    $app->get('/bbs/:bid/modify', function($bid) use ($app, $log, $isLoggedIn) {

        $info = $app->db->selectOne('pb_board', '*', array('bid'=>$bid));
        $mine = ($info['uid'] == $_SESSION['user']['uid'] or $_SESSION['user']['user_type']=='A') ? true : false;

        $images = array();
        $files = array();
        $info['images'] = $app->db->select('pb_board_file', '*', array('bid'=>$bid, 'file_type'=>'IMG'));
        $info['files'] = $app->db->select('pb_board_file', '*', array('bid'=>$bid, 'file_type'=>'FILE'));
        
        $param = array(
            'info' => $info,
            'mine' => $mine,
        );

        $app->render('pb/pb_board_write.php', $param);
    });

    // 게시판 수정
    $app->post('/bbs/:bid/modify', function($bid) use ($app, $log, $isLoggedIn) {

        $subject = $app->request->post('subject');
        $contents = $app->request->post('contents');
        $attach_images_filename = $app->request->post('attach_images_filename');
        $attach_images_savename = $app->request->post('attach_images_savename');
        $attach_files_filename = $app->request->post('attach_files_filename');
        $attach_files_savename = $app->request->post('attach_files_savename');

        $param = array(
            'subject' => $subject,
            'contents'=> $contents,
        );
        
        //$app->db->update('pb_board', $param, array('bid'=>$bid, 'uid'=>$_SESSION['user']['uid']));
        $app->db->update('pb_board', $param, array('bid'=>$bid));
		 

        $app->db->delete('pb_board_file', array('bid'=>$bid, 'file_type'=>'IMG'));
        if (is_array($attach_images_filename)) {
            foreach ($attach_images_filename as $k => $v) {
                $param = array(
                    'bid'  => $bid,
                    'file_type' => 'IMG',
                    'save_name' => $attach_images_savename[$k],
                    'file_name' => $attach_images_filename[$k],
                );

                $app->db->insert('pb_board_file', $param);
            }
        }
        
        $app->db->delete('pb_board_file', array('bid'=>$bid, 'file_type'=>'FILE'));
        if (is_array($attach_files_filename)) {

            foreach ($attach_files_filename as $k => $v) {

                $param = array(
                    'bid'  => $bid,
                    'file_type' => 'FILE',
                    'save_name' => $attach_files_savename[$k],
                    'file_name' => $attach_files_filename[$k],
                );

                $app->db->insert('pb_board_file', $param);
            }
        }

        $app->redirect('/pb/bbs/'.$bid);
    });


    // 게시판 삭제
    $app->get('/bbs/:bid/delete', function($bid) use ($app, $log, $isLoggedIn) {

        //$app->db->delete('pb_board', array('bid'=>$bid, 'uid'=>$_SESSION['user']['uid']));
		$app->db->delete('pb_board', array('bid'=>$bid));

        $savePath = $app->config('data.path').'/contents/';
        $info['images'] = $app->db->select('pb_board_file', '*', array('bid'=>$bid, 'file_type'=>'IMG'));
        foreach ($info['images'] as $v) {
            @unlink($savePath.$v['save_name']); 
        }

        $info['files'] = $app->db->select('pb_board_file', '*', array('bid'=>$bid, 'file_type'=>'FILE'));
        foreach ($info['files'] as $v) {
            @unlink($savePath.$v['save_name']); 
        }
        $app->db->delete('pb_board_file', array('bid'=>$bid));

        $app->redirect('/pb/bbs');
    });


    // 게시판 파일
    $app->post('/bbs/delete_file', function() use ($app, $log, $isLoggedIn) {

        $savePath = $app->config('data.path').'/contents/';
        @unlink($savePath.$app->request->post('savename'));

        echo "success";
    });

    // 게시판 파일
    $app->post('/bbs/upload_images', function() use ($app, $log, $isLoggedIn) {

        $savePath = $app->config('data.path').'/contents/';
        $max_file_size = 1024 * 1024 * 5;

        // 업로드 된 파일이 있는지 확인
        switch($_FILES['images']['error']){
            case UPLOAD_ERR_OK:
                $filename = $_FILES['images']['name'];
                $filesize = $_FILES['images']['size'];
                $filetmpname = $_FILES['images']['tmp_name'];
                $filetype = $_FILES['images']['type'];
                $tmpfileext = explode('.', $filename);
                $fileext = $tmpfileext[count($tmpfileext)-1];


                if(strpos($filetype, 'image') === false){
                    $result['success'] = false;
                    $result['msg'] = '이미지 파일만 업로드 가능합니다';
                    echo json_encode($result);
                    $app->stop();
                }

                if(!preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)){
                    $result['success'] = false;
                    $result['msg'] = '확장자가 jpg, gif, png 파일만 업로드가 가능합니다';
                    echo json_encode($result);
                    $app->stop();
                }

                if($filesize > $max_file_size){
                    $result['success'] = false;
                    $result['msg'] = '파일은 5MB 이하로 업로드해주세요';
                    echo json_encode($result);
                    $app->stop();
                }

                if(!is_uploaded_file($filetmpname)){
                    $result['success'] = false;
                    $result['msg'] = '정상적인 방법으로 업로드해주세요';
                    echo json_encode($result);
                    $app->stop();
                }

                if(!is_dir($savePath)){
                    mkdir($savePath, 0705);
                    chmod($savePath, 0707);
                }

                $saveFilename = md5(uniqid(rand(), true));
                while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
                    $saveFilename = md5(uniqid(rand(), true));
                }

                $finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
                if(!move_uploaded_file($filetmpname, $finalFilename)){
                    $result['success'] = false;
                    $result['msg'] = '업로드에 실패하였습니다';
                    echo json_encode($result);
                    $app->stop();
                }
                
                $result['success'] = true;
                $result['filename'] = $filename;
                $result['savename'] = $saveFilename.'.'.$fileext;
                $result['filesize'] = $filesize;

		        echo json_encode($result);
                $app->stop();
                break;

            case UPLOAD_ERR_INI_SIZE:
                $result['success'] = false;
                $result['msg'] = '업로드 가능 용량을 초과하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $result['success'] = false;
                $result['msg'] = '업로드 가능 용량을 초과하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_PARTIAL:
                $result['success'] = false;
                $result['msg'] = '업로드에 실패하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_NO_FILE:
                $result['success'] = false;
                $result['msg'] = '첨부된 파일이 없습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            default:
                $result['success'] = false;
                $result['msg'] = '업로드에 실패하였습니다';
		        echo json_encode($result);
                $app->stop();
        }

    });

    // 게시판 파일
    $app->post('/bbs/upload_files', function() use ($app, $log, $isLoggedIn) {

        $savePath = $app->config('data.path').'/contents/';
        $max_file_size = 1024 * 1024 * 5;

        // 업로드 된 파일이 있는지 확인
        switch($_FILES['files']['error']){
            case UPLOAD_ERR_OK:
                $filename = $_FILES['files']['name'];
                $filesize = $_FILES['files']['size'];
                $filetmpname = $_FILES['files']['tmp_name'];
                $filetype = $_FILES['files']['type'];
                $tmpfileext = explode('.', $filename);
                $fileext = $tmpfileext[count($tmpfileext)-1];

                if($filesize > $max_file_size){
                    $result['success'] = false;
                    $result['msg'] = '첨부파일은 5MB 이하로 업로드해주세요';
                    echo json_encode($result);
                    $app->stop();
                }

                if(!is_uploaded_file($filetmpname)){
                    $result['success'] = false;
                    $result['msg'] = '정상적인 방법으로 업로드해주세요';
                    echo json_encode($result);
                    $app->stop();
                }


                if(!is_dir($savePath)){
                    mkdir($savePath, 0705);
                    chmod($savePath, 0707);
                }

                $saveFilename = md5(uniqid(rand(), true));
                while(file_exists($savePath.'/'.$saveFilename.'.'.$fileext)){
                    $saveFilename = md5(uniqid(rand(), true));
                }

                $finalFilename = $savePath.'/'.$saveFilename.'.'.$fileext;
                if(!move_uploaded_file($filetmpname, $finalFilename)){
                    $result['success'] = false;
                    $result['msg'] = '업로드에 실패하였습니다';
                    echo json_encode($result);
                    $app->stop();
                }
                
                $result['success'] = true;
                $result['filename'] = $filename;
                $result['savename'] = $saveFilename.'.'.$fileext;
                $result['filesize'] = $filesize;

		        echo json_encode($result);
                $app->stop();
                break;

            case UPLOAD_ERR_INI_SIZE:
                $result['success'] = false;
                $result['msg'] = '업로드 가능 용량을 초과하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $result['success'] = false;
                $result['msg'] = '업로드 가능 용량을 초과하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_PARTIAL:
                $result['success'] = false;
                $result['msg'] = '업로드에 실패하였습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            case UPLOAD_ERR_NO_FILE:
                $result['success'] = false;
                $result['msg'] = '첨부된 파일이 없습니다';
		        echo json_encode($result);
                $app->stop();
                break;
            default:
                $result['success'] = false;
                $result['msg'] = '업로드에 실패하였습니다';
		        echo json_encode($result);
                $app->stop();
        }

    });

    // 게시판 내용
    $app->get('/bbs/:bid', function($bid) use ($app, $log, $isLoggedIn) {

        $info = $app->db->selectOne('pb_board', '*', array('bid'=>$bid));
        $info['user'] = $app->db->selectOne('user', '*', array('uid'=>$info['uid']));
        $images = array();
        $files = array();
        $info['images'] = $app->db->select('pb_board_file', '*', array('bid'=>$bid, 'file_type'=>'IMG'));
        $info['files'] = $app->db->select('pb_board_file', '*', array('bid'=>$bid, 'file_type'=>'FILE'));
        
        $param = array(
            'info' => $info,
            'mine' => ($info['uid'] == $_SESSION['user']['uid']) ? true : false,
        );

        $app->render('pb/pb_board_detail.php', $param);
    });


    // 게시판 코멘트
    $app->get('/bbs/:bid/reply', function($bid) use ($app, $log, $isLoggedIn) {

        $count = $app->db->selectCount('pb_board_comment', array('bid'=>$bid));

        $param = array(
            'count' => $count,
            'bid' => $bid
        );

        $app->render('pb/pb_board_comment.php', $param);
    });

    // 게시판 코멘트
    $app->post('/bbs/:bid/reply/write', function($bid) use ($app, $log, $isLoggedIn) {

        $data = array(
                    'bid' => $bid,
                    'uid'  => $_SESSION['user']['uid'],
                    'contents' => $app->request->post('contents'),
                    'secret' => $app->request->post('secret') ? $app->request->post('secret') : '0',
                );

        if ($app->db->insert('pb_board_comment', $data)) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['msg'] = '처리중 오류가 발생하였습니다';
        }

		echo json_encode($result);
    });

    // 게시판 코멘트
    $app->post('/bbs/:bid/reply/modify', function($bid) use ($app, $log, $isLoggedIn) {

        $data = array(
                    'contents' => $app->request->post('contents'),
                );

        if ($app->db->update('pb_board_comment', $data, array('cid'=>$app->request->post('cid'), 'uid'=>$_SESSION['user']['uid']))) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['msg'] = '처리중 오류가 발생하였습니다';
        }

		echo json_encode($result);
    });

    // 게시판 코멘트
    $app->post('/bbs/:bid/reply/delete', function($bid) use ($app, $log, $isLoggedIn) {

        if ($app->db->delete('pb_board_comment', array('cid'=>$app->request->post('cid'), 'uid'=>$_SESSION['user']['uid']))) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['msg'] = '처리중 오류가 발생하였습니다';
        }

		echo json_encode($result);
    });

    // 게시판 코멘트
    $app->get('/bbs/:bid/reply_list', function($bid) use ($app, $log, $isLoggedIn) {

        $page = $app->request->get('page');
        $page = ($page) ? $page : 1;
        $limit = 10;
        $start = ($page - 1) * $limit;

        $sql = "SELECT a.*, b.nickname, b.picture, b.user_type
                FROM pb_board_comment a INNER JOIN user b ON a.uid = b.uid
                WHERE a.bid = '$bid'
                ORDER BY cid ASC
                LIMIT $start, $limit";

        $result = $app->db->conn->query($sql);

        $lists = array();
        while ($row = $result->fetch_array()) {
            $lists[] = $row;
        }

        $info = $app->db->selectOne('pb_board', '*', array('bid'=>$bid));

        $param = array(
            'lists' => $lists,
            'info'  => $info
        );

        $app->render('pb/pb_board_comment_list.php', $param);
    });

    // 다운로드
    $app->get('/bbs/download/:fid', function ($fid) use ($app, $log, $isLoggedIn) {

        $files = $app->db->selectOne('pb_board_file', '*', array('fid'=>$fid));

        if(empty($files)){
            $app->halt(404, 'not found');
        }


        $savePath = $app->config('data.path').'/contents/';
        $filepath = $savePath.'/'.$files['save_name'];
        $filesize = filesize($filepath);
        $filename = urlencode($files['file_name']);

        $app->response->headers->set('Content-Type', 'application/octet-stream');
        $app->response->headers->set('Pragma', 'dummy=bogus');
        $app->response->headers->set('Cache-Control', 'private');
        $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $app->response->headers->set('Content-Transfer-Encoding', 'binary');
        $app->response->headers->set('Content-Length', $filesize);

        readfile($filepath);
    });

});
