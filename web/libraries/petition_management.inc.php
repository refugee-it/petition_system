<?php
/* Copyright (C) 2012-2017  Stephan Kreutzer
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
 * @file $/web/libraries/petition_management.inc.php
 * @author Stephan Kreutzer
 * @since 2016-07-25
 */



require_once(dirname(__FILE__)."/database.inc.php");



define("PETITION_STATUS_UNLISTED", 1);
define("PETITION_STATUS_LISTED", 2);
define("PETITION_STATUS_TRASHED", 3);


function AddNewPetition($title,
                        $description,
                        $datetimeEnd,
                        $idUser)
{
    /** @todo Check for empty parameters. Check, if $title exists already in the database. */

    if (Database::Get()->IsConnected() !== true)
    {
        return -1;
    }

    if (Database::Get()->BeginTransaction() !== true)
    {
        return -2;
    }

    $handle = md5(uniqid(rand(), true));
    $userId = 1;

    if (is_string($datetimeEnd) !== true)
    {
        $datetimeEnd = null;
    }

    $values = array(NULL, $title, $description, PETITION_STATUS_UNLISTED, $datetimeEnd, $handle, $userId);
    $types = array(Database::TYPE_NULL, Database::TYPE_STRING, Database::TYPE_STRING, Database::TYPE_INT);

    if (is_numeric($datetimeEnd) === true &&
        $datetimeEnd != null)
    {
        $types[] = Database::TYPE_STRING;
    }
    else
    {
        $types[] = Database::TYPE_NULL;
    }

    $types[] = Database::TYPE_STRING;
    $types[] = Database::TYPE_INT;

    $id = Database::Get()->Insert("INSERT INTO `".Database::Get()->GetPrefix()."petitions` (`id`,\n".
                                  "    `title`,\n".
                                  "    `description`,\n".
                                  "    `status`,\n".
                                  "    `datetime_created`,\n".
                                  "    `datetime_end`,\n".
                                  "    `handle`,\n".
                                  "    `id_user`)\n".
                                  "VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)\n",
                                  $values,
                                  $types);

    if ($id <= 0)
    {
        Database::Get()->RollbackTransaction();
        return -3;
    }

    if (Database::Get()->CommitTransaction() === true)
    {
        return array("id" => $id, "handle" => $handle);
    }

    Database::Get()->RollbackTransaction();
    return -4;
}

function GetPetitionByHandle($handle)
{
    /** @todo Check for empty parameters. */

    if (Database::Get()->IsConnected() !== true)
    {
        return -1;
    }

    $petition = Database::Get()->Query("SELECT `id`,\n".
                                       "    `title`,\n".
                                       "    `description`,\n".
                                       "    `status`,\n".
                                       "    `datetime_created`,\n".
                                       "    `datetime_end`,\n".
                                       "    `handle`,\n".
                                       "    `id_user`\n".
                                       "FROM `".Database::Get()->GetPrefix()."petitions`\n".
                                       "WHERE `handle` LIKE ?\n",
                                       array($handle),
                                       array(Database::TYPE_STRING));

    if (is_array($petition) !== true)
    {
        return null;
    }

    if (count($petition) <= 0)
    {
        return null;
    }

    return $petition[0];
}

function SignPetition($idPetition,
                      $name,
                      $zipCode,
                      $city)
{
    /** @todo Check for empty parameters. */

    if (Database::Get()->IsConnected() !== true)
    {
        return -1;
    }

    if (Database::Get()->BeginTransaction() !== true)
    {
        return -2;
    }

    $id = Database::Get()->Insert("INSERT INTO `".Database::Get()->GetPrefix()."signatures` (`id`,\n".
                                  "    `name`,\n".
                                  "    `zip_code`,\n".
                                  "    `city`,\n".
                                  "    `datetime_signed`,\n".
                                  "    `id_petition`)\n".
                                  "VALUES (?, ?, ?, ?, NOW(), ?)\n",
                                  array(NULL, $name, $zipCode, $city, $idPetition),
                                  array(Database::TYPE_NULL, Database::TYPE_STRING, Database::TYPE_STRING, Database::TYPE_STRING, Database::TYPE_INT));

    if ($id <= 0)
    {
        Database::Get()->RollbackTransaction();
        return -3;
    }

    if (Database::Get()->CommitTransaction() === true)
    {
        return array("id" => $id);
    }

    Database::Get()->RollbackTransaction();
    return -4;
}

function GetPetitionList()
{
    if (Database::Get()->IsConnected() !== true)
    {
        return -1;
    }

    $petitions = Database::Get()->QueryUnsecure("SELECT `id`,\n".
                                                "    `title`,\n".
                                                "    `handle`\n".
                                                "FROM `".Database::Get()->GetPrefix()."petitions`\n".
                                                "WHERE 1\n");

    if (is_array($petitions) !== true)
    {
        return null;
    }

    if (count($petitions) <= 0)
    {
        return null;
    }

    return $petitions;
}

function GetSignatures($id)
{
    /** @todo Check for empty parameters. */

    if (Database::Get()->IsConnected() !== true)
    {
        return -1;
    }

    $signatures = Database::Get()->Query("SELECT `id`,\n".
                                       "    `name`,\n".
                                       "    `zip_code`,\n".
                                       "    `city`,\n".
                                       "    `datetime_signed`\n".
                                       "FROM `".Database::Get()->GetPrefix()."signatures`\n".
                                       "WHERE `id_petition`=?\n",
                                       array($id),
                                       array(Database::TYPE_INT));

    if (is_array($signatures) !== true)
    {
        return null;
    }

    if (count($signatures) <= 0)
    {
        return null;
    }

    return $signatures;
}



?>
