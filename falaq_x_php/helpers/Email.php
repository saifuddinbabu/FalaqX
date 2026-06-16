<?php

namespace FalaqX\Helpers;

/**
 * FalaqX Helper - Email
 * Sends emails via SMTP using PHP's built-in sockets.
 * For production, drop in PHPMailer/SwiftMailer and swap the send() internals.
 */
class Email
{
    private string $to        = '';
    private string $toName    = '';
    private string $subject   = '';
    private string $body      = '';
    private string $altBody   = ''; // plain-text fallback
    private string $fromEmail = '';
    private string $fromName  = '';
    private array  $cc        = [];
    private array  $bcc       = [];
    private array  $replyTo   = [];
    private array  $attachments = [];
    private bool   $isHtml    = true;

    public function __construct()
    {
        $this->fromEmail = MAIL_FROM_EMAIL;
        $this->fromName  = MAIL_FROM_NAME;
    }

    // ── Fluent setters ────────────────────────────────────────────────────────

    public function to(string $email, string $name = ''): self
    {
        $this->to     = $email;
        $this->toName = $name;
        return $this;
    }

    public function from(string $email, string $name = ''): self
    {
        $this->fromEmail = $email;
        $this->fromName  = $name ?: $email;
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function body(string $body, bool $isHtml = true): self
    {
        $this->body   = $body;
        $this->isHtml = $isHtml;
        return $this;
    }

    public function altBody(string $text): self
    {
        $this->altBody = $text;
        return $this;
    }

    public function cc(string $email, string $name = ''): self
    {
        $this->cc[] = ['email' => $email, 'name' => $name];
        return $this;
    }

    public function bcc(string $email, string $name = ''): self
    {
        $this->bcc[] = ['email' => $email, 'name' => $name];
        return $this;
    }

    public function replyTo(string $email, string $name = ''): self
    {
        $this->replyTo[] = ['email' => $email, 'name' => $name];
        return $this;
    }

    public function attach(string $filePath, string $fileName = ''): self
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Attachment file not found: {$filePath}");
        }
        $this->attachments[] = [
            'path' => $filePath,
            'name' => $fileName ?: basename($filePath),
        ];
        return $this;
    }

    // ── Send ─────────────────────────────────────────────────────────────────

    /**
     * Send the email.
     * Uses PHP's mail() by default. Replace with SMTP logic / PHPMailer as needed.
     *
     * @throws \RuntimeException on failure
     */
    public function send(): bool
    {
        if (!Security::isEmail($this->to)) {
            throw new \RuntimeException("Invalid recipient email: {$this->to}");
        }

        $headers  = $this->buildHeaders();
        $boundary = 'FalaqX_' . md5(uniqid('', true));
        $message  = $this->buildMessage($boundary);

        // For SMTP, integrate PHPMailer here. For now, use mail():
        $sent = mail(
            $this->formatAddress($this->to, $this->toName),
            $this->subject,
            $message,
            implode("\r\n", $headers)
        );

        if (!$sent) {
            throw new \RuntimeException('mail() returned false. Check your mail configuration.');
        }

        return true;
    }

    // ── Private builders ──────────────────────────────────────────────────────

    private function buildHeaders(): array
    {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'From: ' . $this->formatAddress($this->fromEmail, $this->fromName);
        $headers[] = 'X-Mailer: FalaqX Framework';

        foreach ($this->cc as $cc) {
            $headers[] = 'Cc: ' . $this->formatAddress($cc['email'], $cc['name']);
        }
        foreach ($this->bcc as $bcc) {
            $headers[] = 'Bcc: ' . $this->formatAddress($bcc['email'], $bcc['name']);
        }
        foreach ($this->replyTo as $rt) {
            $headers[] = 'Reply-To: ' . $this->formatAddress($rt['email'], $rt['name']);
        }

        return $headers;
    }

    private function buildMessage(string $boundary): string
    {
        if (empty($this->attachments) && !$this->isHtml) {
            return $this->body;
        }

        if ($this->isHtml && empty($this->attachments)) {
            // Multipart/alternative for HTML + plain
            $alt = 'FalaqX_alt_' . md5(uniqid('', true));
            return implode("\r\n", [
                "Content-Type: multipart/alternative; boundary=\"{$alt}\"",
                "",
                "--{$alt}",
                "Content-Type: text/plain; charset=" . APP_CHARSET,
                "",
                $this->altBody ?: strip_tags($this->body),
                "",
                "--{$alt}",
                "Content-Type: text/html; charset=" . APP_CHARSET,
                "",
                $this->body,
                "",
                "--{$alt}--",
            ]);
        }

        // Mixed with attachments
        $parts   = [];
        $parts[] = "--{$boundary}";
        $parts[] = "Content-Type: text/html; charset=" . APP_CHARSET;
        $parts[] = "";
        $parts[] = $this->body;

        foreach ($this->attachments as $att) {
            $content  = base64_encode(file_get_contents($att['path']));
            $parts[] = "--{$boundary}";
            $parts[] = "Content-Type: application/octet-stream; name=\"{$att['name']}\"";
            $parts[] = "Content-Transfer-Encoding: base64";
            $parts[] = "Content-Disposition: attachment; filename=\"{$att['name']}\"";
            $parts[] = "";
            $parts[] = chunk_split($content);
        }
        $parts[] = "--{$boundary}--";

        return implode("\r\n", $parts);
    }

    private function formatAddress(string $email, string $name = ''): string
    {
        return $name ? "\"{$name}\" <{$email}>" : $email;
    }

    // ── Static convenience ────────────────────────────────────────────────────

    /**
     * Quick one-liner: Email::quick('to@mail.com', 'Subject', '<p>Body</p>');
     */
    public static function quick(string $to, string $subject, string $body): bool
    {
        return (new self())->to($to)->subject($subject)->body($body)->send();
    }
}
