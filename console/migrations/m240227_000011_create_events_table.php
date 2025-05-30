<?php 
/**
 * Migration for creating events table
 */
class m240227_000011_create_events_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%events}}', [
            'event_id' => $this->primaryKey(),
            'tour_id' => $this->integer()->null()->defaultValue(null),
            'event_name' => $this->string(100)->notNull(),
            'description' => $this->text(),
            'event_date' => $this->date()->notNull(),
            'start_time' => $this->time()->notNull(),
            'end_time' => $this->time()->notNull(),
            'location' => $this->string(100)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB');

        // Add foreign key
        $this->addForeignKey(
            'fk-events-tour_id',
            '{{%events}}',
            'tour_id',
            '{{%tours}}',
            'tour_id',
            'CASCADE'
        );

        // Add check constraint
        $this->execute('ALTER TABLE {{%events}} ADD CONSTRAINT chk_event_time CHECK (end_time > start_time)');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-events-tour_id', '{{%events}}');
        $this->dropTable('{{%events}}');
    }
}
