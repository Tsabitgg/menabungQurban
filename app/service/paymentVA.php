<?php
date_default_timezone_set("Asia/Jakarta");
class JWT
{
	/**
	 * Decodes a JWT string into a PHP object.
	 *
	 * @param string      $jwt    The JWT
	 * @param string|null $key    The secret key
	 * @param bool        $verify Don't skip verification process 
	 *
	 * @return object      The JWT's payload as a PHP object
	 * @throws UnexpectedValueException Provided JWT was invalid
	 * @throws DomainException          Algorithm was not provided
	 * 
	 * @uses jsonDecode
	 * @uses urlsafeB64Decode
	 */
	public static function decode($jwt, $key = null, $verify = true)
	{
		$tks = explode('.', $jwt);
		if (count($tks) != 3) {
			throw new UnexpectedValueException('Wrong number of segments');
		}
		list($headb64, $bodyb64, $cryptob64) = $tks;
		if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}
		if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64))) {
			throw new UnexpectedValueException('Invalid segment encoding');
		}
		$sig = JWT::urlsafeB64Decode($cryptob64);
		if ($verify) {
			if (empty($header->alg)) {
				throw new DomainException('Empty algorithm');
			}
			if ($sig != JWT::sign("$headb64.$bodyb64", $key, $header->alg)) {
				throw new UnexpectedValueException('Signature verification failed');
			}
		}
		return $payload;
	}
	/**
	 * Converts and signs a PHP object or array into a JWT string.
	 *
	 * @param object|array $payload PHP object or array
	 * @param string       $key     The secret key
	 * @param string       $algo    The signing algorithm. Supported
	 *                              algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string      A signed JWT
	 * @uses jsonEncode
	 * @uses urlsafeB64Encode
	 */
	public static function encode($payload, $key, $algo = 'HS256')
	{
		$header = array('typ' => 'JWT', 'alg' => $algo);
		$segments = array();
		$segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($header));
		$segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($payload));
		$signing_input = implode('.', $segments);
		$signature = JWT::sign($signing_input, $key, $algo);
		$segments[] = JWT::urlsafeB64Encode($signature);
		return implode('.', $segments);
	}
	/**
	 * Sign a string with a given key and algorithm.
	 *
	 * @param string $msg    The message to sign
	 * @param string $key    The secret key
	 * @param string $method The signing algorithm. Supported
	 *                       algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string          An encrypted message
	 * @throws DomainException Unsupported algorithm was specified
	 */
	public static function sign($msg, $key, $method = 'HS256')
	{
		$methods = array(
			'HS256' => 'sha256',
			'HS384' => 'sha384',
			'HS512' => 'sha512',
		);
		if (empty($methods[$method])) {
			throw new DomainException('Algorithm not supported');
		}
		return hash_hmac($methods[$method], $msg, $key, true);
	}
	/**
	 * Decode a JSON string into a PHP object.
	 *
	 * @param string $input JSON string
	 *
	 * @return object          Object representation of JSON string
	 * @throws DomainException Provided string was invalid JSON
	 */
	public static function jsonDecode($input)
	{
		$obj = json_decode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::_handleJsonError($errno);
		} else if ($obj === null && $input !== 'null') {
			throw new DomainException('Null result with non-null input');
		}
		return $obj;
	}
	/**
	 * Encode a PHP object into a JSON string.
	 *
	 * @param object|array $input A PHP object or array
	 *
	 * @return string          JSON representation of the PHP object or array
	 * @throws DomainException Provided object could not be encoded to valid JSON
	 */
	public static function jsonEncode($input)
	{
		$json = json_encode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::_handleJsonError($errno);
		} else if ($json === 'null' && $input !== null) {
			throw new DomainException('Null result with non-null input');
		}
		return $json;
	}
	/**
	 * Decode a string with URL-safe Base64.
	 *
	 * @param string $input A Base64 encoded string
	 *
	 * @return string A decoded string
	 */
	public static function urlsafeB64Decode($input)
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}
	/**
	 * Encode a string with URL-safe Base64.
	 *
	 * @param string $input The string you want encoded
	 *
	 * @return string The base64 encode of what you passed in
	 */
	public static function urlsafeB64Encode($input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}
	/**
	 * Helper method to create a JSON error.
	 *
	 * @param int $errno An error number from json_last_error()
	 *
	 * @return void
	 */
	private static function _handleJsonError($errno)
	{
		$messages = array(
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
			JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
		);
		throw new DomainException(
			isset($messages[$errno])
				? $messages[$errno]
				: 'Unknown JSON error: ' . $errno
		);
	}
}
//=========================================
//class JWT
$transaksi = new JWT();
$key = "TokenJWT_BMI_ICT";

