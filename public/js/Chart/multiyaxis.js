
/* * * * * * * Script utilisé pour les graphiques temporels multi-axes * * * * * * */



/* * * * *   Fonction permettant l'ajout d'un axe-Y   * * * * */

function addyAxis()
{
	/* Initialisation des variables de débogage */
	var styleText = 'color: #FF5733;';
	var styleVar = 'color: #8085E8; font-style: italic;';
	
	/* Ajout du formulaire de l'axe Y au graphique */
	var idYAxis = $('#container_yAxis').children().length + 1; 
	var axis = $(
		$('#prototype_yAxis')
			.attr('data-prototype')
				.replace(/__nameyaxis__/g, idYAxis));
	$('#container_yAxis').append(axis);
	$('#tabs_yAxis').append('<li id="ajout_yAxis_' + idYAxis + '"><a href="#tab-panel-' + idYAxis + '">Axe-Y n°' + idYAxis + '</a></li>');
	if (debug) { console.log('%c----- Ajout de l\'axe-Y %c' + idYAxis, styleText, styleVar); }
	
	/* Récupération des champs du formulaire de l'axe Y */
	var titleYAxis = $('#container_yAxis').find('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_titleYAxis');
	var typeYAxis = $('#container_yAxis').find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_typeYAxis');
	var opposite = $('#container_yAxis').find('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_opposite');
	$('#container_yAxis').find('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_orderY').val(idYAxis);
	
	/* Ajout de l'axe Y au graphique Highcharts */
	var chart = $('#container').highcharts();
	var chartHeight = 320 * idYAxis;  // Valeur en pixels
	var yAxisHeight = 100 / idYAxis; // Valeur en pourcentages
	chart.setSize(chart.chartWidth, chartHeight);

	chart.addAxis({
		id: 'yAxis' + idYAxis,
		title: { text: undefined },
		type: $(typeYAxis).val(),
		offset: 0,
		lineWidth: 2
	});

	/* Mise à jour de la hauteur de tous les axes Y */
	for (var i = 2; i < chart.yAxis.length; i++) { // i commence à 2 car les graphiques HighStock dispose déjà de 2 axes Y par défaut
		chart.yAxis[i].update({
	    	height: yAxisHeight + '%'
		});
	}

	/* 	Mise à jour de la position par rapport au top de tous les axes Y */
	for (var i = 2; i < chart.yAxis.length; i++) {
		chart.yAxis[i].update({
	    	top: (0 + yAxisHeight * (i-2)) + '%'
		});
	}
	
	/* 
	 * Note: les paramètres 'top' et 'height' des axes Y d'un graphique multi-axes sont définis dynamiquement à la création du graphique.
	 * Cela signifie que les champs 'Top' et 'Taille' dans le formulaire d'un axe Y peuvent être vides et que ces valeurs peuvent être null en base de données.
	*/ 
	
	/* Ajout de listeners sur les champs du formulaire de l'axe Y */
	$(titleYAxis).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10); // Les identifiant des axes peuvent changer (à cause des suppressions) donc on est obligé de les récupérer à chaque appel du listener
		chart.yAxis[idYAxis+1].setTitle({ text: $(this).val() });
		if (debug) { console.log('%cModification du titre de l\'axe-Y %c' + idYAxis + '%c pour %c' + $(this).val(), styleText, styleVar, styleText, styleVar); }
	});
	
	$(typeYAxis).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		var yAxis = $('#container').highcharts().yAxis[idYAxis+1];
		updateTypeAxis(typeYAxis, yAxis);
		if (debug) { console.log('%cModification du type de l\'axe-Y %c' + idYAxis + '%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar); }
	});
	
	$(opposite).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		if ($(this).is(':checked')) {
			chart.yAxis[idYAxis+1].update({
				opposite: true,
				labels: { align: 'left' } // Ajout de cette propriété pour aligner correctement les labels de l'axe Y
			});
		} else {
			chart.yAxis[idYAxis+1].update({
				opposite: false,
				labels: { align: 'right' }
			});
		}
		if (debug) { console.log('%cModification de l\'inversion de l\'axe-Y %c' + idYAxis + '%c pour %c' + $(this).is(':checked'), styleText, styleVar, styleText, styleVar); }
	});

	/* Mise à jour de l'onglet actif sur le dernier axe Y */
	$.each($('#container_yAxis').children().get(), function(index, value) { 
		$(value).removeClass('active in'); 
	});
	$.each($('#tabs_yAxis').children().get(), function(index, value) { 
		$(value).removeClass('active in'); 
	});
	$('li#ajout_yAxis_' + $('.list_yAxis').length).addClass('active');
	$('div#tab-panel-' + $('.list_yAxis').length).addClass('active in');

	/* Ajout de listeners sur les boutons d'ajout des séries/flags */
	$('button#addOneSerie_' + idYAxis).on('click', function(evt) {
		var containerSerie = $(this).parents('div.addSeries').next();
		addOneSerie(containerSerie, true);
	});

	$('button#addFlag_' + idYAxis).on('click', function(evt) {
		var containerFlag = $(this).parents('div.addSeries').next();
		addFlag(containerFlag, true);
	});

	$('button#addAllSeries_' + idYAxis).on('click', function(evt) {
		var containerSerie = $(this).parents('div.addSeries').next();
		var idDataList = parseInt($(this).prev().val(), 10);
		addAllSerie(dataGlob, idDataList, containerSerie);
	});
	
	$('button#addAllFlag_' + idYAxis).on('click', function(evt) {
		var containerFlag = $(this).parents('div.addSeries').next();
		var idDataList = parseInt($(this).prev().prev().val(), 10);
		addAllFlag(dataGlob, idDataList, containerFlag);
	});
}


