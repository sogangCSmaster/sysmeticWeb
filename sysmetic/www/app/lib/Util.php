<?php
$aStItemInfo = array(
	1 => 'K200선물'
	,2 => 'K200옵션'
	,3 => '주식/ETF'
	,9 => '해외선물'
	,10 => '해외옵션'
	,11 => '주식선물'
	,13 => '해외주식'
	,4 => '중국선물'
	,15 => '원달러선물'
	,16 => '국고채선물'
	,17 => '국내ETF'
	,18 => '국내주식'
	,19 => '상품선물'
);

function __v($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

function alert($msg, $url='') {
    $move = ($url) ? "location.href='{$url}';" : "history.back();";
    echo "<script>
            alert('$msg');
            $move
          </script>";
}

function isEmail($email)
{
	$emailPattern = '/^[0-9a-zA-Z_\-\.]+@([0-9a-zA-Z_\-]+\.)+[0-9a-zA-Z_\-]+$/';
	if(!preg_match($emailPattern, $email)) return false;
	else return true;
}

function isUrl($url)
{
	$urlPattern = '%^(?:(?:https?)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu';
	if(!preg_match($urlPattern, $url)) return false;
	else return true;
}

// 메일보내기시 메일제목 인코딩
function encode_2047($subject) {
	return '=?UTF-8?b?'.base64_encode($subject).'?=';
}

// 메일보내기 User <user@example.com>
function sendmail($from, $from_name, $to, $subject, $content){
	$ok = @mail($to, encode_2047($subject), $content, "From: ".encode_2047($from_name)." <".$from.">\nContent-Type:text/html;charset=UTF-8");
	if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
		printf("<xmp align='left'>%s</xmp>", print_r($ok, true));
	}
	if($ok) return true;
	else return false;
}

// 메일보내기 User <user@example.com>
function sendEncodedMail($from, $from_name, $to, $subject, $content){
	$ok = @mail($to, encode_2047($subject), rtrim(chunk_split(base64_encode($content))), "From: ".encode_2047($from_name)." <".$from.">\r\nContent-Type:text/html;charset=UTF-8;\r\nContent-Transfer-Encoding: base64\r\n");
	if($ok) return true;
	else return false;
}

// 유니크한 인증번호 생성(비밀번호 또는 이메일인증 등 랜덤한 값 생성에 사용됨)
function createAuthorKey()
{
	return md5(uniqid(rand(), true));
}

// 썸네일 생성후 그 경로를 리턴한다
// $constrain = false, $crop = false 전체를 무조건 정사각형으로 만드는 옵션
// $constrain = false, $crop = true 정사각형을 벗어나는 부분은 잘라내면서 정사각형으로 만드는 옵션
function createThumbnail($file, $thumb, $width = 128, $height = 128, $constrain = false, $crop = true)
{

	$tmpImage = @getimagesize($file);

	switch($tmpImage[2]){
		case 1:	$srcImage = @imagecreatefromgif($file); break;
		case 2:	$srcImage = @imagecreatefromjpeg($file); break;
		case 3:	$srcImage = @imagecreatefrompng($file);	break;
		case 6:	$srcImage = @imagecreatefromwbmp($file); break;
		default : exit('error. try again');
	}

	if(!$srcImage){
		$image = exif_thumbnail($file, $exif_width, $exif_height, $exif_type);
		if($image !== false){
			$handle = fopen($thumb, 'a');
			fwrite($handle, $image);

			$tmpImage = @getimagesize($thumb);

			switch($tmpImage[2]){
				case 1:	$srcImage = @imagecreatefromgif($thumb); break;
				case 2:	$srcImage = @imagecreatefromjpeg($thumb, 100); break;
				case 3:	$srcImage = @imagecreatefrompng($thumb); break;
				case 6:	$srcImage = @imagecreatefromwbmp($thumb); break;
				default : exit('error. try again');
			}

		}else exit('error. try again');
	}
	$imageWidth = $tmpImage[0];
	$imageHeight = $tmpImage[1];

	// resizing width, height value
	/* 가로 크기에 맞춰 세로 변경하기 */
	if($constrain){
		if(($imageWidth/$width) == ($imageHeight/$height)){
			$dstWidth=$width;
			$dstHeight=$height;
		}elseif(($imageWidth/$width) < ($imageHeight/$height)){
			$dstWidth=$height*($imageWidth/$imageHeight);
			$dstHeight=$height;
		}else{
			$dstWidth=$width;
			$dstHeight=$width*($imageHeight/$imageWidth);
		}
	}else{
		/* 해당 사이즈보다 작은 경우 늘려서 해당 크기에 정확히 맞추기(무조건 주어진 크기에 맞춘다) */
		$dstWidth = $width;
		$dstHeight = $height;
	}

	$dstImage = @imagecreatetruecolor($dstWidth, $dstHeight);
	$black = imagecolorallocate($dstImage, 255, 255, 255);

	// crop은 전체이미지를 축소하는것이 아니라 부분을 잘라냄
	if($crop){
		$crop_size = $imageWidth > $imageHeight ? $imageHeight : $imageWidth;

		$dstImage = @imagecreatetruecolor($dstWidth, $dstHeight);
		// $thumbImage = @imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $imageWidth, $imageHeight);
		$thumbImage = @imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $crop_size, $crop_size);
		// php old version
		// if(!$thumbImage) $thumbImage = @imagecopyresized($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $imageWidth, $imageHeight);
		if(!$thumbImage) $thumbImage = @imagecopyresized($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $crop_size, $crop_size);
	}else{
		// imagecolortransparent($dstImage, $black);
		$thumbImage = @imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $imageWidth, $imageHeight);
		// php old version
		if(!$thumbImage) $thumbImage = @imagecopyresized($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $imageWidth, $imageHeight);

		//@imageinterlace($dstImage);
	}

	switch($tmpImage[2]){
		case 1:	Imagegif($dstImage, $thumb); break;
		case 2:	Imagejpeg($dstImage, $thumb); break;
		case 3:	Imagepng($dstImage, $thumb); break;
		case 6:	Imagewbmp($dstImage, $thumb); break;
		default: exit('error. try again');
	}

	@imagedestroy($dstImage);
	@imagedestroy($srcImage);

	return $thumb;
}

function getDateString($date){
	return getDateStringWithDelimiter($date,'.');
}

function getDateStringWithDelimiter($date, $delimiter){
	$date = removeDateCharacter($date);
	return substr($date,0,4).$delimiter.substr($date,4,2).$delimiter. substr($date,6,2);
}

function removeDateCharacter($date){
	$date = str_replace('-','', $date);
	$date = str_replace('/','', $date);
	$date = str_replace('.','', $date);
	return $date;
}

function printAnalytics()
{
	/*
print <<<END
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-59957541-1', 'auto');
  ga('send', 'pageview');

</script>
END;
*/
}

// 용량에 맞게 파일사이즈 단위와 함께 리턴
function getFileSize($size)
{
	if(!$size) return "0 Byte";
	if($size<1024) return $size." Byte";
	elseif($size >= 1024 && $size < 1024 * 1024) return sprintf("%0.1f KB",$size / 1024);
	else return sprintf("%0.2f MB",$size / (1024*1024));
}

// 시간을 글자로 변경
function time_elapsed_string($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1)
    {
        return '0 sec';
    }

    $a = array( 365 * 24 * 60 * 60  =>  'yr',
                 30 * 24 * 60 * 60  =>  'mon',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hr',
                                60  =>  'min',
                                 1  =>  'sec'
                );
    $a_plural = array( 'yr'   => 'yrs',
                       'mon'  => 'mons',
                       'day'    => 'days',
                       'hr'   => 'hrs',
                       'min' => 'mins',
                       'sec' => 'secs'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ';
        }
    }
}

// 표준편차
if (!function_exists('stats_standard_deviation')) {
    /**
     * This user-land implementation follows the implementation quite strictly;
     * it does not attempt to improve the code or algorithm in any way. It will
     * raise a warning if you have fewer than 2 values in your array, just like
     * the extension does (although as an E_USER_WARNING, not E_WARNING).
     *
     * @param array $a
     * @param bool $sample [optional] Defaults to false
     * @return float|bool The standard deviation or false on error.
     */
    function stats_standard_deviation(array $a, $sample = false) {
        $n = count($a);
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);
            return false;
        }
        if ($sample && $n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);
            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((double) $val) - $mean;
            $carry += $d * $d;
        };
        if ($sample) {
           --$n;
        }
        return sqrt($carry / $n);
    }
}

// 파일내용가져오기
function get_include_constents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}

