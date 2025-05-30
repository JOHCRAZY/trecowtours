<?php 

/**
 * Migration for creating tour_guides table
 */
class m240227_000008_create_tour_guides_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tour_guides}}', [
            'guide_id' => $this->primaryKey(),
            'first_name' => $this->string(50)->notNull(),
            'last_name' => $this->string(50)->notNull(),
            'email' => $this->string(100)->notNull()->unique(),
            'phone' => $this->string(20),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB');
    }

    public function safeDown()
    {
        $this->dropTable('{{%tour_guides}}');
    }
}
