<?php

use yii\db\Migration;

/**
 * Class m240522_200036_scale_events
 */
class m240522_200036_scale_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%event}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'event_text' => $this->string()->nunotNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], null);

        $this->addColumn('history', 'event_id', $this->integer());
        
        $this->addForeignKey(
            'fk-history-event_id',
            'history',
            'event_id',
            'event',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-history-event_id',
            'history'
        );
        $this->dropColumn('history', 'event_id');
        $this->dropTable('{{%event}}');

        return false;
    }
}
