<?php
/**
 * 회원가입
 */

$app->group('/member', function() use ($app, $log, $isLoggedIn) {

	
    // 실명확인 추가등록
    $app->get('/mem_update', function() use ($app) {

        $email = $app->request->get('email');
		$result = $app->db->conn->query('SELECT * FROM user WHERE email="'.$email.'"');
		$row = $result->fetch_array();


        // 실명인증
        $CurTime = date(YmdHis);
        $id = $app->config('auth.userid');
        $srvNo = '008002';
        $reqNum = $CurTime.rand(100000, 999999);
        $certDate = $CurTime;
        $certGb = 'H';
        $addVar = '';
        $retUrl = '32http://'.$_SERVER['HTTP_HOST'].'/member/auth_result';
        $auth_module = $app->config('auth.path').'/';

        /************************************************************************************/
        /* reqNum 값은 최종 결과값 복호화를 위한 SecuKey로 활용 되므로 중요합니다.          */
        /* reqNum 은 본인 확인 요청시 항상 새로운 값으로 중복 되지 않게 생성 해야 합니다.    */
        /* 쿠키 또는 Session및 기타 방법을 사용해서 reqNum 값을                             */
        /* vname_result_seed.php에서 가져 올 수 있도록 해야 함.                             */
        /* 샘플을 위해서 쿠키를 사용한 것이므로 참고 하시길 바랍니다.                        */
        /************************************************************************************/
        //01. reqNum 쿠키 생성
        setcookie("REQNUM", $reqNum, time()+600);
        $exVar       = "0000000000000000";        // 확장임시 필드입니다. 수정하지 마세요..
        //02. 암호화 파라미터 생성
        $reqInfo = $id . "^" . $srvNo . "^" . $reqNum . "^" . $certDate . "^" . $certGb . "^" . $addVar . "^" . $exVar;
        //03. 본인확인 요청정보 1차암호화
        $iv = "";
        //2014.02.07 KISA 권고사항
        //위 변조 및, 불법 시도 차단을 위하여 아래 패턴에 해당하는 문자열만 허용
        // if(preg_match('~[^0-9a-zA-Z+/=^]~', $reqInfo, $matches)){
        //     echo "입력 값 확인이 필요합니다.(req)"; exit;
        // }

        $enc_reqInfo = exec($app->config('auth.path')."/SciSecuX SEED 1 1 $reqInfo ");//암호화모듈 설치시 생성된 SciSecuX 파일이 있는 리눅스 경로를 설정해주세요.
        //04. 요청정보 위변조검증값 생성
        $hmac_str = exec($app->config('auth.path')."/SciSecuX HMAC 1 1 $enc_reqInfo ");
        //05. 요청정보 2차암호화
        //데이터 생성 규칙 : "요청정보 1차 암호화^위변조검증값^암복화 확장 변수"
        $enc_reqInfo = $enc_reqInfo. "^" .$hmac_str. "^" ."0000000000000000";
        $enc_reqInfo = exec($app->config('auth.path')."/SciSecuX SEED 1 1 $enc_reqInfo ");

        // $param['reqInfo'] = $reqInfo;
        $param['enc_reqInfo'] = $enc_reqInfo;
        $param['retUrl'] = $retUrl;
        $param['email'] = $email;

        $app->render("member/mem_update.php", $param);
    });
    $app->post('/mem_update', function() use ($app) {

        $email = $app->request->post('email');
        $name = $app->request->post('name');
        $password = $app->request->post('password');
        $mobile = $app->request->post('mobile');

		$password_hash = create_hash($password);

		$result = $app->db->conn->query('update user set name="'.$name.'", nickname="'.$name.'", user_password="'.$password_hash.'", mobile="'.$mobile.'", live_name_chk="Y" WHERE email="'.$email.'"');
		//$row = $result->fetch_array();

		$app->flash('error', '실명인증이 완료되었습니다. 감사합니다.');
		$app->redirect('/signin');
    });

    // pjh 회원가입유형 선택
    $app->get('/join_select', function() use ($app) {

        $type = $app->request->get('type');
        $type = (empty($type)) ? 'N' : $type;

        $platform = $app->request->get('platform');
        if(empty($platform)){
            $platform = '';
        }

        $app->render('member/join_select.php', array('type'=>$type, 'platform'=>$platform));
    });

    // 약관동의
    $app->get('/agree', function() use ($app) {

        $type = $app->request->get('type');
        $type = setUserType($type);

        $platform = $app->request->get('platform');
        if(empty($platform)){
            $platform = '';
        }

        $app->render('member/agree.php', array('type'=>$type, 'platform'=>$platform));
    });

    $app->post('/agree_ok', function() use ($app) {

        $type = $app->request->post('type');
        $agree1 = $app->request->post('agree1');
        $agree2 = $app->request->post('agree2');
        $agree3 = $app->request->post('agree3');
        $platform = $app->request->post('platform');

        if(!empty($agree1) && !empty($agree2)){
            $_SESSION['agree'] = true;
        }else{
            $app->redirect('/member/join_select');
        }

        if(empty($platform)){
            $app->redirect('/member/signup?type='.$type);
        }else{
            $app->redirect('/member/signup/'.$platform);
        }
    });

    // 회원가입 폼
    $app->get('/signup', function() use ($app) {

        if(empty($_SESSION['agree'])){
            $app->redirect('/member/join_select');
        }

        $type = $app->request->get('type');
        $type = setUserType($type);

        $param['type'] = $type;

        if ($type == 'P') {
            $param['brokers'] = $app->db->select('broker', 'broker_id, company', array(), array('company'=>'asc'));
        }

        // 실명인증
        $CurTime = date(YmdHis);
        $id = $app->config('auth.userid');
        $srvNo = '008001';
        $reqNum = $CurTime.rand(100000, 999999);
        $certDate = $CurTime;
        $certGb = 'H';
        $addVar = '';
        $retUrl = '32http://'.$_SERVER['HTTP_HOST'].'/member/auth_result';
        $auth_module = $app->config('auth.path').'/';

        /************************************************************************************/
        /* reqNum 값은 최종 결과값 복호화를 위한 SecuKey로 활용 되므로 중요합니다.          */
        /* reqNum 은 본인 확인 요청시 항상 새로운 값으로 중복 되지 않게 생성 해야 합니다.    */
        /* 쿠키 또는 Session및 기타 방법을 사용해서 reqNum 값을                             */
        /* vname_result_seed.php에서 가져 올 수 있도록 해야 함.                             */
        /* 샘플을 위해서 쿠키를 사용한 것이므로 참고 하시길 바랍니다.                        */
        /************************************************************************************/
        //01. reqNum 쿠키 생성
        setcookie("REQNUM", $reqNum, time()+600);
        $exVar       = "0000000000000000";        // 확장임시 필드입니다. 수정하지 마세요..
        //02. 암호화 파라미터 생성
        $reqInfo = $id . "^" . $srvNo . "^" . $reqNum . "^" . $certDate . "^" . $certGb . "^" . $addVar . "^" . $exVar;
        //03. 본인확인 요청정보 1차암호화
        $iv = "";
        //2014.02.07 KISA 권고사항
        //위 변조 및, 불법 시도 차단을 위하여 아래 패턴에 해당하는 문자열만 허용
        // if(preg_match('~[^0-9a-zA-Z+/=^]~', $reqInfo, $matches)){
        //     echo "입력 값 확인이 필요합니다.(req)"; exit;
        // }

        $enc_reqInfo = exec($app->config('auth.path')."/SciSecuX SEED 1 1 $reqInfo ");//암호화모듈 설치시 생성된 SciSecuX 파일이 있는 리눅스 경로를 설정해주세요.
        //04. 요청정보 위변조검증값 생성
        $hmac_str = exec($app->config('auth.path')."/SciSecuX HMAC 1 1 $enc_reqInfo ");
        //05. 요청정보 2차암호화
        //데이터 생성 규칙 : "요청정보 1차 암호화^위변조검증값^암복화 확장 변수"
        $enc_reqInfo = $enc_reqInfo. "^" .$hmac_str. "^" ."0000000000000000";
        $enc_reqInfo = exec($app->config('auth.path')."/SciSecuX SEED 1 1 $enc_reqInfo ");

        // $param['reqInfo'] = $reqInfo;
        $param['enc_reqInfo'] = $enc_reqInfo;
        $param['retUrl'] = $retUrl;

        $app->render("member/join.php", $param);
    });

    // 이메일 등록여부
    $app->post('/email_check', function() use ($app, $isLoggedIn) {
        $email = $app->request->post('email');

        $is_exist = false;

        if(!empty($email)){
            $exist_member = $app->db->selectCount('user', array('email'=>$email));
            if($exist_member > 0){
                $is_exist = true;
            }
        }

        echo json_encode(array('result'=>$is_exist));
    });

    // 실명인증 결과 수신
    $app->post('/auth_result', function() use ($app, $log) {

        $iv = "";
        if (isset($_COOKIE["REQNUM"])) {
            $iv = $_COOKIE["REQNUM"];
            //쿠키 삭제
            setcookie("REQNUM", "", time()-600);
        } else {
           /* <script language=javascript>
                alert("세션이 만료되었습니다.!!");
            </script>*/
        }

        // 파라메터로 받은 요청결과
        $enc_retInfo = $app->request->post("retInfo");

        //02. 요청결과 복호화
        //2014.02.07 KISA 권고사항
        //위 변조 및, 불법 시도 차단을 위하여 아래 패턴에 해당하는 문자열만 허용
        if (preg_match('~[^0-9a-zA-Z+/=^]~', $iv, $matches) || preg_match('~[^0-9a-zA-Z+/=^]~', $enc_retInfo, $matches)) {
            // echo "입력 값 확인이 필요합니다.(res-1)"; exit;
        }

        $dec_retInfo = exec($app->config('auth.path')."/SciSecuX SEED 2 0 $iv $enc_retInfo ");//암호화모듈 설치시 생성된 SciSecuX 파일이 있는 리눅스 경로를 설정해주세요.

        /*
        [본인확인 결과 수신 Sample-PHP] <br> <br>

        [복호화 하기전 수신값] <br><br>
        retInfo : <? echo $enc_retInfo ?> <br>
        */
        //데이터 조합 : "본인확인1차암호화값/위변조검증값/암복화확장변수"
        $totInfo = explode("^", $dec_retInfo);

        $encPara  = $totInfo[0];        //본인확인1차암호화값
        $encMsg   = $totInfo[1];        //암호화된 통합 파라미터의 위변조검증값

        //03. 위변조검증값 생성
        //2014.02.07 KISA 권고사항
        //위 변조 및, 불법 시도 차단을 위하여 아래 패턴에 해당하는 문자열만 허용
        if (preg_match('~[^0-9a-zA-Z+/=^]~', $encPara, $matches)) {
            // echo "입력 값 확인이 필요합니다.(res-2)"; exit;
        }

        $hmac_str = exec($app->config('auth.path')."/SciSecuX HMAC 1 0 $encPara ");

        if ($hmac_str != $encMsg) {
            /*
            <script language=javascript>
                alert("비정상적인 접근입니다.!!");
            </script>
            <a href="http://.../pcc_V3_input_seed.php">[Back]</a>
            */
        }

        //04. 본인확인1차암호화값 복호화
        //2014.02.07 KISA 권고사항
        //위 변조 및, 불법 시도 차단을 위하여 아래 패턴에 해당하는 문자열만 허용
        if (preg_match('~[^0-9a-zA-Z+/=^]~', $iv, $matches) || preg_match('~[^0-9a-zA-Z+/=^]~', $encPara, $matches)){
            // echo "입력 값 확인이 필요합니다.(res-3)"; exit;
        }
        $decPara = exec($app->config('auth.path')."/SciSecuX SEED 2 0 $iv $encPara ");

        //05. 파라미터 분리
        $split_dec_retInfo = explode("^", $decPara);

        $name       = $split_dec_retInfo[0];        //성명
        $birYMD     = $split_dec_retInfo[1];        //생년월일
        $sex        = $split_dec_retInfo[2];        //성별
        $fgnGbn     = $split_dec_retInfo[3];        //내외국인 구분값
        $di         = $split_dec_retInfo[4];        //DI
        $ci1        = $split_dec_retInfo[5];        //CI1
        $ci2        = $split_dec_retInfo[6];        //CI2
        $civersion  = $split_dec_retInfo[7];        //CI Version
        $reqNum     = $split_dec_retInfo[8];        //요청번호
        $result     = $split_dec_retInfo[9];        //본인확인 결과 (Y/N)
        $certGb     = $split_dec_retInfo[10];       //인증수단
        $cellNo     = $split_dec_retInfo[11];       //핸드폰 번호
        $cellCorp   = $split_dec_retInfo[12];       //이동통신사
        $certDate   = $split_dec_retInfo[13];       //검증시간
        $addVar     = $split_dec_retInfo[14];       //추가 파라메터

        //예약 필드
        $ext1       = $split_dec_retInfo[15];
        $ext2       = $split_dec_retInfo[16];
        $ext3       = $split_dec_retInfo[17];
        $ext4       = $split_dec_retInfo[18];
        $ext5       = $split_dec_retInfo[19];

        $name = iconv('EUC-KR', 'UTF-8', $name);
        $param = array(
            'result' => $result,
            'name'   => $name,
            'mobile'   => $cellNo,
        );
//__v($param);
        $app->render('member/auth_result.php', $param);
    });

    $app->post('/signup', function() use ($app, $log) {

        if(empty($_SESSION['agree'])){
            $app->redirect('/member/agree');
        }

        $user_type = $app->request->post('user_type');
        $email1 = $app->request->post('email1');
        $email2 = $app->request->post('email2');
        $email = $email1.'@'.$email2;
        $password = $app->request->post('password');
        $password_confirm = $app->request->post('password_confirm');
        $name = $app->request->post('name');
        $nickname = $app->request->post('nickname');
        $mobile = $app->request->post('mobile');
        $birthday = $app->request->post('birthday');
        $sido = $app->request->post('sido');
        $gugun = $app->request->post('gugun');
        $gender = $app->request->post('gender');
        $alarm_feeds = $app->request->post('alarm_feeds');
        $alarm_all = $app->request->post('alarm_all');

        $broker_id = $app->request->post('broker_id');
        $sido2 = $app->request->post('sido2');
        $gugun2 = $app->request->post('gugun2');
        $part = $app->request->post('part');
        $addr = $app->request->post('addr');

        $sample_photo = $app->request->post('sample_photo');
	

        $nickname = empty($nickname) ? $name : $nickname;

        if ($broker_id) {
            $broker = $app->db->selectOne('broker', 'company', array('broker_id' => $broker_id));
            $company = $broker['company'];
        }

        $company = empty($company) ? '' : $company;
        $sido2 = empty($sido2) ? '' : $sido2;
        $gugun2 = empty($gugun2) ? '' : $gugun2;
        $part = empty($part) ? '' : $part;
        $addr = empty($part) ? '' : $addr;

        // 프로필 이미지
        $profile_url = empty($_SESSION['temp_profile_url']) ? '' : $_SESSION['temp_profile_url'];
        $profile_s_url = empty($_SESSION['temp_profile_s_url']) ? '' : $_SESSION['temp_profile_s_url'];

        // 네임카드 이미지
        $namecard_url = empty($_SESSION['temp_namecard_url']) ? '' : $_SESSION['temp_namecard_url'];
        $namecard_s_url = empty($_SESSION['temp_namecard_s_url']) ? '' : $_SESSION['temp_namecard_s_url'];

        // 입력내용 되살리기용
        $app->flash('user_type', $user_type);
        $app->flash('email1', $email1);
        $app->flash('email2', $email2);
        $app->flash('name', $name);
        $app->flash('nickname', $nickname);
        $app->flash('mobile', $mobile);
        $app->flash('birthday', $birthday);
        $app->flash('sido', $sido);
        $app->flash('gugun', $gugun);
        $app->flash('gender', $gender);
        $app->flash('alarm_feeds', $alarm_feeds);
        $app->flash('alarm_all', $alarm_all);

        $app->flash('broker_id', $broker_id);
        $app->flash('company', $company);
        $app->flash('sido2', $sido2);
        $app->flash('gugun2', $gugun2);
        $app->flash('part', $part);
        $app->flash('addr', $addr);

        // 프로필 이미지
        $app->flash('profile_s_url', $profile_s_url);
        $app->flash('namecard_s_url', $namecard_s_url);

        $user_type = setUserType($user_type);

        if(empty($email)){
            $app->flash('error', '이메일과 비밀번호는 필수 입력사항입니다.');
            $app->redirect('/signup');
        }else{
            if(!isEmail($email)){
                $app->flash('error', '이메일이 올바르지 않습니다.');
                $app->redirect('/member/signup');
            }
        }

        if(empty($password) || empty($password_confirm)){
            $app->flash('error', '이메일과 비밀번호는 필수 입력사항입니다.');
            $app->redirect('/member/signup');
        }else{
            if($password != $password_confirm){
                $app->flash('error', '비밀번호가 일치하지 않습니다.');
                $app->redirect('/member/signup');
            }

            if(strlen($password) < 6 || strlen($password) >= 20){
                $app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
                $app->redirect('/member/signup');
            }

            if(preg_match('/^(?=.*\d)(?=.*[a-zA-Z]).{6,19}$/', $password)){

            }else{
                $app->flash('error', '비밀번호는 문자, 숫자포함 6자 이상이어야 합니다.');
                $app->redirect('/member/signup');
            }

            $password_hash = create_hash($password);
        }

        if(empty($name)){
            $app->flash('error', '이름은 필수 입력사항입니다.');
            $app->redirect('/member/signup');
            $name = '';
        }

        if(empty($nickname)){
            $app->flash('error', '닉네임은 필수 입력사항입니다.');
            $app->redirect('/member/signup');
            // $nickname = '';
        }

        if(!empty($mobile)){
            if(preg_match('/^[0-9]{10,11}$/', $mobile)){
            }else{
                $app->flash('error', '정확한 휴대폰 번호를 확인해 주세요.');
                $app->redirect('/member/signup');
            }
        }else{
            $mobile = '';
        }

        if(!empty($birthday)){
            if(preg_match('/^[0-9]{8}$/', $birthday)){
            }else{
                $app->flash('error', '생년월일이 올바르지 않습니다.');
                $app->redirect('/member/signup');
            }
        }else{
            $birthday = '';
        }

        if($gender != 'M'){
            $gender == 'F';
        }

        if(empty($sido)){
            $sido = '';
        }

        if(empty($gugun)){
            $gugun = '';
        }

        $exist_member = $app->db->selectOne('user', '*', array('email'=>$email));

        if(!empty($exist_member)){
            $app->flash('error', '이미 가입된 이메일 입니다. 다른 이메일로 가입해주세요');
            $app->redirect('/member/signup');
        }

        $now = time();

        $is_request_trader = '0';
        $is_request_pb = '0';
        
        if ($user_type == 'T') $is_request_trader = '1';
        if ($user_type == 'P') $is_request_pb = '1';

		if($sample_photo)$profile_url=$profile_s_url=$sample_photo;

        $new_member = array(
            'user_type'=>$user_type,
            'email'=>$email,
            'name'=>$name,
            'nickname'=>$nickname,
            'platform'=>'',
            'platform_uid'=>'',
            'user_password'=>$password_hash,
            'mobile'=>$mobile,
            'birthday'=>$birthday,
            'sido'=>$sido,
            'gugun'=>$gugun,
            'gender'=>$gender,
            'picture'=>$profile_url,
            'picture_s'=>$profile_s_url,
            'broker_id'=>$broker_id,
            'company'=>$company,
            'sido2'=>$sido2,
            'gugun2'=>$gugun2,
            'part'=>$part,
            'addr'=>$addr,
            'live_name_chk'=>'Y',
            'namecard'=>$namecard_url,
            'namecard_s'=>$namecard_s_url,
            'alarm_feeds'=>$alarm_feeds ? '1' : '0',
            'alarm_all'=>$alarm_all ? '1' : '0',
            'is_request_trader'=>$is_request_trader,
            'is_request_pb'=>$is_request_pb
        );

        $new_member_id = $app->db->insert('user', $new_member);
        if (!$new_member_id) {
            $app->flash('error', '회원가입중 오류가 발생하였습니다');
            $app->redirect('/member/signup');
        }

        $url = $app->config('scheme').'://'.$app->config('host');

        ob_start();
		if($user_type == 'T'){
			include $app->config('templates.path').'/mail/mail_signup_trader.php';
		}elseif($user_type == 'P'){
			include $app->config('templates.path').'/mail/mail_signup_pb.php';
		}else{
			include $app->config('templates.path').'/mail/mail_signup_normal.php';
		}
        $content = ob_get_contents();
        ob_end_clean();

        $from = $app->config('system_sender_email');
        $from_name = $app->config('system_sender_name');
        $to = $email;
        $subject = $app->config('name').' 회원가입';
        sendmail($from, $from_name, $to, $subject, $content);

		//문자발송
		$SMSINFO['smsMsg']="시스메틱의 회원가입이 완료되었습니다.";
		$SMSINFO['smsHp']=$mobile;
		sendSMS($SMSINFO);

        $_SESSION['temp_profile_url'] = '';
        $_SESSION['temp_profile_s_url'] = '';
        $_SESSION['temp_namecard_url'] = '';
        $_SESSION['temp_namecard_s_url'] = '';

        $app->flash('user_type', $user_type);
        $app->redirect('/member/join_complete');
    });

    // 가입완료
    $app->get('/join_complete', function() use ($app) {
        $app->render('member/join_complete.php');
    });

    // 아이디 찾기 인증번호 요청
    $app->post('/search_email_auth', function() use ($app, $log) {
        $mobile = $app->request->post('mobile');

        $member = $app->db->selectOne('user', '*', array('mobile'=>$mobile));
        if ($member) {
            // 인증번호 생성

            // 문자 발송
            $userid = $app->config('sms.userid');           // 문자나라 아이디
            $passwd = $app->config('sms.userpw');           // 문자나라 비밀번호
            $hpSender = $app->config('admin_phone');        // 보내는분 핸드폰번호
            $hpReceiver = $mobile;                          // 받는분의 핸드폰번호
            $adminPhone = $app->config('admin_phone2');     // 비상시 메시지를 받으실 관리자 핸드폰번호
            $authKey = substr(createAuthorKey(), 0, 6);
            $hpMesg = "인증번호 : ".$authKey;                   // 메시지
            $hpMesg = iconv("UTF-8", "EUC-KR","$hpMesg");
            $hpMesg = urlencode($hpMesg);
            $endAlert = 1;  // 전송완료알림창 ( 1:띄움, 0:안띄움 )

            $_SESSION['emailAuth']['key'] = $authKey;
            $_SESSION['emailAuth']['mobile'] = $mobile;

            // 한줄로 이어쓰기 하세요.
            $smsResult = SendMesg("/MSG/send/web_admin_send.htm?userid=$userid&passwd=$passwd&sender=$hpSender&receiver=$hpReceiver&encode=1&end_alert=$endAlert&message=$hpMesg");

            // $result = $authKey."::".$smsResult;
            $result = true;
        } else {
            $result = false;
        }

        echo json_encode(array('result'=>$result));
    });

    // 아이디 찾기 인증번호 재요청
    $app->post('/search_email_reauth', function() use ($app, $log) {
        $mobile = $_SESSION['emailAuth']['mobile'];

        $member = $app->db->selectOne('user', '*', array('mobile'=>$mobile));
        if ($member) {
            // 인증번호 생성

            // 문자 발송
            $userid = $app->config('sms.userid');           // 문자나라 아이디
            $passwd = $app->config('sms.userpw');           // 문자나라 비밀번호
            $hpSender = $app->config('admin_phone');        // 보내는분 핸드폰번호
            $hpReceiver = $mobile;                          // 받는분의 핸드폰번호
            $adminPhone = $app->config('admin_phone2');     // 비상시 메시지를 받으실 관리자 핸드폰번호
            $authKey = substr(createAuthorKey(), 0, 6);
            $hpMesg = "인증번호 : ".$authKey;                   // 메시지
            $hpMesg = iconv("UTF-8", "EUC-KR","$hpMesg");
            $hpMesg = urlencode($hpMesg);
            $endAlert = 1;  // 전송완료알림창 ( 1:띄움, 0:안띄움 )

            $_SESSION['emailAuth']['key'] = $authKey;
            $_SESSION['emailAuth']['mobile'] = $mobile;

            // 한줄로 이어쓰기 하세요.
            $smsResult = SendMesg("/MSG/send/web_admin_send.htm?userid=$userid&passwd=$passwd&sender=$hpSender&receiver=$hpReceiver&encode=1&end_alert=$endAlert&message=$hpMesg");

            //$result = $authKey."::".$smsResult;
            $result = true;
      //      $result = "/MSG/send/web_admin_send.htm?userid=$userid&passwd=$passwd&sender=$hpSender&receiver=$hpReceiver&encode=1&end_alert=$endAlert&message=$hpMesg";
        } else {
            $result = false;
        }

        echo json_encode(array('result'=>$result));
    });

    $app->post('/check_email_auth', function() use ($app, $log) {
        if ($app->request->post('authNum') == $_SESSION['emailAuth']['key']) {
            $member = $app->db->selectOne('user', 'email', array('mobile' => $_SESSION['emailAuth']['mobile']));
            $_SESSION['emailAuth'] = '';
            echo json_encode(array('result'=>$member['email']));
        } else {
            echo json_encode(array('result'=>false));
        }
    });


    $app->get('/forget_password', function() use ($app, $isLoggedIn) {
        $redirect_url = '';
        $app->render('join.php', array('redirect_url'=>$redirect_url, 'show_forget_password'=>true));
    });

    $app->post('/forget_password', function() use ($app, $log, $isLoggedIn) {
        $email = $app->request->post('email');

        if(empty($email)){
            $app->flash('error', 'email is empty');
            $app->redirect('/forget_password');
        }

        $target_member = $app->db->selectOne('user', '*', array('email'=>$email));

        if(!empty($target_member)){
            $code = createAuthorKey();
            $app->db->update('user', array('password_code'=>$code), array('email'=>$target_member['email']));

            $from = $app->config('system_sender_email');
            $from_name = $app->config('system_sender_name');
            $to = $target_member['email'];
            $subject = 'SYSMETIC TRADERS 비밀번호 재설정 링크를 안내해드립니다.';
            $password_link = $app->request->getScheme().'://'.$app->request->getHost().'/set_password?uid='.$target_member['uid'].'&code='.$code;
            $url = $app->request->getScheme().'://'.$app->request->getHost();
            /*
            $content = '
            <!DOCTYPE html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>'.$app->config('name').' - Reset password</title>
            </head>
            <body>
            <div>
            If you want to reset your password, click this link.<br>
            <a href="'.$app->request->getScheme().'://'.$app->request->getHost().'/set_password?uid='.$target_member['uid'].'&code='.$code.'" target="_blank">Reset password</a>
            </div>
            </body>
            </html>
            ';
            */

            ob_start();
            include $app->config('templates.path').'/mail_password.php';
            $content = ob_get_contents();
            ob_end_clean();

            sendmail($from, $from_name, $to, $subject, $content);

            $type = $app->request->post('type');
            if(!empty($type) && $type == 'json'){
                echo json_encode(array('result'=>true));
                $app->stop();
            }else{
                $app->flash('success', 'Sent email to reset password.');
            }
        }else{
            $type = $app->request->post('type');
            if(!empty($type) && $type == 'json'){
                echo json_encode(array('result'=>false));
                $app->stop();
            }else{
                $app->flash('error', 'This email is wrong');
            }
        }

        $app->redirect('/forget_password');
    });
});
