<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (eregi("block_suggest.php", $_SERVER['PHP_SELF']))
{
    die ("You cannot open this page directly");
} 

function affich_block_suggest($blok)
{
    global $user, $nuked;

    if (!$user)
    {
        $visiteur = 0;
    } 
    else
    {
        $visiteur = $user[1];
    } 

    $blok['content'] .= "<table width=\"100%\"><tr><td>\n";

    $modules = array();
    $path = "modules/Suggest/modules/";
    $handle = opendir($path);
    while (false !== ($mod = readdir($handle)))
    {
        if ($mod != "." && $mod != "..")
        {
            $mod = str_replace(".php", "", $mod);

            if ($mod == "Gallery")
            {
                $modname = _NAVGALLERY;
            } 
            else if ($mod == "Download")
            {
                $modname = _NAVDOWNLOAD;
            } 
            else if ($mod == "Links")
            {
                $modname = _NAVLINKS;
            } 
            else if ($mod == "News")
            {
                $modname = _NAVNEWS;
            } 
            else if ($mod == "Sections")
            {
                $modname = _NAVART;
            } 
            else
            {
                $modname = $mod;
            } 

            array_push($modules, $modname . "|" . $mod);
        } 
    } 
        closedir($handle);
        natcasesort($modules);

        foreach($modules as $value)
        {
            $temp = explode("|", $value);

            $sql = mysql_query("SELECT * FROM " . SUGGEST_TABLE . " WHERE module = '" . $temp[1] . "'");
            $nb_sug = mysql_num_rows($sql);

            $level_access = nivo_mod($temp[1]);
            $level_admin = admin_mod($temp[1]);

            if ($visiteur >= $level_access && $level_access > -1)
            {
                $blok['content'] .= "&nbsp;<b><big>&middot;</big></b>&nbsp;<a href=\"index.php?file=Suggest&amp;module=" . $temp[1] . "\">" . $temp[0] . "</a>";
            } 

            if ($visiteur >= $level_admin && $level_admin > -1)
            {
                if ($nb_sug > 0)
                {
                    $blok['content'] .= "&nbsp;(<a href=\"index.php?file=Suggest&amp;page=admin\">" . $nb_sug . "</a>)<br />\n";
                } 
                else
                {
                    $blok['content'] .= "&nbsp;(" . $nb_sug . ")<br />\n";
                } 
            } 
            else if ($visiteur >= $level_access && $level_access > -1)
            {
                $blok['content'] .= "<br />\n";
            }
        } 
    $blok['content'] .= "</td></tr></table>\n";
    return $blok;
} 


function edit_block_suggest($bid)
{
    global $nuked, $language;

    $sql = mysql_query("SELECT active, position, titre, module, content, type, nivo, page FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
    list($active, $position, $titre, $modul, $content, $type, $nivo, $pages) = mysql_fetch_array($sql);
    $titre = stripslashes($titre);
    $titre = htmlentities($titre);

    if ($active == 1) $checked1 = "selected=\"selected\"";
    else if ($active == 2) $checked2 = "selected=\"selected\"";
    else $checked0 = "selected=\"selected\"";

    echo "<a href=\"#\" onclick=\"javascript:window.open('help/" . $language . "/block.html','Help','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=300');return(false)\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a><div style=\"text-align: center;\"><h3>" . _ADMINBLOCK . "</h3></div>\n"
    . "<form method=\"post\" action=\"index.php?file=Admin&amp;page=block&amp;op=modif_block\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _TITLE . "</b></td><td><b>" . _BLOCK . "</b></td><td><b>" . _POSITION . "</b></td><td><b>" . _LEVEL . "</b></td></tr>\n"
    . "<tr><td align=\"center\"><input type=\"text\" name=\"titre\" size=\"40\" value=\"" . $titre . "\" /></td>\n"
    . "<td align=\"center\"><select name=\"active\">\n"
    . "<option value=\"1\" " . $checked1 . ">" . _LEFT . "</option>\n"
    . "<option value=\"2\" " . $checked2 . ">" . _RIGHT . "</option>\n"
    . "<option value=\"0\" " . $checked0 . ">" . _OFF . "</option></select></td>\n"
    . "<td align=\"center\"><input type=\"text\" name=\"position\" size=\"2\" value=\"" . $position . "\" /></td>\n"
    . "<td align=\"center\"><select name=\"nivo\"><option>" . $nivo . "</option>\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select></td></tr><tr><td colspan=\"4\">&nbsp;</td></tr>\n"
    . "<tr><td colspan=\"4\" align=\"center\"><b>" . _PAGESELECT . " :</b></td></tr><tr><td colspan=\"4\">&nbsp;</td></tr>\n"
    . "<tr><td colspan=\"4\" align=\"center\"><select name=\"pages[]\" size=\"8\" multiple=\"multiple\">\n";

    select_mod2($pages);

    echo "</select></td></tr><tr><td colspan=\"4\" align=\"center\"><br />\n"
    . "<input type=\"hidden\" name=\"type\" value=\"" . $type . "\" />\n"
    . "<input type=\"hidden\" name=\"bid\" value=\"" . $bid . "\" />\n"
    . "<input type=\"submit\" name=\"send\" value=\"" . _MODIFBLOCK . "\" />\n"
    . "</td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin&amp;page=block\"><b>" . _BACK . "</b></a> ]</div></form><br />\n";

}

?>