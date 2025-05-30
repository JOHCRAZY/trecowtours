<?php 

/**
 * Migration for creating activities table
 */
class m240227_000003_create_activities_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%activities}}', [
            'activity_id' => $this->primaryKey(),
            'activity_name' => $this->string(100)->notNull(),
            'description' => $this->text(),
            'location' => $this->string(100),
            'activity_date' => $this->date(),
        ], 'ENGINE=InnoDB');
    }

    public function safeDown()
    {
        $this->dropTable('{{%activities}}');
    }
}
