<?php
   require_once('Factual.php');
    $factual = new Factual("QPGlQD5uElzu0toEklOWmcOUysxO5Hpa1Fy7pvaX","Ran91UTDN7FGyRwRrlBurakog8ccoAxiOlnSPgu5");
exit;
	    $tableName = "products-cpg";
		$query = new FactualQuery;
    $query->search("sni");
    //$query->field("brand")->equal("pantene"); //don't hate me because I'm beautiful
		$res = $factual->fetch($tableName, $query); 
		$result=$res->getData();
		echo json_encode($result);
	exit;


if (empty($key) || empty($secret)){
	echo "Your Facual Key and Secret are required parameters. See https://github.com/Factual/factual-php-driver/wiki/Getting-Started for more info\n";
	exit;
}


//Set error level -- best not to change this.
error_reporting (E_ERROR);

require_once('FactualTest.php');
	
//Run tests	
$factualTest = new factualTest($key,$secret);	
$factualTest->setLogFile($logFile);   
$factualTest->test();

?>
