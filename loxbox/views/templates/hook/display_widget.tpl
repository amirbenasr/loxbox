
  
  <html lang="en">
  <head>
   

    <!-- Optional theme -->
  
 

    <!-- Latest compiled and minified JavaScript -->
  
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
      integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
      crossorigin=""
    />
    <script
      src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
      integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
      crossorigin=""
    ></script>

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Loxbox</title>
    <!-- Latest compiled and minified CSS -->
  </head>
  <body>
    <div class="parent">
   {if {$valid}==200}

    <div class="relay-content">

      <div class="header-label">Select your Loxbox relay point</div>
      <div class="results">
        <div class="list">
            <h2>Loxbox</h2>
            <p>
              Type something in the input field to search the list for specific
              items:
            </p>
            <label for="">city name :</label>
            <input
              class="form-control"
              id="cityInput"
              type="text"
              name="city"
              placeholder="Search.."
            />
            
            <label for="">zip code :</label>

            <input
              class="form-control"
              id="zipInput"
              type="text"
              name="pc"
              placeholder="Search.."
            />
            <button type="submit" class="btn pull-right" id="button" >
              Search
            </button>
           
       
            <ul class="list-group" id="myList">
             
            </ul>
        </div>
        <div id="map"></div>

          
    </div>
       <div class="alert alert-success" style="display:none" role="alert" id="selected-relay-valid">
  <p class="alert-text">Relay Point Selected</p>
  </div>
      </div>
    </div>

   {else}
     <div class="alert alert-warning" role="alert" >
  <p class="alert-text">
    Loxbox is not activated. Therefore you cannot see relayPoints
  </p>
  </div>
    </div>
  {/if}

  </body>


</html>

 