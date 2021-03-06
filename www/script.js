

// select an image for cropping
var currentid=0, lastid=0;
function crop(i,id,path,project,mode) {
    $('#croppingarea').html('<img src="'+path+'/'+project+'/temp/'+mode+'/'+i+'" alt="'+i+'" id="croppingimage"/>');
    $('#filename').val(i);
    $('#cr'+id).addClass('active');
    if (lastid) {
	$('#cr'+lastid).removeClass('active');
    }
    currentid=id;
    lastid=id;
    $('#relx1').val("");
    $('#rely1').val("");
    $('#relx2').val("");
    $('#rely2').val("");
    $('#w').val("");
    $('#h').val("");
    $('#r').val("");
    redraw();
    // Now we search for the top/left/right/bottom values and launch a redraw
    $.ajax({
	dataType: "json",
	url: 's3_crop_ajax.php?action=get&picture='+i+'&mode='+mode+'&project='+project,
	cache: false,
	async: false,
	success: function(data) {
	    $('#relx1').val(data.left);
	    $('#rely1').val(data.top);
	    $('#relx2').val(data.right);
	    $('#rely2').val(data.bottom);
	    $('#w').val("");
	    $('#h').val("");
	    $('#r').val(data.rotate);
	    redraw();
	    var parentOffset = $("#cl"+currentid).parent().position(); 
	    $('#cropscroll').scrollTop($('#cropscroll').scrollTop()+(parentOffset.top-300));
	}
    });
}


