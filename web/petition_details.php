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
 * @file $/web/petition_details.php
 * @brief Shows the details of a petition.
 * @author Stephan Kreutzer
 * @since 2017-10-23
 */



require_once("./libraries/https.inc.php");
require_once("./libraries/session.inc.php");

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
    http_response_code(404);
    exit(1);
}

require_once("./libraries/petition_management.inc.php");

$petition = GetPetitionByHandle($handle);

if (is_array($petition) !== true)
{
    http_response_code(404);
    exit(1);
}

$protocol = "https://";

if (HTTPS_ENABLED !== true)
{
    $protocol = "http://";
}

$baseURL = $protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);

require_once("./libraries/languagelib.inc.php");
require_once(getLanguageFile("petition_details"));

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
     "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".getCurrentLanguage()."\" lang=\"".getCurrentLanguage()."\">\n".
     "  <head>\n".
     "    <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "    <title>".LANG_PAGETITLE."</title>\n".
     "    <link rel=\"stylesheet\" type=\"text/css\" href=\"mainstyle.css\"/>\n".
     "    <meta http-equiv=\"expires\" content=\"1296000\"/>\n".
     "    <style type=\"text/css\">\n".
     "      .th, .td\n".
     "      {\n".
     "          padding: 0px 10px 0px 0px;\n".
     "      }\n".
     "    </style>\n".
     "  </head>\n".
     "  <body>\n".
     "    <div class=\"mainbox\">\n".
     "      <div class=\"mainbox_header\">\n".
     "        <h1 class=\"mainbox_header_h1\">".LANG_HEADER."</h1>\n".
     "      </div>\n".
     "      <div class=\"mainbox_body\">\n".
     "        <div class=\"table\">\n".
     "          <div class=\"tr\">\n".
     "            <span class=\"th\">".LANG_CAPTION_PETITIONURL."</span> <span class=\"td\" style=\"font-family:monospace\">".$baseURL."/petition_sign.php?handle=".htmlspecialchars($petition['handle'], ENT_COMPAT | ENT_XML1, "UTF-8")."</span>\n".
     "          </div>\n".
     "          <div class=\"tr\">\n".
     "            <span class=\"th\">".LANG_PETITIONTITLECAPTION."</span> <span class=\"td\">".htmlspecialchars($petition['title'], ENT_COMPAT | ENT_XML1, "UTF-8")."</span>\n".
     "          </div>\n".
     "          <div class=\"tr\">\n".
     "            <span class=\"th\">".LANG_PETITIONDESCRIPTIONCAPTION."</span> <span class=\"td\">".htmlspecialchars($petition['description'], ENT_COMPAT | ENT_XML1, "UTF-8")."</span>\n".
     "          </div>\n".
     "          <div class=\"tr\">\n".
     "            <span class=\"th\">".LANG_CAPTION_PETITIONSTATUS."</span> <span class=\"td\">".StatusToDisplayText($petition['status'])."</span>\n".
     "          </div>\n".
     "          <div class=\"tr\">\n".
     "            <span class=\"th\">".LANG_CAPTION_PETITIONCREATED."</span> <span class=\"td\">".htmlspecialchars($petition['datetime_created'], ENT_COMPAT | ENT_XML1, "UTF-8")."</span>\n".
     "          </div>\n".
     "          <div class=\"tr\">\n".
     "            <span class=\"th\">".LANG_PETITIONENDCAPTION."</span> <span class=\"td\">".htmlspecialchars($petition['datetime_end'], ENT_COMPAT | ENT_XML1, "UTF-8")."</span>\n".
     "          </div>\n".
     "        </div>\n";

$signatures = GetSignatures((int)$petition['id']);

if (is_array($signatures) === true)
{
    echo "        <table border=\"1\">\n".
         "          <thead>\n".
         "            <tr>\n".
         "              <th>".LANG_CAPTION_SIGNATURENAME."</th>\n".
         "              <th>".LANG_CAPTION_SIGNATUREZIPCODE."</th>\n".
         "              <th>".LANG_CAPTION_SIGNATURECITY."</th>\n".
         "              <th>".LANG_CAPTION_SIGNATURESIGNED."</th>\n".
         "            </tr>\n".
         "          </thead>\n".
         "          <tbody>\n";

    foreach ($signatures as $signature)
    {
        echo "            <tr>\n".
             "              <td>".htmlspecialchars($signature['name'], ENT_COMPAT | ENT_XML1, "UTF-8")."</td>\n".
             "              <td>".htmlspecialchars($signature['zip_code'], ENT_COMPAT | ENT_XML1, "UTF-8")."</td>\n".
             "              <td>".htmlspecialchars($signature['city'], ENT_COMPAT | ENT_XML1, "UTF-8")."</td>\n".
             "              <td>".htmlspecialchars($signature['datetime_signed'], ENT_COMPAT | ENT_XML1, "UTF-8")."</td>\n".
             "            </tr>\n";
    }

    echo "          </tbody>\n".
         "        </table>\n";
}

echo "        <form action=\"petitions_list.php\" method=\"post\">\n".
     "          <fieldset>\n".
     "            <input type=\"submit\" value=\"".LANG_BACK."\"/>\n".
     "          </fieldset>\n".
     "        </form>\n".
     "      </div>\n".
     "    </div>\n".
     "    <div class=\"footerbox\">\n".
     "      <a href=\"license.php\" class=\"footerbox_link\">".LANG_LICENSE."</a>\n".
     "    </div>\n".
     "  </body>\n".
     "</html>\n";



function StatusToDisplayText($status)
{
    switch ((int)$status)
    {
    case PETITION_STATUS_UNLISTED:
        return LANG_CAPTION_PETITIONSTATUSUNLISTED;
    case PETITION_STATUS_LISTED:
        return LANG_CAPTION_PETITIONSTATUSLISTED;
    case PETITION_STATUS_TRASHED:
        return LANG_CAPTION_PETITIONSTATUSTRASHED;
    default:
        return "?";
    }
}



?>
