<?php
/**
 * Migration for creating tour_images table
 */
class m240227_000013_create_tour_images_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tour_images}}', [
            'image_id' => $this->primaryKey(),
            'tour_id' => $this->integer()->notNull(),
            'image_url' => $this->string(255)->notNull(),
        ], 'ENGINE=InnoDB');

        // Add foreign key
        $this->addForeignKey(
            'fk-tour_images-tour_id',
            '{{%tour_images}}',
            'tour_id',
            '{{%tours}}',
            'tour_id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-tour_images-tour_id', '{{%tour_images}}');
        $this->dropTable('{{%tour_images}}');
    }
}