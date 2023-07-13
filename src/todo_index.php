<?php 
define( "SRC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/PHP_1STPJ-main/src/" );
define( "URL_DB", SRC_ROOT."common/db_connect.php" );
include_once( URL_DB );

// 페이지 번호 받아오는 if문, get 방식으로 넘어온 페이지 번호가 없을 시 1로 고정
if( array_key_exists("page_num", $_GET) )
{
    $page_num = $_GET["page_num"];
}
else
{
    $page_num = 1;
}

$date_ymd = date("Y-m-d"); // $date_ymd: 유저가 이동한 날짜를 저장하는 변수, 초기값은 현재 날짜
$print_date = $date_ymd; // $print_date: $date_ymd에 담긴 날짜를 페이지 타이틀 영역에서 보여줄 때 사용
$limit_num = 5; // $limit_num: 한 페이지에 보여 줄 할 일 개수를 저장하는 변수
$offset = ( $page_num - 1 ) * $limit_num; // $offset: 페이지 이동 시 몇 번째 항목부터 보여줄 지 저장하는 변수

// get 방식으로 list_start_date 값이 넘어올 시 $date_ymd와 $print_date에 해당 값을 저장하는 if문
if( array_key_exists("list_start_date", $_GET))
{
    if( $_GET["list_start_date"] === "" )
    {
        $date_ymd = date("Y-m-d");
    }
    else
    {
        $date_ymd = $_GET["list_start_date"];
    }
    $print_date = $date_ymd;
}

// get 방식으로 search 값이 넘어올 시 $print_date에는 "검색 결과"라는 string을, $date_ymd2(마감날짜 변수)에는 '0000-00-00'을 저장하고, search 값이 넘어오지 않았을 시 $date_ymd2에 $date_ymd 값을 저장하는 if문
if( array_key_exists("search", $_GET))
{
    $search = $_GET["search"];
    if( $search === "" )
    {
        $print_date = $date_ymd;
        $date_ymd2 = $date_ymd;
    }
    else
    {
        $print_date = "검색 결과";
        $date_ymd2 = '0000-00-00';
    }
}
else
{
    $search = "";
    $date_ymd2 = $date_ymd;
}

// select_list_search: 리스트 페이지 목록에 표시할 날짜별 할 일들을 배열로 가져오는 함수, $arr_prepare1에 필요한 파라미터 값들을 저장하여 사용
$arr_prepare1 = array(
    "list_start_date"   => $date_ymd
    ,"list_due_date"    => $date_ymd2
    ,"searchword"       => $search
    ,"limit_num"        => $limit_num
    ,"offset"           => $offset
    );
$result_paging = select_list_search( $arr_prepare1 );

// select_list_cnt: 페이지 번호 매길 때 필요한 날짜별 할 일 개수를 가져오는 함수, $arr_prepare2에 필요한 파라미터 값들을 저장하여 사용
$arr_prepare2 = array(
    "list_start_date"   => $date_ymd
    ,"list_due_date"    => $date_ymd2
    ,"searchword"       => $search
    );

$result_cnt = select_list_cnt( $arr_prepare2 );
$max_page_num = ceil( $result_cnt[0]["cnt"] / $limit_num ); // $max_page_num: 날짜별 할 일 목록의 전체 페이지 수를 저장

