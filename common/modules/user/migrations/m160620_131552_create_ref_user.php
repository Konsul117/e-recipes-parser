<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation for table `ref_user`.
 */
class m160620_131552_create_ref_user extends Migration {

	const TABLE_NAME = 'ref_user';

	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->createTable(static::TABLE_NAME, [
			'id'            => $this->primaryKey() . ' COMMENT "Уникальный идентификатор пользователя"',
			'username'      => Schema::TYPE_STRING . '(255) NOT NULL COMMENT "Имя пользователя"',
			'password'      => Schema::TYPE_STRING . '(60) NOT NULL COMMENT "Пароль"',
			'email'         => Schema::TYPE_STRING . '(255) NOT NULL COMMENT "Email"',
			'auth_key'      => Schema::TYPE_STRING . '(50) NOT NULL COMMENT "Ключ безопасности"',
			'created_stamp' => Schema::TYPE_DATETIME . ' NOT NULL COMMENT "Дата-врем создания записи"',
			'updated_stamp' => Schema::TYPE_DATETIME . ' NOT NULL COMMENT "Дата-врем обновления записи"',
			'UNIQUE KEY(username)',
			'UNIQUE KEY(email)',
			'UNIQUE KEY(auth_key)'
		],'COMMENT "Справочник пользователей"');
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropTable(static::TABLE_NAME);
	}
}