function calculateData($daily_values){
	if(count($daily_values) == 0) return $daily_values;
	/*
	원금 : 전일원금+당일입출금
	* 최초거래일은 최초일잔고
	누적입출금액
	잔고/원금 = (당일잔고+입출금)/당일원금
	일손익 : 당일잔고-전일잔고-당일입출금
	총이익 : SUMIF(최초일일손익:당일일손익,">0")
	총손실 : SUMIF(최초일일손익:당일일손익,"<0")
	누적손익=누적수익 : SUM(최초일일손익:당일일손익)
	최대누적수익=MAX(최초일누적수익:당일누적수익)
	최대누적수익율=최대누적수익/당일원금
	PEAK : MAX(최초일누적손익:당일누적손익)
	DD(현재자본인하금액) : 당일누적손익-당일PEAK
	MDD(최대자본인하금액) : MIN(최초일DD:당일DD)
	평균손익 : 당일누적손익/(COUNT(최초거래일:당일)-1)
	평균손익률 : 평균손익 / 당일원금 * 100
	자체랭킹지수 :당일평균손익/STDDEV(최초일일손익:당일일손익)
	일수익률 : 당일일손익/당일원금*100
	누적수익률 : 당일누적손익/당일원금*100
	PEAK(%) : 당일PEAK/당일원금*100
	DD(%)(현재자본인하율) : 당일DD/당일원금*100
	MDD(%)(최대자본인하율) : 당일MDD/당일원금*100
	최대일이익(최대일수익) : MAX(최초일일손익:당일일손익,0)
	최대일이익률 : 당일최대일이익/당일원금
	최대일손실 : MIN(최초일일손익:당일일손익,0)
	최대일손실률 : 당일최대일손실/당일원금
	연속손익일수 :  산식표현이 어려움.
	당일이 연속으로 3일간 이익이 났으면 +3,
	당일이 연속으로 2일간 손실이 났으면 -2 로 표시,
	0이면 초기화
	최대연속이익일수 : MAX(최초일연속손익일수:당일연속손일일수,0)
	최대연속손실일수 : MIN(최초연속손익일수, 당일연속손익일수,0)
	이익일 : COUNTIF(최초일일손익:당일일손익,">0")
	손실일 : COUNTIF(최초일일손익:당일일손익,"<0")
	고점후경과일 : IF(당일PEAK=당일누적손익,0,IF(전일고점후경과일=0,1,전일고점경과일+1))
	승률 : 당일이익일/(당일이익일+당일손실일)
	이익승수(=Profit Factor) : 당일총수익/당일총손실
	ROA : 당일누적손익금액/당일최대자본인하금액
	평균수익 : 총이익/이익일
	평균손실 : 총손실/손실일
	평균손익비 : 당일평균수익/ABS(당일평균손실)
	Profit factor : 평균손익비:1
	총수익/총손실 : 당일총이익/당일총손실
	변동계수 : STDDEV(최초일일손익:당일일손익)/당일평균손익*100
	Sharp ratios : 당일평균손익/STDDEV(최초일일손익:당일일손익)
	기준가계산용일손익 : 최초일원금/당일원금*당일일손익
	기준가계산용누적손익 : SUM(최초일기준가계산용일손익:당일기준가계산용일손익)
	기준가 :당일기준가계산용누적손익/당일원금*100+1000
	기준가일수익률 : (당일기준가-전일기준가)/전일기준가
	기준가누적수익률 : (당일기준가-1000)/1000
	월간수익률 : (당월말기준가 - 전월말기준가)/100
	*/

	foreach($daily_values as $k => $v){
		// 일손익
		if(isset($daily_values[$k]['PL'])){
			$daily_values[$k]['daily_return'] = $daily_values[$k]['PL'];
		}
		// 총이익 :
		$daily_values[$k]['total_plus_return'] = calSumIf($daily_values, 'daily_return', $k, '+');
		// 총손실 :
		$daily_values[$k]['total_minus_return'] = calSumIf($daily_values, 'daily_return', $k, '-');
		// 누적손익 :
		$daily_values[$k]['total_profit'] = calSum($daily_values, 'daily_return', $k);
		// 최대누적수익=
		$daily_values[$k]['max_total_profit'] = calMax($daily_values, 'total_profit', $k);

		if(isset($daily_values[$k-1]['principal'])){
			$tmp_flow_plus = calSumIf($daily_values, 'flow', $k, '+');
			$tmp_flow_minus = calSumIf($daily_values, 'flow', $k, '-');
			$tmp_total_profit = $daily_values[$k]['total_profit'];
			$tmp_flow_minus = $tmp_flow_minus + $tmp_total_profit;
			if( $tmp_flow_minus>0|| $tmp_total_profit < 0) $tmp_flow_minus = 0;
			$daily_values[$k]['principal'] = $daily_values[$k-1]['principal']+ $tmp_flow_plus + $tmp_flow_minus ;
		}else{
			$daily_values[$k]['principal'] = $daily_values[$k]['flow'];
			$daily_values[$k]['flow'] = 0;
		}

		// 누적입출금액
		if(isset($daily_values[$k-1]['flow'])){
			$daily_values[$k]['total_flow'] = $daily_values[$k-1]['total_flow'] + $daily_values[$k]['flow'];
		}else{
			$daily_values[$k]['total_flow'] = $daily_values[$k]['flow'];
		}

		// 잔고/원금
		if(isset($daily_values[$k-1]['balance']) && $daily_values[$k]['principal']!= 0){
			$daily_values[$k]['balance_principal']= ($daily_values[$k-1]['balance']+$daily_values[$k]['flow'])/$daily_values[$k]['principal'];
		}else if( $daily_values[$k]['principal']!= 0){
			$daily_values[$k]['balance_principal']= (0+$daily_values[$k]['flow'])/$daily_values[$k]['principal'];
		}

		// 연속손익일수 :  산식표현이 어려움. 	당일이 연속으로 3일간 이익이 났으면 +3, 	당일이 연속으로 2일간 손실이 났으면 -2 로 표시, 0이면 초기화
		if(isset($daily_values[$k-1]['daily_return'])){
			if(checkNumberType($daily_values[$k]['daily_return']) == '0' || checkNumberType($daily_values[$k-1]['daily_return']) != checkNumberType($daily_values[$k]['daily_return'])){
				$daily_values[$k]['continue_days'] = 0;
				$daily_values[$k]['continue_plus_days'] = 0;
				$daily_values[$k]['continue_minus_days'] = 0;
			}else{
				if(checkNumberType($daily_values[$k]['daily_return']) == '+'){
					$daily_values[$k]['continue_plus_days'] = (isset($daily_values[$k-1]['continue_plus_days']) ? $daily_values[$k-1]['continue_plus_days'] : 0) + 1;
					$daily_values[$k]['continue_minus_days'] = 0;
					$daily_values[$k]['continue_days'] = (isset($daily_values[$k-1]['continue_days']) ? $daily_values[$k-1]['continue_days'] : 0) + 1;
				}else{
					$daily_values[$k]['continue_plus_days'] = 0;
					$daily_values[$k]['continue_minus_days'] = (isset($daily_values[$k-1]['continue_minus_days']) ? $daily_values[$k-1]['continue_minus_days'] : 0) + 1;
					$daily_values[$k]['continue_days'] = (isset($daily_values[$k-1]['continue_days']) ? $daily_values[$k-1]['continue_days'] : 0) - 1;
				}
			}
		}else{
			$daily_values[$k]['continue_plus_days'] = 0;
			$daily_values[$k]['continue_minus_days'] = 0;
		}
		// 최대연속이익일수 : MAX(최초일연속손익일수:당일연속손일일수,0)
		$daily_values[$k]['max_continue_plus_days'] = calMax($daily_values, 'continue_plus_days', $k);
		// 최대연속손실일수 : MIN(최초연속손익일수, 당일연속손익일수,0)
		$daily_values[$k]['max_continue_minus_days'] = calMax($daily_values, 'continue_minus_days', $k);
		// 최대누적수익율=
		if($daily_values[$k]['principal'] != 0){
			$daily_values[$k]['max_total_profit_rate'] = $daily_values[$k]['max_total_profit']/$daily_values[$k]['principal']*100;
		}else{
			$daily_values[$k]['max_total_profit_rate'] = 0;
		}
		// PEAK :
		$daily_values[$k]['peak'] = calMax($daily_values, 'total_profit', $k, null);
		// DD :
		$daily_values[$k]['dd'] = $daily_values[$k]['total_profit']-$daily_values[$k]['peak'];
		// MDD :
		$daily_values[$k]['mdd'] = calMin($daily_values, 'dd', $k, null);
		// 평균손익 :
		if((count($daily_values)-1) > 0){
			$daily_values[$k]['avg_return'] = $daily_values[$k]['total_profit']/(count($daily_values)-1);
		}else{
			$daily_values[$k]['avg_return'] = 0;
		}
		// 일수익률 :
		if($k != 0 && $daily_values[$k]['principal'] != 0){
			$daily_values[$k]['daily_return_rate'] = log(($daily_values[$k]['daily_return']+$daily_values[$k]['principal'])/$daily_values[$k]['principal'])*100 ;
		}else{
			$daily_values[$k]['daily_return_rate'] = 0;
		}
		// 누적수익률 :
		$daily_values[$k]['total_profit_rate'] = round(calSum($daily_values, 'daily_return_rate', $k), 2);
		// 평균손익률 :
		$daily_values[$k]['avg_return_rate'] = round($daily_values[$k]['total_profit_rate'] / calCountIf($daily_values, 'daily_return', $k, ''), 2);
		// PEAK(%) :
		$daily_values[$k]['peak_rate'] = calMax($daily_values, 'total_profit_rate', $k, null);
		// DD(%) :
		$daily_values[$k]['dd_rate'] = $daily_values[$k]['total_profit_rate'] - $daily_values[$k]['peak_rate'];
		// MDD(%) :
		$daily_values[$k]['mdd_rate'] = calMin($daily_values, 'dd_rate', $k, null);


		$daily_values[$k]['dd_day'] = 0;
		if($daily_values[$k]['dd_rate']<0) $daily_values[$k]['dd_day'] = 1;

		$daily_values[$k]['dd_max'] = 0;
		if($daily_values[$k]['dd_rate']!=0 && $k>0)
			if( $daily_values[$k]['dd_rate'] > $daily_values[$k]['dd_max'])
				$daily_values[$k]['dd_max'] = $daily_values[$k]['dd_rate'];
			else
				$daily_values[$k]['dd_max'] = $daily_values[$k-1]['dd_max'];

		$daily_values[$k]['dd_max2'] = 0;
		if($daily_values[$k]['dd_max']<0 && $k>0){
			$daily_values[$k]['dd_max2'] = $daily_values[$k]['dd_max'];
			$daily_values[$k-1]['dd_max2'] = 0;
		}


		// 자체랭킹지수 :
		$daily_values[$k]['ranking_k'] = 0;
		$dd_max2 = ABS(calSum($daily_values, 'dd_max2', $k));
		$dd_day = calSum($daily_values, 'dd_day', $k);
		$total_day = $k+1;
		if( $dd_max2 > 0 )
			$daily_values[$k]['ranking_k'] = $daily_values[$k]['total_profit_rate']/($dd_max2*(sqrt($dd_day/$total_day)));

		// 최대일이익 :
		$daily_values[$k]['max_plus_return'] = calMax($daily_values, 'daily_return', $k, 0);
		// 최대일이익률 :
		$daily_values[$k]['max_plus_return_rate'] = calMax($daily_values, 'daily_return_rate', $k, 0);

		// 최대일손실 :
		$daily_values[$k]['max_minus_return'] = calMin($daily_values, 'daily_return', $k, 0);
		// 최대일손실률 :
		$daily_values[$k]['max_minus_return_rate'] = calMin($daily_values, 'daily_return_rate', $k, 0);

		// 이익일 :
		$daily_values[$k]['plus_days'] = calCountIf($daily_values, 'daily_return', $k, '+');
		// 손실일 :
		$daily_values[$k]['minus_days'] = calCountIf($daily_values, 'daily_return', $k, '-');
		// 고점후경과일 :
		if($daily_values[$k]['peak'] == $daily_values[$k]['total_profit']){
			$daily_values[$k]['max_after_days'] = 0;
		}else{
			$daily_values[$k]['max_after_days'] = (isset($daily_values[$k-1]['max_after_days']) ? $daily_values[$k-1]['max_after_days'] : 0) + 1;
		}
		// 승률 :
		if(($daily_values[$k]['plus_days']+$daily_values[$k]['minus_days']) != 0){
			$daily_values[$k]['winning_rate'] = round($daily_values[$k]['plus_days']/($daily_values[$k]['plus_days']+$daily_values[$k]['minus_days'])*100, 2);
		}else{
			$daily_values[$k]['winning_rate'] = 0;
		}
		// 이익승수(=Profit Factor) : 당일총수익/당일총손실
		if($daily_values[$k]['total_minus_return'] != 0){
			$daily_values[$k]['profit_win_count'] = $daily_values[$k]['total_plus_return']/$daily_values[$k]['total_minus_return'];
		}else{
			$daily_values[$k]['profit_win_count'] = 0;
		}
		// ROA : 당일누적손익금액/당일최대자본인하금액
		if($daily_values[$k]['mdd'] != 0){
			$daily_values[$k]['roa'] = $daily_values[$k]['total_profit']/$daily_values[$k]['mdd'];
		}else{
			$daily_values[$k]['roa'] = 0;
		}

		// 평균수익 :
		if($daily_values[$k]['plus_days'] != 0){
			$daily_values[$k]['avg_plus'] = $daily_values[$k]['total_plus_return']/$daily_values[$k]['plus_days'];
		}else{
			$daily_values[$k]['avg_plus'] = 0;
		}
		// 평균손실 :
		if($daily_values[$k]['minus_days'] != 0){
			$daily_values[$k]['avg_minus'] = $daily_values[$k]['total_minus_return']/$daily_values[$k]['minus_days'];
		}else{
			$daily_values[$k]['avg_minus'] = 0;
		}
		// 평균손익비 :
		if($daily_values[$k]['avg_minus'] != 0){
			$daily_values[$k]['avg_return_ratio'] = round($daily_values[$k]['avg_plus']/abs($daily_values[$k]['avg_minus']), 4);
		}else{
			$daily_values[$k]['avg_return_ratio'] = 0;
		}
		// profit factor = 총수익/총손실 :
		if($daily_values[$k]['total_minus_return'] != 0){
			$daily_values[$k]['profit_factor'] = round($daily_values[$k]['total_plus_return']/$daily_values[$k]['total_minus_return'],4);
		}else{
			$daily_values[$k]['profit_factor'] = 0;
		}

		// 변동계수 :
		if($daily_values[$k]['avg_return'] != 0){
			$daily_values[$k]['change_k'] = calSTDDEV($daily_values, 'daily_return', $k)/$daily_values[$k]['avg_return']*100;
		}else{
			$daily_values[$k]['change_k'] = 0;
		}

		// Sharp ratios :
		if(calSTDDEV($daily_values, 'daily_return', $k) != 0){
			$daily_values[$k]['sharp_ratio'] = round($daily_values[$k]['avg_return']/calSTDDEV($daily_values, 'daily_return', $k), 4);
		}else{
			$daily_values[$k]['sharp_ratio'] = 0;
		}

		// 기준가 :
		if($daily_values[$k]['principal'] != 0){
			$daily_values[$k]['c_price'] = round($daily_values[$k]['total_profit_rate']+1000, 4);
		}else{
			$daily_values[$k]['c_price'] = 1000;
		}

		// 기준가일수익률 :
		//if(isset($daily_values[$k-1]['c_price']) && $daily_values[$k-1]['c_price'] != 0){
		//	$daily_values[$k]['c_daily_return_rate'] = ($daily_values[$k]['c_price']-$daily_values[$k-1]['c_price'])/$daily_values[$k-1]['c_price'];
		//}else{
		//	$daily_values[$k]['c_daily_return_rate'] = ($daily_values[$k]['c_price']-1000)/1000;
		//}
		// 기준가누적수익률 :
		//$daily_values[$k]['c_total_return_rate'] = ($daily_values[$k]['c_price']-1000)/1000;
		// 월간수익률 : (당월말기준가 - 전월말기준가)/100

		// 그래프용 타임스탬프
		$daily_values[$k]['m_timestamp'] = mktime(0, 0, 0, substr($daily_values[$k]['target_date'], 4, 2), substr($daily_values[$k]['target_date'], 6, 2), substr($daily_values[$k]['target_date'], 0, 4)) * 1000;
	}

	return $daily_values;
}

function getSMIndexArray($strategy_smindex, $startdate, $enddate){
	$strategy_smindex_array = Array();
	$startdate = getDateStringWithDelimiter($startdate, '-');
	$enddate = getDateStringWithDelimiter($enddate, '-');

	foreach($strategy_smindex as $k => $v){
		if($startdate <= $v['basedate'] && $v['basedate'] <= $enddate ){
			$strategy_smindex_array[] = $v;
		}
	}

	return $strategy_smindex_array;
}

function calPortfolioSMIndexGraph($strategies){
 	$portfolio_sm_index = Array();

	for($i=0 ; $i < count($strategies[0]['daily_values_graph']) ; $i++){
        	$portfolio_sm_index[$i]['sm_index'] = 0;
        }

	// 100 퍼센트 이상을 처리할때 사용함
	$nSumPercent = 0;
	foreach($strategies as $k => $v){
		$nSumPercent += $v['percents'];
	}
	if($nSumPercent < 100) $nSumPercent = 100;

	foreach($strategies as $k => $v){
        	$tmp_percent = $v['percents']/$nSumPercent;
                foreach($v['daily_values_graph'] as $kk => $vv){
                	$portfolio_sm_index[$kk]['sm_index'] = $portfolio_sm_index[$kk]['sm_index'] + $vv['sm_index'] * $tmp_percent;
                        $portfolio_sm_index[$kk]['basedate'] = $vv['basedate'];
                }
        }

	return $portfolio_sm_index;
}

function getChartDataString($chart_data, $data_col){
	$graph_data_array = array();

	foreach($chart_data as $k => $v){
                $m_timestamp = strtotime($v['basedate'])*1000;
                $graph_data_array[] = '['.$m_timestamp.','.$v[$data_col].']';
	}

	return '['.implode(',', $graph_data_array ).']';
}


function calPortfolioPLrate($portfolio_daily_values){
	$ret = 0;
	if(count($portfolio_daily_values) > 0 )
		$ret = round($portfolio_daily_values[count($portfolio_daily_values)-1]['sm_index']-$portfolio_daily_values[0]['sm_index'],2);

	return $ret;
}

// 월별 마지막 기준가
function calMonthlyData($daily_values){
	$c_price_map = array();
	foreach($daily_values as $k => $v){
		// YYYYMM 키를 가진 배열에 마지막 기준가를 넣음(월별 마지막 기준가가 됨)
		$c_price_map[substr($v['target_date'], 0, 6)] = $v['c_price'];
	}

	return $c_price_map;
}

function calMonthlyPLRate($monthly_values){
	$monthly_profit_rate = array();
	for($i=$monthly_values[0]['baseyear'];$i<=$monthly_values[count($monthly_values)-1]['baseyear'];$i++){
		for($j=1;$j<=12;$j++){
			$monthly_profit_rate[$i.''][$j.''] = 0;
		}
	}

	foreach($monthly_values as $k => $v){
		$monthly_profit_rate[$v['baseyear']][$v['basemonth']]=$v['monthly_pl_rate'];
	}

	return $monthly_profit_rate;
}

function calYearlyPLRate($yearly_values){
	$yearly_pl_rate = array();
	for($i=$yearly_values[0]['baseyear'];$i<=$yearly_values[count($yearly_values)-1]['baseyear'];$i++){
			$yearly_pl_rate[$i.''] = 0;
	}

	foreach($yearly_values as $k => $v){
		$yearly_profit_rate[$v['baseyear']]=$v['yearly_pl_rate'];
	}

	return $yearly_profit_rate;
}

function calYearlyProfitRate($daily_values){
	if(count($daily_values) == 0) return $daily_values;

	// 연간 수익률을 구함
	$yearly_profit_rate = array();
	for($i=intval(substr($daily_values[0]['target_date'], 0, 4));$i<=intval(substr($daily_values[count($daily_values)-1]['target_date'], 0, 4));$i++){
		$yearly_profit_rate[$i.''] = 0;
	}

	for($i=0 ; $i<count($daily_values); $i++){
		$year = intval(substr($daily_values[$i]['target_date'], 0, 4));
		$val = $daily_values[$i]['daily_return_rate'];
		$yearly_profit_rate[$year.''] += $val;
	}

	for($i=intval(substr($daily_values[0]['target_date'], 0, 4));$i<=intval(substr($daily_values[count($daily_values)-1]['target_date'], 0, 4));$i++){
		$yearly_profit_rate[$i.''] = round($yearly_profit_rate[$i.''],2);
	}

	return $yearly_profit_rate;
}

function calMonthlyProfitRate($daily_values){
	// 월간 수익률을 구함
	$monthly_profit_rate = array();
	for($i=intval(substr($daily_values[0]['basedate'], 0, 4));$i<=intval(substr($daily_values[count($daily_values)-1]['basedate'], 0, 4));$i++){
		for($j=1;$j<=12;$j++){
			$monthly_profit_rate[$i.''][$j.''] = 0;
		}
	}

	for($i=0 ; $i<count($daily_values); $i++){
		$year = intval(substr($daily_values[$i]['basedate'], 0, 4));
		$month = intval(substr($daily_values[$i]['basedate'], 4, 2));
		$val = $daily_values[$i]['daily_pl_rate'];
		$monthly_profit_rate[$year.''][$month.''] += $val;
	}

	for($i=intval(substr($daily_values[0]['basedate'], 0, 4));$i<=intval(substr($daily_values[count($daily_values)-1]['basedate'], 0, 4));$i++){
		for($j=1;$j<=12;$j++){
			$monthly_profit_rate[$i.''][$j.''] = round($monthly_profit_rate[$i.''][$j.''],2);
		}
	}

	return  $monthly_profit_rate;
}