$year_pick = (int)substr($date_ymd, 0, 4); // $year_pick: 유저가 이동한 날짜 string에서 연도 값만 int 형식으로 저장
$month_pick = (int)substr($date_ymd, 5, 2); // $month_pick: 유저가 이동한 날짜 string에서 월 값만 int 형식으로 저장
$firstday = $year_pick."-".$month_pick."-01"; // $firstday: 유저가 이동한 날짜에 해당하는 달의 첫번째 날을 string 형식으로 저장
$day_pick = date('w', strtotime($firstday)); // $day_pick: 해당 달의 첫 날의 요일을 0~6까지의 숫자 형식으로 저장

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/todo_index_c.css"> <!-- css 파일 링크 -->
    <script src="https://kit.fontawesome.com/15c1734573.js" crossorigin="anonymous"></script> <!-- 폰트어썸 링크 -->
    <title>Todo List</title>
    <link rel="icon" href="common/img/magic-book.png"> <!-- 파비콘 링크 -->
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="todo_index.php"><img src="common/img/title.png" alt="title"></a> <!-- 헤더 영역의 이미지, <a> 태그로 감싸서 리스트 페이지로 이동할 수 있게 함 -->
        </div>
        <div class="paper">
        <div class="sidebar">
            <div class="profile">
                <div class="profile_img">
                    <img class="grow_img" src="common/img/grow<?php echo level_cal() ?>.png" alt="grow"> <!-- 이미지 이름에 레벨이 포함되어 있어, level_cal 함수를 통해 해당 레벨의 이미지를 가져올 수 있음 -->
                </div>
                <div class="profile_text">
                    <span class="level">Lv. <?php echo level_cal() ?></span> <!-- 현재 레벨을 level_cal 함수를 통해 보여줌 -->
                    <span class="point">Point : <?php echo point_cal() ?></span> <!-- 현재 포인트를 point_cal 함수를 통해 보여줌 -->
                </div>
            </div>
            <hr>
            <span class="calendar_text">Calendar</span>
            <div class="calendar">
                <form method="get" action="todo_index.php"> <!-- input date를 사용해서 브라우저 달력으로 날짜 이동할 수 있는 폼, get 방식으로 값을 서버로 보냄 -->
                    <input type="date" name="list_start_date">
                    <button type="submit" class="calendar_btn"><i class="fa-solid fa-angles-right"></i></button>
                </form>
                <div class="calendar_month"> <!-- 월 좌우 버튼에 strtotime 함수를 통해 한 달 단위로 날짜 이동을 할 수 있는 <a> 태그를 적용 -->
                    <a href="todo_index.php?list_start_date=<? echo $year_pick."-".date("m", strtotime($date_ymd." -1 month"))."-01" ?>"><i class="fa-solid fa-chevron-left"></i></a>
                    <span><?php echo $month_pick ?>월</span>
                    <a href="todo_index.php?list_start_date=<? echo $year_pick."-".date("m", strtotime($date_ymd." +1 month"))."-01" ?>"><i class="fa-solid fa-chevron-right"></i></a>
                </div>
                <div class="day_list">
                    <span>일</span><span>월</span><span>화</span><span>수</span><span>목</span><span>금</span><span>토</span>
                    <?php make_calendar( $year_pick, $month_pick, $day_pick ) ?> <!-- make_calendar: 달력의 날짜 부분 <a> 태그로 생성하고, 각 월별 1일의 요일에 따라 앞에 더미로 <span> 태그 삽입하는 함수, 파라미터로 유저가 이동한 날짜의 연도인 $year_pick, 월인 $month_pick과 해당 달의 첫 날의 요일값인 $day_pick을 받음 -->
                </div>
            </div>
        </div>
        <div class="main">
            <div class="upper_section">
            <div class="date_section">
                <h2><?php echo $print_date ?></h2> <!-- 메인 영역의 타이틀 부분, 유저가 이동한 날짜를 표시하거나 검색 시 '검색 결과'라는 메시지를 표시함 -->
                <hr>
            </div>
            <div class="list_section">
                <ul>
                    <?php li_display( $result_paging, $date_ymd ) ?> <!-- li_display: <li> 태그로 할 일 목록 띄우는 함수, 파라미터로 select_list_search를 이용해 받아온 배열 $result_paging과 유저가 이동한 날짜 값인 $date_ymd를 받음 -->
                </ul>
            </div>
            </div>
            <div class="lower_section">
            <div class="page_section">
                <?php select_list_paging( $page_num, $max_page_num, $date_ymd, $search ) ?> <!-- select_list_paging: <a> 태그로 페이지 번호 만드는 함수, 파라미터로 현재 페이지 값인 $page_num, 전체 페이지 값인 $max_page_num, 유저가 이동한 날짜 값인 $date_ymd, 검색어 값인 $search를 받음 -->
            </div>
            <div class="search_section">
                <form method="get" action="todo_index.php"> <!-- input search를 이용해서 검색어를 get 방식으로 서버로 넘겨주는 폼 -->
                    <input type="search" name="search" value=<?php echo $search ?>> <!-- 유저가 입력한 검색어를 value를 통해 확인할 수 있게 하여 유저 편의성을 높임 -->
                    <button type="submit" class="search_btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
            <div class="move_section"> <!-- 메인 영역 하단의 좌우 버튼, strtotime 함수를 이용해 하루 단위로 날짜 이동을 할 수 있게 함 -->
                    <a class=left_btn href="todo_index.php?list_start_date=<? echo date("Y-m-d", strtotime($date_ymd." -1 day")) ?>"><i class="fa-solid fa-chevron-left"></i></a>
                    <a class=right_btn href="todo_index.php?list_start_date=<? echo date("Y-m-d", strtotime($date_ymd." +1 day")) ?>"><i class="fa-solid fa-chevron-right"></i></a>
            </div>
            </div>
            <div class="button_section">
                <a href="todo_insert.php"><span class="insert_btn">작성하기</span></a> <!-- 작성 페이지로 이동할 수 있는 <a> 태그 -->
            </div>
        </div>
        </div>
    </div>
</body>
</html>