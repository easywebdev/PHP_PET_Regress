// create object for ChartBuilder
var rowIndex;
var colIndex;
var chartBuilder = new  ChartBuilder(document.getElementById('myChart'));

$(document).ready(function() {
    buildRegress('/linear');

    // Get current rowIndex and colIndex
    $('#inputtbl tbody tr td').click(function () {
        rowIndex = $(this).closest('tr').index();
        colIndex = $(this).closest('td').index();

    });

    // Get clipboard data
    $('#inputtbl').bind('paste', function (e) {

        // stop pasting
        e.stopPropagation();
        e.preventDefault();

        let data = e.originalEvent.clipboardData.getData("text/plain");
        let rows = data.split("\n");
        let columnCount = rows[0].split('\t').length;
        let addRows = (rows.length - 1) - ($('#inputtbl tbody tr').length - rowIndex); // 'rows' always has + 1 empty row and we have to take it to account

        // add needed rows
        if(addRows > 0) {
            for (let i = 0; i < addRows; i++) {
                addRow();
            }
        }

        // paste data
        for (let i = 0; i < rows.length; i++) {
            let col = rows[i].split("\t");
            for (let j = 0; j < col.length; j++) {
                $('#inputtbl tbody tr:eq(' + (rowIndex + i) + ') td:eq(' + (colIndex + j) +')').html(col[j]);
            }
        }
    });

    $(window).resize(function() {
        setChartFonts();
    });

});

function buildRegress(regress)
{
    let dataPoints = [{x: 0, y: 0}, {x: 1, y: 1}, {x: 2, y: 2}, {x: 3, y: 3}, {x: 4, y: 4}, {x: 5, y: 5}];
    let dataLine = [{x: 0, y: 0}, {x: 1, y: 1}, {x: 2, y: 2}, {x: 3, y: 3}, {x: 4, y: 4}, {x: 5, y: 5}];

    // get input data
    dataPoints = parceTableDataToJson('inputtbl');

    $.ajax({
        url: regress,
        type: 'GET',
        data: {
            "inputData": dataPoints
        },
        success: function(answer) {
            // alert(JSON.stringify(dataPoints));
            // alert(JSON.stringify(answer));

            if(answer.err == null) {
                renderHtmlResult(answer, regress);
            }
            else {
                //alert(JSON.stringify(answer.err));
                msg = 'Error list:\n';
                $.each(answer.err, function (key, value) {
                   msg += (key + 1) + '. ' + value + '\n';
                });
                alert(msg);
            }
        }
    });


}

function renderHtmlResult(answer, regress)
{
    // Default values
    let dataPoints = [{x: 0, y: 0}, {x: 1, y: 1}, {x: 2, y: 2}, {x: 3, y: 3}, {x: 4, y: 4}, {x: 5, y: 5}];
    let dataLine = [{x: 0, y: 0}, {x: 1, y: 1}, {x: 2, y: 2}, {x: 3, y: 3}, {x: 4, y: 4}, {x: 5, y: 5}];

    // Regress values
    dataPoints = answer.source;
    dataLine = answer.interpolate;

    $('#result').html('<h2 class="h2">Result</h2><div id="regress"></div>');

    if(regress == '/polynomial') {
        $('#regress').html(
            '<p>Used formula: <span class="formula">A3&middot;X<sup>3</sup> + A2&middot;X<sup>2</sup> + A1&middot;X + A0</span></p>' +
            '<table class="tbl">' +
            '<tr><td>A3</td><td>' + answer.regress.A3 + '</td></tr>' +
            '<tr><td>A2</td><td>' + answer.regress.A2 + '</td></tr>' +
            '<tr><td>A1</td><td>' + answer.regress.A1 + '</td></tr>' +
            '<tr><td>A0</td><td>' + answer.regress.A0 + '</td></tr>' +
            '<tr><td>R<sup>2</sup></td><td>' + answer.regress.R2 + '</td></tr>' +
            '<tr><td>Err</td><td>' + answer.regress.regressErr + '</td></tr>' +
            '</table>'
        );
    }
    else {
        $('#regress').html(
            '<p>Used formula: <span class="formula">A1&middot;X + A0</span></p>' +
            '<table class="tbl">' +
            '<tr><td>Intercept (A1)</td><td>' + answer.regress.A1 + '</td></tr>' +
            '<tr><td>Slope (A0)</td><td>' + answer.regress.A0 + '</td></tr>' +
            '<tr><td>R<sup>2</sup></td><td>' + answer.regress.R2 + '</td></tr>' +
            '<tr><td>Err</td><td>' + answer.regress.regressErr + '</td></tr>' +
            '</table>'
        );
    }

    // Build chart
    chartBuilder.buildChart(dataPoints, dataLine);

    // Chart font size
    setChartFonts();
}

function parceTableDataToJson(tableID)
{
    let data = [];
    let row = [];
    let dataObj = new Object();
    let outArr = [];
    let jsonStr = '';

    // clear empty rows
    removeEmptyRows();

    // create row array from table
    // After php code table building table has more extrasybols: 'space' and '\n'
    // When javascript function 'addRow' add row - no extra symbols are add
    // If row has extrasymbols - each function split row by them and add to data array
    // To get same data array in any cases we have to remove extrasymbols first
    $('#' + tableID + ' tbody tr').each(function (i, elem) {
        row[i] = $(elem).html();
        row[i] = row[i].replace(/\n/g, '');                                                     // remove all '\n'
        row[i] = row[i].replace(/ /g, '');                                                      // remove all 'spaces'
        row[i] = row[i].replace('<tdcontenteditable="true">', '<td contenteditable="true">')    // fix for editable cells
    });

    // create array of data
    for (let m = 0; m < row.length; m++) {
        data[m] = [];
        $(row[m] + ' td').each( function (j, col) {
            data[m][j] = $(col).text();
        })
    }

    // create object from array
    for (let i = 0; i < row.length; i++) {
        dataObj = {
            x: data[i][0].trim(),
            y: data[i][1].trim(),
        }
        //outArr[i - 1] = JSON.stringify(dataObj)
        outArr.push(dataObj);
    }

    // jsonStr = JSON.stringify(dataObj);
    // console.log(outArr);

    return outArr;
}

function addRow()
{
    // find number of last row
    let lastNumber = parseInt($('#inputtbl tbody').find('tr').last().find('td').first().text());

    // Add row
    $('#inputtbl tbody').append(
        '<tr>' +
        '<td>' + (lastNumber + 1) + '</td>' +
        '<td contenteditable="true"></td>' +
        '<td contenteditable="true"></td>' +
        '</tr>'
    );
}

function removeLastRow()
{
    let curIndex = parseInt($('#inputtbl tr:last td:first').text());

    if(curIndex > 1) {
        $('#inputtbl tr:last').remove();
    }
    else {
        alert('Table must have rows');
    }
}

function removeEmptyRows()
{
    $('#inputtbl tbody tr').each(function (i, elem) {
        let emptyCount = 0;
        $(elem).find('td').each(function() {
            if($(this).text().trim() == '') {
                emptyCount++;
            }
        })

        if(emptyCount > 1) {
            $(this).closest('tr').remove();
        }
    });
}

function setChartFonts()
{
    let fontSize =  $('html').css('font-size');
    fontSize = fontSize.replace('px', '');

    this.chartBuilder.setFontSize(fontSize, fontSize, fontSize);
}