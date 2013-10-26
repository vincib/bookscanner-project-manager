
function submit_crop(way,mode,project) {
    // 1: previous   2: next  3: all 
    $('#submitmsg').html("<div class=\"alert\">Submitting data...</div>");
    var ret = $.ajax({
	url:            's3_crop_ajax.php?action='+way
	    +'&left='+$('#relx1').val()
	    +'&right='+$('#relx2').val()
	    +'&top='+$('#rely1').val()
	    +'&bottom='+$('#rely2').val()
	    +'&project='+project
	    +'&picture='+$('#filename').val()
	    +'&mode='+mode,
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
	    $('#submitmsg').html(data);
	}
    })
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
}

function clickCanvas(e) {
    var parentOffset = $("#croppingcanvas").offset(); 
    var relX = e.pageX - parentOffset.left;
    var relY = e.pageY - parentOffset.top;
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

function cam_zoomin() {
    $('#zoomstatus').html("<div class=\"alert\">Zooming IN...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=zoomin',
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
	    $('#zoomstatus').html(data);
	  }
	})
}


function cam_zoomout() {
    $('#zoomstatus').html("<div class=\"alert\">Zooming OUT...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=zoomout',
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
	    $('#zoomstatus').html(data);
	  }
	})
}


function cam_resetzoom() {
    $('#zoomstatus').html("<div class=\"alert\">Resetting zoom...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=resetzoom',
	    type:           'GET',
	    cache:          false,
	    async:           true,
	    success: function(data) {
	    // TODO : if ERROR: tell it
	    if (data.substring(0,6)=="ERROR:") {
	      data="<div class=\"alert alert-error\">"+data+"</div>";
	    }
	    if (data.substring(0,3)=="OK:") {
	      data="<div class=\"alert alert-success\">"+data+"</div>";
	    }
	    $('#zoomstatus').html(data);
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


