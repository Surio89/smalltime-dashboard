var pathToSmalltime = "https://office.mycompany.com/smalltime/";

$( document ).ready(function() {
	setStatus();
	clock = $('.clock').FlipClock({
		clockFace: 'TwentyFourHourClock',
		showSeconds: false
	});	
	$(".mitarbeiter").click(function(){
		$(this).css("pointer-events", "none");
		curStatus = $(this).attr("id-status");
		curUser = $(this).attr("id-usershort");
		newStatus = "0";
		//WENN ABWESEND
		
		if (curStatus == "0"){
			newStatus = "1";			
		}
		
		$(this).addClass("showstamp");
		$.get( pathToSmalltime+"/stampjson.php?id="+$(this).attr("id-secret"), function( data ) {
		  if(data.ok == "1"){
			$curMA = $(".mitarbeiter[id-usershort="+data.user+"]");
			$curMA.find(".info h3").html(data.time);
			if (newStatus == "1"){
				$curMA.find(".info h2").html("Kommt");
				$curMA.find(".anwesenheit").html("Anwesend");
			}else{
				$curMA.find(".info h2").html("Geht");
				$curMA.find(".anwesenheit").html("Abwesend");
			}
			setTimeout(function($curMA, newStatus){
				$curMA.attr( "id-status",newStatus ).removeClass("showstamp")
				$curMA.css("pointer-events", "auto");			
			}, 2000, $curMA,newStatus);
			
		  }else{
			alert("Ein Fehler ist aufgetreten, bitte wenden Sie sich an den Administrator.");  
		  }
		  
		}, "json" )
	
		.fail(function(){
			alert("Ein Fehler ist aufgetreten, bitte wenden Sie sich an den Administrator.");
		});			
	});
});

function setStatus(){
	$.get( pathToSmalltime+"/status.php", function( data ) {
	  $( "#getStatus" ).html( data );
	})
	.done(function(){
		$("#getStatus tr").each(function(){
			thisUsershort = $(this).attr("id-name");
			thisStatus = $(this).attr("id-online");
			curMitarb = $(".mitarbeiter[id-usershort="+thisUsershort+"]");
			curMitarb.attr("id-status",thisStatus);
			if (thisStatus == "1"){
				curMitarb.find(".anwesenheit").html("Anwesend").redraw();
			}
			else{
				curMitarb.find(".anwesenheit").html("Abwesend").redraw();
			}
		});
	});
}

window.setInterval(function(){
	var item = $('.mitarbeiter.showstamp');
	if (item.length == 0) {
	  setStatus();
	}	
}, 5000);


$.fn.redraw = function(){
  $(this).each(function(){
    var redraw = this.offsetHeight;
  });
};
