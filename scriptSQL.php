<?php

ini_set('max_execution_time', 900);

$host = 'localhost'; //Host
$database = 'bd_name'; //Name BD
$user = 'user_name'; // User name
$password = ''; // Password
$link = mysqli_connect($host, $user, $password, $database) or die("Ошибка: " . mysqli_error($link));

$prefix = 'cscart_'; //Prefix tables

$tables = [
    $prefix . 'call_requests' => [
        'name' => 'change_name',
        'phone' => 'change_phone',
        'notes' => 'clear_column'
    ],
    $prefix . 'companies' => [
        'company' => 'change_company',
        'address' => 'change_address',
        'email' => 'change_email',
        'phone' => 'change_phone'
    ],
    $prefix . 'discussion_posts' => [
        'name' => 'change_name'
    ],
    $prefix . 'mango_calls' => [
        'abonent' => 'change_phone'
    ],
    $prefix . 'mango_entries' => [
        'abonent' => 'change_phone'
    ],
    $prefix . 'orders' => [
        'notes' => 'clear_column',
        'firstname' => 'change_name',
        'lastname' => 'change_name',
        'company' => 'change_company',
        'b_firstname' => 'change_name',
        'b_lastname' => 'change_name',
        'b_address' => 'change_address',
        'b_address_2' => 'change_address',
        'b_phone' => 'change_phone',
        's_firstname' => 'change_name',
        's_lastname' => 'change_name',
        's_address' => 'change_address',
        's_address_2' => 'change_address',
        's_phone' => 'change_phone',
        'phone' => 'change_phone',
        'email' => 'change_email'
    ],
    $prefix . 'order_logs' => [
        'description' => 'clear_column'
    ],
    $prefix . 'order_payments' => [
        'transaction_id' => 'change_code'
    ],
    $prefix . 'product_subscriptions' => [
        'email' => 'change_email'
    ],
    $prefix . 'profile_fields_data' => [
        'value' => 'clear_column'
    ],
    $prefix . 'queue_mail' => [
        'to_email' => 'change_email'
    ],
    $prefix . 'queue_sms' => [
        'to_phone' => 'change_phone'
    ],
    $prefix . 'rus_multishipping_pickups' => [
        'name' => 'change_address',
    ],
    $prefix . 'rus_online_cash_register_receipts' => [
        'email' => 'change_email',
        'phone' => 'change_phone'
    ],
    $prefix . 'rus_russianpost_offices' => [
        'address' => 'change_address',
        'phone' => 'change_phone'
    ],
    $prefix . 'shippers' => [
        'supplier' => 'change_company',
        'email' => 'change_email',
        'inn' => 'change_inn'
    ],
    $prefix . 'store_location_descriptions' => [
        'pickup_address' => 'change_address',
        'pickup_phone' => 'change_phone'
    ],
    $prefix . 'subscribers' => [
        'email' => 'change_email'
    ],
    $prefix . 'users' => [
        'password' => 'change_password',
        'firstname' => 'change_name',
        'lastname' => 'change_name',
        'company' => 'change_company',
        'email' => 'change_email',
        'phone' => 'change_phone'
    ],
    $prefix . 'user_profiles' => [
        'b_firstname' => 'change_name',
        'b_lastname' => 'change_name',
        'b_address' => 'change_address',
        'b_address_2' => 'change_address',
        'b_phone' => 'change_phone',
        's_firstname' => 'change_name',
        's_lastname' => 'change_name',
        's_address' => 'change_address',
        's_address_2' => 'change_address',
        's_phone' => 'change_phone'
    ],
    $prefix . 'logs' => 'clear_table',
    $prefix . 'access_logs' => 'clear_table',
    $prefix . 'logs_archive' => 'clear_table',
    $prefix . 'sessions' => 'clear_table',
    $prefix . 'user_session_products' => 'clear_table',
    $prefix . 'stored_sessions' => 'clear_table'
];

