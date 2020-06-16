<?
  class DB{
	  private $server,$user,$pass,$dbname,$db, $conection;
	  
	  function __construct($server,$user,$pass,$dbname)
      {
         $this->server = $server;
         $this->user = $user;
         $this->pass = $pass;
         $this->dbname = $dbname;
         $this->openConnection();                 
      }
	  
	  public function openConnection()  {
		  if(!$this->db)  {
			  $connection = @mysql_connect($this->server,$this->user,$this->pass);
			  if ($connection) {
				  $selectDB = @mysql_select_db($this->dbname,$connection);
				  if($selectDB) {
					  $this -> db = true;
					  mysql_query('SET NAMES UTF8');
                      return true;
				  }
                  else return false;				  
			  }
			  else return false;
		  }
		  else return true;
	  }
	  
	  public function executeQuery($query){
          
		  $result = mysql_query($query);
		  if($result){
			  return $result;
			   
		  }
		  else return false;
	  }
	  
	  public function closeConnection() {
		  @mysql_close( $this->$connection );
	  }
  }
?>