<?php

include_once( "db_connect.php" );

// ********************************
// 함수명 : select_list_search
// 기능 : 리스트 페이지 목록에 표시할 날짜별 할 일들을 배열로 가져오기
// 파라미터 : $param_arr
// 리턴 값 : $result
// **********************************
function select_list_search( &$param_arr )
{
    $sql = 
    " SELECT "
    ."      list_no "
    ."      ,list_title "
    ."      ,list_start_date "
    ."      ,list_due_date "
    ."      ,list_imp_flg "
    ."      ,list_clear_flg "
    ." FROM "
    ."      todo_list_info "
    ." WHERE "
    ."      DATE_SUB(list_start_date, INTERVAL 1 DAY) <= :list_start_date "
    ."      AND "
    ."      list_due_date >= :list_due_date "
    ."      AND "
    ."      list_title LIKE CONCAT('%', :searchword, '%') "
    ." ORDER BY "
    ."      list_clear_flg ASC "
    ."      ,list_imp_flg DESC "
    ."      ,list_due_date ASC "
    ."      ,list_start_date DESC "
    ."      ,list_no DESC "
    ." LIMIT :limit_num OFFSET :offset "
    ;

    $arr_prepare =
        array(
            ":list_start_date"        => $param_arr["list_start_date"]
            ,":list_due_date"        => $param_arr["list_due_date"]
            ,":searchword"           => $param_arr["searchword"]
            ,":limit_num"       => $param_arr["limit_num"]
            ,":offset"          => $param_arr["offset"]
        );

    $conn = null;
    try
    {
        db_conn( $conn );
        $stmt = $conn->prepare( $sql );
        $stmt->execute( $arr_prepare );
        $result = $stmt->fetchAll();
    }
    catch( Exception $e )
    {
        return $e->getMessage();
    }
    finally
    {
        $conn = null;
    }

    return $result;
}

// ********************************
// 함수명 : select_list_cnt
// 기능 : 페이지 번호 매길 때 필요한 날짜별 할 일 개수 가져오기
// 파라미터 : $param_arr
// 리턴 값 : $result
// **********************************
function select_list_cnt( &$param_arr )
{
    $sql = 
    " SELECT "
    ."      COUNT(*) cnt "
    ." FROM "
    ."      todo_list_info "
    ." WHERE "
    ."      DATE_SUB(list_start_date, INTERVAL 1 DAY) <= :list_start_date "
    ."      AND "
    ."      list_due_date >= :list_due_date "
    ."      AND "
    ."      list_title LIKE CONCAT('%', :searchword, '%') "
    ;

    $arr_prepare =
        array(
            ":list_start_date"        => $param_arr["list_start_date"]
            ,":list_due_date"        => $param_arr["list_due_date"]
            ,":searchword"           => $param_arr["searchword"]
        );

    $conn = null;
    try
    {
        db_conn( $conn );
        $stmt = $conn->prepare( $sql );
        $stmt->execute( $arr_prepare );
        $result = $stmt->fetchAll();
    }
    catch( Exception $e )
    {
        return $e->getMessage();
    }
    finally
    {
        $conn = null;
    }

    return $result;
}

// ********************************
// 함수명 : select_list_paging
// 기능 : <a> 태그로 페이지 번호 만드는 함수
// 파라미터 : $param_page, $param_max, $param_date, $param_search
// 리턴 값 : "<a href='todo_index.php?page_num=".$i."&list_start_date=".$param_date."&search=".$param_search."'>".$i."</a>"
// **********************************
function select_list_paging( $param_page, $param_max, $param_date, $param_search )
{
    if($param_max > 1)
    {
        for($i=1; $i <= $param_max; $i++)
        {
            echo "<a href='todo_index.php?page_num=".$i."&list_start_date=".$param_date."&search=".$param_search."'>".$i."</a>";
        }
    }
}

