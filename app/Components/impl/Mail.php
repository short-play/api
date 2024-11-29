<?php

declare(strict_types=1);

namespace App\Components\impl;

use App\Components\MailInterface;
use App\Mails\EmailTemplateInterface;
use Hyperf\Config\Annotation\Value;
use Hyperf\Coroutine\Coroutine;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Mail implements MailInterface
{

    #[Value("mail")]
    protected readonly array $configValue;

    /**
     * 发送邮件
     * @param EmailTemplateInterface $emailTemplate
     * @return void
     */
    public function send(EmailTemplateInterface $emailTemplate): void
    {
        Coroutine::create(function () use ($emailTemplate) {
            $email = new Email();
            $transport = Transport::fromDsn($this->getDsn());
            $mailer = new Mailer($transport);
            $email->addFrom(new Address($this->configValue['from_address'], $this->configValue['from_name']))
                ->to($emailTemplate->getTo())
                ->subject($emailTemplate->getSubject())
                ->html($emailTemplate->getHtml());
            // 发送邮件
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                logger()->error('发送邮件失败', [$e->getMessage(), $e->getTraceAsString()]);
            }
        });
    }

    protected function getDsn(): string
    {
        return sprintf(
            'smtp://%s:%s@%s:%d?encryption=%s',
            $this->configValue['username'],
            $this->configValue['password'],
            $this->configValue['host'],
            $this->configValue['port'],
            $this->configValue['encryption'],
        );
    }
}
