<html>
    <head>
        <title>PlexRequest</title>
        <meta content="PlexRequest" property="og:title" />
        <meta content="https://infinitycommunicationsgateway.net/plexreq/PlexReqLogoPvw.png" property="og:image" />
        <meta content="374" property="og:image:width"/>
        <meta content="200" property="og:image:height"/>
        <meta content="website" property="og:type" />
        <meta content="website" property="og:type" />
        <meta content="https://infinitycommunicationsgateway.net/plexreq/" property="og:url" />
        <script src="https://cdn.jsdelivr.net/npm/base64-js@1.3.0/base64js.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" />
        <style>
            body {
                -moz-user-select: none;
                -webkit-user-select: none;
                -ms-user-select:none;
                user-select:none;
                -o-user-select:none;
                font-family: "Helvetica Neue", Helvetica, Arial;
                font-size: 14px;
                line-height: 20px;
                font-weight: 400;
                color: #3b3b3b;
                -webkit-font-smoothing: antialiased;
                font-smoothing: antialiased;
                background: #2b2b2b;
            }
            @media screen and (max-width: 580px) {
                body {
                    font-size: 16px;
                    line-height: 22px;
                }
            }

            .PlexReqLogo {
                background-image: url(PlexReqLogo.png);
                width: 251px;
                height: 134px;
                margin: 18px;
            }

            .wrapper {
                margin: 0 auto;
                padding: 16px;
                max-width: 800px;
            }

            .searchwrap {
                margin: 0 auto;
                padding: 16px;
                max-width: 800px;
            }

            .table {
                margin: 10px 0px 40px 0px;
                width: 100%;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
                display: table;
            }
            @media screen and (max-width: 580px) {
                .table {
                    display: block;
                }
            }


            .searchtable {
                width: 100%;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
                display: table;
                z-index:1000;
            }
            @media screen and (max-width: 580px) {
                .searchtable {
                    display: block;
                }
            }

            .searchrow{
                transition: all 0.1s ease-in-out;
            }
            .searchrow:hover{
                background: #1fa6ff4f !important;
            }

            .row {
                display: table-row;
                background: rgb(76, 75, 75);
                color: rgba(255,255,255,1);
            }

            .row:nth-of-type(odd) {
                background: #3a3939;
            }

            .row.header {
                font-weight: 900;
                color: #ffffff;
                background: #2980b9;
            }

            .row.header div:first-child{
                border-radius:10px 0px 0px 0px;
            }
            .row.header div:last-child{
                border-radius:0px 10px 0px 0px;
            }
            .table .row:last-child .cell:first-child{
                /* border-radius:0px 0px 0px 10px; */
            }
            .table .row:last-child div:last-child{
                /* border-radius:0px 0px 10px 0px; */
            }
            .searchtable .row:last-child .cell:first-child{
                border-radius:0px 0px 0px 10px;
            }
            .searchtable .row:last-child div:last-child{
                border-radius:0px 0px 10px 0px;
            }
            @media screen and (max-width: 580px) {
                .row {
                    padding: 14px 0 7px;
                    display: block;
                }
                .row.header {
                    padding: 0;
                    height: 6px;
                }
                .row.header .cell {
                    display: none;
                }
                .row .cell {
                    margin-bottom: 10px;
                }
                .row .cell:before {
                    margin-bottom: 3px;
                    content: attr(data-title);
                    min-width: 98px;
                    font-size: 10px;
                    line-height: 10px;
                    font-weight: bold;
                    text-transform: uppercase;
                    color: #969696;
                    display: block;
                }
            }

            .cell {
                padding: 6px 12px;
                display: table-cell;
                vertical-align: middle;
            }
            @media screen and (max-width: 580px) {
                .cell {
                    padding: 2px 16px;
                    display: block;
                }
            }
            .form-control {
                display: block;
                width: 100%;
                height: 38px;
                padding: 8px 12px;
                font-size: 14px;
                line-height: 1.42857143;
                color: #272b30;
                background-color: #ffffff;
                background-image: none;
                border: 1px solid #cccccc;
                border-radius: 4px;
                -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075), 0px 0px 6px 1px rgba(255,255,255,0.6);
                box-shadow: inset 0 1px 1px rgba(0,0,0,0.075), 0px 0px 6px 1px rgba(255,255,255,0.6);
                -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
                -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
                transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            }
            .loader {
                display: block;
                width: 100%;
                height: 38px;
                padding: 8px 12px;
                line-height: 1.42857143;
                background-image: none;
            }
            .search-no-results{
                border-radius:10px;
            }
            .search-with-results{
                border-radius:10px 10px 0px 0px;
            }

            .spinner {
                -webkit-animation: rotate 2s linear infinite;
                animation: rotate 2s linear infinite;
                z-index: 2;
                position: absolute;
                top: 28px;
                right: 8px;
                margin: -11px 0 0 -11px;
                width: 22px;
                height: 22px;
            }
            .spinner .path {
                stroke: #93bfec;
                stroke-linecap: round;
                -webkit-animation: dash 1.5s ease-in-out infinite;
                animation: dash 1.5s ease-in-out infinite;
            }

            @-webkit-keyframes rotate {
                100% {
                    -webkit-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }

            @keyframes rotate {
                100% {
                    -webkit-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }
            @-webkit-keyframes dash {
                0% {
                    stroke-dasharray: 1, 150;
                    stroke-dashoffset: 0;
                }
                50% {
                    stroke-dasharray: 90, 150;
                    stroke-dashoffset: -35;
                }
                100% {
                    stroke-dasharray: 90, 150;
                    stroke-dashoffset: -124;
                }
            }
            @keyframes dash {
                0% {
                    stroke-dasharray: 1, 150;
                    stroke-dashoffset: 0;
                }
                50% {
                    stroke-dasharray: 90, 150;
                    stroke-dashoffset: -35;
                }
                100% {
                    stroke-dasharray: 90, 150;
                    stroke-dashoffset: -124;
                }
            }

.reqmov-btn {
  position:absolute;
  padding:7px;
  border-radius:6px;
  transition: all 0.1s ease-in-out;
}

.reqmov-sub {
  background-color:rgb(116, 189, 50);
  left:200px;
  bottom:30px;
}
.reqmov-sub:hover {
  background-color: rgb(128, 202, 62);
}
.reqmov-sub:active {
  background-color: rgb(122, 193, 59);
}
.reqmov-cxc {
  left:130px;
  bottom:30px;
  background-color:rgb(189, 50, 50);
}
.reqmov-cxc:hover {
  background-color:rgb(189, 69, 69);
}
.reqmov-cxc:active {
  background-color:rgb(189, 50, 50);
}

.reqmov-title {
  position:absolute;
  left:170px;
  top:45px;
  text-shadow: 0px 0px 4px rgba(255,255,255,0.8);
  font-size: 16pt;
}

.reqmov-date {
  position:absolute;
  left:170px;
  top:65px;
  text-shadow: 0px 0px 4px rgba(255,255,255,0.8);
  font-size: 10pt;
}

.reqmov-poster {
  position:absolute;
  top:0px;
  left:0px;
  margin:20px;
  background-image:url("");
}

.poster {
  width:92px;
  height:138px;
  box-shadow: inset 1px 0px 7px 1px rgba(255,255,255,0.5), 4px 4px 6px rgba(0,0,0,1), inset 0px 0px 3px 2px rgb(0,0,0);
  border-radius:6px !important;
    position: absolute;
    left: 8px;
    top: 8px;

}
.reqmovPrompt {
  display:none;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  color:white;
  z-index:10;
  position: absolute;
  width:350px;
  height:185px;
  background:rgb(76, 75, 75);
  border-radius:10px;
  box-shadow: 0px 0px 11px 2px rgba(255,255,255,0.6);
}
.pop-overlay {
    display:none;
    position:absolute;
    top:0px;
    left:0px;
    z-index:8;
    background-color:rgba(0,0,0,0.8);
    width:100%;
    height:100%;
}

.poster-box-wrapper {
    background-color: rgba(255,255,255,0.02);
    position: absolute;
    left: 7px;
    top: 6px;
    height: 141px;
    width: 11px;
    border-radius: 6px 0px 0px 6px;
    box-shadow: inset 0px 0px 1px 1px rgba(255, 255, 255, 0.2);
}

.poster-box-edge{
    position: absolute;
    right: 7px;
    height: 141px;
    width: 83px;
    border-radius: 0px 6px 6px 0px;
    box-shadow: inset 0px 0px 1px 1px rgba(255, 255, 255, 0.1);
    top: 6px;

}
.poster-wrapper {
    position:relative;
}
.poster-fallback {
    z-index: 0;
    font-size: 16px;
    margin: 0px;
    text-align: center;
    position: absolute;
    left: 20px;
    top: 46px;
    height: 61px;
    padding: 0px;
    width: 73px;
    text-shadow: 0px 0px 15px rgb(255,255,255);
}
</style>
    </head>
    <body>
        <div class="PlexReqLogo"></div>
        <div id="popOverlay" class="pop-overlay"></div>
        <div id="reqPrompt" class="reqmovPrompt">
            <div id="reqPoster" class="reqmov-poster poster"></div>
            <div id="reqTitle" class="reqmov-title">NaN</div>
            <div id="reqDate" class="reqmov-date">Reloased: NaN</div>
            <div id="reqCancel" class="reqmov-btn reqmov-cxc">Cancel</div>
            <div id="reqSubmit" data-tmdbid="" class="reqmov-btn reqmov-sub">Sumbit Request</div>
        </div>
        <div class="wrapper">
            <span style="color: rgba(255,255,255,1);font-size: 22px;padding:8px;">Something Missing?</span>
            <div class="form-group" style="position: relative;">
                <div id="search-spinner" class="loader" style="display:none;padding: 0px;z-index: 2;position:absolute;width: 100%;/* right: 0px; */">
                    <svg class="spinner" viewBox="0 0 50 50">
                        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                    </svg>
                </div>
                <div style="z-index: 1;position:absolute;width: 100%;">
                    <input id="movSearch" style="background: #4c4b4b;color: white;outline: none;margin-top: 10px;" type="text" value="" placeholder="Search here for something new!" class="form-control search-no-results">
                    <div class="searchtable" id="movResults" style="display:none;"></div>
                    <div class="wrapper2" style="margin-top: 19px;">
                        <span style="color: rgba(255,255,255,1);font-size: 22px;padding:8px;">Active Requests</span>
                        <div class="table">
                            <div class="row header">
                                <div class="cell">Poster</div>
                                <div class="cell">Name</div>
                                <div class="cell">Year</div>
                                <div class="cell">Request Date</div>
                                <div class="cell">Status</div>
                            </div>
                            <div class="row">
                                <div class="cell poster-wrapper" style='width:92px;height:159px;padding:8px;'><div class="poster-fallback">Image Not Available</div><div class="poster" style='background-image: url("test.jpg");padding:0px;margin: 0px 0px 4px 0px;'></div><div class="poster-box-wrapper"></div><div class="poster-box-edge"></div></div>
                                <div class="cell" data-title="Name">Halloween</div>
                                <div class="cell" data-title="Year">2018</div>
                                <div class="cell" data-title="Date">2018.01.01@12:00a EST</div>
                                <div class="cell" data-title="Status">Queued</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function submitReq(obj){
                //Queue new Movie
                console.log("test");
            }
            function getQueue(){
              //Retrieve Movie Queue
            }
            /*$(document).on('click','.searchrow',function(obj){
                console.log(obj);
                /*$("#reqPoster").css('background-image', 'url(//image.tmdb.org/t/p/w92/'+item.poster_path+')');
                $("#reqTitle").html(item.title);
                $("#reqDate").html("Released:"+item.release_date);
                $("#reqSubmit").attr("data-obj",Base64Encode(JSON.stringify(item)));//.data("tmdb-id","bah!");
                //document.getElementById("reqSubmit").setAttribute("reqdata",Stringify(item));
                $("#popOverlay").show();
                $("#reqPrompt").show();
             });*/
            $("#movSearch").on("change keyup paste", function(event){
                event.stopPropagation();
                if ($(this).val()==""){
                    $("#movSearch").removeClass("search-with-results");
                    $("#movSearch").removeClass("search-no-results");
                    $("#movSearch").addClass("search-no-results");
                    $("#movResults").css("display", "none");
                } else {
                    $("#search-spinner").css("display","block");
                    $.getJSON("//api.themoviedb.org/3/search/movie?",{
                        api_key: "ccdbe335923ce7f6edf53d7db06562b2",
                        language: "en-US",
                        query: $(this).val(),
                        page: "1",
                        include_adult: "false"
                    },function(data) {
                      //console.log(data);
                      $("#movResults").empty();
                      //$("#results").append("Results for <b>" + q + "</b>");
                      if (data.total_results>0){
                          $("#movSearch").removeClass("search-no-results");
                          $("#movSearch").addClass("search-with-results");
                          $("#movResults").css("display", "table");
                      } else {
                          $("#movSearch").removeClass("search-with-results");
                          $("#movSearch").addClass("search-no-results");
                          $("#movResults").css("display", "none");

                      }
                      var results = document.getElementById("movResults")
                      $.each(data.results, function(i,item){
                          var row= $("<div id='tmdbid-'"+item.id+"' class='row searchrow'><div class='cell poster-wrapper' style='width:92px;height:159px;padding:8px;'><div class='poster-fallback'>Image Not Available</div><div class='poster' style='background-image: url(//image.tmdb.org/t/p/w92/"+item.poster_path+");padding:0px;margin: 0px 0px 4px 0px;'></div><div class='poster-box-wrapper'></div><div class='poster-box-edge'></div></div><div class='cell' data-title='Name'>"+item.title+" ("+item.release_date.substring(0, 4)+")</div></div>");
                          row.attr("data-obj",Base64Encode(JSON.stringify(item)));
                          row.on('click',submitReq);
                          $("#movResults").append(row);
                      });
                    })
                    .always(function(){$("#search-spinner").css("display","none")});
                }
            });
            function Base64Encode(str, encoding = 'utf-8') {
                var bytes = new (TextEncoder || TextEncoderLite)(encoding).encode(str);
                return base64js.fromByteArray(bytes);
            }

            function Base64Decode(str, encoding = 'utf-8') {
                var bytes = base64js.toByteArray(str);
                return new (TextDecoder || TextDecoderLite)(encoding).decode(bytes);
            }
            $("#reqSubmit").click(function(){
                console.log($("#reqSubmit").attr("data-obj"));
                resetReqPopup();
            });
            $("#reqCancel").click(function(){
                resetReqPopup();
            });
            function resetReqPopup(){
                $("#reqPrompt").hide();
                $("#popOverlay").hide();
                $("#reqPoster").css('background-image', 'url("")');
                $("#reqTitle").html("");
                $("#reqDate").html("");
                $("#reqSubmit").attr("data-obj","");
            };
            //$("#movResults").on('click',submitReq);
        </script>
    </body>
</html>
