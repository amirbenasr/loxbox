<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin="" />
{literal}
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>
{/literal}



<div class="loxbox-widget">
    {if {$valid}==200}

        <div class="relay-content">


        {else}
            <div class="alert alert-warning" role="alert">
                <p class="alert-text">
                    Le module LoxBox n'est pas activé. Pour plus d'informations contacter : <a
                        href="mailto:contact@loxbox.tn">contact@loxbox.tn</>
                </p>
            </div>

        </div>
    {/if}
