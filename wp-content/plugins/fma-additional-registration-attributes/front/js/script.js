jQuery(function($){
	var myArray = $("#customer_details .col-1 p");
	var count = 0;

	myArray.sort(function (a, b) {
    
    // convert to integers from strings
    a = parseInt($(a).attr("id"), 10);
    b = parseInt($(b).attr("id"), 10);
    count += 2;
    // compare
    if(a > b) {
        return 1;
    } else if(a < b) {
        return -1;
    } else {
        return 0;
    }
});

$("#customer_details .col-1").append(myArray);

var a = $('#ship-to-different-address-checkbox').prop('checked');
$('input[name="ship_to_different_address"]').click(function(){

    if($(this).is(":checked")){

    		var myArray2 = $("#customer_details .col-2 .shipping_address p");
			var count2 = 0;

			myArray2.sort(function (a2, b2) {
		    
		    // convert to integers from strings
		    a2 = parseInt($(a2).attr("id"), 10);
		    b2 = parseInt($(b2).attr("id"), 10);
		    count2 += 2;
		    // compare
		    if(a2 > b2) {
		        return 1;
		    } else if(a2 < b2) {
		        return -1;
		    } else {
		        return 0;
		    }
		});

$("#customer_details .col-2 .shipping_address").append(myArray2);

    }
    else if($(this).is(":not(:checked)")) {
    }

});

if(a!=false)
{
	var myArray3 = $("#customer_details .col-2 .shipping_address p");
	var count3 = 0;

	myArray3.sort(function (a3, b3) {
    
    // convert to integers from strings
    a3 = parseInt($(a3).attr("id"), 10);
    b3 = parseInt($(b3).attr("id"), 10);
    count3 += 2;
    // compare
    if(a3 > b3) {
        return 1;
    } else if(a3 < b3) {
        return -1;
    } else {
        return 0;
    }
});

$("#customer_details .col-2 .shipping_address").append(myArray3);
}


});
jQuery(document).ready(function(){
    jQuery('.register').attr('enctype','multipart/form-data');
});





