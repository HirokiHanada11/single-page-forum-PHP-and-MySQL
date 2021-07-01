<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>西日本を旅行するなら</title>
</head>
<body>
    <?php
        //database setup
        $dsn = 'mysql:dbname=**********;host=localhost';
        $user = '*********';
        $password = '**********';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        //creating a table if it does not exist
        $sql = "CREATE TABLE IF NOT EXISTS forumdb"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "postDate TEXT,"
        . "password TEXT"
        .");";
        
        $stmt = $pdo->query($sql);
        $filename = "mission3-5.txt";
        
        //edit handling UPDATE
        if(!empty($_POST["edit"]) && !empty($_POST["password"])){
            $edit = $_POST["edit"];
            $password = $_POST["password"];
            
            $sql = 'SELECT * FROM forumdb WHERE id=:id AND password=:password';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindParam(':id', intval($edit), PDO::PARAM_INT);
            $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
            $stmt -> execute();
            $result = $stmt -> fetchAll();
            
            //setting the form value
            if(!empty($result)){
                $comment = $result[0]["comment"];
                $name = $result[0]["name"];
            } else{
                $edit = '';
            }
        }        
        //new entry and edit handling
        if(!empty($_POST["comment"]) && !empty($_POST["name"])){
            $comment = $_POST["comment"];
            $name = $_POST["name"];
            //editing
            if(!empty($_POST["edit_num"])){
                $edit_num = $_POST["edit_num"];
                //update db
                $sql = 'UPDATE forumdb SET name=:name, comment=:comment WHERE id=:id';
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);    
                $stmt -> bindParam(':id', intval($edit_num), PDO::PARAM_INT);
                $stmt -> execute();


            }else{
                //inserting new entries
                $password = (!empty($_POST["password"])) ? $_POST["password"] : '';
                $post_date = date("Y/m/d/ H:i:s");
                
                $sql = $pdo -> prepare("INSERT INTO forumdb (name , comment, postDate, password) VALUES (:name, :comment, :postDate, :password)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':postDate', $post_date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql -> execute();
            }
        //delete handling
        } elseif(!empty($_POST["delete"]) && !empty($_POST["password"])){
            $delete = $_POST["delete"];
            $password = $_POST["password"];
            
            //preparing delete sql
            $sql = 'DELETE FROM forumdb WHERE id=:id AND password=:password';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':id', intval($delete), PDO::PARAM_INT);
            $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
            $stmt -> execute();
        } 
    ?>
    <h1>西日本　おすすめ　掲示板</h1>
    <h2>
        名古屋より西に行ったことがないので、西日本を旅行したいと考えています。<br>
        おすすめの食べ物や旅行先があれば教えてください！
    </h2>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="お名前" style="width:300px" 
        value="<?php echo (!empty($_POST["edit"]) && !empty($name)) ? $name : ''; ?>"><br>
        <input type="text" name="comment" placeholder="コメント" style="width:300px"
        value="<?php echo (!empty($_POST["edit"]) && !empty($comment)) ? $comment : ''; ?>"><br>
        <input type="text" name="password" placeholder="パスワード" style="width:300px"><br>
        <input type ="hidden" name="edit_num" 
        value="<?php echo (!empty($_POST["edit"])) ? $edit : ''; ?>"><br>
        <input type="submit" name="submit" value="投稿">
    </form>
    
    <form method="POST" action="">
        削除したい投稿の投稿番号とパスワードを入力:<br>
        <input type="number" name="delete">
        <input type="text" name="password" placeholder="パスワード" style="width:150px"><br>
        <input type="submit" value="削除">
    </form>
    
    <form method="POST" action="">
        編集したい投稿の投稿番号とパスワードを入力:<br>
        <input type="number" name="edit">
        <input type="text" name="password" placeholder="パスワード" style="width:150px"><br>
        <input type="submit" value="編集">
    </form>
    <br><hr>
    <h3>投稿：</h3>
    <?php
        $sql = 'SELECT * FROM forumdb';
        $stmt = $pdo->query($sql);
        $posts = $stmt->fetchAll();
        foreach($posts as $post){
            echo "投稿番号：".$post["id"]."<br><b>".$post["name"]."</b> -- ".$post["postDate"]."<br>";
            echo ">>>".$post["comment"]."<br>";
            echo "  パスワード：".$post["password"]."<br><hr>";
        }

    ?>

</body>