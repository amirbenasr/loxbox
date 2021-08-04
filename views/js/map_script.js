$(document).ready(function () {
  console.log("test");

  //unselect all active
  function unselectAll() {
    $("li").each(function (index) {
      $(this).removeClass("active");
    });
  }

  ///loading map
  var map = L.map("map", {
    center: [0, 0],
    zoom: 13,
  }).on("click", function () {
    unselectAll();
  });
  //setting preconfigured popup

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
        map.setView([position.coords.latitude, position.coords.longitude], 8);
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

  ///event handler for searchin city
  $("#cityInput").keyup(function (e) {
    e.preventDefault();

    // var list = relays.filter(element => element.City.startsWith(e.target.value));
    $("#myList > li").each(function () {
      var element = relays.find((element) => element.Name == $(this).text());
      console.log(element);
      if (element.City.toLowerCase().startsWith(e.target.value.toLowerCase())) {
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
  $.ajax({
    type: "GET",
    url: "https://www.loxbox.tn/api/Relaypoints/",

    data: "json",
    dataType: "json",
    headers: {
      Authorization: "Token 7b23ca4c7d38ef2d6d0b0cf945b8f70599753a00",
    },
    success: function (response) {
      console.log(response);
      relays = response;
      copyRelays = relays;
      var distance;
      var smallestDistance;
      var chosenRelay;
      relays.forEach((element) => {
        function loadRelay(element) {
          console.log(element);
        }
        ///inject relays to myList
        $("#myList").append(
          ` <li class="list-group-item" id="${element.Name}">${element.Name}</li>`
        );

        ///use CRS distance from leafs (result in meters)

        try {
          distance = map.distance(
            [element.latitude, element.Longitude],
            [location.coords.latitude, location.coords.longitude]
          );
        } catch (error) {
          distance = 0;
        }
        smallestDistance = distance;
        chosenRelay = element;
        if (smallestDistance > distance) {
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
            $("li").each(function () {
              // console.log($(this)[0].id);
              if ($(this)[0].id == element.Name) {
                $(this).addClass("active");
              }
            });
          });
      });

      $("li").on("click", function (element) {
        console.log(element.target.id);
        unselectAll();

        var relay = relays.find(
          (_element) => _element.Name == element.target.id
        );
        L.popup()
          .setLatLng([relay.latitude, relay.Longitude])
          .setContent(`${getDiv(relay)}`)
          .openOn(map);
        map.panTo([relay.latitude, relay.Longitude]);
        $(this).addClass("active");
      });

      ///set the map location to smallestDistance
      map.setView([chosenRelay.latitude, chosenRelay.Longitude], 10);

      //setting popup
      var popup = L.popup()
        .setLatLng([chosenRelay.latitude, chosenRelay.Longitude])
        .setContent(`${getDiv(chosenRelay)}`)
        .openOn(map);
    },
  });
});

var div = `<div class="leaflet-popup-content" style="width: 301px;"><div class="InfoWindow"><div class="PR-Name">KASA</div><div class="Tabs-Btns"><span class="Tabs-Btn Tabs-Btn-Selected" id="btn_011721_01" onclick="MR_jQuery('#Zone_Widget').trigger('TabSelect','011721_01');">Horaires</span><span class="Tabs-Btn" id="btn_011721_02" onclick="MR_jQuery('#Zone_Widget').trigger('TabSelect','011721_02');">Photo</span></div><div class="Tabs-Tabs"><div class="Tabs-Tab Tabs-Tab-Selected" id="tab_011721_01"><table class="PR-Hours" cellspacing="0" cellpadding="0" border="0"><tbody><tr><th>Monday</th><td>14h00-19h00</td><td>-</td></tr></tbody></table><table class="PR-Hours" cellspacing="0" cellpadding="0" border="0"><tbody><tr class="d"><th>Tuesday</th><td>10h00-19h00</td><td>-</td></tr></tbody></table><table class="PR-Hours" cellspacing="0" cellpadding="0" border="0"><tbody><tr><th>Wednesday</th><td>10h00-19h00</td><td>-</td></tr></tbody></table><table class="PR-Hours" cellspacing="0" cellpadding="0" border="0"><tbody><tr class="d"><th>Thursday</th><td>10h00-19h00</td><td>-</td></tr></tbody></table><table class="PR-Hours" cellspacing="0" cellpadding="0" border="0"><tbody><tr><th>Friday</th><td>10h00-19h00</td><td>-</td></tr></tbody></table><table class="PR-Hours" cellspacing="0" cellpadding="0" border="0"><tbody><tr class="d"><th>Saturday</th><td>10h00-19h00</td><td>-</td></tr></tbody></table><table class="PR-Hours" cellspacing="0" cellpadding="0" border="0"><tbody><tr><th>Sunday</th><td>10h00-19h00</td><td>-</td></tr></tbody></table></div><div class="Tabs-Tab" id="tab_011721_02"><img src="https://www.mondialrelay.com/ww2/img/dynamique/pr.aspx?id=FR011721" alt="No picture" width="182" height="112"></div></div></div></div>`;

function getDiv(chosenRelay) {
  return `<table class="table table-striped" >
   <thead>
   <h5>${chosenRelay.Name}</h5>
   </thead>
  <tbody>
    <tr>
      <td>Monday</td>
      <td>9:30 AM-9:30 PM</td>
      <td>-</td>
    </tr>
    <tr>
      <td>Tuesday</td>
      <td>9:30 AM-9:30 PM</td>
      <td>-</td>
    </tr>
    <tr>
    <td>Wednesday</td>
    <td>9:30 AM-9:30 PM</td>
    <td>-</td>
  </tr>
  <tr>
  <td>Thursday</td>
  <td>9:30 AM-9:30 PM</td>
  <td>-</td>
    </tr>
    <tr>
    <td>Friday</td>
    <td>9:30 AM-9:30 PM</td>
    <td>-</td>
    </tr>
    <tr>
   
 
  </tbody>
</table>`;
}
