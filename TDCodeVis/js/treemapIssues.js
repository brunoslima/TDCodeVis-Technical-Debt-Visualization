var svg = d3.select("svg"),
    width = +svg.attr("width"),
    height = +svg.attr("height");


var format = d3.format(",d");

var stratify = d3.stratify()
    .parentId(function(d) { return d.id.substring(0, d.id.lastIndexOf("/")); });

var x = d3.scaleLinear().range([0, width]),
    y = d3.scaleLinear().range([0, height]);

var treemap = d3.treemap()
    .size([width, height])
    .paddingOuter(3)
    .paddingTop(19)
    .paddingInner(2)
    .round(true);

d3.csv("../js/read/treemapIssues.csv", type, function(error, data2) {
  if (error) throw error;

  var root = stratify(data2)
      .sum(function(d) { return d.value; })
      .sort(function(a, b) { return b.height - a.height || b.value - a.value; });

  treemap(root);

  var cell = svg
    .selectAll(".node")
    .data(root.descendants())
    .enter().append("g")
      .attr("transform", function(d) { return "translate(" + d.x0 + "," + d.y0 + ")"; })
      .attr("class", "node")
      .each(function(d) { d.node = this; })
      .on("mouseover", hovered(true))
      .on("mouseout", hovered(false))
      .on("click",click);

  cell.append("rect")
      .attr("id", function(d) { return "rect-" + d.id; })
      .attr("width", function(d) { return d.x1 - d.x0; })
      .attr("height", function(d) { return d.y1 - d.y0; })
      .style("fill", function(d) { return selectColor(0); })
      .filter(function(d) { return !d.children; })
        .style("fill", function(d) { return selectColor(d.data.severity) });

  cell.append("clipPath")
      .attr("id", function(d) { return "clip-" + d.id; })
    .append("use")
      .attr("xlink:href", function(d) { return "#rect-" + d.id + ""; });

  var label = cell.append("text")
      .attr("clip-path", function(d) { return "url(#clip-" + d.id + ")"; });

  label
    .filter(function(d) { return d.children; })
    .selectAll("tspan")
      .data(function(d) { return d.id.substring(d.id.lastIndexOf("/") + 1).split(/(?=[A-Z][^A-Z])/g).concat("\xa0" + format(d.value)); })
    .enter().append("tspan")
      .attr("x", function(d, i) { return i ? null : 10; })
      .attr("y", 13)
      .text(function(d) { return d; });

  label
    .filter(function(d) { return !d.children; })
    .selectAll("tspan")
      .data(function(d) { return d.id.substring(d.id.lastIndexOf("/") + 1).split(/(?=[A-Z][^A-Z])/g).concat(format(d.value)); })
    .enter().append("tspan")
      .attr("x", 4)
      .attr("y", function(d, i) { return 15 + i * 10; })
      .text(function(d) { return d; });

  cell.append("title")
      .text(function(d) { return "Location: " + d.id + "\nAmount: " + format(d.value) + "\n" + getSeverity(d.data.severity); });

  function click(d) {
  x.domain([d.x0, d.x0 + d.x1]);
    y.domain([d.y0, 1]).range([d.y0 ? 20 : 0, height]);

    cell.transition()
      .duration(750)
      .attr("x", function(d) { return x(d.x0); })
      .attr("y", function(d) { return y(d.y0); })
      .attr("width", function(d) { return x(d.x0 + d.x1) - x(d.x0); })
      .attr("height", function(d) { return y(d.y0 + d.y1) - y(d.y0); });
  }
});

function hovered(hover) {
  return function(d) {
    d3.selectAll(d.ancestors().map(function(d) { return d.node; }))
        .classed("node--hover", hover)
      .select("rect")
        .attr("width", function(d) { return d.x1 - d.x0 - hover; })
        .attr("height", function(d) { return d.y1 - d.y0 - hover; });
  };
}

function type(d) {
  d.id = d.id;
  d.value = +d.value;
  d.severity = +d.severity;
  return d;
}

function selectColor(s) {
    
  if(s == 5) return(d3.rgb(255,0,0)); //VERMELHO
  else if(s == 4) return(d3.rgb(251,115,10)); //LARANJA
  else if(s == 3) return(d3.rgb(10,39,204)); //AZUL
  else if(s == 2) return(d3.rgb(255,251,40)); //AMARELO
  else if(s == 1)return(d3.rgb(0,255,0)); //VERDE
  else if(s== -1) return(d3.rgb(42,154,224));
  else return(d3.rgb(178,178,178));

}

function getSeverity(s) {
  
  c = "There is severity: ";
  if(s == 5) return(c + "Blocker"); //VERMELHO
  else if(s == 4) return(c + "Critical"); //LARANJA
  else if(s == 3) return(c + "Major"); //AZUL
  else if(s == 2) return(c + "Minor"); //AMARELO
  else if(s == 1)return(c + "Info"); //VERDE
  else return("");

}