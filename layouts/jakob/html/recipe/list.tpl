{use $model, $root = $model->request->root}
{var $ingredientList = array()}
{tr_context "recipes"}
<ul class="commands">
	<li><a href="#" id="clear-list">{tr "Clear List"}</a></li>
	<script type="text/javascript">
    // <![CDATA[ {literal}
    $( document ).ready( function() {
		$( "#clear-list" ).bind( "click", function(e) {
			e.preventDefault();
			e.stopPropagation();
			$('td.amount input').val(0);
			$('input[type="submit"]').click();
		});
    });
    {/literal} // ]]>
    </script>
	<li><a href="{$root}/recipes/tags">{tr "Tagindex"}</a></li>
	<li><a href="{$root}/recipes/all">{tr "Alphabetically"}</a></li>
</ul>
<div class="page">
	<h2>{tr "Personal recipe list"}</h2>

	<form action="{$root}/{$model->request->controller}/{$model->request->action}" method="post">
	<fieldset>
		<legend>{tr "Edit recipe list"}</legend>

		<input type="hidden" name="_arbit_form_token" value="{arbit_form_token()}" />

		<table>
			<thead>
				<tr><th>{tr "Recipe"}</th><th>{tr "People"}</th></tr>
			</thead>
			<tbody>
			{foreach $model->list as $row}
				<tr>
					<td class="title"><a href="{$root}/{$model->request->controller}/view/{$row['recipe']->id}">{$row['recipe']->title}</a></td>
					<td class="amount"><input type="text" name="amount[{$row['recipe']->id}]" value="{$row['amount']}" /></td>
				</tr>
			{/foreach}
			</tbody>
		</table>

		<label>
			<input type="submit" name="update" value="{tr "Update list"}" />
		</label>
	</fieldset>
	</form>

	<h3>{tr "Ingredient list"}</h3>

	{foreach $model->list as $row}
		{foreach $row['recipe']->ingredients as $ingredients}
			{foreach $ingredients as $ingredient}
				{if is_set( $ingredientList[$ingredient['ingredient']] )}
					{$ingredientList[$ingredient['ingredient']]['amount'] += $ingredient['amount'] / $row['recipe']->amount * $row['amount']}
				{else}
					{$ingredientList[$ingredient['ingredient']] = $ingredient}
					{$ingredientList[$ingredient['ingredient']]['amount'] *= ( 1 / $row['recipe']->amount ) * $row['amount']}
				{/if}
			{/foreach}
		{/foreach}
	{/foreach}

	<table class="ingredients">
	{foreach $ingredientList as $ingredient}
		<tr>
			<td class="amount">{str_number( $ingredient['amount'], 2, '.', ',' )} {$ingredient['unit']}</td>
			<td>{$ingredient['ingredient']}</td>
		</tr>
	{/foreach}
	</table>
</div>