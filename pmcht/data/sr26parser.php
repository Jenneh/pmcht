<?php
	/*	
		TODO:
		-Take into account common sense serving sizes for foods
			-This will be done $foodDefs
	*/
	
	$dbName = "sr26.db";
	$fileOutputName = "pmchtFoodDefs.json";
	$FOOD_DES_NDB_Nos = [9040, 19093, 9202, 9003, 28195, 1118, 1080, 21271, 11090, 11529, 8640, 8288, 21250, 18069, 18064, 6395, 11124, 19095, 21472, 1009];
	
	/*
	$foodDefs = [
		[
			"NDB_No"=>9040,
			"Seq"=>4 // see WEIGHT table
		]
	];
	*/
	
	$recommendations = [
		"PROCNT"=>50,
		//"PROCNT"=>30,
		"FAT"=>65,
		//"CHOCDF"=>300, // cholesteral handled below
		"ENERC_KCAL"=>2000,
		"WATER"=>226.769,
		//"caffeine"=>,
		//"sugar"=>,
		"FIBTG"=>25,
		"CA"=>1000,
		"FE"=>18,
		"MG"=>400,
		"P"=>1000,
		"K"=>3500,
		"NA"=>2400,
		"ZN"=>15,
		"CU"=>2,
		"MN"=>2,
		//"selenium"=>, // null units
		"VITA_IU"=>5000,
		//"carotene"=>, // null units
		"TOCPHA"=>30, // IU, need to convert to MG- MG is stored in DB
		"VITD"=>400,
		"VITC"=>60,
		"THIA"=>1.5,
		"RIBF"=>1.7,
		"NIA"=>20,
		"PANTAC"=>10,
		"VITB6A"=>2,
		"VITB12"=>6, // null units
		"VITK1"=>7, // null units
		"CHOLE"=>300,
		"FASAT"=>20, // saturated fat
	];
	
	
	$db = new PDO("sqlite:$dbName");	
	
	$foodDefinitions = [];
	foreach($FOOD_DES_NDB_Nos as $no) {
		$foodDefinitions []= returnFoodDefinitionAsArray($db, $no, $recommendations);
	}
	//var_dump($foodDefinitions);
	file_put_contents($fileOutputName, json_encode($foodDefinitions, JSON_PRETTY_PRINT));
	
	function returnFoodDefinitionAsArray($db, $FOOD_DES_NDB_No, $recommendations) {
		$foodDefinition;
		foreach($db->query("select FOOD_DES.NDB_No, FOOD_DES.Long_Desc, FD_GROUP.FdGrp_Desc from FOOD_DES join FD_GROUP on FOOD_DES.FdGrp_Cd = FD_GROUP.FdGrp_Cd where NDB_No = $FOOD_DES_NDB_No", PDO::FETCH_ASSOC) as $FOOD_DES) {
			$foodDefinition = $FOOD_DES;
		}
		$foodDefinition['nutrition'] = [];
		foreach($db->query("select NUT_DATA.Nutr_Val as val, NUTR_DEF.Tagname, NUTR_DEF.NutrDesc, NUTR_DEF.Units from NUT_DATA join NUTR_DEF on NUT_DATA.Nutr_No = NUTR_DEF.Nutr_No where NUT_DATA.NDB_NO = $FOOD_DES_NDB_No", PDO::FETCH_ASSOC) as $NUT) {
			if(array_key_exists($NUT['Tagname'], $recommendations)) {
				$NUT['tokens'] = getTokens($NUT['val'], $recommendations[$NUT['Tagname']]);
				$foodDefinition['nutrition'] []= $NUT;
			}
		}
		return $foodDefinition;
	}
	
	function getTokens($value, $recommendation) {
		$RECOMMENDATION_TOKENS = 10;
		$MEALS_PER_DAY = 1;
		$tokens = round(($value / $recommendation) * ($RECOMMENDATION_TOKENS * $MEALS_PER_DAY), 0);
		if($tokens == 0 && $value > 0) $tokens = 1;
		return $tokens;
	}
?>