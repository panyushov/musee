<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SMTPConfig extends Model
{
    const DEFAULT_ENCRYPTION = "tls";

    public $timestamps = false;
    protected $table = 'smtp_configs';
    protected $fillable = ['host', 'port', 'username', 'encryption', 'from'];
    protected $guarded = ['password', 'notify'];

    /**
     * Method makes sure there's only one config stored
     * in the db.
     *
     * @param $input
     */
    public static function saveConfig($input)
    {
        DB::transaction(function () use ($input) {
            self::truncate();
            $config = new SMTPConfig();
            $config->fill($input);
            $config->encryption = self::DEFAULT_ENCRYPTION;
            $config->password = $input['password'];
            $config->save();
        });
    }

    /**
     * Stores email recipients in the config table.
     *
     * @param $recipients
     */
    public static function setRecipients($recipients)
    {
        if (strlen($recipients) > 0 and ($config = self::config())) {
            $config->notify = $recipients;
            $config->save();
        }
    }

    /**
     * Always return first record where main config
     * is stored.
     *
     * @return mixed
     */
    public static function config()
    {
        return SMTPConfig::first();
    }

    /**
     * Simple binary check if config exists.
     *
     * @return bool
     */
    public static function exists()
    {
        return (self::config() ? true : false);
    }

    /**
     * Adds corruption messages in the config table.
     *
     * @param $message
     */
    public static function markAsCorrupt($message)
    {
        $config = self::config();
        if ($config and !$config->corrupt) {
            $config->corrupt = $message;
            $config->save();
        }
    }

    /**
     * Cleans any corruption messages
     */
    public static function cleanCorruption()
    {
        $config = self::config();
        if ($config and $config->corrupt) {
            $config->corrupt = null;
            $config->save();
        }
    }

    /**
     * Stores locale in the config table.
     *
     * @param $locale
     */
    public static function setLocale($locale)
    {
        if (strlen($locale) > 0 and ($config = self::config())) {
            $config->locale = $locale;
            $config->save();
        }
    }

    /**
     * Sets app's mail config based on the values stored
     * in the table.
     */
    public function setConfig()
    {
        Config::set('mail.driver', 'smtp');
        Config::set('mail.host', $this->host);
        Config::set('mail.port', $this->port);
        Config::set('mail.encryption', $this->encryption);
        Config::set('mail.username', $this->username);
        Config::set('mail.password', $this->password);
        Config::set('mail.from.address', $this->from);
        Config::set('mail.from.name', '');
    }

    /**
     * Returns email recipients as an array.
     *
     * @return array
     */
    public function getRecipients()
    {
        $recipients = [];
        if ($this->notify) {
            $recipients = array_unique(array_map("trim", array_filter(explode(",", $this->notify))));
        }
        return $recipients;
    }


}
