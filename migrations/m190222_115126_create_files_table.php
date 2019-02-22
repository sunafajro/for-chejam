<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%files}}`.
 */
class m190222_115126_create_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%files}}', [
            'id' => $this->primaryKey(),
            'filename' => $this->string()->notNull(),
            'commits' => $this->integer()->notNull(),
            'authors' => $this->string()->notNull(),
        ]);
        $this->createIndex(
            'idx-files-filename',
            'files',
            'filename'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%files}}');
    }
}
