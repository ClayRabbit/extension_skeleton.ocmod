<?
function input($name, $value, $type, $options) {
    if (is_array(@$options[$type])) { ?>
        <select name="<?= $name ?>" id="input-<?= $name ?>" class="form-control">
        <? foreach ($options[$type] as $key => $elem) {
            if ($key == $value) { ?>
                <option value="<?= $key ?>" selected="selected"><?= $elem ?></option>
            <? } else { ?>
                <option value="<?= $key ?>"><?= $elem ?></option>
            <? }
        } ?>
        </select>
    <? } else { ?>
        <input type="text" name="<?= $name ?>" value="<?= $value ?>" placeholder="<?= $type ?>" id="input-<?= $name ?>" class="form-control" />
    <? }
} ?>
<?= $header ?><?= $column_left ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-total" data-toggle="tooltip" title="<?= $button_save ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?= $cancel ?>" data-toggle="tooltip" title="<?= $button_cancel ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?= $heading_title ?></h1>
      <ul class="breadcrumb">
        <? foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?= $breadcrumb['href'] ?>"><?= $breadcrumb['text'] ?></a></li>
        <? } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <? if ($error_warning) { ?>
    <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?= $error_warning ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <? } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?= $text_edit ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?= $action ?>" method="post" enctype="multipart/form-data" id="form-total" class="form-horizontal">
          <? foreach ($extension_settings as $name => $setting) { ?>
            <? $id = $extension_id . '_' . $name; ?>
			<? if ($name == "list") { ?>
				<style>
					table#setting_list tr td:last-child button.btn-danger {
						display: none
					}
					table#setting_list tr:last-child td:last-child button.btn-danger {
						display: inline-block
					}
				</style>
	            <div>
				  <table id="setting_list" width="100%">
	              <? foreach ($setting as $key => $val) { ?>
                    <? if ($key == 0) { ?>
					  <tr>
						<? foreach ($val as $key2 => $val2) { ?>
						<th class="text-center" <?= $val2 =="sort_order" ? 'style="width:4%"' : '' ?>>
							<?= @$entry[$val2] ? $entry[$val2] : $val2 ?>
							<input type="hidden" name="<?= $id ?>[<?= $key ?>][<?= $key2 ?>]" value="<?= $val2 ?>" id="input-<?= $id ?>[<?= $key ?>][<?= $key2 ?>]" readonly/>
						</th>
						<? } ?>
						<th style="padding-left:5px">
<script>
function addRow2List() {
	var elem = $('table#setting_list tr.list').first().clone();
	elem.appendTo('table#setting_list');
	var count = $('table#setting_list tr.list').length - 1;
	$('table#setting_list tr:last-child td .form-control').each(function(i) {
		if ($(this).attr('id')) {
			$(this).attr('name', '<?= $id ?>[' + count + '][' + i + ']');
			$(this).attr('id', 'input-' + $(this).attr('name'));
			$(this).attr('value', '');
			$(this).prop('readonly', false);
		}
	});
	$('table#setting_list tr:last-child').show();
}
</script>
							<button class="btn btn-sm btn-success" onclick="addRow2List(); return false;"><i class="fa fa-plus-circle"></i></button>
						</th>
					  </tr>
					  <tr class="list" style="display: none;">
					<? } else { ?>
					  <tr class="list">
					<? } ?>
                    <? foreach (array_keys($setting[0]) as $key2) { ?>
	                  <td>
						<? if ($key == 0) { ?>
							<?= input('', '', $val[$key2], $setting_options); ?>
						<? } else { ?>
							<?= input($id . '[' . $key . '][' . $key2 . ']', $val[$key2], $setting[0][$key2], $setting_options); ?>
						<? } ?>
					  </td>
				    <? } ?>
					  <td style="padding-left:5px">
						<button class="btn btn-sm btn-danger" onclick="$(this).parent().parent().remove();"><i class="fa fa-minus-circle"></i></button>
					  </td>
					</tr>
				  <? } ?>
				  </table>
	            </div>
            <? } elseif (is_array($setting)) { ?>
              <? foreach ($setting as $key => $val) { ?>
                <? if (!is_array($val)) { ?>
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-<?= $id ?>[<?= $key ?>]"><? if ($entry[$key]) { ?><?= $entry[$key] ?><? } else { ?><?= $name ?>[<?= $key ?>]<? } ?></label>
                    <div class="col-sm-9">
					  <?= input($id . '[' . $key . ']', $val, $key, $setting_options); ?>
                    </div>
                  </div>
                <? } else { ?>
                  <? foreach ($val as $key2 => $val2) { ?>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-<?= $id ?>[<?= $key ?>][<?= $key2 ?>]"><?= $entry[$key2] ? $entry[$key2] : $name ?>[<?= $key ?>][<?= $key2 ?>]</label>
                      <div class="col-sm-9">
						<?= input($id . '[' . $key . '][' . $key2 . ']', $val2, $key2, $setting_options); ?>
                      </div>
                    </div>
                  <? } ?>
                <? } ?>
              <? } ?>
            <? } else { ?>
            <div class="form-group">
              <label class="col-sm-3 control-label" for="input-setting"><?= $entry[$name] ? $entry[$name] : $name ?></label>
              <div class="col-sm-9">
				<?= input($id, $setting, $name, $setting_options); ?>
              </div>
            </div>
            <? } ?>
          <? } ?>
        </form>
      </div>
    </div>
  </div>
</div>
<center><b><?= $extension_id ?></b> v<b><?= $extension_ver ?></b> by <a href="mailto:opencart@clayrabbit.ru">Andrey Chesnakov</a> © 2021 (<a target="_blank" href="https://oc.clayrabbit.ru/extensions/<?= $extension_id ?>/manual.htm">документация</a>)</center>
<?= $footer ?>
