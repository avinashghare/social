<div class="row" style="padding:1% 0">
    <div class="col-md-12">
        <div class="pull-right">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                clientprovider Details
            </header>
            <div class="panel-body">
                <form class='form-horizontal tasi-form' method='post' action='<?php echo site_url("site/createclientprovidersubmit");?>' enctype='multipart/form-data'>
                    <div class="panel-body">
                        <div class=" form-group">
                            <label class="col-sm-2 control-label" for="normal-field">Client</label>
                            <div class="col-sm-4">
                                <?php echo form_dropdown( "client",$client,set_value( 'client'), "class='chzn-select form-control'");?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="normal-field">App Key</label>
                            <div class="col-sm-4">
                                <input type="text" id="normal-field" class="form-control" name="appkey" value='<?php echo set_value(' appkey ');?>'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="normal-field">Secret Key</label>
                            <div class="col-sm-4">
                                <input type="text" id="normal-field" class="form-control" name="secretkey" value='<?php echo set_value(' secretkey ');?>'>
                            </div>
                        </div>
                        <div class=" form-group">
                            <label class="col-sm-2 control-label" for="normal-field">Provider</label>
                            <div class="col-sm-4">
                                <?php echo form_dropdown( "provider",$provider,set_value( 'provider'), "class='chzn-select form-control'");?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="normal-field">&nbsp;</label>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="<?php echo site_url("site/viewclientprovider"); ?>" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                </form>
                </div>
        </section>
        </div>
    </div>
