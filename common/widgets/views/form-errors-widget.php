<?php
use common\widgets\ModelErrorsWidget;
use yiiCustom\core\View;

/**
 * @var View $this
 * @var ModelErrorsWidget $widget
 */
?>

<?php if (count($widget->model->errors) > 0): ?>
	<div class="alert alert-warning">
		<?php foreach($widget->model->errors as $field => $errors): ?>
			<?php foreach($errors as $error): ?>
				<?php if ($field === 'fatal'): ?>
					<?= $error ?>
				<?php else: ?>
					<p>
						<?php if ($widget->withFieldsNames === true): ?>
							Поле "<?= $widget->model->getAttributeLabel($field) ?>":
						<?php endif ?>
						<?= $error ?>
					</p>
				<?php endif ?>
			<?php endforeach ?>
		<?php endforeach ?>
	</div>
<?php endif ?>