function calWeeklyProfitValues($daily_values, $base_date){
	$d = $base_date;
	$d_1 = date("n/j", strtotime($base_date . " - 1 day"));
	$d_2 = date("n/j", strtotime($base_date . " - 2 day"));
	$d_3 = date("n/j", strtotime($base_date . " - 3 day"));
	$d_4 = date("n/j", strtotime($base_date . " - 4 day"));
	$d_5 = date("n/j", strtotime($base_date . " - 5 day"));
	$d_6 = date("n/j", strtotime($base_date . " - 6 day"));


	// 년간 수익률을 구함
	$weekly_profit_values[$d] = calDailyReturnByDate($daily_values, $d);
	$weekly_profit_values[$d_1] = calDailyReturnByDate($daily_values, $d_1);
	$weekly_profit_values[$d_2] = calDailyReturnByDate($daily_values, $d_2);
	$weekly_profit_values[$d_3] = calDailyReturnByDate($daily_values, $d_3);
	$weekly_profit_values[$d_4] = calDailyReturnByDate($daily_values, $d_4);
	$weekly_profit_values[$d_5] = calDailyReturnByDate($daily_values, $d_5);
	$weekly_profit_values[$d_6] = calDailyReturnByDate($daily_values, $d_6);
	$weekly_profit_values["sum"] = $weekly_profit_values[$d] + $weekly_profit_values[$d_1] + $weekly_profit_values[$d_2] + $weekly_profit_values[$d_3] + $weekly_profit_values[$d_4]+ $weekly_profit_values[$d_5] + $weekly_profit_values[$d_6] ;

	return $weekly_profit_values;
}

function calDailyReturnByDate($daily_values, $base_date){
	if(count($daily_values) == 0) return 0;

	$dailyReturn = 0;
	for($k=count($daily_values)-1 ; $k>=0 ; $k-- ){
		if($daily_values[$k]['basedate'] < date("o-m-d", strtotime($base_date))){
			break;
		}
		else if ($daily_values[$k]['basedate'] == date("o-m-d", strtotime($base_date))){
			$dailyReturn = $daily_values[$k]['daily_pl'];
			break;
		}
	}
	//echo $base_date .":". $dailyReturn;
	return $dailyReturn;
}


function checkNumberType($intval){
	if($intval < 0) return '-';
	else if($intval > 0) return '+';
	else return '0';
}

// daily_values 배열에서 해당 key 의 0번째부터 goal_index까지 값을 더하는 함수
function calSum($daily_values, $key, $goal_index){
	$value = 0;

	for($i=0;$i<=$goal_index;$i++){
		$value += $daily_values[$i][$key];
	}

	return $value;
}

// daily_values 배열에서 해당 key 의 0번째부터 goal_index까지 값을 더하는 함수인데 type이 + 양수값만, - 음수값만더함
function calSumIf($daily_values, $key, $goal_index, $type = '+'){
	$value = 0;

	for($i=0;$i<=$goal_index;$i++){
		if($type == '+'){
			if($daily_values[$i][$key] > 0) $value += $daily_values[$i][$key];
		}else{
			if($daily_values[$i][$key] < 0) $value += $daily_values[$i][$key];
		}
	}

	return $value;
}

// daily_values 배열에서 해당 key 의 0번째부터 goal_index까지 값중에 type이 + 양수값만, - 음수값 갯수
function calCountIf($daily_values, $key, $goal_index, $type = '+'){
	$value = 0;

	for($i=0;$i<=$goal_index;$i++){
		if($type == '+'){
			if($daily_values[$i][$key] > 0) $value += 1;
		}else if($type == '-'){
			if($daily_values[$i][$key] < 0) $value += 1;
		}else {
			$value += 1;
		}
	}

	return $value;
}

// daily_values 배열에서 해당 key 의 0번째부터 goal_index까지 값중 가장 큰 값
function calMax($daily_values, $key, $goal_index, $init_value = null){
	$value = $init_value == null ? $daily_values[0][$key] : $init_value;

	for($i=0;$i<=$goal_index;$i++){
		$value = $daily_values[$i][$key] > $value ? $daily_values[$i][$key] : $value;
	}

	return $value;
}

// daily_values 배열에서 해당 key 의 0번째부터 goal_index까지 값중 가장 작은값
function calMin($daily_values, $key, $goal_index, $init_value = null){
	$value = $init_value == null ? $daily_values[0][$key] : $init_value;

	for($i=0;$i<=$goal_index;$i++){
		$value = $daily_values[$i][$key] < $value ? $daily_values[$i][$key] : $value;
	}

	return $value;
}

// daily_values 배열에서 해당 key 의 0번째부터 goal_index까지 값의 표준편차
function calSTDDEV($daily_values, $key, $goal_index){
	$values = array();

	for($i=0;$i<=$goal_index;$i++){
		$values[] = $daily_values[$i][$key];
	}

	return stats_standard_deviation($values);
}

function generate_state() {
        $mt = microtime();
        $rand = mt_rand();
        return md5($mt . $rand);
}

