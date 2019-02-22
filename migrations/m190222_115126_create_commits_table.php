<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%commits}}`.
 */
class m190222_115126_create_commits_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%commits}}', [
            'sha' => $this->string()->notNull(),
            'files' => $this->json()->notNull(),
        ]);
        $this->addPrimaryKey(
            'pk-commits-sha',
            'commits',
            'sha'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%commits}}');
    }
}
