<?php

declare(strict_types=1);
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;

class Email {

    private PHPMailer $mail;
    public function __construct(){
		$this->mail              = new PHPMailer(true);
		$this->mail->isSMTP();
		$this->mail->CharSet     = "utf-8";
		$this->mail->Host        = 'smtp.email.sa-saopaulo-1.oci.oraclecloud.com';
		$this->mail->SMTPAuth    = true;
		$this->mail->Username    = 'ocid1.user.oc1..aaaaaaaamgxkl4xjqwnxmjsntrujbwxae7xo7zor4hnxqp2ssi6yzfvlfzbq@ocid1.tenancy.oc1..aaaaaaaai7a72s5jgxce6rik7qna2nx2j3flclxvvrkg3mojvxmrjz3hmf6q.pw.com';
		$this->mail->Password    = '[;j9DIBcoC]6r0T:P1kE';
		$this->mail->SMTPSecure  = 'ssl';
		$this->mail->SMTPAutoTLS = true;
		$this->mail->SMTPSecure  = PHPMailer::ENCRYPTION_STARTTLS;
		$this->mail->Port        = 587;
		$this->mail->setFrom('oci@smsprefeiturasp.com.br', 'SISAD - SISTEMA DE ATENÇÃO DOMICILIAR');
    }

    public function send(string $title, string $message, $recipient, $cc = [], $cco = [], bool $image = false, array $imagePath = []) {
        $this->mail->clearAddresses();
        $this->mail->addAddress(strtolower($recipient));
		if (!empty($cc)) {
			foreach ($cc as $rec) {
				$this->mail->addCC(strtolower($rec));
			}
		}
		if (!empty($cco)) {
			foreach ($cco as $rec) {
				$this->mail->addBCC(strtolower($rec));
			}
		}

		$this->mail->isHTML(true);
		$this->mail->Subject = $title;

		$message .= "<br><br>
		Este e-mail foi enviado automaticamente, por favor não responder.<br>
		Em caso de dúvidas, entrar em contato com o suporte técnico através do e-mail smssuportesistemas@prefeitura.sp.gov.br
		";
		

        if($image and !empty($imagePath)){
			$c = 0;
			foreach($imagePath as $key => $image){
				if($c == 2)
					$this->mail->Body .= "<br><br> $message <br><br>";
				$this->mail->AddEmbeddedImage($image, "image$key");
				$this->mail->Body .= "<img style='width: 30%; height: auto;' src='cid:image$key'>";
				$c++;
			}
        }
        else {
		    $this->mail->Body = $message;
        }

        try {
			$this->mail->send();
			return true;
		} catch (Exception $e) {
			throw new Exception('Erro ao tentar enviar o email! Tente novamente mais tarde.', 400);
		}
    }
}