// Database configuration
$db_host = '103.23.103.43:3306';
$db_username = 'root';
$db_password = 'Smartpay1ct';
$db_name = 'menabung_qurban';

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tagihans = array();


$decoded = $transaksi->decode($_GET['token'], $key, array('HS256'));

$decoded_array = (array) $decoded;

$METHOD =  $decoded_array['METHOD'];
$USERNAME =  $decoded_array['USERNAME'];
$PASSWORD =  $decoded_array['PASSWORD'];

if (isset($METHOD) && $METHOD == 'INQUIRY' && isset($_GET['token'])) {
	$VANO =  $decoded_array['VANO'];
	$TRXDATE =  $decoded_array['TRXDATE'];
	$REFNO =  $decoded_array['REFNO'];
	$CHANNELID =  $decoded_array['CHANNELID'];

	$TRXDATE = date_create($TRXDATE);
	//$TRXDATE = date("Y-m-d h:i:s",strtotime($TRXDATE)); //server publik 39 atau lokal 20
	$TRXDATE = date_format($TRXDATE, "Y-m-d h:i:s"); //server publik 35 atau lokal 22
	// $VANO10 = substr($VANO, 6, 16);
	//cek NIM
	//$ceknim = "Select NoInduk from mst_personal WHERE concat('751000', LPAD(NoInduk, 10, 0))='".$VANO."' or concat('751000', LPAD(NoLain, 10, 0))='".$VANO."'";
	//$ceknim = "Select NOCUST, NMCUST from scctcust WHERE (LPAD(NOCUST,10,0)='" . $VANO10 . "' or NUM2ND='" . $VANO10 . "') and STCUST=1";
	$ceknim = "SELECT billing.va_number as VA, billing.username as NMCUST
    FROM billing
    WHERE billing.va_number = '" . $VANO . "' 
    and billing.success = false
    ORDER BY billing.billing_date DESC
    LIMIT 1
	";

// 	$ceknim = "SELECT users.va_number AS VA, users.username AS NMCUST, billing.billing_id  , billing.billing_amount
// FROM billing
// JOIN users ON billing.user_id = users.user_id
// WHERE billing.va_number = '" . $VANO . "' 
// AND billing.success = false
// AND billing.billing_id = (
//     SELECT MAX(billing_id)
//     FROM billing
//     WHERE va_number = '" . $VANO . "' 
//     AND success = false
// )
// GROUP BY users.va_number, users.username
// LIMIT 1";




	// $quceknim = mysql_query($ceknim) or die('Wups...');
	// $adanim = mysql_num_rows($quceknim);
	// $datanya = mysql_fetch_array($quceknim);
	$quceknim = $conn->query($ceknim) or die('Wups...');
	$adanim = $quceknim->num_rows;
	$datanya = $quceknim->fetch_array();
	//$Nama = $datanya['NMCUST'];
	$mashemaganteng = $datanya['NMCUST'];

	if (!$quceknim) // jika query error
	{
		$response = array(
			'ERR' => '30', //96
			'METHOD' => 'INQUIRY',
			'DESCRIPTION' => 'Salah format message',
			'CUSTNAME' => '',
			'DESCRIPTION2' => '',
			'BILL' => '0', //+ 2000
			'CCY' => '360'
		);
		// echoing JSON response


	} else {

		if ($adanim == 1) {
	// 		$cektagihan = "SELECT
	// sum(SCCTBILL.BILLAM) as nominal, 
	// SCCTCUST.NMCUST as Nama,
	// SCCTCUST.NUM2ND as NoPend,
	// SCCTCUST.NOCUST as NIM,
	// SCCTBILL.BILLNM as NamaTagihan
	// FROM SCCTBILL
	// LEFT JOIN SCCTCUST on SCCTCUST.CUSTID = SCCTBILL.CUSTID 
	// where 
	// ( LPAD(NOCUST,10,0)='" . $VANO10 . "' or NUM2ND='" . $VANO10 . "')
	// AND SCCTBILL.FSTSBolehBayar = 1 
	// AND SCCTBILL.PAIDST=0 
	// AND SCCTCUST.STCUST = 1
	
	// group by SCCTCUST.NMCUST,
	// SCCTCUST.NUM2ND,
	// SCCTCUST.NOCUST,
	// SCCTBILL.BILLNM,
	// SCCTBILL.FUrutan
	// order by SCCTBILL.FUrutan DESC";
	$cektagihan = "SELECT
    billing.billing_id,
    billing.billing_amount AS nominal,
    billing.username AS Nama,
    billing.phone_number AS NoPend,
    billing.va_number AS NIM,
    billing.category AS NamaTagihan,
    billing.message,
    COALESCE(billing.zakat_id, billing.campaign_id, billing.infak_id, billing.wakaf_id) AS related_id,
    CASE 
        WHEN billing.user_id IS NOT NULL THEN billing.user_id 
        ELSE NULL 
    END AS user_id
FROM billing
WHERE billing.va_number = '" . $VANO . "'
  AND billing.success = 0
GROUP BY billing.billing_id, 
         billing.username, 
         billing.phone_number, 
         billing.va_number, 
         related_id,
         billing.user_id
ORDER BY billing.billing_date DESC 
LIMIT 1;
";



			// $listtagihan = mysql_query($cektagihan) or die('witszz...query tagihan opo ki');
			$listtagihan = $conn->query($cektagihan) or die('witszz...query tagihan opo ki');

			if (!$listtagihan) // jika query error
			{
				$response = array(
					'ERR' => '30', //96
					'METHOD' => 'INQUIRY',
					'DESCRIPTION' => 'Salah format message',
					'CUSTNAME' => '',
					'DESCRIPTION2' => '',
					'BILL' => '0', //+ 2000
					'CCY' => '360'
				);
			} else {

				// $kosong = mysql_num_rows($listtagihan);
				$kosong = $listtagihan->num_rows;
				if ($kosong <> 0) { //jika ada data 
					$total = 0;
					while ($row = $listtagihan->fetch_array()) {
						$custname = str_replace("'", "", $row['Nama']);
						$CUSTNAME = RTRIM(strtoupper($custname));
						$description1 = RTRIM(strtoupper($row['NIM']));
						$description2 = RTRIM($row['NamaTagihan']);
						//$bill = ($row['nominal'] + 3000) * 100;
						$bill = $row['nominal'] * 100;
					} //while

					$response  = array(
						'ERR' => '00', //'PesanRespon' => 'Transaksi disetujui',
						'METHOD' => 'INQUIRY',
						'DESCRIPTION' => $description1,
						'CUSTNAME' => $CUSTNAME,
						'DESCRIPTION2' => $description2,
						'BILL' => $bill, //+ 2000
						'CCY' => '360'
					);
				} else { // jika VA tidak ada

					$response = array(
						'ERR' => '00',
						'METHOD' => 'INQUIRY',
						'DESCRIPTION' => 'TOP UP',
						//'DESCRIPTION' => 'MBUH',
						//'CUSTNAME' => 'UANG SAKU',
						//'CUSTNAME' => $Nama,
						'CUSTNAME' => $mashemaganteng,
						'DESCRIPTION2' => '',
						'BILL' => '0', //+ 2000
						'CCY' => '360'
					);
					//$response["response"] = ";;;;Bill ID not found;15";
				}
			} //if gagal select query
		} elseif ($adanim > 1) {
			$response = array(
				'ERR' => '12',
				'METHOD' => 'INQUIRY',
				'DESCRIPTION' => 'Declined - Invalid transaction',
				'CUSTNAME' => '',
				'DESCRIPTION2' => '',
				'BILL' => '0', //+ 2000
				'CCY' => '360'
			);
		} elseif ($adanim == 0) { //if tidak ada NIM
			$response = array(
				'ERR' => '15',
				'METHOD' => 'INQUIRY',
				'DESCRIPTION' => 'Nomor Identitas Pembayaran tidak ditemukan di basis data Billing Provider',
				'CUSTNAME' => '',
				'DESCRIPTION2' => '',
				'BILL' => '0', //+ 2000
				'CCY' => '360'
			);
		}
	}
	$jwt = JWT::encode($response, $key);
	//echo $cektagihan ;
	echo $jwt;
	//echo json_encode($response);

}elseif (isset($METHOD) && $METHOD == 'PAYMENT' && isset($_GET['token'])) {

	$VANO =  $decoded_array['VANO'];
	$VANO10 = substr($VANO, 6, 15);
	$TRXDATE =  $decoded_array['TRXDATE'];
	$REFNO =  $decoded_array['REFNO'];
	$CHANNELID =  $decoded_array['CHANNELID'];
	$CCY =  $decoded_array['CCY'];
	$BILL =  $decoded_array['BILL'];
	$PAYMENT =  $decoded_array['PAYMENT'];

	$TRXDATE = date_create($TRXDATE);
	$TRXDATE = date_format($TRXDATE, "Y-m-d h:i:s");

	if ($VANO == '' || $TRXDATE == '' || $REFNO == '' || $CHANNELID == '' || $PAYMENT == 0) {
		$response = array(
			'ERR' => '30', //96
			'METHOD' => 'PAYMENT',
			'DESCRIPTION' => 'Salah format message',
			'CUSTNAME' => '',
			'DESCRIPTION2' => '',
			'BILL' => '0', //+ 2000
			'CCY' => '360'
		);
	} else {
$cektagihanbuatinsert = "SELECT billing.billing_id, 
       billing.billing_amount AS nominal, 
       billing.username AS Nama, 
       billing.phone_number AS NoPend,
       billing.va_number AS NIM, 
       billing.category AS NamaTagihan, 
       billing.message, 
       billing.zakat_id,
       billing.campaign_id, 
       billing.infak_id, 
       billing.wakaf_id,
       CASE 
           WHEN billing.user_id IS NOT NULL THEN billing.user_id 
           ELSE NULL 
       END AS user_id
FROM billing
WHERE billing.va_number = '" . $conn->real_escape_string($VANO) . "' 
  AND billing.success = 0
GROUP BY billing.billing_id, 
         billing.username, 
         billing.phone_number, 
         billing.va_number, 
         billing.zakat_id, 
         billing.campaign_id, 
         billing.infak_id, 
         billing.wakaf_id
ORDER BY billing.billing_date DESC 
LIMIT 1;
";

$listtagihanbuatinsert = $conn->query($cektagihanbuatinsert) or die('Query error: ' . $conn->error);

if ($listtagihanbuatinsert->num_rows > 0) { // Masukkan data yang sudah dicek
    while ($row = $listtagihanbuatinsert->fetch_array()) {
        // Penanganan user_id yang mungkin NULL
        $user_id = !empty($row['user_id']) ? $row['user_id'] : 'NULL';

        $query = "INSERT INTO transaction (
            user_id, transaction_amount, transaction_date, ref_no, channel, va_number, method, success, message, 
            category, zakat_id, campaign_id, infak_id, wakaf_id, username, phone_number
        ) VALUES (
            $user_id,
            " . $PAYMENT . ",
            '" . $conn->real_escape_string($TRXDATE) . "',
            '" . $conn->real_escape_string($REFNO) . "',
            'ONLINE',
            '" . $conn->real_escape_string($VANO) . "',
            'VA NUMBER',
            1,
            '" . $conn->real_escape_string($row['message']) . "',
            '" . $conn->real_escape_string($row['NamaTagihan']) . "',
            " . (!empty($row['zakat_id']) ? $row['zakat_id'] : 'NULL') . ",
            " . (!empty($row['campaign_id']) ? $row['campaign_id'] : 'NULL') . ",
            " . (!empty($row['infak_id']) ? $row['infak_id'] : 'NULL') . ",
            " . (!empty($row['wakaf_id']) ? $row['wakaf_id'] : 'NULL') . ",
            '" . $conn->real_escape_string($row['Nama']) . "',
            '" . $conn->real_escape_string($row['NoPend']) . "'
        )";
        
        $insertResult = $conn->query($query);
        
        if (!$insertResult) {
            echo "Error inserting transaction: " . $conn->error;
        } else {
            // Update tabel campaign/zakat/infak/wakaf setelah insert berhasil
            if (!empty($row['campaign_id'])) {
                // Mengambil campaign_code berdasarkan campaign_id
                $getCodeQuery = "SELECT campaign_code FROM campaign WHERE campaign_id = " . $row['campaign_id'];
                $codeResult = $conn->query($getCodeQuery);
                if ($codeResult && $codeRow = $codeResult->fetch_assoc()) {
                    $updateProcedure = "CALL lazismu.update_campaign_current_amount('" . $codeRow['campaign_code'] . "', " . $PAYMENT . ")";
                }
            } elseif (!empty($row['zakat_id'])) {
                // Mengambil zakat_code berdasarkan zakat_id
                $getCodeQuery = "SELECT zakat_code FROM zakat WHERE zakat_id = " . $row['zakat_id'];
                $codeResult = $conn->query($getCodeQuery);
                if ($codeResult && $codeRow = $codeResult->fetch_assoc()) {
                    $updateProcedure = "CALL lazismu.update_zakat_amount('" . $codeRow['zakat_code'] . "', " . $PAYMENT . ")";
                }
            } elseif (!empty($row['infak_id'])) {
                // Mengambil infak_code berdasarkan infak_id
                $getCodeQuery = "SELECT infak_code FROM infak WHERE infak_id = " . $row['infak_id'];
                $codeResult = $conn->query($getCodeQuery);
                if ($codeResult && $codeRow = $codeResult->fetch_assoc()) {
                    $updateProcedure = "CALL lazismu.update_infak_amount('" . $codeRow['infak_code'] . "', " . $PAYMENT . ")";
                }
            } elseif (!empty($row['wakaf_id'])) {
                // Mengambil wakaf_code berdasarkan wakaf_id
                $getCodeQuery = "SELECT wakaf_code FROM wakaf WHERE wakaf_id = " . $row['wakaf_id'];
                $codeResult = $conn->query($getCodeQuery);
                if ($codeResult && $codeRow = $codeResult->fetch_assoc()) {
                    $updateProcedure = "CALL lazismu.update_wakaf_amount('" . $codeRow['wakaf_code'] . "', " . $PAYMENT . ")";
                }
            }

            // Jalankan stored procedure untuk update jumlah
            if (isset($updateProcedure)) {
                $updateResult = $conn->query($updateProcedure);

                if (!$updateResult) {
                    echo "Error updating amount: " . $conn->error;
                }
            }

            // Update tabel billing setelah insert berhasil
            $updateBilling = "UPDATE billing SET success = 1 WHERE va_number = '" . $VANO . "' AND success = 0 ORDER BY billing.billing_date DESC LIMIT 1";
            $updateBillingResult = $conn->query($updateBilling);

            if (!$updateBillingResult) {
                echo "Error updating billing data: " . $conn->error;
            } else {
                // Mengirim pesan WhatsApp otomatis
                $waApiUrl = "https://wa.srv11.wapanels.com/send-message";
                $apiKey = "98057717bd2cec21101df07126a1681f8e31ef13";
                $sender = "6283129363915";
                $number = $row['NoPend'];
                $message = "Terimakasih sudah berdonasi melalui platform Lazismu Kota Semarang";

                $postData = [
                    'api_key' => $apiKey,
                    'sender' => $sender,
                    'number' => $number,
                    'message' => $message
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $waApiUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $output = curl_exec($ch);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($output === false) {
                    echo "Error sending WhatsApp message: " . $curlError;
                } else {
                    $responseDecoded = json_decode($output, true);
                    if ($responseDecoded && $responseDecoded['status'] == 'success') {
                        // Success message or further processing
                        $response = array(
                            'ERR' => '00',
                            'METHOD' => 'PAYMENT',
                            'DESCRIPTION' => 'Top Up Payment Success',
                            'CUSTNAME' => '',
                            'DESCRIPTION2' => '',
                            'BILL' => $PAYMENT,
                            'CCY' => '360'
                        );
                    } else {
                        echo "WhatsApp API responded with an error: " . $output;
                    }
                }
            }
        }
    }
}
 else {
    $response = array(
        'ERR' => '31',
        'METHOD' => 'PAYMENT',
        'DESCRIPTION' => 'No pending billing found',
        'CUSTNAME' => '',
        'DESCRIPTION2' => '',
        'BILL' => '0',
        'CCY' => '360'
    );
}

	

	$jwt = JWT::encode($response, $key);
	echo $jwt;
	//echo $query;
	//echo json_encode($response);
}
}

