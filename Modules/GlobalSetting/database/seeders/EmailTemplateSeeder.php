<?php

namespace Modules\GlobalSetting\database\seeders;

use Illuminate\Database\Seeder;
use Modules\GlobalSetting\app\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'password_reset',
                'subject' => 'Password Reset',
                'message' => '<p>Dear {{user_name}},</p>
                <p>Do you want to reset your password? Please Click the following link and Reset Your Password.</p>',
            ],
            [
                'name' => 'contact_mail',
                'subject' => 'Contact Email',
                'message' => '<p>Hello there,</p>
                <p>&nbsp;Mr. {{name}} has sent a new message. you can see the message details below.&nbsp;</p>
                <p>Email: {{email}}</p>
                <p>Phone: {{phone}}</p>
                <p>Subject: {{subject}}</p>
                <p>Message: {{message}}</p>',
            ],
            [
                'name' => 'subscribe_notification',
                'subject' => 'Subscribe Notification',
                'message' => '<p>Hi there, Congratulations! Your Subscription has been created successfully. Please Click the following link and Verified Your Subscription. If you will not approve this link, you can not get any newsletter from us.</p>',
            ],

            [
                'name' => 'user_verification',
                'subject' => 'User Verification',
                'message' => '<p>Dear {{user_name}},</p>
                <p>Congratulations! Your Account has been created successfully. Please Click the following link and Active your Account.</p>',
            ],

            [
                'name' => 'approved_refund',
                'subject' => 'Refund Request Approval',
                'message' => '<p>Dear {{user_name}},</p>
                <p>We are happy to say that, we have send {{refund_amount}} USD to your provided bank information. </p>',
            ],

            [
                'name' => 'new_refund',
                'subject' => 'New Refund Request',
                'message' => '<p>Hello InnvaTech, </p>

                <p>Mr. {{user_name}} has send a new refund request to you.</p>',
            ],

            [
                'name' => 'pending_wallet_payment',
                'subject' => 'Wallet Payment Approval',
                'message' => '<p>Hello {{user_name}},</p>
                <p>We have received your wallet payment request. we find your payment to our bank account.</p>
                <p>Thanks &amp; Regards</p>',
            ],

            [
                'name' => 'approved_withdraw',
                'subject' => 'Withdraw Request Approval',
                'message' => '<p>Dear {{user_name}},</p>
                <p>We are happy to say that, we have send a withdraw amount to your provided bank information.</p>
                <p>Thanks &amp; Regards</p>
                <p>InnvaTech</p>',
            ],

        ];

        foreach ($templates as $index => $template) {
            $new_template = new EmailTemplate();
            $new_template->name = $template['name'];
            $new_template->subject = $template['subject'];
            $new_template->message = $template['message'];
            $new_template->save();
        }
    }
}
