<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    // Sender info
    public string $fromEmail  = 'rogelioalmerol1@gmail.com';
    public string $fromName   = 'CLSU HRMO';
    public string $recipients = '';

    /**
     * User agent
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * Protocol
     */
    public string $protocol = 'smtp';

    /**
     * Sendmail path (not used for SMTP, but required)
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP settings (GMAIL)
     */
    public string $SMTPHost = 'smtp.gmail.com';
    public string $SMTPUser = 'rogelioalmerol1@gmail.com';

    // ⚠️ USE GMAIL APP PASSWORD (16 characters, no spaces)
    public string $SMTPPass = 'pxvzurahffuoayil';

    public int $SMTPPort = 587;
    public int $SMTPTimeout = 10;
    public bool $SMTPKeepAlive = false;

    /**
     * Encryption
     */
    public string $SMTPCrypto = 'tls';

    /**
     * Email formatting
     */
    public bool $wordWrap = true;
    public int $wrapChars = 76;
    public string $mailType = 'html';
    public string $charset  = 'UTF-8';
    public bool $validate  = false;

    /**
     * Priority
     */
    public int $priority = 3;

    /**
     * Newlines (IMPORTANT for Gmail)
     */
    public string $CRLF    = "\r\n";
    public string $newline = "\r\n";

    /**
     * BCC
     */
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize  = 200;

    /**
     * Delivery Status Notification
     */
    public bool $DSN = false;
}
