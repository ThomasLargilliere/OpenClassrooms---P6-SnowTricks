$("#mediaMobile").css('display', 'none');
$("#btnShowTrick").on("click", function(e){
    e.preventDefault();
    var info = $("#mediaMobile").css('display');
    if (info == "block"){
        $("#mediaMobile").css('display', 'none');
        $("#btnShowTrick").text("Afficher les médias");
    } else {
        $("#mediaMobile").css('display', 'block');
        $("#btnShowTrick").text("Cacher les médias");
    }
})