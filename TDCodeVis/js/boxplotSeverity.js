//Definindo as dimensões e margens do gráfico.
var margin = {top: 10, right: 20, bottom: 30, left: 60},
    width = 960 - margin.left - margin.right,
    height = 600 - margin.top - margin.bottom;    
    
var barWidth = 100;

//Leitura dos dados e formatação.
d3.csv("../js/read/boxplotSeverity.csv", function(error, dados) {
  if (error) throw error;
  var data = [];
  var severity = [];
  dados.forEach(function(x) {
    if(severity.includes(x.severity) == false)severity.push(x.severity);
    var e = Math.floor(x.id - 1),
        s = Math.floor(x.debt),
        d = data[e];
    if (!d) d = data[e] = [s];
    else d.push(s);
    if (s > max) max = s;
    if (s < min) min = s;
  });
  
  //Ordena as contagens dos grupos de versões para que os métodos quantile funcionem.
  for(var key in data) {
    var dados = data[key];
    data[key] = dados.sort(sortNumber);
  }

  //Preparando os dados para construir os box plots.
  var boxPlotData = [];
  var out = [];
  var maxAbsolut = 0;
  for (var [key, dados] of Object.entries(data)) {
    //var localMin = d3.min(dados);
    //var localMax = d3.max(dados);

    //Trecho de código para identificação de outliers.
    var outliers = [];
    var q1 = d3.quantile(dados, 0.25);
    var q3 = d3.quantile(dados, 0.75);
    var iqr = q3 - q1;

    var index = 0;
    var lowerWhisker = Infinity;
    //Procura pelo whisker inferior, o valor mininmum dentro de q1 - 1.5 * iqr.
    while (index < dados.length && lowerWhisker == Infinity) {

      if (dados[index] >= (q1 - 1.5*iqr))
        lowerWhisker = dados[index];
      else if(outliers.indexOf(dados[index]) == -1){
        outliers.push(dados[index]);
      }
      index++;
    }

    index = dados.length-1; //Ajustando o indice para o fim do array.
    var upperWhisker = -Infinity;
    //Procura pelo whisker superior, o valor máximo dentro de q1 + 1.5 * iqr.
    while (index >= 0 && upperWhisker == -Infinity) {

      if (dados[index] <= (q3 + 1.5*iqr))
        upperWhisker = dados[index];
      else if(outliers.indexOf(dados[index]) == -1){
        outliers.push(dados[index]);
      }
      index--;
    }

    if(maxAbsolut < d3.max(outliers))maxAbsolut = d3.max(outliers);

    var obj = [];
    obj["key"] = key;
    obj["counts"] = dados;
    obj["quartile"] = boxQuartiles(dados); //Calcula o intervalo médio e interquartil.
    //obj["whiskers"] = [localMin, localMax];
    obj["whiskers"] = [lowerWhisker,upperWhisker];
    obj["color"] = selectColor(severity[key]);
    obj["outliers"] = outliers;
    boxPlotData.push(obj);
  }

  console.log(boxPlotData);

  //Calcula a escala do eixo x (versões do software).
  var xScale = d3.scalePoint()
    .domain(Object.keys(data)) //Data - tamanho desse vetor é o número de versões.
    .rangeRound([0, width])
    .padding([0.5]);

  //Calculando a escla do eixo y (tempo de retrabalho).
  var min = d3.min(dados); //Tempo de retrabalho minimo identificado.
  var max = d3.max(dados); //Tempo de retrabalho maximo identificado.
  if(max > maxAbsolut) maxAbsolut = max;
  var yScale = d3.scaleLinear()
    //.domain([min, max]) //Dominio entre o minimo e o máximo.
    .domain([0,maxAbsolut]) //Poderia ser 0 também.
    .range([height, 0]);
    
  //Selecioando o svg na qual os box plots são desenhados na tela.
	var svg = d3.select("#boxplot")
  	  .attr("width", width + margin.left + margin.right)
    	.attr("height", height + margin.top + margin.bottom)
	  .append("g")
  	  .attr("transform",
    	      "translate(" + margin.left + "," + margin.top + ")");

  //Adicionando os box plot no svg.
  var g = svg.append("g");

  //Desenhando os pontos que representam os outliers do conjunto se existem.
  for (var i = boxPlotData.length - 1; i >= 0; i--) {
    for (var j = boxPlotData[i].outliers.length - 1; j >= 0; j--) {
      var yOut = boxPlotData[i].outliers[j];

      g.selectAll(".whiskers")
        .data(boxPlotData)
        .enter()
        .append("circle")
        .attr("r", 5.0)
        .attr("cx", xScale(i))
        .attr("cy", yScale(yOut))
        //.style("stroke", "#000") //Borda dos outliers na cor preta.
        .style("stroke", selectColor(severity[i])) //Borda dos outliers na cor de acordo com a severidade do grupo.
        .style("stroke-width", 2)
        .style("fill", "none");
    }
  }
  
  //Desenhando as linhas verticais (bigode) do box plot.
  var verticalLines = g.selectAll(".verticalLines")
    .data(boxPlotData)
    .enter()
    .append("line")
    .attr("x1", function(d) { return xScale(d.key); })
    .attr("y1", function(d) { return yScale(d.whiskers[0]); })
    .attr("x2", function(d) { return xScale(d.key); })
    .attr("y2", function(d) { return yScale(d.whiskers[1]); })
    .attr("stroke", "#000")
    .attr("stroke-width", 2);
    //.attr("fill", "none");

  //Desenhando as linhas verticais do box plot.
  var rects = g.selectAll("rect")
    .data(boxPlotData)
    .enter()
    .append("rect")
    .attr("width", barWidth)
    .attr("height", function(d) {
      var quartiles = d.quartile;
      var height =  yScale(quartiles[0]) - yScale(quartiles[2]);      
      return height;
    })
    .attr("x", function(d) { return xScale(d.key) - (barWidth/2); })
    .attr("y", function(d) { return yScale(d.quartile[2]); })
    .attr("fill", function(d) { return d.color; })
    .attr("stroke", "#000")
    .attr("stroke-width", 2);

  //Desenhando todas as linhas horizontais de uma vez - bigodes e medianas.
  var horizontalLineConfigs = [
    // Bigode superior.
    {
      x1: function(d) { return xScale(d.key) - barWidth/2 },
      y1: function(d) { return yScale(d.whiskers[0]) },
      x2: function(d) { return xScale(d.key) + barWidth/2 },
      y2: function(d) { return yScale(d.whiskers[0]) }
    },
    // Linha da mediana.
    {
      x1: function(d) { return xScale(d.key) - barWidth/2 },
      y1: function(d) { return yScale(d.quartile[1]) },
      x2: function(d) { return xScale(d.key) + barWidth/2 },
      y2: function(d) { return yScale(d.quartile[1]) }
    },
    // Bigode inferior.
    {
      x1: function(d) { return xScale(d.key) - barWidth/2 },
      y1: function(d) { return yScale(d.whiskers[1]) },
      x2: function(d) { return xScale(d.key) + barWidth/2 },
      y2: function(d) { return yScale(d.whiskers[1]) }
    }
  ];

  //Percorendo cada linha horizontal e desenhando a mesma.
  for(var i=0; i < horizontalLineConfigs.length; i++) {

    var lineConfig = horizontalLineConfigs[i];
    var horizontalLine = g.selectAll(".whiskers")
      .data(boxPlotData)
      .enter()
      .append("line")
      .attr("x1", lineConfig.x1)
      .attr("y1", lineConfig.y1)
      .attr("x2", lineConfig.x2)
      .attr("y2", lineConfig.y2)
      .attr("stroke", "#000")
      .attr("stroke-width", 2)
      .attr("fill", "none");
  }

  //Adicionando eixo x - Versões do software.
  svg.append("g")
     .attr("transform", "translate(0," + height + ")")
     .call(d3.axisBottom(xScale));

  //Adicionando eixo y - Tempo de retrabalho.
  svg.append("g")
     .call(d3.axisLeft(yScale));

  //Adicionando rotulo eixo x.
  svg.append("text")             
      .attr("transform","translate(" + (width/2) + " ," + (height + margin.top + 16) + ")")
      .style("text-anchor", "middle")
      .text("Severity group");

  //Adicionando rotulo eixo y.
  svg.append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 15 - margin.left)
      .attr("x",0 - (height / 2))
      .attr("dy", "1em")
      .style("text-anchor", "middle")
      .text("Rework Time (min)");

  //Funções adicionais.
  function boxQuartiles(d) {
    return [
      d3.quantile(d, .25),
      d3.quantile(d, .5),
      d3.quantile(d, .75)
    ];
  }

  function sortNumber(a,b) {
    return a - b;
  }

  function selectColor(s) {
    
    if(s == 5) return(d3.rgb(255,0,0)); //VERMELHO
    else if(s == 4) return(d3.rgb(251,115,10)); //LARANJA
    else if(s == 3) return(d3.rgb(10,39,204)); //AZUL
    else if(s == 2) return(d3.rgb(255,251,40)); //AMARELO
    else if(s == 1)return(d3.rgb(0,255,0)); //VERDE
  }

});