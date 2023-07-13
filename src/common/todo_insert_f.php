<?php

include_once( "db_connect.php" );

//*********************************
// 함수명 : insert_todo_info
// 기능 : 리스트 데이터 작성
// 파라미터 : &$param_arr
// 리턴 값 : $result_cnt(디버그에서 1나오면 정상작동)
// *********************************

function insert_todo_info( &$param_arr )
{
    $sql = 
        " INSERT INTO "
        ." todo_list_info( "
        ." list_title "
        ." ,list_detail "
        ." ,list_start_date "
        ." ,list_due_date "
        ." ,list_imp_flg "
        ." ) "
        ." VALUES( "
        ." :list_title "
        ." ,:list_detail "
        ." ,:list_start_date "
        ." ,:list_due_date "
        ." ,:list_imp_flg "
        ." ) "
        ;

    $arr_prepare =
        array(
            ":list_title" => $param_arr["list_title"]
            ,":list_detail" => $param_arr["list_detail"]
            ,":list_start_date" => $param_arr["list_start_date"]
            ,":list_due_date" => $param_arr["list_due_date"]
            ,":list_imp_flg" => $param_arr["list_imp_flg"]
        );

    $conn = null;
    try 
    {
        db_conn( $conn ); 
        $conn->beginTransaction(); 
        $stmt = $conn->prepare( $sql ); 
        $stmt->execute( $arr_prepare ); 
        $result_cnt = $stmt->rowCount();
        $conn->commit();
    } 
    catch ( Exception $e ) 
    {
        $conn->rollback();
        return $e->getMessage();
    }
    finally
    {
        $conn = null; 
    }
    
    return $result_cnt;
}

//***********************************
// 함수명 : delete_todo_info
// 기능 : 해당 데이터 삭제
// 파라미터 : $param_no(삭제할 list_no)
// 리턴 값 : $result_cnt(디버그에서 1나오면 정상작동)
// **********************************

function delete_todo_info( $param_no )
{
    $sql =
    " DELETE "
    ." FROM "
    ." todo_list_info "
    ." WHERE "
    ." list_no = "
    ." :list_no "
    ;

    $arr_prepare =
        array(
            ":list_no" => $param_no
        );
    
    try 
    {
        db_conn( $conn ); 
        $conn->beginTransaction(); 
        $stmt = $conn->prepare( $sql ); 
        $stmt->execute( $arr_prepare ); 
        $result_cnt = $stmt->rowCount(); 
        $conn->commit();
    } 
    catch ( Exception $e ) 
    {
        $conn->rollback();
        return $e->getMessage();
    }
    finally
    {
        $conn = null; 
    }
    
    return $result_cnt;
}

// ********************************
// 함수명 : select_list_no_desc     // 0420 기능변경으로 함수명 변경(구 함수명: select_list_no_cnt)
// 기능 : 가장 최신의 list_no를 가져옴   // 0420 함수기능 변경(구 기능 : list의 총 갯수를 구해서 가져옴)
// 파라미터 : 없음
// 리턴 값 : $result[0]
// **********************************

function select_list_no_desc()
{
    $sql =
        " SELECT "
        ." list_no "
        ." FROM "
        ." todo_list_info "
        ." ORDER BY "
        ." list_no "
        ." DESC "
        ." LIMIT "
        ." 1 "
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
    catch ( Exception $e ) 
    {
        return $e->getMessage();
    }
    finally
    {
        $conn = null;
    }
    
    return $result[0];
}
?>

