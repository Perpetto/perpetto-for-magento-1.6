<?php
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$connection = $this->getConnection();

$slotsTableName = $this->getTable('perpetto/slots');

$slotsTable = $connection->newTable($slotsTableName)
    ->addColumn('slot_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Entity ID')
    ->addColumn('perpetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Perpetto Slot ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Slot Name')
    ->addColumn('count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => true,
    ), 'Count')
    ->addColumn('html', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'HTML')
    ->addColumn('token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Token')
    ->addColumn('owner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => true,
    ), 'Owner ID')
    ->addColumn('domain_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => true,
    ), 'Store ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
    ), 'Create Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
    ), 'Update Time')
    ->addColumn('page', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Page')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => true,
    ), 'Status')
    ->addColumn('running', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned' => true,
        'nullable' => true,
    ), 'Running Flag')
    ->addColumn('css', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true,
    ), 'CSS')
    ->addColumn('compressed_css', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true,
    ), 'Compressed CSS')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Title')
    ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => true,
    ), 'Template ID')
    ->addColumn('compiled_html', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true,
    ), 'Compiled HTML')
    ->addColumn('engine_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => true,
    ), 'Recommendations Engine')
    ->addIndex('IDX_PERPETTO_ID',
        array('perpetto_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex('IDX_PERPETTO_TEMPLATE_ID',
        array('template_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX))
    ->setComment('Perpetto Slots Table');

if ($connection->isTableExists($slotsTableName)) {
    $connection->dropTable($slotsTableName);
}

$connection->createTable($slotsTable);

$this->endSetup();
