<?php
/**
 * Template Name: doneaza
 *
 * A custom page template without sidebar.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header();
?>
<script>
<!--
function formCheck(formobj){
	// Enter name of mandatory fields
	var fieldRequired = Array("email");
	// Enter field description to appear in the dialog box
	var fieldDescription = Array("E-mail");
	// dialog message
	var alertMsg = "Va rugam completati urmatoarele campuri :\n";
	
	var l_Msg = alertMsg.length;
	
	for (var i = 0; i < fieldRequired.length; i++){
		var obj = formobj.elements[fieldRequired[i]];
		if (obj){
			switch(obj.type){
			case "select-one":
				if (obj.selectedIndex == -1 || obj.options[obj.selectedIndex].text == ""){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			case "select-multiple":
				if (obj.selectedIndex == -1){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			case "text":
			case "textarea":
				if (obj.value == "" || obj.value == null){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			default:
			}
			if (obj.type == undefined){
				var blnchecked = false;
				for (var j = 0; j < obj.length; j++){
					if (obj[j].checked){
						blnchecked = true;
					}
				}
				if (!blnchecked){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
			}
		}
	}

	if (alertMsg.length == l_Msg){
		return true;
	}else{
		alert(alertMsg);
		return false;
	}
}
// -->
</script>


<form action="plateste.php" enctype="multipart/form-data" method="post" name="EuPlatesc" onsubmit="return formCheck(this);">
<table class="aligncenter" border="0" cellpadding="5" cellspacing="0" width="300">
<tbody>
<tr>
<td>Suma Donata</td>
<td><input name="amount" required="" type="text"><br>
<input class="inline" id="curr-ron" checked="checked" name="curr" value="RON" type="radio"> <label class="inline" for="curr-ron">LEI</label> <input class="inline" id="curr-eur" name="curr" value="EUR" type="radio"> <label class="inline" for="curr-eur">EURO</label> <input class="inline" id="curr-usd" name="curr" value="USD" type="radio"> <label class="inline" for="curr-usd">USD</label></td>
</tr>
<tr>
<td>Nume</td>
<td><input name="fname" required="" type="text"></td>
</tr>
<tr>
<td>Prenume</td>
<td><input name="lname" required="" type="text"></td>
</tr>
<tr>
<td>E-mail</td>
<td><input name="email" required="" type="text"></td>
</tr>
</tbody>
</table>
<p style="text-align: ;"><input class="button dark_blue" name="Doneazaa" value="Doneaza online" type="submit"></p>
</form>


<?php get_footer(); ?>