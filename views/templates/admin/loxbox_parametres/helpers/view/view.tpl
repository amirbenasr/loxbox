
<div  class="panel clearfix">
<div>
  <img  src="/prestashop/modules/loxbox/views/img/loxbox_hd_2.png" class="pull-left loxbox-img">
</div>
<div>
  <p>
LoxBox Services est une société qui transporte les colis de ses clients vers son réseau de points relais physiques et électroniques sur le territoire tunisien. <br>
  </p>

  <div class="hidden-xs">
    <p>
    Son objectif ultime est de satisfaire ses clients et veiller à ce qu’ils bénéficient d’un service irréprochable en termes de qualité : <br>
      {* <strong> La rentabilité </strong> : Low prices from 3.79Eur (excl. Tax) ! <br> *}
      <strong> La rapidité </strong> : LoxBox Services mettra tout en œuvre pour que les délais de livraisons soient respectés <br>
      <strong> La sûreté </strong> : LoxBox Services s’engage avec ses clients, les destinataires de ses clients ou toute autre personne à ce que tout se discute et se déroule dans les règles de l’art.
    </p>

    <p>
    LoxBox Services est toujours de bonne écoute, si vous avez des propositions ou des suggestions, nous vous invitons à nous envoyer un e-mail sur l’adresse suivante : contact@loxbox.tn <br>
    <a href="https://www.loxbox.tn/" target="_blank"> Decouvrir Loxbox</a>
    </p>
  </div>

  <div class="visible-xs">
    <p>
      Give your customers a cheap, easy, safe and convenient delivery offer.
    </p>

    <p>
      <a href="https://www.mondialrelay.fr/solutionspro/decouvrez-votre-offre/" target="_blank"> Discover Mondial Relay offers</a>
    </p>
  </div>
</div>

</div>
<form method="POST" id="formToken" name="formName">
<div class="panel">

    <div class="panel-heading"><h1>Authentification:</h1></div>

    <div class="alert alert-success" role="alert" style="display:none">
  <p class="alert-text">
    Votre code "Token" a été vérifié avec succès - <a href="https://loxbox.tn" target="blank">https://loxbox.tn</a>.
  </p>
</div> 
<div class="alert alert-warning" role="alert" style="display:none">
  <p class="alert-text">
    Le module LoxBox n'est pas activé. Pour plus d'informations contacter : <a href="mailto:contact@loxbox.tn">contact@loxbox.tn</a>
  </p>
</div>
        <label for="print" >{l s='Token:' mod='loxbox'}</label>
        <input type="text" placeholder="Saisir votre code d'activation" class="input-sm" name="ltoken" id="_ltoken" class="form-control" value={$LoxboxToken}>

    <div class="panel-footer">
        <button type="submit" name="saveToken" id="saveToken" class="btn btn-primary pull-right" >
            <i class="process-icon-save">

            </i>
            {l s="Save" mod="loxbox"}
        </button>
    </div>
</div>
    </form>