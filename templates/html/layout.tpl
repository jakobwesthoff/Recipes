{use $model, $root = $model->request->root}
<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html 
	xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="{$model->mimetype}; charset={$model->charset}" />

    <link rel="Stylesheet" type="text/css" href="{$root}/styles/screen.css" media="screen" />
    <script src="{$root}/scripts/form.js" type="text/ecmascript"></script>

    <title>Receipt Manager</title>
</head>
<body class="dashboard">
	<h1>
		<a href="{$root}/">Receipt Manager</a>
	</h1>

	<div class="main">
        {raw arbit_decorate($model->view)}
	</div>

	<div class="footer">
	</div>

	{if $model->debugMode}
		{include arbit_get_template( 'html/debug.tpl' )}
	{/if}
</body>
</html>
