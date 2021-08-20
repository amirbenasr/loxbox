
  
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

      <div class="header-label">Localiser un point relais :</div>
      <div class="results">
        <div class="list">
            <h2>Choisir le point relais le plus proche :</h2>
      <label class="col-sm-2" for="city">Ville :</label>
      <div class="col-sm-6 col-md-4">
        <select id="city" class="form-control">
        <option>Tous</option>
       
        </select> 
      </div>
        
         
               

         <br>
            <br>
            <button type="submit" class="btn pull-right" id="button" >
              Search
            </button>
           
       
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

   {else}
     <div class="alert alert-warning" role="alert" >
  <p class="alert-text">
    Le module LoxBox n'est pas activé. Pour plus d'informations contacter : <a href="mailto:contact@loxbox.tn">contact@loxbox.tn</>
  </p>
  </div>
    </div>
  {/if}

  </body>


</html>

 