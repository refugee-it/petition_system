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
 * @file $/web/petition_sign.php
 * @brief Sign a petition.
 * @author Stephan Kreutzer
 * @since 2014-06-08
 */



$handle = null;

if (isset($_POST['handle']) === true)
{
    $handle = $_POST['handle'];
}

if (isset($_GET['handle']) === true)
{
    $handle = $_GET['handle'];
}

if ($handle == null)
{
    header("HTTP/1.1 404 Not Found");
    exit(1);
}

require_once("./libraries/petition_management.inc.php");

$petition = GetPetitionByHandle($handle);

if (is_array($petition) !== true)
{
    header("HTTP/1.1 404 Not Found");
    exit(1);
}

require_once("./libraries/languagelib.inc.php");
require_once(getLanguageFile("petition_sign"));
require_once("./language_selector.inc.php");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
     "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".getCurrentLanguage()."\" lang=\"".getCurrentLanguage()."\">\n".
     "    <head>\n".
     "        <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "        <title>".LANG_PAGETITLE."</title>\n".
     "        <link rel=\"stylesheet\" type=\"text/css\" href=\"mainstyle.css\"/>\n".
     "        <meta http-equiv=\"expires\" content=\"1296000\"/>\n".
     "        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"/>\n".
     "    </head>\n".
     "    <body>\n".
     getHTMLLanguageSelector("petition_sign.php?handle=".$handle).
     "        <div class=\"mainbox\">\n".
     "          <div class=\"mainbox_header\">\n".
     "            <h1 class=\"mainbox_header_h1\">".htmlspecialchars($petition['title'], ENT_COMPAT | ENT_HTML401, "UTF-8")."</h1>\n".
     "          </div>\n";

$datetimeEnd = null;

if ($petition['datetime_end'] != null)
{
    $datetimeEnd = date("U", strtotime($petition['datetime_end']));
}

if ($datetimeEnd === null ||
    time() <= $datetimeEnd)
{
    echo "          <div class=\"mainbox_body\">\n";

    if (isset($_POST['name']) === true &&
        isset($_POST['zip_code']) === true &&
        isset($_POST['city']) === true)
    {
        if (is_array(SignPetition((int)$petition['id'], $_POST['name'], $_POST['zip_code'], $_POST['city'])) === true)
        {
           echo "            <p class=\"success\">\n".
                "              ".LANG_SIGNSUCCESS."\n".
                "            </p>\n";
        }
        else
        {
            echo "            <p class=\"error\">\n".
                 "              ".LANG_SIGNFAILED."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n".
                 "    </body>\n".
                 "</html>\n";

            exit(-1);
        }
    }
    else
    {
        echo "            <p style=\"text-align:justified;\">\n".
             "              ".htmlspecialchars($petition['description'], ENT_COMPAT | ENT_HTML401, "UTF-8")."\n".
             "            </p>\n".
             "            <form action=\"petition_sign.php\" method=\"post\">\n".
             "              <fieldset>\n".
             "                <input name=\"zip_code\" type=\"text\" size=\"10\" maxlength=\"10\"/> ".LANG_SIGNEEZIPCODECAPTION."<br/>\n".
             "                <input name=\"city\" type=\"text\" size=\"40\" maxlength=\"255\"/> ".LANG_SIGNEECITYCAPTION."<br/>\n".
             "                <input name=\"name\" type=\"text\" size=\"40\" maxlength=\"255\"/> ".LANG_SIGNEENAMECAPTION."<br/>\n".
             "                <input type=\"hidden\" name=\"handle\" value=\"".htmlspecialchars($handle, ENT_COMPAT | ENT_HTML401, "UTF-8")."\"/>\n".
             "                <input type=\"submit\" value=\"".LANG_SIGNPETITIONBUTTON."\"/>\n".
             "              </fieldset>\n".
             "            </form>\n";
    }

    echo "          </div>\n";
}
else
{
    echo "          <div class=\"mainbox_body\">\n".
         "            <p>\n".
         "              ".LANG_PETITIONEXPIRED."\n".
         "            </p>\n".
         "          </div>\n";
}

echo "        </div>\n".
     "        <div class=\"footerbox\">\n".
     "          <a href=\"license.php\" class=\"footerbox_link\">".LANG_LICENSE."</a>\n".
     "        </div>\n".
     "    </body>\n".
     "</html>\n";




?>
