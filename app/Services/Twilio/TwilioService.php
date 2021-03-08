<?php

namespace App\Services\Twilio;

use Twilio\Rest\Client;

/**
 * Class TwilioService
 * @package App\Http\Services\Twilio
 */
class TwilioService
{

  /**
   * Send SMS via Twilio SDK
   * @param String $message : SMS content
   * @param String $recipient : mobile number of user
   * @return String error message
   */
  public function sendSMS($message, $recipient)
  {
    $config = config('services.twilio');
    $twilioSID = $config['account_sid'];
    $authToken = $config['auth_token'];
    $twilio_number = $config['phone_number'];

    try {
      $client = new Client($twilioSID, $authToken);
      $client->messages->create($recipient, ['from' => $twilio_number, 'body' => $message]);
      return '';
    } catch(\Exception $e) {
      return "error: " . $e->getMessage();
    }
  }
}
