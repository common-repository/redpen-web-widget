document.addEventListener("DOMContentLoaded", function() {
  function isGuid(value) {
    var regex = /[a-f0-9]{8}(?:-[a-f0-9]{4}){3}-[a-f0-9]{12}/i;
    var match = regex.exec(value);
    return match != null;
  }
  var inputValue = document.getElementById('webWidgetId').value;
  if (
    inputValue != undefined &&
    inputValue.trim().length >= 0 &&
    isGuid(inputValue)
  ) {
	document.getElementById('webWidgetId').disabled=true;
  }
});


            function changeTheColorOfRedpenConigurebtn() {
                if (document.getElementById("webWidgetId").value !== "") {
                    document.getElementById("rpconfigurebtn").style.background = "#f44336";
                } else {
                    document.getElementById("rpconfigurebtn").style.background = "#e0e0e0";
                }
            }
      
            document.getElementById("button").style.background = '#ffffff';