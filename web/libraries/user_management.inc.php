<?php
/* Copyright (C) 2012-2016  Stephan Kreutzer
 *
 * This file is part of petition system for refugee-it.de.
 *
 * petition system for refugee-it.de is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License version 3 or any later version,
 * as published by the Free Software Foundation.
 *
 * petition system for refugee-it.de is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License 3 for more details.
 *
 * You should have received a copy of the GNU Affero General Public License 3
 * along with petition system for refugee-it.de. If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * @file $/web/libraries/user_management.inc.php
 * @author Stephan Kreutzer
 * @since 2012-06-02
 */



require_once(dirname(__FILE__)."/database.inc.php");



define("USER_ROLE_ADMIN", 1);
define("USER_ROLE_PETITION_OWNER", 2);



function insertNewUser($name, $password, $role)
{
    /** @todo Check for empty $name, $password or $role. */

    if (Database::Get()->IsConnected() !== true)
    {
        return -1;
    }

    if (Database::Get()->BeginTransaction() !== true)
    {
        return -2;
    }

    $salt = md5(uniqid(rand(), true));
    $password = hash('sha512', $salt.$password);

    $id = Database::Get()->Insert("INSERT INTO `".Database::Get()->GetPrefix()."users` (`id`,\n".
                                  "    `name`,\n".
                                  "    `salt`,\n".
                                  "    `password`,\n".
                                  "    `role`)\n".
                                  "VALUES (?, ?, ?, ?, ?)\n",
                                  array(NULL, $name, $salt, $password, $role),
                                  array(Database::TYPE_NULL, Database::TYPE_STRING, Database::TYPE_STRING, Database::TYPE_STRING, Database::TYPE_INT));

    if ($id <= 0)
    {
        Database::Get()->RollbackTransaction();
        return -4;
    }

    if (Database::Get()->CommitTransaction() === true)
    {
        return $id;
    }

    Database::Get()->RollbackTransaction();
    return -7;
}



?>
