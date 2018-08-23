/* Start : used to generate image file using SVG of chart */
function generateChartPDF(classname, filename){
	/* Start : locally generate PDF */
	var doc = new jsPDF();
    var chartHeight = 80;
    //loop through each chart
    var cindex = 1;
    var existin = 0;
    $('.'+classname).each(function (index) {
    	if($(this).is(':empty') || $(this).text() == 'No Assignments'){
    	}else{
        	if(cindex%4 == 0){
        		doc.addPage();
        		chartHeight = 0;
            	sheight = 0;
            	cindex = 1;
            } else {
            	if(cindex == 1){
	            	chartHeight = 0;
	            	sheight = 0;
	            } else { 
	            	chartHeight = 80;
	            	sheight = (cindex-1) * chartHeight;
	            }
            } 
        	existin = 1;
            var imageData = $(this).highcharts().createCanvas();
           
            doc.addImage(imageData, 'JPEG', 35, sheight + 20, 150, chartHeight);
            cindex++;    
    	}
    });
	if(existin == 1){
	    var d = new Date();
		var curr_date = d.getDate();
		var curr_month = parseInt(d.getMonth())+1;
		var curr_year = d.getFullYear();
		var curr_hour = d.getHours();
		var curr_min = d.getMinutes();
		var curr_sec = d.getSeconds();
		if(curr_date < 10){
			curr_date = "0" + curr_date;
		}
		if(curr_month < 10){
			curr_month = "0" + curr_month;
		}
		var dateform = curr_month + "_" + curr_date + "_" + curr_year+ "__" + curr_hour + "_" + curr_min + "_" +curr_sec;
	    //save with name
		console.log(doc);
	    doc.save(filename + dateform +'.pdf');	
	}
    /* End : locally generate PDF */
}

function generateChartWithTablePDF(classname,tableid, filename){
	/* Start : locally generate PDF */
	var doc = new jsPDF();
    var chartHeight = 80;
    //loop through each chart
    var cindex = 1;
    var existin = 0;
    $('.'+classname).each(function (index) {
    	if($(this).is(':empty') || $(this).text() == 'No Assignments'){
    	}else{
        	if(cindex%4 == 0){
        		doc.addPage();
        		chartHeight = 0;
            	sheight = 0;
            	cindex = 1;
            } else {
            	if(cindex == 1){
	            	chartHeight = 0;
	            	sheight = 0;
	            } else { 
	            	chartHeight = 80;
	            	sheight = (cindex-1) * chartHeight;
	            }
            } 
        	existin = 1;
            var imageData = $(this).highcharts().createCanvas();
           
            doc.addImage(imageData, 'JPEG', 35, sheight + 20, 100, chartHeight);
            cindex++;    
    	}
    });
	if(existin == 1){
	    var d = new Date();
		var curr_date = d.getDate();
		var curr_month = parseInt(d.getMonth())+1;
		var curr_year = d.getFullYear();
		var curr_hour = d.getHours();
		var curr_min = d.getMinutes();
		var curr_sec = d.getSeconds();
		if(curr_date < 10){
			curr_date = "0" + curr_date;
		}
		if(curr_month < 10){
			curr_month = "0" + curr_month;
		}
		var dateform = curr_month + "_" + curr_date + "_" + curr_year+ "__" + curr_hour + "_" + curr_min + "_" +curr_sec;
	    //save with name
	    
		console.log(doc);
	    doc.save(filename + dateform +'.pdf');	
	}
    /* End : locally generate PDF */
}
//Highchart download pdf file functionality
/* Start : used to generate image file using SVG of chart */
(function (H) {
	H.Chart.prototype.createCanvas = function (divId) {
        var svg = this.getSVG(),
            width = parseInt(svg.match(/width="([0-9]+)"/)[1]),
            height = parseInt(svg.match(/height="([0-9]+)"/)[1]),
            canvas = document.createElement('canvas');

        canvas.setAttribute('width', width);
        canvas.setAttribute('height', height);

        if (canvas.getContext && canvas.getContext('2d')) {

            canvg(canvas, svg);

            return canvas.toDataURL("image/jpeg");

        } 
        else {
            alert("Your browser doesn't support this feature, please use a modern browser");
            return false;
        }

    }
}(Highcharts));
/* End : used to generate image file using SVG of chart */
