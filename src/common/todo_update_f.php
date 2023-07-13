<?php

include_once( "db_connect.php" ); // include_once( "db_connect.php" );는 PHP에서 외부 파일인 'db_connect.php' 파일을 한 번만 포함(include)하도록 하는 코드입니다. 
                                  // 이렇게 포함된 파일은 한 번만 실행되고, 중복 포함을 방지할 수 있습니다.
//----------------------------------------------------------------------------------------------------------------------------------------------------
// 첫 번째 함수인 update_todo_list_info_no는 todo_list_info라는 테이블에서 list_no라는 조건에 맞는 데이터의 필드 값을 업데이트합니다. 
// 이 함수는 입력받은 연관 배열 $arr_post를 바탕으로 SQL문을 작성하고, PDO(PHP Data Objects)를 사용하여 데이터베이스에 연결한 뒤, 해당 SQL문을 실행합니다. 
// 성공적으로 실행된 경우 업데이트된 행의 개수를 반환하고, 실패한 경우 Exception 메시지를 반환합니다. 
function update_todo_list_info_no( &$arr_post ) //"&$arr_post"는 매개변수 $arr_post를 참조(reference)로 전달하는 것을 의미합니다.
{
    $sql = 
    " UPDATE "
    ." todo_list_info "
    ." SET "
    ." list_title = :list_title "
    ." ,list_detail = :list_detail "
    ." ,list_start_date = :list_start_date "
    ." ,list_due_date = :list_due_date "
    ." ,list_imp_flg = :list_imp_flg "
    ." WHERE "
    ." list_no = :list_no "
    ;
//--------------------------------------------------------------------------------------------------------------------------------------------------    
// 이 코드는 todo_list_info 테이블에서 list_no 값이 $arr_post["list_no"]와 일치하는 행의 정보를 업데이트합니다.
// $sql 변수에는 SQL 쿼리문이 담겨있습니다. UPDATE 문을 사용하여 todo_list_info 테이블에서 list_title, list_detail, list_start_date, list_due_date, 
// list_imp_flg 값을 $arr_post 배열에서 가져온 값으로 업데이트하고, list_no 값이 $arr_post["list_no"]와 일치하는 행을 찾아 업데이트합니다. 
// 이 때, UPDATE 문의 구문은 SET [column1=value1, column2=value2, ...] WHERE [condition] 형태를 따릅니다.
    $arr_prepare =
    array(
        ":list_no" => $arr_post["list_no"]
        ,":list_title" => $arr_post["list_title"]
        ,":list_detail" => $arr_post["list_detail"]
        ,":list_start_date" => $arr_post["list_start_date"]
        ,":list_due_date" => $arr_post["list_due_date"] 
        ,":list_imp_flg" => $arr_post["list_imp_flg"]
        );
//해당 코드는 PDO를 사용하여 SQL 쿼리에 전달할 변수 값을 배열 형태로 생성하는 코드입니다.
 // array
 // :list_no, :list_title, :list_detail, :list_start_date, :list_due_date, :list_imp_flg 와 같이 콜론(:)으로 시작하는 것은 
 // SQL 쿼리에서 placeholder로 사용됩니다. $arr_post 배열에서 받아온 값을 :list_no, :list_title, :list_detail, :list_start_date, :list_due_date, 
 // :list_imp_flg에 매핑하여, 해당 값을 가지고 있는 연관 배열을 생성합니다. 이 배열은 SQL 쿼리 실행 시, 쿼리에 바인딩됩니다.
//--------------------------------------------------------------------------------------------------------------------------------------------------
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
    catch( Exception $e )
    {
        $conn->rollback();
        return $e->getMessage();
        
    }
    finally
    {
        $conn = null; 
    }
    return $result_cnt;
// PHP에서 MySQL 데이터베이스와 연결하고, 트랜잭션을 사용하여 데이터베이스에 쿼리를 실행하는 함수입니다.
    // $conn = null;: $conn 변수를 null로 초기화합니다.
    // db_conn( $conn );: db_conn() 함수를 호출하여 MySQL 데이터베이스와 연결을 수행하고, 연결된 객체를 $conn 변수에 할당합니다. 이 함수는 데이터베이스 연결을 설정하는데 사용됩니다.
    // $conn->beginTransaction();: 데이터베이스 연결 객체인 $conn에 대해 트랜잭션을 시작합니다. 데이터베이스 트랜잭션은 여러 쿼리를 그룹화하여 원자성, 일관성, 격리성, 지속성 (ACID) 속성을 보장하는 데 사용됩니다.
    // $stmt = $conn->prepare( $sql );: 데이터베이스 연결 객체인 $conn에 대해 미리 준비된(prepared) SQL 쿼리를 생성하고, 이를 $stmt 변수에 할당합니다. 이를 통해 SQL 인젝션 공격을 방지하고, 성능을 향상시킬 수 있습니다.
    // $stmt->execute( $arr_prepare );: 미리 준비된(prepared) SQL 쿼리를 실행하고, $arr_prepare 배열에 저장된 값들을 바인딩하여 쿼리를 실행합니다. 이를 통해 사용자 입력 값의 안전성을 보장할 수 있습니다.
    // $result_cnt = $stmt->rowCount();: 실행된 쿼리로 영향을 받는 행(row)의 수를 $result_cnt 변수에 저장합니다.
    // $conn->commit();: 데이터베이스 트랜잭션을 커밋하여 변경된 데이터를 실제로 데이터베이스에 적용합니다.
    // catch( Exception $e ): 예외(Exception)가 발생한 경우 해당 예외를 처리합니다.
    // $conn->rollback();: 데이터베이스 트랜잭션을 롤백하여 변경된 데이터를 취소합니다.
    // return $e->getMessage();: 예외의 메시지를 반환합니다. 예외가 발생한 경우 해당 메시지를 반환하여 오류를 처리하는 것입니다.
    // $conn = null;: 데이터베이스 연결 객체인 $conn을 null로 초기화합니다.
    // return $result_cnt;: 실행된 쿼리로 영향을 받는 행(row)의 수를 반환합니다. 함수의 실행 결과로 사용됩니다.
}
//---------------------------------------------------------------------------------------------------------------------------
// 두 번째 함수인 select_list_info_no는 todo_list_info 테이블에서 list_no가 $param_no와 일치하는 데이터를 선택합니다. 
// 이 함수도 마찬가지로 PDO를 사용하여 데이터베이스에 연결하고, $ param_no를 바탕으로 SQL문을 작성한 뒤, 해당 SQL문을 실행합니다. 
// 이 함수는 선택된 데이터를 배열로 반환합니다.
//---------------------------------------------------------------------------------------------------------------------------
function select_list_info_no( &$param_no ) // "&$param_no"는 매개변수 $param_no를 참조(reference)로 전달하는 것을 의미합니다.
{
	$sql =
		" SELECT "
		." list_no "
        ." ,list_title "
        ." ,list_detail "
        ." ,list_start_date "
        ." ,list_due_date "
        ." ,list_imp_flg"
		." FROM "
		." 	todo_list_info "
		." WHERE "
        ." list_no = :list_no "
		;
	
	$arr_prepare =
		array(
			":list_no"	=> $param_no
		// $arr_prepare는 SQL 쿼리의 실행에 사용되는 매개변수 배열입니다.
        // 해당 코드에서는 $arr_prepare 배열이 :list_no라는 키와 $param_no라는 값으로 구성되어 있습니다. 
        // 이 매개변수 배열은 SQL 쿼리의 플레이스홀더에 값을 바인딩하는 데 사용됩니다.
        // 즉, $param_no 변수의 값이 :list_no라는 플레이스홀더에 바인딩되어 SQL 쿼리가 실행될 때, 해당 값이 :list_no에 대체되어 실행되게 됩니다. 
        // 이를 통해 동적인 값들을 SQL 쿼리에 전달하여 다양한 데이터베이스 연산을 수행할 수 있습니다.
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

	return $result[0];
    
    //데이터베이스에서 가져온 결과를 반환하는 함수입니다.
    // $conn = null;: $conn 변수를 미리 초기화합니다.
    // db_conn( $conn );: 데이터베이스 연결 함수를 호출하여 $conn 변수에 데이터베이스 연결을 할당합니다.
    // $stmt = $conn->prepare( $sql );: SQL 쿼리를 준비합니다.
    // $stmt->execute( $arr_prepare );: 준비된 쿼리를 실행합니다.
    // $result = $stmt->fetchAll();: 실행 결과를 모두 가져와서 $result 변수에 할당합니다.
    // return $result[0];: 결과 배열에서 첫 번째 항목을 반환합니다.
}
?>