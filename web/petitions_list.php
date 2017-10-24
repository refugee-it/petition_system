<?php
/* Copyright (C) 2014-2017  Stephan Kreutzer
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
 * @file $/web/petitions_list.php
 * @brief Lists the petitions.
 * @author Stephan Kreutzer
 * @since 2014-06-08
 */



session_start();

if (isset($_POST['logout']) === true)
{
    $language = null;

    if (isset($_SESSION['language']) === true)
    {
        $language = $_SESSION['language'];
    }

    $_SESSION = array();

    if ($language != null)
    {
        $_SESSION['language'] = $language;
    }
    else
    {
        if (isset($_COOKIE[session_name()]) == true)
        {
            setcookie(session_name(), '', time()-42000, '/');
        }
    }
}

$isLoggedIn = false;

if (isset($_SESSION['user_id']) === true &&
    isset($_SESSION['instance_path']) === true)
{
    if (dirname(__FILE__) === $_SESSION['instance_path'])
    {
        $isLoggedIn = true;
    }
}



require_once("./libraries/languagelib.inc.php");
require_once(getLanguageFile("petitions_list"));
require_once("./libraries/petition_management.inc.php");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
     "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".getCurrentLanguage()."\" lang=\"".getCurrentLanguage()."\">\n".
     "    <head>\n".
     "        <title>".LANG_PAGETITLE."</title>\n".
     "        <link rel=\"stylesheet\" type=\"text/css\" href=\"mainstyle.css\"/>\n".
     "        <meta http-equiv=\"expires\" content=\"1296000\"/>\n".
     "        <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "    </head>\n".
     "    <body>\n".
     "        <div class=\"mainbox\">\n".
     "          <div class=\"mainbox_header\">\n".
     "            <h1 class=\"mainbox_header_h1\">".LANG_HEADER."</h1>\n".
     "          </div>\n".
     "          <div class=\"mainbox_body\">\n";

if ($isLoggedIn === true)
{
    $petitions = GetPetitionList();

    if (is_array($petitions) === true)
    {
        echo "            <ul>\n";

        foreach ($petitions as $petition)
        {
            echo "              <li><a href=\"petition_details.php?handle=".htmlspecialchars($petition['handle'], ENT_COMPAT | ENT_XML1, "UTF-8")."\">".htmlspecialchars($petition['title'], ENT_COMPAT | ENT_XML1, "UTF-8")."</a></li>\n";
        }

        echo "            </ul>\n";
    }

    echo "            <form action=\"petition_create.php\" method=\"post\">\n".
         "              <fieldset>\n".
         "                <input type=\"submit\" value=\"".LANG_CREATEPETITIONBUTTON."\"/><br/>\n".
         "              </fieldset>\n".
         "            </form>\n".
         "            <form action=\"petitions_list.php\" method=\"post\">\n".
         "              <fieldset>\n".
         "                <input type=\"submit\" name=\"logout\" value=\"".LANG_BUTTON_LOGOUT."\"/><br/>\n".
         "              </fieldset>\n".
         "            </form>\n";
}

echo "            <form action=\"index.php\" method=\"post\">\n".
     "              <fieldset>\n".
     "                <input type=\"submit\" value=\"".LANG_BUTTON_MAINPAGE."\"/><br/>\n".
     "              </fieldset>\n".
     "            </form>\n".
     "          </div>\n".
     "        </div>\n".
     "        <div class=\"footerbox\">\n".
     "          <a href=\"license.php\" class=\"footerbox_link\">".LANG_LICENSE."</a>\n".
     "        </div>\n".
     "    </body>\n".
     "</html>\n";




?>
