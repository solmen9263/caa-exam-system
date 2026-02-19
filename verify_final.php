<?php
/**
 * 最終答案驗證腳本
 * 直接比對資料庫答案 vs Word 標準答案
 * 訪問：https://solmen9263.dynu.net:5081/caa-data/verify_final.php
 */
header('Content-Type: text/html; charset=utf-8');
require_once 'config.php';

// Word 標準答案對照表（由 Python 自動生成）
$word_answers = {
  "362": "D",
  "88": "C",
  "207": "B",
  "54": "B",
  "97": "A",
  "123": "A",
  "369": "D",
  "349": "B",
  "67": "A",
  "81": "D",
  "33": "A",
  "132": "B",
  "98": "D",
  "51": "B",
  "382": "D",
  "120": "D",
  "379": "A",
  "153": "D",
  "297": "B",
  "169": "B",
  "168": "C",
  "31": "C",
  "135": "A",
  "195": "C",
  "186": "B",
  "105": "D",
  "176": "C",
  "7": "B",
  "92": "D",
  "124": "C",
  "64": "A",
  "39": "A",
  "310": "B",
  "253": "D",
  "201": "D",
  "163": "D",
  "32": "B",
  "34": "A",
  "197": "D",
  "377": "B",
  "372": "A",
  "72": "C",
  "259": "A",
  "161": "C",
  "138": "B",
  "220": "D",
  "281": "C",
  "251": "C",
  "20": "A",
  "358": "D",
  "121": "A",
  "370": "B",
  "4": "A",
  "306": "C",
  "45": "C",
  "187": "B",
  "217": "D",
  "315": "D",
  "126": "C",
  "196": "A",
  "1": "D",
  "314": "D",
  "35": "A",
  "167": "D",
  "238": "D",
  "179": "B",
  "91": "C",
  "373": "C",
  "18": "B",
  "363": "A",
  "232": "D",
  "284": "C",
  "8": "D",
  "190": "C",
  "371": "C",
  "175": "D",
  "141": "B",
  "233": "B",
  "125": "C",
  "342": "C",
  "346": "C",
  "62": "C",
  "250": "A",
  "53": "B",
  "246": "C",
  "85": "D",
  "329": "D",
  "23": "B",
  "148": "A",
  "312": "D",
  "205": "C",
  "274": "C",
  "322": "D",
  "95": "D",
  "263": "B",
  "200": "D",
  "244": "B",
  "38": "B",
  "24": "C",
  "9": "D",
  "136": "A",
  "71": "A",
  "375": "D",
  "116": "B",
  "311": "B",
  "236": "A",
  "46": "A",
  "42": "B",
  "320": "B",
  "156": "A",
  "225": "B",
  "15": "B",
  "3": "C",
  "303": "B",
  "41": "C",
  "149": "D",
  "68": "C",
  "87": "C",
  "13": "A",
  "140": "B",
  "177": "A",
  "76": "C",
  "134": "B",
  "10": "D",
  "305": "A",
  "294": "D",
  "296": "D",
  "335": "D",
  "111": "B",
  "254": "A",
  "198": "A",
  "245": "B",
  "300": "D",
  "57": "A",
  "194": "A",
  "210": "A",
  "99": "D",
  "6": "A",
  "185": "C",
  "104": "C",
  "276": "A",
  "256": "C",
  "74": "D",
  "364": "B",
  "159": "D",
  "223": "A",
  "249": "C",
  "93": "C",
  "180": "C",
  "242": "D",
  "317": "D",
  "155": "C",
  "17": "A",
  "47": "D",
  "37": "D",
  "264": "A",
  "5": "C",
  "266": "A",
  "368": "D",
  "327": "B",
  "337": "A",
  "107": "D",
  "102": "A",
  "78": "B",
  "288": "B",
  "59": "C",
  "147": "B",
  "26": "B",
  "40": "D",
  "66": "D",
  "166": "C",
  "340": "A",
  "212": "B",
  "298": "B",
  "247": "A",
  "218": "A",
  "30": "C",
  "228": "C",
  "48": "A",
  "173": "A",
  "275": "B",
  "265": "D",
  "193": "A",
  "215": "B",
  "118": "C",
  "360": "D",
  "213": "D",
  "22": "D",
  "164": "B",
  "376": "A",
  "101": "D",
  "344": "C",
  "139": "A",
  "330": "A",
  "221": "C",
  "309": "A",
  "108": "D",
  "334": "B",
  "96": "D",
  "278": "B",
  "328": "D",
  "295": "C",
  "299": "B",
  "319": "D",
  "204": "C",
  "227": "A",
  "240": "C",
  "222": "D",
  "282": "D",
  "100": "B",
  "336": "A",
  "345": "D",
  "271": "A",
  "110": "C",
  "160": "B",
  "216": "C",
  "170": "A",
  "199": "D",
  "241": "B",
  "235": "C",
  "350": "D",
  "321": "C",
  "318": "D",
  "183": "D",
  "58": "A",
  "378": "D",
  "338": "A",
  "348": "D",
  "152": "C",
  "106": "D",
  "25": "A",
  "80": "B",
  "181": "B",
  "131": "B",
  "325": "D",
  "52": "D",
  "286": "D",
  "287": "B",
  "145": "A",
  "258": "A",
  "154": "B",
  "353": "B",
  "174": "B",
  "94": "D",
  "165": "A",
  "323": "C",
  "89": "A",
  "188": "A",
  "343": "D",
  "56": "B",
  "29": "B",
  "313": "C",
  "277": "C",
  "272": "A",
  "234": "D",
  "248": "B",
  "304": "C",
  "61": "B",
  "316": "D"
};

