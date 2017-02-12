<?php

use yii\db\Migration;
use yii\db\Schema;

class m170212_152257_create_flavor_brand extends Migration {

	/**
	 * @inheritdoc
	 */
	public function safeUp() {
		$this->createTable('flavor_brand', [
			'id'    => $this->primaryKey() . ' COMMENT "Уникальный идентификатор"',
			'title' => Schema::TYPE_STRING . '(50) NOT NULL COMMENT "Название"',
		], 'COMMENT "Бренды ароматизаторов"');

		$this->addColumn('flavor', 'brand_id', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "Идентификатор бренда"');
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() {
		$this->dropColumn('flavor', 'brand_id');
		$this->dropTable('flavor_brand');
	}
}
