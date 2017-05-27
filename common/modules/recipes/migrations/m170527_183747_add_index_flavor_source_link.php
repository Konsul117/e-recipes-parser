<?php

use yii\db\Migration;

/**
 * @inheritdoc
 */
class m170527_183747_add_index_flavor_source_link extends Migration {

	/**
	 * @inheritdoc
	 */
	public function safeUp() {
		$this->createIndex('ix-flavor_source_link-[source_id,source_flavor_id]', 'flavor_source_link', ['source_id', 'source_flavor_id']);
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() {
		$this->dropIndex('ix-flavor_source_link-[source_id,source_flavor_id]', 'flavor_source_link');
	}
}
