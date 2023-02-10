
function socket(){

    var http = new XMLHttpRequest();
    http.open("POST", "socket.php", true);
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            alert(this.responseText);
            if(this.responseText == 'ACTIVO'){
                llamados();
            };
        }
    };
    http.send();
}

function llamados(){
    var http = new XMLHttpRequest();
    http.open("POST", "llamados.php", true);
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('escena').innerHTML = this.responseText;
        }
    };
    http.send();
}
