
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
