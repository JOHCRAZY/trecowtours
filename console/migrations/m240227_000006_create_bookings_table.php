<?php

/**
 * Migration for creating bookings table
 */
class m240227_000006_create_bookings_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%bookings}}', [
            'booking_id' => $this->primaryKey(),
            'tour_id' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'booking_type' => "ENUM('single', 'couple') NOT NULL",
            'booking_date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'payment_status' => "ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending'",
            'total_amount' => $this->decimal(10, 2)->notNull(),
            'discount_applied' => $this->decimal(10, 2)->defaultValue(0),
            'is_deleted' => $this->boolean()->defaultValue(false),
        ], 'ENGINE=InnoDB');

        // Add foreign keys
        $this->addForeignKey(
            'fk-bookings-tour_id',
            '{{%bookings}}',
            'tour_id',
            '{{%tours}}',
            'tour_id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-bookings-customer_id',
            '{{%bookings}}',
            'customer_id',
            '{{%customers}}',
            'customer_id',
            'CASCADE'
        );

        // Create indexes
        $this->createIndex(
            'idx_bookings_tour_id',
            '{{%bookings}}',
            'tour_id'
        );

        $this->createIndex(
            'idx_bookings_customer_id',
            '{{%bookings}}',
            'customer_id'
        );

        // Add check constraints
        $this->execute('ALTER TABLE {{%bookings}} ADD CONSTRAINT chk_total_amount CHECK (total_amount >= 0)');
        $this->execute('ALTER TABLE {{%bookings}} ADD CONSTRAINT valid_discount CHECK (discount_applied >= 0 AND discount_applied <= total_amount)');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-bookings-customer_id', '{{%bookings}}');
        $this->dropForeignKey('fk-bookings-tour_id', '{{%bookings}}');
        $this->dropIndex('idx_bookings_customer_id', '{{%bookings}}');
        $this->dropIndex('idx_bookings_tour_id', '{{%bookings}}');
        $this->dropTable('{{%bookings}}');
    }
}








