

function cam_search() {
    $('#camerastatus').html("<div class=\"alert\">Searching for cameras...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=search',
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


function cam_zoomout() {
    $('#zoomstatus').html("<div class=\"alert\">Zooming OUT...</div>");
      var ret = $.ajax({
	url:            's2_scan_ajax.php?action=zoomout',
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

function updateTypeAttributes() {
    $.ajax({
	url: 'attributeform.php?type='+$('#type').val(),
	success: function(data) {
            $('#typeattributes').html(data);
	}
    });
}


/* update the <select> list with objects of type 'type' */
function searchObjectType(type,list) {
    $.ajax({
	url: 'objectsfromtype.php?type='+type,
	success: function(data) {
            $('#'+list).html(data);
	}
    });
}


/* update the <select> list with objects the correspond to linktype 'ltype' with an other object of type 'otype' */
function searchObjectLinkType(ltype,otype,list) {
    $.ajax({
	url: 'objectsfromtype.php?otype='+otype+'&ltype='+ltype,
	success: function(data) {
            $('#'+list).html(data);
	}
    });
}


/* update the <select> list with linkstypes of type 'type' */
function searchLinksType(type,list) {
    $.ajax({
	url: 'linkstypefromtype.php?type='+type,
	success: function(data) {
            $('#'+list).html(data);
	}
    });
}

// Show one object's subobject (object=source, subobject=destination)
function explode(source,destination) {
    $.ajax({
        url: 'oneobject.php?id='+destination+'&exclude='+source,
        success: function(data) {
            $('#obj_'+source+'_'+destination).html(data);
        }
    });
}
