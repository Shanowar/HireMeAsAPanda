(function($) {

    $(document).ready(function() {

   // Gestion de l'affichage au clic sur le bouton    
	   $("#buttonGraph").click(function(){

	   	if( $("#myChart").hasClass("invisible") ){

	   		$("#myChart").removeAttr("class", "invisible");
	   		$("table").addClass("invisible");
	   		tableParse();

	   	} else {

	   		$("table").removeAttr("class", "invisible");
	   		$("#myChart").addClass("invisible");
	   	}

	});

  	function tableParse(){
  	 	// Récupération des données chiffrées présentes en HTML
	   var init = [];

	   for(var i = 1 ; i <= 12 ; i++) {
	        init[i-1] = parseInt($("table tr:nth-child("+i+") td:nth-child(2)").html().replace(/ /g,''));         		
	    }


	   // Définition des options et contenus du graphique
	   var options = Chart.defaults.global;
	   var data = {
		    labels: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre"],
		    datasets: [
		        {
		            label: "My First dataset",
		            fillColor: "rgba(220,220,220,0.2)",
		            strokeColor: "rgba(220,220,220,1)",
		            pointColor: "rgba(220,220,220,1)",
		            pointStrokeColor: "#fff",
		            pointHighlightFill: "#fff",
		            pointHighlightStroke: "rgba(220,220,220,1)",
		            data: [ // Ici j'aurais voulu boucler sur init mais ça ne fonctionne pas...
		            init[0], 
		            init[1], 
		            init[2], 
		            init[3], 
		            init[4], 
		            init[5], 
		            init[6], 
		            init[7], 
		            init[8], 
		            init[9], 
		            init[10], 
		            init[11], 
		            init[12] 
		            ] 
		        }
		    ]
		}  

		// Mise en place du graphique
	   	var ctx = $("#myChart").get(0).getContext("2d");
		var myNewChart = new Chart(ctx);
		new Chart(ctx).Line(data, options);

	};

   });

})(jQuery);
