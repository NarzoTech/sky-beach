<?php
namespace Modules\GlobalSetting\database\seeders;

use Illuminate\Database\Seeder;
use Modules\GlobalSetting\app\Models\Setting;

class GlobalSettingInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::truncate();
        $setting_data = [
            'app_name'                             => 'QuickShifter',
            'version'                              => '1.00',
            'logo'                                 => 'uploads/website-images/logo.png',
            'timezone'                             => 'Asia/Dhaka',
            'favicon'                              => 'uploads/website-images/favicon.png',
            'copyright_text'                       => 'this is copyright text',
            'default_avatar'                       => 'uploads/website-images/default-avatar.png',
            'breadcrumb_image'                     => 'uploads/website-images/breadcrumb-image.jpg',
            'mail_host'                            => 'mail_host',
            'mail_sender_email'                    => 'sender@gmail.com',
            'mail_username'                        => 'mail_username',
            'mail_password'                        => 'mail_password',
            'mail_port'                            => 'mail_port',
            'mail_encryption'                      => 'ssl',
            'mail_sender_name'                     => 'InnvaTech',
            'contact_message_receiver_mail'        => 'receiver@gmail.com',
            'pusher_app_id'                        => 'pusher_app_id',
            'pusher_app_key'                       => 'pusher_app_key',
            'pusher_app_secret'                    => 'pusher_app_secret',
            'pusher_app_cluster'                   => 'pusher_app_cluster',
            'pusher_status'                        => 'inactive',

            'last_update_date'                     => date('Y-m-d H:i:s'),
            'is_queable'                           => 'inactive',

            'login'                                => '',
            'mobile'                               => '',
            'email'                                => '',
            'address'                              => '',
            'type'                                 => '',
            'city'                                 => '',
            'zip'                                  => '',
            'country'                              => '',
            'website'                              => '',
            'start_date'                           => '',
            'date_format'                          => '',
            'time_format'                          => '',
            'report_default_days'                  => '',
            'color'                                => '',
            'status'                               => '',
            'currency'                             => '',
            'vat'                                  => '',
            'min_phone_number'                     => '',
            'invoice_prefix'                       => '',
            'invoice_suffix'                       => '',
            'deliverycharge'                       => '',
            'pos_due_payment'                      => '',
            'enable_auto_print'                    => '',
            'enable_exchange'                      => '',
            'enable_investment'                    => '',
            'enable_minimum_sale_price'            => '',
            'enable_negative_sale'                 => '',
            'enable_online_order'                  => '',
            'enable_product_model'                 => '',
            'enable_product_variant'               => '',
            'enable_purchase_panel_profit_percent' => '',
            'sale_and_print_confirmation'          => '',
            'enable_service'                       => '',
            'show_stock_in_pos'                    => '',
            'logo'                                 => '',
        ];

        foreach ($setting_data as $index => $setting_item) {
            $new_item        = new Setting();
            $new_item->key   = $index;
            $new_item->value = $setting_item;
            $new_item->save();
        }
    }
}
