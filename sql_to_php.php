<?php
class sql_to_php {
  //SQLSRV
  private $Connection     = Null;
  private $Server         = Null;
  private $Database       = Null;
  private $Username       = Null;
  private $Password       = Null;
  //SQL SCHEMA
  private $Tables         = Null;
  private $Table_Columns  = Null;
  function __set_array($array){
    if(is_array($array) && count($array) > 0){
			foreach($array as $key=>$value){
				if($this->__isset($key)){
					$this->$key = $value;
				}
			}
		}
  }
  function __set($key, $value){
		if($this->__isset($key)){
			$this->$key = $value;
		}
	}
  function __get($key){
    if(is_array($key) && count($key) > 0){
      $array = $key;
      unset($key);
      foreach($array as $key=>$value){
        unset($array[$key]);
        if($this->__isset($key)){
          $array[$key] = $this->__get($key);
        } elseif($this->__isset($value)){
          $array[$value] = $this->__get($key);
        }
      }
      return $array;
    } elseif($this->__isset($key)){
      return $this->$key;
    }
  }
  function __isset($key){
		return property_exists($this,$key);
	}
  public function __construct($Server = Null, $Database = Null, $Username = Null, $Password = Null){
    self::__set_array(array($Server, $Database, $Username, $Password));
    if(self::check_server_ip()){
      self::connect_to_database();
      self::collect_tables();
      self::collect_columns();
    }
  }
  private function check_ip(){return ip2long(parent::__get($Server)) !== false;}
  private function return_connection_options(){
    return array(
      'Database'  =>  $this->__get('Dataabase'),
      'UID'       =>  $this->__get('Username'),
      'Password'  =>  $this->__get('Password')
    );
  }
  private function connect_to_database(){$this->__set("Connection",sqlsrv_connect($this->__get('Server'), self::return_connection_options()));}
  private function collect_tables(){
    $r = sqlsrv_query($this->__get('Connection'), "SELECT TABLES.TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE';");
    $Tables = array();
    if($r){while($row = sqlsrv_fetch_array($r)){
      array_push($Tables, $row['TABLE_NAME']);
    }}
    $this->__set("Tables", $Tables);
  }
  private function collect_columns(){
    $Table_Columns = array();
    foreach($this->__get('Tables') AS $index=>$Table){
      $r = sqlsrv_query($this->__get('Connection'), "SELECT COLUMNS.COLUMN_NAME, COLUMNS.DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?;",array($Table));
      $Table_Columns[$Table] = array();
      if($r){while($row = sqlsrv_fetch_array($r)){
        $Table_Columns[$Table][$row['COLUMN_NAME']] = $row['DATA_TYPE'];
      }}
    }
    $this->__set('Table_Columns', $Table_Columns);
  }
}
?>
