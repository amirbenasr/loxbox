function loadHtml() {
  $(".relay-content").html(
    `<div class="header-label">Localiser un point relais :</div>
       <div class="results">
         <div class="list">
             <h2>Choisir le point relais le plus proche :</h2>
       <label class="col-sm-2" for="city">Ville :</label>
       <div class="col-sm-6 col-md-4 wrapper-customer">
         <select id="city" class="custom-drop" >
         <option>Tous</option>   
         </select> 
       </div>
  
          <br>
          <br>
             <br>
  
             <ul class="list-group" id="myList">
              
             </ul>
         </div>
         <div id="map"></div>
  
           <div class="alert alert-success" style="display:none" role="alert" id="selected-relay-valid">
   <p class="alert-text">Point relais sélectionné - <span id="selectedRelay"></span></p> 
   </div>  
     </div>
      
       </div>
     </div>
       `
  );
}

function loadMap() {
  var map = L.map("map", {
    center: [0, 0],
    zoom: 13,
  })
    .on("click", function () {
      mapSelect();
    })
    //center popup
    .on("popupopen", function (e) {
      var px = map.project(e.target._popup._latlng); // find the pixel location on the map where the popup anchor is
      px.y -= e.target._popup._container.clientHeight / 2; // find the height of the popup container, divide by 2, subtract from the Y axis of marker location
      map.panTo(map.unproject(px), { animate: true }); // pan to new center
    });
  //loading tile layer
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map);

  //we need map instance to create markups
  fetchData(map);
}

function loadPlugin() {
  loadHtml();
  loadMap();
}

function hidePlugin() {
  $(".relay-content").html("");
}

function fetchData(map) {
  $.ajax({
    type: "GET",
    url: "https://www.loxbox.tn/api/Relaypoints/",

    data: "json",
    dataType: "json",
    headers: {
      Authorization: "Token 7b23ca4c7d38ef2d6d0b0cf945b8f70599753a00",
    },
    success: function (response) {
      handleResponse(response, map);
    },
  });
}

function handleResponse(response, map) {
  var list = response;
  renderListToUl(list);
  renderMarkUp(list, map);
  setMapToFirstLocation(list[0], map);
  loadCitiesToDropDown(list);
  filterEvent(list);
  listClickEvent(list, map);
  panelEvent();
}

