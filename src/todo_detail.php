<?php 
    define( "SRC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/PHP_1STPJ-main/src/" );
    define( "URL_DB", SRC_ROOT."common/db_connect.php" );
    include_once( URL_DB );

    $http_method = $_SERVER["REQUEST_METHOD"];
    if($http_method === "GET"){
        // select
        $arr_get = $_GET;
        $arr_prepare = array(
            "list_no"   => (int)$arr_get["list_no"] // get -> string 데이터형으로 넘어옴
        );
        $arr_prepare_2 = array(
            "list_start_date" => $arr_get["list_start_date"]
        );
        $detail_info = todo_select_detail_info( $arr_prepare );
        $detail_today = todo_select_detail_list( $arr_prepare_2 );
        // strtotime() : 문자열 형태의 날짜를 입력받아 UNIX timestamp(초 단위로 세어지는 정수로 표현한 값) 형식의 값을 돌려주는 함수
        // $today_list = date("Y-m-d", strtotime($detail_info["list_start_date"]));
        // $today = date("Y-m-d", strtotime($detail_today[0]["list_start_date"]));
        $today_list = substr($detail_info["list_start_date"], 0, 9);
        $today = substr($detail_today[0]["list_start_date"], 0, 9);
    }else{
        // update
        $list_no_post = $_POST["list_no"]; // input type="hidden"으로 받은 list_no를 저장
        if(isset($_POST["ip_name_check"])){ // $_POST로 들어온 값이 'check'라면 밑의 실행문 실행
            $result_cnt = todo_com_update_detail_list( $list_no_post );
            if($result_cnt === 1){
                header( "Location: todo_index.php" ); // 위의 실행을 실행한 후에 index 페이지로 이동
                exit(); // 이 이후의 실행들을 모두 종료
            }
        }else{
            $result_cnt = todo_nocom_update_detail_list( $list_no_post );
            if($result_cnt === 1){
                header( "Location: todo_index.php" ); // 위의 실행을 실행한 후에 index 페이지로 이동
                exit();
            }else{
                header( "Location: todo_index.php" ); // 위의 실행을 실행한 후에 index 페이지로 이동
                exit();
            }
        }
    }

    // calender
    $list_start_date = $arr_get["list_start_date"];
    // substr : 문자열 자르기
    $year_pick = substr($list_start_date, 0, 4); // 2023
    $month_pick = substr($list_start_date, 5, 2); // 01 ~ 12
    $firstday = $year_pick."-".$month_pick."-01"; // 2023-04-01
    // date('w') : 오늘의 요일(출력 순서 일(0) ~ 토(6))
    $day_pick = date('w', strtotime($firstday)); // 2023-04-01(토) -> 토(6) > 6칸만큼 공백

    // left to do
    $limit_num = 4; // 남은 할 일을 출력할 최대 값
    $arr_prepare1 = array(
        "list_start_date"   => $list_start_date
        ,"list_due_date"    => $list_start_date
        ,"limit_num"        => $limit_num
        );
    $result_paging = select_list_detail( $arr_prepare1 );
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/todo_detail_c.css">
    <script src="https://kit.fontawesome.com/8c69259d3d.js" crossorigin="anonymous"></script> <!-- 폰트어썸 아이콘을 사용하기 위한 스크립트 -->
    <title>Todo List : 상세</title>
    <link rel="icon" href="common/img/magic-book.png"> <!-- 브라우저 파비콘 이미지 -->
</head>
<header>
    <a href="todo_index.php"><img src="./common/img/title.png" alt="header_title"></a>
