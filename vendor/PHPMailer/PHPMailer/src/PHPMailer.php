<?php
namespace PHPMailer\PHPMailer;

class PHPMailer
{
    public $Host = '';
    public $Port = 587;
    public $SMTPAuth = false;
    public $Username = '';
    public $Password = '';
    public $SMTPSecure = '';
    public $From = '';
    public $FromName = '';
    public $Subject = '';
    public $Body = '';
    public $isHTML = false;
    public $to = [];
    public $ErrorInfo = '';
    public $SMTPDebug = 0;
    
    const ENCRYPTION_STARTTLS = 'tls';
    const ENCRYPTION_SMTPS = 'ssl';
    
    public function isSMTP()
    {
        return true;
    }
    
    public function setFrom($email, $name = '')
    {
        $this->From = $email;
        $this->FromName = $name;
        return $this;
    }
    
    public function addAddress($email, $name = '')
    {
        $this->to[] = ['email' => $email, 'name' => $name];
        return $this;
    }
    
    public function isHTML($ishtml = true)
    {
        $this->isHTML = $ishtml;
        return $this;
    }
    
    public function setSubject($subject)
    {
        $this->Subject = $subject;
        return $this;
    }
    
    public function setBody($body)
    {
        $this->Body = $body;
        return $this;
    }
    
    public function send()
    {
        // Real SMTP email sending implementation
        $socket = fsockopen($this->Host, $this->Port, $errno, $errstr, 30);
        if (!$socket) {
            $this->ErrorInfo = "Could not connect to {$this->Host}:{$this->Port}";
            return false;
        }
        
        // Read server greeting
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '220') {
            $this->ErrorInfo = "Server greeting failed: $response";
            fclose($socket);
            return false;
        }
        
        // Send EHLO
        fputs($socket, "EHLO " . $this->Host . "\r\n");
        $response = fgets($socket, 515);
        
        // Start TLS if required
        if ($this->SMTPSecure == 'tls') {
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) == '220') {
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                fputs($socket, "EHLO " . $this->Host . "\r\n");
                $response = fgets($socket, 515);
            }
        }
        
        // Authenticate if required
        if ($this->SMTPAuth) {
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) == '334') {
                fputs($socket, base64_encode($this->Username) . "\r\n");
                $response = fgets($socket, 515);
                if (substr($response, 0, 3) == '334') {
                    fputs($socket, base64_encode($this->Password) . "\r\n");
                    $response = fgets($socket, 515);
                    if (substr($response, 0, 3) != '235') {
                        $this->ErrorInfo = "Authentication failed: $response";
                        fclose($socket);
                        return false;
                    }
                }
            }
        }
        
        // Send MAIL FROM
        fputs($socket, "MAIL FROM:<{$this->From}>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            $this->ErrorInfo = "MAIL FROM failed: $response";
            fclose($socket);
            return false;
        }
        
        // Send RCPT TO for each recipient
        foreach ($this->to as $recipient) {
            fputs($socket, "RCPT TO:<{$recipient['email']}>\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                $this->ErrorInfo = "RCPT TO failed: $response";
                fclose($socket);
                return false;
            }
        }
        
        // Send DATA
        fputs($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '354') {
            $this->ErrorInfo = "DATA command failed: $response";
            fclose($socket);
            return false;
        }
        
        // Send email headers and body
        $headers = "From: {$this->FromName} <{$this->From}>\r\n";
        $headers .= "To: " . implode(', ', array_map(function($r) { return $r['email']; }, $this->to)) . "\r\n";
        $headers .= "Subject: {$this->Subject}\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: " . ($this->isHTML ? 'text/html' : 'text/plain') . "; charset=UTF-8\r\n";
        $headers .= "\r\n";
        
        fputs($socket, $headers . $this->Body . "\r\n.\r\n");
        $response = fgets($socket, 515);
        
        // Send QUIT
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        
        if (substr($response, 0, 3) == '250') {
            return true;
        } else {
            $this->ErrorInfo = "Email sending failed: $response";
            return false;
        }
    }
}
?>
