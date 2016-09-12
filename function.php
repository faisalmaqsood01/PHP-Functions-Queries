<?php 
/*Site Base url */
define("BASEURL","http://localhost/ims/");
define("REPORT","".BASEURL."reports/");
define("STOCK","".BASEURL."stock/");


class dbmanager{
	/*Database Details*/
	private $host 	= "localhost";
	private $user 	= "root";
	private $pass 	= "";
	private $db 	= "inventory";
	/*Variables*/
	private $conn;
	private $result;
	private $query;

	function __construct(){
		$this->conn = $this->dbconn();
	}

	// Database Connection 
	function dbconn(){
		$this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
		if ($this->conn->connect_error){
			die('Connection Problem : ' .$this->conn->connect_error);
		}
		else{
			return $this->conn;
		}
	}


	function run_query($query){
		$this->query = $this->conn->query($query);
		if(!$this->query){
			echo "Invalid Query : ".$this->conn->error;
		}
		else{
			return $this->query;
		}
	}
	// Fetch Query Last Auto Generated Inserted ID 
	function last_id(){
		return $this->conn->insert_id;
	}

	// Fetching Single Record
	function fetch_single($query){
		$this->result = $this->conn->query($query);
		if ($this->conn->error) {
			die('Invalid Query :'. $this->conn->error);
		}
		else{
			if ($this->result->num_rows > 0) {
				return $this->result->fetch_array();
			}

		}
	}

	// Fetching Multiple Records 
	function fetch_all( $query){
		$this->result = $this->conn->query($query);
		if ($this->conn->error) {
			die ('Invalid Query: '. $this->conn->error);		
		}
		else{
			if ($this->result->num_rows > 0 ) {
				while($row = $this->result->fetch_assoc()){
					$data[] = $row; 
				}
				return $data; 
			}
			else{
				return $this->conn->error;	
			}
		}
	}

	// count number rows
	function num_rows($result){
		return $this->result->num_rows;
	}

	function escape_string($str){
		return $this->conn->escape_string($str);
	}

	// Quries
	public function insert($table, $inserts) {
		$values = 	array_values($inserts);
		$keys = 	array_keys($inserts);
		return $this->run_query('INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) VALUES (\''.implode('\',\'', $values).'\')');
	}

	public function delete($table, $where , $id) {
		return $this->run_query('DELETE FROM `'.$table.'` WHERE `'.$where.'` = "'.$id.'" ');
	}

	public function select_all($table)
	{
		return $this->run_query('SELECT * FROM `'.$table.'`');
	}

	public function select_where_single($table , $key , $id)
	{
		return $this->fetch_single('SELECT * FROM `'.$table.'` WHERE `'.$key.'` = "'.$id.'" ');
	}

	public function select_2where_single($table , $key1 , $id1 , $key2 , $id3)
	{
		return $this->fetch_single('SELECT * FROM `'.$table.'` WHERE `'.$key1.'` = "'.$id1.'" AND `'.$key2.'` = "'.$id3.'" ');
	}	
	// select all where key ==
	public function select_where_all($table , $key , $id)
	{
		return $this->run_query('SELECT * FROM `'.$table.'` WHERE `'.$key.'` = "'.$id.'" ');
	}

		

	// select query select table where id = id and id = id
	public function select_wa_all($table , $wherekey1 , $value1 , $wherekey2 , $value2)
	{
		return $this->run_query(' SELECT * FROM `'.$table.'` WHERE `'.$wherekey1.'` = "'.$value1.'" AND `'.$wherekey2.'` = "'.$value2.'" ');
	}	

	// single update query
	public function update_single_where_key($table , $key , $value , $where , $id)
	{	

		return $this->run_query('UPDATE `'.$table.'` SET `'.$key.'` = "'.$value.'" WHERE `'.$where.'` = '.$id.' ');
	}	

