<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * @inheritdoc
 */
class m161120_053458_alter_ref_user extends Migration {

	/**
	 * @inheritdoc
	 */
	public function safeUp() {
		$this->addColumn('ref_user', 'last_activity_stamp', Schema::TYPE_DATETIME . ' NULL COMMENT "Дата-время последней активности пользователя" AFTER `auth_key`');
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() {
		$this->dropColumn('ref_user', 'last_activity_stamp');
	}
}
