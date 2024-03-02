<?php

namespace NicolaeSoitu\TelegramDump;

use Illuminate\Support\Facades\Http;
use Throwable;
use Exception;


class TelegramDump
{
  private static $limit = 2000;
  private static $apiUrl = 'https://api.telegram.org/bot';
  private static $title = '';
  private static $type = '';
  private static $file = '';
  private static $description = '';
  private static $to = [];

  private static function getRecipients($chatIDs = null){
    if(is_null($chatIDs) && count(self::$to) > 0){
        $chatIDs = self::$to;
    } else if(is_numeric($chatIDs)){
      // $chatIDs = $chatIDs;
    } else if(is_string($chatIDs)){
      $chatIDs = env($chatIDs);
    }
    if(is_null($chatIDs)){
      $chatIDs = config('telegram-dump.chat-id');
    }
    if(is_null($chatIDs))
      return false;
    if(!is_array($chatIDs)){
      $chatIDs = [$chatIDs];
    }
    return array_unique($chatIDs);
  }

  static function getCallerFile()
  {

    $text = '';
    if(!empty(self::$file)){
      $text = self::$file;
    } else {
      $debug_backtrace = debug_backtrace();
      if(isset($debug_backtrace[1])){
        $text = @$debug_backtrace[1]['file'] . ':' . @$debug_backtrace[1]['line'];
      }
    }
    if(!empty($text)){
      $text = str_replace(base_path().'/', "", $text);
    }
    return "<em>Project: " . basename(base_path()) . PHP_EOL .  $text . "</em>";
  }
  static function getMessage($msg){
    if(is_array($msg)){
      $msg = self::array_normally($msg, true);
    }
    if ($msg instanceof Exception) {
        $msg = self::e($msg, 'Exception');
    } else if ($msg instanceof Throwable) {
        $msg = self::e($msg, 'Throwable');
    }
    if(!is_string($msg)){
      $msg = var_export($msg,1);
    }
    return $msg;
  }
  static function send($msg, $chatIDs = null)
  {
    $recipients = self::getRecipients($chatIDs);
    if(is_null(config('telegram-dump.token')) || $recipients == false)
      return false;
    $msg = self::getMessage($msg);

    $symbolType = empty(self::$type) ? "" : " " . self::$type . " ";
    $callerFile = self::getCallerFile();



    $symbols = strlen($msg);
    $title = empty(self::$title) ? "" : self::$title . " ($symbols symbols) " . PHP_EOL;
    $description = empty(self::$description) ? "" : "<em>".self::$description ."</em>" . PHP_EOL;

    $responses = [];

    $partText = '';
    foreach ($recipients as $chatID) {
      $parts = str_split($msg, self::$limit);
      foreach ($parts as $key => $partMsg) {
        if(count($parts) > 1){
          $partText = "Part: " . $i . '/' . $parts . " - limit $limit" . PHP_EOL;
        }
        $data = [
            'chat_id'=> $chatID,
            'text'=> $symbolType . $partText . $title . $description . "<b><pre style='color:#ff0000;'>" . $partMsg . "</pre></b>" . PHP_EOL . $callerFile,
            'parse_mode'=>'html',
        ];
        $response = Http::post(self::$apiUrl . config('telegram-dump.token') . '/sendMessage?',$data);
        $responses[] =$response -> body();
      }
    }

    self::flush();
    return $responses;
  }

  public static function setTitle($title){
      self::$title = $title;
      return new static();
  }

  public static function to($to){
      self::$to = $to;
      return new static();
  }

  public static function description($description){
      self::$description = $description;
      return new static();
  }

  public static function setType($type = 'info'){
      if($type == 'info'){
          $type = "ℹ️";
      } else if($type == 'warning'){
          $type = "⚠️";
      } elseif($type == 'ok'){
          $type = '✅';
      } elseif($type == 'delete'){
          $type = '❌';
      }
      self::$type = $type;
      return new static();
  }

  public static function array_normally($expression)
  {
    $export = var_export($expression, TRUE);
    $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
    $array = preg_split("/\r\n|\n|\r/", $export);
    $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ']$1', ' => ['], $array);
    $export = join(PHP_EOL, array_filter(["["] + $array));
    return str_replace('    ',"  ", $export);
  }

  public static function flush(){
      self::$title = '';
      self::$type = '';
      self::$description = '';
      self::$file = '';
      self::$to = [];
  }

  static function getUpdates(){
    $response = Http::get(self::$apiUrl . config('telegram-dump.token') . '/getUpdates');
    return $response -> json();
  }
  static function e($e, $type){
    self::$type = '🔴';
    self::$title = 'Exception error ' . $e -> getCode();
    self::$file = "⚠️ ".$e -> getFile() . ":" . $e -> getLine();
    return $e -> getMessage();
  }
}
