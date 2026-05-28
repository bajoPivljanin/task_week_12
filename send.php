<?php
header('Content-Type: application/json');
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
$host = "localhost";
$user = "root";
$pass = "";
$db = "tw-12";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $message = trim($_POST["message"] ?? '');
    $subject = trim($_POST["subject"] ?? '');

    if (empty($name) || empty($email) || empty($message) || empty($subject)) {
        echo json_encode(['status' => 'error', 'message' => 'Sva polja su obavezna']);
        exit;
    }
    if (strlen($message) < 10) {
        echo json_encode(['status' => 'error', 'message' => 'Poruka mora da ima minimum 10 znakova']);
        exit;
    }
    try{
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO consultations (name, email, subject, message) VALUES (:name, :email, :subject, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'bajagaaa9@gmail.com';
            $mail->Password   = 'GolfMK7GTD';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Implicit TLS or STARTTLS
            $mail->Port       = 587;

            $mail->setFrom($email, $name);
            $mail->addAddress('bajagaaa9@gmail.com');

            $mail->isHTML(false);
            $mail->Subject = 'Nova konsultacija: ' . $subject;
            $mail->Body    = "Dobili ste novu poruku vezanu za konsultacije.\n\nOd: $name ($email)\nNaslov: $subject\n\nPoruka:\n$message";

            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'Mejl je poslat!']);
        }
        catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    catch(PDOException $e){
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Greska']);
}