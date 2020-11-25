<!DOCTYPE html>
<html>

<head>
  <title>Totem</title>
  <link rel="stylesheet" href="utiles/bootstrap.min.css">
  <link rel="stylesheet" href="utiles/texto.css">
  <script src="utiles/jquery.js" type="text/javascript"></script>
  <script src="utiles/bootstrap.min.js" type="text/javascript"></script>
  <script src="reloj.js" type="text/javascript"></script>
</head>

<body class="p-2 mt-1">

  <div class="row m-0">
    <div class="col-md-4">
      <img id="logo" src="utiles/logo.png" width="150" style="position: absolute; left: 0; padding: 10px;"></img>
    </div>
    <div class="col-md-4">
    </div>
    <div class="col-md-4">
      <img src="utiles/ht-logo.png" width="180" style="position: absolute; right: 0; padding: 10px;" class="mt-3"></img>
    </div>
  </div>
  <br> <br> <br> <br>
  <div class="row m-0 mt-5">
    <div id="car1" class="carousel-item active" style="border: 0;">
      <video id="mi-video" autoplay muted class="d-block w-100" style="border: 0;">
        <source src="utiles/video1.mp4" type="video/mp4">
      </video>
    </div>
  </div>
  <br>
  <br>
  <br>
  <table class="container mb-5" id="tabla" style="border-radius: 10%;">
    <tr>
      <td>
        <table>
          <tr>
            <td width=50% class="text-center pt-4">
              <div id="header" style="font-size: 300%;">Bienvenido!</div><br><br>
              <div style="font-size: 200%;">Aforo Actual:</div>
              <strong>
                <div id="div_total" style="font-size: 850%;">0</div>
              </strong>
              <div style="font-size: 200%;">Aforo Permitido:</div>
              <div id="div_max" style="font-size: 600%;">100</div><br>
              <strong>
                <div id="msg" style="font-size: 200%;"></div>
              </strong>
            </td>
            <td width=50%><img id="sign" src="utiles/go.png" width="85%"></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>
        <div id="marquee">
          <div id="scrolling"></div>
        </div><br>
        <div id="debug" style="font-size: 80%;"></div>
  </table>

  <footer class="page-footer font-small stylish-color-dark pt-4 mb-4" style="bottom: 0; position: fixed; width: 100%;" hidden>
    <div class="footer-copyright text-center py-3 bg-light mt-3">
      <img src="utiles/logo1.jpg" class="rounded-circle" width="40" height="34">
      <a href="https://www.checkseguro.com/">www.checkseguro.com </a>
      <img src="utiles/icono_instagram.jpg" class="rounded-circle" width="63" height="43">@check_seguro
      <img src="utiles/icono_facebook.jpg" class="rounded-circle" width="50" height="40">Check Seguro
    </div>
  </footer>


  <script>
    // initialization //////////////////////////////////////////
    updateData();

    // functions //////////////////////////////////////////
    function getTotal(ip, user, pass, counter, debug) {
      var xmlHttp = new XMLHttpRequest();
      xmlHttp.open("GET", "http://" + ip + "/stw-cgi/eventsources.cgi?msubmenu=peoplecount&action=check&Channel=0", false, user, pass); // false for synchronous request
      xmlHttp.withCredentials = true;
      xmlHttp.send(null);

      var str = xmlHttp.responseText;

      var in_key = "." + counter + ".InCount=";
      var out_key = "." + counter + ".OutCount=";
      var in_pos = str.indexOf(in_key) + in_key.length;
      var in_end = str.indexOf("\r", in_pos);
      var in_value = Number(str.slice(in_pos, in_end));
      var out_pos = str.indexOf(out_key) + out_key.length;
      var out_end = str.indexOf("\r", out_pos);
      var out_value = Number(str.slice(out_pos, out_end));
      var total = in_value - out_value;

      if (debug) {
        if (str.indexOf('401 - Unauthorized') >= 0)
          printDebug(' >> Connection or Login problem. Please first try to log-in to the camera(s) WebViewer page.<br>');
        else
          printDebug(" | Counter " + counter + ", In " + in_value + ", Out " + out_value + ", Total " + total);
      }
      return total;
    }

    function updateData() {
      var queryString = window.location.search;
      var urlParams = new URLSearchParams(queryString);

      var u = urlParams.get("u");
      var p = window.atob(urlParams.get("p"));
      var debug = urlParams.has("debug");

      if (debug)
        clearDebug();

      var max = 50;
      if (urlParams.has("max") && !isNaN(urlParams.get("max")))
        max = Number(urlParams.get("max"));

      var logo_file = "utiles/logo.png";
      if (urlParams.has("logo_file"))
        logo_file = "utiles/" + urlParams.get("logo_file");

      var logo_width = 150;
      if (urlParams.has("logo_width") && !isNaN(urlParams.get("logo_width")))
        logo_width = Number(urlParams.get("logo_width"));

      document.getElementById("logo").src = logo_file;
      document.getElementById("logo").width = logo_width;

      var total = 0;

      for (var i = 1; i <= 8; i++) {
        if (urlParams.has("ip" + i)) {
          var ip = urlParams.get("ip" + i);
          var c1 = urlParams.has("ip" + i + "_c1");
          var c2 = urlParams.has("ip" + i + "_c2");

          if (ip == "" || u == "" || p == "")
            continue;

          if (debug)
            printDebug("IP " + ip);

          if (c1)
            total += getTotal(ip, u, p, 1, debug);

          if (c2)
            total += getTotal(ip, u, p, 2, debug);

          if (debug && !isNaN(total))
            printDebug(" | <a onclick=\"resetCounters('" + ip + "', '" + u + "', '" + p + "')\" href=\"#\"><font size='-3' color='red'>RESET</font></a><br>");
        }
      }

      var correction = 0;
      if (urlParams.has("correction") && !isNaN(urlParams.get("correction")))
        correction = Number(urlParams.get("correction"));

      if (debug && !isNaN(total))
        printDebug("Correction: " + correction + "<br>");

      var header = "Bienvenido!";
      if (urlParams.has("header"))
        header = urlParams.get("header");

      document.getElementById("header").innerHTML = header;

      var msg_stop = "Por Favor Espere...!";
      if (urlParams.has("stop"))
        msg_stop = urlParams.get("stop");

      var msg_go = "Ud puede pasar!";
      if (urlParams.has("go"))
        msg_go = urlParams.get("go");

      var msg_scrolling = "";
      if (urlParams.has("scrolling"))
        msg_scrolling = urlParams.get("scrolling");

      total += correction;

      if (total < 0 && urlParams.has("negative") == false)
        total = 0;

      var refresh = 5;
      if (urlParams.has("refresh") && !isNaN(urlParams.get("refresh")))
        refresh = Number(urlParams.get("refresh"));

      if (isNaN(total)) {
        document.getElementById("div_total").innerHTML = "N/A";
        document.getElementById("div_max").innerHTML = "N/A";
        document.getElementById("sign").style.display = "none";
        setTimeout(updateData, refresh * 1000);
        return;
      }

      document.getElementById("div_total").innerHTML = total.toString();
      document.getElementById("div_max").innerHTML = max.toString();
      document.getElementById("sign").style.display = "";
      if (total >= max) {
        // document.body.style.backgroundColor = "#f0c0c0";
        document.getElementById("tabla").style.background = "#f0c0c0";
        // document.getElementById("marquee").style.background = "#f0c0c0";
        document.getElementById("div_total").style.color = "red";
        document.getElementById("msg").innerHTML = msg_stop;
        document.getElementById("msg").style.color = "red";
        document.getElementById("sign").src = "utiles/stop.png";
      } else {
        // document.body.style.backgroundColor = "#c0f0c0";
        document.getElementById("tabla").style.background = "#c0f0c0";
        // document.getElementById("marquee").style.background = "#c0f0c0";
        document.getElementById("div_total").style.color = "green";
        document.getElementById("msg").innerHTML = msg_go;
        document.getElementById("msg").style.color = "green";
        document.getElementById("sign").src = "utiles/go.png";
      }
      document.getElementById("scrolling").innerHTML = msg_scrolling;
      setTimeout(updateData, refresh * 1000);
    }

    function resetCounters(ip, user, pass) {
      var xmlHttp = new XMLHttpRequest();
      xmlHttp.open("GET", "http://" + ip + "/stw-cgi/system.cgi?msubmenu=databasereset&action=control&IncludeDataType=PeopleCount", false, user, pass); // false for synchronous request
      xmlHttp.withCredentials = true;
      xmlHttp.send(null);
      updateData();
    }

    function printDebug(text) {
      document.getElementById("debug").innerHTML = document.getElementById("debug").innerHTML + text;
    }

    function clearDebug() {
      document.getElementById("debug").innerHTML = "Debug Info: <a onclick=\"hideDebug()\" href=\"#\"><font size='-3' color='green'>[HIDE]</font></a><br>";
    }

    function hideDebug() {
      var url = window.location.href;
      url = url.replace('&debug=true', '').replace('&debug', '');
      window.location.href = url;
    }

    $("#mi-video").on('ended', function() {
      var $fuente = "";
      $('#mi-video').find('source').each(function() {
        $fuente = $(this).attr('src');
      });

      switch ($fuente) {
        case "utiles/video1.mp4":
          $('source', $('#mi-video')).attr('src', "utiles/video2.mp4");
          break;
        case "utiles/video2.mp4":
          $('source', $('#mi-video')).attr('src', "utiles/video3.mp4");
          break;
        case "utiles/video3.mp4":
          $('source', $('#mi-video')).attr('src', "utiles/video4.mp4");
          break;
        case "utiles/video4.mp4":
          $('source', $('#mi-video')).attr('src', "utiles/video1.mp4");
          break;
        default:
          $('source', $('#mi-video')).attr('src', "utiles/video1.mp4");
          break;
      }
      $('#mi-video')[0].load();
      $('#mi-video')[0].play();
    });
  </script>

</body>

<footer class="page-footer font-small stylish-color-dark pt-4 mb-4" style="bottom: 0; width: 100%;">

  <div class="footer-copyright text-center py-3 bg-light mt-3">
    <img src="utiles/logo1.jpg" class="rounded-circle" width="40" height="34">
    <a href="https://www.checkseguro.com/">www.checkseguro.com </a>
    <img src="utiles/icono_instagram.jpg" class="rounded-circle" width="63" height="43">@check_seguro
    <img src="utiles/icono_facebook.jpg" class="rounded-circle" width="50" height="40">Check Seguro

  </div>
</footer>

</html>