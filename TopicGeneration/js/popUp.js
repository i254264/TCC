document.addEventListener("DOMContentLoaded", function() {
    const $ = document.querySelector.bind(document);
    $("#help_button").addEventListener("click", evt => {
        $("header").classList.remove("blur-out");
        $("#main").classList.remove("blur-out");
        $("header").classList.add("blur-in");
        $("#main").classList.add("blur-in");
        fadeIn();
    });

    $(".box-popUp button").addEventListener("click", evt => {
        $("header").classList.remove("blur-in");
        $("#main").classList.remove("blur-in");
        $("header").classList.add("blur-out");
        $("#main").classList.add("blur-out");
        fadeOut();
    });

    function fadeOut() {
        var opacity = 1;
        var interval = setInterval(function () {
          if (opacity <= 0.1) {
            clearInterval(interval);
            $(".pop-up").style.display = 'none';
          }
          $(".pop-up").style.opacity = opacity;
          opacity -= opacity * 0.1;
        }, 20);
      }
      function fadeIn() {
        $(".pop-up").style.opacity = '0';
        $(".pop-up").style.display = 'block';
        var opacity = 0;
        var interval = setInterval(function () {
          if (opacity >= 1) {
            clearInterval(interval);
          }
          $(".pop-up").style.opacity = opacity;
          opacity += 0.1;
        }, 30);
      }
});