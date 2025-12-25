<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Mail\UserRegistration;
use App\Traits\MailSenderTrait;
use App\Mail\UserForgetPassword;
use App\Jobs\SendVerifyMailToUser;
use App\Jobs\UserForgetPasswordJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SocialLoginDefaultPasswordJob;
use Modules\Customer\app\Emails\UserBanned;
use App\Mail\SocialLoginDefaultPasswordMail;
use Modules\NewsLetter\app\Models\NewsLetter;
use Modules\Customer\app\Emails\SendMailToUser;
use Modules\Customer\app\Jobs\SendBulkEmailToUser;
use Modules\GlobalSetting\app\Models\EmailTemplate;
use Modules\Customer\app\Jobs\SendUserBannedMailJob;
use Modules\NewsLetter\app\Jobs\NewsLetterVerifyJob;
use Modules\NewsLetter\app\Emails\NewsLetterVerifyMail;
use Modules\NewsLetter\app\Emails\SendMailToNewsLetter;
use Modules\NewsLetter\app\Jobs\SendMailToNewsletterJob;

class MailSenderService {
    use MailSenderTrait;

    public function sendVerifyMailToUserFromTrait( $user_type, $user_info = null ) {
        if ( self::setMailConfig() ) {
            try {
                if ( self::isQueable() ) {
                    dispatch( new SendVerifyMailToUser( $user_type, $user_info) );
                } else {
                    if ( $user_type == 'all_user' ) {
                        $users = User::where( 'email_verified_at', null )->orderBy( 'id', 'desc' )->get();
                        foreach ( $users as $index => $user ) {
                            $user->verification_token = \Illuminate\Support\Str::random( 100 );
                            $user->save();

                            try {
                                $template = EmailTemplate::where( 'name', 'user_verification' )->first();
                                $subject = $template->subject;
                                $message = $template->message;
                                $message = str_replace( '{{user_name}}', $user->name, $message );

                                Mail::to( $user->email )->send( new UserRegistration( $message, $subject, $user ) );
                            } catch ( Exception $ex ) {
                                    Log::error( $ex->getMessage() );
                            }
                        }
                    } else {
                        try {
                            $template = EmailTemplate::where( 'name', 'user_verification' )->first();
                            $subject = $template->subject;
                            $message = $template->message;
                            $message = str_replace( '{{user_name}}', $user_info->name, $message );

                            Mail::to( $user_info->email )->send( new UserRegistration( $message, $subject, $user_info ) );
                        } catch ( Exception $ex ) {
                                Log::error( $ex->getMessage() );
                        }
                    }
                }

                return true;
            } catch ( Exception $th ) {
                    Log::error( $th->getMessage() );

                return false;
            }
        }

        return false;
    }

    public function sendUserForgetPasswordFromTrait( $from_user, $mail_template_path = 'auth' ) {
        if ( self::setMailConfig() ) {
            try {
                if ( self::isQueable() ) {
                    dispatch( new UserForgetPasswordJob( $from_user, $mail_template_path ) );
                } else {
                    try {
                        $template = EmailTemplate::where( 'name', 'password_reset' )->first();
                        $subject = $template->subject;
                        $message = $template->message;
                        $message = str_replace( '{{user_name}}', $from_user->name, $message );
                        Mail::to( $from_user->email )->send( new UserForgetPassword( $message, $subject, $from_user, $mail_template_path ) );
                    } catch ( Exception $ex ) {
                            Log::error( $ex->getMessage() );
                    }
                }

                return true;
            } catch ( Exception $th ) {
                    Log::error( $th->getMessage() );

                return false;
            }
        }

        return false;
    }

    public function sendSocialLoginDefaultPasswordFromTrait( $user, $password ) {
        if ( self::setMailConfig() ) {
            try {
                if ( self::isQueable() ) {
                    dispatch( new SocialLoginDefaultPasswordJob( $user, $password ) );
                } else {
                    try {
                        Mail::to( $user->email )->send( new SocialLoginDefaultPasswordMail( $user, $password ) );
                    } catch ( Exception $ex ) {
                            Log::error( $ex->getMessage() );
                    }
                }

                return true;
            } catch ( Exception $th ) {
                    Log::error( $th->getMessage() );

                return false;
            }
        }

        return false;
    }

    public function sendMailToUserFromTrait( $mail_subject, $mail_message, $user_type, $user_info = null ) {
        if ( self::setMailConfig() ) {
            try {
                if ( self::isQueable() ) {
                    dispatch( new SendBulkEmailToUser( $mail_subject, $mail_message, $user_type, $user_info) );
                } else {
                    if ( $user_type == 'all_user' ) {
                        $users = User::where( ['status' => 'active', 'is_banned' => 'no'] )->where( 'email_verified_at', '!=', null )->orderBy( 'id', 'desc' )->get();
                        foreach ( $users as $index => $user ) {
                            try {
                                Mail::to( $user->email )->send( new SendMailToUser( $mail_message, $mail_subject ) );
                            } catch ( Exception $ex ) {
                                Log::error($ex->getMessage());
                            }
                        }
                    } else {
                        try {
                            Mail::to( $user_info->email )->send( new SendMailToUser( $mail_message, $mail_subject ) );
                        } catch ( Exception $ex ) {
                            Log::error($ex->getMessage());
                        }
                    }
                }

                return true;
            } catch ( Exception $th ) {
                    Log::error( $th->getMessage() );

                return false;
            }
        }

        return false;
    }

    public function SendUserBannedMailFromTrait( $mail_message, $mail_subject, $user ) {
        if ( self::setMailConfig() ) {
            try {
                if ( self::isQueable() ) {
                    dispatch( new SendUserBannedMailJob( $mail_message, $mail_subject, $user ) );
                } else {
                    Mail::to( $user->email )->send( new UserBanned( $mail_message, $mail_subject ) );
                }
                return true;
            } catch ( Exception $th ) {
                    Log::error( $th->getMessage() );

                return false;
            }
        }

        return false;
    }
    public function sendVerifyMailToNewsletterFromTrait( $newsletter ) {
        if ( self::setMailConfig() ) {
            try {
                if ( self::isQueable() ) {
                    dispatch(new NewsLetterVerifyJob($newsletter));
                } else {
                    $template = EmailTemplate::where('name', 'subscribe_notification')->first();
                    $message = $template->message;
                    $subject = $template->subject;
                    Mail::to($newsletter->email)->send(new NewsLetterVerifyMail($newsletter, $subject, $message));
                }
                return true;
            } catch ( Exception $th ) {
                    Log::error( $th->getMessage() );

                return false;
            }
        }

        return false;
    }

    public function SendMailToNewsletterFromTrait( $mail_subject, $mail_message ) {
        if ( self::setMailConfig() ) {
            try {
                if ( self::isQueable() ) {
                    dispatch( new SendMailToNewsletterJob( $mail_subject, $mail_message ) );
                } else {
                    $newsletters = NewsLetter::orderBy( 'id', 'desc' )->where( 'status', 'verified' )->get();
                    foreach ( $newsletters as $index => $item ) {
                        try {
                            Mail::to( $item->email )->send( new SendMailToNewsLetter( $mail_subject, $mail_message ) );
                        } catch ( Exception $ex ) {
                            Log::error($ex->getMessage());
                        }
                    }
                }
                return true;
            } catch ( Exception $th ) {
                    Log::error( $th->getMessage() );

                return false;
            }
        }

        return false;
    }
}
