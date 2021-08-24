var colorJet = [
    [0, '#0000f8'],
    [0.15, '#0000ff'],
    [0.3, '#008fff'],
    [0.4, '#00ffff'],
    [0.5, '#00ff00'],
    [0.6, '#ffff00'],
    [0.7, '#ff8f00'],
    [0.85, '#ff0000'],
    [1, '#8f0000']
];

function getLimitValues(data){
    var limitValues = {
    'minXAxis': null,
    'maxXAxis': null,
    'minYAxis': null,
    'maxYAxis': null,
    'minValue': null,
    'maxValue': null,
    'uniqueXAxis': null
    };
	limitValues.length = 7;
	var unique = [];

	var firstValue = true;
	data.forEach(function(element){
		var xElement = element['x'];
       	var yElement = element['y'];
       	var valueElement = element['value'];
       	if(firstValue){
       		firstValue = false;
       	    limitValues['minXAxis'] = xElement;
       	    limitValues['maxXAxis'] = xElement;
           	limitValues['minYAxis'] = yElement;
           	limitValues['maxYAxis'] = yElement;
           	limitValues['minValue'] = valueElement;
           	limitValues['maxValue'] = valueElement;
           	unique = [xElement];
       	}else{
           	if(limitValues['minXAxis'] > xElement)
           		limitValues['minXAxis'] = xElement;
           	else if(limitValues['maxXAxis'] < xElement)
           		limitValues['maxXAxis'] = xElement;
           	if(!unique.includes(xElement))
           		unique.push(xElement);

           	if(limitValues['minYAxis'] > yElement)
           		limitValues['minYAxis'] = yElement;
           	else if(limitValues['maxYAxis'] < yElement)
           		limitValues['maxYAxis'] = yElement;
             
           	if(limitValues['minValue'] > valueElement)
           		limitValues['minValue'] = valueElement;
           	else if(limitValues['maxValue'] < valueElement)
           		limitValues['maxValue'] = valueElement;
       	}
   	});
   	limitValues['uniqueXAxis'] = unique.length;

    return limitValues;
}

export { colorJet, getLimitValues }
