<?php

// Car Stuff

class cl_car{

	private $make;
	private $model;
	private $trans;
	private $horse;
	private $code;
	private $cyl;

	public function __construct($aMake, $aModel, $aCyl, $aHorse, $aEngine, $aTrans){

		$this->make = $aMake;
		$this->model = $aModel;
		$this->trans = $aTrans;
		$this->horse = $aHorse;
		$this->code = $aEngine;
		$this->cyl = $aCyl;

	}


	public function getMakeAndModel(){

		return '<b class="text-danger">Make:</b> ' . $this->make . '<br> <b class="text-warning">Model:</b> ' . $this->model . '<br> <b class="text-success">Transmission:</b> ' . $this->trans . '<br> <b class="text-info">Horse Power:</b> ' . $this->horse . '<br> <b>Engine Code:</b> ' . $this->code . '<br> <b class="text-muted">Number of Cylinders:</b> ' . $this->cyl;

	}


}

class cl_carFactory{

	public static function create($aMake, $aModel, $aHorse, $aCyl, $aEngine, $aTrans){

		return new cl_car($aMake, $aModel, $aCyl, $aHorse, $aEngine, $aTrans);

	}

}

// Page Stuff

class cl_main{

	private $cArray = [];

	public function __construct($aCSV){

		$this->cArray = $this->readCSV($aCSV);

		$page_request = 'cl_carChoose';

		if(!empty($_REQUEST)){

			$page_request = $_REQUEST['page'];
			if(!($page_request == "cl_carChoose") && !($page_request == "cl_carShow")){
				$page_request = "cl_carChoose";
			}
		}

		$page = new $page_request($this->getCSV());

		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			
			$page->get();
		
		}else{
			
			//defaulting to post, pretty much
			$page->post();
		
		}

	}

	public function readCSV($aCsvFile){

		$loc_LoT = array();
		$i = 0;
		$loc_handle = fopen($aCsvFile, 'r');

		while(($row = fgetcsv($loc_handle, 1024)) !== false){

			foreach($row as $k=> $value){

				$loc_LoT[$i][$k] = strtoupper($value);

			}

			$i++;
		}

		fclose($loc_handle);
		return $loc_LoT;

	}

	public function getCSV(){

		return $this->cArray;
	
	}

}

class cl_page{

	//This class doesn't get and post since, it isn't necessary
	private $array_of_cars;

	public function __construct($aArray){

		$this->array_of_cars = $aArray;

	}

	public function getArray(){

		return $this->array_of_cars;
	
	}

}

class cl_carChoose extends cl_page{

	private $make_array = [];
	private $model_array = [];
	private $cylin_array = [];
	private $horse_array = [];
	private $engine_array = [];
	private $trans_array = [];

	public function get(){

		$locArray = $this->getArray();

		foreach($locArray as $row => $car){

			$this->makeSelector($car, $this->make_array, 0);
			$this->makeSelector($car, $this->model_array, 1);
			$this->makeSelector($car, $this->cylin_array, 2);
			$this->makeSelector($car, $this->horse_array, 3);
			$this->makeSelector($car, $this->engine_array, 4);
			$this->makeSelector($car, $this->trans_array, 5);
		
		}

		$this->createForm($this->make_array,$this->model_array,$this->cylin_array,$this->horse_array,$this->engine_array,$this->trans_array);

	}

	public function post(){
		
		$locArray = $this->getArray();

		foreach($locArray as $row => $car){

			$this->make_array = $this->makeSelector($car, $this->make_array, 0);
			$this->model_array = $this->makeSelector($car, $this->model_array, 1);
			$this->cylin_array = $this->makeSelector($car, $this->cylin_array, 2);
			$this->horse_array = $this->makeSelector($car, $this->horse_array, 3);
			$this->engine_array = $this->makeSelector($car, $this->engine_array, 4);
			$this->trans_array = $this->makeSelector($car, $this->trans_array, 5);
		
		}

		$this->createForm($this->make_array,$this->model_array,$this->cylin_array,$this->horse_array,$this->engine_array,$this->trans_array);

	}

	public function makeSelector($aCSV, $aArrCh, $aIn){

		if(!in_array(strtoupper($aCSV[$aIn]), $aArrCh)){

			$aArrCh[] = strtoupper($aCSV[$aIn]);
		
		}

		return $aArrCh;

	}

