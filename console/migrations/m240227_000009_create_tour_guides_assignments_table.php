<?php 

/**
 * Migration for creating tour_guides_assignments table
 */
class m240227_000009_create_tour_guides_assignments_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tour_guides_assignments}}', [
            'tour_id' => $this->integer()->notNull(),
            'guide_id' => $this->integer()->notNull(),
            'assignment_date' => $this->date()->defaultExpression('CURRENT_DATE'),
        ], 'ENGINE=InnoDB');

        // Add primary key
        $this->addPrimaryKey('pk-tour_guides_assignments', '{{%tour_guides_assignments}}', ['tour_id', 'guide_id']);

        // Add foreign keys
        $this->addForeignKey(
            'fk-tour_guides_assignments-tour_id',
            '{{%tour_guides_assignments}}',
            'tour_id',
            '{{%tours}}',
            'tour_id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-tour_guides_assignments-guide_id',
            '{{%tour_guides_assignments}}',
            'guide_id',
            '{{%tour_guides}}',
            'guide_id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-tour_guides_assignments-guide_id', '{{%tour_guides_assignments}}');
        $this->dropForeignKey('fk-tour_guides_assignments-tour_id', '{{%tour_guides_assignments}}');
        $this->dropPrimaryKey('pk-tour_guides_assignments', '{{%tour_guides_assignments}}');
        $this->dropTable('{{%tour_guides_assignments}}');
    }
}
