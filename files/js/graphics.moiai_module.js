/* Вспомогательные функции */

function labelFormatter(label, series) {
	return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
}

function plotAccordingToChoices() {

	var data = [];

	var choiceContainer = $("#choices");

	choiceContainer.find("input:checked").each(function () {
		var key = $(this).attr("name");
		if (key && datasets[key]) {
			data.push(datasets[key]);
		}
	});

	if (data.length > 0) {
		$.plot("#placeholder", data, {
			yaxis: {
				min: 0
			},
			xaxis: {
				tickDecimals: 0
			}
		});
	}
}

// helper for returning the weekends in a period
function weekendAreas(axes) {

	var markings = [],
		d = new Date(axes.xaxis.min);

	// go to the first Saturday

	d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
	d.setUTCSeconds(0);
	d.setUTCMinutes(0);
	d.setUTCHours(0);

	var i = d.getTime();

	// when we don't set yaxis, the rectangle automatically
	// extends to infinity upwards and downwards

	do {
		markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
		i += 7 * 24 * 60 * 60 * 1000;
	} while (i < axes.xaxis.max);

	return markings;
}

/* Круговая диаграмма */

function showDiagramPie(diagramData, placeholderClass) {

	var data = new Object( [] );

	for (var i = 0; i < diagramData.length; i++) {
		data[i]  = new Object();
		data[i]['label'] = diagramData[i][0];
		data[i]['data'] = diagramData[i][1];
	};

    var placeholder = $("." +placeholderClass);
	placeholder.unbind();

	$.plot(placeholder, data, {

		series: {
			pie: {
				show: true,
				radius: 1,
				label: {
					show: true,
					radius: 3/4,
					formatter: labelFormatter,
					background: {
					    opacity: 0.5
					}
				}
			}
		}

		});

		placeholder.bind("plotclick", function(event, pos, obj) {
	
		if (!obj) {
			return;
		}
		
		percent = parseFloat(obj.series.percent).toFixed(2);
			alert(""  + obj.series.label + ": " + percent + "%");
		});

}

/* График */

function showDiagramPlot(diagramData, placeholderClass){
	$(document).ready(function() {

		for (var i = 0; i < diagramData.length; ++i) {
			diagramData[i][0] += 60 * 60 * 1000;
		}

		var options = {
			xaxis: {
				mode: "time",
				tickLength: 5
			},
			selection: {
				mode: "x"
			},
			grid: {
				markings: weekendAreas
			}
		};

		var plot = $.plot("." + placeholderClass, [diagramData], options);

		var overview = $.plot("#overview", [diagramData], {
			series: {
				lines: {
					show: true,
					lineWidth: 1
				},
				shadowSize: 0
			},
			xaxis: {
				ticks: [],
				mode: "time"
			},
			yaxis: {
				ticks: [],
				min: 0,
				autoscaleMargin: 0.1
			},
			selection: {
				mode: "x"
			}
		});

		// now connect the two

		$("." + placeholderClass).bind("plotselected", function (event, ranges) {

			// do the zooming
			$.each(plot.getXAxes(), function(_, axis) {
				var opts = axis.options;
				opts.min = ranges.xaxis.from;
				opts.max = ranges.xaxis.to;
			});
			plot.setupGrid();
			plot.draw();
			plot.clearSelection();

			// don't fire event on the overview to prevent eternal loop

			overview.setSelection(ranges, true);
		});

		$("#overview").bind("plotselected", function (event, ranges) {
			plot.setSelection(ranges);
		});

	});
}

/* Диаграмма столбиками */

function showDiagramColumn(diagramData, placeholderClass){
		
		$.plot("." + placeholderClass, [ diagramData ], {
			series: {
				bars: {
					show: true,
					barWidth: 0.6,
					align: "center"
				}
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			}
		});

}

/* Связной график */

function showDiagramLinked(dataset, placeholderClass){
	
	$(function() {

		var datasets = dataset;

		//console.log(dataset);

		// hard-code color indices to prevent them from shifting as
		// countries are turned on/off

		var i = 0;
		$.each(datasets, function(key, val) {
			val.color = i;
			++i;
		});

		// insert checkboxes 
		var choiceContainer = $(".choices_" + placeholderClass);
		$.each(datasets, function(key, val) {
			choiceContainer.append("<input type='checkbox' name='" + key +
				"' checked='checked' id='id" + key + "'></input>" +
				"<label for='id" + key + "'>"
				+ val.label + "</label>");
		});

		choiceContainer.find("input").click(plotAccordingToChoices);

		function plotAccordingToChoices() {

			var data = [];

			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("name");
				if (key && datasets[key]) {
					data.push(datasets[key]);
				}
			});

			if (data.length > 0) {
				$.plot(".placeholder_right_" + placeholderClass, data, {
					yaxis: {
						min: 0
					},
					xaxis: {
						mode: "time"
					}
				});
			}
		}

		plotAccordingToChoices();

	});

	/*var datasets = {
		"usa": {
			label: "USA",
			data: [[1988, 483994], [1989, 479060], [1990, 457648], [1991, 401949], [1992, 424705], [1993, 402375], [1994, 377867], [1995, 357382], [1996, 337946], [1997, 336185], [1998, 328611], [1999, 329421], [2000, 342172], [2001, 344932], [2002, 387303], [2003, 440813], [2004, 480451], [2005, 504638], [2006, 528692]]
		},        
		"russia": {
			label: "Russia",
			data: [[1988, 218000], [1989, 203000], [1990, 171000], [1992, 42500], [1993, 37600], [1994, 36600], [1995, 21700], [1996, 19200], [1997, 21300], [1998, 13600], [1999, 14000], [2000, 19100], [2001, 21300], [2002, 23600], [2003, 25100], [2004, 26100], [2005, 31100], [2006, 34700]]
		},
		"uk": {
			label: "UK",
			data: [[1988, 62982], [1989, 62027], [1990, 60696], [1991, 62348], [1992, 58560], [1993, 56393], [1994, 54579], [1995, 50818], [1996, 50554], [1997, 48276], [1998, 47691], [1999, 47529], [2000, 47778], [2001, 48760], [2002, 50949], [2003, 57452], [2004, 60234], [2005, 60076], [2006, 59213]]
		}
	};

	// hard-code color indices to prevent them from shifting as
	// countries are turned on/off

	var i = 0;
	$.each(datasets, function(key, val) {
		val.color = i;
		++i;
	});

	if (datasets.length > 0) {
		$.plot(".placeholder_link", data, {
			yaxis: {
				min: 0
			},
			xaxis: {
				tickDecimals: 0
			}
		});
	}

	//insert checkboxes 
	var choiceContainer = $("#choices");
	$.each(datasets, function(key, val) {
		choiceContainer.append("<br/><input type='checkbox' name='" + key +
			"' checked='checked' id='id" + key + "'></input>" +
			"<label for='id" + key + "'>"
			+ val.label + "</label>");
	});

	choiceContainer.find("input").click(plotAccordingToChoices);

	plotAccordingToChoices();*/

}