	public function createForm($aMake, $aModel, $aHorse, $aCyl, $aEngine, $aTrans){

		
		echo '<form method="post">';
		$this->createSelector("col-md-12", $aMake, "make");
		$this->createSelector("col-md-12", $aModel, "model");
		$this->createSelector("col-md-12", $aHorse, "horsepower");
		$this->createSelector("col-md-12", $aCyl, "cylinders");
		$this->createSelector("col-md-12", $aEngine, "engine");
		$this->createSelector("col-md-12", $aTrans, "transmission");
		echo '<hr>';
		echo '<button class="col-md-1.5 btn btn-info bt-lg" type="submit" name="page" value="cl_carShow">Give Me The Carfax</button>';
		echo ' 	</form>';
		echo '</div>';
	
	}

	public function createSelector($aClass, $aArray, $aName){
		
		echo '<center><div class="item '. $aClass . '">';
		echo '<h4>Choose your ' . $aName . '<br></h4>';
		echo '<select class="btn btn-danger bt-sm" name="' . $aName . '">';
		echo ' 		<option value="*">All</option>';
		echo ' 		<option value="N/A">N/A</option>';

		foreach($aArray as $row => $part){
			echo '	<option value="' . $part . '">' . $part . '</option>';
		}
		
		echo '	</select>';
		echo '</div>';
	
	}
}

class cl_carShow extends cl_page{

	private $cMake;
	private $cModel;
	private $cHorse;
	private $cCylin;
	private $cEngine;
	private $cTrans;

	public function get(){

		if(!empty($_REQUEST)){
			$this->cMake = $_REQUEST['make'];
			$this->cModel = $_REQUEST['model'];
			$this->cHorse = $_REQUEST['horsepower'];
			$this->cCylin = $_REQUEST['cylinders'];
			$this->cEngine = $_REQUEST['engine'];
			$this->cTrans = $_REQUEST['transmission'];
		}else{
			$this->cMake = "*";
			$this->cModel = "*";
			$this->cHorse = "*";
			$this->cCylin = "*";
			$this->cEngine = "*";
			$this->cTrans = "*";
		}

		$this->readCars("Make", $this->cMake, $this->getArray(), 0);
		$this->readCars("Model", $this->cMake, $this->getArray(), 1);
		$this->readCars("Horsepower", $this->cMake, $this->getArray(), 2);
		$this->readCars("Cylinder Count", $this->cMake, $this->getArray(), 3);
		$this->readCars("Engine Code", $this->cMake, $this->getArray(), 4);
		$this->readCars("Trasmission", $this->cMake, $this->getArray(), 5);
		echo '<form method="POST">';
		echo '<button class="btn btn-info" type="submit" name="page" value="cl_carChoose">Bring Me Back</button>';
		echo '</form>';

	}

	public function post(){
		
		if(!empty($_REQUEST)){
			$this->cMake = $_REQUEST['make'];
			$this->cModel = $_REQUEST['model'];
			$this->cHorse = $_REQUEST['horsepower'];
			$this->cCylin = $_REQUEST['cylinders'];
			$this->cEngine = $_REQUEST['engine'];
			$this->cTrans = $_REQUEST['transmission'];
		}else{
			$this->cMake = "*";
			$this->cModel = "*";
			$this->cHorse = "*";
			$this->cCylin = "*";
			$this->cEngine = "*";
			$this->cTrans = "*";
		}

		$this->readCars("Make", $this->cMake, $this->getArray(), 0);
		$this->readCars("Model", $this->cModel, $this->getArray(), 1);
		$this->readCars("Horsepower", $this->cHorse, $this->getArray(), 2);
		$this->readCars("Cylinder Count", $this->cCylin, $this->getArray(), 3);
		$this->readCars("Engine Code", $this->cEngine, $this->getArray(), 4);
		$this->readCars("Transmission", $this->cTrans, $this->getArray(), 5);
		echo '<br/>';
		echo '<form class="container" method="POST">';
		echo ' 	<button class="btn btn-info bt-lg" type="submit" name="page" value="cl_carChoose">Bring Me Back</button>';
		echo '</form>';
		echo '<br/>';
		echo '<br/>';
		echo '<br/>';
		echo '<br/>';

	}

	public function readCars($aName, $aVar, $aCars, $aInd){

		echo '<div class="container">';
		echo '<h1 class="jumbotron">By ' . $aName . ': ' . $aVar . '</h1><br>';

		foreach($aCars as $row => $comp){

			if( $aVar == "*" || ($aVar != "*" && $aVar == $comp[$aInd])){

				$car = cl_carFactory::create($comp[0], $comp[1], $comp[2], $comp[3], $comp[4], $comp[5]);
				echo '<div class="col-md-3">';
				print_r($car->getMakeAndModel());
				echo '<p><hr>';
				echo '</div>';
				unset($car);
			
			}

		}
		echo '</div>';
		echo '<br/><hr>';

	}

}

?>