function submit_crop(way,mode,project) {
    if (way==3) {
	if (!confirm("Confirm ?")) 
	    return;
    }
    // 1: previous   2: next  3: all 
    $('#submitmsg').html("<div class=\"alert\">Submitting data...</div>");
    var ret = $.ajax({
	url:            's3_crop_ajax.php?action='+way
	    +'&left='+$('#relx1').val()
	    +'&right='+$('#relx2').val()
	    +'&top='+$('#rely1').val()
	    +'&bottom='+$('#rely2').val()
	    +'&rotate='+$('#r').val()
	    +'&project='+project
	    +'&picture='+$('#filename').val()
	    +'&mode='+mode,
	type:           'GET',
	cache:          false,
	async:          false,
	success: function(data) {
	    if (data.substring(0,6)=="ERROR:") {
		data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
		data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#submitmsg').html(data);
	}
    })
    // we are here AFTER the return of the call (synchronously)
    if (way==1) {
	if (currentid>0) {
	    currentid--;
	}
    } else {
	if ($('#cl'+(currentid+1))) {
	    currentid++;
	}
    }
    if ($('#cl'+currentid)) {
	$('#cl'+currentid).click();
    }
}


function cleartopleft() {
    $('#relx1').val("");
    $('#rely1').val("");
    redraw();
}
function clearbottomright() {
    $('#relx2').val("");
    $('#rely2').val("");
    redraw();
}
function clearwidthheight() {
    $('#w').val("");
    $('#h').val("");
    redraw();
}

function redraw(isclick=false) {
    var context=document.getElementById("croppingcanvas");
    context.width=context.width; // CLEAR
    var relx1=0, relxy1=0, relx2=0, rely2=0, ok=0;
    var context=document.getElementById("croppingcanvas").getContext("2d");
    
    if (
	$('#relx1').val() && $('#rely1').val()
    ) {
	relx1=parseInt($('#relx1').val(),10);
	rely1=parseInt($('#rely1').val(),10);
	context.moveTo(relx1-10, rely1);
	context.lineTo(relx1+10, rely1);
	context.moveTo(relx1, rely1-10);
	context.lineTo(relx1, rely1+10);
	context.strokeStyle = "#F00";
	context.stroke();
	ok++;
	if (isclick && !$('#relx2').val() && !$('#rely2').val() &&
	    $('#w').val() && $('#h').val()
	   ) { // top and width filled but not bottom => auto fill
	    $('#relx2').val( relx1+parseInt($('#w').val()) )
	    $('#rely2').val( relx1+parseInt($('#h').val()) )
	}
    }
    if (
	$('#relx2').val() && $('#rely2').val()
    ) {
	relx2=parseInt($('#relx2').val(),10);
	rely2=parseInt($('#rely2').val(),10);
	context.moveTo(relx2-10, rely2);
	context.lineTo(relx2+10, rely2);
	context.moveTo(relx2, rely2-10);
	context.lineTo(relx2, rely2+10);
	context.strokeStyle = "#F00";
	context.stroke();
	ok++;
    }
 
    if (ok==2) {
	// we have both, draw the rectangle
	context.moveTo(relx1-10, rely1);
	context.lineTo(relx2, rely1);
	context.moveTo(relx1, rely1-10);
	context.lineTo(relx1, rely2);
	context.moveTo(relx2, rely1);
	context.lineTo(relx2, rely2+10);
	context.moveTo(relx1, rely2);
	context.lineTo(relx2+10, rely2);
	context.strokeStyle = "#F00";
	context.stroke();
	$('#w').val(parseInt($('#relx2').val())-parseInt($('#relx1').val()))
	$('#h').val(parseInt($('#rely2').val())-parseInt($('#rely1').val()))
	$('#prev').removeClass('disabled');	$('#prev').removeAttr('disabled','');
	$('#next').removeClass('disabled');	$('#next').removeAttr('disabled','');
	$('#allnext').removeClass('disabled');	$('#allnext').removeAttr('disabled','');
    } else {
	$('#prev').addClass('disabled');	$('#prev').attr('disabled','disabled');
	$('#next').addClass('disabled');	$('#next').attr('disabled','disabled');
	$('#allnext').addClass('disabled');	$('#allnext').attr('disabled','disabled');
    }
    rot=parseInt($('#r').val(),10);
    if (isNaN(rot)) rot=0;
    $('#croppingimage').css("-moz-transform","rotate("+(rot/10)+"deg)");
    $('#croppingimage').css("-webkit-transform","rotate("+(rot/10)+"deg)");
    $('#croppingimage').css("transform","rotate("+(rot/10)+"deg)");
}

function clickCanvas(e) {
    var parentOffset = $("#croppingcanvas").offset(); 
    var relX = parseInt(e.pageX - parentOffset.left,10);
    var relY = parseInt(e.pageY - parentOffset.top,10);
    if (!$('#relx1').val() || !$('#rely1').val()) {
	$('#relx1').val(relX);
	$('#rely1').val(relY);
    } else 
	if (!$('#relx2').val() || !$('#rely2').val()) {
	    $('#relx2').val(relX);
	    $('#rely2').val(relY);
	} else {
	    $('#relx1').val(relX);
	    $('#rely1').val(relY);
	    if ($('#w').val() && $('#h').val()) {
		$('#relx2').val(relX+parseInt($('#w').val(),10));
		$('#rely2').val(relY+parseInt($('#h').val(),10));
	    }
	}
    redraw(true);
}

function cam_search() {
    $('#camerastatus').html("<div class=\"alert\">Searching for cameras...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=search',
	    type:           'GET',
	    cache:          false,
	    async:           true,
	    success: function(data) {
	    if (data.substring(0,6)=="ERROR:") {
	      data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
	      data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#camerastatus').html(data);
	  }
	})
}

function cam_zoomin(much) {
    $('#camerastatus').html("<div class=\"alert\">Zooming IN...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=zoomin&much='+much,
	    type:           'GET',
	    cache:          false,
	    async:           true,
	    success: function(data) {
	    if (data.substring(0,6)=="ERROR:") {
	      data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
	      data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#camerastatus').html(data);
	  }
	})
}


function cam_zoomout(much) {
    $('#camerastatus').html("<div class=\"alert\">Zooming OUT...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=zoomout&much='+much,
	    type:           'GET',
	    cache:          false,
	    async:           true,
	    success: function(data) {
	    if (data.substring(0,6)=="ERROR:") {
	      data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
	      data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#camerastatus').html(data);
	  }
	})
}


function cam_resetzoom() {
    $('#camerastatus').html("<div class=\"alert\">Resetting zoom...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=resetzoom',
	    type:           'GET',
	    cache:          false,
	    async:           true,
	    success: function(data) {
	    if (data.substring(0,6)=="ERROR:") {
	      data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
	      data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#camerastatus').html(data);
	  }
	})
}


function cam_prepare() {
    $('#camerastatus').html("<div class=\"alert\">Preparing cameras...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=prepare',
	    type:           'GET',
	    cache:          false,
	    async:           true,
	    success: function(data) {
	    if (data.substring(0,6)=="ERROR:") {
	      data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
	      data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#camerastatus').html(data);
	  }
	})
}

function cam_shoot(project,alsoget) {
    $('#camerastatus').html("<div class=\"alert\">Shooting...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=shoot&project='+project+'&alsoget='+alsoget,
	    type:           'GET',
	    cache:          false,
	    async:           true,
	    success: function(data) {
	    if (data.substring(0,6)=="ERROR:") {
	      data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
	      data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#camerastatus').html(data);
	  }
	})
}

function cam_get(project) {
    $('#camerastatus').html("<div class=\"alert\">Getting files...</div>");
      var ret = $.ajax({
	  url:            's2_scan_ajax.php?action=get&project='+project,
	    type:           'GET',
	    cache:          false,
	    async:           true,
	    success: function(data) {
	    if (data.substring(0,6)=="ERROR:") {
	      data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
	      data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#camerastatus').html(data);
	  }
	})
}

function help(str) {
    $("#help").html(str);
}

// unused ? 
function updateTypeAttributes() {
    $.ajax({
	url: 'attributeform.php?type='+$('#type').val(),
	success: function(data) {
            $('#typeattributes').html(data);
	}
    });
}

function rotate(value) {
    var rot;
    rot=parseInt($('#r').val(),10);
    if (isNaN(rot)) rot=0;
    rot+=value;
    $('#r').val(rot);
    $('#croppingimage').css("-moz-transform","rotate("+(rot/10)+"deg)");
    $('#croppingimage').css("-webkit-transform","rotate("+(rot/10)+"deg)");
    $('#croppingimage').css("transform","rotate("+(rot/10)+"deg)");
}
function clearrotate() {
    $('#r').val(0);
    rotate(0);
}



function iamlost() {
    $('#camerastatus').html("<div class=\"alert\">Resetting everything...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=resetall',
	    type:           'GET',
	    cache:          false,
	    async:           true,
	    success: function(data) {
	    if (data.substring(0,6)=="ERROR:") {
	      data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
	      data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#camerastatus').html(data);
	  }
	})
}