// 값에 따른 부호 클래스 지정
function getSignClass($val, $is_one_side){
	if( $val > 0 && $is_one_side=='false')
		return '';
	else if( $val > 0 && $is_one_side=='always')
		return 'plus';
	else if( $val < 0 )
		return 'minus';
	else
		return '';
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function fetchStrategyData($strategy_id){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://sysmetic.co.kr/strategies/'.$strategy_id.'/fetch');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}

function setUserType($user_type) {
	if (!empty($user_type) && in_array($user_type, array('N', 'T', 'B', 'A', 'P'))){
	} else {
		$user_type = 'N';
	}
    return $user_type;
}

function getEmailList() {
    return array(
        'naver.com',
        'chol.com',
        'empal.com',
        'freechal.com',
        'gmail.com',
        'hanmail.net',
        'hanmir.com',
        'hitel.com',
        'hotmail.com',
        'korea.com',
        'lycos.com',
        'nate.com',
        'netian.com',
        'paran.com',
        'yahoo.com',
    );
}

function getAreaList() {
    return array(
        "서울특별시",
        "부산광역시",
        "인천광역시",
        "대구광역시",
        "광주광역시",
        "대전광역시",
        "울산광역시",
        "세종특별자치",
        "경기도",
        "강원도",
        "충청남도",
        "충청북도",
        "경상북도",
        "경상남도",
        "전라북도",
        "전라남도",
        "제주도",
    );
}

/**
 * paging
 * total : 전체 row, page_start : 시작 페이지, count : 한 페이지 출력 row수, page_count : 페이지 출력 수
 */
function getPaging($page, $total, $page_start, $count, $page_count, $script='movePage') {
    if ($total <= 0) {
        return '';
    }

    $total_page = ceil($total / $count);

    $link = $script."(1)";
    $html = '<nav class="page_nate">
                <a href="javascript:;'.$link.';" class="btn_page first">first</a>';
    if ($page_start > $page_count) {
        $link = $script."(".($page_start - 1).")";
        $html .= ' <a href="javascript:;'.$link.'" class="btn_page prev">prev</a>';
    }

    for ($i = $page_start;$i<=$page_start + $page_count - 1;$i++) {
        if ($i > ceil($total / $count)) break;
        $curr = ($page == $i) ? 'curr' : '';
        $link = $script."($i)";
        $html .= ' <a href="javascript:;'.$link.';" class="direct '.$curr.'">'.$i.'</a>';
    }

    if ($page_start + $page_count <= $total_page) {
        $link = $script."(".($start_page + $page_count).")";
        $html .= ' <a href="javascript:;'.$link.'" class="btn_page next">next</a>';
    }

    $link = $script."($total_page)";
    $html .= ' <a href="javascript:;'.$link.';" class="btn_page last">last</a>
        </nav>';

    return $html;
}

// 메시지발송
function SendMesg($url) {
    $fp = fsockopen("211.233.20.184", 80, $errno, $errstr, 10);
    if(!$fp){
        echo "$errno : $errstr";
        exit;
    }

    fwrite($fp, "GET $url HTTP/1.0\r\nHost: 211.233.20.184\r\n\r\n");
    $flag = 0;

    while(!feof($fp)){
        $row = fgets($fp, 1024);

        if($flag) $out .= $row;
        if($row=="\r\n") $flag = 1;
    }
    fclose($fp);
    return $out;
}


function percent_format($v, $n=2) {
    $v = round($v, 2);
    return number_format($v, $n, '.', ',');
}

function getProfileImg($img='') {
    return ($img) ? $img : '/images/img_profile_sample'.rand(1,4).'.jpg';
}


function makeTwitterShare($title, $url = '') {

    if (!$url) $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
    $url = urlencode($url);
    $title = urlencode($title);

    return sprintf("url=%s&amp;text=%s;$amp;", $url, $title);
}

function makeFacebookShare($title, $url = '') {

    if (!$url) $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
	//$url = "http://sysmetic-live.mypro.co.kr/images/common/sysmetic_logo.png";
    $url = urlencode($url);
    $title = urlencode($title);

    return sprintf("u=%s&amp;p=%s;$amp;", $url, $title);

}


function getPortfolioMddRate($qDB, $qaStIds) {
	
	$RetVal = 0;
	if(is_array($qaStIds) && count($qaStIds) > 0) {
		foreach((array)$qaStIds as $val) {
			$sql = sprintf("SELECT (((c_price - max_sm_index) / c_price) * 100) AS mdd_rate FROM strategy a INNER JOIN (SELECT strategy_id, MAX(sm_index) AS max_sm_index  FROM `strategy_daily_analysis` WHERE strategy_id = '%s') b ON a.strategy_id = b.strategy_id", $val);
			$row = $qDB->conn->query($sql)->fetch_array();
			$RetVal += abs($row['mdd_rate']);
		}
		$RetVal /= count($qaStIds);

	}
	return $RetVal;
}



function setStrategyAnalysis($qDB, $st_id, $start_day, $end_day="") {
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);

	$sql_old = sprintf("SELECT * FROM strategy_daily_analysis WHERE strategy_id='%s' AND basedate < '%s' AND holiday_flag = 0 ORDER BY basedate DESC LIMIT 1", $st_id, $start_day);
	$aRowOld = $qDB->conn->query($sql_old)->fetch_array();

	$sql_old_sum = sprintf("SELECT sum(dd_ln_rate) as dd_ln_rate_sum, sum(dd_max_ln_rate) as dd_max_ln_rate_sum, sum(dd_days) as dd_days_sum, MAX(acc_pl_ln_rate) as app_pl_ln_rate_max, MIN(dd_ln_rate) as dd_max_ln_rate FROM strategy_daily_analysis WHERE strategy_id='%s' AND basedate < '%s' AND holiday_flag = 0", $st_id, $start_day);
	$aRowOldSum = $qDB->conn->query($sql_old_sum)->fetch_array();


	if($end_day == "") {
		$sql_last = sprintf("SELECT target_date FROM strategy_daily WHERE strategy_id='%s' AND target_date > '%s' ORDER BY target_date DESC LIMIT 1", $st_id, Date("Ymd", strtotime($start_day)));
		$aLast = $qDB->conn->query($sql_last)->fetch_array();
		$end_day = ($aLast['target_date'] != '' ? Date("Y-m-d", strtotime($aLast['target_date'])) : '');
	}

	if($end_day != '' && strtotime($end_day) >= strtotime($start_day)) {

		// 요약정보
		$aTot = array(
			'trade_days' => max($aRowOld['trade_days']*1,0)
			,'principal' => $aRowOld['principal']*1
			,'balance' => $aRowOld['balance']*1
			,'sm_index' => max($aRowOld['sm_index']*1, 0)
			,'max_sm_index' => $aRowOld['max_sm_index']*1
			,'max_daily_profit' => $aRowOld['max_daily_profit']*1
			,'daily_pl' => ($aRowOld['daily_pl'] != '' ? $aRowOld['daily_pl']*1 : 0)
			,'daily_pl_rate' => ($aRowOld['daily_pl_rate'] != 0 ? $aRowOld['daily_pl_rate']*1 : '')
			,'daily_pl_stdev' => ($aRowOld['daily_pl_stdev'] != 0 ? $aRowOld['daily_pl_stdev']*1 : 0)
			,'acc_flow' => $aRowOld['acc_flow']*1
			,'acc_inflow' => max($aRowOld['acc_inflow']*1, 0)
			,'acc_outflow' => $aRowOld['acc_outflow']*1
			,'max_daily_profit_rate' => ($aRowOld['max_daily_profit_rate'] != '' ? $aRowOld['max_daily_profit_rate']*1 : 0)
			,'max_daily_loss' => ($aRowOld['max_daily_loss'] != '' ? $aRowOld['max_daily_loss']*1 : 0)
			,'max_daily_loss_rate' => $aRowOld['max_daily_loss_rate']*1
			,'total_profit' => $aRowOld['total_profit']*1
			,'profit_days' => $aRowOld['profit_days']*1
			,'profit_days_continue' => $aRowOld['profit_days_continue']*1
			,'total_loss' => $aRowOld['total_loss']*1
			,'loss_days' => $aRowOld['loss_days']*1
			,'loss_days_continue' => $aRowOld['loss_days_continue']*1
			,'acc_pl' => $aRowOld['acc_pl']*1
			,'acc_pl_rate' => $aRowOld['acc_pl_rate']*1
			,'acc_pl_ln_rate' => $aRowOld['acc_pl_ln_rate']*1
			,'max_acc_pl' => max($aRowOld['max_acc_pl']*1,0)
			,'max_acc_pl_rate' => max($aRowOld['max_acc_pl_rate']*1,0)
			,'peak' => $aRowOld['peak']*1
			,'peak_rate' => $aRowOld['peak_rate']*1
			,'after_peak_days' => $aRowOld['after_peak_days']*1
			,'mdd' => min($aRowOld['mdd']*1,0)					// 최대자본인하금액 - MIN() - AL
			,'mdd_rate' => min($aRowOld['mdd_rate']*1,0)					// 최대자본인하금액률 - MIN() - AM
			,'dd_days' => $aRowOld['dd_days']*1
			,'sharp_ratio' => $aRowOld['sharp_ratio']*1
				//- ,'dd_max_ln_rate' => $aRowOld['dd_max_ln_rate']*1
			,'sum_prev_dd_rate' => $aRowOld['sum_prev_dd_rate']*1

			,'dd_ln_rate_sum' => $aRowOldSum['dd_ln_rate_sum']*1

			// kp_ratio 계산용 이전구간 정보조회 (2017-05-03)
			,'dd_days_sum' => $aRowOld['dd_days_sum']*1
			,'dd_max_ln_rate' => $aRowOld['dd_max_ln_rate']*1
			,'dd_max_ln_rate_sum' => $aRowOld['dd_max_ln_rate_sum']*1
				//- ,'dd_days_sum' => $aRowOldSum['dd_days_sum']*1
				//- ,'dd_max_ln_rate_sum' => $aRowOldSum['dd_max_ln_rate_sum']*1
				//- ,'dd_max_ln_rate' => $aRowOldSum['dd_max_ln_rate']*1
			,'app_pl_ln_rate_max' => $aRowOldSum['app_pl_ln_rate_max']*1

		);

		for($nLoopTime = strtotime($start_day); $nLoopTime <= strtotime($end_day); $nLoopTime+=86400) {
			$sLoopDay = Date("Ymd", $nLoopTime);
			$sBaseDate = Date("Y-m-d", $nLoopTime);
			$sql = sprintf("SELECT * FROM strategy_daily WHERE strategy_id='%s' AND target_date = '%s'", $st_id, $sLoopDay);
			$aDay = $qDB->conn->query($sql)->fetch_array();

			$sql = sprintf("SELECT daily_id FROM strategy_daily_analysis WHERE strategy_id='%s' AND basedate = '%s'", $st_id, $sBaseDate);
			$aExist = $qDB->conn->query($sql)->fetch_array();


			$row = array();
			$row['strategy_id'] = $st_id;
			$row['basedate'] = $sBaseDate;

			$row['flow'] = ($aDay && is_array($aDay) && count($aDay) > 0 && isset($aDay['flow'])) ? $aDay['flow'] * 1 : 0;								// 입출금 - B
			$row['daily_pl'] = ($aDay && is_array($aDay) && count($aDay) > 0 && isset($aDay['PL'])) ? $aDay['PL'] * 1 : 0;							// 일손익 - C

			// 토,일요일이라도 거래가 있으면 포함
			if( ($row['flow'] == 0 && $row['daily_pl'] == 0) ) {										// Date("N", $nLoopTime) >= 6 ||
				// 입출금(이건 기존처럼 0으로 처리), 일손익(이건 기존처럼 0으로 처리), 거래일수, 입금, 출금, 일손익,일손익률
				$row['flow'] = 0;
				$row['daily_pl'] = 0;
				$row['holiday_flag'] = (Date("N", $nLoopTime) >= 6 ? 7 : 9);					// 7:공휴일, 9:거래없는날
				$row['acc_pl'] = $aTot['acc_pl'];
				$row['acc_pl_rate'] = $aTot['acc_pl_rate'];
				$row['principal'] = $aTot['principal'];

				// 추가 설정값
				$row['acc_flow'] = $aTot['acc_flow'];
				$row['acc_inflow'] = $aTot['acc_inflow'];
				$row['acc_outflow'] = $aTot['acc_outflow'];
				$row['max_daily_profit_rate'] = $aTot['max_daily_profit_rate'];
				$row['max_daily_loss'] = $aTot['max_daily_loss'];
				$row['max_daily_loss_rate'] = $aTot['max_daily_loss_rate'];
				$row['total_profit'] = $aTot['total_profit'];
				$row['total_loss'] = $aTot['total_loss'];
				$row['acc_pl_ln_rate'] = $aTot['acc_pl_ln_rate'];
				$row['max_acc_pl'] = $aTot['max_acc_pl'];
				$row['max_acc_pl_rate'] = $aTot['max_acc_pl_rate'];
				$row['peak'] = $aTot['peak'];
				$row['peak_rate'] = $aTot['peak_rate'];
				$row['mdd'] = $aTot['mdd'];
				$row['mdd_rate'] = $aTot['mdd_rate'];
				$row['dd_days'] = $aTot['dd_days'];

				if($aExist['daily_id'] > 0) {
					$sql_del = sprintf("DELETE FROM strategy_daily_analysis WHERE daily_id='%s'", $aExist['daily_id']);
					$qDB->conn->query($sql_del);
					$row['daily_id'] = $aExist['daily_id'];
				}
				$qDB->insert('strategy_daily_analysis', $row);

			} else {

				if($aTot['trade_days'] > 0) {
					if(round($aTot['principal']) != 0 && ($aTot['balance'] / $aTot['principal']) != 0) {
						$row['principal'] = $aTot['principal'] + ($row['flow'] / ($aTot['balance'] / $aTot['principal']) );			// 원금 - E
					} else {
						$row['principal'] = $row['flow'];																// 원금 - E
					}
					$row['balance'] = $aTot['balance'] + $row['flow'] + $row['daily_pl'];															// 잔고 - F

					// 1yr recent pl rate
					$sql_year_pl = sprintf("SELECT (SUM(daily_pl)/AVG(principal) * 100) as one_yr_pl_rate    FROM strategy_daily_analysis    WHERE strategy_id = '%s' AND basedate > DATE_ADD('%s',INTERVAL -1 YEAR) AND basedate < '%s' AND holiday_flag = 0", $st_id, $sBaseDate, $sBaseDate);
					$aYearPl = $qDB->conn->query($sql_year_pl)->fetch_array();
					$row['one_yr_pl_rate'] = round($aYearPl['one_yr_pl_rate'], 4);

				} else {
					$row['principal'] = $row['flow'];																// 원금 - E
					$row['balance'] = $row['principal'] + $row['daily_pl'];							// 잔고 - F
					$row['flow'] = 0;																					// 최초 입금액은 원금으로 처리되고, 입금액은 0 처리 됨.
				}

				$row['trade_days'] = $aTot['trade_days'] + 1;
					//- $row['trade_days'] = ($row['daily_pl'] != 0 ? $aTot['trade_days'] + 1 : $aTot['trade_days']);					// 손익이 0이면 거래일수를 합산하지 않는다.
				$row['acc_realized_pl'] = $row['principal'] - $row['balance'];					// 평가손익 - G
				$row['sm_index'] = ($row['principal'] != 0 ? (abs($row['balance'] / $row['principal']) * 1000) : 0);				// 기준가 - J
				if($row['balance'] < 1 || $row['principal'] < 1) {
					$row['sm_index'] = $row['sm_index'] * -1;
				}
				$row['max_sm_index'] = max($row['sm_index'], $aTot['max_sm_index'], 0);			// 최대 기준가
				$row['acc_flow'] = $aTot['acc_flow'] + $row['flow'];											// 누적입출금 - K
				$row['inflow'] = ($row['flow'] > 0 ? $row['flow'] : 0);										// 입금 - L
				$row['acc_inflow'] = $aTot['acc_inflow'] + $row['inflow'];								// 누적입금 - M
				$row['outflow'] = ($row['flow'] < 0 ? $row['flow'] : 0);										// 출금 - N
				$row['acc_outflow'] = $aTot['acc_outflow'] + $row['outflow'];						// 누적출금 - O
				$row['daily_pl_rate'] = round((($aTot['trade_days'] > 0 && $aTot['sm_index'] > 0 ?  (($row['sm_index'] - $aTot['sm_index']) / $aTot['sm_index']) : (($row['sm_index'] - 1000) / 1000) ) * 100), 4);						// 일손익률 - P

				if(round($row['principal']) > 0) {
					$row['daily_pl_ln_rate'] = round((log(($row['daily_pl'] + $row['principal']) / $row['principal']) * 100),4);
					if(!preg_match("/^[0-9-.]+$/", $row['daily_pl_ln_rate'])) $row['daily_pl_ln_rate'] = 0;
						//- if(!is_float($row['daily_pl_ln_rate']) && !is_numeric($row['daily_pl_ln_rate'])) $row['daily_pl_ln_rate'] = 0;
				}

				$row['acc_pl_ln_rate'] = $aTot['acc_pl_ln_rate'] + $row['daily_pl_ln_rate'];
				if(!preg_match("/^[0-9-.]+$/", $row['acc_pl_ln_rate'])) $row['acc_pl_ln_rate'] = 0;
					//- if(!is_float($row['acc_pl_ln_rate']) && !is_numeric($row['acc_pl_ln_rate'])) $row['acc_pl_ln_rate'] = 0;

				$row['max_daily_profit'] = max($aTot['max_daily_profit'], $row['daily_pl'], 0);				// 최대이익일 - Q
				$row['max_daily_profit_rate'] = round((($row['daily_pl_rate'] != '' ? max($row['daily_pl_rate'], $aTot['max_daily_profit_rate'], 0) : $aTot['max_daily_profit_rate'])), 4);			// 최대일이익률 - R

				if($row['daily_pl'] < 0) {
					if($row['daily_pl'] <= $aTot['max_daily_loss']) {
						$row['max_daily_loss'] = $row['daily_pl'];
					} else {
						$row['max_daily_loss'] = $aTot['max_daily_loss'];
					}
				} else {
					$row['max_daily_loss'] = $aTot['max_daily_loss'];
				}
					//- $row['max_daily_loss'] = ($row['daily_pl'] < 0 && $row['daily_pl'] <= $aTot['max_daily_loss'] ? $row['daily_pl'] : $aTot['max_daily_loss']);			// 최대일손실 - S


				$row['max_daily_loss_rate'] = min($row['daily_pl_rate'], $aTot['max_daily_loss_rate'] , 0);			// 최대일손실률 - T
				$row['total_profit'] = $aTot['total_profit'] + ($row['daily_pl'] > 0 ? $row['daily_pl'] : 0);			// 총이익 - U
				$row['profit_days'] = $aTot['profit_days'] + ($row['daily_pl'] > 0 ? 1 : 0);							// 이익일수 - V
				$row['profit_days_continue'] = ($row['daily_pl'] > 0 ? $aTot['profit_days_continue']+1 : 0);							// 연속이익일수 - V
				$row['avg_profit'] = ($row['profit_days'] > 0 ? ($row['total_profit'] / $row['profit_days']) : 0);			// 평균이익 - W
				$row['total_loss'] = $aTot['total_loss'] + ($row['daily_pl'] < 0 ? $row['daily_pl'] : 0);			// 총손실 - X
				$row['loss_days'] = $aTot['loss_days'] + ($row['daily_pl'] < 0 ? 1 : 0);							// 손실일수 - Y
				$row['loss_days_continue'] = ($row['daily_pl'] < 0 ? $aTot['loss_days_continue']+1 : 0);							// 연속손실일수 - V
				$row['avg_loss'] = ($row['loss_days'] > 0 ? ($row['total_loss'] / $row['loss_days']) : 0);			// 평균손실 - Z
				$row['acc_pl'] = $aTot['acc_pl'] + $row['daily_pl'];				// 누적손익 - AA
				$row['acc_pl_rate'] = round(((( $row['sm_index'] / 1000 ) - 1) * 100),4);			// 누적손익률 - AB
					//- $row['max_acc_pl'] = max((($row['acc_pl'] > $aTot['max_acc_pl']) ? $row['acc_pl'] : $aTot['max_acc_pl']), 0);			// 최대누적손익 - AC
						// IF v_acc_pl > v_max_acc_pl THEN SET v_max_acc_pl = v_acc_pl; SET v_max_acc_pl_rate = v_acc_pl_rate; SET v_max_acc_pl_ln_rate = v_acc_pl_ln_rate; END IF;

				if($row['acc_pl'] >= $aTot['max_acc_pl']) {				//- $row['acc_pl'] > 0 &&
					$aDebug[] = sprintf("[%s] (acc_pl : %s) >= (Tmax_acc_pl : %s) ... (acc_pl_rate : %s) ... (acc_pl_ln_rate : %s)", $row['basedate'], $row['acc_pl'], $aTot['max_acc_pl'], $row['acc_pl_rate'], $row['acc_pl_ln_rate']);


					$row['max_acc_pl'] = $row['acc_pl'];
					$row['max_acc_pl_rate'] = $row['acc_pl_rate'];
					$aTot['app_pl_ln_rate_max'] = $row['acc_pl_ln_rate'];
				} else {

					$aDebug[] = sprintf("[%s] (acc_pl : %s) < (Tmax_acc_pl : %s) ... (Tmax_acc_pl_rate : %s) ... (acc_pl_ln_rate : %s)", $row['basedate'], $row['acc_pl'], $aTot['max_acc_pl'], $aTot['max_acc_pl_rate'], $row['acc_pl_ln_rate']);

					$row['max_acc_pl'] = $aTot['max_acc_pl'];
					$row['max_acc_pl_rate'] = $aTot['max_acc_pl_rate'];
				}

				if(round($row['acc_pl']) > 0) {
					$row['dd'] = $row['acc_pl'] - $row['max_acc_pl'];
				}

					//- $row['max_acc_pl_rate'] = max(round((($row['acc_pl_rate'] > $aTot['max_acc_pl_rate']) ? $row['acc_pl_rate'] : $aTot['max_acc_pl_rate']), 4),0);			// 최대누적손익률 - AD
				$row['avg_pl'] = (($row['trade_days'] > 0) ? $row['acc_pl'] / $row['trade_days'] : 0);			// 평균손익 - AE
				$row['avg_pl_rate'] = ($row['trade_days'] > 0 ? round(($row['acc_pl_rate'] / $row['trade_days']),4) : 0);			// 평균손익률 - AF
				$row['peak'] = max((($row['acc_pl'] > $aTot['peak']) ? $row['acc_pl'] : $aTot['peak']), 0);			// peak - AG
				$row['peak_rate'] = max(round((($row['acc_pl_rate'] > $aTot['peak_rate']) ? $row['acc_pl_rate'] : $aTot['peak_rate']), 4), 0);			// peak_rate - AH
				$row['after_peak_days'] = ($row['peak'] == $aTot['peak'] && $row['peak'] > 0) ? $aTot['after_peak_days'] + 1 : 0;			// 고점후 경과일 - AI
					//- $row['dd'] = ($row['acc_pl'] > 0) ? $row['acc_pl'] - $row['max_acc_pl'] : 0;			// 현재자본인하금액 - AJ
				$row['dd_rate'] = round((( ($row['sm_index'] - 1000) > 0) ? (($row['sm_index'] - $aTot['max_sm_index']) / $row['max_sm_index']) * 100 : 0), 4);			// 현재자본인하률 - AK
					//- $row['dd_rate'] = round((( ($row['sm_index'] - 1000) > 0) ? (($row['sm_index'] - $aTot['max_sm_index']) / $row['sm_index']) * 100 : 0), 4);			// 현재자본인하률 - AK
					// IF((J56-1000)>0,(J56-MAX($J$4:J56,0))/J56,0)
				$row['mdd'] = min($row['dd'], $aTot['mdd'], 0);			// 최대자본인하금액 - mdd - AL .. ??
				$row['mdd_rate'] = round(min($row['dd_rate'], $aTot['mdd_rate'], 0),4);			// 최대자본인하금액률 - mdd_rate - AM
				$row['winning_rate'] = ($row['trade_days'] > 0 ? round((($row['profit_days'] / $row['trade_days']) * 100),4) : 0);					// 승률 - AN
				$row['profit_factor'] = round((($row['total_loss'] < 0) ? ($row['total_profit'] / abs($row['total_loss']) ) : 0), 8);				// profit_factor - AO
				$row['roa'] = round(($row['mdd'] != 0 ? ($row['acc_pl'] / $row['mdd']) * -1 : 0), 8);			// ROA - AP
				$row['avg_pl_ratio'] = abs($row['avg_loss']) != 0 ? round(($row['avg_profit'] / abs($row['avg_loss'])),8) : 0;			// 평균손익비 - AQ
					//				$row['variation_factor'] = $row['avg_pl'] != 0 ? round(($aTot['daily_pl_stdev'] / $row['avg_pl']) * 100, 8) : 0;			// 변동계수 - AR
					//				$row['sharp_ratio'] = $aTot['daily_pl_stdev'] != 0 ? round(($row['avg_pl'] / $aTot['daily_pl_stdev']),8) : 0;			// SharpRatio - AS

					//- $row['sm_index'] = $row['acc_pl_ln_rate'] + 1000;			// 예전 계산방식!!


					//				if(round($row['acc_pl']) > 0) {
					//					$aDebug[] = sprintf("[%s] if(acc_pl > 0) { dd_ln_rate = %s - %s }", $row['basedate'], $row['acc_pl_ln_rate'], $aTot['app_pl_ln_rate_max']);
					//					$row['dd_ln_rate'] = $row['acc_pl_ln_rate'] - $aTot['app_pl_ln_rate_max'];
					//					if(!preg_match("/^[0-9-.]+$/", $row['dd_ln_rate'])) $row['dd_ln_rate'] = 0;
					//						//- if(!is_float($row['dd_ln_rate']) && !is_numeric($row['dd_ln_rate'])) $row['dd_ln_rate'] = 0;
					//				}

				if($row['dd'] < 0) {
					$row['dd_ln_rate'] = $row['acc_pl_ln_rate'] - $aTot['app_pl_ln_rate_max'];
					$aDebug[] = sprintf("\t (dd_ln_rate : %s) =    %s    -    %s    ...    Tdd_ln_rate : %s", $row['dd_ln_rate'], $row['acc_pl_ln_rate'], $aTot['app_pl_ln_rate_max'], $aTot['dd_max_ln_rate']);
						//-if(!preg_match("/^[0-9-.]+$/", $row['dd_ln_rate'])) $row['dd_ln_rate'] = 0;

					$row['dd_days'] = $aTot['dd_days'] + 1;
					$row['sum_prev_dd_rate'] = ($aTot['sum_prev_dd_rate'] != $row['mdd_rate'] ? $row['mdd_rate'] : $aTot['sum_prev_dd_rate']);
					$aTot['dd_days'] = $row['dd_days'];
				} else {
					if($aTot['dd_days'] > 0) {
						$row['dd_days'] = $aTot['dd_days'] + 1;
					}
				}
					//				if($row['dd'] < 0) {
					//					$row['dd_days'] = $aTot['dd_days'] + 1;
					//					++$aTot['dd_days_sum'];
					//					$row['sum_prev_dd_rate'] = ($aTot['sum_prev_dd_rate'] != $row['mdd_rate'] ? $row['mdd_rate'] : $aTot['sum_prev_dd_rate']);
					//				} else {
					//					$row['sum_prev_dd_rate'] = 0;
					//						//+++ $aTot['dd_days_sum'] = $aTot['dd_days_sum'] + $row['dd_days'];
					//					$row['dd_days'] = 0; 
					//				}

				$row['dd_max_ln_rate'] = $aTot['dd_max_ln_rate'];
				if($row['dd_ln_rate'] < 0) {
					if($row['dd_ln_rate'] < $aTot['dd_max_ln_rate']) {
						$row['dd_max_ln_rate'] = $row['dd_ln_rate'];
						$aTot['dd_max_ln_rate'] = $row['dd_max_ln_rate'];
							//- if(!preg_match("/^[0-9-.]+$/", $row['dd_max_ln_rate'])) $row['dd_max_ln_rate'] = 0;
							//- if(!is_float($row['dd_max_ln_rate']) && !is_numeric($row['dd_max_ln_rate'])) $row['dd_max_ln_rate'] = 0;
					} else {
						//- $row['dd_ln_rate'] = $aTot['dd_max_ln_rate'];
					}
				} else {

						//- $row['dd_max_ln_rate'] = 0;
				}

				if($row['acc_pl'] > $aTot['peak'] && $row['acc_pl'] > 0) {
					$row['peak_ln_rate'] = $row['acc_pl_ln_rate'];
				}

					//				if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
					//					if($row['basedate'] >= '2011-11-14') {
					//						print '<xmp>';
					//						printf("[%s] dd: %s\n", $row['basedate'], $row['dd']);
					//						printf("acc_pl_ln_rate : %s\n", $row['acc_pl_ln_rate']);
					//						printf("dd_ln_rate_sum : %s\n", $aTot['dd_ln_rate_sum']);
					//						printf("dd_max_ln_rate : %s\n", $row['dd_max_ln_rate']);
					//						printf("aTot_dd_max_ln_rate : %s\n", $aTot['dd_max_ln_rate']);
					//						printf("dd_max_ln_rate_sum : %s\n", $aTot['dd_max_ln_rate_sum']);
					//						printf("dd_days_sum : %s\n", $aTot['dd_days_sum']);
					//						printf("dd_days : %s\n", $row['dd_days']);
					//						printf("trade_days : %s\n", $row['trade_days']);
					//						printf("           ABS(%s) + ABS(%s) : %s\n", ABS($aTot['dd_max_ln_rate_sum']),ABS($aTot['dd_max_ln_rate']), ABS($aTot['dd_max_ln_rate_sum'])+ABS($aTot['dd_max_ln_rate']));
					//						printf("old __ ABS(%s) + ABS(%s) : %s\n", ABS($aTot['dd_max_ln_rate_sum']),ABS($row['dd_ln_rate']), ABS($aTot['dd_max_ln_rate_sum'])+ABS($row['dd_ln_rate']));
					//						printf("SQRT((( %s + %s ) / %s)) : %s\n", $aTot['dd_days_sum'],$row['dd_days'],$row['trade_days'], SQRT(($aTot['dd_days_sum']+$row['dd_days'])/$row['trade_days']) );
					//						@printf("          kp_ratio_old : %s\n", round($row['acc_pl_ln_rate'] / ((ABS($aTot['dd_ln_rate_sum'])+ABS($row['dd_ln_rate'])) * SQRT(($aTot['dd_days_sum']+$row['dd_days'])/$row['trade_days'])), 6));
					//						printf("                 kp_ratio : %s\n", round($row['acc_pl_ln_rate'] / ((ABS($aTot['dd_max_ln_rate_sum'])+ABS($aTot['dd_max_ln_rate'])) * SQRT(($aTot['dd_days_sum']+$row['dd_days'])/$row['trade_days'])), 6));
					//						print '===========================</xmp>';
					//					}
					//
					//					if($row['basedate'] == '2011-12-29') {
					//						exit;
					//					}
					//
					//					if($row['trade_days'] > 0 && ($aTot['dd_days_sum']+$row['dd_days']) > 0 && ((abs($row['dd_ln_rate_sum'])+abs($row['dd_max_ln_rate'])) * SQRT( ($aTot['dd_days_sum']+$row['dd_days']) / $row['trade_days']) ) != 0) {
					//						// daily_pl_rate
					//							//- $row['kp_ratio'] = round($row['acc_pl_ln_rate'] / ((ABS($aTot['dd_ln_rate_sum'])+ABS($row['dd_max_ln_rate'])) * SQRT(($aTot['dd_days_sum']+$row['dd_days'])/$row['trade_days'])), 6);
					//						$row['kp_ratio'] = round($row['acc_pl_ln_rate'] / ((ABS($aTot['dd_max_ln_rate_sum'])+ABS($aTot['dd_max_ln_rate'])) * SQRT(($aTot['dd_days_sum']+$row['dd_days'])/$row['trade_days'])), 6);
					//					}
					//				} else {
					//					if($row['trade_days'] > 0 && ($aTot['dd_days_sum']+$row['dd_days']) > 0 && ((abs($row['dd_max_ln_rate_sum'])+abs($row['dd_ln_rate'])) * SQRT( ($aTot['dd_days_sum']+$row['dd_days']) / $row['trade_days']) ) != 0) {
					//						// daily_pl_rate
					//						$row['kp_ratio'] = round($row['acc_pl_ln_rate'] / ((ABS($aTot['dd_max_ln_rate_sum'])+ABS($row['dd_ln_rate'])) * SQRT(($aTot['dd_days_sum']+$row['dd_days'])/$row['trade_days'])), 6);
					//					}
					//				}

				// 거래일수가 100일 이상부터 kp_ratio 및 sm_score 적용해야함
				if($row['trade_days'] > 99 && ($aTot['dd_days_sum']+$row['dd_days']) > 0 && ((abs($aTot['dd_max_ln_rate_sum'])+abs($aTot['dd_max_ln_rate'])) * SQRT( ($aTot['dd_days_sum']+$row['dd_days']) / $row['trade_days']) ) != 0) {
					$row['kp_ratio'] = round($row['acc_pl_ln_rate'] / ((ABS($aTot['dd_max_ln_rate_sum'])+ABS($aTot['dd_max_ln_rate'])) * SQRT(($aTot['dd_days_sum']+$row['dd_days'])/$row['trade_days'])), 6);
				}

				// dd관련 다음번 값 설정 - 미리설정하면 kp_ratio 값 의 오류가 생김 (2017-04-24)
				if($row['dd'] < 0) {

				} else {
					$row['sum_prev_dd_rate'] = 0;
						//+++ $aTot['dd_days_sum'] = $aTot['dd_days_sum'] + $row['dd_days'];
					if($aTot['dd_days'] > 0) {
						$aTot['dd_days_sum'] += $aTot['dd_days']+1;
						$aTot['dd_max_ln_rate_sum'] += $row['dd_max_ln_rate'];
						$aTot['dd_ln_rate_sum'] += $row['dd_ln_rate'];
						$row['dd_max_ln_rate'] = 0;
					}
					$aTot['dd_days'] = 0;
				}

				// kp_ratio 계산용 이전구간 정보저장
				$row['dd_days_sum'] = $aTot['dd_days_sum'];
				$row['dd_max_ln_rate_sum'] = $aTot['dd_max_ln_rate_sum'];

					//				if($row['trade_days'] > 0 && ($aTot['dd_days_sum']+$row['dd_days']) > 0 && ((abs($row['sum_prev_dd_rate'])+abs($row['mdd_rate'])) * SQRT( ($aTot['dd_days_sum']+$row['dd_days']) / $row['trade_days']) ) != 0) {
					//					// daily_pl_rate
					//					$row['kp_ratio'] = round( $row['acc_pl_rate'] / ((abs($row['sum_prev_dd_rate'])+abs($row['mdd_rate'])) * SQRT( ($aTot['dd_days_sum']+$row['dd_days']) / $row['trade_days']) ) );
					//				}

					//				if($row['trade_days'] > 0 && (ABS($aTot['dd_max_ln_rate_sum']+$row['dd_max_ln_rate'])*SQRT(($aTot['dd_days_sum']+$row['dd_days'])/$row['trade_days'])) != 0) {
					//					$row['kp_ratio'] = round($row['acc_pl_ln_rate'] / (ABS($aTot['dd_max_ln_rate_sum']+$row['dd_max_ln_rate'])*SQRT(($aTot['dd_days_sum']+$row['dd_days'])/$row['trade_days'])), 6);
					//					if(!preg_match("/^[0-9-.]+$/", $row['kp_ratio'])) $row['kp_ratio'] = 0;
					//						//- if(!is_float($row['kp_ratio']) && !is_numeric($row['kp_ratio'])) $row['kp_ratio'] = 0;
					//				}
				// $aDebug[] = sprintf("%s = %s / (ABS(%s + %s) * SQRT(( %s + %s ) / %s )) \n", $row['kp_ratio'], $row['acc_pl_ln_rate'], $aTot['dd_max_ln_rate_sum'], $row['dd_max_ln_rate'], $aTot['dd_days_sum'], $row['dd_days'], $row['trade_days']);


					//- print $_sDebug = sprintf("  [%s] {%s} flow:%s , daily_pl:%s , acc_pl:%s , tot_max_acc_pl:%s , max_acc_pl:%s , max_pl_rate:%s , max_pl_ln:%s , dd:%s , max_loss:%s < %s , kp : %s = %s / (ABS(%s + %s) * SQRT(( %s + %s ) / %s )) \n", $sBaseDate, $st_id, $row['flow'], $row['daily_pl'], $row['acc_pl'], $aTot['max_acc_pl'], $row['max_acc_pl'], $row['max_acc_pl_rate'], $row['app_pl_ln_rate_max'], $row['dd'], $row['max_daily_loss'], $aTot['max_daily_loss'], $row['kp_ratio'], $row['acc_pl_ln_rate'], $aTot['dd_max_ln_rate_sum'], $row['dd_max_ln_rate'], $aTot['dd_days_sum'], $row['dd_days'], $row['trade_days']);
					//- $aDebug[] = $_sDebug;

				if($aExist['daily_id'] > 0) {
					$sql_del = sprintf("delete from strategy_daily_analysis where daily_id='%s'", $aExist['daily_id']);
					$qDB->conn->query($sql_del);
					$row['daily_id'] = $aExist['daily_id'];
				}

				$qDB->insert('strategy_daily_analysis', $row);

				// 일손익 - 표준편차 저장
				$sql_stdev = sprintf("SELECT STDDEV(daily_pl) as daily_pl_stdev FROM strategy_daily_analysis WHERE strategy_id='%s' AND basedate <= '%s' AND holiday_flag = 0", $st_id, $sBaseDate);
				$result = $qDB->conn->query($sql_stdev);
				$aStdev = $result->fetch_array();
					$row['variation_factor'] = $row['avg_pl'] != 0 ? round(($aStdev['daily_pl_stdev'] / $row['avg_pl']) * 100, 8) : 0;			// 변동계수 - AR
					$row['sharp_ratio'] = $aStdev['daily_pl_stdev'] != 0 ? round(($row['avg_pl'] / $aStdev['daily_pl_stdev']),8) : 0;			// SharpRatio - AS
				$qDB->update('strategy_daily_analysis', array('daily_pl_stdev'=>$aStdev['daily_pl_stdev']*1, 'variation_factor'=>$row['variation_factor'], 'sharp_ratio'=>$row['sharp_ratio']), array('strategy_id'=>$st_id, 'basedate'=>$sBaseDate));


				$sql_smidx = sprintf("SELECT * FROM strategy_smindex WHERE strategy_id = '%s' AND basedate='%s'", $st_id, $sBaseDate);
				$aSmIdx = $qDB->conn->query($sql_smidx)->fetch_array();
				$row['nSmIndex'] = ( ($row['sm_index'] > 0) ? $row['sm_index'] : getSmIndexInterpolate($qDB, $st_id, $sBaseDate) );

				if($aSmIdx['strategy_id'] != '') {
					$qDB->update('strategy_smindex', array('sm_index'=>$row['nSmIndex']), array('strategy_id'=>$st_id, 'basedate'=>$sBaseDate));
				} else {
					$qDB->insert('strategy_smindex', array('strategy_id'=>$st_id, 'basedate'=>$sBaseDate, 'sm_index'=>$row['nSmIndex']));
				}

				setStrategyScoreDay($qDB, $sBaseDate);
					//----------------- setStrategyScore($qDB, $st_id);

				// 요약정보 설정
				$aTot['trade_days'] = $row['trade_days'];
				$aTot['principal'] = $row['principal'];
				$aTot['balance'] = $row['balance'];
				$aTot['sm_index'] = $row['sm_index'];
				$aTot['max_sm_index'] = $row['max_sm_index'];
				$aTot['max_daily_profit'] = $row['max_daily_profit'];
				$aTot['daily_pl'] = max($aTot['daily_pl'], $row['daily_pl']);
				$aTot['daily_pl_rate'] = $row['daily_pl_rate'];
				$aTot['acc_flow'] = $row['acc_flow'];
				$aTot['acc_inflow'] = $row['acc_inflow'];
				$aTot['acc_outflow'] = $row['acc_outflow'];
				$aTot['max_daily_profit_rate'] = round(max($aTot['max_daily_profit_rate'], $row['max_daily_profit_rate']),4);
				$aTot['max_daily_loss'] = $row['max_daily_loss'];
				$aTot['max_daily_loss_rate'] = round(min($aTot['max_daily_loss_rate'], $row['max_daily_loss_rate']),4);
				$aTot['total_profit'] = $row['total_profit'];
				$aTot['profit_days'] = $row['profit_days'];
				$aTot['profit_days_continue'] = $row['profit_days_continue'];
				$aTot['total_loss'] = $row['total_loss'];
				$aTot['loss_days'] = $row['loss_days'];
				$aTot['loss_days_continue'] = $row['loss_days_continue'];
				$aTot['acc_pl'] = $row['acc_pl'];
				$aTot['acc_pl_rate'] = $row['acc_pl_rate'];
				$aTot['acc_pl_ln_rate'] = $row['acc_pl_ln_rate'];
				$aTot['max_acc_pl'] = $row['max_acc_pl'];
				$aTot['max_acc_pl_rate'] = $row['max_acc_pl_rate'];
				$aTot['peak'] = $row['peak'];
				$aTot['peak_rate'] = $row['peak_rate'];
				$aTot['after_peak_days'] = $row['after_peak_days'];
				$aTot['mdd'] = $row['mdd'];					// 최대자본인하금액 - MIN() - AL
				$aTot['mdd_rate'] = $row['mdd_rate'];					// 최대자본인하금액률 - MIN() - AM
				$aTot['dd_max_ln_rate'] = $row['dd_max_ln_rate'];
					//- $aTot['dd_days'] = $row['dd_days'];
				$aTot['sum_prev_dd_rate'] = $row['sum_prev_dd_rate'];
				$aTot['daily_pl_stdev'] = $aStdev['daily_pl_stdev'];
				$aTot['sharp_ratio'] = $row['sharp_ratio'];
					//- $aTot['dd_days_sum'] = $aTot['dd_days_sum'];
					//- $aTot['dd_max_ln_rate_sum'] = $aTot['dd_max_ln_rate_sum'];
					//- $aTot['app_pl_ln_rate_max'] = $aTot['app_pl_ln_rate_max'];
			}
		}

		// 그래프를위한 sm_index 일자별 설정진행 - 향후 phpFunction 으로 재구현해야함. (2017-04-27)
			//- $qDB->executesp('interpolate_strategy',array('p_strategy_id'=>$st_id));
		$sql_first = sprintf("SELECT target_date FROM strategy_daily WHERE strategy_id = '%s' ORDER BY target_date ASC LIMIT 1", $st_id);
		$aRowFirst = $qDB->conn->query($sql_first)->fetch_array();
		for($nLoopTime = strtotime($start_day) - (86400*7); $nLoopTime <= strtotime($end_day); $nLoopTime+=86400) {
			$sLoopDay = Date("Ymd", $nLoopTime);
			$sBaseDate = Date("Y-m-d", $nLoopTime);
			if(date("Y-m-d", strtotime($aRowFirst['target_date'])) > $sBaseDate) {
				continue;
			} else {
				$sql_smidx = sprintf("SELECT * FROM strategy_smindex WHERE strategy_id = '%s' AND basedate='%s'", $st_id, $sBaseDate);
				$aSmIdx = $qDB->conn->query($sql_smidx)->fetch_array();
				if(!is_array($aSmIdx) || $aSmIdx['strategy_id'] == '' || $aSmIdx['sm_index'] == '') {
					$nSmIndex = getSmIndexInterpolate($qDB, $st_id, $sBaseDate);

					if($aSmIdx['strategy_id'] != '') {
						$qDB->update('strategy_smindex', array('sm_index'=>$nSmIndex), array('strategy_id'=>$st_id, 'basedate'=>$sBaseDate));
					} else {
						$qDB->insert('strategy_smindex', array('strategy_id'=>$st_id, 'basedate'=>$sBaseDate, 'sm_index'=>$nSmIndex));
					}
				}
			}
		}

	}
	$aDebug['end'] = time();
	$aDebug['t_e'] = Date("H:i:s", $aDebug['end']);
	$aDebug['exec'] = $aDebug['end'] - $aDebug['start'];

		//- $qDB->insert('z_dev_log', array('st_id'=>$st_id, 'dl_type'=>'strategy_analysis', 'dl_memo'=>print_r($aDebug, true), 'dl_sys'=>sprintf("%s \n _FILES : %s \n _SERVER : %s", print_r($_REQUEST, true), print_r($_FILES, true), print_r($_SERVER, true))));
}

function setStrategyAnalysisMonthly($qDB, $st_id) {
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);

	$sql_del = sprintf("DELETE FROM strategy_monthly_analysis WHERE  strategy_id='%s'", $st_id);
	$qDB->conn->query($sql_del);
	$qDB->conn->query("SET @ACC_PL=0");
	$qDB->conn->query("SET @ACC_Principal=0");
	$qDB->conn->query("SET @ACC_Count=0");
	$sql = sprintf("
		INSERT INTO strategy_monthly_analysis 
			(strategy_id, baseyear, basemonth, avg_principal, flow, monthly_pl, monthly_pl_rate, acc_pl, acc_pl_rate)
		SELECT 	strategy_id
			,		baseyear
			,		basemonth
			,		ROUND(principal, 2)
			,		flow
			,		monthly_pl
			,		ROUND(monthly_pl/principal * 100, 4)   
			,		(@ACC_PL:=IFNULL(@ACC_PL,0)+ monthly_pl) acc_monthly_pl
			,		ROUND(IFNULL(@ACC_PL,0)/principal*100, 4) acc_monthly_pl_rate
			FROM	(     
						SELECT	strategy_id
						,		YEAR(basedate) baseyear, MONTH(basedate) basemonth
						,		AVG(Principal) principal
						, 		SUM(flow) flow
						, 		SUM(daily_pl) monthly_pl
						FROM 	strategy_daily_analysis 
						WHERE	strategy_id='%s'
						AND holiday_flag = 0
						GROUP 	BY YEAR(basedate), MONTH(basedate)
					) MONTHLY
		", $st_id);
	$qDB->conn->query($sql);

	// 월손익률 별도계산 (2017-05-03)
	$aPrev = array('base_mon'=>'', 'sm_index'=>0);
	$sql = sprintf("SELECT LEFT(basedate,7) AS base_mon FROM strategy_daily_analysis WHERE strategy_id = '%s' AND holiday_flag = 0 GROUP BY base_mon ORDER BY base_mon", $st_id);
	$result = $qDB->conn->query($sql);
	while($row = $result->fetch_array()){
		if($aPrev['base_mon'] == '') {
			$sql_first = sprintf("SELECT basedate, ROUND(sm_index) as sm_index FROM strategy_daily_analysis WHERE strategy_id = '%s' AND basedate like '%s%%' AND holiday_flag = 0 ORDER BY basedate ASC LIMIT 1", $st_id, $row['base_mon']);
			$aRow = $qDB->conn->query($sql_first)->fetch_array();
			if($aRow['basedate'] != '') {
				$aPrev['base_mon'] = substr($aRow['basedate'], 0, 7);
				$aPrev['sm_index'] = $aRow['sm_index'];
			}
		}

		$sql_last = sprintf("SELECT basedate, ROUND(sm_index) as sm_index, acc_pl_rate FROM strategy_daily_analysis WHERE strategy_id = '%s' AND basedate like '%s%%' AND holiday_flag = 0 ORDER BY basedate DESC LIMIT 1", $st_id, $row['base_mon']);
		$aRow2 = $qDB->conn->query($sql_last)->fetch_array();
		if($aRow2['basedate'] != '') {
			$row['monthly_pl_rate'] = $aPrev['sm_index'] != 0 ? round((($aRow2['sm_index'] - $aPrev['sm_index']) / $aPrev['sm_index']) * 100, 3) : 0;

			$row['timeBase'] = strtotime($aRow2['basedate']);
			$sql_up = sprintf("UPDATE strategy_monthly_analysis SET monthly_pl_rate='%s' WHERE strategy_id = '%s' AND baseyear='%s' AND basemonth='%s'", $row['monthly_pl_rate'], $st_id, date("Y", $row['timeBase']), date("n", $row['timeBase']));
				//- printf("%s", $sql_up);
				//- printf("    (%s - %s) / %s = %s\n", $aRow2['sm_index'], $aPrev['sm_index'], $aPrev['sm_index'], $row['monthly_pl_rate']);
			$qDB->update('strategy_monthly_analysis', array('monthly_pl_rate'=>$row['monthly_pl_rate'], 'acc_pl_rate'=>$aRow2['acc_pl_rate']), array('strategy_id'=>$st_id, 'baseyear'=>date("Y", $row['timeBase']), 'basemonth'=>date("n", $row['timeBase'])));

			$aPrev['base_mon'] = substr($aRow2['basedate'], 0, 7);
			$aPrev['sm_index'] = $aRow2['sm_index'];
		}
	}



	$aDebug['end'] = time();
	$aDebug['t_e'] = Date("H:i:s", $aDebug['end']);
	$aDebug['exec'] = $aDebug['end'] - $aDebug['start'];
		//- $qDB->insert('z_dev_log', array('st_id'=>$st_id, 'dl_type'=>'strategy_month', 'dl_memo'=>print_r($aDebug, true), 'dl_sys'=>sprintf("%s \n _FILES : %s \n _SERVER : %s", print_r($_REQUEST, true), print_r($_FILES, true), print_r($_SERVER, true))));
}

function setStrategyAnalysisYearly($qDB, $st_id) {
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);

	$sql_del = sprintf("DELETE FROM strategy_yearly_analysis WHERE  strategy_id='%s'", $st_id);
	$qDB->conn->query($sql_del);
	$qDB->conn->query("SET @ACC_PL=0");
	$qDB->conn->query("SET @ACC_Principal=0");
	$qDB->conn->query("SET @ACC_Count=0");
	$sql = sprintf("
		INSERT INTO strategy_yearly_analysis 
			(strategy_id, baseyear, avg_principal, flow, yearly_pl, yearly_pl_rate, acc_pl, acc_pl_rate)
		SELECT 	strategy_id
		,		YEARLY.baseyear
		,		ROUND(principal,2)
		,		flow
		,		yearly_pl
		,		MONTHLY.yearly_pl_rate
		,		(@ACC_PL:=@ACC_PL+ yearly_pl) acc_monthly_pl
		,		ROUND((@ACC_PL/((@ACC_Principal:=@ACC_Principal+ principal)/(@ACC_Count:=@ACC_Count+1)))*100,4) acc_monthly_pl_rate
		FROM	(     
					SELECT	strategy_id
					,		YEAR(basedate) baseyear
					,		AVG(Principal) principal
					, 		SUM(flow) flow
					, 		SUM(daily_pl) yearly_pl
					FROM 	strategy_daily_analysis 
					WHERE	strategy_id='%s'
					AND holiday_flag = 0
					GROUP 	BY YEAR(basedate)
				) YEARLY
		,		(
					SELECT 	baseyear, SUM(monthly_pl_rate) yearly_pl_rate 
					FROM 	strategy_monthly_analysis
					WHERE	strategy_id='%s'
					GROUP 	BY baseyear
				) MONTHLY
		WHERE 	YEARLY.baseyear = MONTHLY.baseyear
		", $st_id, $st_id);
	$qDB->conn->query($sql);

	$aDebug['end'] = time();
	$aDebug['t_e'] = Date("H:i:s", $aDebug['end']);
	$aDebug['exec'] = $aDebug['end'] - $aDebug['start'];
		//- $qDB->insert('z_dev_log', array('st_id'=>$st_id, 'dl_type'=>'strategy_year', 'dl_memo'=>print_r($aDebug, true), 'dl_sys'=>sprintf("%s \n _FILES : %s \n _SERVER : %s", print_r($_REQUEST, true), print_r($_FILES, true), print_r($_SERVER, true))));
}

function setStrategyScoreDay($qDB, $qDay) {
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);

	$sql = "
		UPDATE strategy_daily_analysis AAA 
				INNER JOIN (
					SELECT 	AA.strategy_id
					, 		AA.basedate 
					, 		ROUND((AA.kp_ratio - average) / STDDEV, 6) zval
					, 		ROUND(CND((AA.kp_ratio - average) / STDDEV)*0.7 + (AA.trade_days/BB.max_trade_days)*100*0.3,6) score
					FROM	(
								SELECT 	A.strategy_id, A.basedate, A.kp_ratio, A.trade_days
								FROM	strategy_daily_analysis A
								WHERE A.holiday_flag = 0
								AND A.basedate = '". $qDay ."'
								AND A.kp_ratio != 0
							) AA
					,		(
								SELECT  AVG(kp_ratio) average,  STD(kp_ratio) STDDEV, MAX(trade_days) max_trade_days
								FROM	strategy_daily_analysis A
								WHERE A.holiday_flag = 0
								AND A.basedate = '". $qDay ."'
								AND A.kp_ratio != 0
							) BB
				)  BBB
				ON AAA.strategy_id = BBB.strategy_id AND AAA.basedate = BBB.basedate
			SET sm_score = BBB.score
			,	z_value = BBB.zval
	";
	$qDB->conn->query($sql);
	$aDebug[] = time();
	$aDebug[] = $sql;

	$aDebug['end'] = time();
	$aDebug['t_e'] = Date("H:i:s", $aDebug['end']);
	$aDebug['exec'] = $aDebug['end'] - $aDebug['start'];
		//- $qDB->insert('z_dev_log', array('st_id'=>$st_id, 'dl_type'=>'strategy_score', 'dl_memo'=>print_r($aDebug, true), 'dl_sys'=>sprintf("%s \n _FILES : %s \n _SERVER : %s", print_r($_REQUEST, true), print_r($_FILES, true), print_r($_SERVER, true))));
}

