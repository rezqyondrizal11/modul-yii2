<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_social_media}}`.
 */
class m210108_032856_create_user_social_media_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_social_media}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'user_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_social_media}}');
    }
}