function getDiv(element) {
  var chosenRelay = element;
  var html = "";
  if (element.WorkingHours != null) {
    console.log("get timing array and append to tr");
    element.WorkingHours[0].timing.forEach((day) => {
      if (day.isClosed == 0) {
        if (day.hours.size == 1) {
          html += `<tr> <td> ${day.day} </td> <td> ${day.hours[0].open}</td> <td> ${day.hours[0].close}</td> </tr>`;
        } else {
          html += `<tr> <td> ${day.day} </td> <td> ${day.hours[0].open} - ${day.hours[0].close} </td> <td>${day.hours[1].open} - ${day.hours[1].close} </td> </tr>`;
        }
      } else {
        html += `<tr> <td> ${day.day} </td> <td style="text-align:center;"> - </td> <td style="text-align:center;"> - </td> </tr>`;
      }
    });
  } else {
    console.log("show - in all days");
    html += "Pas d'informations disponible";
  }
  return `<table class="table table-striped">
   <thead>
   <h4>${chosenRelay.Name} </h4>
   <h5>${chosenRelay.City + " " + chosenRelay.Zipcode}</h5> 
   <h5>${chosenRelay.Address}</h5>
   <span><a style="font-style:italic;font-size:16;text-decoration:underline" href="${
     chosenRelay.MapLocation
   }" target="_blank">google location</a> </span>
   </h4>
   </thead>

  <tbody>
  ${html}
   
 
  </tbody>
</table>`;
}
function renderListToUl(list) {
  var html;
  alert($(window).width());
  if ($(window).width() <= 768) {
    var collapsable = ` 
    <div  class="panel panel-default" title="${element.Name}">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title">
        <a role="button"  data-toggle="collapse" data-parent="#myList" href="#${
          element.Name
        }" aria-expanded="false" aria-controls="collapseTwo" value="${
      element.Name
    }">
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
    html += collapsable;
  } else {
    list.forEach((element) => {
      var template = `<li class="list-group-item" id="${element.Name}" value="${element.Name}"><span style="font-weight:bold;">${element.Name} </span> <br>${element.City} ${element.Zipcode}<br>${element.Address}</li>`;
      html += template;
    });
  }

  $("#myList").html(html);
}

function renderMarkUp(list, map) {
  list.forEach((element) => {
    //create popup
    var popup = L.popup({ autoPan: true })
      .setLatLng([element.latitude, element.Longitude])
      .setContent(`${popupContent(element)}`); //setContent of the popup
    //create marker
    L.marker([element.latitude, element.Longitude], {
      icon: L.icon({
        iconUrl:
          "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png",
        shadowUrl:
          "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
        iconSize: [25, 41],

        shadowSize: [41, 41],
      }),
    })
      .addTo(map) //add markers to map
      .bindPopup(popup, { maxWidth: "auto" })
      .on("click", function (e) {
        selectMarker(element);
      }); //bind poups to marker
  });
}

function popupContent(element) {
  var chosenRelay = element;
  var html = "";
  if (element.WorkingHours != null) {
    element.WorkingHours[0].timing.forEach((day) => {
      if (day.isClosed == 0) {
        if (day.hours.size == 1) {
          html += `<tr> <td class="text-nowrap"> ${day.day} </td> <td class="text-nowrap"> ${day.hours[0].open}</td> <td class="text-nowrap"> ${day.hours[0].close}</td> </tr>`;
        } else {
          html += `<tr> <td class="text-nowrap"> ${day.day} </td> <td class="text-nowrap"> ${day.hours[0].open} &#8209; ${day.hours[0].close} </td> <td class="text-nowrap">${day.hours[1].open} - ${day.hours[1].close} </td> </tr>`;
        }
      } else {
        html += `<tr> <td class="text-nowrap"> ${day.day} </td> <td style="text-align:center;"> - </td> <td style="text-align:center;"> - </td> </tr>`;
      }
    });
  } else {
    html += "Pas d'informations disponible";
  }
  return `<table class="table table-striped">
     <thead>
     <h4>${chosenRelay.Name} </h4>
     <h5>${chosenRelay.City + " " + chosenRelay.Zipcode}</h5> 
     <h5>${chosenRelay.Address}</h5>
     <span><a style="font-style:italic;font-size:16;text-decoration:underline" href="${
       chosenRelay.MapLocation
     }" target="_blank">google location</a> </span>
     </h4>
     </thead>
  
    <tbody>
    ${html}
     
   
    </tbody>
  </table>`;
}

function setMapToFirstLocation(element, map) {
  if (element != "undefined") {
    map.setView([element.latitude, element.Longitude], 10);
  }
}

function loadCitiesToDropDown(list) {
  var cities = [];
  list.forEach((e) => cities.push(e.City));

  var u_cities = [...new Set(cities)];

  u_cities.forEach((e) => {
    $("#city").append(` <option>${e}</option>`);
  });
}

function panelEvent() 
{
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

    $('.panel-heading').removeClass('active');
    //If the panel was open and would be closed by this click, do not active it
    if(!$(this).closest('.panel').find('.panel-collapse').hasClass('in'))
        $(this).parents('.panel-heading').addClass('active');
    else{
      SELECTED=false;
    }
   
 });
}

function filterEvent(list) {
  $("#city").change(function (e) {
    var city = this.value;
    if (city === "Tous") {
      $("#myList > li").each(function () {
        $(this).show();
      });
      $(".panel").each(function (index, element) {
        // element == this
        $(this).show();
      });

      return;
    } else {
      $("#myList > li").each(function () {
        var element = list.find((element) => element.Name === this.id);
        if (element.City.toLowerCase() === city.toLowerCase()) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    }
  });
}

function listClickEvent(list, map) {
  $("li").on("click", function (element) {
    resetList();
    $(this).addClass("active");
    $("#selected-relay-valid").show();
    $("#selectedRelay").text(element.target.id);
    var relay = list.find((_element) => _element.Name == element.target.id);
    showItemOnMap(relay, map);
  });
}

function resetList() {
  $("li").each(function (indexInArray, valueOfElement) {
    $(this).removeClass("active");
  });
}

function showItemOnMap(element, map) {
  L.popup()
    .setLatLng([element.latitude, element.Longitude])
    .setContent(`${popupContent(element)}`)
    .openOn(map);
}

function selectMarker(element) {
  resetList();
  $("#selected-relay-valid").show();
  $("li").each(function () {
    if ($(this)[0].id == element.Name) {
      $(this).addClass("active");
      $("#selectedRelay").text(element.Name);
    }
  });
}
function mapSelect() {
  resetList();
}