</header>
<body>
<div class="total_container">
    <div class="detail"> <!-- 프로필 영역 -->
        <div class="profile"> <!-- 프로필 이미지, 레벨, 포인트 -->
            <div class="prof_img"> 
                <img class="grow_img" src="./common/img/grow<?php echo level_cal() ?>.png" alt="grow1">
            </div>
            <span class="prof_name_level">
                Lv. <?php echo level_cal() ?><br>
                point : <?php echo point_cal() ?>
            </span>
            <hr class="prof_hr">
        </div>
        <div class="detail_total_calender"> <!-- 달력 영역 -->
            <h3>Calender</h3>
            <div class="detail_calender"> <!-- 달력 -->
                <div class="calendar_title">
                    <p><?php echo $month_pick ?>월</p>
                </div>
                <div class="day_list">
                    <span>일</span><span>월</span><span>화</span><span>수</span><span>목</span><span>금</span><span>토</span>
                    <?php make_calendar_detail( $year_pick, $month_pick, $day_pick ) ?>
                </div>
            </div>
        </div>
        <div class="detail_today"> <!-- 현재 선택한 할 일과 같은 날의 남은 할 일을 표시하는 영역 -->
            <h3>Left to do</h3>
            <div class="today_info">
                <ul>
                    <?php li_display_detail( $result_paging, $list_start_date ) ?> <!-- 남은 할 일을 리스트 형태로 출력하는 함수 실행 -->
                </ul>
            </div>
        </div>
    </div>
    <div class="detail_info"> <!-- 상세 내용 영역 -->
        <div class="info_title"> <!-- 선택한 할 일의 시작 날짜, 마감 날짜 -->
            <span class="todo_date">
                <?php echo substr($detail_info["list_start_date"], 5, 5)." ~ ".substr($detail_info["list_due_date"], 5, 5); ?>
            <span>
            <hr>
        </div>
        <div class="imp_star"> <!-- 할 일의 중요도 표시 -->
            <?php if($detail_info["list_imp_flg"] === '1'){ ?>
                <span class="imp">
                    <i class="fa-solid fa-star" style="color: #FFDA56;"></i> <!-- 폰트어썸 아이콘 -->
                </span>
                <?php } else { ?>
                <span class="no_imp">
                    <i class="fa-regular fa-star" style="color: #1d293f;"></i> <!-- 폰트어썸 아이콘 -->
                </span>
            <?php } ?>
        </div>
        <form action="todo_detail.php" method="post">
        <div class="detail_content"> <!-- 선택한 할 일의 제목, 시간, 내용 영역 -->
            <div class="detail_info_title"> 
                <!-- 할 일 완료 선택 체크박스 -->
                <input type="hidden" value="<?= $arr_prepare["list_no"] ?>" name="list_no">
                <?php if($detail_info["list_clear_flg"] === '1'){ ?>
                    <input type="checkbox" id="check_label" checked> <!-- disabled : checkbox를 수정 할 수 없게 처리 -->
                <?php }else{ ?>
                    <!-- checkbox를 클릭하고 완료 버튼을 누를 시 id(check)의 값(check)이 post 방식으로 보내짐 -->
                    <input type="checkbox" id="check_label" name="ip_name_check" value="check" class="todo_check">
                <?php } ?>
                <!-- 상세 제목, 시간 -->
                <label for="check_label" class="todo_title"><?= $detail_info["list_title"] ?></label>
                <br>
                <label for="check_label" class="todo_date_time">
                    <?php echo substr($detail_info["list_start_date"], 11, 5)." ~ ".substr($detail_info["list_due_date"], 11, 5); ?>
                <label>
            </div>
            <!-- 상세 내용 -->
            <textarea cols="50" rows="10" readonly><?php echo $detail_info["list_detail"]?></textarea>
        </div>
        <div class="com_btn"> <!-- 체크 박스 체크 후 완료 버튼 > post 방식으로 값 전달 -->
            <button type="submit" class="com">완료</button>
        </div>
        </form>
        <!-- 수장, 리스트 페이지 이동 버튼 -->
        <a href="todo_update.php?list_no=<?php echo $arr_prepare["list_no"] ?>"><button class="modify_btn" type="button">수정</button></a>
        <a href="todo_index.php"><button class="return_btn" type="button">돌아가기</button></a>
    </div>
</div>
</body>
</html>