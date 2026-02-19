<?php
/**
 * 管理員 API - 用於答案驗證
 * 此 API 端點會返回包含答案的完整題目資料
 * 
 * 使用方法:
 * GET https://solmen9263.dynu.net:5081/caa-data/admin_api.php?action=export_questions&key=verify2026
 */

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 簡單的安全key（實際使用時應使用更安全的方式）
$admin_key = $_GET['key'] ?? '';
if ($admin_key !== 'verify2026') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized'
    ]);
    exit;
}

require_once 'config.php';

$action = $_GET['action'] ?? '';

try {
    $pdo = getDBConnection();
    
    if ($action === 'export_questions') {
        $exam_type = $_GET['exam_type'] ?? 'general';
        $table_name = 'questions_' . $exam_type;
        
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
            FROM {$table_name}
            ORDER BY question_number
        ");
        
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 轉換為以題號為 key 的字典
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
            'exam_type' => $exam_type,
            'count' => count($result),
            'questions' => $result
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
