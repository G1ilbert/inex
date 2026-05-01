<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class Notifications extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('Notifications', [
            'id' => false,
            'primary_key' => ['ID'],
        ])
            ->addColumn('ID', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => true,
            ])
            ->addColumn('SenderID', 'biginteger', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'ID',
            ])
            ->addColumn('ReceiverID', 'biginteger', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'SenderID',
            ])
            ->addColumn('Type', 'string', [
                'null' => false,
                'limit' => 64,
                'after' => 'ReceiverID',
            ])
            ->addColumn('Ref_Users', 'biginteger', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'Type',
            ])
            ->addColumn('Ref_Medals', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'Ref_Users',
            ])
            ->addColumn('Ref_Comments', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'Ref_Items',
            ])
            ->addColumn('Ref_Comments_Reply', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'Ref_Comments',
            ])
            ->addColumn('Date', 'timestamp', [
                'null' => false,
                'default' => 'current_timestamp()',
                'update' => 'CURRENT_TIMESTAMP',
                'after' => 'Ref_Comments_Reply',
            ])
            ->addColumn('Read', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => 1,
                'after' => 'Date',
            ])
            ->addColumn('Dismissed', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => 1,
                'after' => 'Read',
            ])
            ->addIndex(['SenderID', 'ReceiverID', 'Type', 'Ref_Users', 'Ref_Medals', 'Ref_Comments', 'Ref_Comments_Reply'], [
                'name' => 'R',
                'unique' => true,
            ])
            ->create();
    }
}
