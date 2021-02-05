'use strict';

$(function()
{
	Chart.defaults.global.multiTooltipTemplate= "<%= 'S/' + value %>";

	var dataSales = dataSalesView;
	var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
	var salesChart       = new Chart(salesChartCanvas);

	var salesChartData = {
		labels  : dataSales.map(function(data) { return data.mes }),
		datasets: [
			{
			label               : 'Compras',
			strokeColor         : 'rgba(255, 118, 0, 0.85)',
			pointColor          : 'rgba(255, 118, 0, 0.85)',
			pointStrokeColor    : 'rgba(255, 118, 0, 0.85)',
			pointHighlightFill  : '#fff',
			pointHighlightStroke: 'rgb(220,220,220)',
			data                : dataSales.map(function(data) { return data.compras })
		},
		{
			label               : 'Egresos',
			strokeColor         : 'rgba(255, 0, 0, 1)',
			pointColor          : 'rgba(255, 0, 0, 1)',
			pointStrokeColor    : 'rgba(255, 0, 0, 1)',
			pointHighlightFill  : '#fff',
			pointHighlightStroke: 'rgb(220,220,220)',
			data                : dataSales.map(function(data) { return data.egresos })
		},
		{
			label               : 'Ventas FE',
			strokeColor         : 'rgba(20, 160, 3, 0.95)',
			pointColor          : 'rgba(20, 160, 3, 0.95)',
			pointStrokeColor    : 'rgba(20, 160, 3, 0.95)',
			pointHighlightFill  : '#fff',
			pointHighlightStroke: 'rgba(20, 160, 3, 0.95)',
			data                : dataSales.map(function(data) { return data.ventasfe })
		},
		{
			label               : 'Ventas WEF',
			strokeColor         : 'rgba(239, 7, 200, 0.95)',
			pointColor          : 'rgba(239, 7, 200, 0.95)',
			pointStrokeColor    : 'rgba(239, 7, 200, 0.95)',
			pointHighlightFill  : '#fff',
			pointHighlightStroke: 'rgba(239, 7, 200, 0.95)',
			data                : dataSales.map(function(data) { return data.ventaswef })
		}
		]
	};

	var salesChartOptions = {
		datasetFill: false,
		// Boolean - If we should show the scale at all
		showScale               : true,
		// Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines      : true,
		// String - Colour of the grid lines
		scaleGridLineColor      : 'rgba(0,0,0,.05)',
		// Number - Width of the grid lines
		scaleGridLineWidth      : 1,
		// Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		// Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines  : true,
		// Boolean - Whether the line is curved between points
		bezierCurve             : true,
		// Number - Tension of the bezier curve between points
		bezierCurveTension      : 0.3,            
		// Number - Radius of each point dot in pixels
		pointDotRadius          : 4,
		// Number - Pixel width of point dot stroke
		pointDotStrokeWidth     : 1,
		// Number - amount extra to add to the radius to cater for hit detection outside the drawn point
		pointHitDetectionRadius : 20,
		// Boolean - Whether to show a stroke for datasets
		datasetStroke           : true,
		// Number - Pixel width of dataset stroke
		datasetStrokeWidth      : 2,
		// String - A legend template
		legendTemplate          : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li><span style=\'background-color:<%=datasets[i].strokeColor%>; width: 28px; display: inline-block;\'>&nbsp;</span> <%=datasets[i].label%></li><%}%></ul>',
		// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio     : true,
		// Boolean - whether to make the chart responsive to window resizing
		responsive              : true,
	};

	// Create the line chart
	Chart.defaults.global.multiTooltipTemplate= "<%= value %>";
	var myChart = salesChart.Line(salesChartData, salesChartOptions);
	var legend = myChart.generateLegend();
	$('#legend').append(legend);

	var dataSalesSunat = dataSalesSunatView;
	var salesChartCanvasSunat = $('#salesChartSunat').get(0).getContext('2d');
	var salesChartSunat       = new Chart(salesChartCanvasSunat);

	var salesChartDataSunat = {
		labels  : dataSalesSunat.map(function(data) { return data.mes }),
		datasets: [
			{
				label               : 'Facturas, NC, ND, GR y RD',
				strokeColor         : '#00c0ef',
				pointColor          : '#00c0ef',
				pointStrokeColor    : '#00c0ef',
				fillColor    : '#00c0ef',
				pointHighlightFill  : '#fff',
				pointHighlightStroke: 'rgb(220,220,220)',
				data                : dataSalesSunat.map(function(data) { return data.documentosGenerado })
			},
			{
				label               : 'Boletas',
				strokeColor         : '#3f51b5',
				pointColor          : '#3f51b5',
				pointStrokeColor    : '#3f51b5',
				fillColor    : '#3f51b5',
				pointHighlightFill  : '#fff',
				pointHighlightStroke: 'rgb(220,220,220)',
				data                : dataSalesSunat.map(function(data) { return data.boletasEmitidas })
			},
			{
				label               : 'Ventas WEF',
				strokeColor         : '#ffc107',
				pointColor          : '#ffc107',
				pointStrokeColor    : '#ffc107',
				fillColor    : '#ffc107',
				pointHighlightFill  : '#fff',
				pointHighlightStroke: 'rgb(220,220,220)',
				data                : dataSalesSunat.map(function(data) { return data.ventaswefemitidas })
			}
		]
	};

	var salesChartOptions = {
		datasetFill: false,
		// Boolean - If we should show the scale at all
		showScale               : true,
		// Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines      : true,
		// String - Colour of the grid lines
		scaleGridLineColor      : 'rgba(0,0,0,.05)',
		// Number - Width of the grid lines
		scaleGridLineWidth      : 1,
		// Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		// Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines  : true,
		// Boolean - Whether the line is curved between points
		bezierCurve             : true,
		// Number - Tension of the bezier curve between points
		bezierCurveTension      : 0.3,            
		// Number - Radius of each point dot in pixels
		pointDotRadius          : 4,
		// Number - Pixel width of point dot stroke
		pointDotStrokeWidth     : 1,
		// Number - amount extra to add to the radius to cater for hit detection outside the drawn point
		pointHitDetectionRadius : 20,
		// Boolean - Whether to show a stroke for datasets
		datasetStroke           : true,
		// Number - Pixel width of dataset stroke
		datasetStrokeWidth      : 2,
		// String - A legend template
		legendTemplate          : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li><span style=\'background-color:<%=datasets[i].strokeColor%>; width: 28px; display: inline-block;\'>&nbsp;</span> <%=datasets[i].label%></li><%}%></ul>',
		// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio     : true,
		// Boolean - whether to make the chart responsive to window resizing
		responsive              : true,
	};

	// Create the line chart
	var myChart = salesChartSunat.Bar(salesChartDataSunat, salesChartOptions);
	
	var legend = myChart.generateLegend();
	$('#legendSunat').append(legend);
});