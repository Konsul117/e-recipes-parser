<?php

use yii\db\Migration;

/**
 * @inheritdoc
 */
class m170520_102805_alter_recipe_flavor_content extends Migration {
	/**
	 * @inheritdoc
	 */
	public function safeUp() {
		$this->execute('ALTER TABLE `recipe_flavor` ADD `content` float(4,4) NOT NULL DEFAULT 0 COMMENT "Процент содержания"');
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() {
		$this->dropColumn('recipe_flavor', 'content');
	}
}
