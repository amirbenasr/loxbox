    <form method="POST" id="formToken" name="formName">
<div class="panel">

    <div class="panel-heading"><h1>Loxbox configruation</h1></div>

    <div class="alert alert-success" role="alert" style="display:none">
  <p class="alert-text">
    Your loxbox token is verified by - <a href="#">https://loxbox.tn</a>.
  </p>
</div>
<div class="alert alert-warning" role="alert" style="display:none">
  <p class="alert-text">
    Your loxbox token is not valid for assistance check - <a href="#">https://loxbox.tn</a>.
  </p>
</div>
        <label for="print" >{l s='Token:' mod='loxbox'}</label>
        <input type="text" placeholder="type your user token" class="input-sm" name="ltoken" id="_ltoken" class="form-control" value={$LoxboxToken}>

    <div class="panel-footer">
        <button type="submit" name="saveToken" id="saveToken" class="btn btn-primary pull-right" >
            <i class="process-icon-save">

            </i>
            {l s="Save" mod="loxbox"}
        </button>
    </div>
</div>
    </form>