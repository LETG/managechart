function addAttributes() {
	var attributsSpatiaux = $($('#prototype_attributSpatial').attr('data-prototype').replace(/__name__/g, $('.attributSpatial').length + 1));
	$('#container_attribut').prepend(attributsSpatiaux);
}

function removeAttribut(btn) {
	$(btn).parents('.attributSpatial').remove();

	$.each($('#container_attribut').children('.attributSpatial').get().reverse(), function(index, value) {
		$(value).children('div').children('label').first().html('Attribut Spatial n° ' + (index + 1));
		
		$(value).find('input, select').each(function() {
			var id = $(this).attr('id');
			var idArray = id.split("_");
			var newId = id.replace(idArray[4], (index + 1));
			$(this).attr('id', newId);

			var name = $(this).attr('name');
			var nameArray = name.split("[");
			var newName = name.replace(nameArray[2], (index + 1) + "]");
			$(this).attr('name', newName);

			var label = $(this).parent('div').prev('label').attr('for');
			var labelArray = label.split("_");
			var newLabel = label.replace(labelArray[4], (index + 1));
			$(this).parent('div').prev('label').attr('for', newLabel);
		});
	});
}
