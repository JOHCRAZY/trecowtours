<?php 

/**
 * Migration for creating contact_info table
 */
class m240227_000007_create_contact_info_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%contact_info}}', [
            'contact_id' => $this->primaryKey(),
            'platform' => $this->string(50)->notNull(),
            'contact_value' => $this->string(100)->notNull(),
            'contact_type' => "ENUM('social', 'phone', 'email') DEFAULT 'social'",
            'is_active' => $this->boolean()->defaultValue(true),
        ], 'ENGINE=InnoDB');
    }

    public function safeDown()
    {
        $this->dropTable('{{%contact_info}}');
    }
}