$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, question_number, question_text, option_a, option_b, option_c, option_d, correct_answer FROM questions_general ORDER BY question_number");
$db_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
$correct = 0;
$wrong = [];

foreach ($db_questions as $q) {
    $qnum = (string)$q['question_number'];
    if (!isset($word_answers[$qnum])) continue;

    $total++;
    $db_ans = strtoupper(trim($q['correct_answer']));
    $word_ans = strtoupper(trim($word_answers[$qnum]));

    if ($db_ans === $word_ans) {
        $correct++;
    } else {
        $wrong[] = [
            'id' => $q['id'],
            'question_number' => $q['question_number'],
            'question_text' => $q['question_text'],
            'option_a' => $q['option_a'],
            'option_b' => $q['option_b'],
            'option_c' => $q['option_c'],
            'option_d' => $q['option_d'],
            'db_answer' => $db_ans,
            'word_answer' => $word_ans,
        ];
    }
}

$wrong_count = count($wrong);
$accuracy = $total > 0 ? round($correct / $total * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head><meta charset="UTF-8"><title>答案驗證報告</title>
<style>
body{font-family:Microsoft JhengHei,Arial,sans-serif;margin:20px;background:#f5f5f5}
.summary{background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.1)}
.ok{color:#27ae60;font-weight:bold}
.err{color:#e74c3c;font-weight:bold}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)}
th{background:#2c3e50;color:#fff;padding:10px;text-align:left}
td{padding:10px;border-bottom:1px solid #eee;vertical-align:top}
tr:hover{background:#f9f9f9}
.badge-db{background:#e74c3c;color:#fff;padding:2px 8px;border-radius:12px;font-weight:bold}
.badge-word{background:#27ae60;color:#fff;padding:2px 8px;border-radius:12px;font-weight:bold}
pre{background:#2c3e50;color:#1abc9c;padding:15px;border-radius:8px;overflow-x:auto;font-size:12px}
.question-text{max-width:400px;word-wrap:break-word}
</style>
</head>
<body>
<h1>🔍 答案驗證報告</h1>
<div class="summary">
  <h2>📊 統計摘要</h2>
  <p>比對題數：<strong><?= $total ?></strong> 題</p>
  <p>答案一致：<strong class="ok"><?= $correct ?> 題</strong></p>
  <p>答案錯誤：<strong class="err"><?= $wrong_count ?> 題</strong></p>
  <p>準確率：<strong><?= $accuracy ?>%</strong></p>
</div>

<?php if ($wrong_count > 0): ?>
<h2>❌ 需要修正的題目（共 <?= $wrong_count ?> 題）</h2>
<table>
<tr><th>題號</th><th>題目</th><th>選項</th><th>DB答案</th><th>Word正解</th></tr>
<?php foreach ($wrong as $w): ?>
<tr>
  <td><?= $w['question_number'] ?></td>
  <td class="question-text"><?= htmlspecialchars($w['question_text']) ?></td>
  <td>
    A: <?= htmlspecialchars($w['option_a']) ?><br>
    B: <?= htmlspecialchars($w['option_b']) ?><br>
    C: <?= htmlspecialchars($w['option_c']) ?><br>
    D: <?= htmlspecialchars($w['option_d']) ?>
  </td>
  <td><span class="badge-db"><?= $w['db_answer'] ?></span></td>
  <td><span class="badge-word"><?= $w['word_answer'] ?></span></td>
</tr>
<?php endforeach; ?>
</table>

<h2>🔧 SQL 修正語句</h2>
<pre>
<?php foreach ($wrong as $w): ?>
-- 題號 <?= $w['question_number'] ?>: DB=<?= $w['db_answer'] ?> → Word=<?= $w['word_answer'] ?>

UPDATE questions_general SET correct_answer='<?= $w['word_answer'] ?>' WHERE question_number=<?= $w['question_number'] ?>;
<?php endforeach; ?>
</pre>
<?php else: ?>
<div class="summary"><h2 class="ok">✅ 所有答案均與 Word 標準一致！</h2></div>
<?php endif; ?>
</body></html>