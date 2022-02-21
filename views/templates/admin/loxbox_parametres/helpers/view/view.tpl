<form method="POST" id="formToken" name="formName">
<div class="panel">

    <div class="panel-heading"><h1>Loxbox configruation</h1></div>

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