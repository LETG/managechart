/* Couleurs primaires */
var colorMix = [
	'#000000'	// Black
	,'#FF0000'	// Red
	,'#00FF00'	// Green
	,'#0000FF'	// Blue
	,'#FFFF00'	// Yellow
	,'#FFFFFF'	// White
	,'#8800FF'	// Purple
	,'#FF8800'	// Orange
	,'#FF00FF'	// Pink
];

/* Liste de couleurs sélectionnés
	Valeurs identiques à ./managechart/src/Mc/ChartBundle/AvailableChoice/AvailableColorSerie.php */
var colorAvailable = [
	'#3399CC' // Blue
	,'#65BFE4'
	,'#006699'
	,'#1A7BB8'
	,'#D4E6F2'
	,'#007D83' // Green
	,'#2AA8B0'
	,'#A0D8DC'
	,'#228527'
	,'#3BA737'
	,'#BAE0B9'
	,'#799B13'
	,'#9CC11B'
	,'#CBDE88'
	,'#E0B100' // Orange
	,'#FCCA12'
	,'#FEE58A'
	,'#D68300'
	,'#F19607'
	,'#F9D397'
	,'#BB431B' // Red
	,'#E65321'
	,'#F4B39D'
	,'#B82222'
	,'#E53336'
	,'#F19395' // Pink
	,'#B80065'
	,'#E1037C'
	,'#F49ECC'
	,'#6E1E68' // Purple
	,'#9E5298'
	,'#D9BCD7'
	,'#5A4E40' // Brown
	,'#968A7A'
	,'#DBD6D1' // Light Gray
	,'#00274A' // Dark Blue
	,'#656565' 	// Dark Gray
];

/* Convertit une chaîne de caractère en couleur */
function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

/* Convertit une chaîne de caractère RGB en couleur Hex */
function rgbToHex(r, g, b) {
    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

/* Fonction REGEX convertisant une couleur rgb(r,g,b) en tableau */
function parseRGB(color){
	var matchColors = /^rgb\((0|255|25[0-4]|2[0-4]\d|1\d\d|0?\d?\d),(0|255|25[0-4]|2[0-4]\d|1\d\d|0?\d?\d),(0|255|25[0-4]|2[0-4]\d|1\d\d|0?\d?\d)\)$/
	var match = matchColors.exec(color);
	if( match != null){
		return [match[1],match[2],match[3]];
	}else
		return null;
}

/* Fonction permettant de mixer une couleur de base avec une couleur prédéfini */
function colorMixing(baseColor, mixColor){
	var rgbBaseColor = parseRGB(baseColor);
	var rgbMixColor = parseRGB(mixColor);

	var red = Math.floor((parseInt(rgbBaseColor[0])+parseInt(rgbMixColor[0]))/2);
	var green =  Math.floor((parseInt(rgbBaseColor[1])+parseInt(rgbMixColor[1]))/2);
	var blue =  Math.floor((parseInt(rgbBaseColor[2])+parseInt(rgbMixColor[2]))/2);
	var hexColor = rgbToHex(red,green,blue);

	return hexColor;
}

/*  Fonction créant un tableau de couleur HEX, résultat du mix entre une couleur de base et de couleurs prédéfini */
function getColorsMixed(baseColor){
	var graphColors = [];
	graphColors.push(baseColor);

	for(var mix = 0; mix < colorMix.length;mix++){
		var hexColor = colorMixing(Highcharts.Color(baseColor).get(), Highcharts.Color(colorMix[mix]).get());
		graphColors.push(hexColor);
	}

	return graphColors;
}

/* Fonction créant un tableau de couleur HEX, résultat d'un algorithme de sélection entre la couleur de base, le nombre d'entité de la série et le tableau de couleurs à disposition */
function getColorsShuffled(baseColor, numberItems){
	var graphColors = [];
	graphColors.push(baseColor);

	if((numberItems != null) && (numberItems > 0)){
		var startingIndex = colorAvailable.indexOf(baseColor);
		var currentIndex = startingIndex;
		var step = 1;

		if(numberItems < colorAvailable.length)
			step = Math.floor(colorAvailable.length / numberItems);

		for(var shuffle = 1; shuffle < numberItems; shuffle++){
			currentIndex = (currentIndex + step)%((colorAvailable.length) - 1);
			graphColors.push(colorAvailable[currentIndex]);
		}
	}
	return graphColors;
}