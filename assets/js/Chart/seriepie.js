
/* * * * * * * Script utilisé pour les graphiques circulaires * * * * * * */



/* * * * *   Fonction permettant l'ajout d'une série   * * * * */
/* 
 * containerSerie : élément HTML (<div>) dans lequel on va ajouter le formulaire d'ajout d'une série.
 * getLastDataList : booléen permettant de savoir si l'on souhaite initialiser la nouvelle série avec la requête (i.e. dataList) de la série précédente.
*/

function addOneSerie(containerSerie, getLastDataList)
{
	/* Initialisation des variables de débogage */
	var styleText = 'color: #43A832;';
	var styleVar = 'color: #8085E8; font-style: italic;';
	
	var idSerie = containerSerie.children().length + 1;	// On définie le numéro de la série en fonction des séries déjà présents sur l'axe Y
	var series = $(
		$('#prototype_series')
			.attr('data-prototype')
				.replace(/__nameyaxis__/g, 1)
				.replace(/__name__/g, idSerie));
	containerSerie.prepend(series);
	if (debug) { console.log('%c----- Ajout de la série %c' + idSerie, styleText, styleVar); }
	
	/* Récupération des champs du formulaire de la série */
	var titleSerie = $(containerSerie).find('input#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_titleSerie');
	var unitSerie = $(containerSerie).find('input#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_unitSerie');
	var dataList = $(containerSerie).find('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_dataList');
	var parameterDataList = $(containerSerie).find('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_parameterDataList');
	var colorSerie = $(containerSerie).find('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_colorSerie');
	var colorPicker = $(containerSerie).find('input#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_colorPicker');
	var size = $(containerSerie).find('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_size');
	var innerSize = $(containerSerie).find('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_innersize');
	var invertedChart = $('input#mc_chartbundle_chart_invertedChart');
	$(containerSerie).find('input#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_yaxisOrder').val(idSerie);
	
	/* Gestion des couleurs de la série */
	colorSeries(idSerie);
	var defaultColor = $(colorSerie).val();
	var mixedColors = getColorsMixed(defaultColor);

	/* Ajout de la série au graphique Highcharts */
	var chart = $('#container').highcharts();
	chart.addSeries({
		id: '1_' + idSerie, // Les graphiques circulaires n'ont qu'un seul axe Y
		color: defaultColor,
		colors: mixedColors,
		type: 'pie', 
		data: []
	});

	/* 
	 * Ajout de listeners sur les champs du formulaire de la série.
	 * Le type "pie" fait que les séries sont ajoutées à l'objet "chart.series" et non à l'objet "chart.yAxis.series" comme pour les autres graphiques. 
	 * C'est aussi pour cette raison qu'il n'y a pas d'attribut "yAxis" lors de l'ajout d'une série de type "pie" au graphique Highcharts.
	*/
	$(dataList).on('change', function() {
		idSerie = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		addAjaxQueryOnDataList(dataList, idSerie, 1, false);
		if (debug) { console.log('%cModification de la requête de la série %c' + idSerie + ' %c(' + chart.series[idSerie-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});
	
	$(parameterDataList).on('change', function() {
		idSerie = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		setSerie(idSerie, $(dataList).val(), $(this).val(), 1, false);
		updateTitleAndUnitWithParameter($(this));
		if (debug) { console.log('%cModification du paramètre de la série %c' + idSerie + ' %c(' + chart.series[idSerie-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});
	
	$(titleSerie).on('change', function() {
		idSerie = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		chart.series[idSerie-1].update({
			name: produceTitleWithUnit($(this).val(), $(unitSerie).val())
		});
		if (debug) { console.log('%cModification du titre de la série %c' + idSerie + ' %c(' + chart.series[idSerie-1].userOptions.id + ')%c pour %c' + $(this).val(), styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});
	
	$(unitSerie).on('change', function() {
		idSerie = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		chart.series[idSerie-1].update({
			name: produceTitleWithUnit($(titleSerie).val(), $(this).val())
		});
		if (debug) { console.log('%cModification de l\'unité de la série %c' + idSerie + ' %c(' + chart.series[idSerie-1].userOptions.id + ')%c pour %c' + $(this).val(), styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});
	
	$(colorSerie).on('change', function() {
		idSerie = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		chart.series[idSerie-1].update({
			color: $(this).val(),
			colors: getColorsMixed($(this).val())
		});
		if (debug) { console.log('%cModification de la couleur de la série %c' + idSerie + ' %c(' + chart.series[idSerie-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});
	
	$(colorPicker).on('change', function() {
		idSerie = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		chart.series[idSerie-1].update({
			color: $(this).val(),
			colors: getColorsMixed($(this).val())
		});
		if (debug) { console.log('%cModification de la couleur de la série %c' + idSerie + ' %c(' + chart.series[idSerie-1].userOptions.id + ')%c pour %c' + $(this).val(), styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});

	$(size).on('change', function() {
		idSerie = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		chart.series[idSerie-1].update({
			size: $(this).val()
		});
		if (debug) { console.log('%cModification de la taille de la série %c' + idSerie + ' %c(' + chart.series[idSerie-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});	

	$(innerSize).on('change', function() {
		idSerie = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		chart.series[idSerie-1].update({
			innerSize: $(this).val()
		});
		if (debug) { console.log('%cModification de la taille intérieure de la série %c' + idSerie + ' %c(' + chart.series[idSerie-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});

	/* Si 'getLastDataList' == true, on initialise la nouvelle série avec la même requête que la série précédente mais avec le paramètre suivant */
	if (getLastDataList)
	{
		var idDataList = parseInt($(containerSerie).find('select#mc_chartbundle_chart_list_yAxis_1_series_' + (idSerie-1) + '_dataList').val(), 10);
		var idPrevParam = parseInt($(containerSerie).find('select#mc_chartbundle_chart_list_yAxis_1_series_' + (idSerie-1) + '_parameterDataList').val(), 10);
		var idNewParam = parseInt($('select#mc_chartbundle_chart_list_yAxis_1_series_' + (idSerie-1) + '_parameterDataList option[value="' + (idPrevParam+1) + '"]').val(), 10);

		if (!Number.isNaN(idDataList) && !Number.isNaN(idNewParam)) // Attention, idDatalist ou idNewParam peut être égal à 0 donc "if (idPrevDatalist && idPrevParam)" n'est pas correct dans cette situation
		{
			if (debug) { console.log('%cInitilisation de la série %c' + idSerie + '%c avec la requête %c' + $('select#mc_chartbundle_chart_list_yAxis_1_series_' + (idSerie-1) + '_dataList option[value="' + idDataList + '"]').text() + '%c et le paramètre %c' + $('select#mc_chartbundle_chart_list_yAxis_1_series_' + (idSerie-1) + '_parameterDataList option[value="' + idNewParam + '"]').text(), styleText, styleVar, styleText, styleVar, styleText, styleVar); }
			
			$(containerSerie).find('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_dataList').val(idDataList).change();
			$(containerSerie).find('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_parameterDataList').val(idNewParam).change();
		}
		else
		{
			if (debug) { console.log('%cInitialisation de la série impossible avec la requête précédente.', styleText); }
		}
	}
}


/* * * * *   Fonction permettant l'ajout de toutes les séries d'une requête   * * * * */

function addAllSerie(dataGlob, idDataList, containerSerie)
{
	/* Initialisation des variables de débogage */
	var styleText = 'color: #900880;';
	var styleVar = 'color: #8085E8; font-style: italic;';
	
	var idSerie = containerSerie.children().length + 1; // Identifiant de la première série que l'on ajoute (on prend en compte les séries/flags déjà existant sur l'axe)
	
	if (debug) { console.log('%c----- Ajout de toutes les séries de la requête n°%c' + idDataList +'%c en commençant à la série %c' + idSerie, styleText, styleVar, styleText, styleVar); }
	
	/* Pour chaque paramètre compris dans la requête (dataGlob[idDataList]), on appel la function addOneSerie */
	var functionCallBack = function() {
		var firstSerie = true;
		var count = new Array();

		for (var parameter in dataGlob[idDataList]) {
			count.push(parameter);
		}
		
		for (var j = 0; j < count.length; j++)
		{
    		setTimeout(function(numSerie) {
				/* Si il s'agit de la première série, on lui définit la requête, les suivantes sont mise à jour dans la fonction addOneSerie */
				if (firstSerie) { 
					if (debug) { console.log('%cAjout de la série %c' + numSerie, styleText, styleVar); }
					addOneSerie(containerSerie, false);
					$('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_dataList').val(idDataList).change();
					firstSerie = false; 
				} else {
					if (debug) { console.log('%cAjout de la série %c' + numSerie, styleText, styleVar); }
					addOneSerie(containerSerie, true);
				}
    		}, 100 * j, idSerie+j); // On met un délai de 0.1s entre chaque ajout de série
		}
	}

	getData(idDataList, null, true, functionCallBack);
}


/* * * * *   Fonction permettant la suppression d'une série   * * * * */

function removeSerie(btn)
{
	/* Initialisation des variables de débogage */
	var styleText = 'color: #43A832;';
	var styleVar = 'color: #8085E8; font-style: italic;';
	
	var serieId = parseInt($(btn).parents('div.serie').find('input').first().attr('id').match(/\d+/g)[1], 10);
	var chart = $('#container').highcharts();
	if (debug) { console.log('%c----- Suppression de la série %c' + serieId, styleText, styleVar); }

	/* Suppression de la série dans le graphique et mis à jour des identifiants des autres séries */
	chart.series[serieId-1].remove();
	for (let j=0 ; j<chart.series.length ; j++)
	{
		if (debug) { var prevId = chart.series[j].userOptions.id; }
		chart.series[j].update({
			id: '1_' + (j+1)
		});
		if (debug) { console.log('%cAncienne et nouvelle propriété "id" sur le graphique pour la série %c' + (j+1) + '%c : %c' + prevId + '%c -> %c' + chart.series[j].userOptions.id, styleText, styleVar, styleText, styleVar, styleText, styleVar); }
	}
	$(btn).parents('.serie').remove();

	/* Pour chaque série/flag, on met à jour tous les numéros de série/flag dans les propriétés des composants HTML */
	$.each($('#container_series_1').children('div').get().reverse(), function(index, value) // reverse car les séries sont affichées par ordre décroissant (plus grande en 1er)
	{
		$(value).find('label').first().html('Série n° ' + (index + 1));
			
		/* On redéfinit les numéros des séries pour les propriétés "id" et "name" des composants HTML de type "input" */
		var inputs = $(value).find('input');
		for (var i=0 ; i<inputs.length ; i++) {
			$(inputs[i]).attr('id', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_1_series_\d+_/, 'mc_chartbundle_chart_list_yAxis_1_series_' + (index + 1) + '_'); });
			$(inputs[i]).attr('name', function( i, val ) { return val.replace(/mc_chartbundle_chart\[list_yAxis\]\[1\]\[series\]\[\d+\]/, 'mc_chartbundle_chart[list_yAxis][1][series][' + (index + 1) + ']'); });
		}
		
		/* On redéfinit les numéros des séries pour la propriété "for" des composants HTML de type "label" */
		var labels = $(value).find('label');
		for (var i=0 ; i<labels.length ; i++) {
			if ($(labels[i]).attr('for')) {
				$(labels[i]).attr('for', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_1_series_\d+_/, 'mc_chartbundle_chart_list_yAxis_1_series_' + (index + 1) + '_'); });
			}
		}
		
		/* On redéfinit les numéros des séries/flags pour les propriétés "id" et "name" des composants HTML de type "select" */
		var selects = $(value).find('select');
		for (var i=0 ; i<selects.length ; i++) {
			$(selects[i]).attr('id', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_1_series_\d+_/, 'mc_chartbundle_chart_list_yAxis_1_series_' + (index + 1) + '_'); });
			$(selects[i]).attr('name', function( i, val ) { return val.replace(/mc_chartbundle_chart\[list_yAxis\]\[1\]\[series\]\[\d+\]/, 'mc_chartbundle_chart[list_yAxis][1][series][' + (index + 1) + ']'); });
		}
		
		/* On redéfinit les valeurs des yaxisOrder */
		$(value).find('input#mc_chartbundle_chart_list_yAxis_1_series_' + (index + 1) + '_yaxisOrder').val((index + 1));
	});
}


/* * * * *   Fonction gérant les couleurs des séries (liste de couleurs prédéfinies + color picker)   * * * * */

function colorSeries(idSerie)
{
	// Initialisations
	var select = $('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_colorSerie');
	var colorPicker = $('input#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_colorPicker');
	
	var opt = select.find('option:eq(' + (idSerie -1) + ')').val();
	select.val(opt).css('background-color', opt);
	colorPicker.val(opt);

	$.each(select.children('option'), function() {
		$(this).css('background-color', $(this).val());
	});

	// EventListener sur la liste des couleurs prédéfinies
	select.change(function() {
		$(this).css('background-color', $(this).val());
		colorPicker.val($(this).val());
	});
	
	// EventListener sur le colorPicker
	colorPicker.change(function() {
		var couleur = $(this).val().toUpperCase();
		select.css('background-color', couleur);
		var optionExists = ($('select#mc_chartbundle_chart_list_yAxis_1_series_' + idSerie + '_colorSerie option[value="' + couleur + '"]').length > 0);

		// Si la couleur sélectionnée dans le color picker n'existe pas, on l'ajoute dans la liste des couleurs prédéfinies
        if(!optionExists) {
			var option = new Option(couleur, couleur);
			select.append($(option));
			select.find('option[value="' + couleur + '"]').css('background-color', couleur);
		}
		
		select.val(couleur);
	});
}


/* * * * *   Fonction permettant la mise à jour automatique des champs titleSerie et unitSerie en fonction du parametre   * * * * */

function updateTitleAndUnitWithParameter(parameter)
{
	var strParam = $('#' + $(parameter).attr('id') + ' option[value="' + $(parameter).val() + '"]').text();
	var arrayParam = strParam.split('@');
	var title = arrayParam[0];
	var unit = '';

	if (arrayParam.length > 1) {
		unit = arrayParam[1].slice(0, arrayParam[1].length);
	}

	$(parameter).parents('.form-group').prev('.form-group').find('input[id$=_titleSerie]').val(title).change();
	$(parameter).parents('.form-group').prev('.form-group').find('input[id$=_unitSerie]').val(unit).change();
}
