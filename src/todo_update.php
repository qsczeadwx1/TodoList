<?php
define( "SRC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/PHP_1STPJ-main/src/" );  // SRC_ROOT라는 상수를 정의하고, '$_SERVER["DOCUMENT_ROOT"]와
define( "URL_DB", SRC_ROOT."common/db_connect.php" );                    // "/PHP_1STPJ-main/src/" 를 결합하여 값으로 설정합니다.
include_once( URL_DB );                       // URL_DB 라는 상수를 정의하고 'SRC_ROOT'와 "common/db_connect.php"를 결합하여 값으로 설정합니다. 
// URL_DB 파일을 포함시킵니다.

$http_method = $_SERVER["REQUEST_METHOD"]; // $_SERVER["REQUEST_METHOD"]의 값이 "GET"이면 다음을 수행합니다. 
                                                
if( $http_method === "GET" )
{
  $list_no = 1;
  if( array_key_exists( "list_no", $_GET ) )
  {
    $list_no = $_GET["list_no"];
  }
  $result_info = select_list_info_no( $list_no );

  $result_imp_flg = $result_info["list_imp_flg"];
    if($result_imp_flg == "1" )
    {
      $one_1 = "checked";
    }
    else
    {
      $one_1 ="";
    }
// 1. list_no 변수를 1로 설정합니다.
// 2. $_GET 배열에서 "list_no" 키가 존재하는 경우, 해당 값을 $list_no 변수에 할당합니다.
// 3. 'select_list_info_no' 함수를 호출하여 '$list_no'에 해당하는 정보를 가져옵니다.
// 4. '$result_imp_flg' 변수에 '$result_info["list_imp_flg"] 값을 항당합니다.
// 5. list_imp_flg 값이 1이면 $one_1 변수에 "checked" 문자열을, 그렇지 않으면 빈 문자열을 할당합니다. 
}
else
{
  // 그렇지 않은 경우 (즉, `$_SERVER["REQUEST_METHOD"]'의 값이 "GET"이 아닌 경우) 다음을 수행합니다.
  // 1. `$arr_post` 변수에 `$_POST` 배열을 할당합니다. 
  // 2. `$list_imp_flg' 변수를 초기화 합니다. `$arr_post["list_imp.flg"]` 값이 설정되어 있다면 
  // 1을, 그렇지 않으면 0을 할당합니다.
  // 3. `$arr_info` 배열을 초기화합니다. 이 배열은 `update_todo_list_info_no` 함수에 전달됩니다.
  // 4. `$arr_info` 배열에 "list_no", "list_title", "list_detail", "list_start_date", "list_due_date", "list_imp_flg" 키와 
  // 그에 해당하는 값들을 할당합니다.
  // 5. `update_todo_list_info_no` 함수를 호출하여 `$arr_info` 배열의 내용을 데이터베이스에 업데이트 합니다.
  // 6. `header` 함수를 사용하여 `todo_detail.php` 페이지로 이동합니다. URL 매개변수로 `list_no`와 `list_start_date` 값을 전달합니다.

  $arr_post = $_POST;
  $list_imp_flg = isset($arr_post["list_imp_flg"]) ? 1 : 0;
  $arr_info = 
  array(
    "list_no" => $arr_post["list_no"],
    "list_title" => $arr_post["list_title"],
    "list_detail" => $arr_post["list_detail"],
    "list_start_date" => $arr_post["list_start_date"],
    "list_due_date" => $arr_post["list_due_date"],
    "list_imp_flg" => $list_imp_flg
        );

  $result_cnt = update_todo_list_info_no( $arr_info );  

  header( "Location: todo_detail.php?list_no=".$arr_post["list_no"]."&list_start_date=".substr($arr_post["list_start_date"],0,10) );
  exit();
}
?>

<!-- HTML 페이지의 내용을 출력합니다. 
 `$_GET`, `$_POST`, $_SERVER` 전역 변수를 사용하여 HTTP 요청의
 내용을 처리합니다. 또한 데이터베이스에 대한 작업을 수행하는 함수를 호출합니다.
 HTML 페이지를 생성하기 위해 PHP를 사용하며, 이 페이지에서는 HTML 폼을 사용하여 사용자
 로부터 정보를 입력 받을 수 있습니다.
-->

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel='stylesheet' href='../src/css/todo_update_c.css'>
<title>To do list : 수정</title>
<link rel="icon" href="common/img/magic-book.png"> <!-- 해당 코드는 웹페이지에서 사용될 아이콘을 설정하는 코드입니다. 
      웹페이지의 탭 상단에 표시되는 아이콘, 즉 '파비콘 (favicon)'을 설정하는 코드입니다. 'href' 속성에는 아이콘 파일의 경로가 들어갑니다. 
      이 경우, 'common/img/magic-book.png' 경로의 이미지를 아이콘으로 사용하게 됩니다. -->
</head>
<body>
  <div class= "container">
    <div class="header">
      <a href="todo_index.php"><img src="common/img/title.png" alt="title"></a>
    </div>
    <div class="contents_outside">
      <form class="form_contents" method="post" action="todo_update.php">
        <div class="contents_container">
          <input type="hidden" name="list_no" value="<?php echo $result_info["list_no"] ?>">        
            <div class="contents_title">
            <label for="list_title">퀘스트 제목</label>
            <input class="list_title_1" type="text" name="list_title" id="list_title" value="<?php echo $result_info["list_title"] ?>" required>
            <!-- $result_info라는 변수에서 list_' '이라는 키 값을 가진 데이터를 가져와서 출력하는 코드입니다. 출력된 결과는 value 속성에 할당됩니다. -->
            </div>
          <br>
            <div class="contents_detail">
            <label for="list_detail">퀘스트 내용</label>
            <input  class="list_detail_1" type="text" name="list_detail" id="list_detail" value="<?php echo $result_info["list_detail"] ?>">
            </div>
          <br>
              <div class="start_end_date">
            <span class="start_date_dir">
                <label for="list_start_date">시작 날짜</label>
                <input class="start_1" type="datetime-local" name="list_start_date" id="list_start_date" value="<?php echo substr($result_info["list_start_date"],0,16) ?>" required>
            </span>
            <span class="end_date_dir">
                <label for="list_due_date">마감 날짜</label>
                <input class="end_1" type="datetime-local" name="list_due_date" id="list_due_date" value="<?php echo substr($result_info["list_due_date"],0,16) ?>" required>
            </span>
            </div>
        <br>
            <div class="contents_imp">
                  <input type="checkbox" name="list_imp_flg" id="list_imp_flg" value="1" <?php echo $one_1; ?>>
                  <label for="list_imp_flg">중요</label>
            </div>
            </div>
          <div class="submit_button">
            <a href = "todo_delete.php?list_no=<?php echo $result_info["list_no"]."&list_start_date=".substr($result_info["list_start_date"],0,10) ?>"><button class="delete_1" type="button">포기</button></a>
            <a href = "todo_detail.php?list_no=<?php echo $result_info["list_no"]."&list_start_date=".substr($result_info["list_start_date"],0,10) ?>"><button class="cancel_1" type="button">취소</button></a> 
            <button class="edit_1" type="submit">수정</button>
          </div>
      </form>
    </div>
  <div>
</body> 
</html>