var diameter = 680; //Tamanho maximo das bolhas.

var bubble = d3.layout.pack()
    .sort(null)
    .size([diameter, diameter])
    .padding(8.0);

var svg = d3.select("#vis")
    .attr("width", diameter)
    .attr("height", diameter)
    .attr("class", "bubble");

d3.csv("../js/read/bubbleChartRework.csv", function(error, data){

    console.log(data);
    //Convertendo valores da quantidade representados em string para o formato númerico.
    data = data.map(function(d){ d.value = +d["rework"]; return d; });
    var nodes = bubble.nodes({children:data}).filter(function(d) { return !d.children; });
    
    var bolhas = svg.append("g")
        .attr("transform", "translate(0,0)")
        .selectAll(".bubble")
        .data(nodes)
        .enter();

    //Criando os circulos que representam cada bolha.
    bolhas.append("circle")
        .attr("r", function(d){ return d.r; }) //raio
        .attr("cx", function(d){ return d.x; })
        .attr("cy", function(d){ return d.y; })
        .on("click", function(d) {
            window.location="../view/treemapissues.php?type=2-issue="+d.title;
          })
        .style("fill", function(d) { return selecionarCores(d["severity"]); }) //cor de acordo com a severidade da issue.
        .append("title")
            .text(function(d){ return getDescription(d["title"],d["rework"],d["severity"],d["description"]);}); //Descrição da issue.

    //Adicionando o rotulo textual de cada bolha.
    bolhas.append("text")
        .attr("x", function(d){ return d.x; })
        .attr("y", function(d){ return d.y + 5; })
        .attr("text-anchor", "middle")
        .attr("font-size", function(d){return d.r/2.2;}) //Calculando tamanho da fonte dentro da bolha com base em seu raio.
        .attr("fill", "#000")
        .text(function(d){ return d["title"]; })
        .style({"font-family":"Helvetica Neue, Helvetica, Arial, san-serif"});

});

function selecionarCores(s) {
    
  if(s == 5) return(d3.rgb(255,0,0)); //VERMELHO - Blocker
  else if(s == 4) return(d3.rgb(251,115,10)); //LARANJA - Critical
  else if(s == 3) return(d3.rgb(10,39,204)); //AZUL - Major
  else if(s == 2) return(d3.rgb(255,251,40)); //AMARELO - Minor
  else return(d3.rgb(0,255,0)); //VERDE - Info
}

function getDescription(title, rework, severity, description){
    var rotulo = "";
    var severityTxt = "";

    if(severity == 5) severityTxt = "Blocker";
    else if(severity == 4) severityTxt = "Critical";
    else if(severity == 3) severityTxt = "Major";
    else if(severity == 2) severityTxt = "Minor";
    else severityTxt = "Info";

    description = getReplace(description);

    rotulo = rotulo + "Title: " + title + "\nRework: " + rework + " minutes" + "\nSeverity: " + severityTxt + "\nDescription: " + description;
    return rotulo;
}

function getReplace($s){

    return $s.replace(/<br>/g, "\n\t");
}