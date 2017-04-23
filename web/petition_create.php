<?php
/* Copyright (C) 2014-2016  Stephan Kreutzer
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
 * @file $/web/petition_create.php
 * @brief Create a new petition.
 * @author Stephan Kreutzer
 * @since 2014-06-08
 */



require_once("./libraries/session.inc.php");

require_once("./libraries/languagelib.inc.php");
require_once(getLanguageFile("petition_create"));

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

if (isset($_POST['title']) == false ||
    isset($_POST['description']) == false)
{
    echo "            <form action=\"petition_create.php\" method=\"post\">\n".
         "              <fieldset>\n".
         "                <input name=\"title\" type=\"text\" size=\"40\" maxlength=\"255\"/> ".LANG_PETITIONTITLECAPTION."<br/>\n".
         "                <textarea name=\"description\" cols=\"80\" rows=\"12\">".LANG_PETITIONDESCRIPTIONCAPTION."</textarea><br/>\n".
         "                <input type=\"text\" name=\"end\" value=\"\" size=\"20\" maxlength=\"20\"/> ".LANG_PETITIONENDCAPTION."<br/>\n".
         "                <input type=\"submit\" value=\"".LANG_PETITIONCREATEBUTTON."\"/>\n".
         "              </fieldset>\n".
         "            </form>\n";

}
else
{
    require_once("./libraries/petition_management.inc.php");

    $datetimeEnd = null;

    if (isset($_POST['end']) === true)
    {
        $datetimeEnd = $_POST['end'];
    }

    $result = AddNewPetition($_POST['title'],
                             $_POST['description'],
                             $datetimeEnd,
                             $_SESSION['user_id']);

    if (is_array($result) === true)
    {
        echo "            <p>\n".
             "              <span class=\"success\">".LANG_PETITIONCREATEDSUCCESSFULLY."</span>\n".
             "            </p>\n".
             "            <p style=\"font-family: monospace;\">\n".
             "              ".$result['handle']."\n".
             "            </p>\n".
             "            <form action=\"petitions_list.php\" method=\"post\">\n".
             "              <fieldset>\n".
             "                <input type=\"submit\" value=\"".LANG_CONTINUE."\"/>\n".
             "              </fieldset>\n".
             "            </form>\n";
    }
    else
    {
        echo "            <p>\n".
             "              <span class=\"error\">".LANG_PETITIONCREATEFAILED."</span>\n".
             "            </p>\n".
             "            <form action=\"petition_create.php\" method=\"post\">\n".
             "              <fieldset>\n".
             "                <input type=\"submit\" value=\"".LANG_BACK."\"/>\n".
             "              </fieldset>\n".
             "            </form>\n";
    }
}

echo "          </div>\n".
     "        </div>\n".
     "        <div class=\"footerbox\">\n".
     "          <a href=\"license.php\" class=\"footerbox_link\">".LANG_LICENSE."</a>\n".
     "        </div>\n".
     "    </body>\n".
     "</html>\n";




?>
