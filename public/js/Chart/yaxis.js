
/* * * * * * * Script utilisé pour les graphiques simples, temporels, dynamiques, dynamiques temporels, heatmap, circulaires et polaires * * * * * * */



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
	if (debug) { console.log('%c----- Ajout de l\'axe-Y %c' + idYAxis, styleText, styleVar); }
	
	/* Si on a un graphique qui peut avoir plusieurs axes Y (i.e. simple ou temporel), on ajoute un onglet */
	var multiAxesY = false;
	if ($('#tabs_yAxis').length) { multiAxesY = true; }
	if (multiAxesY) {
		$('#tabs_yAxis').append('<li id="ajout_yAxis_' + idYAxis + '"><a href="#tab-panel-' + idYAxis + '">Axe-Y n°' + idYAxis + '</a></li>');
	}
	
	/* Récupération des champs du formulaire de l'axe Y */
	var titleYAxis = $('#container_yAxis').find('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_titleYAxis');
	var typeYAxis = $('#container_yAxis').find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_typeYAxis');
	var top = $('#container_yAxis').find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_top');
	var height = $('#container_yAxis').find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_height');
	var opposite = $('#container_yAxis').find('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_opposite');
	$('#container_yAxis').find('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_orderY').val(idYAxis);

	/* Ajout de l'axe Y au graphique Highcharts */
	var chart = $('#container').highcharts();

	chart.addAxis({
		id: 'yAxis' + idYAxis,
		title: { text: undefined },
		type: $(typeYAxis).val(),
		//offset: 0
	});

	/* Ajout de listeners sur les champs du formulaire de l'axe Y */
	$(titleYAxis).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10); // Les identifiant des axes peuvent changer (à cause des suppressions) donc on est obligé de les récupérer à chaque appel du listener
		if(chart.userOptions.isStock) { idYAxis = idYAxis + 2; } // On différencie les graphiques HighCharts et HighStock car les HighStock contiennent 2 axes Y de base et les HighCharts 0
		chart.yAxis[idYAxis-1].setTitle({ text: $(this).val() });
		if (debug) { if(chart.userOptions.isStock) { idYAxis=idYAxis-2; } console.log('%cModification du titre de l\'axe-Y %c' + idYAxis + '%c pour %c' + $(this).val(), styleText, styleVar, styleText, styleVar); }
	});

	$(typeYAxis).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		if(chart.userOptions.isStock) { idYAxis = idYAxis + 2; }
		var yAxis = $('#container').highcharts().yAxis[idYAxis-1];
		updateTypeAxis(typeYAxis, yAxis);
		if (debug) { if(chart.userOptions.isStock) { idYAxis=idYAxis-2; } console.log('%cModification du type de l\'axe-Y %c' + idYAxis + '%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar); }
	});

	$(top).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		if(chart.userOptions.isStock) { idYAxis = idYAxis + 2; }
		chart.yAxis[idYAxis-1].update({
			top: $(this).val()
		});
		if (debug) { if(chart.userOptions.isStock) { idYAxis=idYAxis-2; } console.log('%cModification du top de l\'axe-Y %c' + idYAxis + '%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar); }
	});

	$(height).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		if(chart.userOptions.isStock) { idYAxis = idYAxis + 2; }
		chart.yAxis[idYAxis-1].update({
			height: $(this).val()
		});
		if (debug) { if(chart.userOptions.isStock) { idYAxis=idYAxis-2; } console.log('%cModification de la taille de l\'axe-Y %c' + idYAxis + '%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar); }
	});

	$(opposite).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		if(chart.userOptions.isStock) { idYAxis = idYAxis + 2; }
		if ($(this).is(':checked')) {
			chart.yAxis[idYAxis-1].update({
				opposite: true,
				labels: { align: 'left' } // Ajout de cette propriété pour aligner correctement les labels de l'axe Y
			});
		} else {
			chart.yAxis[idYAxis-1].update({
				opposite: false,
				labels: { align: 'right' }
			});
		}
		if (debug) { if(chart.userOptions.isStock) { idYAxis=idYAxis-2; } console.log('%cModification de l\'inversion de l\'axe-Y %c' + idYAxis + '%c pour %c' + $(this).is(':checked'), styleText, styleVar, styleText, styleVar); }
	});


	/* Mise à jour de l'onglet actif sur le dernier axe Y, si on a un graphique qui peut avoir plusieurs axes Y */
	if (multiAxesY)
	{
		$.each($('#container_yAxis').children().get(), function(index, value) { 
			$(value).removeClass('active in'); 
		});
		$.each($('#tabs_yAxis').children().get(), function(index, value) { 
			$(value).removeClass('active'); 
		});
		$('li#ajout_yAxis_' + $('.list_yAxis').length).addClass('active');
		$('div#tab-panel-' + $('.list_yAxis').length).addClass('active in');
	}

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
/* Cette fonction ne sert que pour les graphiques qui peuvent avoir plusieurs axes Y (i.e. simple ou temporel) */

function removeYaxis(btn)
{
	/* Initialisation des variables de débogage */
	var styleText = 'color: #FF5733;';
	var styleVar = 'color: #8085E8; font-style: italic;';
	
	var chart = $('#container').highcharts();
	var yaxisId = parseInt($(btn).parents('.list_yAxis').find('label').first().html().match(/\d+/)[0], 10);
	if (debug) { console.log('%c----- Suppression de l\'axe-Y %c' + yaxisId, styleText, styleVar); }
	

		/* * * * * MISE A JOUR DU GRAPHIQUE HIGHCHARTS * * * * */

	/* On différencie les graphiques HighCharts et HighStock car les HighStock contiennent 2 axes Y de base et les HighCharts 0 */
	if(chart.userOptions.isStock)
	{
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
	}
	else
	{
		/* Suppression de l'axe Y */
		chart.yAxis[yaxisId-1].remove();
		
		/* Mise à jour des identifiants des autres axes Y et des leurs séries/flags */
		for (let i=0 ; i<chart.yAxis.length ; i++) { 
			chart.yAxis[i].update({
				id: 'yAxis' + (i+1),
			});
			
			for (let j=0 ; j<chart.yAxis[i].series.length ; j++)
			{
				// TODO : Repositionner correctement les flags qui ont un attribut onSeries : if (chart.yAxis[i].series[j].type == 'flags' && chart.yAxis[i].series[j].onSeries != undefined)
				
				chart.yAxis[i].series[j].update({
					yAxis: 'yAxis' + (i+1),
					id: (i+1) + '_' + (j+1)
				});
			}
		}
	}

		/* * * * *  MISE A JOUR DES FORMULAIRES * * * * */

	// Suppression du formulaire de l'axe Y
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

	var newType = getTypeAxis($(typeAxis).val());
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
