
/* * * * * * * Script utilisé pour les graphiques temporels et temporels multi-axes * * * * * * */



/* * * * *   Fonction permettant l'ajout d'un flag   * * * * */
/* 
 * containerFlag : élément HTML (<div>) dans lequel on va ajouter le formulaire d'ajout d'un flag.
 * getLastDataList : booléen permettant de savoir si l'on souhaite initialiser le nouveau flag avec la requête (i.e. dataList) de la série précédente.
*/

function addFlag(containerFlag, getLastDataList)
{
	/* Initialisation des variables de débogage */
	var styleText = 'color: #43A832;';
	var styleVar = 'color: #8085E8; font-style: italic;';
	
	/* Ajout du formulaire du flag à l'axe Y */
	var idYAxis = parseInt(containerFlag.attr('id').match(/\d+/)[0], 10); // On récupère le numéro d'axe Y
	var idFlag = containerFlag.children().length + 1; // On définie le numéro de la série en fonction des séries/flags déjà présents sur l'axe Y
	var flags = $(
		$('#prototype_flag')
			.attr('data-prototype')
				.replace(/__nameyaxis__/g, idYAxis)
				.replace(/__name__/g, idFlag));
	containerFlag.prepend(flags);
	if (debug) { console.log('%c----- Ajout du flag %c' + idFlag +'%c à l\'axe %c' + idYAxis, styleText, styleVar, styleText, styleVar); }
	
	/* Récupération des champs du formulaire du flag */
	var titleFlag = $(containerFlag).find('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_titleFlag');
	var onSeries = $(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_onseries');
	var dataList = $(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_dataList');
	var parameterDataList = $(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_parameterDataList');
	var shapeFlag = $(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_shapeflag');
	var colorFlag = $(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_colorflag');
	var colorPicker = $(containerFlag).find('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_colorPickerFlag');
	var styleFlag = $(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_styleflag');
	var widthFlag = $(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_widthflag');
	var invertedChart = $('input#mc_chartbundle_chart_invertedChart');
	$(containerFlag).find('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_yaxisOrder').val(idFlag);
	
	/* Gestion des couleurs du flag */
	color_Flag(idFlag, idYAxis);
	//style_Flag(styleFlag, idFlag);

	jQuery.fn.reverse = [].reverse;
	
	/* Ajout de toutes les séries déjà existante dans les options onSeries du nouveau flag */
	containerFlag.children().reverse().each(function (index) {
		index = index+1;
    	if (this.className == 'serie') {
			onSeries.append(new Option('Série ' + index, index, false, false));
    	}
	});
   
	/* Ajout du flag au graphique HighStock */
    var chart = $('#container').highcharts();
	chart.addSeries({
		id: idYAxis + '_' + idFlag,
		yAxis: 'yAxis' + idYAxis,
        type: 'flags',
        color: $(colorFlag).val(),
        fillColor: $(colorFlag).val(),
        data:[]
	});

	/* Ajout de listeners sur les champs du formulaire du flag */
	$(dataList).change(function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		idFlag = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		
		addAjaxQueryOnDataList(dataList, idFlag, (idYAxis+2), true); // +2 car les graphiques HighStock dispose déjà de 2 axes Y par défaut
		
		if (debug) { console.log('%cModification de la requête du flag %c' + idFlag + '%c de l\'axe %c' + idYAxis + ' %c(' + chart.yAxis[idYAxis+1].series[idFlag-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});

	$(parameterDataList).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		idFlag = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		
		setSerie(idFlag, $(dataList).val(), $(this).val(), idYAxis+2, true);
		sortSeries(chart.yAxis[idYAxis+1].series);
		updateTitleWithParameter($(this));
		
		if (debug) { console.log('%cModification du paramètre du flag %c' + idFlag + '%c de l\'axe %c' + idYAxis + ' %c(' + chart.yAxis[idYAxis+1].series[idFlag-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});

	$(onSeries).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		idFlag = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		idSerieOn = parseInt($(this).val(), 10);
		
		if (idSerieOn > 0) { // On met le flag sur une série
			chart.yAxis[idYAxis+1].series[idFlag-1].update({
				onSeries: idYAxis + '_' + idSerieOn,
			});
		} else { // On met le flag sur l'axe X
			chart.yAxis[idYAxis+1].series[idFlag-1].update({
				onSeries: undefined,
			});
		}
		sortSeries(chart.yAxis[idYAxis+1].series);
		
		if (debug) { console.log('%cModification du paramètre du flag %c' + idFlag + '%c de l\'axe %c' + idYAxis + ' %c(' + chart.yAxis[idYAxis+1].series[idFlag-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text() + '%c (identifiant %c' + idYAxis + '_' + idSerieOn + '%c sur le graphique)', styleText, styleVar, styleText, styleVar, 'color: #000000;', styleText, styleVar, styleText, styleVar, styleText); }
	});

    $(colorFlag).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		idFlag = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		
		chart.yAxis[idYAxis+1].series[idFlag-1].update({
			color: $(this).val(),
			fillColor: $(this).val()
		});
		sortSeries(chart.yAxis[idYAxis+1].series);
		
		if (debug) { console.log('%cModification de la couleur du flag %c' + idFlag + '%c de l\'axe %c' + idYAxis + ' %c(' + chart.yAxis[idYAxis+1].series[idFlag-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});
	
	$(colorPicker).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		idFlag = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		
		chart.yAxis[idYAxis+1].series[idFlag-1].update({
			color: $(this).val(),
			fillColor: $(this).val()
		});
		sortSeries(chart.yAxis[idYAxis+1].series);
		
		if (debug) { console.log('%cModification de la couleur du flag %c' + idFlag + '%c de l\'axe %c' + idYAxis + ' %c(' + chart.yAxis[idYAxis+1].series[idFlag-1].userOptions.id + ')%c pour %c' + $(this).val(), styleText, styleVar, styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});
	
	$(styleFlag).on('change', function() {
	 	idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		idFlag = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		
	 	chart.yAxis[idYAxis+1].series[idFlag-1].update({
	 		style: {
				fontSize: '12px',
				fontWeight: $(this).val()
			}
	 	});
	 	sortSeries(chart.yAxis[idYAxis+1].series);
	 	
	 	if (debug) { console.log('%cModification du style de police du flag %c' + idFlag + '%c de l\'axe %c' + idYAxis + ' %c(' + chart.yAxis[idYAxis+1].series[idFlag-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	 });

	$(shapeFlag).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		idFlag = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		
		chart.yAxis[idYAxis+1].series[idFlag-1].update({
			shape: $(this).val()
		});
		sortSeries(chart.yAxis[idYAxis+1].series);
		
		if (debug) { console.log('%cModification de la forme du flag %c' + idFlag + '%c de l\'axe %c' + idYAxis + ' %c(' + chart.yAxis[idYAxis+1].series[idFlag-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});

	$(titleFlag).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		idFlag = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		
		chart.yAxis[idYAxis+1].series[idFlag-1].update({
			name: $(this).val()
		});
		sortSeries(chart.yAxis[idYAxis+1].series);
		
		if (debug) { console.log('%cModification du titre du flag %c' + idFlag + '%c de l\'axe %c' + idYAxis + ' %c(' + chart.yAxis[idYAxis+1].series[idFlag-1].userOptions.id + ')%c pour %c' + $(this).val(), styleText, styleVar, styleText, styleVar, 'color: #000000;', styleText, styleVar); }
	});

	$(widthFlag).on('change', function() {
		idYAxis = parseInt($(this).attr('id').match(/\d+/g)[0], 10);
		idFlag = parseInt($(this).attr('id').match(/\d+/g)[1], 10);
		
		chart.yAxis[idYAxis+1].series[idFlag-1].update({
			lineWidth: $(this).val()
		});
		sortSeries(chart.yAxis[idYAxis+1].series);
		
		if (debug) { console.log('%cModification de la largeur de la ligne du flag %c' + idFlag + '%c de l\'axe %c' + idYAxis + ' %c(' + chart.yAxis[idYAxis+1].series[idFlag-1].userOptions.id + ')%c pour %c' + $('option:selected', this).text(), styleText, styleVar, styleText, styleVar, styleText, styleVar); }
	});

	/* Si 'getLastDataList' == true, on initialise le nouveau flag avec la même requête que le flag précédent mais avec le paramètre suivant */
	if (getLastDataList)
	{
		var idDataList = parseInt($(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + (idFlag-1) + '_dataList').val(), 10);
		var idPrevParam = parseInt($(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + (idFlag-1) + '_parameterDataList').val(), 10);
		var idNewParam = parseInt($('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + (idFlag-1) + '_parameterDataList option[value="' + (idPrevParam+1) + '"]').val(), 10);
		
		if (!Number.isNaN(idDataList) && !Number.isNaN(idNewParam)) // Attention, idDataList peut être égal à 0 donc "if (idPrevDatalist && idPrevParam)" n'est pas correct dans cette situation
		{
			if (debug) { console.log('%cInitilisation du flag %c' + idFlag + '%c avec la requête %c' + $('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + (idFlag-1) + '_dataList option[value="' + idDataList + '"]').text() + '%c et le paramètre %c' + $('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + (idFlag-1) + '_parameterDataList option[value="' + idNewParam + '"]').text(), styleText, styleVar, styleText, styleVar, styleText, styleVar); }
			
			$(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_dataList').val(idDataList).change();
			$(containerFlag).find('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_parameterDataList').val(idNewParam).change();
		}
		else
		{
			if (debug) { console.log('%cInitialisation du flag impossible avec la requête précédente.', styleText); }
		}
	}
}


/* * * * *   Fonction permettant l'ajout de tous les flags d'une requête   * * * * */

function addAllFlag(dataGlob, idDataList, containerFlag)
{
	/* Initialisation des variables de débogage */
	var styleText = 'color: #900880;';
	var styleVar = 'color: #8085E8; font-style: italic;';
	
	var idYAxis = parseInt(containerFlag.attr('id').match(/\d+/)[0], 10);
	var idFlag = containerFlag.children().length + 1; // Identifiant du premier flag que l'on ajoute (on prend en compte les séries/flags déjà existant sur l'axe)
	
	if (debug) { console.log('%c----- Ajout de tous les flags de la requête n°%c' + idDataList +'%c à l\'axe %c' + idYAxis + '%c en commençant au flag %c' + idFlag, styleText, styleVar, styleText, styleVar, styleText, styleVar); }
	
	/* Pour chaque parametre compris dans la requête (dataGlob[idDataList]), on appel la function addOneFlag */
	var functionCallBack = function() {
		var firstFlag = true;
		var count = new Array();

		for (var parameter in dataGlob[idDataList]) {
			count.push(parameter);
		}
		
		for (var j = 0; j < count.length; j++)
		{
    		setTimeout(function(numFlag) {
				/* Si il s'agit du premier flag, on lui définit la requête, les suivants sont mise à jour dans la fonction addFlag */
    			if (firstFlag) {
					if (debug) { console.log('%cAjout du flag %c' + numFlag, styleText, styleVar); }
					addFlag(containerFlag, false);
					$('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_dataList').val(idDataList).change();
					firstFlag = false; 
				} else {
					if (debug) { console.log('%cAjout du flag %c' + numFlag, styleText, styleVar); }
					addFlag(containerFlag, true);
				}
    		}, 100 * j, idFlag+j); // On met un délai de 0.1s entre chaque ajout de flag
		}
	}

	getData(idDataList, null, true, functionCallBack);
}


/* * * * *   Fonction permettant la suppression d'un flag   * * * * */

function removeFlag(btn)
{
	/* Initialisation des variables de débogage */
	var styleText = 'color: #43A832;';
	var styleVar = 'color: #8085E8; font-style: italic;';
	
	var yaxisId = parseInt($(btn).parents('div.flag').find('input').first().attr('id').match(/\d+/g)[0], 10);
	var flagId = parseInt($(btn).parents('div.flag').find('input').first().attr('id').match(/\d+/g)[1], 10);
	var chart = $('#container').highcharts();
	if (debug) { console.log('%c----- Suppression du flag %c' + flagId + '%c de l\'axe-Y %c' + yaxisId, styleText, styleVar, styleText, styleVar); }

	/* Suppression du flag dans le graphique et mis à jour des identifiants des autres séries/flags */
	chart.yAxis[yaxisId+1].series[flagId-1].remove();
	for (let j=0 ; j<chart.yAxis[yaxisId+1].series.length ; j++)
	{
		// TODO : Repositionner correctement les flags qui ont un attribut onSeries
		
		if (debug) { var prevId = chart.yAxis[yaxisId+1].series[j].userOptions.id; }
		chart.yAxis[yaxisId+1].series[j].update({
			id: yaxisId + '_' + (j+1)
		});
		if (debug) { console.log('%cAncienne et nouvelle propriété "id" sur le graphique pour la série/flag %c' + (j+1) + '%c : %c' + prevId + '%c -> %c' + chart.yAxis[yaxisId+1].series[j].userOptions.id, styleText, styleVar, styleText, styleVar, styleText, styleVar); }
	}
	$(btn).parents('.flag').remove();

	/* Pour chaque série/flag, on met à jour tous les numéros de série/flag dans les propriétés des composants HTML */
	$.each($('#container_series_' + yaxisId).children('div').get().reverse(), function(index, value) // reverse car les séries sont affichées par ordre décroissant (plus grande en 1er)
	{
		var isSerie = true;
		if ($(value).attr('class') == 'flag') { isSerie = false; }
		
		if (isSerie) {
			$(value).find('label').first().html('Série n° ' + (index + 1));
		} else {
			$(value).find('label').first().html('Flag (Série) n° ' + (index + 1));
		}
			
		/* On redéfinit les numéros des séries/flags pour les propriétés "id" et "name" des composants HTML de type "input" */
		var inputs = $(value).find('input');
		for (var i=0 ; i<inputs.length ; i++) {
			if (isSerie) {
				$(inputs[i]).attr('id', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_\d+_series_\d+_/, 'mc_chartbundle_chart_list_yAxis_' + yaxisId + '_series_' + (index + 1) + '_'); });
				$(inputs[i]).attr('name', function( i, val ) { return val.replace(/mc_chartbundle_chart\[list_yAxis\]\[\d+\]\[series\]\[\d+\]/, 'mc_chartbundle_chart[list_yAxis][' + yaxisId + '][series][' + (index + 1) + ']'); });
			} else {
				$(inputs[i]).attr('id', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_\d+_flag_\d+_/, 'mc_chartbundle_chart_list_yAxis_' + yaxisId + '_flag_' + (index + 1) + '_'); });
				$(inputs[i]).attr('name', function( i, val ) { return val.replace(/mc_chartbundle_chart\[list_yAxis\]\[\d+\]\[flag\]\[\d+\]/, 'mc_chartbundle_chart[list_yAxis][' + yaxisId + '][flag][' + (index + 1) + ']'); });
			}
		}
		
		/* On redéfinit les numéros des séries/flags pour la propriété "for" des composants HTML de type "label" */
		var labels = $(value).find('label');
		for (var i=0 ; i<labels.length ; i++) {
			if ($(labels[i]).attr('for')) {
				if (isSerie) {
					$(labels[i]).attr('for', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_\d+_series_\d+_/, 'mc_chartbundle_chart_list_yAxis_' + yaxisId + '_series_' + (index + 1) + '_'); });
				} else {
					$(labels[i]).attr('for', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_\d+_flag_\d+_/, 'mc_chartbundle_chart_list_yAxis_' + yaxisId + '_flag_' + (index + 1) + '_'); });
				}
			}
		}
		
		/* On redéfinit les numéros des séries/flags pour les propriétés "id" et "name" des composants HTML de type "select" */
		var selects = $(value).find('select');
		for (var i=0 ; i<selects.length ; i++) {
			if (isSerie) {
				$(selects[i]).attr('id', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_\d+_series_\d+_/, 'mc_chartbundle_chart_list_yAxis_' + yaxisId + '_series_' + (index + 1) + '_'); });
				$(selects[i]).attr('name', function( i, val ) { return val.replace(/mc_chartbundle_chart\[list_yAxis\]\[\d+\]\[series\]\[\d+\]/, 'mc_chartbundle_chart[list_yAxis][' + yaxisId + '][series][' + (index + 1) + ']'); });
			} else {
				$(selects[i]).attr('id', function( i, val ) { return val.replace(/mc_chartbundle_chart_list_yAxis_\d+_flag_\d+_/, 'mc_chartbundle_chart_list_yAxis_' + yaxisId + '_flag_' + (index + 1) + '_'); });
				$(selects[i]).attr('name', function( i, val ) { return val.replace(/mc_chartbundle_chart\[list_yAxis\]\[\d+\]\[flag\]\[\d+\]/, 'mc_chartbundle_chart[list_yAxis][' + yaxisId + '][flag][' + (index + 1) + ']'); });
			}
		}
		
		/* On redéfinit les valeurs des yaxisOrder */
		if (isSerie) {
			$(value).find('input#mc_chartbundle_chart_list_yAxis_' + yaxisId + '_series_' + (index + 1) + '_yaxisOrder').val((index + 1));
		} else {
			$(value).find('input#mc_chartbundle_chart_list_yAxis_' + yaxisId + '_flag_' + (index + 1) + '_yaxisOrder').val((index + 1));
		}
	});
}


/* * * * *   Fonction gérant les couleurs des flags (liste de couleurs prédéfinies + color picker)   * * * * */

function color_Flag(idFlag, idYAxis)
{
	// Initialisations
	var select = $('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_colorflag');
	var colorPicker = $('input#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_colorPickerFlag');
	
	var opt = select.find('option:eq(' + (idFlag -1) + ')').val();
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
		var optionExists = ($('select#mc_chartbundle_chart_list_yAxis_' + idYAxis + '_flag_' + idFlag + '_colorflag option[value="' + couleur + '"]').length > 0);

		// Si la couleur sélectionnée dans le color picker n'existe pas, on l'ajoute dans la liste des couleurs prédéfinies
        if(!optionExists) {
			var option = new Option(couleur, couleur);
			select.append($(option));
			select.find('option[value="' + couleur + '"]').css('background-color', couleur);
		}
		
		select.val(couleur);
	});
}


/* * * * *   Fonction permettant la modification de la police d'écriture   * * * * */

/*function style_Flag(styleFlag, i)
{
	var select = styleFlag.find('option:eq(' + (i -1) + ')').val();
	styleFlag.val(select).css('fontFamily', select);

	$.each($(styleFlag).children('option'), function() {
		$(this).css('fontFamily', $(this).val());
	});

	$(styleFlag).change(function() {
		$(this).css('fontFamily', $(this).val());
	});
}*/


/* * * * *   Fonction permettant de trier les séries d'un axe Y en fonction de leurs identifiants   * * * * */
/*
 * Cette fonction est nécessaire car il y a un comportement anormal avec HighCharts :
 * Lorsqu'on a un graphique avec plusieurs axes Y, quand on update une série d'un axe Y autre que le 1er,
 * la série modifiée se retrouve en 1ere position dans le tableau des séries de l'axe, donc l'ordre des séries est totalement modifié.
 */
function sortSeries(tabSeries)
{
	tabSeries.sort(function compare(a, b)
	{
		var id1 = parseInt(a.userOptions.id.match(/\d+/g)[1], 10);
		var id2 = parseInt(b.userOptions.id.match(/\d+/g)[1], 10);
		
		if (id1 < id2)
			return -1;
		if (id1 > id2)
			return 1;
		return 0;
	});
}


/* * * * *   Fonction permettant la mise à jour automatique des champs titleSerie et unitSerie en fonction du parametre   * * * * */

function updateTitleWithParameter(parameter)
{
	var strParam = $('#' + $(parameter).attr('id') + ' option[value="' + $(parameter).val() + '"]').text();
	var arrayParam = strParam.split('@');
	var title = arrayParam[0];

	if (arrayParam.length > 1) {
		unit = arrayParam[1].slice(0, arrayParam[1].length);
	}

	$(parameter).parents('.form-group').prev('.form-group').find('input[id$=_titleFlag]').val(title).change();

}
