<?php
/**
 * @see       http://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Db\Sql\Platform\Postgresql;

use Zend\Db\Sql\Platform\AbstractPlatform;

// TODO: need to be merged with Postgres PRs depending on which one is accepted first
// this is a stub to void breaking national chars for Postgres, as it is the odd one out
class Postgresql extends AbstractPlatform
{
    public function __construct()
    {
        $this->setTypeDecorator('Zend\Db\Sql\Ddl\CreateTable', new Ddl\CreateTableDecorator());
        $this->setTypeDecorator('Zend\Db\Sql\Ddl\AlterTable', new Ddl\AlterTableDecorator());
    }
}