elseif (isset($METHOD) && $METHOD == 'REVERSAL' && isset($_GET['token'])) {

	$VANO =  $decoded_array['VANO'];
	$VANO10 = substr($VANO, 6, 15);
	$TRXDATE =  $decoded_array['TRXDATE'];
	$REFNO =  $decoded_array['REFNO'];
	$CHANNELID =  $decoded_array['CHANNELID'];
	$CCY =  $decoded_array['CCY'];
	$BILL =  $decoded_array['BILL'];
	$PAYMENT =  $decoded_array['PAYMENT'];
	$PYMTDATE =  $decoded_array['PYMTDATE'];

	$TRXDATE = date_create($TRXDATE);
	$TRXDATE = date_format($TRXDATE, "Y-m-d h:i:s");

	$PYMTDATE = date_create($PYMTDATE);
	$PYMTDATE = date_format($PYMTDATE, "Y-m-d h:i:s");

	if ($VANO == '' || $REFNO == '' || $PYMTDATE == '') {
		$response = array(
			'ERR' => '30', //96
			'METHOD' => 'PAYMENT',
			'DESCRIPTION' => 'Salah format message',
			'CUSTNAME' => '',
			'DESCRIPTION2' => '',
			'BILL' => '0', //+ 2000
			'CCY' => '360'
		);
	} else {

		// $query = "CALL BankRevesal ('" . $VANO . "','" . $REFNO . "','" . $TRXDATE . "','" . $CHANNELID . "','" . $PYMTDATE . "'," . $PAYMENT . ") ";
		$query = "UPDATE transaction SET success = 0
    WHERE va_number = '" . $VANO . "'
    AND ref_no = '" . $REFNO . "'
    AND transaction_date = '" . $PYMTDATE . "'
    AND transaction_amount = " . $PAYMENT;

		/*
			$cekid = "Select id from mst_personal WHERE LPAD(NoInduk, 10, 0)='".$VANO10."' or LPAD(NoLain, 10, 0)='".$VANO10."'";
			$qucekid = mysql_query($cekid) or die('Wups...');
			$row = mysql_fetch_array($qucekid);
			
			$query = "
			UPDATE mst_smartpayment SET StatusBayar=0 WHERE NoTransaksi='".$REFNO."' and PersonalID=".$row['id']." and TanggalBayar='".$PYMTDATE."' and Nominal=".$PAYMENT." and TanggalInput='".date('Y-m-d h:i:s')."'
			";
			*/
		// $repayment = mysql_query($query);
		$repayment = $conn->query($query);

		if (!$repayment) {
			$response = array(
				'ERR' => '12',
				'METHOD' => 'REVERSAL',
				'DESCRIPTION' => 'Declined - Invalid transaction Query',
				'CUSTNAME' => '',
				'DESCRIPTION2' => '',
				'BILL' => '0', //+ 2000
				'CCY' => '360'
			);
		} else {

			$response  = array(
				'ERR' => '00', //'PesanRespon' => 'Transaksi disetujui',
				'METHOD' => 'REVERSAL',
				'DESCRIPTION' => 'Reversal Success',
				'CUSTNAME' => '',
				'DESCRIPTION2' => '',
				'BILL' => $PAYMENT, //+ 2000
				'CCY' => '360'
			);
			//mssql_query("insert into SCCTLOGVA (TRXDATE, Metode, FIDBANK, BILLAM, VANO, NOREFF) values ('".$TRXDATE."','reversal','7511', ".$BILL.", '".$VANO."', '".$REFNO."')");

		}
	} //REVESAL
	$jwt = JWT::encode($response, $key);
	echo $jwt;
	//echo json_encode($response);
} else { //if tidak ada NIM
	$response = array(
		'ERR' => '12',
		'METHOD' => 'REVERSAL',
		'DESCRIPTION' => 'Declined - Invalid transaction Query',
		'CUSTNAME' => '',
		'DESCRIPTION2' => '',
		'BILL' => '0', //+ 2000
		'CCY' => '360'
	);
	$jwt = JWT::encode($response, $key);
	echo $jwt;
	//echo json_encode($response);
}
// Tutup koneksi
$conn->close();
?>