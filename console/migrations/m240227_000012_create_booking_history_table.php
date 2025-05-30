<?php 

/**
 * Migration for creating booking_history table
 */
class m240227_000012_create_booking_history_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%booking_history}}', [
            'history_id' => $this->primaryKey(),
            'booking_id' => $this->integer()->notNull(),
            'old_status' => $this->string(20),
            'new_status' => $this->string(20),
            'changed_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'changed_by' => $this->string(50),
        ], 'ENGINE=InnoDB');
    }

    public function safeDown()
    {
        $this->dropTable('{{%booking_history}}');
    }
}