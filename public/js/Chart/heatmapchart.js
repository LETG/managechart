var defaultColumnSize = 1;

$(document).ready(function()
{
	/* Ajout d'un axe-Y */
	addyAxis();

	/* Modification du formulaire yAxis */
	var containerYAxis = $('div#container_yAxis');
	var listYaxis = $('div#container_yAxis').find('label').first().parent().parent();
	$(containerYAxis).find('div.addSeries').remove();
	listYaxis.prepend('<div class="form-group"><label class="col-sm-2 col-md-2 control-label" style="color: #8085E8">Axe-Y n° 1</label></div>');

	/* Ajout du formulaire de série */
	var containerSerie = $('div#container_series_1');
	var series = $($('#prototype_series').attr('data-prototype')
		.replace(/__nameyaxis__/g, 1)
		.replace(/__name__/g, 1)
	);
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
	//$(containerSerie).find('input[id$=_markerSerie]').parents('.form-group').remove();
	//$(containerSerie).find('select[id$=_dashStyleSerie]').parents('.form-group').remove();

	/* Si on selectionne une requête */
	var dataList = $(containerSerie).find('select[id$=_dataList]');
	var idDataList = $(dataList).val();

	var chart = $('#container').highcharts();

	var functionCallBack = function() {
		for (var parameter in dataGlob[idDataList]) {
			console.log('%cAjout de la serie : %c' + parameter, 'color:  #FFC300', 'color: #8085E8');
    		addOneSerie(parameter, containerSerie, 1);
		}
	}

	if (idDataList != '') {
		getData(idDataList, null, true, functionCallBack);
	}

	/* Au changement de requête */
	$(dataList).on('change', function() {
        for(var i = chart.series.length -1; i >= 0; i--) {
            chart.series[i].remove();
        }

		indexSerieChart = 0;
		idDataList = $(this).val();

		functionCallBack = function() {
			for (parameter in dataGlob[idDataList]) {
				console.log('%cAjout de la serie : %c' + parameter, 'color:  #FFC300', 'color: #8085E8');
	    		addOneSerie(parameter, containerSerie, 1);
			}
		}

		if (idDataList != '') {
 			getData(idDataList, null, true, functionCallBack);
		}
	});
});

/* Fonction permettant l'ajout d'une série */
function addOneSerie(parameter, containerserie, i) {
	var dataList = $(containerserie).find('select[id$=_dataList]');
	
	var markerSerie = $(dataList).parents('.form-group').next().find('input[id$=_markerSerie]');
	var dashStyleSerie = $(dataList).parents('.form-group').next().find('select[id$=_dashStyleSerie]');

	arrayCorresIndexSerieChart[i.toString()] = indexSerieChart;
	indexSerieChart++;

	console.log('%cIdentifiant de la serie : %c' + indexSerieChart, 'color: #FFC300', 'color: #8085E8');
		
	var orderSeries = $(containerserie).find('input#mc_chartbundle_chart_list_yAxis_1_series_1_yaxisOrder').val(1);

	var chart = $('#container').highcharts();
	chart.addSeries({
		id: indexSerieChart,
		yAxis: 'yAxis1',
		name: parameter,
		//type: $(typeSerie).val(),
		data: []
		,turboThreshold: 0
		,boostThreshold: 0
		,rowsize: 1.2
	});

	if ($(dataList).val() != '') {
		setSerie(indexSerieChart.toString(), $(dataList).val(), (indexSerieChart -1).toString(),1);
		updateChart(chart);
	}

	var colsizeinput = $('input#mc_chartbundle_chart_list_yAxis_1_series_1_colsize');
	$(colsizeinput).on('change', function() {
		console.log('%cModification de la taille de la colonne pour : %c' + $(colsizeinput).val(), 'color: #43A832', 'color: #8085E8');

		var value = $('input#mc_chartbundle_chart_list_yAxis_1_series_1_colsize').val();
		if(value == "" || value <= 0)
			var value = defaultColumnSize;
		chart.yAxis[0].series[indexSerieChart-1].update({
			colsize: value
		});
	});
}

function updateChart(chart){
	var limitValues = getLimitValues(chart.yAxis[0].series[indexSerieChart-1].data);
	defaultColumnSize = (limitValues['maxXAxis'] - limitValues['minXAxis'])/(limitValues['uniqueXAxis']);
	
	// ASFLAG : Penser à modifier les champs des noms de labels X, Y & Value pour affecter selon la requête/les champs
	chart.update({
    	xAxis: {
       		labels: {
				formatter: function(){
					return '' + Math.exp(this.value).toFixed(2);
				}
			}
   		},
    	yAxis: {
       		reversed: true
    	},
    	tooltip: {
    		pointFormatter: function(){
				return 'X : ' + Math.exp(this.x) + '<br>' +
				   'Y : ' + this.y + '<br>' +
				   'Value : ' + this.value;
			}
    	}
    },false);

	chart.update({
    	colorAxis: {
    		min: limitValues['minValue'],
    		max: limitValues['maxValue']
    	}
    },false);

    chart.update({
    	series:{
    		colsize: defaultColumnSize
    	}
    });

    // ASFLAG : Assigne la valeur sur l'Input associé. Si la valeur colsize est défini en base, elle remplacera cette valeur.
    $('input#mc_chartbundle_chart_list_yAxis_1_series_1_colsize').val(defaultColumnSize).change();
}
