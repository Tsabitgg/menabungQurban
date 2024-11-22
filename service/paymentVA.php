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
$db_host = '10.99.23.111:3306';
$db_username = 'elpe';
$db_password = 'Bismillah99';
$db_name = 'menabung_qurban';

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tagihans = array();


$decoded = $transaksi->decode($_GET['token'], $key, array('HS256'));

$decoded_array = (array) $decoded;

$METHOD =  $decoded_array['METHOD'];

if (isset($METHOD) && $METHOD == 'INQUIRY' && isset($_GET['token'])) {
	$VANO =  $decoded_array['VANO'];
	$TRXDATE =  $decoded_array['TRXDATE'];
	$REFNO =  $decoded_array['REFNO'];
	$CHANNELID =  $decoded_array['CHANNELID'];

	$TRXDATE = date_create($TRXDATE);

	$TRXDATE = date_format($TRXDATE, "Y-m-d h:i:s");
	$ceknim = "SELECT tagihan.va_number AS VA, users.nama AS NMCUST
    FROM tagihan JOIN users 
    ON tagihan.user_id = users.user_id 
	WHERE tagihan.va_number = '7977542762812768' 
    AND tagihan.success = false
    ORDER BY tagihan.tanggal_tagihan DESC
    LIMIT 1;";


	$quceknim = $conn->query($ceknim) or die('Wups...');
	$adanim = $quceknim->num_rows;
	$datanya = $quceknim->fetch_array();

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
			$cektagihan = "
			select tagihan.tagihan_id,
				tagihan.total_tagihan AS nominal,
				users.nama AS Nama,
				tagihan.va_number AS NIM,
				qurban.tipe_qurban AS NamaTagihan,
				tagihan.user_id,
				tagihan.kartu_qurban_id,
				tagihan.biaya_admin,
				tagihan.jumlah_setoran,
				tagihan.metode_pembayaran
			FROM tagihan
			JOIN users ON tagihan.user_id = users.user_id
			JOIN kartu_qurban ON tagihan.kartu_qurban_id = kartu_qurban.kartu_qurban_id
			JOIN qurban ON kartu_qurban.qurban_id = qurban.qurban_id
			WHERE tagihan.va_number = '" . $VANO . "'
			  AND tagihan.success = 0
			  and tagihan.total_tagihan is not null
			ORDER BY tagihan.tanggal_tagihan, tagihan.tagihan_id DESC
			LIMIT 1;";			

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
				'DESCRIPTION' => 'Nomor Identitas Pembayaran tidak ditemukan di basis data tagihan Provider',
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
    $TRXDATE = date_format($TRXDATE, "Y-m-d");

    if ($VANO == '' || $TRXDATE == '' || $REFNO == '' || $CHANNELID == '' || $PAYMENT == 0) {
        $response = array(
            'ERR' => '30',
            'METHOD' => 'PAYMENT',
            'DESCRIPTION' => 'Salah format message',
            'CUSTNAME' => '',
            'DESCRIPTION2' => '',
            'BILL' => '0',
            'CCY' => '360'
        );
    } else {
        $cektagihanbuatinsert = "SELECT tagihan.tagihan_id,
                                    tagihan.total_tagihan AS nominal,
                                    users.nama AS Nama,
                                    tagihan.va_number AS NIM,
                                    qurban.tipe_qurban AS NamaTagihan,
                                    tagihan.user_id,
                                    tagihan.kartu_qurban_id,
                                    tagihan.biaya_admin,
                                    tagihan.jumlah_setoran,
                                    tagihan.metode_pembayaran
                                FROM tagihan
                                JOIN users ON tagihan.user_id = users.user_id
                                JOIN kartu_qurban ON tagihan.kartu_qurban_id = kartu_qurban.kartu_qurban_id
                                JOIN qurban ON kartu_qurban.qurban_id = qurban.qurban_id
                                WHERE tagihan.va_number = '" . $VANO . "'
                                  AND tagihan.success = 0
                                  AND tagihan.total_tagihan IS NOT NULL
                                ORDER BY tagihan.tanggal_tagihan, tagihan.tagihan_id DESC
                                LIMIT 1";

        $listtagihanbuatinsert = $conn->query($cektagihanbuatinsert) or die('Query error: ' . $conn->error);

        if ($listtagihanbuatinsert->num_rows > 0) {
            while ($row = $listtagihanbuatinsert->fetch_array()) {
                // Penanganan user_id yang mungkin NULL
                $user_id = !empty($row['user_id']) ? $row['user_id'] : 'NULL';

                $query = "INSERT INTO transaksi (
                    user_id, total_tagihan, tanggal_transaksi, ref_no, va_number, metode_pembayaran, 
                    success, tagihan_id, biaya_admin, jumlah_setoran, kartu_qurban_id
                ) VALUES (
                    $user_id,
                    " . $PAYMENT . ",
                    '" . $conn->real_escape_string($TRXDATE) . "',
                    '" . $conn->real_escape_string($REFNO) . "',
                    '" . $conn->real_escape_string($VANO) . "',
                    '" . $conn->real_escape_string($row['metode_pembayaran']) . "',
                    1,
                    '" . $conn->real_escape_string($row['tagihan_id']) . "',
                    '" . $conn->real_escape_string($row['biaya_admin']) . "',
                    '" . $conn->real_escape_string($row['jumlah_setoran']) . "',
                    '" . $conn->real_escape_string($row['kartu_qurban_id']) . "'
                )";

                $insertResult = $conn->query($query);

                if (!$insertResult) {
                    echo "Error inserting transaction: " . $conn->error;
                } else {
                    // Update kolom jumlah_terkumpul pada kartu_qurban
                    $updateJumlahTerkumpul = "UPDATE kartu_qurban 
                                              SET jumlah_terkumpul = jumlah_terkumpul + '" . $conn->real_escape_string($row['jumlah_setoran']) . "' 
                                              WHERE kartu_qurban_id = '" . $conn->real_escape_string($row['kartu_qurban_id']) . "'";
                    $updateResult = $conn->query($updateJumlahTerkumpul);

                    if (!$updateResult) {
                        echo "Error updating jumlah_terkumpul: " . $conn->error;
                    }

                    // Update tabel tagihan
                    $updatetagihan = "UPDATE tagihan 
                                      SET success = 1 
                                      WHERE va_number = '" . $VANO . "' 
                                        AND success = 0 
                                      ORDER BY tagihan.tanggal_tagihan, tagihan.tagihan_id DESC LIMIT 1";
                    $updatetagihanResult = $conn->query($updatetagihan);

                    if (!$updatetagihanResult) {
                        echo "Error updating tagihan data: " . $conn->error;
                    } 
                }
				if ($insertResult && $updateResult && $updatetagihanResult) {
					$response = array(
						'ERR' => '00',
						'METHOD' => 'PAYMENT',
						'DESCRIPTION' => 'Top Up Payment Success',
						'CUSTNAME' => $row['Nama'],
						'DESCRIPTION2' => $row['NamaTagihan'],
						'BILL' => $PAYMENT,
						'CCY' => '360'
					);
				} else {
					$response = array(
						'ERR' => '32',
						'METHOD' => 'PAYMENT',
						'DESCRIPTION' => 'Transaction or update failed',
						'CUSTNAME' => '',
						'DESCRIPTION2' => '',
						'BILL' => '0',
						'CCY' => '360'
					);
				}

            }
        } else {
            $response = array(
                'ERR' => '31',
                'METHOD' => 'PAYMENT',
                'DESCRIPTION' => 'No pending tagihan found',
                'CUSTNAME' => '',
                'DESCRIPTION2' => '',
                'BILL' => '0',
                'CCY' => '360'
            );
        }
    }

    $jwt = JWT::encode($response, $key);
    echo $jwt;
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
		$query = "UPDATE transaksi SET success = 0
    WHERE va_number = '" . $VANO . "'
    AND ref_no = '" . $REFNO . "'
    AND tanggal_transaksi = '" . $PYMTDATE . "'
    AND total_tagihan = " . $PAYMENT;

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