/* * * * *   Fonction permettant la supression d'un axe-Y   * * * * */

function removeYaxis(btn)
{
	/* Initialisation des variables de débogage */
	var styleText = 'color: #FF5733;';
	var styleVar = 'color: #8085E8; font-style: italic;';
	
	var chart = $('#container').highcharts();
	var yaxisId = parseInt($(btn).parents('.list_yAxis').find('label').first().html().match(/\d+/)[0], 10);
	if (debug) { console.log('%c----- Suppression de l\'axe-Y %c' + yaxisId, styleText, styleVar); }
	

		/* * * * * MISE A JOUR DU GRAPHIQUE HIGHCHARTS * * * * */
	
	/* Suppression de l'axe Y */
	chart.yAxis[yaxisId+1].remove(); // Les séries liées à l'axe sont aussi supprimées 
	
	/* Mise à jour des identifiants des autres axes Y et des leurs séries/flags */
	for (let i=2 ; i<chart.yAxis.length ; i++)
	{ 
		chart.yAxis[i].update({
			id: 'yAxis' + (i-1),
		});
		
		for (let j=0 ; j<chart.yAxis[i].series.length ; j++)
		{
			// TODO : Repositionner correctement les flags qui ont un attribut onSeries : if (chart.yAxis[i].series[j].type == 'flags' && chart.yAxis[i].series[j].onSeries != undefined)
			
			chart.yAxis[i].series[j].update({
				yAxis: 'yAxis' + (i-1),
				id: (i-1) + '_' + (j+1)
			});
		}
	}

	var chartHeight = 320 * (chart.yAxis.length-2);  // Valeur en pixels
	var yAxisHeight = 100 / (chart.yAxis.length-2); // Valeur en pourcentages
	chart.setSize(chart.chartWidth, chartHeight);

	/* Mise à jour de la hauteur de tous les axes Y */
	for (var i = 2; i < chart.yAxis.length; i++) { 
		chart.yAxis[i].update({
	    	height: yAxisHeight + '%'
		});
	}

	/* 	Mise à jour de la position par rapport au top de tous les axes Y */
	for (var i = 2; i < chart.yAxis.length; i++) {
		chart.yAxis[i].update({
	    	top: (0 + yAxisHeight * (i-2)) + '%'
		});
	}


		/* * * * *  MISE A JOUR DES FORMULAIRES * * * * */

	/* Suppression du formulaire de l'axe Y */
	$(btn).parents('#tab-panel-' + yaxisId).remove(); // Tous les formulaires des séries/flags sont aussi supprimés
	$('#ajout_yAxis_' + yaxisId).remove();

	/* Pour chaque formulaire d'axe Y, on met à jour tous les numéros d'axes Y dans les propriétés des composants HTML */
	$.each($('#container_yAxis').children().get(), function(index, value)
	{
		$(value).attr('id', 'tab-panel-' + (index + 1));
		$(value).children('.list_yAxis').children('div').children('label').first().html('Axe-Y n° ' + (index + 1));
		$(value).find('div[id^=container_series_]').attr('id', 'container_series_' + (index + 1));
		
		/* On redéfinit les numéros des axes Y pour les propriétés "id" et "name" des composants HTML de type "input" */
		var inputs = $(value).find('input');
		for (var i=0 ; i<inputs.length ; i++) {
			$(inputs[i]).attr('id', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_\d+_/, 'mc_chartbundle_chart_list_yAxis_' + (index + 1) + '_'); });
			$(inputs[i]).attr('name', function( i, val ) { return val.replace(/mc_chartbundle_chart\[list_yAxis\]\[\d+\]/, 'mc_chartbundle_chart[list_yAxis][' + (index + 1) + ']'); });
		}
		
		/* On redéfinit les numéros des axes Y pour la propriété "for" des composants HTML de type "label" */
		var labels = $(value).find('label');
		for (var i=0 ; i<labels.length ; i++) {
			if ($(labels[i]).attr('for')) {
				$(labels[i]).attr('for', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_\d+_/, 'mc_chartbundle_chart_list_yAxis_' + (index + 1) + '_'); });
			}
		}
		
		/* On redéfinit les numéros des axes Y pour les propriétés "id" et "name" des composants HTML de type "select" */
		var selects = $(value).find('select');
		for (var i=0 ; i<selects.length ; i++) {
			$(selects[i]).attr('id', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_\d+_/, 'mc_chartbundle_chart_list_yAxis_' + (index + 1) + '_'); });
			$(selects[i]).attr('name', function( i, val ) { return val.replace(/mc_chartbundle_chart\[list_yAxis\]\[\d+\]/, 'mc_chartbundle_chart[list_yAxis][' + (index + 1) + ']'); });
		}
		
		/* On redéfinit les numéros des axes Y pour les propriétés "id" des composants HTML de type "button" */
		$(value).find('button[id^=addAllSeries_]').attr('id', 'addAllSeries_' + (index + 1));
		$(value).find('button[id^=addAllFlag_]').attr('id', 'addAllFlag_' + (index + 1));
		$(value).find('button[id^=addOneSerie_]').attr('id', 'addOneSerie_' + (index + 1));
		$(value).find('button[id^=addFlag_]').attr('id', 'addFlag_' + (index + 1));
		
		/* On redéfinit les valeurs order */
		$(value).find('input#mc_chartbundle_chart_list_yAxis_' + (index + 1) + '_orderY').val((index + 1));
	});
	
	/* Pour chaque onglet du nav-tabs, on redéfinit l'id, le lien vers l'axe Y et le nom du lien vers l'axe Y */
	$.each($('#tabs_yAxis').children('li').get(), function(index, value) 
	{
		var name = $(value).attr("id");
		if(name != "ajout_yAxis_btn") {
			$(value).attr('id', 'ajout_yAxis_' + (index));
			$(value).children().attr('href', '#tab-panel-' + (index));
			$(value).children().html('Axe-Y n° ' + (index));
		}
	});
	
	/* Mise à jour de l'onglet actif */
	if($('li#ajout_yAxis_' + yaxisId).length) {
		$('li#ajout_yAxis_' + yaxisId).addClass('active');
		$('div#tab-panel-' + yaxisId).addClass('active in');
	} else if ($('li#ajout_yAxis_' + (yaxisId-1)).length) {
		$('li#ajout_yAxis_' + (yaxisId-1)).addClass('active');
		$('div#tab-panel-' + (yaxisId-1)).addClass('active in');
	}
}


