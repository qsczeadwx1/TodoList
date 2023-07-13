<?php
    define( "SRC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/PHP_1STPJ-main/src/" );
    define( "URL_DB", SRC_ROOT."common/db_connect.php" );
    include_once( URL_DB );

    $http_method = $_SERVER["REQUEST_METHOD"];

    if( $http_method === "GET")
    {
        $list_no = 1;
        if( array_key_exists( "list_no", $_GET ) )
        {
            $list_no = $_GET["list_no"];
        }
        else
        {
            header( "Location: todo_index.php" );
        }
        $result_info = select_list_info_no($list_no);
    }
    if($http_method === "POST")
    {
        $result_cnt = delete_todo_info( $_GET["list_no"] );
        header( "Location: todo_index.php" );
        exit;
    }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List : 삭제</title>
    <link rel="stylesheet" href="css/todo_delete_c.css">
    <link rel="icon" href="common/img/magic-book.png">
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="todo_index.php"><img src="common/img/title.png" alt="title"></a>
        </div>
        <div class="delete_outside">
            <form action="todo_delete.php" method="get">
            <div class="delete_container">
                <div class="contents_title">
                    <label for="title">퀘스트 제목</label>
                    <input type="text" id="title" name="todo_title" value="<?php echo $result_info["list_title"]?>" readonly>
                </div>
                <div class="contents_detail">
                    <label for="contents">퀘스트 내용</label>
                    <input type="text" id="contents" name="todo_contents" value="<?php echo $result_info["list_detail"]?> "readonly>
                </div>
                <p>퀘스트를 포기 하시겠습니까?
            <br>
            <br>
                주의! 포기한 퀘스트는 사라집니다</p>
            </div>
            </form>
            <div class="submit_button">
                <form method="post" action="todo_delete.php?list_no=<?php echo $_GET["list_no"]."&list_start_date=".$_GET["list_start_date"] ?>">
                <a href="todo_detail.php?list_no=<?php echo $list_no."&list_start_date=".$_GET["list_start_date"] ?>"><button type="button">취소</button></a>
                    <button type="submit">포기</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

