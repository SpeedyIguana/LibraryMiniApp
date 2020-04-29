<html>
    <head>
        <title>Library System</title>
        <link href="../std/lib.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php
            include("../std/FuncDefs.php");
            errorReporting();
            session_start();
            $ms = mysqliOOP();
            $flag_empty=TRUE;
            $flag_isset = FALSE;
            $reader_id = POST("reader_id", $flag_empty, $flag_isset);
            if($flag_isset && !$flag_empty){
                $query = "SELECT * FROM `READER` WHERE `READERID`=$reader_id;";
                $res = $ms->query($query);
                if($res->num_rows > 0){
                    $_SESSION["logged_in"] = TRUE;
                    $_SESSION["user_type"] = "reader";
                    $_SESSION["user"] = $reader_id;
                }
            }
            mysqliCloseOOP($ms);
            if(!isReaaderLoggedIn()){
                header("Location: ../logout.php");
                exit();
            }
        ?>
        <div class = "logout">
                <a href="../logout.php">Logout</a>
        </div>
        <!--
        <div class = "back">
                <a href="../back.php">Back</a>
        </div>
        -->
        <div class = "container">
            <div class = "main">
                <h2>Library System</h2>
                <h3>Reader Submenu</h3>
                <div class="table">
                    <h3>All the books you have borrowed and the fines for those borrows:</h3>

                    <?php
                        $ms = mysqliOOP();
                        $query = "SELECT * FROM `BORROWS` WHERE `READERID`=" . $_SESSION["user"] .";";
                        $res = $ms->query($query);
                        if($res->num_rows <= 0){
                            echo("You haven't borrowed any documents yet");
                        }
                        else{?>
                        <table>
                            <tr>
                                <th>Borrowing Number</th>
                                <th>Reader ID</th>
                                <th>Document ID</th>
                                <th>Copy Number</th>
                                <th>Library ID</th>
                                <th>Borrowed DateTime</th>
                                <th>Return DateTime</th>
                                <th>Fine Incurred for that Borrow</th>
                            </tr>
                        <?php 
                            while($row = $res->fetch_assoc()){
                                echo("<tr>");
                                echo("<td>" . $row['BORNUMBER'] . "</td>");
                                echo("<td>" . $row['READERID'] . "</td>");
                                echo("<td>" . $row['DOCID'] . "</td>");
                                echo("<td>" . $row['COPYNO'] . "</td>");
                                echo("<td>" . $row['LIBID'] . "</td>");
                                echo("<td>" . $row['BDTIME'] . "</td>");
                                // echo("<td>" . $row['RDTIME'] . "</td>");
                                $borrow = $row['BDTIME'];
                                $return = $row['RDTIME'];
                                if(is_null($return)){
                                    $return = getCurrentDate();
                                    echo("<td>" . "-" . "</td>");
                                }
                                else{
                                    echo("<td>" . $row['RDTIME'] . "</td>");
                                }
                                $fine = computeFine($borrow, $return);
                                echo("<td>$" . round($fine, 2) . "</td>");
                                echo("</tr>");
                            }
                            echo("</table>");
                        }
                        mysqliCloseOOP($ms);
                    ?>
                </div>
                <form id= "reader_searchDocument" name= "reader_searchDocument" method= "post" action= "reader_searchDocument.php">
                    <h3>Search Document by Document ID, Title or publisher</h3>
                    <select id= "srchCrit" name= "srchCrit">
                        <option value= "DOCID">Document ID</option>
                        <option value= "TITLE">Document Title</option>
                        <option value= "PUBNAME">Publisher Name</option>
                    </select>
                    <input type="text" name="fn" id="fn" required = "required" maxlength= "25">
                    <input type= "submit" value= "Search">
                </form>
            </div>
        </div>
    </body>
</html>