function handler($array) {
    foreach ($array as $tableName => $params) {
        if ($params == 'clear_table') {
            if (is_callable($params)) {
                call_user_func($params, $tableName);
            }
        } else {
            foreach ($params as $columnName => $function) {
                if (is_callable($function)) {
                    call_user_func($function, array($tableName, $columnName));
                }
            }
        }
    }
    return "Complete! Table(s) updated!";
}

function random_name($length = 6) {
    $name = substr(str_shuffle(str_repeat($x = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(1 / strlen($x)))),1,1) . substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyz', ceil($length / strlen($x)))),1,$length);
    return $name;
}

function query_update($tableName, $columnName, $value, $nonNull = FALSE, $primaryName = NULL, $primaryValue = NULL) {
    $query = "UPDATE $tableName SET $columnName='$value'";
    if ($nonNull) {
        $query .= " WHERE $columnName IS NOT NULL AND $columnName!=''";
        if (!empty($primaryName) && !empty($primaryValue)) {
            $query .= " AND $primaryName='$primaryValue'";
        }
    }
    $result = mysqli_query($GLOBALS['link'], $query) or die("Ошибка: " . mysqli_error($GLOBALS['link']));

    return $result;
}

function query_primary_key($tableName) {
    $query = "SHOW INDEX FROM $tableName";
    $getIndex = mysqli_query($GLOBALS['link'], $query) or die("Ошибка: " . mysqli_error($GLOBALS['link']));
    $primaryName = mysqli_fetch_assoc($getIndex)['Column_name'];

    return $primaryName;
}

function clear_table($table) {
    mysqli_query($GLOBALS['link'], "TRUNCATE TABLE $table") or die("Ошибка: " . mysqli_error($GLOBALS['link']));
    return true;
}

function change_name($params) {
    $table = $params[0];
    $column = $params[1];
    $primaryName = query_primary_key($table);
    $result = mysqli_query($GLOBALS['link'], "SELECT $column, $primaryName FROM $table") or die("Ошибка: " . mysqli_error($GLOBALS['link']));

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $value = random_name();
            $primaryParam = $row[$primaryName];
            query_update($table, $column, $value, true, $primaryName, $primaryParam);
        }
    }

    return true;
}

function change_company($params) {
    $table = $params[0];
    $column = $params[1];
    $primaryName = query_primary_key($table);
    $result = mysqli_query($GLOBALS['link'], "SELECT $column, $primaryName FROM $table") or die("Ошибка: " . mysqli_error($GLOBALS['link']));
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $name = random_name(8);
            $value = 'ООО "' . $name . ' ' . $name . '"';
            $primaryParam = $row[$primaryName];
            query_update($table, $column, $value,true, $primaryName, $primaryParam);
        }
    }

    return true;
}

function change_email($params) {
    $table = $params[0];
    $column = $params[1];
    $primaryName = query_primary_key($table);
    $result = mysqli_query($GLOBALS['link'], "SELECT $column, $primaryName FROM $table") or die("Ошибка: " . mysqli_error($GLOBALS['link']));
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $md5 = substr(md5($row[$primaryName]+microtime(true)), 0, 9);
            $value = 'email' . $md5 . '@test.com';
            $primaryParam = $row[$primaryName];
            query_update($table, $column, $value,true, $primaryName, $primaryParam);
        }
    }

    return true;
}

function change_phone($params) {
    $table = $params[0];
    $column = $params[1];
    $value = '7(123)777-77-77';
    query_update($table, $column, $value);

    return true;
}

function change_address($params) {
    $table = $params[0];
    $column = $params[1];
    $value = 'Ул. Тест, д. 0';
    query_update($table, $column, $value);

    return true;
}

function change_password($params) {
    $table = $params[0];
    $column = $params[1];
    $value = 'fb12a008c1455af27466fb1420458b06'; // Пароль: 11111111
    query_update($table, $column, $value);

    return true;
}

function clear_column($params) {
    $table = $params[0];
    $column = $params[1];

    query_update($table, $column, '');

    return true;
}

function change_code($params) {
    $table = $params[0];
    $column = $params[1];
    $value = md5( microtime(true));
    query_update($table, $column, $value);

    return true;
}

?>

<pre>
    <? print_r(handler($tables)); ?>
</pre>
