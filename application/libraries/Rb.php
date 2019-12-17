<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retorna um array que pode ser inserido no banco de dados
 * ignora os dados de post que não estejam em fields
 * se for uma chave estrangeira preenche com null
 */
function prepare_fields($fields,$post){
	$f = [];
	foreach($fields as $key){
		if ( isset($post[$key]) ){
			$f[$key] = $post[$key];
		} else
		if (strstr($key,"_id")){#caso seja uma chave estrangeira
			$f[$key] = null;
		}

	}
	return $f;
}

/**
 * Retorna o valor de um array, caso nao exista o valor, retorna em branco
 */
function val($arr,$key,$subKey=""){
	
	if (isset($arr[$key])){
		if ($subKey != null){
			return val($arr[$key],$subKey);
		}
		return $arr[$key];
	} else {
		if (is_bool($subKey)){
			return $subKey;
		} else
		if ($subKey === null){
			return null;
		}
		return "";
	}
	
}


function saveLogs(){
	$logs = R::getDatabaseAdapter()->getDatabase()->getLogger()->getLogs();
	$_SESSION["logs"] = $logs;
}

 
class Rb {
     
    function __construct() {
        // Include database configuration
		include(APPPATH.'/config/database.php');
		// Get Redbean
		include(APPPATH.'/third_party/rb/rb.php');
         
         
        // Database data
        $host 	= $db[$active_group]['hostname'];
        $user 	= $db[$active_group]['username'];
        $pass 	= $db[$active_group]['password'];
		$driver = $db[$active_group]['dbdriver'];
		$dsn 	= $db[$active_group]['dsn'];
        $db 	= $db[$active_group]['database'];
		
		
		if ($driver == "pdo"){
			// SQLITE
			R::setup( $dsn );
		} else
		if ($driver == "mysqli"){
			// MYSQL
			R::setup("mysql:host=$host;dbname=$db", $user, $pass);
		} else {
			// POSTGRES
			R::setup("pgsql:host=$host;dbname=$db", $user, $pass);
		}


		if (ENVIRONMENT == "production"){
			$_SESSION["logs"] = [];
			R::freeze(true);
		}
		R::fancyDebug();
		R::getDatabaseAdapter()->getDatabase()->getLogger()->setMode(1);
		
        
	} //end __contruct()



} //end Rb
