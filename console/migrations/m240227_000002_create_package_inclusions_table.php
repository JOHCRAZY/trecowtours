<?php

/**
 * Migration for creating package_inclusions table
 */
class m240227_000002_create_package_inclusions_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('{{%package_inclusions}}', [
            'inclusion_id' => $this->primaryKey(),
            'tour_id' => $this->integer()->notNull(),
            'inclusion_name' => $this->string(100)->notNull(),
        ], 'ENGINE=InnoDB');

        // Add foreign key
        $this->addForeignKey(
            'fk-package_inclusions-tour_id',
            '{{%package_inclusions}}',
            'tour_id',
            '{{%tours}}',
            'tour_id',
            'CASCADE'
        );

        // Create index
        $this->createIndex(
            'idx_package_inclusions_tour_id',
            '{{%package_inclusions}}',
            'tour_id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-package_inclusions-tour_id', '{{%package_inclusions}}');
        $this->dropIndex('idx_package_inclusions_tour_id', '{{%package_inclusions}}');
        $this->dropTable('{{%package_inclusions}}');
    }
}
