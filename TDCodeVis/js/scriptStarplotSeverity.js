var w = 500,
	h = 500;

var colorscale = d3.scale.category20();
var legendText;

var myConfig = {
  w: w,
  h: h,
  maxValue: 0.6,
  levels: 6,
  ExtraWidthX: 300
}

var myData = null;
var dataset = [];
d3.csv("../js/read/starplotSeverity.csv") //inicio da leitura
    .row(function(d) { return {axis: d.axis, value: +d.value, description: d.description}; })
    .get(function(error, rows) {
	    myData = rows;
	    dataset = myDataIsReady();

	    RadarChart.draw("#chart", dataset, myConfig)

		var svg = d3.select('#body')
			.selectAll('svg')
			.append('svg')
			.attr("width", w+300)
			.attr("height", h)

		//Definindo um título para a legenda.
		var text = svg.append("text")
			.attr("class", "title")
			.attr('transform', 'translate(150,0)') 
			.attr("x", w - 20)
			.attr("y", 10)
			.attr("font-size", "12px")
			.attr("fill", "#404040")
			.text("Subtitle");
				
		//Inicializando legenda	
		var legend = svg.append("g")
			.attr("class", "legend")
			.attr("height", 100)
			.attr("width", 200)
			.attr('transform', 'translate(150,20)') 
			;
			//Criando cores dos quadrados
			legend.selectAll('rect')
			  .data(legendText)
			  .enter()
			  .append("rect")
			  .attr("x", w - 15)
			  .attr("y", function(d, i){ return i * 20;})
			  .attr("width", 10)
			  .attr("height", 10)
			  .style("fill", function(d, i){ return colorscale(i);})
			  ;
			//Creando texto dos quadrados
			legend.selectAll('text')
			  .data(legendText)
			  .enter()
			  .append("text")
			  .attr("x", w - 2)
			  .attr("y", function(d, i){ return i * 20 + 9;})
			  .attr("font-size", "12px")
			  .attr("fill", "black")
			  .text(function(d) { return d; })
			  ;

    });//Fim da leitura

function myDataIsReady() {
  console.log(myData);
  var versions = [];
  var severity = [];
  for (var i = 0; i < myData.length; i++) {
  	severity[i] = myData[i].axis;
  	versions[i] = myData[i].description;
  }
  const uniqueVersions = versions.reduce((arr, el) => arr.concat(arr.includes(el) ? [] : [el]), []);
  const uniqueSeverity = severity.reduce((arr, el) => arr.concat(arr.includes(el) ? [] : [el]), []);
  
  legendText = uniqueVersions;

  var dataset = [];
  var cont = 0;
  for (var i = 0; i < uniqueVersions.length; i++) {
  	dataset[i] = [];
  	for (var j = cont; j < cont+uniqueSeverity.length; j++) {
  		dataset[i][j-cont] = myData[j];
  	}
  	cont+=uniqueSeverity.length;
  }
  
  return(dataset);
}