// ********************************
// 함수명 : li_display
// 기능 : <li> 태그로 할 일 목록 띄우기
// 파라미터 : $param_arr, $param_date
// 리턴 값 : "<li><a href='todo_detail.php?list_no=".$val['list_no']."&list_start_date=".$param_date."'><div class='list_container ".$list_class."'>".$checkbox.$impmark."<span class='list_title_s'>".$val['list_title']."</span><span class='list_date'>".trim_date($val['list_start_date'])." ~ ".trim_date($val['list_due_date'])."</span>".$alert."</div></a></li>"
// **********************************
function li_display( $param_arr, $param_date )
{
    foreach ($param_arr as $val)
    {
        if( $param_date === substr($val['list_due_date'], 0, 10) )
        {
            $alert = "<span class='d_day'>D-DAY</span>";
        }
        else if( $param_date === date("Y-m-d", strtotime($val['list_due_date']." -1 day")) )
        {
            $alert = "<span class='d_1'>D-1</span>";
        }
        else
        {
            $alert = "<i class='fa-solid fa-angle-right'></i>";
        }
        if($val['list_clear_flg'] === '1')
        {
            $checkbox = "<i class='fa-solid fa-square-check'></i>";
            if($val['list_imp_flg'] === '1')
            {
                $impmark = "<i class='fa-solid fa-star'></i>";
                $list_class = "cle_imp";
            }
            else
            {
                $impmark = "<i></i>";
                $list_class = "cle_nimp";
            }
        }
        else
        {
            $checkbox = "<i class='fa-regular fa-square'></i>";
            if($val['list_imp_flg'] === '1')
            {
                $impmark = "<i class='fa-solid fa-star'></i>";
                $list_class = "unc_imp";
            }
            else
            {
                $impmark = "<i></i>";
                $list_class = "unc_nimp";
            }
        }

        echo "<li><a href='todo_detail.php?list_no=".$val['list_no']."&list_start_date=".$param_date."'><div class='list_container ".$list_class."'>".$checkbox.$impmark."<span class='list_title_s'>".$val['list_title']."</span><span class='list_date'>".trim_date($val['list_start_date'])." ~ ".trim_date($val['list_due_date'])."</span>".$alert."</div></a></li>";
    }
}

// ********************************
// 함수명 : point_cal
// 기능 : 완료한 할 일 개수 가져와서 포인트 계산하는 함수
// 파라미터 : X
// 리턴 값 : $result[0]["point"]
// **********************************
function point_cal()
{
    $sql = 
    " SELECT "
    ."      COUNT(*) point "
    ." FROM "
    ."      todo_list_info "
    ." WHERE "
    ."      list_clear_flg = '1' "
    ;

    $arr_prepare = array();

    $conn = null;
    try
    {
        db_conn( $conn );
        $stmt = $conn->prepare( $sql );
        $stmt->execute( $arr_prepare );
        $result = $stmt->fetchAll();
    }
    catch( Exception $e )
    {
        return $e->getMessage();
    }
    finally
    {
        $conn = null;
    }

    return $result[0]["point"];
}

// ********************************
// 함수명 : level_cal
// 기능 : 위의 point_cal 함수 이용해서 레벨 계산하는 함수
// 파라미터 : X
// 리턴 값 : $result
// **********************************
function level_cal()
{
    if(point_cal() === 0)
    {
        $result = 1;
    }
    else if(point_cal() <= 50)
    {
        $result = ceil(point_cal() / 10);
    }
    else
    {
        $result = 5;
    }
    return $result;
}

// ********************************
// 함수명 : trim_date
// 기능 : yyyy-mm-dd 형식으로 되어 있는 날짜 string mm-dd 형식으로 자르는 함수
// 파라미터 : $param_str
// 리턴 값 : $result
// **********************************
function trim_date( $param_str )
{
    $result = substr($param_str, 5, 5);
    return $result;
}

// ********************************
// 함수명 : make_calendar
// 기능 : 달력의 날짜 부분 <a> 태그로 생성하고, 각 월별 1일의 요일에 따라 앞에 더미로 <span> 태그 삽입하는 함수
// 파라미터 : $param_year, $param_month, $param_day
// 리턴 값 : "<a href='todo_index.php?list_start_date=".$param_year."-".$param_month."-".$i."'>".$i."</a>"
// **********************************
function make_calendar( $param_year, $param_month, $param_day )
{
    $temp_arr_31 = [1, 3, 5, 7, 8, 10, 12];
    $temp_arr_30 = [4, 6, 9, 11];

    if( in_array( $param_month, $temp_arr_31 ) )
    {
        $ii = 31;
    }
    else if( in_array( $param_month, $temp_arr_30 ) )
    {
        $ii = 30;
    }
    else
    {
        if( $param_year % 4 === 0 )
        {
            $ii = 29;
        }
        else
        {
            $ii = 28;
        }
    }

    for ($i=1; $i <= $param_day; $i++)
    {
        echo "<span></span>";
    }

    for ($i=1; $i <= $ii; $i++)
    { 
        if($i < 10)
        {
            if($param_month < 10)
            {
                echo "<a href='todo_index.php?list_start_date=".$param_year."-0".$param_month."-0".$i."'>".$i."</a>";
            }
            else
            {
                echo "<a href='todo_index.php?list_start_date=".$param_year."-".$param_month."-0".$i."'>".$i."</a>";
            }
        }
        else
        {
            if($param_month < 10)
            {
                echo "<a href='todo_index.php?list_start_date=".$param_year."-0".$param_month."-".$i."'>".$i."</a>";
            }
            else
            {
                echo "<a href='todo_index.php?list_start_date=".$param_year."-".$param_month."-".$i."'>".$i."</a>";
            }
        }
    }
}

?>