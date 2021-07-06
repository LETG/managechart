$(document).ready(function()
{
	/* Ajout d'un axe-Y */
	addyAxis();

	/* Modification du formulaire yAxis */
	var containerYAxis = $('div#container_yAxis');
	var listYaxis = $(containerYAxis).find('label').first().parent().parent();
	$(containerYAxis).find('label').first().remove();
	$(containerYAxis).find('div.addSeries').remove();
	listYaxis.prepend('<div class="form-group"><label class="col-sm-2 col-md-2 control-label" style="color: #8085E8">Axe-Y n°1</label></div>');

	/* Ajout du formulaire de série */
	var containerSerie = $('div#container_series_1');
	var series = $(
		$('#prototype_series')
			.attr('data-prototype')
				.replace(/__nameyaxis__/g, 1)
				.replace(/__name__/g, 1));
	containerSerie.prepend(series);

	/* Modification du formulaire de série */
	$(containerSerie).find('label').first().html('Série').css('color', '#39A832');
	$(containerSerie).find('button').first().remove();
	$(containerSerie).find('input[id$=_titleSerie]').parents('.form-group').remove();
	$(containerSerie).find('select[id$=_parameterDataList]').parent('div').prev('label').remove();
	$(containerSerie).find('select[id$=_parameterDataList]').parent('div').remove();
	var typeSerieLabel = $(containerSerie).find('select[id$=_typeSerie]').parent('div').prev('label').detach();
	var typeSerieField = $(containerSerie).find('select[id$=_typeSerie]').parent('div').detach();
	$(containerSerie).find('select[id$=_dataList]').parents('.form-group').append(typeSerieLabel).append(typeSerieField);
	$(containerSerie).find('select[id$=_colorSerie]').parents('.form-group').remove();

	var dataList = $(containerSerie).find('select[id$=_dataList]');
	var idDataList = $(dataList).val();

	var chart = $('#container').highcharts();

	var functionCallBack = function()
	{
		var idSerie = 1;
		for (var parameter in dataGlob[idDataList])
		{
			addOneSerie(idSerie, parameter, containerSerie);
			idSerie++;
		}
	}

	/* Chargement des séries si elles ont été définies */
	if (idDataList != '') {
		getData(idDataList, null, true, functionCallBack);
	}
	
	/* Ajout de listeners sur les champs "type", "marker" et "style" */
	var yAxisId = 0;
	if(chart.userOptions.isStock) { yAxisId=2; } // Highcharts = 0 axe Y par défaut, Highstock = 2 axes Y par défaut
	updateSeries(dataList, yAxisId);

	/* Ajout d'un listener sur le champ "dataList" */
	$(dataList).on('change', function()
	{
		if (debug) { console.log('%cModification de la requête pour %c' + $('option:selected', this).text(), 'color:  #FFC300', 'color: #8085E8'); }
		
		/* On différencie les graphiques dynamiques (Highcharts) des graphiques dynamiques temporels (Highstock) */
        if(chart.userOptions.isStock) {
			for (let i=chart.yAxis[2].series.length-1 ; i>=0 ; i--) { chart.yAxis[2].series[i].remove(); }
		} else {
			for (let i=chart.yAxis[0].series.length-1 ; i>=0 ; i--) { chart.yAxis[0].series[i].remove(); }
		}
		
		idDataList = $(this).val();

		var functionCallBack = function()
		{
			var idSerie = 1;
			for (var parameter in dataGlob[idDataList])
			{
				addOneSerie(idSerie, parameter, containerSerie);
				idSerie++;
			}
		}

		if (idDataList != '') {
 			getData(idDataList, null, true, functionCallBack);
		}
	});
});

/* Fonction permettant l'ajout d'une série */
function addOneSerie(idSerie, parameter, containerSerie)
{
	var dataList = $(containerSerie).find('select[id$=_dataList]');
	var typeSerie = $(containerSerie).find('select[id$=_typeSerie]');
	var markerSerie = $(containerSerie).find('input[id$=_markerSerie]');
	var dashStyleSerie = $(containerSerie).find('select[id$=_dashStyleSerie]');
	var orderSeries = $(containerSerie).find('input#mc_chartbundle_chart_list_yAxis_1_series_1_yaxisOrder').val(1);
	
	if (debug) { console.log('%cAjout de la serie %c' + idSerie + '%c avec le paramètre %c' + parameter, 'color:  #FFC300', 'color: #8085E8', 'color:  #FFC300', 'color: #8085E8'); }

	var chart = $('#container').highcharts();
	chart.addSeries({
		id: idSerie,
		yAxis: 'yAxis1',
		//color: firstColorSerie,
		type: $(typeSerie).val(),
		name: parameter,
		marker: { enabled: $(markerSerie).is(':checked') },
		dashStyle: getDashStyle($(dashStyleSerie).val()),
		data: []
	});
	
	if ($(dataList).val() != '')
	{
		if(chart.userOptions.isStock) {
			setSerie(idSerie.toString(), $(dataList).val(), (idSerie-1).toString(), 3);
		} else {
			setSerie(idSerie.toString(), $(dataList).val(), (idSerie-1).toString(), 1);
		}
	}
}

/* Fonction permettant la mise à jour du graphique au changement des champs du formulaire de série */
function updateSeries(dataList, yAxisId)
{
	var idDataList = $(dataList).val();
	var typeSerie = $(dataList).parents('.form-group').find('select[id$=_typeSerie]');
	var markerSerie = $(dataList).parents('.form-group').next().find('input[id$=_markerSerie]');
	var dashStyleSerie = $(dataList).parents('.form-group').next().find('select[id$=_dashStyleSerie]');

	$(typeSerie).on('change', function() {
		if (debug) { console.log('%cModification du type des séries pour : %c' + $(this).val(), 'color: #FFC300', 'color: #8085E8'); }
		var delay = 100;
		for (i=0 ; i<chart.yAxis[yAxisId].series.length ; i++) {
			setTimeout(
		        (function (s) {
		            return function() {
		                chart.yAxis[yAxisId].series[s].update({
							type: $(typeSerie).val()
						});
		            }
		        }) (i), delay);
		    delay += 100;
		}
		
		if ($(this).val() == 'scatter') {
			$(markerSerie).prop('checked', true).change();
		} else {
			$(markerSerie).prop('checked', false).change();
		}
	});

	$(markerSerie).on('change', function() {
		if (debug) { console.log('%cModification du marqueur des séries pour : %c' + $(this).is(':checked'), 'color: #FFC300', 'color: #8085E8'); }
		var delay = 100;
		for (i=0 ; i<chart.yAxis[yAxisId].series.length ; i++) {
			setTimeout(
		        (function (s) {
		            return function() {
		                chart.yAxis[yAxisId].series[s].update({
							marker: { enabled: $(markerSerie).is(':checked') }
						});
		            }
		        }) (i), delay);
		    delay += 100;
		}
	});

	$(dashStyleSerie).on('change', function() {
		if (debug) { console.log('%cModification du style de ligne des séries pour : %c' + $(this).val(), 'color: #FFC300', 'color: #8085E8'); }
		var delay = 100;
		for (i=0 ; i<chart.yAxis[yAxisId].series.length ; i++) {
			setTimeout(
		        (function (s) {
		            return function() {
		                chart.yAxis[yAxisId].series[s].update({
							dashStyle: getDashStyle($(dashStyleSerie).val())
						});
		            }
		        }) (i), delay);
		    delay += 100;
		}
	});	
}

/* Définit un style de ligne par défault */
function getDashStyle(dashStyle)
{
	if (dashStyle == 'Solid') { return null; }
	return dashStyle;
}
