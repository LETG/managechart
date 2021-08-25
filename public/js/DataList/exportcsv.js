function exportTableToCSV(table, filename) {
	var headers = $(table).find('tr:has(th)');
	var rows = $(table).find('tr:has(td)');
	var tmpColDelim = String.fromCharCode(11);
    var tmpRowDelim = String.fromCharCode(0);
    var colDelim = '","';
    var rowDelim = '"\r\n"';

    var csv = '"';
    csv += formatRows(headers.map(grabRow));
    csv += rowDelim;
    csv += formatRows(rows.map(grabRow)) + '"';

    var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

    return {filename: filename, csvData: csvData};
        
    function formatRows(rows){
        return rows.get().join(tmpRowDelim)
            .split(tmpRowDelim).join(rowDelim)
            .split(tmpColDelim).join(colDelim);
    }
    
    function grabRow(i,row){
         
        var $row = $(row);
        var $cols = $row.find('td'); 
        if(!$cols.length) $cols = $row.find('th');  
        return $cols.map(grabCol)
                    .get().join(tmpColDelim);
    }
    
    function grabCol(j,col){
        var $col = $(col),
            $text = $col.text();
        // return addslashes($text);
       return $text.replace(/\"/g,'""');
    }

    // function addslashes(ch) {
    // ch = ch.replace(/\"/g,'""'); //Permet d'echapper les guillemets
    // return ch
    // }
}