function setStrategyScore($qDB, $st_id="") {
	$aDebug = array('exec'=>0, 't_s'=>Date("H:i:s"), 't_e'=>'', 'start'=>time(), 'end'=>0);

	$sql = "
		UPDATE strategy_daily_analysis AAA 
				INNER JOIN (
					SELECT 	AA.strategy_id
					, 		AA.basedate 
					, 		ROUND((AA.kp_ratio - average) / STDDEV, 6) zval
					, 		ROUND(CND((AA.kp_ratio - average) / STDDEV)*0.7 + (AA.trade_days/BB.max_trade_days)*100*0.3,6) score
					FROM	(
								SELECT 	A.strategy_id, A.basedate, A.kp_ratio, A.trade_days
								FROM	strategy_daily_analysis A
								,		(SELECT strategy_id, MAX(basedate) basedate FROM strategy_daily_analysis WHERE holiday_flag = 0 GROUP BY strategy_id) B
								WHERE	A.strategy_id = B.strategy_id
								AND		A.basedate = B.basedate
								AND A.holiday_flag = 0
								AND A.kp_ratio != 0
							) AA
					,		(
								SELECT  AVG(kp_ratio) average,  STD(kp_ratio) STDDEV, MAX(trade_days) max_trade_days
								FROM	strategy_daily_analysis A
								,		(SELECT strategy_id, MAX(basedate) basedate FROM strategy_daily_analysis WHERE holiday_flag = 0 GROUP BY strategy_id) B
								WHERE	A.strategy_id = B.strategy_id
								AND		A.basedate = B.basedate
								AND A.holiday_flag = 0
								AND A.kp_ratio != 0
							) BB
				)  BBB
				ON AAA.strategy_id = BBB.strategy_id AND AAA.basedate = BBB.basedate
			SET sm_score = BBB.score
			,	z_value = BBB.zval
	";
	$qDB->conn->query($sql);
	$aDebug[] = time();
	$aDebug[] = $sql;

	$sql = "
		UPDATE 	strategy AAA 
				INNER JOIN (
					SELECT 	A.strategy_id, A.acc_pl_rate, A.principal, A.mdd, A.mdd_rate, A.sm_score, A.sm_index, A.one_yr_pl_rate, A.total_profit, A.winning_rate
					FROM	strategy_daily_analysis A
					,		(SELECT strategy_id, MAX(basedate) basedate FROM strategy_daily_analysis WHERE holiday_flag = 0 GROUP BY strategy_id) B
					WHERE	A.strategy_id = B.strategy_id
					AND		A.basedate = B.basedate AND A.acc_pl_rate <> 0
					AND A.holiday_flag = 0
				) BBB
				ON AAA.strategy_id = BBB.strategy_id 
		SET		AAA.total_profit_rate = IFNULL(BBB.acc_pl_rate, 0)
		,		AAA.principal = IFNULL(BBB.principal, 0)
		,		AAA.investment = IFNULL(BBB.principal, 0)
		,		AAA.yearly_profit_rate = IFNULL(BBB.one_yr_pl_rate, 0)
		,		AAA.mdd = IFNULL(BBB.mdd, 0)
		,		AAA.mdd_rate = IFNULL(BBB.mdd_rate, 0)
		,		AAA.sharp_ratio = IFNULL(BBB.sm_score, 0)
		,		AAA.c_price = BBB.sm_index
		,		AAA.total_profit = BBB.total_profit
		,		AAA.winning_rate = BBB.winning_rate
	";
	$qDB->conn->query($sql);
	$aDebug[] = time();
	$aDebug[] = $sql;

	$sql = "
		UPDATE 	strategy AAA 
			INNER JOIN (
				SELECT 	strategy_id, ROUND(AVG( yearly_pl_rate ), 4) yearly_pl_rate
				FROM 	strategy_yearly_analysis 
				GROUP 	BY strategy_id
			) BBB
			ON AAA.strategy_id = BBB.strategy_id 
		SET		AAA.yearly_profit_rate = IFNULL(BBB.yearly_pl_rate, 0)
	";
	$qDB->conn->query($sql);
	$aDebug[] = time();
	$aDebug[] = $sql;

	$sql = "
		UPDATE strategy AAA
			INNER JOIN (
				select A.strategy_id, A.acc_pl_rate
				from strategy_daily_analysis A
				, 		(
							select strategy_id, max(daily_id) daily_id
							from strategy_daily_analysis
							WHERE holiday_flag = 0
							group by strategy_id
						) B
				where A.strategy_id	= B.strategy_id
				and A.daily_id = B.daily_id
				AND A.holiday_flag = 0
			) BBB
			ON AAA.strategy_id = BBB.strategy_id
		SET AAA.total_profit_rate = IFNULL(BBB.acc_pl_rate, 0)
	";
	$qDB->conn->query($sql);
	$aDebug[] = $sql;

	$qDB->conn->query("SET @MDD_RANK=0");
	$qDB->conn->query("SET @SM_SCORE_RANK=0");
	$qDB->conn->query("SET @WINNING_RANK=0");

	// mdd_rank
	$sql = "
		UPDATE strategy AAA
			INNER JOIN (
				SELECT A.strategy_id, A.mdd, (@MDD_RANK:=@MDD_RANK+1) as mdd_rank
				FROM strategy A
				WHERE IFNULL(A.mdd,0) != 0
				ORDER BY mdd DESC
			) BBB
			ON AAA.strategy_id = BBB.strategy_id 
		SET AAA.mdd_rank = BBB.mdd_rank
	";
	$qDB->conn->query($sql);
	$aDebug[] = $sql;

	// sm_score_rank
	$sql = "
		UPDATE strategy AAA
			INNER JOIN (
				SELECT A.strategy_id, A.mdd, (@SM_SCORE_RANK:=@SM_SCORE_RANK+1) as sm_score_rank
				FROM strategy A
				WHERE IFNULL(A.mdd,0) != 0
				ORDER BY sharp_ratio DESC
			) BBB
			ON AAA.strategy_id = BBB.strategy_id 
		SET AAA.sm_score_rank = BBB.sm_score_rank
	";
	$qDB->conn->query($sql);
	$aDebug[] = $sql;

	// winning_rank
	$sql = "
		UPDATE strategy AAA
			INNER JOIN (
				SELECT A.strategy_id, A.mdd, (@WINNING_RANK:=@WINNING_RANK+1) as winning_rank
				FROM strategy A
				WHERE IFNULL(A.mdd,0) != 0
				ORDER BY winning_rate DESC
			) BBB
			ON AAA.strategy_id = BBB.strategy_id 
		SET AAA.winning_rank = BBB.winning_rank
	";
	$qDB->conn->query($sql);
	$aDebug[] = $sql;

	$aDebug['end'] = time();
	$aDebug['t_e'] = Date("H:i:s", $aDebug['end']);
	$aDebug['exec'] = $aDebug['end'] - $aDebug['start'];
		//-	$qDB->insert('z_dev_log', array('st_id'=>$st_id, 'dl_type'=>'strategy_score', 'dl_memo'=>print_r($aDebug, true), 'dl_sys'=>sprintf("%s \n _FILES : %s \n _SERVER : %s", print_r($_REQUEST, true), print_r($_FILES, true), print_r($_SERVER, true))));
}

