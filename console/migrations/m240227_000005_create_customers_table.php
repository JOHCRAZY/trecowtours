<?php 

/**
 * Migration for creating customers table
 */
class m240227_000005_create_customers_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%customers}}', [
            'customer_id' => $this->primaryKey(),
            'first_name' => $this->string(50)->notNull(),
            'last_name' => $this->string(50)->notNull(),
            'email' => $this->string(100)->notNull()->unique(),
            'phone' => $this->string(20)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB');

        // Create index
        $this->createIndex(
            'idx_customers_email',
            '{{%customers}}',
            'email'
        );
    }

    public function safeDown()
    {
        $this->dropIndex('idx_customers_email', '{{%customers}}');
        $this->dropTable('{{%customers}}');
    }
}