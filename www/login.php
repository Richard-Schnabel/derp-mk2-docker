<!DOCTYPE html>
<html lang='cs'>
<head>
    <title>Login - DERP</title>
    <meta charset='utf-8' />

    <!-- reference na další soubory -->
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css" />

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="res/favicon.png" />
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

        <!-- main pole -->
        <article class="round_hodne">

            <!-- login pole -->
            <div class="login round_hodne">
                <form method='get' action='login.php'>
                    <h2>LOGIN</h2>
                    <table>
                        <tr>
                            <td>Uživatel:</td>
                            <td>
                                <input type="text" id="nick" name="nick" />
                            </td>
                        </tr>
                        <tr>
                            <td>Heslo:</td>
                            <td>
                                <input type="password" id="pass" name="pass" />
                            </td>
                        </tr>
                    </table>
                    <input type="submit" value="Přihlásit se" />
                </form>

                <?php

                $debugMode = false;

                //z URl info
                $usernick = isset($_GET["nick"]) ? $_GET["nick"] : "";
                $userpass = isset($_GET["pass"]) ? $_GET["pass"]  : "";

                //přihlašovací údaje k mé databázi z FreeSqlDatabase.com
                $server = "sql7.freesqldatabase.com";
                $user   = "sql7600278";
                $pass   = "IGl6XIDQWs";
                $base   = "sql7600278";

                //spoj se s databází
                $spojeni = mysqli_connect($server, $user, $pass, $base);

                function provedSQL($SQLprikaz){

                    global $server, $user, $pass, $base;

                    return(mysqli_query(mysqli_connect($server, $user, $pass, $base), $SQLprikaz));
                }

                //pokud nastala chyba, řekni chyba a nepokračuj, jinak řekni vše ok
                if (!$spojeni) {
                    mysqli_close($spojeni);
                    die($debugMode ? "Chyba připojení k databázi: " . mysqli_connect_error() : "");
                }
                echo($debugMode ? "Připojení k databázi proběhlo v pořádku.<br>" : "");


                //pokud jsi na stráne poprve tak nepokracuj
                if($usernick == "" and $userpass == "") {
                    mysqli_close($spojeni);
                    die("");
                }

                //pokud neni vyplneno jedno z inputu tak nepokracuj
                if($usernick == "" or $userpass == "") {
                    mysqli_close($spojeni);
                    die("<br><b>Uživatel nebo Heslo nevyplňeno<b>");
                }

                $vystup = provedSQL("SELECT * FROM user");

                while($radek = mysqli_fetch_assoc($vystup)) {
                    if($usernick == $radek["nick"] and $userpass = $radek["pass"]) {
                        mysqli_close($spojeni);
                        die("<br><b>Přihlášení proběhlo v pořádku. Prosím čekejte<b><script>
                                window.open('index.php?nick=$usernick&pass=$userpass', '_self');
                            </script>");
                    }
                }

                print("Špatné Uživatelské jméno nebo Heslo");
                mysqli_close($spojeni);
                ?>
            </div>
        </article>
    </main>
</body>
</html>