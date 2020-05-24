$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");

});


document.getElementById("menu-toggle").onclick = function () {
    if (document.getElementById("menu-toggle").innerHTML == "M") {
        document.getElementById("menu-toggle").innerHTML = "O"
    } else {
        document.getElementById("menu-toggle").innerHTML = "M"
    }

}