/* * * * *   Vérification de valeur non négative ou null pour le type d'axe X Logarithmique   * * * * */

function getTypeAxis(typeAxis, newSerie)
{
	var chart = $('#container').highcharts();	

	if (typeAxis == 'logarithmic')
	{
		var noNegatValInSerie = checkNoNegatVal(chart.series);

		if (newSerie != null) {
			var noNegatValInNewSerie = !negatVal(newSerie, true);
		} else {
			var noNegatValInNewSerie = true;
		}
		
		if(noNegatValInSerie && noNegatValInNewSerie) {
			return typeAxis;
		} else {
			return firstTypeAxis;
		}		
	} else {
		return typeAxis;
	}
}


/* * * * *   Vérification de l'absence de valeur négative ou null dans les séries   * * * * */

function checkNoNegatVal(series)
{
	for (var i = 0; i<series.length; i++) {
		if (negatVal(series[i].data)) {
			return false;
		}
	}
	return true;
}


/* * * * *   Retourne true si il y a une valeur negative ou null dans la série   * * * * */

function negatVal(data, newSerie)
{
	if (typeof newSerie === 'undefined') { newSerie = null; }
	
	if (data.length == 0) { return false; }

	for (var i = 0; i < data.length; i++) {
		if (newSerie) {
			if (data[i][0] <= 0 || data[i][1] <= 0) { return true; }
		} else {
			if (data[i].x <= 0 || data[i].y  <= 0) { return true; }
		}
	}
	return false;
}


/* * * * *   Modifie le type d'axe X ou Y si il y a des valeurs negatives ou null à la selection du type Logarithmique   * * * * */

function updateTypeAxis(typeAxis, axis, serie)
{
	if (typeof serie === 'undefined') { typeChart = null; }

	var newType = getTypeAxis($(typeAxis).val(), serie);
	axis.update({ type: newType });

	if ($(typeAxis).val() != newType) {
		toastr.error(jsTranslate['formYAxis_logarithmicError_description'], jsTranslate['formYAxis_logarithmicError_name']);
		$(typeAxis).get(0).selectedIndex = 0;
	}
}


/* * * * *   Active/désactive les boutons d'ajout de toutes les séries/flags en fonction de la requête sélectionnée dans la liste déroulante   * * * * */

function updateSelectRequest(selectValue)
{
	var yAxisId = $(selectValue).attr('id').match(/\d+/);
	var requestId = selectValue.value;
	
	if(requestId == -1) {
		$('#addAllSeries_' + yAxisId).prop('disabled', true);
		$('#addAllFlag_' + yAxisId).prop('disabled', true);
	} else {
		$('#addAllSeries_' + yAxisId).prop('disabled', false);
		$('#addAllFlag_' + yAxisId).prop('disabled', false);
	}
}
