<?php 

/**
 * Migration for creating tour_reviews table
 */
class m240227_000010_create_tour_reviews_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tour_reviews}}', [
            'review_id' => $this->primaryKey(),
            'tour_id' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'rating' => $this->integer()->defaultValue(5),
            'comment' => $this->text(),
            'review_date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB');

        // Add foreign keys
        $this->addForeignKey(
            'fk-tour_reviews-tour_id',
            '{{%tour_reviews}}',
            'tour_id',
            '{{%tours}}',
            'tour_id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-tour_reviews-customer_id',
            '{{%tour_reviews}}',
            'customer_id',
            '{{%customers}}',
            'customer_id',
            'CASCADE'
        );

        // Add check constraint
        $this->execute('ALTER TABLE {{%tour_reviews}} ADD CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5)');
        
        // Add unique constraint
        $this->createIndex(
            'unique_customer_tour_review',
            '{{%tour_reviews}}',
            ['tour_id', 'customer_id'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-tour_reviews-customer_id', '{{%tour_reviews}}');
        $this->dropForeignKey('fk-tour_reviews-tour_id', '{{%tour_reviews}}');
        $this->dropIndex('unique_customer_tour_review', '{{%tour_reviews}}');
        $this->dropTable('{{%tour_reviews}}');
    }
}
