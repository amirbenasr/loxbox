
$(document).on('ready loadMap','.loxbox-widget',function () {

  var container = L.DomUtil.get('map');
      if(container != null){

        container._leaflet_id = null;
      }
  var map = L.map("map", {
    center: [0, 0],
    zoom: 13,
  }).on("click", function () {
    unselectAll();
  });
  //unselect all active
  function unselectAll() {
    SELECTED = false;
    $("#selected-relay-valid").hide();
    $("li").each(function (index) {
      $(this).removeClass("loxbox-active");
    });
  }

  ///loading map
 

  //setting preconfigured popup
  map.on("popupopen", function (e) {
    var px = map.project(e.target._popup._latlng); // find the pixel location on the map where the popup anchor is
    px.y -= e.target._popup._container.clientHeight / 2; // find the height of the popup container, divide by 2, subtract from the Y axis of marker location
    map.panTo(map.unproject(px), { animate: true }); // pan to new center
  });
  var relays;
  var copyRelays;
  var location;

  //loading tile layer
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map);

  //getLocation function
  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        location = position;
        console.log(position.coords);
        map.setView([position.coords.latitude, position.coords.longitude], 10);
      });
    } else {
      div.innerHTML = "The Browser Does not Support Geolocation";
      

    }
  }
  ///get client position
  getLocation();
  ///event for formhandling
  $("#formSubmit").submit(function (e) {
    e.preventDefault();
  });

  ///event handler for dropdown

  $("#city").change(function (e) {
    // map.invalidateSize();
    // e.preventDefault();
    var city = this.value;
    // console.log(this.attributes)
    // alert(city);
    if (city === "Tous") {
      $("#myList > li").each(function () {
        $(this).show();
      });
      $('.panel').each(function (index, element) {
        // element == this
        $(this).show();
        
      });

      return;
    } else {
      $("#myList > li").each(function () {
        var element = relays.find((element) => element.Name === this.id);
        console.log(element);
        if (element.City.toLowerCase() === city.toLowerCase()) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
     
      $(".panel").each(function (element) {
     
        var element = relays.find((element) => element.Name === this.title);
        console.log(element);
        if (element.City.toLowerCase() === city.toLowerCase()) {
          $(this).show();
        } else {
          $(this).hide();
        }
        // alert(this.id);
        // var element = relays.find((element) => element.Name === this.id);
        // console.log(element);
        // if (element.City.toLowerCase() === city.toLowerCase()) {
        //   $(this).show();
        // } else {
        //   $(this).hide();
        // }
      });
    }
  });

  ///event handler for searchin city
  $("#cityInput").keyup(function (e) {
    e.preventDefault();

    // var list = relays.filter(element => element.City.startsWith(e.target.value));
    $("#myList > li").each(function () {
      var element = relays.find((element) => element.Name == $(this).text());
      if (
        element.City.toLowerCase().startsWith(
          e.target.attributes.id.toLowerCase()
        )
      ) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });
  ///event handler for zipcode
  $("#zipInput").keyup(function (e) {
    e.preventDefault();
    $("#myList > li").each(function () {
      var element = relays.find((element) => element.Name == $(this).text());
      console.log();

      if (element.Zipcode.toString().startsWith(e.target.value)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  ///call data
  function fetchData() {
    $.ajax({
      type: "GET",
      url: "https://www.loxbox.tn/api/Relaypoints/",

      data: "json",
      dataType: "json",
      headers: {
        Authorization: "Token " + Loxbox_TOKEN,
      },
      success: function (response) {
       
      
        $("#myList").empty();
        console.log(response);
        relays = response;
        copyRelays = relays;
        var distance = 0;
        var smallestDistance = -1;
        var chosenRelay;
        relays.forEach((element) => {
          function loadRelay(element) {
            console.log(element);
          }

          var collapsable = `
         
          <div  class="panel panel-default" title="${element.Name}">
          <div class="panel-heading" role="tab" id="headingTwo">
            <h4 class="panel-title">
              <a role="button"  data-toggle="collapse" data-parent="#myList" href="#${
                element.Name
              }" aria-expanded="false" aria-controls="collapseTwo" value="${element.Name}">
              ${element.Name}
              </a>
            </h4>
          </div>
          <div id="${
            element.Name
          }" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
            <div class="panel-body">
            ${getDiv(element)}
            </div>
          </div>
        </div>
    
        `;
          ///inject relays to myList
          if ($(window).width() <= 768) {
            $("#myList").append(
              collapsable
              // ` <li class="list-group-item" id="${element.Name}">${element.Name}</li>`
            );
          } else {
            $("#myList").append(
              ` <li class="list-group-item" id="${element.Name}" value="${element.Name}"><span style="font-weight:bold;">${element.Name} </span> <br>${element.City} ${element.Zipcode}<br>${element.Address}</li>`
            );
          }

          ///use CRS distance from leafs (result in meters)

          try {
            distance = map.distance(
              [element.latitude, element.Longitude],
              [location.coords.latitude, location.coords.longitude]
            );
          } catch (error) {
            distance = 0;
          }
          // alert("this is smallest Dsitance"+smallestDistance);
          // alert("this isthe biggest distance"+distance);

          if (smallestDistance < distance && smallestDistance) {
            // alert("we found another distance which is less than the actual distance");
            // alert(element.Name);
            // alert(distance);
            smallestDistance = distance;
            chosenRelay = element;
          }
          var popup = L.popup({ autoPan: true })
            .setLatLng([element.latitude, element.Longitude])
            .setContent(`${getDiv(element)}`);
      
          L.marker([element.latitude, element.Longitude], {
            icon: L.icon({
              iconUrl:
                "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png",
              shadowUrl:
                "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
              iconSize: [25, 41],
              iconAnchor: [12, 41],
              popupAnchor: [1, -34],
              shadowSize: [41, 41],
            }),
          })
            .addTo(map)
   
            .bindPopup(popup)
            .on("click", function () {
              unselectAll();
              $("#selected-relay-valid").show();
              $("li").each(function () {
                // console.log($(this)[0].id);
                if ($(this)[0].id == element.Name) {
                  $(this).addClass("loxbox-active");
                  $('#selectedRelay').text(element.Name);
                  SELECTED = true;
                }
              });
            });

        });
        // alert(smallestDistance);
        map.setView([chosenRelay.latitude, chosenRelay.Longitude], 10)
        

        let cities = [];

        relays.forEach((e) => cities.push(e.City));

        var u_cities = [...new Set(cities)];

        u_cities.forEach((e) => {
          $("#city").append(` <option>${e}</option>`);
        });

        // alert(chosenRelay.Name);
        $("li").on("click", function (element) {
          console.log(element.target.id);
          unselectAll();
          SELECTED = true;
          var relay = relays.find(
            (_element) => _element.Name == element.target.id
          );

          $("#selected-relay-valid").show();
          $("#selectedRelay").text(element.target.id);
          // $(selector).text(textString);

          //ajax call
          $.ajax({
            type: "POST",
            url: baseUri + "module/loxbox/task",
            data:
              "address1=" +
             relay.Name+','+ relay.Address +
              "&ajax=1" +
              "&City=" +
              relay.City +
              "&Zipcode=" +
              relay.Zipcode +
              "&Name=" +
              relay.Name,
            success: function () {
              console.log("sent success");
            },
          });
          L.popup()
            .setLatLng([relay.latitude, relay.Longitude])
            .setContent(`${getDiv(relay)}`)
            .openOn(map);
          // map.panTo([relay.latitude, relay.Longitude]);

          $(this).addClass("loxbox-active");
          var px = map.project(element.target._popup._latlng); // find the pixel location on the map where the popup anchor is
          px.y -= element.target._popup._container.clientHeight / 2; // find the height of the popup container, divide by 2, subtract from the Y axis of marker location
          map.panTo(map.unproject(px), { animate: true });
        });


        ///mobile selection
        // $(".collapsed").on("click", function (element) {
        //   alert(element.target.id);
        //   console.log(element.target.id);
        //   // unselectAll();
        //   SELECTED = true;
        //   var relay = relays.find(
        //     (_element) => _element.Name == element.target.id
        //   );

            
          
        //   // $(selector).text(textString);

        //   //ajax call
        //   $.ajax({
        //     type: "POST",
        //     url: baseUri + "module/loxbox/task",
        //     data:
        //       "address1=" +
        //       relay.Address +
        //       "&ajax=1" +
        //       "&City=" +
        //       relay.City +
        //       "&Zipcode=" +
        //       relay.Zipcode +
        //       "&Name=" +
        //       relay.Name,
        //     success: function () {
        //       console.log("sent success");
        //     },
        //   });
         

        // });
        ///set the map location to smallestDistance
      
        $('.panel-heading a').click(function(element) {
          // alert(element.target);
          var url = element.target.toString().substring(element.target.toString().indexOf('#')+1,element.length);
         url= url.replace('%C3%A9','é');

          // alert(url.length);
          const _relay = url;
          SELECTED = true;
          const relay = relays.find(
            (_element) => _element.Name == _relay
          );
          // alert(relays[0].City)
                     //ajax call
          $.ajax({
            type: "POST",
            url: baseUri + "module/loxbox/task",
            data:
              "address1=" +
             relay.Name +','+ relay.Address +
              "&ajax=1" +
              "&City=" +
              relay.City +
              "&Zipcode=" +
              relay.Zipcode +
              "&Name=" +
              relay.Name,
            success: function () {
              console.log("sent success");
            },
          });

          $('.panel-heading').removeClass('loxbox-active');
          //If the panel was open and would be closed by this click, do not active it
          if(!$(this).closest('.panel').find('.panel-collapse').hasClass('in'))
              $(this).parents('.panel-heading').addClass('loxbox-active');
          else{
            SELECTED=false;
          }
         
       });
     
      },
    });
  }

  fetchData();

  $(window).resize(function () {
    if ($(this).width() <= 768 && $(this).width() >= 730) {
      fetchData();
    } else if ($(this).width() >= 768 && $(this).width() <= 790) {
      fetchData();
    }
  });
});

function getDiv(element) {
  var chosenRelay=element;
  var html ="";
  if(element.WorkingHours!=null )
  {
    console.log("get timing array and append to tr");
    element.WorkingHours[0].timing.forEach((day)=> {
      if(day.isClosed==0)
      {
        if(day.hours.size==1)
        {
          html+= `<tr> <td> ${day.day} </td> <td> ${day.hours[0].open}</td> <td> ${day.hours[0].close}</td> </tr>`;

        }
        else {
        html+= `<tr> <td> ${day.day} </td> <td> ${day.hours[0].open} - ${day.hours[0].close} </td> <td>${day.hours[1].open} - ${day.hours[1].close} </td> </tr>`;

        }

      }
      else {
        html+= `<tr> <td> ${day.day} </td> <td style="text-align:center;"> - </td> <td style="text-align:center;"> - </td> </tr>`;

      }
    })
  }
  else {
    console.log("show - in all days");
    html+="Pas d'informations disponible"
  };
  return `<table class="table table-striped">
   <thead>
   <h4>${chosenRelay.Name} </h4>
   <h5>${chosenRelay.City+' '+chosenRelay.Zipcode}</h5> 
   <h5>${chosenRelay.Address}</h5>
   <span><a style="font-style:italic;font-size:16;text-decoration:underline" href="${chosenRelay.MapLocation}" target="_blank">google location</a> </span>
   </h4>
   </thead>

  <tbody>
  ${html}
   
 
  </tbody>
</table>`;
}
