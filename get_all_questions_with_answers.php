<?php
/**
 * 臨時腳本：匯出所有題目（包含答案）
 * 此腳本需要上傳到伺服器的 caa-data 目錄執行
 */

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 資料庫連接資訊（需要根據實際情況修改）
$host = 'localhost';
$dbname = 'drone_exam';
$username = 'root';
$password = 'Bk6gjo4gj/6';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 抓取 general 題庫的所有題目
    $stmt = $pdo->prepare("
        SELECT 
            id,
            question_number,
            question_text,
            option_a,
            option_b,
            option_c,
            option_d,
            correct_answer
        FROM questions_general
        ORDER BY question_number
    ");
    
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 格式化輸出
    $result = [];
    foreach ($questions as $q) {
        $result[$q['question_number']] = [
            'id' => (int)$q['id'],
            'question_number' => (int)$q['question_number'],
            'question_text' => $q['question_text'],
            'option_a' => $q['option_a'],
            'option_b' => $q['option_b'],
            'option_c' => $q['option_c'],
            'option_d' => $q['option_d'],
            'correct_answer' => $q['correct_answer']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($result),
        'questions' => $result
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => '資料庫連接失敗',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
