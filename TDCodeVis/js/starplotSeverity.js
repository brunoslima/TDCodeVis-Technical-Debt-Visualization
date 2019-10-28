var RadarChart = {
  draw: function(id, d, options){
  var config = {
	 radius: 5,
	 w: 600,
	 h: 600,
	 factor: 1,
	 factorLegend: .85,
	 levels: 3,
	 maxValue: 0,
	 radians: 2 * Math.PI,
	 opacityArea: 0.5,
	 ToRight: 5,
	 TranslateX: 80,
	 TranslateY: 30,
	 ExtraWidthX: 100,
	 ExtraWidthY: 100,
	 color: d3.scale.category20()
	};
	
	if('undefined' !== typeof options){
	  for(var i in options){
		if('undefined' !== typeof options[i]){
		  config[i] = options[i];
		}
	  }
	}
	config.maxValue = Math.max(config.maxValue, d3.max(d, function(i){return d3.max(i.map(function(o){return o.value;}))}));
	var allAxis = (d[0].map(function(i, j){return i.axis}));
	var total = allAxis.length;
	var radius = config.factor*Math.min(config.w/2, config.h/2);
	var Format = d3.format('.0f');
	d3.select(id).select("svg").remove();
	
	var g = d3.select(id)
			.append("svg")
			.attr("width", config.w+config.ExtraWidthX)
			.attr("height", config.h+config.ExtraWidthY)
			.append("g")
			.attr("transform", "translate(" + config.TranslateX + "," + config.TranslateY + ")");
			;

	var tooltip;
	
	//Grades circulares
	for(var j=0; j<config.levels-1; j++){
	  var levelFactor = config.factor*radius*((j+1)/config.levels);
	  g.selectAll(".levels")
	   .data(allAxis)
	   .enter()
	   .append("svg:line")
	   .attr("x1", function(d, i){return levelFactor*(1-config.factor*Math.sin(i*config.radians/total));})
	   .attr("y1", function(d, i){return levelFactor*(1-config.factor*Math.cos(i*config.radians/total));})
	   .attr("x2", function(d, i){return levelFactor*(1-config.factor*Math.sin((i+1)*config.radians/total));})
	   .attr("y2", function(d, i){return levelFactor*(1-config.factor*Math.cos((i+1)*config.radians/total));})
	   .attr("class", "line")
	   .style("stroke", "black")
	   .style("stroke-opacity", "0.75")
	   .style("stroke-width", "0.5px")
	   .attr("transform", "translate(" + (config.w/2-levelFactor) + ", " + (config.h/2-levelFactor) + ")");
	}

	//Texto que indica o valor de cada nivel das grades circulares
	for(var j=0; j<config.levels; j++){
	  var levelFactor = config.factor*radius*((j+1)/config.levels);
	  g.selectAll(".levels")
	   .data([1]) //dummy data
	   .enter()
	   .append("svg:text")
	   .attr("x", function(d){return levelFactor*(1-config.factor*Math.sin(0));})
	   .attr("y", function(d){return levelFactor*(1-config.factor*Math.cos(0));})
	   .attr("class", "legend")
	   .style("font-family", "sans-serif")
	   .style("font-size", "10px")
	   .attr("transform", "translate(" + (config.w/2-levelFactor + config.ToRight) + ", " + (config.h/2-levelFactor) + ")")
	   .attr("fill", "black")
	   .text(Format((j+1)*config.maxValue/config.levels));
	}
	
	series = 0;

	var axis = g.selectAll(".axis")
			.data(allAxis)
			.enter()
			.append("g")
			.attr("class", "axis");

	//Axes linhas - Cada linha representa um atributo: no caso uma versão do software ou uma severidade
	axis.append("line")
		.attr("x1", config.w/2)
		.attr("y1", config.h/2)
		.attr("x2", function(d, i){return config.w/2*(1-config.factor*Math.sin(i*config.radians/total));})
		.attr("y2", function(d, i){return config.h/2*(1-config.factor*Math.cos(i*config.radians/total));})
		.attr("class", "line")
		.style("stroke", "black")
		.style("stroke-width", "1.5px");

	//Axes texto - Rotulo de cada uma das linhas que representam atributos
	axis.append("text")
		.attr("class", "legend")
		.text(function(d){return d})
		.style("font-family", "sans-serif")
		.style("font-size", "12px")
		.attr("text-anchor", "middle")
		.attr("dy", "1.5em")
		.attr("transform", function(d, i){return "translate(0, -10)"})
		.attr("x", function(d, i){return config.w/2*(1-config.factorLegend*Math.sin(i*config.radians/total))-60*Math.sin(i*config.radians/total);})
		.attr("y", function(d, i){return config.h/2*(1-Math.cos(i*config.radians/total))-20*Math.cos(i*config.radians/total);});

 
	d.forEach(function(y, x){
	  dataValues = [];
	  g.selectAll(".nodes")
		.data(y, function(j, i){
		  dataValues.push([
			config.w/2*(1-(parseFloat(Math.max(j.value, 0))/config.maxValue)*config.factor*Math.sin(i*config.radians/total)), 
			config.h/2*(1-(parseFloat(Math.max(j.value, 0))/config.maxValue)*config.factor*Math.cos(i*config.radians/total))		  ]);
		});
	  dataValues.push(dataValues[0]);
	  g.selectAll(".area")
					 .data([dataValues])
					 .enter()
					 .append("polygon")
					 .attr("class", "radar-chart-serie"+series)
					 .style("stroke-width", "3px")
					 .style("stroke", config.color(series)) //Cor da linha que determina a fronteira de uma região
					 .attr("points",function(d) {
						 var str="";
						 for(var pti=0;pti<d.length;pti++){
							 str=str+d[pti][0]+","+d[pti][1]+" ";
						 }
						 return str;
					  })
					 //.style("fill", function(j, i){return config.color(series)}) //Cor da região
					 .style("fill", "#ffffff") //Cor da região
					 //.style("fill-opacity", config.opacityArea)
					 .style("fill-opacity", 0) //Opacidade da regiao
					 .on('mouseover', function (d){
										z = "polygon."+d3.select(this).attr("class");
										g.selectAll("polygon")
										 .transition(200)
										 //.style("fill-opacity", 0.1);
					 					.style("fill-opacity", 0); 
										g.selectAll(z)
										 .transition(200)
										 //.style("fill-opacity", .7);
					 					.style("fill-opacity", 0);
									  })
					 .on('mouseout', function(){
										g.selectAll("polygon")
										 .transition(200)
										 //.style("fill-opacity", config.opacityArea);
										 .style("fill-opacity", 0);
					 });
	  series++;
	});
	series=0;


	d.forEach(function(y, x){
	  g.selectAll(".nodes")
		.data(y).enter()
		.append("svg:circle")
		.attr("class", "radar-chart-serie"+series)
		.attr('r', config.radius)
		.attr("alt", function(j){return Math.max(j.value, 0)})
		.attr("cx", function(j, i){
		  dataValues.push([
			config.w/2*(1-(parseFloat(Math.max(j.value, 0))/config.maxValue)*config.factor*Math.sin(i*config.radians/total)), 
			config.h/2*(1-(parseFloat(Math.max(j.value, 0))/config.maxValue)*config.factor*Math.cos(i*config.radians/total))
		]);
		return config.w/2*(1-(Math.max(j.value, 0)/config.maxValue)*config.factor*Math.sin(i*config.radians/total));
		})
		.attr("cy", function(j, i){
		  return config.h/2*(1-(Math.max(j.value, 0)/config.maxValue)*config.factor*Math.cos(i*config.radians/total));
		})
		.attr("data-id", function(j){return j.axis})
		.style("fill", config.color(series)).style("fill-opacity", 1) //Cor dos pontos
		.on('mouseover', function (d){
					newX =  parseFloat(d3.select(this).attr('cx')) - 10;
					newY =  parseFloat(d3.select(this).attr('cy')) - 5;
					
					tooltip
						.attr('x', newX)
						.attr('y', newY)
						.text(Format(d.value))
						.transition(200)
						.style('opacity', 1);
						
					z = "polygon."+d3.select(this).attr("class");
					g.selectAll("polygon")
						.transition(200)
						.style("fill-opacity", 0.1); 
					g.selectAll(z)
						.transition(200)
						.style("fill-opacity", .7);
				  })
		.on('mouseout', function(){
					tooltip
						.transition(200)
						.style('opacity', 0);
					g.selectAll("polygon")
						.transition(200)
						.style("fill-opacity", config.opacityArea);
				  })
		.append("svg:title")
		.text(function(j){return Math.max(j.value, 0)});

	  series++;
	});

	tooltip = g.append('text')
			   .style('opacity', 0)
			   .style('font-family', 'sans-serif')
			   .style('font-size', '13px');
  }
};
