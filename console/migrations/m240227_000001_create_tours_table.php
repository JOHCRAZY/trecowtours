<?php 

/**
 * Migration for creating tours table
 */
class m240227_000001_create_tours_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tours}}', [
            'tour_id' => $this->primaryKey(),
            'tour_name' => $this->string(100)->notNull(),
            'description' => $this->text(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'booking_deadline' => $this->date()->notNull(),
            'duration_days' => $this->integer()->notNull(),
            'duration_nights' => $this->integer()->notNull(),
            'starting_point' => $this->string(100)->notNull(),
            'single_price' => $this->decimal(10, 2)->notNull(),
            'couple_price' => $this->decimal(10, 2)->notNull(),
            'total_seats' => $this->integer()->notNull(),
            'available_seats' => $this->integer()->notNull(),
            'status' => $this->string(10)->defaultValue('active'),
            'is_deleted' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB');

        // Add check constraints
        $this->execute('ALTER TABLE {{%tours}} ADD CONSTRAINT chk_dates CHECK (start_date < end_date AND booking_deadline < start_date)');
        $this->execute('ALTER TABLE {{%tours}} ADD CONSTRAINT valid_prices CHECK (single_price > 0 AND couple_price > single_price)');
        
        // Add comment
        $this->execute('ALTER TABLE {{%tours}} COMMENT "Contains all tour packages offered by Trecow Tours"');
    }

    public function safeDown()
    {
        $this->dropTable('{{%tours}}');
    }
}
