<!DOCTYPE html>
<html lang='cs'>
    <head>
        <title>DERP</title>
        <meta charset='utf-8'>
    
        <!-- reference na další soubory -->
        <script src="script.js"></script>
        <link rel="stylesheet" href="style.css">

        <!-- favicon -->
        <link rel="icon" type="image/x-icon" href="res/favicon.png">
    </head>
    <body onload="OnLoad()">

        <!-- logo -->
        <header>
            <h1 onclick="location.href='.'">DERP</h1>
            <h2>Developer's Engine for Recording Programing</h2>
        </header>

        <!-- delici cara -->
        <div class="cerna_cara"></div>

        <!-- main -->
        <main class="gradientBG">

            <!-- back button -->
            <div class="div_ramecek round_malo div_pravo">
                <span class="div_text">Back</span>
                <img src="res/back.png" id="back_button" onclick="alert('chyba')" class="round_malo" width="35px">
            </div>

            <!-- misto pro zaznamy-->
            <article class="round_hodne">
                <?php

                $debugMode = false;

                $mod = isset($_GET["mod"]) ? $_GET["mod"]  : "0";
                $usernick = SQLi(isset($_GET["nick"]) ? $_GET["nick"] : "");
                $userpass = SQLi(isset($_GET["pass"]) ? $_GET["pass"]  : "");

                //přihlašovací údaje k mé databázi z FreeSqlDatabase.com
                $server = "sql7.freesqldatabase.com";
                $user   = "sql7600278";
                $pass   = "IGl6XIDQWs";
                $base   = "sql7600278";

                //spoj se s databází
                $spojeni = mysqli_connect($server, $user, $pass, $base);

                function provedSQL($SQLprikaz){
                    global $server, $user, $pass, $base;
                    $fspojeni = mysqli_connect($server, $user, $pass, $base);
                    $query = mysqli_query($fspojeni, $SQLprikaz);
                    mysqli_close($fspojeni);
                    return($query);
                }

                function SQLi($input) {
                    $input = str_replace("'","&#39;",$input);
                    $input = str_replace("<","&#60;",$input);
                    $input = str_replace(">","&#62;",$input);
                    $input = str_replace(";","&#59;",$input);
                    $input = str_replace("=","&#61;",$input);

                    return($input);
                }

                //pokud nastala chyba, řekni chyba a nepokračuj, jinak řekni vše ok
                if (!$spojeni) {
                    die($debugMode ? "Chyba připojení k databázi: " . mysqli_connect_error() : "");
                }
                echo($debugMode ? "Připojení k databázi proběhlo v pořádku.<br>" : "");

                //login kontrola
                $prihlasen = false;
                $admin = false;
                $users = array();
                $vysledek = provedSQL("SELECT * FROM user");
                while($radek = mysqli_fetch_assoc($vysledek)) {
                    $users += array($radek["id"] => $radek["nick"]);
                    if($usernick == $radek["nick"] and $userpass == $radek["pass"]) {
                        $prihlasen = true;
                        $admin = $radek["admin"];
                    }
                }
                //bez na login pokud nepruhlasen
                if(!$prihlasen){
                    die("<script>
                        window.open('login.php', '_self');
                    </script>");
                }
                echo($debugMode ? "Přihlášení proběhlo v pořádku.<br>" : "");
                echo("<script>
                    document.getElementById('back_button').onclick  = function jsFunc() {

                        window.open(".'"'."index.php?nick=$usernick&pass=$userpass".'"'.", '_self');

                    };
                </script>");

                //pokud máme v url adrese ?mod=mazani, spustíme proces mazání zvoleného záznamu
                if ($mod == "mazani") {
                    $mazany = $_GET["zaznam"];  //zjisti z URL id záznamu, který má zmizet
                    $vystup = provedSQL("DELETE from user WHERE (id = '$mazany')");  //vlez do naší databáze a proveď příkaz

                    echo($debugMode ? "<b>Záznam, jenž už nevidíte, byl úspěšně zmizen.</b><br>" : "");
                }

                //pokud máme v url adrese ?mod=vlozeni, spustíme proces vkládání nového záznamu
                if ($mod == "vlozeni") {
                    //ulož hodnoty z formuláře
                    $newnick = SQLi($_POST["newnick"]);
                    $newpass = SQLi($_POST["newpass"]);
                    $newadmin = SQLi($_POST["newadmin"]);

                    $vystup = provedSQL("INSERT INTO user (nick, pass, admin) VALUES ('$newnick', '$newpass', '$newadmin')"); //vlez do naší databáze a proveď příkaz

                    //pokud chyba tak křič, pokud ne, tak taky
                    if (!$vystup) {
                        echo($debugMode ? "Chyba: " . mysqli_error($spojeni) : "");
                    } else {
                        echo($debugMode ? "<b>Záznam úspěšně zrozen!</b><br>" : "");
                    }

                }

                //pokud máme v url adrese ?mod=vlozeni, spustíme proces vkládání nového záznamu
                if ($mod == "edithotov") {
                    $editovany = $_GET["zaznam"];  //zjisti z URL id záznamu, který se má změnit
                    //ulož hodnoty z formuláře
                    $newnick = SQLi($_POST["newnick"]);
                    $newpass = SQLi($_POST["newpass"]);
                    $newadmin = SQLi($_POST["newadmin"]);

                    $sql = "UPDATE user SET nick = '$newnick', pass = '$newpass', admin = '$newadmin' WHERE user.id = '$editovany'";
                    echo($debugMode ? $sql . "<br>" : "");
                    $vystup =  provedSQL($sql); //vlez do naší databáze a proveď příkaz

                    //pokud chyba tak křič, pokud ne, tak taky
                    if (!$vystup) {
                        echo($debugMode ? "Chyba: " . mysqli_error($spojeni) : "");
                    } else {
                        echo($debugMode ? "<b>Záznam úspěšně editován!</b><br>" : "");
                    }

                }

                $sql = "SELECT * FROM user";
                echo($debugMode ? $sql . "<br>": "");
                $vystup = provedSQL($sql); //vlez do naší databáze a proveď příkaz, tj. vytáhni data

                echo("
                    <table class='usertable'>
                        <tr>
                            <th>Jméno</th>
                            <th>Heslo</th>
                            <th>Admin? (y/n)</th>
                        </tr>
                ");
                //urob bunku
                while($radek = mysqli_fetch_assoc($vystup)) {
                    //pokud máme v url adrese ?mod=edit, vytvoříme edit formulář
                    if ($mod == "edit" && $radek["id"] == $_GET["zaznam"]) {
                        echo("
                            <form method='post' action='admin.php?nick=$usernick&pass=$userpass&mod=edithotov&zaznam=".$radek["id"]."'>
                                <tr class='usertr'>
                                    <td><input type='text' id='newnick' name='newnick' value='".$radek["nick"]."'></td>
                                    <td><input type='text' id='newpass' name='newpass' value='".$radek["pass"]."'></td>
                                    <td><input type='number' min=0 max=1 id='newadmin' name='newadmin' value='".$radek["admin"]."'></td>
                                    <td><a href='admin.php?&mod=mazani&zaznam=".$radek["id"]."&nick=$usernick&pass=$userpass'><img src='res/del.png' class='icon'></a></td>
                                    <td><input type='image' src='res/done.png' class='icon'></td>
                                </tr>
                            </form>
                        ");
                    }
                    else {
                        echo("
                            <tr class='usertr'>
                                <td>".$radek["nick"]."</td>
                                <td>".$radek["pass"]."</td>
                                <td>".$radek["admin"]."</td>
                                <td><a href='admin.php?mod=edit&zaznam=$radek[id]&nick=$usernick&pass=$userpass'><img src='res/edit.png' class='round_malo icon'></a></td>
                            </tr>
                        ");
                    }
                }

                //vlož řádek s formulářem
                echo("
                    <form method='post' action='admin.php?mod=vlozeni&nick=$usernick&pass=$userpass'>
                        <tr id='add_new' class='usertr'>
                            <td><input type='text' id='newnick' name='newnick' placeholder='Jméno'></td>
                            <td><input type='text' id='newpass' name='newpass' placeholder='Heslo'></td>
                            <td><input type='number' min=0 max=1 id='newadmin' name='newadmin' placeholder='(0/1)'></td>
                            <td><input type='image' src='res/done.png' class='icon'></td>
                        </tr>
                    </form>
                ");
                echo("</table>");
                mysqli_close($spojeni);
                ?>
                
            </article>

            <!-- add button -->
            <div class="div_ramecek round_malo div_levo">
                <span class="div_text" id="login_as">Nový</span>
                <img src="res/add.png" onclick="ShowForm()" class="round_malo" width="35px" />
            </div>
        
        </main>
    </body>
</html>