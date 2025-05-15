<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends \Illuminate\Notifications\DatabaseNotification
{
    public $incrementing = false;
    protected $keyType = 'string';
}