function getSmIndexInterpolateOld($qDB, $st_id, $base_date, $qDirectFlag="") {
	$sql = sprintf("
		SELECT 
			( SELECT basedate
				FROM strategy_daily_analysis e2
				WHERE e2.basedate < e1.basedate  AND e2.strategy_id = e1.strategy_id AND e2.sm_index IS NOT NULL
				ORDER BY basedate DESC LIMIT 1 ) AS p_day
			,( SELECT sm_index 
				FROM strategy_daily_analysis e2
				WHERE e2.basedate < e1.basedate AND e2.strategy_id = e1.strategy_id AND e2.sm_index IS NOT NULL
				ORDER BY basedate DESC LIMIT 1 ) AS p_sm
			,( SELECT basedate 
				FROM strategy_daily_analysis e3
				WHERE e3.basedate > e1.basedate AND e3.strategy_id = e1.strategy_id AND e3.sm_index IS NOT NULL
				ORDER BY basedate ASC LIMIT 1 ) AS n_day
			,( SELECT sm_index 
				FROM strategy_daily_analysis e3
				WHERE e3.basedate > e1.basedate AND e3.strategy_id = e1.strategy_id AND e3.sm_index IS NOT NULL
				ORDER BY basedate ASC LIMIT 1 ) AS n_sm
		FROM strategy_smindex e1
		WHERE strategy_id = '%s'
		AND basedate = '%s'
	", $st_id, $base_date);
	$aSmInfo = $qDB->conn->query($sql)->fetch_array();

	$RetVal = 1000;
	if($qDirectFlag == "DIRECT") {
		$sql_sm = sprintf("SELECT Interpolation('%s', '%s', '%s', '%s', '%s') as sm_index ", $base_date, $aSmInfo['p_day'], $aSmInfo['p_sm'], $aSmInfo['n_day'], $aSmInfo['n_sm']);
		$aRow = $qDB->conn->query($sql_sm)->fetch_array();
		$RetVal = $aRow['sm_index'];
	} else if($aSmInfo['p_sm'] < 1 && $aSmInfo['n_sm'] < 1) {
		$RetVal = 1000;
	} else if($aSmInfo['p_sm'] > 0 && $aSmInfo['n_sm'] < 1) {
		$RetVal = $aSmInfo['p_sm'];
	} else if($aSmInfo['p_sm'] > 0 && $aSmInfo['n_sm'] > 0) {
		$sql_sm = sprintf("SELECT Interpolation('%s', '%s', '%s', '%s', '%s') as sm_index ", $base_date, $aSmInfo['p_day'], $aSmInfo['p_sm'], $aSmInfo['n_day'], $aSmInfo['n_sm']);
		$aRow = $qDB->conn->query($sql_sm)->fetch_array();
		$RetVal = $aRow['sm_index'];
	}
	return round($RetVal, 2);
}

function getSmIndexInterpolate($qDB, $st_id, $base_date, $qDirectFlag="") {
	$sql = sprintf("
		SELECT 
			( SELECT basedate
				FROM strategy_daily_analysis e2
				WHERE e2.basedate < '%s'  AND e2.strategy_id = '%s' AND e2.sm_index IS NOT NULL
				ORDER BY basedate DESC LIMIT 1 ) AS p_day
			,( SELECT sm_index 
				FROM strategy_daily_analysis e2
				WHERE e2.basedate < '%s' AND e2.strategy_id = '%s' AND e2.sm_index IS NOT NULL
				ORDER BY basedate DESC LIMIT 1 ) AS p_sm
			,( SELECT basedate 
				FROM strategy_daily_analysis e3
				WHERE e3.basedate > '%s' AND e3.strategy_id = '%s' AND e3.sm_index IS NOT NULL
				ORDER BY basedate ASC LIMIT 1 ) AS n_day
			,( SELECT sm_index 
				FROM strategy_daily_analysis e3
				WHERE e3.basedate > '%s' AND e3.strategy_id = '%s' AND e3.sm_index IS NOT NULL
				ORDER BY basedate ASC LIMIT 1 ) AS n_sm
	", $base_date, $st_id, $base_date, $st_id, $base_date, $st_id, $base_date, $st_id);
	$result = $qDB->conn->query($sql);
	$aSmInfo = $result->fetch_array();

	$RetVal = 1000;
	if($qDirectFlag == "DIRECT") {
		$sql_sm = sprintf("SELECT Interpolation('%s', '%s', '%s', '%s', '%s') as sm_index ", $base_date, $aSmInfo['p_day'], $aSmInfo['p_sm'], $aSmInfo['n_day'], $aSmInfo['n_sm']);
		$aRow = $qDB->conn->query($sql_sm)->fetch_array();
		$RetVal = $aRow['sm_index'];
	} else if($aSmInfo['p_sm'] < 1 && $aSmInfo['n_sm'] < 1) {
		$RetVal = 1000;
	} else if($aSmInfo['p_sm'] > 0 && $aSmInfo['n_sm'] < 1) {
		$RetVal = $aSmInfo['p_sm'];
	} else if($aSmInfo['p_sm'] > 0 && $aSmInfo['n_sm'] > 0) {
		$sql_sm = sprintf("SELECT Interpolation('%s', '%s', '%s', '%s', '%s') as sm_index ", $base_date, $aSmInfo['p_day'], $aSmInfo['p_sm'], $aSmInfo['n_day'], $aSmInfo['n_sm']);
		$aRow = $qDB->conn->query($sql_sm)->fetch_array();
		$RetVal = $aRow['sm_index'];
	}
	return round($RetVal, 2);
}

function getPortfolioPlRateInfo($qDB, $qaStIds, $qaPercent=array(), $qStartDay="", $qEndDay="") {
	$bPercent = (is_array($qaPercent) && count($qaPercent) > 0) ? true : false;
	$sWhereAdd = "";
	if($qStartDay != '' && $qEndDay != '' && date("Ymd", strtotime($qStartDay)) >= '19800101' && date("Ymd", strtotime($qEndDay)) >= '19800101' && strtotime($qStartDay) <= strtotime($qEndDay)) {
		$sWhereAdd .= sprintf(" AND basedate >= '%s' AND basedate <= '%s' ", $qStartDay, $qEndDay);
	}
	$aStDaily = array();
	$aPlRateDay = array();
	$aDailyStats = array();
	if($qaStIds){
		$result = $qDB->conn->query('SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $qaStIds).')');
		while($row = $result->fetch_assoc()){

			// 퍼센트 정보가 없을때 모두 1로 설정해줌 - 라운지 및 PB에서 사용 (2017-05-07)
			if($bPercent == false) {
				$qaPercent[$row['strategy_id']] = 1;
			}

			$nLoopGap2 = 0;
			$nSmIndexPrev = 1000;
				//			$sql = sprintf("SELECT strategy_id, basedate, sm_index FROM strategy_daily_analysis WHERE strategy_id = '%s' AND holiday_flag=0 ORDER BY basedate ASC", $row['strategy_id']);
				//			$result2 = $qDB->conn->query($sql);
				//			if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
				//				printf("<xmp align='left'>sql : %s</xmp>", print_r($sql, true));
				//			}
			$sql2 = sprintf("SELECT strategy_id, basedate, sm_index FROM strategy_smindex WHERE strategy_id = '%s' %s ORDER BY basedate ASC", $row['strategy_id'], $sWhereAdd);
			$result2 = $qDB->conn->query($sql2);
			while($row2 = $result2->fetch_assoc()){
				$aStDaily[$row2['basedate']][$row2['strategy_id']]['sm_index_gap'] = ($row2['sm_index'] - $nSmIndexPrev)/$nSmIndexPrev;
					//- $aStDaily[$row2['basedate']][$row2['strategy_id']]['sm_index_gap'] = ($nLoopGap2 > 0) ? (($row2['sm_index'] - $nSmIndexPrev)/$nSmIndexPrev) : 0;
				$aStDaily[$row2['basedate']][$row2['strategy_id']]['percent'] = $qaPercent[$row2['strategy_id']];
				$nSmIndexPrev = $row2['sm_index'];

				++$nLoopGap2;
			}
		}
	}
	ksort($aStDaily);

	$nLoopGap = 0;
	$nPlRatePrev = 0;
	$nTotalPlRate = 0;
	$nTotalSmIndex = 1000;
	$nTotalSmIndexPrev = 1000;
	$nTotalDdRate = 0;
	$nTotalMddRate = 0;
	$nSmIndexMax = 1000;

	foreach($aStDaily as $key3 => $row3) {
		if(count($row3) > 0) {
			/*
				N : N : 포트1일변동률			T : 포트1투자비중
				O : 포트2일변동률			U : 포트2투자비중
				P : 포트3일변동률			V : 포트3투자비중

				= W42
					+W42* (
						  (N43*(T43/(T43+U43+V43)))
						+(O43*(U43/(T43+U43+V43)))
						+(P43*(V43/(T43+U43+V43)))
					)
				// =IF((J67-1000)>0,(J67-MAX($J$4:J67,0))/J67,0)
				// =(J6-J5)/J5
			*/
				//			if(count($row3) == 1) {
				//				$aKeys3 = array_keys($row3);
				//				$sLoopStId = $aKeys3[0];
				//				$nTotalSmIndex = $nTotalSmIndexPrev + ($nTotalSmIndexPrev * $row3[$sLoopStId]['sm_index_gap']);
				//			} else 
			{
				$nCal = 0;
				$nSumPercent = 0;
				foreach((array)$row3 as $key4 => $row4) {
					$nSumPercent += $row4['percent'];
				}
				foreach((array)$row3 as $key4 => $row4) {
					$nCal += ( $row4['percent']/$nSumPercent ) * $row4['sm_index_gap'];
				}
				$nTotalSmIndex = $nTotalSmIndexPrev + ($nTotalSmIndexPrev * $nCal);
			}


			$aPlRateDay[$key3] = $nTotalSmIndex;

			if($nSmIndexMax < $nTotalSmIndex) $nSmIndexMax = $nTotalSmIndex;
			$nTotalPlRate = round(((( $nTotalSmIndex / 1000 ) - 1) * 100),4);						// 누적손익률 - AB
			$nTotalDdRate = round((($nTotalSmIndex - 1000) > 0 ? (($nTotalSmIndex - $nSmIndexMax) / $nSmIndexMax) * 100 : 0),4);
			if($nTotalMddRate > $nTotalDdRate) $nTotalMddRate = $nTotalDdRate;

			$aDailyStats[$key3] = array(
				'basedate'=>$key3, 'sm_index'=>$nTotalSmIndex, 'pl_rate'=>$nTotalPlRate, 'dd_rate'=>$nTotalDdRate, 'mdd_rate'=>$nTotalMddRate
			);

			// 다음 데이터 설정
			$nTotalSmIndexPrev = $nTotalSmIndex;

			++$nLoopGap;
		}
	}

	if(in_array($_SERVER['REMOTE_ADDR'], array('180.70.224.250'))) {
		printf("<xmp align='left'>\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n aDailyStats : %s</xmp>", print_r($aDailyStats, true));
	}

	return array('sm_index'=>$nTotalSmIndex, 'pl_rate'=>$nTotalPlRate, 'dd_rate'=>$nTotalDdRate, 'mdd_rate'=>$nTotalMddRate, 'arr_daily_stats'=>$aDailyStats, 'arr_daily_pl_rate'=>$aPlRateDay);
}

function getPortfolioPlRateInfoOld($qDB, $qaStIds, $qaPercent=array()) {
	$bPercent = (is_array($qaPercent) && count($qaPercent) > 0) ? true : false;
	$aStDaily = array();
	$aPlRateDay = array();
	$aDailyStats = array();
	if($qaStIds){
		$result = $qDB->conn->query('SELECT * FROM strategy WHERE strategy_id IN ('.implode(',', $qaStIds).')');
		while($row = $result->fetch_assoc()){

			// 퍼센트 정보가 없을때 모두 1로 설정해줌 - 라운지 및 PB에서 사용 (2017-05-07)
			if($bPercent == false) {
				$qaPercent[$row['strategy_id']] = 1;
			}

			$nLoopGap2 = 0;
			$nSmIndexPrev = 0;
			$result = $qDB->conn->query(sprintf("SELECT strategy_id, basedate, sm_index FROM strategy_daily_analysis WHERE strategy_id = '%s' AND holiday_flag=0 ORDER BY basedate ASC", $row['strategy_id']));
			while($row2 = $result->fetch_assoc()){
				$aStDaily[$row2['basedate']][$row2['strategy_id']]['sm_index_gap'] = ($nLoopGap2 > 0) ? ($row2['sm_index'] - $nSmIndexPrev) : 0;
				$aStDaily[$row2['basedate']][$row2['strategy_id']]['percent'] = $qaPercent[$row2['strategy_id']];
				$nSmIndexPrev = $row2['sm_index'];

				++$nLoopGap2;
			}
		}
	}
	ksort($aStDaily);

	$nLoopGap = 0;
	$nPlRatePrev = 0;
	$nTotalPlRate = 0;
	$nTotalSmIndex = 0;
	$nTotalSmIndexPrev = 1000;
	$nTotalDdRate = 0;
	$nTotalMddRate = 0;
	$nSmIndexMax = 1000;

	foreach($aStDaily as $key3 => $row3) {
		if(count($row3) > 0) {
			if(count($row3) == 1) {
				$aKeys3 = array_keys($row3);
				$sLoopStId = $aKeys3[0];
				$aPlRateDay[$key3] = $nPlRatePrev + $row3[$sLoopStId]['sm_index_gap'];

				$nTotalPlRate = $aPlRateDay[$key3];
			} else {
				// =I279+$B$2/($B$2+$B$3)*F280+$B$3/($B$2+$B$3)*G280
				$nCal = 0;
				$nSumPercent = 0;
				foreach((array)$row3 as $key4 => $row4) {
					$nSumPercent += $row4['percent'];
				}
				foreach((array)$row3 as $key4 => $row4) {
					$nCal += ( $row4['percent']/$nSumPercent ) * $row4['sm_index_gap'];
				}
				$aPlRateDay[$key3] = $nPlRatePrev + $nCal;
				$nTotalPlRate = $nPlRatePrev + $nCal;
			}

			$nPlRatePrev = $aPlRateDay[$key3];

			++$nLoopGap;
		}
	}

	return array('pl_rate'=>$nTotalPlRate, 'arr_daily_pl_rate'=>$aPlRateDay);
}

//이미지 추출 함수
function str_img($img_full_name){
	if($img_full_name != "")
	{ 
		preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", stripslashes($img_full_name), $out5);   
		$img_array = array();
		for($i=0; $i< sizeof($out5[1]); $i++)
		{ 
			preg_match("/[^= ']*\.(gif|jpg|bmp)/", $out5[1][$i], $regText2);  
			$img_array[] = $regText2[0];
		}

		return $img_array;
	}
}

// 주의 시작일자과 종료일자를 시간값으로 리턴함
function getWeekTimes($qDate="")
{
	$aTmp = array();
	$aTmp['timeBase'] = ($qDate) ? strtotime($qDate) : time();
	$aTmp['weekBase'] = Date("N", $aTmp['timeBase']);
	$aTmp['dayGap'] = (1 - $aTmp['weekBase']);
	$aTmp['timeStart'] = $aTmp['timeBase'] + ($aTmp['dayGap'] * 86400);
	$aTmp['timeEnd'] = $aTmp['timeStart'] + (86400 * 6);

	$RetVal = array();
	$RetVal[0] = $aTmp['timeStart'];
	$RetVal[1] = $aTmp['timeEnd'];
	$RetVal['timeStart'] = &$RetVal[0];
	$RetVal['timeEnd'] = &$RetVal[1];

	return $RetVal;
}

	function sendMailFavorSt($qMailTo, $qHtmlItems) {
		$sHtml = '
			<html>
				<head>
				<title>sysmetic mailform</title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				</head>
				<!-- 이미지파일 경로 서버로 연결 -->
				<body style="margin:0; padding:0;">
				<!-- 여기서부터 -->
					<table cellpadding="0" cellspacing="0" style="margin:0 auto; padding:0; width:800px; border:1px solid #d0d0d0;">
						<!-- header -->
						<tr>
							<td rowspan="3" style="margin:0; padding:0; border:0; width:641px; height:84px;">
								<img src="http://www.sysmetic.co.kr/images/mailform/img_header_left.gif" alt="SYSMETIC" style="display:block; margin:0; padding:0; width:641px; height:84px;" />
							</td>
							<td style="margin:0; padding:0; border:0; width:139px; height:26px;">
								<img src="http://www.sysmetic.co.kr/images/mailform/img_header_center_top.gif" alt="" style="display:block; margin:0; padding:0; width:139px; height:26px; "/>
							</td>
							<td rowspan="3" style="margin:0; padding:0; border:0; width:21px; height:84px;">
								<img src="http://www.sysmetic.co.kr/images/mailform/img_header_right.gif" alt="" style="display:block; margin:0; padding:0; width:21px; height:84px;" />
							</td>
						</tr>
						<tr>
							<td style="margin:0; padding:0; border:0; width:139px; height:29px;">
								<a href="http://www.sysmetic.co.kr/">
									<img src="http://www.sysmetic.co.kr/images/mailform/btn_header_direct_sysmetic.gif" alt="시스메틱 바로가기" style="display:block; margin:0; padding:0; width:139px; height:29px;" />
								</a>
							</td>
						</tr>
						<tr>
							<td style="margin:0; padding:0; border:0; width:139px; height:29px;">
								<img src="http://www.sysmetic.co.kr/images/mailform/img_header_center_bottom.gif" alt="" style="display:block; margin:0; padding:0; width:139px; height:29px;" />
							</td>
						</tr>
						<!-- //header -->
						<!-- contents -->
						<tr>
							<td colspan="3" style="margin:0; padding:0; padding-left:24px; padding-right:24px; border:0; height:90px; vertical-align:middle; border-bottom:1px solid #d0d0d0;">
								<p style="margin:0; letter-spacing:-2px; color:#222; font-size:28px;">
									관심 상품의 변동사항을 알려드립니다.
								</p>
							</td>
						</tr>
						<tr>
							<td colspan="3" style="margin:0; padding:0; padding-top:26px; padding-left:24px; padding-right:20px; padding-bottom:26px; border:0;">
								<table border="1" bordercolor="#d0d0d0" cellpadding="0" cellspacing="0" style="margin:0 auto; padding:0; width:100%; border-collapse:collapse; border-color:#d0d0d0;">
									<tr>
										<th rowspan="2" style="margin:0; padding:0; width:155px; text-align:center; vertical-align:middle; background-color:#f0f0f0; color:#888; font-size:13px; font-weight:normal;">전략명</th>
										<th colspan="5" style="margin:0; padding:0; height:28px; text-align:center; vertical-align:middle; background-color:#f0f0f0; color:#888; font-size:13px; font-weight:normal;">일간손익</th>
										<th rowspan="2" style="margin:0; padding:0; text-align:center; vertical-align:middle; background-color:#f0f0f0; color:#888; font-size:13px; font-weight:normal;">주간손익</th>
										<th rowspan="2" style="margin:0; padding:0; text-align:center; vertical-align:middle; background-color:#f0f0f0; color:#888; font-size:13px; font-weight:normal;">누적손익</th>
									</tr>
									' . $qHtmlItems .'
								</table>
							</td>
						</tr>
						<!-- //contents -->
						<!-- footer -->
						<tr>
							<td rowspan="3" style="margin:0; padding:0; border:0; width:641px; height:78x;">
								<img src="http://www.sysmetic.co.kr/images/mailform/img_footer_left.gif" alt="이 메일은 시스메틱 에서 회원가입 시 자동으로 발송되는 메일로 발송메일 주소는 발신전용입니다. | COPYRIGHT ⓒ 시스메틱 ALL RIGHT RESERVED ㅣ 전화 : 02-6338-1880 ㅣ 문의 : help@sysmetic.co.kr  " style="display:block; margin:0; padding:0; width:641px; height:78x;" />
							</td>
							<td style="margin:0; padding:0; border:0; width:139px; height:13px;">
								<img src="http://www.sysmetic.co.kr/images/mailform/img_footer_center_top.gif" alt="" style="display:block; margin:0; padding:0; width:139px; height:13px; "/>
							</td>
							<td rowspan="3" style="margin:0; padding:0; border:0; width:20px; height:78px;">
								<img src="http://www.sysmetic.co.kr/images/mailform/img_footer_right.gif" alt="" style="display:block; margin:0; padding:0; width:20px; height:78px;" />
							</td>
						</tr>
						<tr>
							<td style="margin:0; padding:0; border:0; width:139px; height:27px; text-align:right; background:#f5f5f5;">
								<a href="http://www.sysmetic.co.kr" target="_blank">
									<img src="http://www.sysmetic.co.kr/images/mailform/btn_footer_no_mail.gif" alt="메일 수신 거부" style="display:block; margin:0; padding:0; width:84px; height:27px;" />
								</a>
							</td>
						</tr>
						<tr>
							<td style="margin:0; padding:0; border:0; width:139px; height:38px;">
								<img src="http://www.sysmetic.co.kr/images/mailform/img_footer_center_bottom.gif" alt="" style="display:block; margin:0; padding:0; width:139px; height:38px;" />
							</td>
						</tr>
					</table>
				</body>
				</html>
		';

		$from = 'noreply@sysmetic.com';
		$from_name = 'SYSMETIC TRADERS';
		$subject = '[SYSMETIC] 관심 상품의 변동사항 입니다.';
		sendmail($from, $from_name, $qMailTo, $subject, $sHtml);
	}

function sendSMS($SMSINFO){
	$msg = $SMSINFO['smsMsg'];
	$hp = $SMSINFO['smsHp'];
	$from_hp = "02-6338-1880";

	//설정
	$userid = "sysmetic";           // 문자나라 아이디
	$passwd = "sys2015sys";           // 문자나라 2차 비밀번호
	$hpSender = $from_hp;         // 보내는분 핸드폰번호
	$hpReceiver = $hp;       // 받는분의 핸드폰번호
	$adminPhone = $from_hp;       // 비상시 메시지를 받으실 관리자 핸드폰번호
	$hpMesg = $msg;           // 메시지
	/*  UTF-8 글자셋 이용으로 한글이 깨지는 경우에만 주석을 푸세요. */
	$hpMesg = iconv("UTF-8", "EUC-KR","$hpMesg");
	/*  ---------------------------------------- */
	$hpMesg = urlencode($hpMesg);
	$endAlert = 0;  // 전송완료알림창 ( 1:띄움, 0:안띄움 )

	// 한줄로 이어쓰기 하세요.
	$url = "/MSG/send/web_admin_send.htm?userid=$userid&passwd=$passwd&sender=$hpSender&receiver=$hpReceiver&encode=1&end_alert=$endAlert&message=$hpMesg";

	////sms 발송//////////////////////////////////////////////////////////////////
    $fp = fsockopen("211.233.20.184", 80, $errno, $errstr, 10);
    if(!$fp){
		echo "$errno : $errstr";
		exit;
	}

    fwrite($fp, "GET $url HTTP/1.0\r\nHost: 211.233.20.184\r\n\r\n"); 
    $flag = 0; 
	
    while(!feof($fp)){
        $row = fgets($fp, 1024);

        if($flag) $out .= $row;
        if($row=="\r\n") $flag = 1;
    }
    fclose($fp);
    return $out;
	//////////////////////////////////////////////////////////////////////////////////
}