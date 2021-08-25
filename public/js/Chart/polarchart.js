
/* * * * * * * Script utilisé pour les graphiques polaires * * * * * * */



$(document).ready(function()
{
	/* Ajout d'un seul axe-Y */
	addyAxis();

	/* Modification du formulaire yAxis */
	var listYaxis = $('div#container_yAxis').find('label').first().parent().parent();
	listYaxis.prepend('<div class="form-group"><label class="col-sm-2 col-md-2 control-label" style="color: #8085E8">Axe-Y n° 1</label></div>');
});
