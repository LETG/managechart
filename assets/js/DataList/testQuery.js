function query(url, request, paginate, attributsSpatiauxInitialises) {
	var dataSource = $('select#mc_datalistbundle_datalist_dataSource').val();
	var attributsSpatiaux = '';
	
	// Si les attributs spatiaux n'ont pas été initialisés préalablement, on essaye de les récupérer dans "container_attribut"
	// Si aucun attribut spatial n'est récupéré, c'est tout simplement qu'il n'y en a pas dans la requête
	if (attributsSpatiauxInitialises) {
		attributsSpatiaux = attributsSpatiauxInitialises;
	} else {
		$.each($('#container_attribut').children('.attributSpatial').get().reverse(), function(index, value) {
			var nameAttribut = $('input#mc_datalistbundle_datalist_attributsSpatiaux_' + (index + 1) + '_nameAttribut').val();
			var valueAttribut = $('input#mc_datalistbundle_datalist_attributsSpatiaux_' + (index + 1) + '_valueAttribut').val();
			var typeAttribut = $('select#mc_datalistbundle_datalist_attributsSpatiaux_' + (index + 1) + '_typeAttribut').val();
			var keywordAttribut = $('select#mc_datalistbundle_datalist_attributsSpatiaux_' + (index + 1) + '_keywordAttribut').val();

			var regexHPS = /\+/gmi;
			var hPS = regexHPS.test(nameAttribut);
			if (hPS) {
				var nameAttribut = nameAttribut.replace("+", "%2B"); 
			}

			/* Concatenation des valeurs */
			attributsSpatiaux += nameAttribut + '@' + valueAttribut + '@' + typeAttribut + '@' + keywordAttribut + ',';
		});

		/* Suppression de la derniere virgule */
		attributsSpatiaux = attributsSpatiaux.substring(0, attributsSpatiaux.length - 1);
	}
		
	$('form').animate({
		'opacity': '0.5'
	});
	$('div.cssload-loader').show();

	var regexHasPlusSymbol = /\+/gmi;
	var hasPlusSymbol = regexHasPlusSymbol.test(request);
	if (hasPlusSymbol) {
		var request = request.replace("+", "%2B"); 
	}

	$.ajax({
		type: 'POST',
		url: url,
		data: 'dataSource=' + dataSource + '&request=' + request + '&attributsSpatiaux=' + attributsSpatiaux + '&test=true',
		success: function(data) {
			$('div.cssload-loader').hide();
			$('form').animate({
				'opacity': '1'
			});

			$('#containerQuery').html(data);

			$('#containerQuery.testBrut').children('table').DataTable({
				"pagingType": "full_numbers",
				language: {
			        paginate: {
			            first:    '«',
			            previous: '‹',
			            next:     '›',
			            last:     '»'
			        },
			        "info": paginate[0],
			        "lengthMenu": paginate[1],
			        "search": "",
			        "searchPlaceholder": paginate[2]
				}
			});
			


			$('button#btncsvExp').one('click', function() {
				$('button#btncsvExp').attr('disabled', 'disabled');
				var img  = "<img src=\'"+ $("#img_path").val() +"\' width='8%'>";
				$('button#btncsvExp').parent('div').append(img);

				$.ajax({
					type: 'POST',
					url: url,
					data: 'dataSource=' + dataSource + '&request=' + request + '&attributsSpatiaux=' + attributsSpatiaux + '&test=false',
					success: function(html) {
						var table = $(html).filter('table').html();
						var filename = $('input#mc_datalistbundle_datalist_nameData').val() + '.csv';
						
						var donnees = exportTableToCSV(table, filename);

					 	$('button#btncsvExp').parent('div').append('<a id="downloadcsvfile" class="btn btn-default" href="' + donnees.csvData + '" download="' + donnees.filename + '">' + donnees.filename + '</a>');
					 	$('img').remove();
					 	$('button#btncsvExp').hide();

						$('#downloadcsvfile')[0].click();// equivalent du JS => document.getElementById("downloadcsvfile").click();
						$('#downloadcsvfile').attr('disabled', 'disabled'); //grisé le bouton (mais toujours cliquable)
					 // 	$('#downloadcsvfile').click(function(event){ // desactiver la possiblité de retélécharger
						// 	event.preventDefault();
						// });
					}
				});
			});
		},
		error: function(xhr) {
			$('div.cssload-loader').hide();
			$('form').animate({
				'opacity': '1'
			});

			$('#containerQuery').html(xhr.responseText);
		}
	});
}