	// SELECT COUNT(*) FROM `order` WHERE `where1` = 'value1'  AND WHERE `where2` = 'value2' 
	public function count($table , $where1 , $value1 , $where2 , $value2)
	{	

		return $this->fetch_single(' SELECT COUNT(*) FROM `'.$table.'` WHERE `'.$where1.'` = "'.$value1.'"  AND  `'.$where2.'` = "'.$value2.'" ');
	}

	//  public function update_all($table, $updateKaData, $where , $id)
	// {
	// 	$values = 	array_values($updateKaData);
	// 	$keys = 	array_keys($updateKaData);
 //        return $this->run_query('UPDATE `'.$table.'`        SET `'.$key.'` = "'.$value.'"                WHERE `'.$where.'` = '.$id.' ');
 //    }

    public function update_all($table, $updateKaData, $where , $id){
        //array('id'=>3);
        //array('name'=>'waqas','email'=>'waqas@gmail.com');
        $x=null;
        foreach($updateKaData as $key=>$val)
        {
            $x.="`".$key."`='".$val."',";
        }
        $x=substr($x,0,-1);
        //UPDATE table set `col1`='val1',`col2`='val2' where id=1
	     // var_dump('UPDATE '.$table.' SET   WHERE `'.$where.'` = "'.$id.'" ');
         // var_dump("UPDATE ".$table." SET ".$x." WHERE ".$where."` = ".$id." ");

        mysql_query('UPDATE `'.$table.'` SET `'.$x.'" WHERE `'.$where.'` = '.$id.' ');
    }





} // End DB Manger Class



function useractive(){
	if (!isset($_SESSION['username'])) {
		echo '<script type="text/javascript">document.location = "'.BASEURL.'login.php";</script>';
	}
}



function sendmail($to, $name, $subject, $body){

	require 'PHPMailer/class.phpmailer.php';
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug = 1;
	$mail->SMTPAuth  = true;
	    $mail->Host      = "mail.facecognize.com"; // SMTP server    
	    $mail->Port      = 25;
	    $mail->IsHTML(true);
	    $mail->Username  = "support@facecognize.com";
	    $mail->Password  = "Admin@12'3;456789";
	    $mail->From      = "support@facecognize.com"; 
	    $mail->FromName  = "FaceCognize";
	    $mail->AddAddress($to);
	    $mail->Subject   = $subject;    
	    $mail->Body      = $body;   
	    $mail->AddAddress($to, $name);
	    $status = $mail->send();
	    if(!$status) {
	    	echo '<script>alert("Mail Could not sent.");</script>';
	    	echo 'Mailer Error: ';
	    }
	    else{
	    	return true;
	    }
	}

function post($value)
{
	return $_POST[''.$value.''];
}





// function seourl($string) {
//     //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
// 	$string = strtolower($string);
//     //Strip any unwanted characters
// 	$string = preg_rep lace("/[^a-z0-9_\s-]/", "", $string);
//     //Clean multiple dashes or whitespaces
// 	$string = preg_replace("/[\s-]+/", " ", $string);
//     //Convert whitespaces and underscore to dash
// 	$string = preg_replace("/[\s_]/", "-", $string);
// 	return $string;
// }


function securepass($password){
	$passwordmd5 = md5($password);
	$hash ="sha1";
	$password = hash($hash, $passwordmd5);
	return $password;
}



// for encrpt data 
function encryptIt( $q ) {
    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
    $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    return( $qEncoded );
}
// for decrypt data 
function decryptIt( $q ) {
    $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
    $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
    return( $qDecoded );
}



function get_timeago( $ptime )
{
	$etime = time() - $ptime;
	if( $etime < 1 )
	{
		return 'less than '.$etime.' second ago';
	}
	$a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
		30 * 24 * 60 * 60       =>  'month',
		24 * 60 * 60            =>  'day',
		60 * 60             =>  'hour',
		60                  =>  'minute',
		1                   =>  'second'
		);
	foreach( $a as $secs => $str )
	{
		$d = $etime / $secs;
		if( $d >= 1 )
		{
			$r = round( $d );
			return  $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
		}
	}
}


$obj = new dbmanager;