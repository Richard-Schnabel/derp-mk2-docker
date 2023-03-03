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

            <!-- filter button -->
            <div class="div_ramecek round_malo div_levo">
                <span class="div_text">Filter</span>
                <img src="res/icon_filter.jpeg" class="round_malo" id="filterBtn" width="35px">

                <!-- filter popup - pozadi -->
                <div id="filterGUI" class="modal">

                    <!-- filter popup - content -->
                    <div class="modal-content round_hodne gradientBG">

                        <!-- X button -->
                        <span class="close">&times;</span>

                        <!-- filter formular -->
                        <form>
                            <table class="filterTable">
                                <tr>
                                    <th>Hledat od:</th>
                                    <th>Hledat do:</th>
                                    <th width="100px"></th>
                                    <th>Seřadit podle:</th>
                                </tr>
                                <tr>
                                    <td><input type="date" id="fdateod"></td>
                                    <td><input type="date" id="fdatedo"></td>
                                    <td></td>
                                    <td><select id="ssortby">
                                        <option value="2">Datum</option>
                                        <option selected value="0">Čas vytvoření</option>
                                        <option value="4">Jazyk</option>
                                        <option value="6">Čas</option>
                                        <option value="8">Hodnocení</option>
                                    </select></td>
                                </tr>
                                <tr>
                                    <td><input type="text" id="fjazyk" placeholder="Jazyk"></td>
                                    <td></td>
                                    <td></td>
                                    <td><select id="ssortor">
                                        <option selected value="0">Vzestupně</option>
                                        <option value="1">Sestupně</option>
                                    </select></td>
                                </tr>
                                <tr>
                                    <td><input type="number" id="ftimeod" placeholder="Čas od"></td>
                                    <td><input type="number" id="ftimedo" placeholder="Čas do"></td>
                                </tr>
                                <tr>
                                    <td><select id="frateod">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select></td>
                                    <td><select id="fratedo">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option selected value="5">5</option>
                                    </select></td>
                                </tr>
                                <tr>
                                    <td height="50px"><img src=res/done.png value="Filtr" onclick="Filtr()" class="icon"></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>

            <!-- login button -->
            <div class="div_ramecek round_malo div_pravo">
                <span class="div_text" id="login_as">Logged in: Admin</span>
                <img src="res/icon_admin.jpeg" onclick="window.location='login.php'" class="round_malo" width="35px">
            </div>
            
            <!-- account button -->
            <div id="account_button">
                <div class="div_ramecek round_malo div_pravo">
                    <span class="div_text">Edit Users</span>
                    <img src="res/edit.png" id="admin_button" class="round_malo" width="35px">
                </div>
            </div>

            <!-- misto pro zaznamy-->
            <article class="round_hodne">
                <?php

                $debugMode = false;

                //z URl poznej jak se má list řadit, pokud není nastaven tak podle 0
                $sort = isset($_GET["sort"]) ? $_GET["sort"] : "0";
                $mod = isset($_GET["mod"]) ? $_GET["mod"]  : "0";
                $filtr = isset($_GET["filtr"]) ? $_GET["filtr"]  : "";
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
                    $input = str_replace("'","&#39",$input);
                    $input = str_replace("<","&#60",$input);
                    $input = str_replace(">","&#62",$input);
                    $input = str_replace(";","&#59",$input);
                    $input = str_replace("=","&#61",$input);
                    
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
                $userid;
                while($radek = mysqli_fetch_assoc($vysledek)) {
                    $users += array($radek["id"] => $radek["nick"]);
                    if($usernick == $radek["nick"] and $userpass == $radek["pass"]) {
                        $prihlasen = true;
                        $admin = $radek["admin"];
                        $userid = $radek["id"];
                    }
                }
                //bez na login pokud nepruhlasen
                if(!$prihlasen){
                    mysqli_close($spojeni);
                    die("<script>
                        window.open('login.php', '_self');
                    </script>");
                }
                echo($debugMode ? "Přihlášení proběhlo v pořádku.<br>" : "");
                echo("<script>
                    document.getElementById('login_as').innerHTML = 'Logged in: $usernick';
                    login = '&nick=$usernick&pass=$userpass';
                    document.getElementById('admin_button').setAttribute('onclick','window.location=".'"'."admin.php?nick=$usernick&pass=$userpass".'"'."');
                ");
                //smas account_button pokud neadmin
                if(!$admin) {
                    echo("
                        document.getElementById('account_button').innerHTML = '';
                    ");
                }
                echo("</script>");

                //pokud máme v url adrese ?mod=mazani, spustíme proces mazání zvoleného záznamu
                if ($mod == "mazani") {
                    $mazany = $_GET["zaznam"];  //zjisti z URL id záznamu, který má zmizet
                    $vystup = provedSQL("DELETE from log WHERE (id = '$mazany')");  //vlez do naší databáze a proveď příkaz

                    echo($debugMode ? "<b>Záznam, jenž už nevidíte, byl úspěšně zmizen.</b><br>" : "");
                }

                //pokud máme v url adrese ?mod=vlozeni, spustíme proces vkládání nového záznamu
                if ($mod == "vlozeni") {
                    //ulož hodnoty z formuláře
                    $date = SQLi($_POST["date"]);
                    $jazyk = SQLi($_POST["jazyk"]);
                    $time = SQLi($_POST["time"]);
                    $rate = SQLi($_POST["rate"]);
                    $popis = SQLi($_POST["popis"]);
                    $vystup = provedSQL("INSERT INTO log (date, jazyk, time, rate, user, popis) VALUES ('$date', '$jazyk', '$time', '$rate', '$userid', '$popis')"); //vlez do naší databáze a proveď příkaz

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
                    $date = SQLi($_POST["editdate"]);
                    $jazyk = SQLi($_POST["editjazyk"]);
                    $time = SQLi($_POST["edittime"]);
                    $rate = SQLi($_POST["editrate"]);
                    $popis = SQLi($_POST["editpopis"]);

                    $vystup =  provedSQL("UPDATE log SET date = '$date', jazyk = '$jazyk', time = '$time', rate = '$rate', popis = '$popis' WHERE log.id = '$editovany'"); //vlez do naší databáze a proveď příkaz

                    //pokud chyba tak křič, pokud ne, tak taky
                    if (!$vystup) {
                        echo($debugMode ? "Chyba: " . mysqli_error($spojeni) : "");
                    } else {
                        echo($debugMode ? "<b>Záznam úspěšně editován!</b><br>" : "");
                    }

                }

                //seřad list podle:
                $order = ["id ASC","id DESC","date ASC","date DESC","jazyk ASC","jazyk DESC","time ASC","time DESC","rate ASC","rate DESC"]; //seznam všech podporovaných sortů

                $filtr2 = str_replace("→", ">", str_replace("←", "<", str_replace("↨", "'", $filtr)));
                $sql = "SELECT * FROM log$filtr2 ORDER BY " . $order[$sort]; //VYBER vše Z log SEŘAZENO PODLE /sort v url/
                echo($debugMode ? $sql . "<br>": "");
                $vystup = provedSQL($sql); //vlez do naší databáze a proveď příkaz, tj. vytáhni data

                //urob bunku
                while($radek = mysqli_fetch_assoc($vystup)) {
                    //pokud máme v url adrese ?mod=edit, vytvoříme edit formulář
                    if ($mod == "edit" && $radek["id"] == $_GET["zaznam"]) {
                        echo("
                            <form method='post' action='index.php?sort=$sort&mod=edithotov&zaznam=".$radek["id"]."&filtr=$filtr&nick=$usernick&pass=$userpass'>
                                <table class='zaznam round_malo edit'>
                                    <tr>
                                        <td>User: $usernick</td>
                                        <td><input type='date' id='editdate' name='editdate' value='".$radek["date"]."'></td>
                                    </tr>
                                    <tr>
                                        <td><input type='text' id='editjazyk' name='editjazyk' value='".$radek["jazyk"]."'></td>
                                        <td><input type='number' id='edittime' name='edittime' min=0 value='".$radek["time"]."'></td>
                                    </tr>
                                    <tr>
                                        <td><textarea id='editpopis' name='editpopis'>".$radek["popis"]."</textarea></td>
                                        <td><select name='editrate' id='editrate'>
                                            <option value=1".($radek["rate"]==1?" selected":"").">1</option>
                                            <option value=2".($radek["rate"]==2?" selected":"").">2</option>
                                            <option value=3".($radek["rate"]==3?" selected":"").">3</option>
                                            <option value=4".($radek["rate"]==4?" selected":"").">4</option>
                                            <option value=5".($radek["rate"]==5?" selected":"").">5</option>
                                        </select></td>
                                    </tr>
                                    <tr>
                                        <td><a href='index.php?sort=$sort&mod=mazani&zaznam=".$radek["id"]."&filtr=$filtr&nick=$usernick&pass=$userpass'><img src='res/del.png' class='icon'></a></td>
                                        <td><input type='image' src='res/done.png' class='icon'></td>
                                    </tr>
                                </table>
                            </form>
                        ");
                    }
                    else {
                        echo("
                            <table class='zaznam round_malo'>
                                <tr>
                                    <td>".$users[$radek["user"]]."</td>
                                    <td>".$radek["date"]."</td>
                                </tr>
                                <tr>
                                    <td>".$radek["jazyk"]."</td>
                                    <td>".$radek["time"]." min</td>
                                </tr>
                                <tr>
                                    <td>".$radek["popis"]."</td>
                                    <td>".$radek["rate"]."/5</td>
                                </tr>
                                <tr>
                                    <td></td>");
                        if ($admin or $users[$radek["user"]] == $usernick) {
                            echo("
                                    <td><a href='index.php?sort=$sort&mod=edit&zaznam=$radek[id]&filtr=$filtr&nick=$usernick&pass=$userpass'><img src='res/edit.png' class='round_malo icon'></a></td>
                                </tr>
                            </table>");
                        } else {
                            echo("
                                    <td></td>
                                </tr>
                            </table>");
                        }
                    }
                }

                //vlož řádek s formulářem
                print("
                    <form method='post' action='index.php?sort=$sort&mod=vlozeni&filtr=$filtr&nick=$usernick&pass=$userpass'>
                        <table id='add_new' class='zaznam round_malo'>
                            <tr>
                                <td>User: $usernick</td>
                                <td><input type='date' id='date' name='date'></td>
                            </tr>
                            <tr>
                                <td><select name='jazyk' id='jazyk'>
                                    <option value='BASIC'>BASIC</option>
                                    <option value='Brainfuck'>Brainfuck</option>
                                    <option value='C'>C</option>
                                    <option value='C++'>C++</option>
                                    <option value='C#'>C#</option>
                                    <option value='CSS'>CSS</option>
                                    <option value='Desmos'>Desmos</option>
                                    <option value='HTML'>HTML</option>
                                    <option value='Java'>Java</option>
                                    <option value='JavaScript'>JavaScript</option>
                                    <option value='MATLAB'>MATLAB</option>
                                    <option value='Ook!'>Ook!</option>
                                    <option value='PHP'>PHP</option>
                                    <option value='Ruby'>Ruby</option>
                                    <option value='Python'>Python</option>
                                    <option value='Rust'>Rust</option>
                                    <option value='Scratch'>Scratch</option>
                                    <option value='Swift'>Swift</option>
                                </select></td>
                                <td><input type='number' id='time' name='time' placeholder='Zadej čas v minutách' min=0></td>
                            </tr>
                            <tr>
                                <td><textarea id='popis' name='popis' placeholder='Zadej popis'></textarea></td>
                                <td><select name='rate' id='rate'>
                                    <option value=1>1</option>
                                    <option value=2>2</option>
                                    <option value=3>3</option>
                                    <option value=4>4</option>
                                    <option value=5>5</option>
                                </select></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type='image' src='res/done.png' class='icon'></td>
                            </tr>
                        </table>
                    </form>
                ");
                mysqli_close($spojeni);

                ?>

            </article>

            <!-- add button -->
            <div class="div_ramecek round_malo div_levo">
                <span class="div_text">Nový</span>
                <img src="res/add.png" onclick="ShowForm()" class="round_malo" width="35px">
            </div>

            <!-- import button -->
            <div class="div_ramecek round_malo div_pravo">
                <form action="import.php" method="post" enctype="multipart/form-data">
                    <span class="div_text">Import</span>
                    <input class="div_text" type="file" name="sql_file" id="sql_file" style="font-size:20px;">   
                    <input type="image" src="res/add.png" class="round_malo" width="35px">
                </form>
            </div>

            <!-- export button -->
            <div class="div_ramecek round_malo div_pravo">
                <span class="div_text">Export</span>
                <img src="res/add.png" onclick="window.open('export.php')" class="round_malo" width="35px">
            </div>
        
        </main>
    </body>
</html>