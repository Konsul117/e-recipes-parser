<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m160805_181158_alter_ref_user
 */
class m160805_181158_alter_ref_user extends Migration {

	const TABLE_NAME = 'ref_user';

	/**
	 * @inheritdoc
	 */
	public function safeUp() {
		$this->addColumn(static::TABLE_NAME, 'email_is_confirmed', Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0 COMMENT "Признак подтверждения e-mail" AFTER `email`');
		$this->addColumn(static::TABLE_NAME, 'phone', Schema::TYPE_STRING . '(255) NOT NULL DEFAULT "" COMMENT "Номер телефона" AFTER `email_is_confirmed`');
		$this->addColumn(static::TABLE_NAME, 'phone_is_confirmed', Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0 COMMENT "Признак подтверждения телефона" AFTER `phone`');
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() {
		$this->dropColumn(static::TABLE_NAME, 'email_is_confirmed');
		$this->dropColumn(static::TABLE_NAME, 'phone');
		$this->dropColumn(static::TABLE_NAME, 'phone_is_confirmed');
	}
}
