<?php 

/**
 * Migration for creating tour_activities table
 */
class m240227_000004_create_tour_activities_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tour_activities}}', [
            'tour_id' => $this->integer()->notNull(),
            'activity_id' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        // Add primary key
        $this->addPrimaryKey('pk-tour_activities', '{{%tour_activities}}', ['tour_id', 'activity_id']);

        // Add foreign keys
        $this->addForeignKey(
            'fk-tour_activities-tour_id',
            '{{%tour_activities}}',
            'tour_id',
            '{{%tours}}',
            'tour_id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-tour_activities-activity_id',
            '{{%tour_activities}}',
            'activity_id',
            '{{%activities}}',
            'activity_id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-tour_activities-activity_id', '{{%tour_activities}}');
        $this->dropForeignKey('fk-tour_activities-tour_id', '{{%tour_activities}}');
        $this->dropPrimaryKey('pk-tour_activities', '{{%tour_activities}}');
        $this->dropTable('{{%tour_activities}}');
    }
}
