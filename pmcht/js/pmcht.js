define(['text!templates/pmcht.html', 'text!data/pmchtFoodDefs.json', 'text!templates/foodTooltip.html'], function(template, foodDefs, tooltipTemplate) {
	
	var nutrDefs = {
		"PROCNT": {
			name: "Protein",
			puzzleMin: 5,
			puzzleMax: 10
		},
		"FAT": {
			name: "Fat",
			puzzleMin: 5,
			puzzleMax: 10
		},
		"ENERC_KCAL": {
			name: "Calories",
			puzzleMin: 5,
			puzzleMax: 10
		},
		"FIBTG": {
			name: "Fiber",
			puzzleMin: 5,
			puzzleMax: 10
		}
	};
	
	//	Map foodDefs to foods
	var foodObjs = _.map(JSON.parse(foodDefs), function(foodObj) {
		foodObj.img = foodObj.NDB_No + ".png";
		return foodObj;
	});
	
	console.log(foodObjs);
	
	/*
	TODO: implement createpuzzle algorithm
	
	function createPuzzle() {
		var uncheckedFood = foodObjs;
		var passedFoods = [];
		var selectedFoods = [];
		
		return;
		
		function constuctCurrentValues() {
			
		};
		
		function checkCurrentValues() {
		
		};
	};
	
	console.log(createPuzzle());
	*/
	
	function start(el) {
		
		var $el = $(el);
		
		$el.html(_.template(template, {
			foods: foodObjs,
			foodsImgDir: 'img/foods/'
		}));
		
		$el.find('.food').tooltip({
			track: true,
			content: function() {
				var id = $(this).data('id');
				var foodObj = _.find(foodObjs, function(obj) { return id == obj.NDB_No; });
				return _.template(tooltipTemplate, {
					nutrientImgDir: 'img/nutrientIcons/',
					foodName: foodObj.Long_Desc,
					nutrients: _.filter(foodObj.nutrition, function(nutrient) {
						var nutrObj;
						if(nutrObj = nutrDefs[nutrient.Tagname]) {
							nutrient.name = nutrObj.name;
							nutrient.img = nutrient.Tagname + ".png";
							return true;
						};
					})
				});
			},
			position: {
				//my: 'center bottom-50',
				//at: 'center top'
			}
		}).draggable({
			//revert: "invalid"
			activeClass: "hoverFood"
		});
		
		$el.find(".plate").droppable({
			accept: ".food",
			hoverClass: "",
			activeClas: "",
			over: function(event, ui) {
				console.log(ui);
				$(ui.draggable).addClass('foodOver');
			},
			out: function(event, ui) {
				console.log(ui);
				$(ui.draggable).removeClass('foodOver');
			}
		});
	};
	
	return {
		SMACK: start
	};
});