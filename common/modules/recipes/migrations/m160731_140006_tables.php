<?php

use yii\db\Migration;
use yii\db\Schema;

class m160731_140006_tables extends Migration {

	/**
	 * @inheritdoc
	 */
	public function safeUp() {
		$this->createTable('source', [
			'id'        => $this->primaryKey() . ' COMMENT "Уникальный идентификатор"',
			'title'     => Schema::TYPE_STRING . '(100) NOT NULL COMMENT "Название"',
			'url'       => Schema::TYPE_STRING . '(50) NOT NULL COMMENT "Адрес источника"',
			'tech_name' => Schema::TYPE_STRING . '(50) NOT NULL COMMENT "Техническое название"',
		], 'COMMENT "Источники"');

		$this->batchInsert('source', ['id', 'title', 'url', 'tech_name'], [
			[1, 'E-liquid-recipes', 'http://e-liquid-recipes.com', 'ELiquidRecipes'],
			[2, 'Vape Craft', 'http://www.vapecraft.ru', 'VapeCraft'],
		]);

		$this->createTable('recipe', [
			'id'               => $this->primaryKey() . ' COMMENT "Уникальный идентификатор"',
			'source_recipe_id' => Schema::TYPE_STRING . ' NULL COMMENT "Идентификатор рецепта в системе источника"',
			'source_id'        => Schema::TYPE_INTEGER . ' NULL COMMENT "Идентификатор источника, с которого взят рецепт"',
			'title'            => Schema::TYPE_STRING . '(255) NOT NULL COMMENT "Название"',
			'notes'            => Schema::TYPE_TEXT . ' NOT NULL COMMENT "Заметка"',
		], 'COMMENT "Рецепты"');

		$this->createIndex('ix-recipe-[source_id,source_recipe_id]', 'recipe', ['source_id', 'source_recipe_id']);

		$this->createTable('flavor', [
			'id'    => $this->primaryKey() . ' COMMENT "Уникальный идентификатор"',
			'title' => Schema::TYPE_STRING . '(255) NOT NULL COMMENT "Название"',
		], 'COMMENT "Ароматизаторы"');

		$this->createTable('flavor_source_link', [
			'flavor_id'        => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Идентификатор ароматизатора"',
			'source_id'        => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Идентификатор источника"',
			'source_flavor_id' => Schema::TYPE_STRING . '(255) COMMENT "Идентификатор ароматизатора в системе источника"',
		], 'COMMENT "Связь между ароматизатором и источниками"');

		$this->addPrimaryKey('pk-flavor_source_link-[flavor_id,source_id,source_flavor_id]', 'flavor_source_link', ['flavor_id','source_id','source_flavor_id']);

		$this->createTable('recipe_flavor', [
			'recipe_id' => Schema::TYPE_INTEGER . '(255) NOT NULL COMMENT "Идентификатор рецепта"',
			'flavor_id' => Schema::TYPE_INTEGER . '(255) NOT NULL COMMENT "Идентификатор ароматизатора"',
		], 'COMMENT "Ароматизаторы, входящие в рецепты"');

		$this->addPrimaryKey('pk-recipe_flavor-[recipe_id,flavor_id]', 'recipe_flavor', ['recipe_id','flavor_id']);
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() {
		$this->dropTable('source');
		$this->dropTable('recipe');
		$this->dropTable('flavor');
		$this->dropTable('flavor_source_link');
		$this->dropTable('recipe_flavor');
	}
}
