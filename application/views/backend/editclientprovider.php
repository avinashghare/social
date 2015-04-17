<section class="panel">
<header class="panel-heading">
clientprovider Details
</header>
<div class="panel-body">
<form class='form-horizontal tasi-form' method='post' action='<?php echo site_url("site/editclientprovidersubmit");?>' enctype= 'multipart/form-data'>
<input type="hidden" id="normal-field" class="form-control" name="id" value="<?php echo set_value('id',$before->id);?>" style="display:none;">
<div class=" form-group">
<label class="col-sm-2 control-label" for="normal-field">Client</label>
<div class="col-sm-4">
<?php echo form_dropdown("client",$client,set_value('client',$before->client),"class='chzn-select form-control'");?>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label" for="normal-field">App Key</label>
<div class="col-sm-4">
<input type="text" id="normal-field" class="form-control" name="appkey" value='<?php echo set_value('appkey',$before->appkey);?>'>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label" for="normal-field">Secret Key</label>
<div class="col-sm-4">
<input type="text" id="normal-field" class="form-control" name="secretkey" value='<?php echo set_value('secretkey',$before->secretkey);?>'>
</div>
</div>
<div class=" form-group">
<label class="col-sm-2 control-label" for="normal-field">Provider</label>
<div class="col-sm-4">
<?php echo form_dropdown("provider",$provider,set_value('provider',$before->provider),"class='chzn-select form-control'");?>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label" for="normal-field">&nbsp;</label>
<div class="col-sm-4">
<button type="submit" class="btn btn-primary">Save</button>
<a href='<?php echo site_url("site/viewpage"); ?>' class='btn btn-secondary'>Cancel</a>
</div>
</div>
</form>
</div>
</section>
